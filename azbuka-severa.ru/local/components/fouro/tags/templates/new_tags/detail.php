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
$APPLICATION->SetPageProperty("title", "Купить " . strtolower($arResult['TAG']['NAME']) . " | В наличии и на заказ в Интерьер Маркет");
$APPLICATION->SetPageProperty("description", "Мебель и аксессуары - " . strtolower($arResult['TAG']['NAME']) . " в наличии и на заказ. Большой выбор в нашем каталоге, консультация дизайнера и гарантия качества.");
$APPLICATION->SetTitle(InteriorHelper::ucfirst($arResult['TAG']['NAME']));
?>
<div class="content">
    <h1 class="page_title page_title__in_section"><? $APPLICATION->ShowTitle(false) ?></h1>
</div>
<section class="catalog section-catalog-list">
    <div class="content">
        <?php
        global $arrFilter;
        $arrFilter['?TAGS'] = $arResult['TAG']['NAME'];

        $sort_type = "PROPERTY_SORT";
        $sort_order = "desc";
        $sort2_type = "PROPERTY_PRIORITET_DLYA_SAYTA";
        $sort2_order = "desc";

        if ($_REQUEST["sort"] == "price") {
            $sort_type = "PROPERTY_NO_PRICE";
            $sort_order = "asc";
            $sort2_type = "PROPERTY_SORT_PRICE";
            $sort2_order = "asc";
        }
        if ($_REQUEST["order"] == "desc" or $_REQUEST["order"] == "asc") {
            $sort2_order = $_REQUEST["order"];
        }
        ?>
        <? $APPLICATION->IncludeComponent(
            "siart:catalog.smart.filter",
            "interior-new",
            array(
                "COMPONENT_TEMPLATE" => "interior-filter",
                "IBLOCK_TYPE" => "1c_catalog",
                "IBLOCK_ID" => "5",
                "SECTION_ID" => "0",
                "SECTION_CODE" => "",
                "FILTER_NAME" => "arrFilter",
                "TAGS" => array($arResult['TAG']['NAME']),
                "HIDE_NOT_AVAILABLE" => "N",
                "TEMPLATE_THEME" => "blue",
                "FILTER_VIEW_MODE" => "vertical",
                "POPUP_POSITION" => "left",
                "DISPLAY_ELEMENT_COUNT" => "Y",
                "SEF_MODE" => "Y",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "Y",
                "SAVE_IN_SESSION" => "N",
                "INSTANT_RELOAD" => "Y",
                "PAGER_PARAMS_NAME" => "11",
                "PRICE_CODE" => array(
                    0 => "Розничная Eur",
                ),
                "CONVERT_CURRENCY" => "N",
                "XML_EXPORT" => "N",
                "SECTION_TITLE" => "NAME",
                "SECTION_DESCRIPTION" => "DESCRIPTION",
                "SEF_RULE" => $arResult['TAG']['DETAIL_LINK'] . "/filter/#SMART_FILTER_PATH#/",
                "SECTION_CODE_PATH" => "",
                "SMART_FILTER_PATH" => $arResult['SMART_FILTER_PATH']
            ),
            false
        ); ?>
        <?php
        $cur = ($_SESSION["cur"]) ? ($_SESSION["cur"]) : ("RUB");
        if ($GLOBALS['arrFilter']) {
            foreach ($GLOBALS['arrFilter'] as $k => &$v) {
                if (strlen($k) == 17) {
                    if ($cur != "EUR") {
                        $v[0] = round(CCurrencyRates::ConvertCurrency($v[0], $cur, "EUR"), 0);
                        $v[1] = round(CCurrencyRates::ConvertCurrency($v[1], $cur, "EUR"), 0);
                    }
                }
            }
        }

        $intSectionID = $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "",
            array(
                "TAG_NAME" => $arResult['TAG']['NAME'],
                "IBLOCK_TYPE" => "1c_catalog",
                "IBLOCK_ID" => "5",
                "ELEMENT_SORT_FIELD" => $sort_type,
                "ELEMENT_SORT_ORDER" => $sort_order,
                "ELEMENT_SORT_FIELD2" => $sort2_type,
                "ELEMENT_SORT_ORDER2" => $sort2_order,
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
                "FILTER_NAME" => "arrFilter",
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
                    0 => 'Розничная Eur ЦЕНА ОТ',
                    1 => "Розничная рубли",
                    2 => "Розничная Eur",
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
            ),
            $component
        ); ?>
    </div>
</section>
<section class="s_tags"></section>
<hr class="hr">
<?php
$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH . "/include/brands_slider.php", array());
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
