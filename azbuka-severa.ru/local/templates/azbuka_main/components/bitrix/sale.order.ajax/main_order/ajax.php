<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Sale;

CModule::IncludeModule("sale");

function addCoupon($orderId) //
{
    // получаем объект заказа
    $order = Sale\Order::load($orderId);

// инициализация менеджера купонов для заказа
    Sale\DiscountCouponsManager::init(
        Sale\DiscountCouponsManager::MODE_ORDER, [
            "userId" => $order->getUserId(),
            "orderId" => $order->getId()
        ]
    );

// применение скидки
    Sale\DiscountCouponsManager::add($coupon);

// получение объекта скидок заказа
    $discounts = $order->getDiscount();

// перерасчёт стоимости заказа
    $discounts->calculate();

// сохранение изменений заказа
    $order->save();
}