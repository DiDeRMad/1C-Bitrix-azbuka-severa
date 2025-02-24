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

if (Loader::IncludeModule("sale") && Loader::IncludeModule("catalog") && isset($_POST['id'])) {
    $order_id = $_POST['id'];
    $siteId = Context::getCurrent()->getSite();
    $order = Order::loadByAccountNumber($order_id);
    $currencyCode = Option::get('sale', 'default_currency', 'RUB');
    $basket = $order->getBasket();
    $fields = $order->getFields();
    $propertyCollection = $order->getPropertyCollection();
    $paymentCollection = $order->getPaymentCollection();
    foreach ($paymentCollection as $payment) {
        $psID = $payment->getPaymentSystemId();
        $psName = $payment->getPaymentSystemName();
    }
    $shipmentCollection = $order->getShipmentCollection();
    foreach ($shipmentCollection as $shipment) {
        if($shipment->isSystem())
            continue;
        $shName = $shipment->getField('DELIVERY_NAME');
        $shId = $shipment->getField('DELIVERY_ID');
    }
    $orderNew = Order::create($siteId, $USER->GetID());



    $orderNew->setPersonTypeId(1);
    $basketNew = Basket::create($siteId);

    foreach ($basket as $key=>$basketItem){
        $item=$basketNew->createItem('catalog',$basketItem->getProductId());
        $item->setFields(array('QUANTITY'=>$basketItem->getQuantity(),'CURRENCY'=>$currencyCode,'LID'=>$siteId,'PRODUCT_PROVIDER_CLASS'=>'\CCatalogProductProvider',));
    }
    $orderNew->setBasket($basketNew);
    $shipmentCollectionNew = $orderNew->getShipmentCollection();
    $shipmentNew = $shipmentCollectionNew->createItem();
    $shipmentNew->setFields(array(
        'DELIVERY_ID' => $shId,
        'DELIVERY_NAME' => $shName,
        'CURRENCY' => $order->getCurrency()
    ));
    $shipmentCollectionNew->calculateDelivery();
     $paymentCollectionNew = $orderNew->getPaymentCollection();
     $PaymentNew = $paymentCollectionNew->createItem();
     $PaymentNew->setFields(array(
         'PAY_SYSTEM_ID' => $psID,
         'PAY_SYSTEM_NAME' => $psName
     ));


    if (empty($USER->GetFullName()) || empty($USER->GetParam("PERSONAL_PHONE")) || empty($USER->GetEmail())) {
        $firstOrder = \Bitrix\Sale\Order::getList([
            'filter' => [
                "USER_ID" => $USER->GetID()
            ],
            'order' => ['ID' => 'ASC']
        ])->fetch();

        if ($firstOrder) {
            $firstOrder = Sale\Order::load($firstOrder["ID"]);
            $ptyCollection = $firstOrder->getPropertyCollection();

			if (empty($USER->GetEmail())) {
            	$data["EMAIL"] = $ptyCollection->getUserEmail()->getValue();
			} else {
				$data["EMAIL"] = $USER->GetEmail();
			}

			if (empty($USER->GetFullName())) {
            	$userName = explode(" ", $ptyCollection->getPayerName()->getValue());

				if (count($userName) !== 2) {
					$data["NAME"] = $userName[0];
				} else {
					list($data["NAME"], $data["LAST_NAME"]) = $userName;
				}
			} else {
				$userName = explode(" ", $USER->GetFullName());
			}

			if (empty($USER->GetParam("PERSONAL_PHONE"))) {
            	$data["PERSONAL_PHONE"] = $ptyCollection->getPhone()->getValue();
			} else {
				$data["PERSONAL_PHONE"] = $USER->GetParam("PERSONAL_PHONE");
			}


            $updateUser = new CUser;
            $updateUser->Update($USER->GetID(), $data);


            $propertyCollection = $orderNew->getPropertyCollection();

            if (empty($propertyCollection->getUserEmail()->getValue()) || empty($propertyCollection->getPayerName()->getValue()) || empty($propertyCollection->getPhone()->getValue())) {
                $propertyCollection->getUserEmail()->setValue($data["EMAIL"]);
                $propertyCollection->getPayerName()->setValue(implode(" ", $userName));
                $propertyCollection->getPhone()->setValue($data["PERSONAL_PHONE"]);
            }
        }
    }


     $orderNew->setField('CURRENCY', $currencyCode);
    $orderNew->doFinalAction(true);
    $r = $orderNew->save();
    if (!$r->isSuccess()) {
        var_dump($r->getErrorMessages());
    } else {
       echo $order_id;
    }
}?>