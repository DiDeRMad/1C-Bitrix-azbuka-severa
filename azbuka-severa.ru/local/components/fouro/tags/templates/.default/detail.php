<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Купить " . mb_strtolower($arResult['TAG']['NAME'], 'UTF-8') . ". Редкая и лучшая вяленая дикая рыба.");
// $APPLICATION->SetPageProperty("title", "Купить " . mb_strtolower($arResult['TAG']['NAME'], 'UTF-8') . " | В наличии и на заказ");

// $APPLICATION->SetPageProperty("title", "Купить " . strtolower($arResult['TAG']['NAME']) . " | В наличии и на заказ в Интерьер Маркет");
// $APPLICATION->SetPageProperty("description", "Мебель и аксессуары - " . strtolower($arResult['TAG']['NAME']) . " в наличии и на заказ. Большой выбор в нашем каталоге, консультация дизайнера и гарантия качества.");
$APPLICATION->SetPageProperty("description", "Самая вкусная вяленая рыба. Редкие виды и идеальная вялка.");
$APPLICATION->SetTitle(InteriorHelper::ucfirst($arResult['TAG']['NAME']));
global $arrFilterTag;
$arrFilterTag['?TAGS'] = $arResult['TAG']['NAME'];


$sortField1 = 'ID';
$sortField2 = 'ID';
$orderFields1 = 'ASC';
$orderFields2 = 'ASC';
if ($_REQUEST['sort'] == 'price') {
    $sortField1 = 'PROPERTY_SORT_PRICE';
    $sortField2 = 'PROPERTY_SORT_PRICE';
    $orderFields1 = $_REQUEST['order'] ?: 'ASC';
    $orderFields2 = $_REQUEST['order'] ?: 'ASC';
}

?>

<div class="inner-header">
    <div class="container">
        <div class="inner-header__wrapper">
            <? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "catalog",
                array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0"
                )
            ); ?>
            <div class="inner-header__caption">
                <h1 class="caption--h1"><?= InteriorHelper::ucfirst($arResult['TAG']['NAME']) ?></h1>
                <p class="inner-header__caption-description"><?= $arResult['TAG']['CNT'] ?></p>
            </div>
        </div>
    </div>
</div>

