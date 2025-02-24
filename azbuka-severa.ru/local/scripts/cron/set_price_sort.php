<?php
$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$arSort = [];
$arFilter = [
    'IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID,
];
$arOrder = false;
$arNav = false;
$arSelect = ['ID', 'CATALOG_PRICE_1', 'PROPERTY_COST_DISCOUNT', 'PROPERTY_DISCOUNT_PERCENT'];

$resDB = CIBlockElement::GetList(
    $arSort,
    $arFilter,
    $arOrder,
    $arNav,
    $arSelect
);

while ($item = $resDB->Fetch()) {
    $newPrice = $item['PROPERTY_COST_DISCOUNT_VALUE'] ?: $item['CATALOG_PRICE_1'];
    $newPrice = $item['PROPERTY_DISCOUNT_PERCENT_VALUE'] ? $newPrice : $item['CATALOG_PRICE_1'];
    CIBlockElement::SetPropertyValuesEx($item['ID'], false, array('SORT_PRICE' => $newPrice));
}