<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Sale,
    \Bitrix\Main\Loader;

class InfProfile extends CBitrixComponent
{
    function onPrepareComponentParams($arParams): array
    {
        return $arParams;
    }

    public function executeComponent(): void
    {
        // Если нет валидного кеша (то есть нужно запросить
        // данные и сделать валидный кеш)
        if ($this->StartResultCache()) {
            global $USER_FIELD_MANAGER;
            global $USER;
            global $APPLICATION;

            $arResult["ID"] = intval($USER->GetID());
            $arResult["GROUP_POLICY"] = CUser::GetGroupPolicy($arResult["ID"]);

            $this->arParams['SEND_INFO'] = $this->arParams['SEND_INFO'] == 'Y' ? 'Y' : 'N';
            $this->arParams['CHECK_RIGHTS'] = $this->arParams['CHECK_RIGHTS'] == 'Y' ? 'Y' : 'N';

            $this->arParams['EDITABLE_EXTERNAL_AUTH_ID'] = isset($this->arParams['EDITABLE_EXTERNAL_AUTH_ID']) && is_array($this->arParams['EDITABLE_EXTERNAL_AUTH_ID'])
                ? $this->arParams['EDITABLE_EXTERNAL_AUTH_ID']
                : [];

            if (!($this->arParams['CHECK_RIGHTS'] == 'N' || $USER->CanDoOperation('edit_own_profile')) || $arResult["ID"] <= 0) {
                /* $APPLICATION->ShowAuthForm("");
                 return;*/
            }

            $arResult["PHONE_REGISTRATION"] = (COption::GetOptionString("main", "new_user_phone_auth", "N") == "Y");
            $arResult["PHONE_REQUIRED"] = ($arResult["PHONE_REGISTRATION"] && COption::GetOptionString("main", "new_user_phone_required", "N") == "Y");
            $arResult["EMAIL_REGISTRATION"] = (COption::GetOptionString("main", "new_user_email_auth", "Y") <> "N");
            $arResult["EMAIL_REQUIRED"] = ($arResult["EMAIL_REGISTRATION"] && COption::GetOptionString("main", "new_user_email_required", "Y") <> "N");
            $arResult["PHONE_CODE_RESEND_INTERVAL"] = CUser::PHONE_CODE_RESEND_INTERVAL;

            $strError = '';

            if ($_SERVER["REQUEST_METHOD"] == "POST" && ($_REQUEST["save"] <> '' || $_REQUEST["apply"] <> '') && check_bitrix_sessid()) {
                if (COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y') {
                    //possible encrypted user password
                    $sec = new CRsaSecurity();
                    if (($arKeys = $sec->LoadKeys())) {
                        $sec->SetKeys($arKeys);
                        $errno = $sec->AcceptFromForm(array('NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM'));
                        if ($errno == CRsaSecurity::ERROR_SESS_CHECK)
                            $strError .= GetMessage("main_profile_sess_expired") . '<br />';
                        elseif ($errno < 0)
                            $strError .= GetMessage("main_profile_decode_err", array("#ERRCODE#" => $errno)) . '<br />';
                    }
                }

                if ($strError == '') {
                    $bOk = false;
                    $obUser = new CUser;

                    $arPERSONAL_PHOTO = $_FILES["PERSONAL_PHOTO"];
                    $arWORK_LOGO = $_FILES["WORK_LOGO"];

                    $rsUser = CUser::GetByID($arResult["ID"]);
                    $arUser = $rsUser->Fetch();
                    if ($arUser) {
                        $arPERSONAL_PHOTO["old_file"] = $arUser["PERSONAL_PHOTO"];
                        $arPERSONAL_PHOTO["del"] = $_REQUEST["PERSONAL_PHOTO_del"];

                        $arWORK_LOGO["old_file"] = $arUser["WORK_LOGO"];
                        $arWORK_LOGO["del"] = $_REQUEST["WORK_LOGO_del"];
                    }

                    $arEditFields = array(
                        "TITLE",
                        "NAME",
                        "LAST_NAME",
                        "SECOND_NAME",
                        "EMAIL",
                        "LOGIN",
                        "PERSONAL_PROFESSION",
                        "PERSONAL_WWW",
                        "PERSONAL_ICQ",
                        "PERSONAL_GENDER",
                        "PERSONAL_BIRTHDAY",
                        "PERSONAL_PHONE",
                        "PERSONAL_FAX",
                        "PERSONAL_MOBILE",
                        "PERSONAL_PAGER",
                        "PERSONAL_STREET",
                        "PERSONAL_MAILBOX",
                        "PERSONAL_CITY",
                        "PERSONAL_STATE",
                        "PERSONAL_ZIP",
                        "PERSONAL_COUNTRY",
                        "PERSONAL_NOTES",
                        "WORK_COMPANY",
                        "WORK_DEPARTMENT",
                        "WORK_POSITION",
                        "WORK_WWW",
                        "WORK_PHONE",
                        "WORK_FAX",
                        "WORK_PAGER",
                        "WORK_STREET",
                        "WORK_MAILBOX",
                        "WORK_CITY",
                        "WORK_STATE",
                        "WORK_ZIP",
                        "WORK_COUNTRY",
                        "WORK_PROFILE",
                        "WORK_NOTES",
                        "TIME_ZONE",
                        "PHONE_NUMBER",
                        "UF_ADDRESSES",
                        "UF_UF_ADDRESSES_NAMES",
                        "UF_BIRTHDATE"
                    );

                    $arFields = array();
                    foreach ($arEditFields as $field) {
                        if (isset($_REQUEST[$field])) {
                            $arFields[$field] = $_REQUEST[$field];
                        }
                    }

                    if (isset($_REQUEST["AUTO_TIME_ZONE"])) {
                        $arFields["AUTO_TIME_ZONE"] = ($_REQUEST["AUTO_TIME_ZONE"] == "Y" || $_REQUEST["AUTO_TIME_ZONE"] == "N" ? $_REQUEST["AUTO_TIME_ZONE"] : "");
                    }

                    if ($USER->IsAdmin() && isset($_REQUEST["ADMIN_NOTES"])) {
                        $arFields["ADMIN_NOTES"] = $_REQUEST["ADMIN_NOTES"];
                    }

                    $arResult['CAN_EDIT_PASSWORD'] = $arUser['EXTERNAL_AUTH_ID'] == ''
                        || in_array($arUser['EXTERNAL_AUTH_ID'], $this->arParams['EDITABLE_EXTERNAL_AUTH_ID'], true);

                    if ($_REQUEST["NEW_PASSWORD"] <> '' && $arResult['CAN_EDIT_PASSWORD']) {
                        $arFields["PASSWORD"] = $_REQUEST["NEW_PASSWORD"];
                        $arFields["CONFIRM_PASSWORD"] = $_REQUEST["NEW_PASSWORD_CONFIRM"];
                    }

                    $arFields["PERSONAL_PHOTO"] = $arPERSONAL_PHOTO;
                    $arFields["WORK_LOGO"] = $arWORK_LOGO;

                    if ($arUser) {
                        if ($arUser['EXTERNAL_AUTH_ID'] <> '') {
                            $arFields['EXTERNAL_AUTH_ID'] = $arUser['EXTERNAL_AUTH_ID'];
                        }
                    }

                    $USER_FIELD_MANAGER->EditFormAddFields("USER", $arFields);

                    if ($obUser->Update($arResult["ID"], $arFields)) {
                        if ($arResult["PHONE_REGISTRATION"] == true && $arFields["PHONE_NUMBER"] <> '') {
                            if (!($phone = \Bitrix\Main\UserPhoneAuthTable::getRowById($arResult["ID"]))) {
                                $phone = ["PHONE_NUMBER" => "", "CONFIRMED" => "N"];
                            }

                            $arFields["PHONE_NUMBER"] = \Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($arFields["PHONE_NUMBER"]);

                            if ($arFields["PHONE_NUMBER"] <> $phone["PHONE_NUMBER"] || $phone["CONFIRMED"] <> 'Y') {
                                //added or updated the phone number for the user, now sending a confirmation SMS
                                list($code, $phoneNumber) = CUser::GeneratePhoneCode($arResult["ID"]);

                                $sms = new \Bitrix\Main\Sms\Event(
                                    "SMS_USER_CONFIRM_NUMBER",
                                    [
                                        "USER_PHONE" => $phoneNumber,
                                        "CODE" => $code,
                                    ]
                                );
                                $smsResult = $sms->send(true);

                                if (!$smsResult->isSuccess()) {
                                    $strError .= implode("<br />", $smsResult->getErrorMessages());
                                }

                                $arResult["SHOW_SMS_FIELD"] = true;
                                $arResult["SIGNED_DATA"] = \Bitrix\Main\Controller\PhoneAuth::signData(['phoneNumber' => $phoneNumber]);
                            }
                        }
                    } else {
                        $strError .= $obUser->LAST_ERROR;
                    }
                }

                if ($strError == '') {
                    if ($this->arParams['SEND_INFO'] == 'Y')
                        $obUser->SendUserInfo($arResult["ID"], SITE_ID, GetMessage("main_profile_update"), true);

                    $bOk = true;
                }
            }

            $rsUser = CUser::GetByID($arResult["ID"]);
            if (!$arResult["arUser"] = $rsUser->GetNext()) {
                $arResult["ID"] = 0;
            }

            $arResult["arUser"]["PHONE_NUMBER"] = "";

            if ($strError <> '') {
                static $skip = array("PERSONAL_PHOTO" => 1, "WORK_LOGO" => 1, "forum_AVATAR" => 1, "blog_AVATAR" => 1);
                foreach ($_POST as $k => $val) {
                    if (!isset($skip[$k])) {
                        if (!is_array($val)) {
                            $val = htmlspecialcharsex($val);
                        }
                        if (mb_strpos($k, "forum_") === 0) {
                            $arResult["arForumUser"][mb_substr($k, 6)] = $val;
                        } elseif (mb_strpos($k, "blog_") === 0) {
                            $arResult["arBlogUser"][mb_substr($k, 5)] = $val;
                        } elseif (mb_strpos($k, "student_") === 0) {
                            $arResult["arStudent"][mb_substr($k, 8)] = $val;
                        } else {
                            $arResult["arUser"][$k] = $val;
                        }
                    }
                }
            }

            $arResult["FORM_TARGET"] = $APPLICATION->GetCurPage();

            $arResult["IS_ADMIN"] = $USER->IsAdmin();
            $arResult['CAN_EDIT_PASSWORD'] = $arUser['EXTERNAL_AUTH_ID'] == ''
                || in_array($arUser['EXTERNAL_AUTH_ID'], $this->arParams['EDITABLE_EXTERNAL_AUTH_ID'], true);

            $arCountries = GetCountryArray();
            $arResult["COUNTRY_SELECT"] = SelectBoxFromArray("PERSONAL_COUNTRY", $arCountries, $arResult["arUser"]["PERSONAL_COUNTRY"], GetMessage("USER_DONT_KNOW"));
            $arResult["COUNTRY_SELECT_WORK"] = SelectBoxFromArray("WORK_COUNTRY", $arCountries, $arResult["arUser"]["WORK_COUNTRY"], GetMessage("USER_DONT_KNOW"));

            $arResult["strProfileError"] = $strError;
            $arResult["BX_SESSION_CHECK"] = bitrix_sessid_post();

            $arResult["DATE_FORMAT"] = CLang::GetDateFormat("SHORT");

            $arResult["COOKIE_PREFIX"] = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");
            if ($arResult["COOKIE_PREFIX"] == '')
                $arResult["COOKIE_PREFIX"] = "BX";

            // ********************* User properties ***************************************************
            $arResult["USER_PROPERTIES"] = array("SHOW" => "Y");
            //if (!empty($this->arParams["USER_PROPERTY"])) {
            $arUserFields = $USER_FIELD_MANAGER->GetUserFields("USER", $arResult["ID"], LANGUAGE_ID);
            if (count($this->arParams["USER_PROPERTY"]) > 0) {
                foreach ($arUserFields as $FIELD_NAME => $arUserField) {
                    if (!in_array($FIELD_NAME, $this->arParams["USER_PROPERTY"]))
                        continue;
                    $arUserField["EDIT_FORM_LABEL"] = $arUserField["EDIT_FORM_LABEL"] <> '' ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"];
                    $arUserField["EDIT_FORM_LABEL"] = htmlspecialcharsEx($arUserField["EDIT_FORM_LABEL"]);
                    $arUserField["~EDIT_FORM_LABEL"] = $arUserField["EDIT_FORM_LABEL"];
                    $arResult["USER_PROPERTIES"]["DATA"][$FIELD_NAME] = $arUserField;
                }
            }
            if (!empty($arResult["USER_PROPERTIES"]["DATA"]))
                $arResult["USER_PROPERTIES"]["SHOW"] = "Y";
            $arResult["bVarsFromForm"] = ($strError == '' ? false : true);
            //}
            // ******************** /User properties ***************************************************

            if ($bOk)
                $arResult['DATA_SAVED'] = 'Y';

            //time zones
            $arResult["TIME_ZONE_ENABLED"] = CTimeZone::Enabled();
            if ($arResult["TIME_ZONE_ENABLED"])
                $arResult["TIME_ZONE_LIST"] = CTimeZone::GetZones();

            //secure authorization
            $arResult["SECURE_AUTH"] = false;
            if (!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y') {
                $sec = new CRsaSecurity();
                if (($arKeys = $sec->LoadKeys())) {
                    $sec->SetKeys($arKeys);
                    $sec->AddToForm('form1', array('NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM'));
                    $arResult["SECURE_AUTH"] = true;
                }
            }

            //socialservices
            $arResult["SOCSERV_ENABLED"] = IsModuleInstalled("socialservices");

            $result['PROFILE'] = $arResult;
            $result['ORDER_HISTORY'] = $this->getOrderHistory();
            $result['FAV'] = $this->getFavorite();
            $result['SUBSCRIPTIONS'] = $this->getSubscriptions();
            $result['REVIEWS'] = $this->getReviews();
            $result['RECIEPTS'] = $this->getReciepts();
            $this->arResult = $result;
            $this->includeComponentTemplate();
        }
    }

