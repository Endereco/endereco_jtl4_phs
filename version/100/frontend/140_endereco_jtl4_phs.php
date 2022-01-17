<?php
/**
 * HOOK_SMARTY_OUTPUTFILTER
 */

if (version_compare(JTL_VERSION, 400, '>=') && class_exists('Shop')) {
    $smarty = Shop::Smarty();
} else {
    global $smarty;
}

require_once $oPlugin->cFrontendPfad . 'classes/class.EnderecoJTL4Client.php';
require_once $oPlugin->cFrontendPfad . 'classes/class.EnderecoPhoneNumber.php';

// If phone number is on the page, load our js.
if (pq('[type="tel"]')->length) {

    $EnderecoClient = EnderecoJTL4Client::createClient($oPlugin);
    $advancedSettings = $EnderecoClient->getAdvancedSettings();
    $config = $EnderecoClient->getCountryConfiguration();
    // Remove country names to prevent problems with utf.
    foreach ($config as &$confitem) {
        unset($confitem['name']);
    }
    $file = $oPlugin->cFrontendPfad . 'template/headprepend.tpl';

    $statusInfos = [];
    // Load status info for the phones.
    if (!empty($_SESSION['Lieferadresse']->cTel)){
        $PhoneNumberFix = new EnderecoPhoneNumber($_SESSION['Lieferadresse']->cTel, 'fixed', $_SESSION['Lieferadresse']->cLand, $oPlugin);
        $EnderecoClient->checkPhoneNumber($PhoneNumberFix);
        $statusInfos[$PhoneNumberFix->number] = [
            'isCorrect' => $PhoneNumberFix->isCorrect(),
            'errorMessages' => $PhoneNumberFix->getErrorTexts(),
            'successMessages' => $PhoneNumberFix->getSuccessTexts(),
        ];
    }
    if (!empty($_SESSION['Lieferadresse']->cMobil)){
        $PhoneNumberMobile = new EnderecoPhoneNumber($_SESSION['Lieferadresse']->cMobil, 'mobil', $_SESSION['Lieferadresse']->cLand, $oPlugin);
        $EnderecoClient->checkPhoneNumber($PhoneNumberMobile);
        $statusInfos[$PhoneNumberMobile->number] = [
            'isCorrect' => $PhoneNumberMobile->isCorrect(),
            'errorMessages' => $PhoneNumberMobile->getErrorTexts(),
            'successMessages' => $PhoneNumberMobile->getSuccessTexts(),
        ];
    }

    if (empty($_SESSION['Lieferadresse'])) {
        if (!empty($_SESSION['Kunde']->cTel)) {
            $PhoneNumberFix = new EnderecoPhoneNumber($_SESSION['Kunde']->cTel, 'fixed', $_SESSION['Kunde']->cLand, $oPlugin);
            $EnderecoClient->checkPhoneNumber($PhoneNumberFix);
            $statusInfos[$PhoneNumberFix->number] = [
                'isCorrect' => $PhoneNumberFix->isCorrect(),
                'errorMessages' => $PhoneNumberFix->getErrorTexts(),
                'successMessages' => $PhoneNumberFix->getSuccessTexts(),
            ];
        }

        if (!empty($_SESSION['Kunde']->cMobil)) {
            $PhoneNumberMobile = new EnderecoPhoneNumber($_SESSION['Kunde']->cMobil, 'mobil', $_SESSION['Kunde']->cLand, $oPlugin);
            $EnderecoClient->checkPhoneNumber($PhoneNumberMobile);
            $statusInfos[$PhoneNumberMobile->number] = [
                'isCorrect' => $PhoneNumberMobile->isCorrect(),
                'errorMessages' => $PhoneNumberMobile->getErrorTexts(),
                'successMessages' => $PhoneNumberMobile->getSuccessTexts(),
            ];
        }
    }

    $translations = [];
    foreach($oPlugin->oPluginSprachvariableAssoc_arr as $key => $translation) {
        $translations[$key] = utf8_encode($translation);
    }

    $smarty->assign('endjtlphsconfig', $config);
    $smarty->assign('endtwoaddr', (!empty($_SESSION['Bestellung']) && (0 !== intval($_SESSION['Bestellung']->kLieferadresse))));
    $smarty->assign('endjtlphssettings', $advancedSettings);
    $smarty->assign('endjtlphsstatuses', $statusInfos);
    $smarty->assign('endjtlphstranslations', $translations);
    $smarty->assign('endphsscriptpath', URL_SHOP . '/includes/plugins/endereco_jtl4_phs/version/'.$oPlugin->nVersion.'/frontend/js/endrphs.min.js');
    $smarty->assign('endriolink', URL_SHOP.'/io.php');
    $html = $smarty->fetch($file);
    pq('head')->prepend($html);
}

