<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('iblock');

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://iiko.biz:9900/api/0/nomenclature/6bffc1fe-bf80-11ea-aa5c-0025906bfe47?revision=0&access_token=oo2iDzB946ZzyFu8enOtNfdFDbk6ZPS8BpS6z8Pcohxw5ctOi26vEI67Lw7cNrpW_DTmH4gi0EIuy4WUGt0x6g2',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
$response = json_decode($response, true)['products'];

curl_close($curl);

$arUuidList = [];

foreach ($response as $item) {
    $arUuidList[$item['id']] = $item['code'];
}

$arSort = [];
$arFilter = ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, 'XML_ID' => array_keys($arUuidList)];
$arOrder = false;
$arNav = false;
$arSelect = ['ID', 'XML_ID'];

$resDB = CIBlockElement::GetList(
    $arSort,
    $arFilter,
    $arOrder,
    $arNav,
    $arSelect
);

while ($item = $resDB->Fetch()) {
    CIBlockElement::SetPropertyValuesEx($item['ID'], IBLOCK_CATALOG_MAIN_ID, array('IIKO_ID' => $arUuidList[$item['XML_ID']]));
}

