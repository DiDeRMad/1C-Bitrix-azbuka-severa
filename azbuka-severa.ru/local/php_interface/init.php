<?php
/* Подключаем файл для SMTP */
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php");
use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
/* Подключаем файл с константами */
require($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/includes/defines.php");
/* Подключаем файл с функциями */
require($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/includes/functions.php");
/* Подключаем файл событиями */
require($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/includes/events.php");
/* Подключаем файл с глобальными переменными */
require($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/includes/globals.php");

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/services/smsru/sms.ru.php");

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/helpers/helperinterior.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/classes/helpers/helpertags.php");

/* Автоматическая генерация статической версии header-bottom меню */
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/includes/menugen.php");

/* Пользовательская функция дл обработки цен в Сниппете для поисковых роботов */
include("informula/seo_settings_func.php");

/* Хэндлеры */
include ($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/includes/_handlers.php');

/* Autoload кастомных классов */
Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'Dev\Helpers\HLBlockHelper' => '/local/classes/helpers/hlbhelper.php',
    'Dev\Helpers\SocialHelper' => '/local/classes/helpers/socialhelper.php',
    'Dev\Helpers\FavoriteHelper' => '/local/classes/helpers/favoritehelper.php',
    'Dev\Helpers\FavoriteRecieptsHelper' => '/local/classes/helpers/favoriterecieptshelper.php',
    'Dev\Helpers\AbstractDataManager' => '/local/classes/helpers/abstractdatamanager.php',
    'Dev\Helpers\GlobalMenuHelper' => '/local/classes/helpers/globalmenuhelper.php',

    'Dev\SendPulse\BasicController' => '/local/classes/services/sendpulse/basic.php',
    'Dev\SendPulse\AddressBookController' => '/local/classes/services/sendpulse/addressbook.php',

    'Dadata\CleanClient' => '/local/classes/services/dadata/CleanClient.php',
    'Dadata\ClientBase' => '/local/classes/services/dadata/ClientBase.php',
    'Dadata\DadataClient' => '/local/classes/services/dadata/DadataClient.php',
    'Dadata\ProfileClient' => '/local/classes/services/dadata/ProfileClient.php',
    'Dadata\Settings' => '/local/classes/services/dadata/Settings.php',
    'Dadata\SuggestClient' => '/local/classes/services/dadata/SuggestClient.php',
]);

function randomNumber($length) {
    $result = '';
    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}


AddEventHandler("main", "OnProlog", "MyOnPageStartFunction");
function MyOnPageStartFunction() { //проверяем куки и авторизируем при вызове OnProlog (при каждом вызове)
    global $USER;
    global $APPLICATION;
    $ispage = $APPLICATION->GetCurPage();
    if ($USER->IsAuthorized()) {
        $auth = "Y";
    } else {
        $auth = "N";
        $ses1 = $APPLICATION->get_cookie("UF_SESSIDS_CODE");
        $ses2 = $APPLICATION->get_cookie("UF_SESSIDS_LOGIN");

        if(!empty($ses1) && !empty($ses2)) {
            $rsUser = CUser::GetByLogin($ses2);

            if ($arUser = $rsUser->Fetch()) {

                if($arUser['LOGIN']==$ses2 && $arUser['UF_SESSIDS']==$ses1) {
                    $USER->Authorize($arUser['ID']);
                    header("Location: ".$ispage);
                    exit();
                }
            }
        }
    }
}


AddEventHandler("main", "OnAfterUserAuthorize", Array("AddCookie", "OnAfterUserAuthorizeHandler"));
class AddCookie { //добавляем куки и свойство пользователю при Authorize
    public static function OnAfterUserAuthorizeHandler($arUser) {
        if($arUser["user_fields"]['ID'] > 0) {

            global $APPLICATION;

            $user_old_cookies = $APPLICATION->get_cookie("UF_SESSIDS_CODE"); // старые куки
            $user = new CUser;
            $user_info = $user->GetByID($arUser["user_fields"]['ID']); //данные пользователя

            if ($oldUserArr = $user_info->Fetch()) {
                if($oldUserArr['UF_SESSIDS'] != $user_old_cookies || empty($oldUserArr['UF_SESSIDS'])) {  // если у пользователя пустое поле или не совпадают куки с этим полем

                    $regrund = randomNumber(10);
                    $mydate = date("His");
                    $sessidf = $regrund.$mydate.$arUser["user_fields"]['ID'];
                    $sessidf = md5($sessidf);

                    //тут добавляем новые куки
                    $APPLICATION->set_cookie("UF_SESSIDS_CODE", $sessidf); // добавляем строку в куки юзера
                    $APPLICATION->set_cookie("UF_SESSIDS_LOGIN", $arUser["user_fields"]['LOGIN']); // добавляем логин в куки юзера

                    $user->Update($arUser["user_fields"]['ID'],array("UF_SESSIDS" => $sessidf)); // добавляем строку в поле юзера

                }
            }
        }
    }
}


AddEventHandler("main", "OnAfterUserLogout", Array("ClearUserString", "DeleteSessionData"));
class ClearUserString { // очищаем пользовательское поле при logout
    public static function DeleteSessionData($arUser) {
        $user = new CUser;
        $user->Update($arUser['USER_ID'],array("UF_SESSIDS" => ''));
    }
}

class ProductAgent
{
	public static function removeOldItemsFromNew(int $iblockId, int $sectionId, int $monthsToKeep)
	{
		$dateLimit = new DateTime();
		$dateLimit->add("-$monthsToKeep months");

		$ciBlockElement = new CIBlockElement;

		$elRequest = $ciBlockElement->GetList(
			[],
			[
				'IBLOCK_ID' => $iblockId,
				'SECTION_ID' => $sectionId,
				'<DATE_CREATE' => $dateLimit,
				'INCLUDE_SUBSECTIONS' => 'Y'
			],
			false,
			false,
			['ID', 'IBLOCK_ID', 'IBLOCK_SECTION']
		);

		while ($ob = $elRequest->GetNextElement()) {
			$elFields = $ob->GetFields();
			$secFields = $ciBlockElement->GetElementGroups($elFields['ID'], true);
			$arSections = array();

			while ($section = $secFields->Fetch()) {
				if ($section["ID"] !== "$sectionId") {
					array_push($arSections, $section["ID"]);
				}
			}

			$ciBlockElement->Update($elFields['ID'], ['IBLOCK_SECTION' => $arSections]);
		}

		return "ProductAgent::removeOldItemsFromNew($iblockId, $sectionId, $monthsToKeep);";
	}
} require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.extramail/classes/general/include_part.php");?>