<?php

require_once realpath(dirname(__FILE__) . '/') . '/class.EnderecoPhoneNumber.php';

class EnderecoJTL4Client
{
    private static $clientRef;

    private $advancedSettings = [];
    private $countryConfiguration = [];
    private $settings = [];

    private $apiKey = '';
    private $clientInfo = 'Endereco JTL4 PhoneCheck Plugin v1.0.0';

    public $pluginReference;

    public static function createClient($oPlugin = null)
    {
        if (empty(self::$clientRef)) {
            self::$clientRef = new EnderecoJTL4Client($oPlugin);
        }
        return self::$clientRef;
    }

    function __construct($oPlugin = null)
    {

        $data = \Shop::DB()->queryPrepared(
            "SELECT * FROM `xplugin_endereco_jtl4_phs_cconfs`",
            [],
            9
        );

        foreach($data as $settingsblock) {
            if ('cconf' === $settingsblock['key']) {
                $this->countryConfiguration = unserialize($settingsblock['value']);
            }
            if ('advset' === $settingsblock['key']) {
                $this->advancedSettings = unserialize($settingsblock['value']);
            }
            if ('settings' === $settingsblock['key']) {
                $this->settings = unserialize($settingsblock['value']);
            }
        }

        $this->apiKey = $this->settings['apiKey'];
        $this->pluginReference = $oPlugin;

    }

    public function checkPhoneNumber(EnderecoPhoneNumber &$phoneNumber)
    {
        // Check in the database, if the number was checked already.
        $daysToInvalid = (int) $this->settings['invalidateAfterDays'];

        $cachedPhoneNumber = \Shop::DB()->queryPrepared(
            "SELECT * FROM `xplugin_endereco_jtl4_phs_checked_numbers` WHERE `number` = :a1 AND `format` = :a2 AND `country` = :a3 AND `last_change_at` >= subdate(NOW(), $daysToInvalid) LIMIT 1",
            [
                'a1' => $phoneNumber->number,
                'a2' => $phoneNumber->getRequiredFormat(),
                'a3' => $phoneNumber->addressCountryCode
            ],
            8
        );

        if (!empty($cachedPhoneNumber)) {
            $phoneNumber->setStatus(unserialize($cachedPhoneNumber['status']));
            $phoneNumber->setPredictions(unserialize($cachedPhoneNumber['predictions']));
            return;
        }

        // Generate session id.
        $referer =  $_SERVER['HTTP_REFERER'];
        $referer = strtok($referer, '?');

        try {

            $message = array(
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => 'phoneCheck',
                'params' => array(
                    'phone' => $phoneNumber->number,
                )
            );

            if (!empty($phoneNumber->addressCountryCode)) {
                $message['params']['countryCode'] = $phoneNumber->addressCountryCode;
            }

            if ($phoneNumber->anyFormatRequired()) {
                $message['params']['format'] = $phoneNumber->getRequiredFormat();
            }

            $newHeaders = array(
                'Content-Type' => 'application/json',
                'X-Auth-Key' => $this->apiKey,
                'X-Transaction-Id' => 'not_required',
                'X-Transaction-Referer' => $referer,
                'X-Agent' => $this->clientInfo,
            );

            $result = $this->sendRequest($message, $newHeaders);

            // Save status and predictions
            if (array_key_exists('result', $result)) {
                // If we reached this far, it's safe to close the session.
                $phoneNumber->setStatus($result['result']['status']);
                $phoneNumber->setPredictions($result['result']['predictions']);

                // Save in cache.
                $sql = "INSERT INTO `xplugin_endereco_jtl4_phs_checked_numbers` (`number`,`format`,`country`,`status`,`predictions`, `last_change_at`) VALUES (:a1,:a2,:a3,:a4,:a5, NOW())
                          ON DUPLICATE KEY UPDATE `status` = VALUES(`status`), `predictions` = VALUES(`predictions`), `last_change_at` = NOW();";
                \Shop::DB()->queryPrepared(
                    $sql,
                    [
                        'a1' => $phoneNumber->number,
                        'a2' => $phoneNumber->getRequiredFormat(),
                        'a3' => $phoneNumber->addressCountryCode,
                        'a4' => serialize($result['result']['status']),
                        'a5' => serialize($result['result']['predictions'])
                    ],
                    1
                );
            }
        } catch(\Exception $e) {
            // Do nothing.
        }
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getAdvancedSettings()
    {
        return $this->advancedSettings;
    }

    public function getCountryConfiguration()
    {
        return $this->countryConfiguration;
    }

    public function generateSessionId() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function closeSessions($sessionIds) {

        // Get sessionids.
        if (!$sessionIds) {
            return;
        }

        $anyDoAccounting = false;
        $referer =  $_SERVER['HTTP_REFERER'];
        $referer = strtok($referer, '?');

        foreach ($sessionIds as $sessionId) {
            try {
                $message = array(
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'doAccounting',
                    'params' => array(
                        'sessionId' => $sessionId
                    )
                );
                $newHeaders = array(
                    'Content-Type' => 'application/json',
                    'X-Auth-Key' => $this->apiKey,
                    'X-Transaction-Id' => $sessionId,
                    'X-Transaction-Referer' => $referer,
                    'X-Agent' => $this->clientInfo,
                );
                $this->sendRequest($message, $newHeaders);
                $anyDoAccounting = true;

            } catch(\Exception $e) {
                // Log.
            }
        }

        if ($anyDoAccounting) {
            try {
                $message = array(
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'doConversion',
                    'params' => array()
                );
                $newHeaders = array(
                    'Content-Type' => 'application/json',
                    'X-Auth-Key' => $this->apiKey,
                    'X-Transaction-Id' => 'not_required',
                    'X-Transaction-Referer' => $referer,
                    'X-Agent' => $this->clientInfo,
                );
                $this->sendRequest($message, $newHeaders);
            } catch(\Exception $e) {
                // Do nothing.
            }
        }
    }

    public function sendRequest($body, $headers) {
        $serviceUrl = 'https://staging.endereco-service.de/rpc/v1';
        $ch = curl_init(trim($serviceUrl));
        $dataString = json_encode($body);

        $parsedHeaders = array();
        foreach ($headers as $headerName=>$headerValue) {
            $parsedHeaders[] = $headerName . ': ' . $headerValue;
        }
        $parsedHeaders[] = 'Content-Length: ' . strlen($dataString);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $parsedHeaders
        );

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result;
    }
}
