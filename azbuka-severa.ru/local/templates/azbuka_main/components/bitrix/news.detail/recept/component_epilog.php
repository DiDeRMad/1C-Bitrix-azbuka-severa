<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 */

global $APPLICATION;

$APPLICATION->SetPageProperty('og:title', $arResult['NAME']);
$APPLICATION->SetPageProperty('og:description', $arResult['DESCRIPTION']);
$APPLICATION->SetPageProperty('og:image', 'https://' . $_SERVER['SERVER_NAME'] . $arResult['PICTURE']);