<div class="catalog">
    <div class="container">
        <div class="catalog__wrapper">
            <?$intSectionID = $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "tags",
                array(
                    "TAG_NAME" => $arResult['TAG']['NAME'],
                    "IBLOCK_TYPE" => "1c_catalog",
                    "IBLOCK_ID" => "5",
                    "ELEMENT_SORT_FIELD" => $sortField1,
                    "ELEMENT_SORT_ORDER" => $orderFields1,
                    "ELEMENT_SORT_FIELD2" => $sortField2,
                    "ELEMENT_SORT_ORDER2" => $orderFields2,
                    "PROPERTY_CODE" => array(
                        0 => "BREND_1",
                        1 => "RAZMERDLYASAYTA",
                        2 => "NEW_PRICE",
                        3 => "HIDE_PRICE",
                        4 => "STRANA",
                        6 => "STATUS_TOVARA",
                        7 => "NEWPRODUCT",
                        8 => "SALELEADER",
                        9 => "SPECIALOFFER",
                        10 => $_SESSION["cur"],
                        11 => "RAZMERDLYASAYTA",
                        12 => "CML2_ARTICLE",
                        13 => "DIZAYNERY",
                        14 => "MATERIAL",
                        15 => "",
                    ),
                    "META_KEYWORDS" => "-",
                    "META_DESCRIPTION" => "-",
                    "BROWSER_TITLE" => "-",
                    "SET_LAST_MODIFIED" => "Y",
                    "INCLUDE_SUBSECTIONS" => "Y",
                    "BASKET_URL" => "/cart/",
                    "ACTION_VARIABLE" => "action",
                    "PRODUCT_ID_VARIABLE" => "id",
                    "SECTION_ID_VARIABLE" => "SECTION_ID",
                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "FILTER_NAME" => "arrFilterTag",
                    "CACHE_TYPE" => "N",
                    "CACHE_TIME" => "36000000",
                    "CACHE_FILTER" => "Y",
                    "CACHE_GROUPS" => "Y",
                    "SET_TITLE" => "N",
                    "SET_STATUS_404" => "Y",
                    "SHOW_404" => "Y",
                    "MESSAGE_404" => "",
                    "FILE_404" => "",
                    "DISPLAY_COMPARE" => "Y",
                    "PAGE_ELEMENT_COUNT" => "24",
                    "LINE_ELEMENT_COUNT" => "4",
                    "PRICE_CODE" => array(
                        0 => "BASE",
                    ),
                    "USE_PRICE_COUNT" => "N",
                    "SHOW_PRICE_COUNT" => "1",

                    "PRICE_VAT_INCLUDE" => "Y",
                    "USE_PRODUCT_QUANTITY" => "Y",
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                    "PRODUCT_PROPERTIES" => array(),

                    "DISPLAY_TOP_PAGER" => "Y",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "PAGER_TEMPLATE" => "main",
                    "PAGER_TITLE" => "Товары",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
                    "PAGER_SHOW_ALL" => "N",
                    "PAGER_BASE_LINK_ENABLE" => "N",

                    "OFFERS_CART_PROPERTIES" => array(
                        0 => "SIZES_SHOES",
                        1 => "SIZES_CLOTHES",
                        2 => "COLOR_REF",
                    ),
                    "OFFERS_FIELD_CODE" => array(
                        0 => "NAME",
                        1 => "PREVIEW_PICTURE",
                        2 => "DETAIL_PICTURE",
                        3 => "",
                    ),
                    "OFFERS_PROPERTY_CODE" => array(
                        0 => "SIZES_SHOES",
                        1 => "SIZES_CLOTHES",
                        2 => "COLOR_REF",
                        3 => "MORE_PHOTO",
                        4 => "ARTNUMBER",
                        5 => "",
                    ),
                    "OFFERS_SORT_FIELD" => "sort",
                    "OFFERS_SORT_ORDER" => "desc",
                    "OFFERS_SORT_FIELD2" => "id",
                    "OFFERS_SORT_ORDER2" => "desc",
                    "OFFERS_LIMIT" => "0",

                    "SECTION_ID" => 0,
                    "SECTION_CODE" => '',
                    "SECTION_URL" => '',
                    "DETAIL_URL" => '',
                    "USE_MAIN_ELEMENT_SECTION" => 'Y',
                    'CONVERT_CURRENCY' => "Y",
                    'CURRENCY_ID' => "RUB",
                    'HIDE_NOT_AVAILABLE' => "N",

                    'LABEL_PROP' => "-",
                    'ADD_PICT_PROP' => "-",
                    'PRODUCT_DISPLAY_MODE' => "Y",

                    'OFFER_ADD_PICT_PROP' => "MORE_PHOTO",
                    'OFFER_TREE_PROPS' => array(
                        0 => "SIZES_SHOES",
                        1 => "SIZES_CLOTHES",
                        2 => "COLOR_REF",
                        3 => "",
                    ),
                    'PRODUCT_SUBSCRIPTION' => "Y",
                    "SHOW_DISCOUNT_PERCENT" => "Y",
                    "SHOW_OLD_PRICE" => "Y",
                    "MESS_BTN_BUY" => "Заказать",
                    "MESS_BTN_ADD_TO_BASKET" => "Заказать",
                    "MESS_BTN_COMPARE" => "Сравнение",
                    "MESS_BTN_DETAIL" => "Подробнее",
                    "MESS_NOT_AVAILABLE" => "Нет в наличии",

                    'TEMPLATE_THEME' => "",
                    "ADD_SECTIONS_CHAIN" => "N",
                    'ADD_TO_BASKET_ACTION' => "",
                    'SHOW_CLOSE_POPUP' => "Y",
                    'COMPARE_PATH' => '',
                    'SHOW_ALL_WO_SECTION' => "Y",
                    "MESS_STICKER_SALE_TEXT" => "СМЕНА ЭКСПОЗИЦИИ",
                    'TAGS_COUNT' => $arResult['TAG']['CNT']
                ),
                $component
            ); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
