<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');
$element = CIBlockElement::GetList(
    array(),
    array('IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID),
    false,
    false,
    array('ID', 'NAME', 'PROPERTY_DISCOUNT_PERCENT')
);
$i = 0;


while ($ar_fields = $element->Fetch()) {

    if (!ctype_digit($ar_fields['PROPERTY_DISCOUNT_PERCENT_VALUE'])) {
        continue;
    }
    if (!empty($ar_fields['PROPERTY_DISCOUNT_PERCENT_VALUE']) && ($ar_fields['PROPERTY_DISCOUNT_PERCENT_VALUE'] > 0 && $ar_fields['PROPERTY_DISCOUNT_PERCENT_VALUE'] < 100)) {
        echo $ar_fields['ID'] . '<br>';
        $DISCOUNT_VALUE = trim($ar_fields['PROPERTY_DISCOUNT_PERCENT_VALUE']); //процент скидки
        addDiscount($ar_fields,$DISCOUNT_VALUE);
    }

//printer($ar_fields);
//    $i++;
//    if ($i > 3) {
//        break;
//    }


}
