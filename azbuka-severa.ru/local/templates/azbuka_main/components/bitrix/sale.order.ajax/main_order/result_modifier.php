<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);

$db_dtype = \Bitrix\Sale\Delivery\Services\Table::getList(array(
    'filter' => array('ACTIVE'=>'Y'),
));
//    CSaleDelivery::GetList(
//    array(
//        "SORT" => "ASC",
//        "NAME" => "ASC"
//    ),
//    array(
//        "LID" => SITE_ID,
//    ),
//    false,
//    false,
//    array()
//);
if ($ar_dtype = $db_dtype->Fetch())
{
    do
    {
        $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE'] = $ar_dtype['CONFIG']['MAIN']['PRICE'];
        $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE_FORMATED'] = CurrencyFormat($ar_dtype['CONFIG']['MAIN']['PRICE'], 'RUB');

        if ($ar_dtype['ID'] == 2) {
            $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE_FROM'] = '';
            if ($arResult['ORDER_PRICE'] > 3500){
                $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE'] = '0';
                $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE_FORMATED'] = CurrencyFormat(0, 'RUB');;
            }
            if ($arResult['ORDER_PRICE'] < 3500){
                $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE'] = '400';
                $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE_FORMATED'] = CurrencyFormat(400, 'RUB');;
            }
        } else {
            $arResult['DELIVERY'][$ar_dtype['ID']]['PRICE_FROM'] = 'от ';
        }
    }
    while ($ar_dtype = $db_dtype->Fetch());
}

$arResult['ONLY_MOSCOW'] = [2, 3, 5, 6, 7];
$arResult['NO_MOSCOW'] = [8];

foreach ($arResult['DELIVERY'] as &$arDelivery) {
    $arDelivery['DESCRIPTION'] = strip_tags($arDelivery['DESCRIPTION']);
}
