<?php
$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$arSort = [];
$arFilter = [
    'IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID,
    '!PROPERTY_DISCOUNT_PERCENT' => false
];
$arOrder = false;
$arNav = false;
$arSelect = ['ID', 'CATALOG_PRICE_1', 'PROPERTY_DISCOUNT_PERCENT'];

$resDB = CIBlockElement::GetList(
    $arSort,
    $arFilter,
    $arOrder,
    $arNav,
    $arSelect
);

while ($item = $resDB->Fetch()) {
    $newPrice = $item['CATALOG_PRICE_1'] - $item['CATALOG_PRICE_1'] * ($item['PROPERTY_DISCOUNT_PERCENT_VALUE'] / 100);
    $newPrice = round($newPrice);

    CIBlockElement::SetPropertyValuesEx($item['ID'], false, array('COST_DISCOUNT' => $newPrice));
}