    private function getOrderHistory(): array
    {
        global $USER;
        Loader::includeModule('sale');
        $orderHisrory = [];
        $cdbOrders = CSaleOrder::GetList(["ID" => "DESC"], ["USER_ID" => $USER->GetID()], false, false);
        while ($order = $cdbOrders->Fetch()) {
            $arOrder = $order;
            $cdbBasket = CSaleBasket::GetList([], ['=ORDER_ID' => $order['ID']], false, false);
            while ($basket = $cdbBasket->fetch()) {
                $arBasket = $basket;
                $cdbEls = CIBlockElement::GetList(
                    ['ID' => 'ASC'],
                    ['IBLOCK_ID' => '5', '=ID' => $basket['PRODUCT_ID']],
                    false,
                    false,
                    ['ID', 'NAME', 'ACTIVE', 'catalog_PRICE_1', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']
                );
                while ($ob = $cdbEls->GetNext()) {
                    if ($ob['ID'] == $basket['PRODUCT_ID']) {
                        $item = $ob;
                        $price = CCatalogProduct::GetOptimalPrice($item['ID'], 1, $USER->GetUserGroupArray(), 'N');
                        $item['CATALOG_REALPRICE'] = $price['DISCOUNT_PRICE'] ? $price['DISCOUNT_PRICE'] : $price['CATALOG_PRICE_1'];
                        $item['CATALOG_RATIO'] = CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $item['ID']], false, false)->fetch()['RATIO'];
                        $arBasket['CATITEM'] = $item;
                    }
                }
                $arOrder['BASKET'][] = $arBasket;
            }
            $orderHisrory[] = $arOrder;
        }

