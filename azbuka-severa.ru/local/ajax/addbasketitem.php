<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

Bitrix\Main\Loader::includeModule("catalog");

$fields = [
    'PRODUCT_ID' => $_POST['productId'],
    'QUANTITY' => $_POST['quantity'],
];

$r = Bitrix\Catalog\Product\Basket::addProduct($fields);

if ($r->isSuccess()) {
    header('Content-Type: application/json');
    echo json_encode(['result' => true]);
} else {
    header('Content-Type: application/json', true, 400);
    echo json_encode(['result' => false, 'errors' => $r->getErrorMessages()]);
}
