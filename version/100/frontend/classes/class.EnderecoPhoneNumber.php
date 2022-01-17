<?php

require_once realpath(dirname(__FILE__) . '/') . '/class.EnderecoJTL4Client.php';

class EnderecoPhoneNumber
{
    public $number;
    public $assumedType = '';
    public $valid = false;
    public $addressCountryCode;

    private $enderecoClient;
    private $countryConfig;
    private $translations;

    public $status = [];
    public $predictions = [];

    public function __construct($number, $assumedType, $addressCountryCode, $pluginReference)
    {
        $this->number = $number;
        $this->assumedType = $assumedType;
        $this->addressCountryCode = $addressCountryCode;

        $this->enderecoClient = EnderecoJTL4Client::createClient();
        $this->countryConfig = $this->enderecoClient->getCountryConfiguration();

        $translations = [];
        foreach($pluginReference->oPluginSprachvariableAssoc_arr as $key => $translation) {
            $translations[$key] = utf8_encode($translation);
        }

        $this->translations = $translations;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setPredictions($predictions)
    {
        $this->predictions = $predictions;
    }

    public function isValid()
    {
        return in_array('phone_correct', $this->status) || in_array('phone_needs_correction', $this->status);
    }

    public function isCorrect()
    {
        return in_array('phone_correct', $this->status);
    }

    public function getErrorTexts()
    {
        $problems = [];
        $requiredFormat = $this->getRequiredFormat();
        $correctNumber = $this->getFormattedNumber();

        if (in_array('phone_invalid', $this->status)) {
            $problems[] = $this->translations['endereco_jtl4_phs_phone_invalid'];
        }

        if (in_array('phone_format_needs_correction', $this->status)) {
            $mapping = [
                '{$requiredFormat}' => $requiredFormat,
                '{$correctNumber}' => $correctNumber,
            ];
            $problems[] = str_replace(
                array_keys($mapping),
                array_values($mapping),
                $this->translations['endereco_jtl4_phs_phone_format_needs_correction']
            );
        }

        if (('fixed' === $this->assumedType) && in_array('phone_is_mobile', $this->status)) {
            $problems[] = $this->translations['endereco_jtl4_phs_phone_wrong_type_mobile'];
        }

        if (('mobile' === $this->assumedType) && in_array('phone_is_fixed_line', $this->status)) {
            $problems[] = $this->translations['endereco_jtl4_phs_phone_wrong_type_fixed'];
        }

        return $problems;
    }

    public function getSuccessTexts()
    {
        $errors = $this->getErrorTexts();
        if (0 === count($errors)) {
            return [$this->translations['endereco_jtl4_phs_phone_correct']];
        }
        return [];
    }

    public function isWrongFormat()
    {
        return in_array('phone_format_needs_correction', $this->status);
    }

    public function isMandatory()
    {
        return 'R' === $this->countryConfig[strtoupper($this->addressCountryCode)][$this->assumedType];
    }

    public function isOptional()
    {
        return 'O' === $this->countryConfig[strtoupper($this->addressCountryCode)][$this->assumedType];
    }

    public function isQuestioned()
    {
        return 'X' !== $this->countryConfig[strtoupper($this->addressCountryCode)][$this->assumedType];
    }

    public function isInvisible()
    {
        return 'X' === $this->countryConfig[strtoupper($this->addressCountryCode)][$this->assumedType];
    }

    public function getRequiredFormat()
    {
        return $this->countryConfig[strtoupper($this->addressCountryCode)]['format'];
    }

    public function anyFormatRequired()
    {
        return !empty($this->countryConfig[strtoupper($this->addressCountryCode)]['format']);
    }

    public function isMobile()
    {
        if (empty($this->status)) {
            return false;
        } else {
            return in_array('phone_is_mobile', $this->status);
        }
    }

    public function isFixedLine()
    {
        if (empty($this->status)) {
            return false;
        } else {
            return in_array('phone_is_fixed_line', $this->status);
        }
    }

    public function getFormattedNumber($format = null)
    {
        $mapping = [
            'E164' => 'formatE164',
            'INTERNATIONAL' => 'formatInternational',
            'NATIONAL' => 'formatNational'
        ];

        $fieldName = $mapping[$this->countryConfig[strtoupper($this->addressCountryCode)]['format']];

        if (!$format) {
            $formattedNumber = $this->predictions[0]['phone'];
        } else {
            $formattedNumber = $this->predictions[0][$fieldName];
        }

        return $formattedNumber;
    }

    public function isEmpty()
    {
        return empty($this->number);
    }
}
