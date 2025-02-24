<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams  = $component->applyTemplateModifications();


if (!empty($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
    if ($arResult['DETAIL_PICTURE'])
        array_unshift($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'], $arResult['DETAIL_PICTURE']['ID']);
} else {
    if ($arResult['DETAIL_PICTURE'])
        $arResult['PROPERTIES']['MORE_PHOTO']['VALUE'][] = $arResult['DETAIL_PICTURE']['ID'];
}

$arResult['SCHEMA_PICTURE'] = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array('width' => 540, 'height' => 540), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];

foreach ($arResult['PROPERTIES']['MORE_PHOTO']['VALUE'] as &$arPhoto) {
    $fileSmall1x                                                    = CFile::ResizeImageGet($arPhoto, array('width' => 131, 'height' => 131), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $fileSmall2x                                                    = CFile::ResizeImageGet($arPhoto, array('width' => 131 * 2, 'height' => 131 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_1X'][]      = $fileSmall1x['src'];
    $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_2X'][]      = $fileSmall2x['src'];
    $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_1X_WEBP'][] = webpExtension($fileSmall1x['src']);
    $arResult['PROPERTIES']['MORE_PHOTO']['SMALL']['SRC_2X_WEBP'][] = webpExtension($fileSmall2x['src']);

    $fileBig1x                                                    = CFile::ResizeImageGet($arPhoto, array('width' => 540, 'height' => 540), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $fileBig2x                                                    = CFile::ResizeImageGet($arPhoto, array('width' => 540 * 2, 'height' => 540 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_1X'][]      = $fileBig1x['src'];
    $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_2X'][]      = $fileBig2x['src'];
    $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_1X_WEBP'][] = webpExtension($fileBig1x['src']);
    $arResult['PROPERTIES']['MORE_PHOTO']['BIG']['SRC_2X_WEBP'][] = webpExtension($fileBig2x['src']);

    $arSchemaImages[] = CFile::GetFileArray($arPhoto);
}
$arResult['SCHEMA_IMAGES'] = $arSchemaImages;
// Добавляем скидку товарам
if ($arResult['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']) {
    $percent                                 = $arResult['PROPERTIES']['DISCOUNT_PERCENT']['VALUE'] / 100;
    $arResult['MIN_PRICE']['DISCOUNT_VALUE'] = $arResult['MIN_PRICE']['VALUE'] - $arResult['MIN_PRICE']['VALUE'] * $percent;
}

if ($arResult['PROPERTIES']['weight']['VALUE']) {
    $arResult['PROPERTIES']['weight']['VALUE'] *= 1000;
    if ($arResult['PROPERTIES']['weight']['VALUE'] % 1000 == 0) {
        $arResult['PROPERTIES']['weight']['VALUE']      = $arResult['PROPERTIES']['weight']['VALUE'] / 1000;
        $arResult['PROPERTIES']['weight']['VALUE_TYPE'] = 'кг';
    } else {
        $arResult['PROPERTIES']['weight']['VALUE_TYPE'] = 'гр.';
    }
}

//массив для использования при формировании заголовка станицы в комплексном компоненте, сразу после вызова текущего компонента
global $arUnit;
$arUnit["weight"] = $arResult['PROPERTIES']['weight']['VALUE'];
$arUnit["type"] = $arResult['PROPERTIES']['weight']['VALUE_TYPE'];

$arCharacters = [];

foreach ($arResult['PROPERTIES'] as $code => $arProp) {
    if (stripos($code, 'CHARACTER_') !== false) {
        if ($code == 'CHARACTER_PRODUCT_SCORE') {
            foreach ($arProp['VALUE'] as $key => $arValue) {
                $arProp['RESULT'][] = [
                    'TEXT'  => $arValue,
                    'VALUE' => $arProp['DESCRIPTION'][$key]
                ];
            }
        }
        if ($code == 'CHARACTER_STRUCTURE') {
            $parts = explode(', ', $arProp['VALUE']);
            if (count($parts) > 1) {
                $arProp['SHORT'] = $parts[0] . ', ' . $parts[1];
                $arProp['LONG']  = $arProp['VALUE'];
            }
        }
        $arCharacters[] = $arProp;
    }
}
$arResult["PROPERTIES"]['ALL_CHARACTERS'] = $arCharacters;

$videoFile1x                                                        = CFile::ResizeImageGet($arResult['PROPERTIES']['VIDEO_PHOTO']['VALUE'], array('width' => 1060, 'height' => 600), BX_RESIZE_IMAGE_EXACT, true);
$videoFile2x                                                        = CFile::ResizeImageGet($arResult['PROPERTIES']['VIDEO_PHOTO']['VALUE'], array('width' => 1060 * 2, 'height' => 600 * 2), BX_RESIZE_IMAGE_EXACT, true);
$arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC']      = $videoFile1x['src'];
$arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['2X']['SRC']      = $videoFile2x['src'];
$arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['1X']['SRC_WEBP'] = webpExtension($videoFile1x['src']);
$arResult['PROPERTIES']['VIDEO_PHOTO']['PICTURE']['2X']['SRC_WEBP'] = webpExtension($videoFile2x['src']);
$arResult['NAME']                                                   = $arResult['PROPERTIES']['NAME_H1']['VALUE'] ?: $arResult['NAME'];
//printer($arCharacters);

// Получение рецепта
if ($arResult['PROPERTIES']['RECEPT']['VALUE']) {
    $resDB = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => IBLOCK_CATALOG_RECEPT_ID, 'ID' => $arResult['PROPERTIES']['RECEPT']['VALUE']],
        false,
        ['nPageSize' => 1],
        ['ID', 'NAME', "IBLOCK_ID", "PROPERTY_TAGS", "DETAIL_TEXT", 'PROPERTY_TIME_TO_COOK', "PREVIEW_PICTURE", 'DETAIL_PAGE_URL']
    );

    while ($recept = $resDB->GetNext()) {
        $arRecept                    = [];
        $arRecept['ID']              = $recept['ID'];
        $arRecept['NAME']            = $recept['NAME'];
        $arRecept['DETAIL_TEXT']     = $recept['DETAIL_TEXT'];
        $arRecept['DETAIL_PAGE_URL'] = $recept['DETAIL_PAGE_URL'];
        $arRecept['TIME_TO_COOK']    = $recept['PROPERTY_TIME_TO_COOK_VALUE'];
        // printer($recept);

        foreach ($recept['PROPERTY_TAGS_VALUE'] as $key => $tag) {
            $arRecept['TAGS'][] = [
                'TEXT' => $tag,
                'LINK' => $recept['PROPERTY_TAGS_DESCRIPTION'][$key]
            ];
        }

        if ($recept['PREVIEW_PICTURE']) {
            $file = CFile::ResizeImageGet($recept['PREVIEW_PICTURE'], array('width' => 901 * 1, 'height' => 400 * 1), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            if ($file['src']) {
                $webp                          = webpExtension($file['src']);
                $arRecept['PICTURE']['SRC_1X'] = $file['src'];
                if ($webp) {
                    $arRecept['PICTURE']['SRC_1X_WEBP'] = $webp;
                }
            }

            $file = CFile::ResizeImageGet($recept['PREVIEW_PICTURE'], array('width' => 901 * 2, 'height' => 400 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            if ($file['src']) {
                $webp                          = webpExtension($file['src']);
                $arRecept['PICTURE']['SRC_2X'] = $file['src'];
                if ($webp) {
                    $arRecept['PICTURE']['SRC_2X_WEBP'] = $webp;
                }
            }
        }
        $arResult['RECEPT'] = $arRecept;
    }
}

// Получение идентичного товара, но с другими весами
$arOffers = [];
if ($arResult['PROPERTIES']['PRODUCT_SIZE']['VALUE']) {
    $resDB = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, 'ACTIVE' => 'Y', 'PROPERTY_PRODUCT_SIZE' => $arResult['PROPERTIES']['PRODUCT_SIZE']['VALUE']],
        false,
        false,
        ['ID', 'DETAIL_PAGE_URL', 'WEIGHT', 'PROPERTY_WEIGHT_OFFER']
    );

    while ($offer = $resDB->GetNext()) {
        $arOffers[] = [
            'ID'     => $offer['ID'],
            'LINK'   => $offer['DETAIL_PAGE_URL'],
            'WEIGHT' => $offer['PROPERTY_WEIGHT_OFFER_VALUE'] ?: 0,
            'ACTIVE' => $offer['ID'] == $arResult['ID'] ? 'active' : ''
        ];
    }
}

$arResult['OFFERS'] = $arOffers;

$arResult['TITLE_OFFER'] = $arResult['PROPERTIES']['TITLE_OFFER']['VALUE'] ?: 'Вес тушки (кг)';

$cp = $this->__component; // объект компонента

if (is_object($cp)) {
    $cp->arResult['DESCRIPTION'] = $arResult['PREVIEW_TEXT'] ?: $arResult['DETAIL_TEXT'];
    $cp->arResult['PICTURE']     = $arResult['SCHEMA_PICTURE'];
    $cp->arResult['DETAIL_URL']  = $arResult['DETAIL_PAGE_URL'];
    $cp->SetResultCacheKeys(array('DESCRIPTION', 'PICTURE', 'DETAIL_URL'));
}

// $arResult["GLOBAL_COPY"] = $GLOBALS['productCounts'];
// $arResult["GLOBAL_COPY2"] = $arResult['ID'];

if (false && $GLOBALS['thisDomain'] != 'main') {
    if (isset($GLOBALS['productCounts']) && !empty($GLOBALS['productCounts'])) {
        if (array_key_exists($arResult['ID'], $GLOBALS['productCounts'])) {
            $arResult['PRODUCT']['QUANTITY'] = $GLOBALS['productCounts'][$arResult['ID']]['AMOUNT'];
        } else {
            $arResult['PRODUCT']['QUANTITY'] = '0';
            $arResult['CAN_BUY']             = 'N';
        }
    }
}
else {
    $arResult['PRODUCT']['QUANTITY'] = $arResult["CATALOG_QUANTITY"];
}


// SEO текст для страницы
$arResult["SEO_TEXT"] = [];
$curPage = $APPLICATION->GetCurPage();
$res = CIBlockElement::GetList(
    ["SORT" => "ASC"],
    [
        "IBLOCK_ID" => IBLOCK_SEO_TEXT,
        "ACTIVE" => "Y",
        "PROPERTY_PAGE" => $curPage
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "DETAIL_TEXT"]
);
while($arFields = $res->GetNext()) {
    $arResult["SEO_TEXT"][] = $arFields["~DETAIL_TEXT"];
}