        return $orderHisrory;
    }

    private function getFavorite(): array
    {
        $arFavorites = $favIds = [];
        global $USER;
        Loader::includeModule('catalog');
        $userId = $USER->IsAuthorized() ? $USER->GetID() : 0;
        $session = $_COOKIE['PHPSESSID'];

        $favIds = \Dev\Helpers\FavoriteHelper::getItems($session, $userId ?: "")['UF_ITEMS'];
        $cdbEls = CIBlockElement::GetList(
            ['ID' => 'ASC'],
            ['IBLOCK_ID' => '5', '=ID' => $favIds],
            false,
            false,
            ['ID', 'NAME', 'catalog_PRICE_1', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL']
        );
        while ($ob = $cdbEls->GetNext()) {
            if (!empty($favIds)) {
                if (in_array($ob['ID'], $favIds)) {
                    $item = $ob;
                    $price = CCatalogProduct::GetOptimalPrice($item['ID'], 1, $USER->GetUserGroupArray(), 'N');
                    $item['CATALOG_REALPRICE'] = $price['DISCOUNT_PRICE'] ? $price['DISCOUNT_PRICE'] : $price['CATALOG_PRICE_1'];
                    $item['CATALOG_RATIO'] = CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $item['ID']], false, false)->fetch()['RATIO'];
                    $arFavorites[] = $item;
                }
            }
        }

        return $arFavorites;
    }

    private
    function getSubscriptions(): bool
    {
        global $USER;
        Loader::includeModule('subscribe');
        $res = \Dev\SendPulse\AddressBookController::getEmailList();
        foreach ($res as $addressItem) {
            $arEmails[] = $addressItem['email'];
        }

        return in_array($USER->GetByID($USER->GetID())->fetch()['EMAIL'], $arEmails);
    }

    private
    function getReviews(): array
    {
        global $USER;
        $arReviews = [];
        $cdbReciepts = CIBlockElement::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => '14', 'CREATEDY_BY' => $USER->GetID()], false, false);
        while ($ob = $cdbReciepts->fetch()) {
            if ($ob['CREATED_BY'] == $USER->GetID())
                $arReviews[] = $ob;
        }
        return $arReviews;
    }

    private
    function getReciepts(): array
    {
        $arReciepts = $recIds = [];
        global $USER;
        Loader::includeModule('catalog');
        $userId = $USER->IsAuthorized() ? $USER->GetID() : 0;
        $session = $_COOKIE['PHPSESSID'];

        $recIds = \Dev\Helpers\FavoriteRecieptsHelper::getItems($session, $userId ?: "")['UF_ITEMS'];
        $cdbReciepts = CIBlockElement::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => '8', '=ID' => $recIds], false, false);
        while ($ob = $cdbReciepts->fetch()) {
            if (!empty($recIds)) {
                if (in_array($ob['ID'], $recIds)) {
                    $arReciepts[] = $ob;
                }
            }
        }

        return $arReciepts;
    }
}