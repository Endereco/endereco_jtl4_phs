<?php

require_once $oPlugin->cFrontendPfad . 'classes/class.EnderecoJTL4Client.php';
require_once $oPlugin->cFrontendPfad . 'classes/class.EnderecoPhoneNumber.php';

if ('endereco_phs_request' === $_REQUEST['io']) {
    $statusInfos = [];
    if (!empty($_REQUEST['phoneNumber'])){
        $EnderecoClient = EnderecoJTL4Client::createClient();
        $PhoneNumber = new EnderecoPhoneNumber($_REQUEST['phoneNumber'], $_REQUEST['phoneType'],  $_REQUEST['countryCode'], $oPlugin);
        $EnderecoClient->checkPhoneNumber($PhoneNumber);
        $statusInfos[$PhoneNumber->number] = [
            'isCorrect' => $PhoneNumber->isCorrect(),
            'errorMessages' => $PhoneNumber->getErrorTexts(),
            'successMessages' => $PhoneNumber->getSuccessTexts(),
        ];
    }

    sleep(1);

    $result = json_encode($statusInfos);
    header('Content-Type: application/json');
    echo $result;
    exit();
}
