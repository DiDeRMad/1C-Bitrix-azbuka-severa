<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

Bitrix\Main\Loader::includeModule("sale");


if($_REQUEST['productId']){

    $quantity = floatval($_REQUEST['quantity']);
    $productId = $_REQUEST['productId'];
    $action = $_REQUEST['action'];
    $productInBasket = null;

    $basket = \Bitrix\Sale\Basket::loadItemsForFUser(
        \Bitrix\Sale\Fuser::getId(),
        \Bitrix\Main\Context::getCurrent()->getSite()
    );

    $basketItems = $basket->getBasketItems();

    if($basketItems) {
        foreach($basketItems as $basketItem) {
            if($basketItem->getField('PRODUCT_ID') == $productId){
                $productInBasket = $basketItem;
                break;
            }
        }
    }

    if($productInBasket) {

        if($action == 'plus'){
            $quantity = $productInBasket->getQuantity() + $quantity;
        }elseif($action == 'minus'){
            $quantity = $productInBasket->getQuantity() - $quantity;
        }

        if($quantity > 0){
            $productInBasket->setField('QUANTITY', $quantity);
        }else{
            $productInBasket->delete();
        }
    }

if ($action === 'remove') {
    $quantity = $productInBasket->getQuantity() - 1;
}

    $r = $basket->save();

    if (!$r->isSuccess()) {
        var_dump($r->getErrorMessages());
    }
}