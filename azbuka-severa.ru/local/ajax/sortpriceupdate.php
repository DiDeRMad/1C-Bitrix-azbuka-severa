<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader as Loader;

Loader::IncludeModule('iblock');

$el = new CIBlockElement;

if (!empty($_POST)) {
    $arElems = $_POST['arElems'];
    foreach ($arElems as $id => $elem) {
        $elid = $id;
        $sort = $elem['sort'];
        $price = $elem['price'];
        $count = $elem['count'];

        $fields = [
            'SORT' => $sort
        ];
        $priceFields = [
            "PRODUCT_ID" => $elid,
            "CATALOG_GROUP_ID" => 1,
            "CURRENCY" => "RUB",
            'PRICE' => floatval($price)
        ];
        if ($el->Update($elid, $fields)) {
            $res = CPrice::GetList(array(),array("PRODUCT_ID" =>$elid, "CATALOG_GROUP_ID" => 1));
            if ($arr = $res->Fetch()) {
                CPrice::Update($arr["ID"], $priceFields);
            }
            else {
                CPrice::Add($priceFields);
            }

            $prodFields = [
                'QUANTITY' => $count
            ];
            CCatalogProduct::update($elid, $prodFields);
        }
    }

}