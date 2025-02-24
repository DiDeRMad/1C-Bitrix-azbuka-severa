<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arResult['ELEMENTS_COUNT'] = CIBlockSection::GetSectionElementsCount($arResult['ID'], ['CNT_ACTIVE' => 'Y']);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['PREVIEW_PICTURE']['SRC_1X'] = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=> 303, 'height'=> 303), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];
    $arItem['PREVIEW_PICTURE']['SRC_WEBP_1X'] = webpExtension($arItem['PREVIEW_PICTURE']['SRC_1X']);
    $arItem['PREVIEW_PICTURE']['SRC_2X'] = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=> 303 * 2, 'height'=> 303 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];
    $arItem['PREVIEW_PICTURE']['SRC_WEBP_2X'] = webpExtension($arItem['PREVIEW_PICTURE']['SRC_2X']);

    // Добавляем скидку товарам
    if ($arItem['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']) {
        $percent = $arItem['PROPERTIES']['DISCOUNT_PERCENT']['VALUE'] / 100;
        $arItem['MIN_PRICE']['DISCOUNT_VALUE'] = $arItem['MIN_PRICE']['VALUE'] - $arItem['MIN_PRICE']['VALUE'] * $percent;
    }

    if ($arItem['PROPERTIES']['weight']['VALUE']) {
        $arItem['PROPERTIES']['weight']['VALUE'] *= 1000;
        if ($arItem['PROPERTIES']['weight']['VALUE'] % 1000 == 0) {
            $arItem['PROPERTIES']['weight']['VALUE'] = $arItem['PROPERTIES']['weight']['VALUE'] / 1000;
            $arItem['PROPERTIES']['weight']['VALUE_TYPE'] = 'кг';
        } else {
            $arItem['PROPERTIES']['weight']['VALUE_TYPE'] = 'гр.';
        }
    }
    $arItem['NAME'] = $arItem['PROPERTIES']['NAME_H1']['VALUE'] ?: $arItem['NAME'];
    //$arItem['CATALOG_MEASURE_RATIO'] = $arItem['PROPERTIES']['STEP_TO_ADD']['VALUE'] ?: $arItem['CATALOG_MEASURE_RATIO'];
    if (false && (isset($GLOBALS['productCounts']) && !empty($GLOBALS['productCounts']))) {
        if (array_key_exists($arItem['ID'], $GLOBALS['productCounts'])) {
            $arItem['PRODUCT']['QUANTITY'] = $GLOBALS['productCounts'][$arItem['ID']]['AMOUNT'];
        } else {
            $arItem['PRODUCT']['QUANTITY'] = '0';
            $arItem['CAN_BUY'] = 'N';
        }
    }
    else {
        $arItem['PRODUCT']['QUANTITY'] = $arItem["CATALOG_QUANTITY"];
    }


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

