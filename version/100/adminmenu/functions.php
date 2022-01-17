<?php

global $smarty, $oPlugin;
$pathToTemplates = PFAD_ROOT . PFAD_PLUGIN.$oPlugin->cVerzeichnis . "/" . PFAD_PLUGIN_VERSION . $oPlugin->nVersion . "/adminmenu/tpl/functions.tpl";

$settings = Shop::DB()->queryPrepared(
    "SELECT * FROM `xplugin_endereco_jtl4_phs_cconfs` WHERE `key` = 'settings'",
    [],
    9
);

if (isset($settings[0])) {
    $configId = intval($settings[0]['id']);
    $settings = unserialize($settings[0]['value']);
} else {
    $settings = [];
    $configId = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save-settings'])) {
    $settings  =[
        'useAdvancedFunctions' => ('on' === $_POST['useAdvancedFunctions']),
        'apiKey' => $_POST['apiKey'],
        'wrongNumberAction' => intval($_POST['wrongNumberAction']),
        'wrongFormatAction' => intval($_POST['wrongFormatAction']),
        'wrongTypeAction' => intval($_POST['wrongTypeAction']),
        'invalidateAfterDays' => intval($_POST['invalidateAfterDays']),
    ];

    // Save to db.
    Shop::DB()->queryPrepared(
        "UPDATE `xplugin_endereco_jtl4_phs_cconfs`
            SET `value` = :configuration
            WHERE `id` = :id AND `key` = 'settings'
                ",
        [
            ':id' => intval($_POST['endfncid']),
            ':configuration' => serialize($settings),
        ],
        1
    );
}

$smarty->assign('endjtlphsfunctions', $settings);
$smarty->assign('endfncid', $configId);
echo $smarty->fetch($pathToTemplates);
