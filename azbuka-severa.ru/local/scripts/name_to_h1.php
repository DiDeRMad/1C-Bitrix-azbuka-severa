<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$resDB = CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID],
    false,
    false,
    ['ID', 'IBLOCK_ID', 'NAME']
);

while ($item = $resDB->Fetch()) {
    CIBlockElement::SetPropertyValuesEx($item['ID'], $item['IBLOCK_ID'], ['NAME_H1' => $item['NAME']]);
}