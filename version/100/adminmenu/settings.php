<?php
global $smarty, $oPlugin;
$pathToTemplates = PFAD_ROOT . PFAD_PLUGIN.$oPlugin->cVerzeichnis . "/" . PFAD_PLUGIN_VERSION . $oPlugin->nVersion . "/adminmenu/tpl/settings.tpl";

// Get config.
$config = Shop::DB()->queryPrepared(
    "SELECT * FROM `xplugin_endereco_jtl4_phs_cconfs` WHERE `key` = 'advset'",
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save-adv-settings'])) {
    $config  = [
        'rafix' => [
            's' => $_POST['rafix']['s'],
            'ws' => $_POST['rafix']['ws'],
            'rs' => $_POST['rafix']['rs'],
            'cs' => $_POST['rafix']['cs'],
        ],
        'ramob' => [
            's' => $_POST['ramob']['s'],
            'ws' => $_POST['ramob']['ws'],
            'rs' => $_POST['ramob']['rs'],
            'cs' => $_POST['ramob']['cs'],
        ],
        'lafix' => [
            's' => $_POST['lafix']['s'],
            'ws' => $_POST['lafix']['ws'],
            'rs' => $_POST['lafix']['rs'],
            'cs' => $_POST['lafix']['cs'],
        ],
        'lamob' => [
            's' => $_POST['lamob']['s'],
            'ws' => $_POST['lamob']['ws'],
            'rs' => $_POST['lamob']['rs'],
            'cs' => $_POST['lamob']['cs'],
        ],
        'other' => [
            'latog' => $_POST['other']['latog'],
            'ordsub' => $_POST['other']['ordsub'],
        ],
    ];

    // Save to db.
    Shop::DB()->queryPrepared(
        "UPDATE `xplugin_endereco_jtl4_phs_cconfs`
            SET `value` = :configuration
            WHERE `id` = :id AND `key` = 'advset'
                ",
        [
            ':id' => intval($_POST['endsetid']),
            ':configuration' => serialize($config),
        ],
        1
    );
}

$smarty->assign('endjtlphssettings', $config);
$smarty->assign('endsetid', $configId);

echo $smarty->fetch($pathToTemplates);
