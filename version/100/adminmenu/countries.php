<?php
global $smarty, $oPlugin;

$pathToTemplates = PFAD_ROOT . PFAD_PLUGIN.$oPlugin->cVerzeichnis . "/" . PFAD_PLUGIN_VERSION . $oPlugin->nVersion . "/adminmenu/tpl/countries.tpl";

// Get config.
$config = Shop::DB()->queryPrepared(
    "SELECT * FROM `xplugin_endereco_jtl4_phs_cconfs` WHERE `key` = 'cconf'",
    [],
    9
);

if (isset($config[0])) {
    $configId = intval($config[0]['id']);
    $config = unserialize($config[0]['value']);
} else {
    $config = [];
    $configId = 0;
}

$newConfig = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['country'] AS $countryCode => $values) {
        $newConfig[$countryCode] = [
            'mobile' => $values['mobile'],
            'fixed' => $values['fixed'],
            'format' => $values['format'],
        ];
    }

    foreach ($config as $countryCode => &$configitem) {
        if (isset($newConfig[$countryCode])) {
            $configitem = array_merge($configitem, $newConfig[$countryCode]);
        }
    }

    // Save to db.
    Shop::DB()->queryPrepared(
        "UPDATE `xplugin_endereco_jtl4_phs_cconfs`
            SET `value` = :configuration
            WHERE `id` = :id AND `key` = 'cconf'
                ",
        [
            ':id' => intval($_POST['confid']),
            ':configuration' => serialize($config),
        ],
        1
    );
}

$smarty->assign('endjtlphsconfig', $config);
$smarty->assign('endconfid', $configId);

// Get all countries.
echo $smarty->fetch($pathToTemplates);
