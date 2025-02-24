<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$resDB = CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID],
    false,
    false,
    ['ID']
);

while ($item = $resDB->Fetch()) {
    CCatalogProduct::Update($item['ID'], ['QUANTITY' => 1000]);
}