// Add error messages.

/**
 * If order submit button is on the page and the blocker is needed, block the button and display message.
 */
if (pq('#complete-order-button')->length) {
    $EnderecoClient = EnderecoJTL4Client::createClient($oPlugin);
    $settings = $EnderecoClient->getSettings();
    $advancedSettings = $EnderecoClient->getAdvancedSettings();

    $translations = [];
    foreach($oPlugin->oPluginSprachvariableAssoc_arr as $key => $translation) {
        $translations[$key] = $translation;
    }

    // The advanced function should be active and the api key should be provided.
    if ($settings['useAdvancedFunctions'] && !empty($settings['apiKey'])) {
        $hasErrors = false;
        $blockSubmit = false;
        $shouldReload = false;
        $errorMessages = [];

        /**
         * In this block we check all phones, that needs to be checked. We prefer delivery phone over the normal phone.
         */
        $PhoneNumberFix = new EnderecoPhoneNumber($_SESSION['Lieferadresse']->cTel, 'fixed', $_SESSION['Lieferadresse']->cLand, $oPlugin);
        if (!empty($_SESSION['Lieferadresse']->cTel)){
            $EnderecoClient->checkPhoneNumber($PhoneNumberFix);
        }

        $PhoneNumberMobile = new EnderecoPhoneNumber($_SESSION['Lieferadresse']->cMobil, 'mobile', $_SESSION['Lieferadresse']->cLand, $oPlugin);
        if (!empty($_SESSION['Lieferadresse']->cMobil)) {
            $EnderecoClient->checkPhoneNumber($PhoneNumberMobile);
        }

        /**
         * The next four checks handle the condition in "Wenn die Telefonnummer falsch ist" setting.
         */
        if (!$PhoneNumberFix->isEmpty()
            && (2 === $settings['wrongNumberAction'])
            && $PhoneNumberFix->isMandatory()
        ) {
            if (!$PhoneNumberFix->isValid()) {
                $hasErrors = true;
                $blockSubmit = true;
                $errorMessages[] = $translations['endereco_jtl4_phs_phone_invalid_fixed'];
            }
        }

        if (!$PhoneNumberFix->isEmpty()
            && $PhoneNumberFix->isQuestioned()
            && (3 === $settings['wrongNumberAction'])
        ) {
            if (!$PhoneNumberFix->isValid()) {
                $hasErrors = true;
                $blockSubmit = true;
                $errorMessages[] = $translations['endereco_jtl4_phs_phone_invalid_fixed'];
            }
        }

        if (!$PhoneNumberMobile->isEmpty()
            && (2 === intval($settings['wrongNumberAction']))
            && $PhoneNumberMobile->isMandatory()
        ) {
            if (!$PhoneNumberMobile->isValid()) {
                $hasErrors = true;
                $blockSubmit = true;
                $errorMessages[] = $translations['endereco_jtl4_phs_phone_invalid_mobile'];
            }
        }

        if (!$PhoneNumberMobile->isEmpty()
            && $PhoneNumberMobile->isQuestioned()
            && (3 === $settings['wrongNumberAction'])
        ) {
            if (!$PhoneNumberMobile->isValid()) {
                $hasErrors = true;
                $blockSubmit = true;
                $errorMessages[] = $translations['endereco_jtl4_phs_phone_invalid_mobile'];
            }
        }

        /**
         * The next  checks handle the condition in "Wenn die Telefonnummer im falschen Format ist" setting.
         */
        if (!$PhoneNumberFix->isEmpty()
            && (2 === $settings['wrongFormatAction'])
            && $PhoneNumberFix->anyFormatRequired()
            && $PhoneNumberFix->isMandatory()
            && $PhoneNumberFix->isWrongFormat()
        ) {
            $hasErrors = true;
            $blockSubmit = true;
            $format = $PhoneNumberFix->getRequiredFormat();
            $mapping = [
                '{$requiredFormat}' => $format
            ];
            $errorMessages[] = str_replace(
                array_keys($mapping),
                array_values($mapping),
                $translations['endereco_jtl4_phs_phone_format_needs_correction_fixed']
            );
        }

        if (!$PhoneNumberFix->isEmpty()
            && (3 === $settings['wrongFormatAction'])
            && $PhoneNumberFix->anyFormatRequired()
            && $PhoneNumberFix->isQuestioned()
            && $PhoneNumberFix->isWrongFormat()
        ) {
            $hasErrors = true;
            $blockSubmit = true;
            $format = $PhoneNumberFix->getRequiredFormat();
            $mapping = [
                '{$requiredFormat}' => $format
            ];
            $errorMessages[] = str_replace(
                array_keys($mapping),
                array_values($mapping),
                $translations['endereco_jtl4_phs_phone_format_needs_correction_fixed']
            );
        }

        if (!$PhoneNumberFix->isEmpty()
            && (4 === $settings['wrongFormatAction'])
            && $PhoneNumberFix->anyFormatRequired()
            && $PhoneNumberFix->isQuestioned()
            && $PhoneNumberFix->isWrongFormat()
        ) {
            $hasErrors = false;
            $shouldReload = true;
            $blockSubmit = true;

            $correctPhoneNumber = $PhoneNumberFix->getFormattedNumber();

            // Update in Lieferung.
            $_SESSION['Lieferadresse']->cTel = $correctPhoneNumber;
            // Update in db.
            if (!empty($_SESSION['Lieferadresse']->kLieferadresse)) {
                $Lieferadresse = new Lieferadresse($_SESSION['Lieferadresse']->kLieferadresse);
                $Lieferadresse->cTel = $correctPhoneNumber;
                $Lieferadresse->updateInDB();
            }

            // Optionally update in Kunde
            if (0 === intval($_SESSION['Bestellung']->kLieferadresse)) {
                $_SESSION['Kunde']->cTel = $correctPhoneNumber;

                // Update in db.
                if (!empty($_SESSION['Kunde']->kKunde)) {
                    $Kunde = new Kunde($_SESSION['Kunde']->kKunde);
                    $Kunde->cTel = $correctPhoneNumber;
                    $Kunde->updateInDB();
                }
            }
        }

        // For mobile phone.
        if (!$PhoneNumberMobile->isEmpty()
            && (2 === $settings['wrongFormatAction'])
            && $PhoneNumberMobile->anyFormatRequired()
            && $PhoneNumberMobile->isMandatory()
            && $PhoneNumberMobile->isWrongFormat()
        ) {
            $hasErrors = true;
            $blockSubmit = true;
            $format = $PhoneNumberMobile->getRequiredFormat();
            $mapping = [
                '{$requiredFormat}' => $format
            ];
            $errorMessages[] = str_replace(
                array_keys($mapping),
                array_values($mapping),
                $translations['endereco_jtl4_phs_phone_format_needs_correction_mobile']
            );
        }

        if (!$PhoneNumberMobile->isEmpty()
            && (3 === $settings['wrongFormatAction'])
            && $PhoneNumberMobile->anyFormatRequired()
            && $PhoneNumberMobile->isQuestioned()
            && $PhoneNumberMobile->isWrongFormat()
        ) {
            $hasErrors = true;
            $blockSubmit = true;
            $format = $PhoneNumberMobile->getRequiredFormat();
            $mapping = [
                '{$requiredFormat}' => $format
            ];
            $errorMessages[] = str_replace(
                array_keys($mapping),
                array_values($mapping),
                $translations['endereco_jtl4_phs_phone_format_needs_correction_mobile']
            );
        }

        if (!$PhoneNumberMobile->isEmpty()
            && (4 === $settings['wrongFormatAction'])
            && $PhoneNumberMobile->anyFormatRequired()
            && $PhoneNumberMobile->isQuestioned()
            && $PhoneNumberMobile->isWrongFormat()
        ) {
            $hasErrors = false;
            $shouldReload = true;
            $blockSubmit = true;

            $correctPhoneNumber = $PhoneNumberMobile->getFormattedNumber();

            // Update in Lieferung.
            $_SESSION['Lieferadresse']->cMobil = $correctPhoneNumber;

            // Update in db.
            if (!empty($_SESSION['Lieferadresse']->kLieferadresse)) {
                $Lieferadresse = new Lieferadresse($_SESSION['Lieferadresse']->kLieferadresse);
                $Lieferadresse->cMobil = $correctPhoneNumber;
                $Lieferadresse->updateInDB();
            }

            // Optionally update in Kunde
            if (0 === intval($_SESSION['Bestellung']->kLieferadresse)) {
                $_SESSION['Kunde']->cMobil = $correctPhoneNumber;

                // Update in db.
                if (!empty($_SESSION['Kunde']->kKunde)) {
                    $Kunde = new Kunde($_SESSION['Kunde']->kKunde);
                    $Kunde->cMobil = $correctPhoneNumber;
                    $Kunde->updateInDB();
                }
            }
        }

        /**
         * The next checks handle the condition in "Wenn die Mobilfunknetz oder Festnetznummern im falschen Telefonfeld steht" setting.
         */
        if (!$PhoneNumberMobile->isMobile()
            && $PhoneNumberFix->isMobile()
            && $PhoneNumberMobile->isQuestioned()
            && $PhoneNumberMobile->isEmpty()
            && (2 === $settings['wrongTypeAction'])
        ) {
            $hasErrors = false;
            $shouldReload = true;
            $blockSubmit = true;

            // Update in Lieferung.
            $_SESSION['Lieferadresse']->cMobil = $_SESSION['Lieferadresse']->cTel;
            $_SESSION['Lieferadresse']->cTel = '';

            // Update in db.
            if (!empty($_SESSION['Lieferadresse']->kLieferadresse)) {
                $Lieferadresse = new Lieferadresse($_SESSION['Lieferadresse']->kLieferadresse);
                $Lieferadresse->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $Lieferadresse->cTel = $_SESSION['Lieferadresse']->cTel;
                $Lieferadresse->updateInDB();
            }

            // Optionally update in Kunde
            if (0 === intval($_SESSION['Bestellung']->kLieferadresse)) {
                $_SESSION['Kunde']->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $_SESSION['Kunde']->cTel = $_SESSION['Lieferadresse']->cTel;

                // Update in db.
                if (!empty($_SESSION['Kunde']->kKunde)) {
                    $Kunde = new Kunde($_SESSION['Kunde']->kKunde);
                    $Kunde->cMobil = $_SESSION['Lieferadresse']->cMobil;
                    $Kunde->cTel = $_SESSION['Lieferadresse']->cTel;
                    $Kunde->updateInDB();
                }
            }
        }

        if ($PhoneNumberMobile->isFixedLine()
            && !$PhoneNumberFix->isFixedLine()
            && $PhoneNumberFix->isQuestioned()
            && $PhoneNumberFix->isEmpty()
            && (2 === $settings['wrongTypeAction'])
        ) {
            $hasErrors = false;
            $shouldReload = true;
            $blockSubmit = true;

            // Update in Lieferung.
            $_SESSION['Lieferadresse']->cTel = $_SESSION['Lieferadresse']->cMobil;
            $_SESSION['Lieferadresse']->cMobil = '';

            // Update in db.
            if (!empty($_SESSION['Lieferadresse']->kLieferadresse)) {
                $Lieferadresse = new Lieferadresse($_SESSION['Lieferadresse']->kLieferadresse);
                $Lieferadresse->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $Lieferadresse->cTel = $_SESSION['Lieferadresse']->cTel;
                $Lieferadresse->updateInDB();
            }

            // Optionally update in Kunde
            if (0 === intval($_SESSION['Bestellung']->kLieferadresse)) {
                $_SESSION['Kunde']->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $_SESSION['Kunde']->cTel = $_SESSION['Lieferadresse']->cTel;

                // Update in db.
                if (!empty($_SESSION['Kunde']->kKunde)) {
                    $Kunde = new Kunde($_SESSION['Kunde']->kKunde);
                    $Kunde->cMobil = $_SESSION['Lieferadresse']->cMobil;
                    $Kunde->cTel = $_SESSION['Lieferadresse']->cTel;
                    $Kunde->updateInDB();
                }
            }
        }

        if (!$PhoneNumberMobile->isMobile()
            && $PhoneNumberFix->isMobile()
            && $PhoneNumberMobile->isQuestioned()
            && (3 === $settings['wrongTypeAction'])
        ) {
            $hasErrors = false;
            $shouldReload = true;
            $blockSubmit = true;

            // Update in Lieferung.
            $_SESSION['Lieferadresse']->cMobil = $_SESSION['Lieferadresse']->cTel;
            $_SESSION['Lieferadresse']->cTel = '';

            // Update in db.
            if (!empty($_SESSION['Lieferadresse']->kLieferadresse)) {
                $Lieferadresse = new Lieferadresse($_SESSION['Lieferadresse']->kLieferadresse);
                $Lieferadresse->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $Lieferadresse->cTel = $_SESSION['Lieferadresse']->cTel;
                $Lieferadresse->updateInDB();
            }

            // Optionally update in Kunde
            if (0 === intval($_SESSION['Bestellung']->kLieferadresse)) {
                $_SESSION['Kunde']->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $_SESSION['Kunde']->cTel = $_SESSION['Lieferadresse']->cTel;

                // Update in db.
                if (!empty($_SESSION['Kunde']->kKunde)) {
                    $Kunde = new Kunde($_SESSION['Kunde']->kKunde);
                    $Kunde->cMobil = $_SESSION['Lieferadresse']->cMobil;
                    $Kunde->cTel = $_SESSION['Lieferadresse']->cTel;
                    $Kunde->updateInDB();
                }
            }
        }

        if ($PhoneNumberMobile->isFixedLine()
            && !$PhoneNumberFix->isFixedLine()
            && $PhoneNumberFix->isQuestioned()
            && (3 === $settings['wrongTypeAction'])
        ) {
            $hasErrors = false;
            $shouldReload = true;
            $blockSubmit = true;

            // Update in Lieferung.
            $_SESSION['Lieferadresse']->cTel = $_SESSION['Lieferadresse']->cMobil;
            $_SESSION['Lieferadresse']->cMobil = '';

            // Update in db.
            if (!empty($_SESSION['Lieferadresse']->kLieferadresse)) {
                $Lieferadresse = new Lieferadresse($_SESSION['Lieferadresse']->kLieferadresse);
                $Lieferadresse->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $Lieferadresse->cTel = $_SESSION['Lieferadresse']->cTel;
                $Lieferadresse->updateInDB();
            }

            // Optionally update in Kunde
            if (0 === intval($_SESSION['Bestellung']->kLieferadresse)) {
                $_SESSION['Kunde']->cMobil = $_SESSION['Lieferadresse']->cMobil;
                $_SESSION['Kunde']->cTel = $_SESSION['Lieferadresse']->cTel;

                // Update in db.
                if (!empty($_SESSION['Kunde']->kKunde)) {
                    $Kunde = new Kunde($_SESSION['Kunde']->kKunde);
                    $Kunde->cMobil = $_SESSION['Lieferadresse']->cMobil;
                    $Kunde->cTel = $_SESSION['Lieferadresse']->cTel;
                    $Kunde->updateInDB();
                }
            }
        }

        if ($shouldReload) {
            $file = $oPlugin->cFrontendPfad . 'template/reloadpage.tpl';
            $html = $smarty->fetch($file);
            pq($advancedSettings['other']['ordsub'])->before($html);
        } elseif ($hasErrors) {
            $file = $oPlugin->cFrontendPfad . 'template/orderalert.tpl';
            $smarty->assign('endrc_translations', $translations);
            $smarty->assign('endrc_errormessages', $errorMessages);
            $html = $smarty->fetch($file);
            pq($advancedSettings['other']['ordsub'])->before($html);
        }

        // If needs to block order, block it.
        if ($blockSubmit) {
            pq($advancedSettings['other']['ordsub'])->attr('disabled', 'disabled');
        }
    }
}



