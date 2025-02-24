<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

foreach ($arResult['ITEMS'] as &$arItem) {

    $timeToFormat = $arItem['PROPERTIES']['SHOW_DATE']['VALUE'] ?: $arItem['TIMESTAMP_X'];

    $arItem['DATE_CREATE'] = FormatDate(
        array(
            "d" => 'j F',                   // выведет "9 июля", если месяц прошел
            //"" => 'j F Y',                    // выведет "9 июля 2012", если год прошел
        ),
        MakeTimeStamp($timeToFormat),
        time()
    );

    $arItem['NEED_SHOW_MORE'] = strlen($arItem['PREVIEW_TEXT']) > 150 ? 'Y' : 'N';
}