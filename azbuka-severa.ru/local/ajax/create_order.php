<?php
require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

function writeLog($data, $logFile = '/order_debug.log') {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $logFile;
    file_put_contents($filePath, print_r($data, true), FILE_APPEND | LOCK_EX);
}

use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;

global $USER, $DB;

Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

$request = Context::getCurrent()->getRequest();
writeLog('--- REQUEST DATA ---');
writeLog($_REQUEST);
$siteId = Context::getCurrent()->getSite();
$currencyCode = CurrencyManager::getBaseCurrency();

//Mock
/*$_REQUEST['COMMENT'] = 'asdf';
$_REQUEST['FIRST_NAME'] = 'Artem';
$_REQUEST['LAST_NAME'] = 'Valevich';
$_REQUEST['PHONE'] = '123123';
$_REQUEST['DELIVERY_ID'] = 5;
$_REQUEST['PAYMENT_ID'] = 1;
$_REQUEST['ORDER_ID_CITY'] = 'Москва';
$_REQUEST['ORDER_ID_STREET'] = 'Левобережная';
$_REQUEST['ORDER_ID_HOME'] = '4к7';
$_REQUEST['ORDER_ID_APARTMENT'] = '460';
$_REQUEST['ORDER_ID_FLOOR'] = '18';*/
$arErrors = [];
$result = [];

if (!isset($_REQUEST['DELIVERY_ID']) && !$_REQUEST['DELIVERY_ID']) {
    $arErrors[] = 'Не выбрана служба доставки';
}

if (!isset($_REQUEST['PAYMENT_ID']) && !$_REQUEST['PAYMENT_ID']) {
    $arErrors[] = 'Не выбран способ оплаты';
}

if (!isset($_REQUEST['PHONE']) ||
    strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['PHONE']))) < 5) {
    $arErrors[] = 'Не заполнено поле Телефон';
}

if (!isset($_REQUEST['FIRST_NAME']) ||
    strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['FIRST_NAME']))) < 1) {
    $arErrors[] = 'Не заполнено поле Имя';
}
if (!isset($_REQUEST['ORDER_ID_EMAIL']) ||
    strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['ORDER_ID_EMAIL']))) < 1) {
    $arErrors[] = 'Не заполнено поле Email';
}

if (!empty($_REQUEST['ORDER_ID_EMAIL'])) {
    if (!filter_var($_REQUEST['ORDER_ID_EMAIL'], FILTER_VALIDATE_EMAIL)) {
        $arErrors[] = "E-mail адрес" . " " . $_REQUEST['ORDER_ID_EMAIL'] . " " . "указан неверно.\n";
    } else {
        if ($_REQUEST["SUBSCRIBE"]) {
            $aParams = array(
                'SEND_CONFIRM' => 'Y',
                'EMAIL' => $_REQUEST["ORDER_ID_EMAIL"],
                'SUBSCRIBE_LIST' => array(1)
            );
            \Bitrix\Sender\Subscription::subscribe($aParams);
            $contact = \Bitrix\Sender\ContactTable::getRow([
                'filter' => array(
                    '=CODE' => $_REQUEST['ORDER_ID_EMAIL'])
            ]);
            if ($contact) {
                \Bitrix\Sender\ContactTable::update($contact['ID'], [
                    'NAME' => $_REQUEST['FIRST_NAME'],
                    'SEND_CONFIRM' => 'Y',
                ]);
            }
        }
    }
}
if (!empty($_REQUEST['PHONE']) && strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['PHONE']))) >= 1) {
    $phone_valid = $_REQUEST['PHONE'];
    if (!is_valid_russian_phone_number($phone_valid)) {
        $arErrors[] = "Телефон" . " " . $_REQUEST['PHONE'] . " " . "указан неверно";
    }
}

if ($_REQUEST['DELIVERY_ID'] == 6 || $_REQUEST['DELIVERY_ID'] == 2 || $_REQUEST['DELIVERY_ID'] == 5 || $_REQUEST['DELIVERY_ID'] == 8) {
    if (!isset($_REQUEST['ORDER_ID_CITY']) ||
        strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['ORDER_ID_CITY']))) < 1) {
        $arErrors[] = 'Не заполнено поле Город';
    }
    if (!isset($_REQUEST['ORDER_ID_STREET']) ||
        strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['ORDER_ID_STREET']))) < 1) {
        $arErrors[] = 'Не заполнено поле Адрес доставки';
    }
    if (!isset($_REQUEST['ORDER_ID_HOME']) ||
        strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['ORDER_ID_HOME']))) < 1) {
        $arErrors[] = 'Не заполнено поле Дом, строение, корпус';
    }
    if (!isset($_REQUEST['ORDER_ID_APARTMENT']) ||
        strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['ORDER_ID_APARTMENT']))) < 1) {
        $arErrors[] = 'Не заполнено поле Этаж';
    }
    if (!isset($_REQUEST['ORDER_ID_FLOOR']) ||
        strlen(preg_replace("/\s*/mi", "", strval($_REQUEST['ORDER_ID_FLOOR']))) < 1) {
        $arErrors[] = 'Не заполнено поле Квартира';
    }
}

function is_valid_russian_phone_number($phone_valid)
{
    // Удаляем все не символы кроме цифр
    $phone = preg_replace('/\D/', '', $phone_valid);

    // Номер должен начинается на цифру 7
    if (substr($phone, 0, 1) !== '7') {
        return false;
    }

    // Длиной 11 символов
    if (strlen($phone) !== 11) {
        return false;
    }

    return true;
}

