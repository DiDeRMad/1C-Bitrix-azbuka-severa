<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');

$resDB = CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID],
    false,
    [],
    ['ID', "IBLOCK_ID", 'NAME', 'PROPERTY_CHARACTER_PRODUCT_SCORE']
);

$arResult = [];

while ($item = $resDB->GetNextElement()) {
    $arFields = $item->GetFields();
    $arProps = $item->GetProperties([], ['CODE' => 'CHARACTER_PRODUCT_SCORE']);
    $id = $arFields['ID'];
    $arResult[$arFields['ID']] = [
        'ID' => $arFields['ID'],
        'NAME' => $arFields['NAME'],
        'URL' => "http://dev1.azbuka-severa.dev-v.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=5&type=catalog&lang=ru&ID=$id&find_section_section=-1&WF=Y",
        'BELOK' => $arProps['CHARACTER_PRODUCT_SCORE']['DESCRIPTION'][0],
        'JIR' => $arProps['CHARACTER_PRODUCT_SCORE']['DESCRIPTION'][1],
        'YGLEVOD' => $arProps['CHARACTER_PRODUCT_SCORE']['DESCRIPTION'][2],
        'KKAL' => $arProps['CHARACTER_PRODUCT_SCORE']['DESCRIPTION'][3],
    ];
}
$fp = fopen('characters.csv', 'w');

foreach ($arResult as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);
printer(error_get_last());
