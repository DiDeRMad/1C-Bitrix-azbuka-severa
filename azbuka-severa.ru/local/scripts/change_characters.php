<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$resDB = CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, 'PROPERTY_CHARACTER_PRODUCT_SCORE' => false],
    false,
    false,
    ['ID', 'IBLOCK_ID', 'PROPERTY_CHARACTER_PRODUCT_SCORE']
);

while ($item = $resDB->GetNextElement()) {
    $arFields = $item->GetFields();
    $arProps = $item->GetProperties([], ['CODE' => 'CHARACTER_PRODUCT_SCORE']);
    echo $arFields['ID'] . "<br>";

    $arNewCharacters = [];
    foreach ($arProps['CHARACTER_PRODUCT_SCORE']['VALUE'] as $key => $character) {
        if ($character == 'Угеводы')
            $character = 'Углеводы';
        if ($character == 'Белок')
            $character = 'Белки';

        $arNewCharacters[$character] = $arProps['CHARACTER_PRODUCT_SCORE']['DESCRIPTION'][$key];
    }

    $arTmp = [];
    $text = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя abcdefghijklmnopqrstuvwxyz 0123456789 .,!?';
    $arTmp['Белки'] =  mb_eregi_replace('[^0-9 ]', '', $arNewCharacters['Белки']) . ' гр.';
    $arTmp['Жиры'] = mb_eregi_replace('[^0-9 ]', '', $arNewCharacters['Жиры']) . ' гр.';
    $arTmp['Углеводы'] = mb_eregi_replace('[^0-9 ]', '', $arNewCharacters['Углеводы']) . ' гр.';
    $arTmp['Калорийность'] = $arNewCharacters['Калорийность'];
    $arUpdate = [];

    foreach ($arTmp as $value => $description) {
        $arUpdate[] = [
            'VALUE' => '',
            'DESCRIPTION' => ''
        ];
    }

    //CIBlockElement::SetPropertyValuesEx($arFields['ID'], IBLOCK_CATALOG_MAIN_ID, array('CHARACTER_PRODUCT_SCORE' => $arUpdate));

   // printer($arProps['CHARACTER_PRODUCT_SCORE']);
//    printer($arNewCharacters);
   // printer($arTmp);
    printer($arUpdate);
}