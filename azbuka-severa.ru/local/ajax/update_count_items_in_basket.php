<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

use Bitrix\Sale;

$quantity = $_POST['quantity'];

if ($_POST['action'] == 'minus') {
    $quantity-=1;
    if ($quantity == 0) {
        echo 'empty';
    } else {
        echo num_word($quantity, ['товар', 'товара', 'товаров']);
    }
} else {
    $quantity++;
    echo num_word($quantity, ['товар', 'товара', 'товаров']);
}