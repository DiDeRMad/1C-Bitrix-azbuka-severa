<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;
use Bitrix\Main,
    Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Loader as Loader,
    Bitrix\Main\Config\Option as Option,
    Bitrix\Sale\Delivery as Delivery,
    Bitrix\Sale\PaySystem as PaySystem,
    Bitrix\Sale\Basket as Basket,
    Bitrix\Sale as Sale,
    Bitrix\Sale\Order as Order,
    Bitrix\Sale\DiscountCouponsManager as DiscountCouponsManager,
    Bitrix\Main\Context as Context;

Loader::IncludeModule("sale");
Loader::IncludeModule("catalog");

$user = 5534;
$rsUser = CUser::GetByID($user);
$arUser = $rsUser->Fetch();
echo "<pre>",print_r($arUser, true),"</pre>";

if (empty($arUser["NAME"]) || empty($arUser["PERSONAL_PHONE"]) || empty($arUser["EMAIL"])) {
    $firstOrder = \Bitrix\Sale\Order::getList([
        'filter' => [
            "USER_ID" => $user
        ],
        'order' => ['ID' => 'ASC']
    ])->fetch();

    if ($firstOrder) {
        $firstOrder = Sale\Order::load($firstOrder["ID"]);
        $propertyCollection = $firstOrder->getPropertyCollection();

        $data["EMAIL"] = $propertyCollection->getUserEmail()->getValue();
        $userName = explode(" ", $propertyCollection->getPayerName()->getValue());
        $data["PERSONAL_PHONE"] = $propertyCollection->getPhone()->getValue();

        if (count($userName) !== 2) {
            $data["NAME"] = $userName[0];
        } else {
            list($data["NAME"], $data["LAST_NAME"]) = $userName;
        }

        $updateUser = new CUser;
        $updateUser->Update($user, $data);



    }

    echo "<pre>",print_r($data, true),"</pre>";
}
