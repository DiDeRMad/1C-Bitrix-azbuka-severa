<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Sale;
Bitrix\Main\Loader::includeModule("sale");

$basket = Sale\Basket::loadItemsForFUser(
    Sale\Fuser::getId(),
    \Bitrix\Main\Context::getCurrent()->getSite()
);

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] === 'cartState') {
    $result = [];
    foreach ($basket as $basketItem) {
        $result[$basketItem->getProductId()] = $basketItem->getQuantity();
    }

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else {
    $price = $basket->getPrice();
    header('Content-Type: text/plain');
    echo $price;
    exit;
}
