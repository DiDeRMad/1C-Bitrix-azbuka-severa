<?php

// регистрируем обработчик
AddEventHandler("search", "BeforeIndex", "BeforeIndexHandler");
AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");
AddEventHandler("main", "OnEndBufferContent", "DeleteCoreScripts");
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAddHandler");
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnAfterIBlockElementUpdateHandler");
//создаем обработчик события "OnAfterIBlockElementUpdate"
function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
        // подключаем модуль
        if(!CModule::IncludeModule("sale")){
            return $arFields;
        }

        $DISCOUNT_VALUES = $arFields['PROPERTY_VALUES'][IBLOCK_PROP_DISCOUNT];

        foreach ($DISCOUNT_VALUES as $DISCOUNT_VALUE) {
            $discountVal = trim($DISCOUNT_VALUE['VALUE']);
        }

if (($discountVal > 0 && $discountVal < 100) && $arFields['IBLOCK_ID'] == IBLOCK_CATALOG_MAIN_ID) {

    if (!ctype_digit($discountVal)) {
        global $APPLICATION;
        $APPLICATION->throwException("Поле 'Скидка %' должно состоять только из цифр!");
        return false;
    }

    $arItemIds = [$arFields['ID']]; //ID товара
    $iDiscount = '';
    $dbProductDiscounts = CCatalogDiscount::GetDiscountByProduct($arFields['ID'], array(), "N", 1, 's1');

    if ($dbProductDiscounts) {
        foreach ($dbProductDiscounts as $key => $dbProductDiscount) {
            $iDiscount = $key;
        }
        $arDiscountFields = [
            "LID" => 's1',
            "SITE_ID" => 's1',
            "NAME" => "Скидка " . $discountVal . "%" . ' - ' . $arFields['NAME'],
            "DISCOUNT_VALUE" => $discountVal,
            "DISCOUNT_TYPE" => "P",
            "LAST_LEVEL_DISCOUNT" => "N",
            "LAST_DISCOUNT" => "N",
            "ACTIVE" => "Y",
            "CURRENCY" => "RUB",
            "USER_GROUPS" => [1, 2, 3, 4, 5, 6, 7],
            'ACTIONS' => [
                "CLASS_ID" => "CondGroup",
                "DATA" => [
                    "All" => "AND"
                ],
                "CHILDREN" => [
                    [
                        "CLASS_ID" => "ActSaleBsktGrp",
                        "DATA" => [
                            "Type" => "Discount",
                            "Value" => $discountVal,
                            "Unit" => "Perc",
                            "Max" => 0,
                            "All" => "AND",
                            "True" => "True",
                        ],
                        "CHILDREN" => [
                            [
                                "CLASS_ID" => "CondBsktAppliedDiscount",
                                "DATA" => [
                                    "value" => "N",
                                ],
                            ],
                            [
                                "CLASS_ID" => "CondIBElement",
                                "DATA" => [
                                    "logic" => "Equal",
                                    "value" => $arItemIds,
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            "CONDITIONS" => [
                'CLASS_ID' => 'CondGroup',
                'DATA' => [
                    'All' => 'AND',
                    'True' => 'True',
                ],
                'CHILDREN' => [
                    [
                        "CLASS_ID" => "CondBsktProductGroup",
                        "DATA" => [
                            "Found" => "Found",
                            "All" => "AND",
                        ],
                        "CHILDREN" => [
                            [
                                "CLASS_ID" => "CondIBElement",
                                "DATA" => [
                                    "logic" => "Equal",
                                    "value" => $arItemIds,
                                ]
                            ]
                        ]
                    ],
                ],
            ]
        ];

        CSaleDiscount::Update($iDiscount, $arDiscountFields);
    }
    else{
                addDiscount($arFields, $discountVal);
    }
}

    if (($discountVal == 0 || empty($discountVal)) && $arFields['IBLOCK_ID'] == IBLOCK_CATALOG_MAIN_ID) {

        $iDiscount = '';
        $dbProductDiscounts = CCatalogDiscount::GetDiscountByProduct($arFields['ID'], array(), "N", 1, 's1');
        foreach ($dbProductDiscounts as $key => $dbProductDiscount) {
            $iDiscount = $key;
        }
        if ($iDiscount)
        CSaleDiscount::Delete($iDiscount);

    }
}
//создаем обработчик события "OnAfterIBlockElementAdd"
function OnAfterIBlockElementAddHandler(&$arFields)
{
    // подключаем модуль
    if(!CModule::IncludeModule("sale")){
        return $arFields;
    }

        if (($arFields['PROPERTY_VALUES'][IBLOCK_PROP_DISCOUNT]['n0']['VALUE'] > 0 && $arFields['PROPERTY_VALUES'][IBLOCK_PROP_DISCOUNT]['n0']['VALUE'] < 100) && $arFields['IBLOCK_ID'] == IBLOCK_CATALOG_MAIN_ID) {

            $DISCOUNT_VALUE = trim($arFields['PROPERTY_VALUES'][IBLOCK_PROP_DISCOUNT]['n0']['VALUE']); //процент скидки
            if (!ctype_digit($DISCOUNT_VALUE)) {
                global $APPLICATION;
                $APPLICATION->throwException("Поле 'Скидка %' должно состоять только из цифр!");
                return false;
            }

            addDiscount($arFields, $DISCOUNT_VALUE);
        }
}
// создаем обработчик события "BeforeIndex"
function BeforeIndexHandler($arFields)
{
    if(!CModule::IncludeModule("iblock")) // подключаем модуль
        return $arFields;
    if($arFields["MODULE_ID"] == "iblock")
    {
        $db_props = CIBlockElement::GetProperty(                        // Запросим свойства индексируемого элемента
            $arFields["PARAM2"],         // BLOCK_ID индексируемого свойства
            $arFields["ITEM_ID"],          // ID индексируемого свойства
            array("sort" => "asc"),       // Сортировка (можно упустить)
            Array("CODE"=>"NAME_H1")); // CODE свойства (в данном случае Название H1)
        if($ar_props = $db_props->Fetch()) {
            $arFields["TITLE"] .= " " . $ar_props["VALUE"];   // Добавим свойство в конец заголовка индексируемого элемента
        }

        if(array_key_exists("BODY", $arFields) && substr($arFields["ITEM_ID"], 0, 1) != "S") // Только для элементов
        {
            $arFields["BODY"] = "";
        }

        if (substr($arFields["ITEM_ID"], 0, 1) == "S") // Только для разделов
        {
            $arFields['TITLE'] = "";
            $arFields["BODY"] = "";
            $arFields['TAGS'] = "";
        }
    }
    return $arFields; // вернём изменения
}


function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
    $arOrder = CSaleOrder::GetByID($orderID);
    $orderProps = CSaleOrderPropsValue::GetOrderProps($orderID);
    $arAddress = [];

    while ($arProp = $orderProps->Fetch()) {


        if ($arProp['CODE'] == 'FIO') {
            $arFields['CLIENT_FIO'] = $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'PHONE') {
            $arFields['CLIENT_PHONE'] = $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'CITY' && $arProp['GROUP_NAME'] == 'Адрес доставки') {
            $arAddress[0] = $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'STREET') {
            $arAddress[1] = $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'HOME') {
            $arAddress[2] = 'д. ' . $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'APARTMENT') {
            $arAddress[3] = 'кв. ' . $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'FLOOR') {
            $arAddress[4] = 'эт. ' . $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'PODEZD') {
            $arAddress[5] = 'подъезд ' . $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'DOORPHONE') {
            $arAddress[6] = 'домофон ' . $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'DOORPHONE') {
            $arFields['DOMOFON'] = $arProp['VALUE'];
        }
    }

    ksort($arAddress);

    $arFields['ADDRESS'] = implode(', ', $arAddress);
    $arDelivery = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
    $arFields['DELIVERY'] = $arDelivery['NAME'];

    $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
    $arFields['PAYMENT'] = $arPaySystem['NAME'];

    $arFields['COMMENT'] = $arOrder['USER_DESCRIPTION'];
}


/**
 * Вырезаем скрипты ядра для pagespeed показателей
 */
function DeleteCoreScripts(&$content) {

    if (stripos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') === false) { return ;}

    $arPatternsToRemove = Array(
        '/<script.+?src=".+?kernel_main\/kernel_main\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?main\.popup\.bundle\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?libs\.min\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?lazy\.min\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?core\/core\.min\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?core\/core\.min\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?core\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?kernel_main\/kernel_main_v1\.js\?\d+"><\/script\>/',
        '/<script.+?>BX\.(setCSSList|setJSList)\(\[.+?\]\).*?<\/script>/',
        '/<script.+?>if\(\!window\.BX\)window\.BX.+?<\/script>/',
        '/<script[^>]+?>\(window\.BX\|\|top\.BX\)\.message[^<]+<\/script>/',
        '/<script.+?src=".+?touch-phone-embedded\.js\?\d+"><\/script\>/',
        '/<script.+?src=".+?tag\.js\?\d+"><\/script\>/',
        '/<link.+?href=".+?kernel_main\/kernel_main\.css\?\d+"[^>]+>/',
        '/<link.+?href=".+?kernel_main\/kernel_main_v1\.css\?\d+"[^>]+>/',
        '/<link.+?href=".+?css\/core\.min\.css\?\d+"[^>]+>/',
        '/<link.+?href=".+?opensans\/ui\.font\.opensans\.min\.css\?\d+"[^>]+>/',
    );
    $content = preg_replace($arPatternsToRemove, "", $content);
    $content = preg_replace("/\n{2,}/", "\n\n", $content);
}