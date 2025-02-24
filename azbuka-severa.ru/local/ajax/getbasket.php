<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");
global $USER;
use Bitrix\Sale;
$basketIds = [];

$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
$basketItems = [];

foreach ($basket as $basketItem) {
    $basketIds[] = $basketItem->getField('PRODUCT_ID');
    $basketItems[$basketItem->getField('PRODUCT_ID')] =  $basketItem->getQuantity();
}

echo json_encode($basketItems);