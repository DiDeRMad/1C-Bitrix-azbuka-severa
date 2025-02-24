<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('iblock');
Cmodule::IncludeModule('catalog');
CModule::IncludeModule('nkhost.phpexcel');
global $PHPEXCELPATH;

// Ваш код далее
require_once ($PHPEXCELPATH . '/PHPExcel/IOFactory.php');

$xls = PHPExcel_IOFactory::load(__DIR__ . "/azbuka.xls");
// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
// Получаем активный лист
$sheet = $xls->getActiveSheet();
$arUpdate = [];

for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
    $nColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
    for ($j = 0; $j < $nColumn; $j++) {
        $arProducts[$i][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();
    }
    $arUpdate[$sheet->getCellByColumnAndRow(1, $i)->getValue()] = [
        'IIKO_ID' => $sheet->getCellByColumnAndRow(1, $i)->getValue(),
        'WEIGHT' => $sheet->getCellByColumnAndRow(13, $i)->getValue(),
        'CHARACTER_STORAGE_CONDITIONS' => $sheet->getCellByColumnAndRow(19, $i)->getValue(),
        'CHARACTER_PACKAGE' => $sheet->getCellByColumnAndRow(21, $i)->getValue(),
        'CHARACTER_STRUCTURE' => $sheet->getCellByColumnAndRow(23, $i)->getValue(),
        'DETAIL_TEXT' => $sheet->getCellByColumnAndRow(24, $i)->getValue(),
        'CHARACTER_PRODUCT_SCORE' => [
            'belok' => $sheet->getCellByColumnAndRow(14, $i)->getValue(),
            'jir' => $sheet->getCellByColumnAndRow(15, $i)->getValue(),
            'yglevod' => $sheet->getCellByColumnAndRow(16, $i)->getValue(),
            'kkal' => $sheet->getCellByColumnAndRow(17, $i)->getValue(),
        ],
    ];
}

printer($arUpdate);

$arSort = [];
$arFilter = ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, 'PROPERTY_IIKO_ID' => array_keys($arUpdate), '!ID' => 912];
$arOrder = false;
$arNav = [];
$arSelect = ['ID', 'PROPERTY_IIKO_ID'];

$resDB = CIBlockElement::GetList(
    $arSort,
    $arFilter,
    $arOrder,
    $arNav,
    $arSelect
);

while ($item = $resDB->Fetch()) {
    echo $item['ID'];
    $info = $arUpdate[$item['PROPERTY_IIKO_ID_VALUE']];

    $temp = [
        [
            'VALUE' => 'Белок',
            'DESCRIPTION' => $info['CHARACTER_PRODUCT_SCORE']['belok'] . ' г'
        ],
        [
            'VALUE' => 'Угеводы',
            'DESCRIPTION' => $info['CHARACTER_PRODUCT_SCORE']['jir'] . ' г'
        ],
        [
            'VALUE' => 'Жиры',
            'DESCRIPTION' => $info['CHARACTER_PRODUCT_SCORE']['yglevod'] . ' г'
        ],
        [
            'VALUE' => 'Калорийность',
            'DESCRIPTION' => $info['CHARACTER_PRODUCT_SCORE']['kkal'] . ' ккал'
        ],
    ];
    CIBlockElement::SetPropertyValuesEx($item['ID'], IBLOCK_CATALOG_MAIN_ID, array('CHARACTER_PRODUCT_SCORE' => $temp));
    CIBlockElement::SetPropertyValuesEx($item['ID'], IBLOCK_CATALOG_MAIN_ID, array('CHARACTER_STORAGE_CONDITIONS' => $info['CHARACTER_STORAGE_CONDITIONS']));
    CIBlockElement::SetPropertyValuesEx($item['ID'], IBLOCK_CATALOG_MAIN_ID, array('CHARACTER_PACKAGE' => $info['CHARACTER_PACKAGE']));
    CIBlockElement::SetPropertyValuesEx($item['ID'], IBLOCK_CATALOG_MAIN_ID, array('CHARACTER_STRUCTURE' => $info['CHARACTER_STRUCTURE']));


    $el = new CIBlockElement;
    $arLoadProductArray = Array(
        "DETAIL_TEXT" => $info['DETAIL_TEXT'],
        "ACTIVE" => 'Y'
    );
    $res = $el->Update($item['ID'], $arLoadProductArray);

    CCatalogProduct::Update($item['ID'], ['WEIGHT' => $info['WEIGHT']]);
}
