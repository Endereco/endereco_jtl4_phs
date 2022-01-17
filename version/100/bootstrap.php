<?php
namespace endereco_jtl4_phs;

use Shop;

class Bootstrap extends \AbstractPlugin {

    public $defaultConfig = [
        'PL' => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
        'CZ' => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
        'BG' => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
        'RO' => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
        'HU' => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
        'SK' => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
        'RU'  => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
        'BY' => [
            'mobile' => 'R',
            'fixed' => 'O',
            'format' => 'E164',
        ],
    ];

    public $defaultSettings = [
        'rafix' => [
            's' => '[name="tel"]',
            'ws' => '.form-group',
            'rs' => '.form-group',
            'cs' => '[name="land"]',
        ],
        'ramob' => [
            's' => '[name="mobil"]',
            'ws' => '.form-group',
            'rs' => '.form-group',
            'cs' => '[name="land"]',
        ],
        'lafix' => [
            's' => '[name="register[shipping_address][tel]"]',
            'ws' => '.form-group',
            'rs' => '.form-group',
            'cs' => '[name="register[shipping_address][land]"]',
        ],
        'lamob' => [
            's' => '[name="register[shipping_address][mobil]"]',
            'ws' => '.form-group',
            'rs' => '.form-group',
            'cs' => '[name="register[shipping_address][land]"]',
        ],
        'other' => [
            'latog' => '#checkout_register_shipping_address',
            'ordsub' => '#complete-order-button',
        ],
    ];

    public $defaultFunctions = [
        'useAdvancedFunctions' => true,
        'apiKey' => '',
        'wrongNumberAction' => 2,
        'wrongFormatAction' => 4,
        'wrongTypeAction' => 2,
        'invalidateAfterDays' => 30
    ];

    /**
     * @return mixed
     */
    public function installed() {

        // Get all countries.
        $countries = Shop::DB()->queryPrepared(
            "SELECT `cISO`, `cDeutsch` FROM `tland`",
            [],
            9
        );

        // Create country configs.
        $countryConfigs = [];
        foreach ($countries as $country) {
            $countryConfigs[$country['cISO']] = [
                'name' => $country['cDeutsch'],
                'mobile' => 'X',
                'fixed' => 'X',
                'format' => 'E164',
            ];

            if (isset($this->defaultConfig[$country['cISO']])) {
                $countryConfigs[$country['cISO']] = array_merge($countryConfigs[$country['cISO']], $this->defaultConfig[$country['cISO']]);
            }
        }

        // Insert countries config to db.
        Shop::DB()->queryPrepared(
            "INSERT INTO `xplugin_endereco_jtl4_phs_cconfs`
                    (`key`, `value`)
                 VALUES
                    ('cconf', :configuration)
                ",
            [
                ':configuration' => serialize($countryConfigs),
            ],
            1
        );

        // Insert default selectors.
        Shop::DB()->queryPrepared(
            "INSERT INTO `xplugin_endereco_jtl4_phs_cconfs`
                    (`key`, `value`)
                 VALUES
                    ('advset', :settings)
                ",
            [
                ':settings' => serialize($this->defaultSettings),
            ],
            1
        );

        // Insert default selectors.
        Shop::DB()->queryPrepared(
            "INSERT INTO `xplugin_endereco_jtl4_phs_cconfs`
                    (`key`, `value`)
                 VALUES
                    ('settings', :settings)
                ",
            [
                ':settings' => serialize($this->defaultFunctions),
            ],
            1
        );

        return;

    }

    /**
     * @return mixed
     */
    public function uninstalled() {

    }

    /**
     * @return mixed
     */
    public function enabled() {
        // Activate phonenumber field everywhere.

    }

    /**
     * @return mixed
     */
    public function disabled() {

    }

}