if (!empty($arErrors)) {
    $result['status'] = 'error';
    $result['errors'] = $arErrors;
    $result['data'] = $_REQUEST;
	writeLog('--- VALIDATION ERRORS ---');
    writeLog($arErrors);
    echo json_encode($result);
    return false;
}


function getPropertyByCode($propertyCollection, $code)
{
    foreach ($propertyCollection as $property) {
        if ($property->getField('CODE') == $code)
            return $property;
    }
}

if (!$USER->IsAuthorized()) {
    $sql = "SELECT USER_ID FROM b_user_phone_auth WHERE PHONE_NUMBER = '" . mysqli_real_escape_string($DB->db_Conn, $_REQUEST['PHONE']) . "'";

    if ($dbRes = $DB->Query($sql)) {
        $userId = $dbRes->GetNext()['USER_ID'];
    } else {
        $userId = false;
    }
} else {
    $userId = $USER->GetID();
}

if (!$userId) {
    $arFields = [
        'NAME' => $_REQUEST['FIRST_NAME'],
        'LAST_NAME' => $_REQUEST['LAST_NAME'],
        'EMAIL' => $_REQUEST['EMAIL'],
        'PHONE_NUMBER' => $_REQUEST['PHONE'],
        'PASSWORD' => '123Pass321Word',
        'CONFIRM_PASSWORD' => '123Pass321Word',
        'ACTIVE' => 'Y',
        'LOGIN' => trim($_REQUEST['PHONE'])
    ];

    $userId = $USER->Add($arFields);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/useradd.log", print_r($USER->LAST_ERROR, true), FILE_APPEND);
}

writeLog('--- USER CREATION ---');
writeLog(['USER_ID' => $userId, 'LAST_ERROR' => $USER->LAST_ERROR]);

// Создаёт новый заказ
$order = Order::create($siteId, $userId ?: 459);
$order->setPersonTypeId(1);
$order->setField('CURRENCY', $currencyCode);
if ($_REQUEST['COMMENT']) {
    $order->setField('USER_DESCRIPTION', $_REQUEST['COMMENT']); // Устанавливаем поля комментария покупателя
}

// Создаём корзину с одним товаром
$basketUser = Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
$basketItems = $basketUser->getBasketItems(); // массив объектов Sale\BasketItem
$basket = Basket::create($siteId);
//printer($basketItems);

foreach ($basketItems as $basketItem) {
    $item = $basket->createItem('catalog', $basketItem->getProductId());
    $item->setFields(array(
        'QUANTITY' => $basketItem->getQuantity(),
        'CURRENCY' => $currencyCode,
        'LID' => $siteId,
        'PRICE' => $basketItem->getPrice(),
        'CUSTOM_PRICE' => 'Y',
        'NAME' => $basketItem->getField('NAME'),

    ));
}
$order->setBasket($basket);

writeLog('--- BASKET DATA ---');
writeLog($basketItems);


// Создаём одну отгрузку и устанавливаем способ доставки - "Без доставки" (он служебный)
$shipmentCollection = $order->getShipmentCollection();
$shipment = $shipmentCollection->createItem();
$service = Delivery\Services\Manager::getById($_REQUEST['DELIVERY_ID']);

$shipment->setFields(array(
    'DELIVERY_ID' => $service['ID'],
    'DELIVERY_NAME' => $service['NAME'],
));
$shipmentItemCollection = $shipment->getShipmentItemCollection();
$shipmentItem = $shipmentItemCollection->createItem($item);
$shipmentItem->setQuantity($item->getQuantity());


// Создаём оплату со способом #1
$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->createItem();
$paySystemService = PaySystem\Manager::getObjectById($_REQUEST['PAYMENT_ID']);
$payment->setFields(array(
    'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
    'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
));

// Устанавливаем свойства
$propertyCollection = $order->getPropertyCollection();
$phoneProp = $propertyCollection->getPhone();
$phoneProp->setValue($_REQUEST['PHONE']);
$nameProp = $propertyCollection->getPayerName();
$nameProp->setValue($_REQUEST['FIRST_NAME'] . ' ' . $_REQUEST['LAST_NAME']);

foreach ($_REQUEST as $key => $req) {
    if (stripos($key, 'ORDER_ID') !== false) {
        $propObj = getPropertyByCode($propertyCollection, str_replace("ORDER_ID_", "", $key));
        if ($propObj)
            $propObj->setValue($req);
    }
}

// Сохраняем
$order->doFinalAction(true);
$result = $order->save();
$orderId = $order->getId();

if ($result->isSuccess()) {
    CSaleBasket::DeleteAll(\Bitrix\Sale\Fuser::getId());
    $resultOrder['status'] = 'success';
    $data['emails'][] = htmlspecialcharsbx($_POST['ORDER_ID_EMAIL']);

    \Dev\SendPulse\AddressBookController::addEmail($data);
    echo json_encode($resultOrder);
} else {
    echo json_encode(['status' => 'error']);
}

writeLog('--- ORDER SAVE ---');
writeLog([
    'ORDER_FIELDS' => $order->getFields(),
    'PROPERTIES' => $propertyCollection->getArray()
]);

writeLog('--- ORDER SAVE RESULT ---');
writeLog($result->getErrors());
