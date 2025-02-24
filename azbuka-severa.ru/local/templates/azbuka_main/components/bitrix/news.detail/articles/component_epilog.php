<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/**
 * @var CMain $APPLICATION
 * @var array $arResult
 */

ob_start();
if ($arResult['PROPERTIES']['ITEM_1']['VALUE']) {

    echo '</div>';
    echo '</div>';

    global $filter1;
    $filter1['ID'] = $arResult['PROPERTIES']['ITEM_1']['VALUE'];

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "slider",
        array(
            "ACTION_VARIABLE" => "action",
            "ADD_PICT_PROP" => "-",
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "ADD_TO_BASKET_ACTION" => "ADD",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "BACKGROUND_IMAGE" => "-",
            "BASKET_URL" => "/basket/",
            "BROWSER_TITLE" => "-",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "USE_COMPARE_LIST" => "Y",
            "COMPARE_PATH" => "/catalog/compare.php?action=#ACTION_CODE#",
            "COMPARE_ELEMENT_SORT_FIELD" => "sort",
            "COMPARE_ELEMENT_SORT_ORDER" => "asc",
            "COMPARE_FIELD_CODE" => array(
                0 => "NAME",
                1 => "DETAIL_PICTURE",
                2 => "",
            ),
            "COMPARE_NAME" => "CATALOG_COMPARE_LIST",
            "COMPARE_POSITION" => "top left",
            "COMPARE_POSITION_FIXED" => "Y",
            "COMPARE_PROPERTY_CODE" => array(
                0 => "ARTICLE",
                1 => "BRAND",
                2 => "SEX",
                3 => "",
            ),
            "COMPATIBLE_MODE" => "Y",
            "CONVERT_CURRENCY" => "N",
            "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
            "DETAIL_URL" => "",
            "DISABLE_INIT_JS_IN_COMPONENT" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "USE_COMPARE" => "Y",
            "DISPLAY_COMPARE" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_SORT_FIELD" => "sort",
            "ELEMENT_SORT_FIELD2" => "id",
            "ELEMENT_SORT_ORDER" => "asc",
            "ELEMENT_SORT_ORDER2" => "desc",
            "ENLARGE_PRODUCT" => "STRICT",
            "FILTER_NAME" => "filter1",
            "HIDE_NOT_AVAILABLE" => "Y",
            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
            "IBLOCK_ID" => "5",
            "IBLOCK_TYPE" => "catalog",
            "INCLUDE_SUBSECTIONS" => "Y",
            "LABEL_PROP" => array(),
            "LAZY_LOAD" => "N",
            "LINE_ELEMENT_COUNT" => "3",
            "LOAD_ON_SCROLL" => "N",
            "MESSAGE_404" => "",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "MESS_BTN_BUY" => "Купить",
            "MESS_BTN_COMPARE" => "Сравнить",
            "MESS_BTN_DETAIL" => "Подробнее",
            "MESS_BTN_SUBSCRIBE" => "Подписаться",
            "MESS_NOT_AVAILABLE" => "Нет в наличии",
            "META_DESCRIPTION" => "-",
            "META_KEYWORDS" => "-",
            "OFFERS_LIMIT" => "5",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Товары",
            "PAGE_ELEMENT_COUNT" => "8",
            "PARTIAL_PRODUCT_PROPERTIES" => "Y",
            "PRICE_CODE" => array(
                0 => "BASE",
            ),
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
            "PRODUCT_SUBSCRIPTION" => "Y",
            "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
            "RCM_TYPE" => "personal",
            "SECTION_CODE" => "",
            "SECTION_ID" => $_REQUEST["SECTION_ID"],
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "SECTION_URL" => "",
            "SECTION_USER_FIELDS" => array(
                0 => "",
                1 => "",
            ),
            "SEF_MODE" => "N",
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SHOW_ALL_WO_SECTION" => "Y",
            "SHOW_CLOSE_POPUP" => "Y",
            "SHOW_DISCOUNT_PERCENT" => "N",
            "SHOW_FROM_SECTION" => "N",
            "SHOW_MAX_QUANTITY" => "N",
            "SHOW_OLD_PRICE" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "SHOW_SLIDER" => "Y",
            "SLIDER_INTERVAL" => "3000",
            "SLIDER_PROGRESS" => "N",
            "TEMPLATE_THEME" => "blue",
            "USE_ENHANCED_ECOMMERCE" => "N",
            "USE_MAIN_ELEMENT_SECTION" => "N",
            "USE_PRICE_COUNT" => "N",
            "USE_PRODUCT_QUANTITY" => "N",
            "PROPERTY_CODE" => array(
                0 => "BRAND",
                1 => "",
            ),
            "COMPONENT_TEMPLATE" => "slider",
            "PROPERTY_CODE_MOBILE" => array(),
            "PRODUCT_PROPERTIES" => array(),
        ),
        false
    );
    echo ' <div class="article-content">';
    echo ' <div class="container">';
}


$item1 = ob_get_clean();

ob_start();
if ($arResult['PROPERTIES']['ITEM_2']['VALUE']) {

    echo '</div>';
    echo '</div>';

    global $filter2;
    $filter2['ID'] = $arResult['PROPERTIES']['ITEM_2']['VALUE'];

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "slider",
        array(
            "ACTION_VARIABLE" => "action",
            "ADD_PICT_PROP" => "-",
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "ADD_TO_BASKET_ACTION" => "ADD",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "BACKGROUND_IMAGE" => "-",
            "BASKET_URL" => "/basket/",
            "BROWSER_TITLE" => "-",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "USE_COMPARE_LIST" => "Y",
            "COMPARE_PATH" => "/catalog/compare.php?action=#ACTION_CODE#",
            "COMPARE_ELEMENT_SORT_FIELD" => "sort",
            "COMPARE_ELEMENT_SORT_ORDER" => "asc",
            "COMPARE_FIELD_CODE" => array(
                0 => "NAME",
                1 => "DETAIL_PICTURE",
                2 => "",
            ),
            "COMPARE_NAME" => "CATALOG_COMPARE_LIST",
            "COMPARE_POSITION" => "top left",
            "COMPARE_POSITION_FIXED" => "Y",
            "COMPARE_PROPERTY_CODE" => array(
                0 => "ARTICLE",
                1 => "BRAND",
                2 => "SEX",
                3 => "",
            ),
            "COMPATIBLE_MODE" => "Y",
            "CONVERT_CURRENCY" => "N",
            "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
            "DETAIL_URL" => "",
            "DISABLE_INIT_JS_IN_COMPONENT" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "USE_COMPARE" => "Y",
            "DISPLAY_COMPARE" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_SORT_FIELD" => "sort",
            "ELEMENT_SORT_FIELD2" => "id",
            "ELEMENT_SORT_ORDER" => "asc",
            "ELEMENT_SORT_ORDER2" => "desc",
            "ENLARGE_PRODUCT" => "STRICT",
            "FILTER_NAME" => "filter2",
            "HIDE_NOT_AVAILABLE" => "Y",
            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
            "IBLOCK_ID" => "5",
            "IBLOCK_TYPE" => "catalog",
            "INCLUDE_SUBSECTIONS" => "Y",
            "LABEL_PROP" => array(),
            "LAZY_LOAD" => "N",
            "LINE_ELEMENT_COUNT" => "3",
            "LOAD_ON_SCROLL" => "N",
            "MESSAGE_404" => "",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "MESS_BTN_BUY" => "Купить",
            "MESS_BTN_COMPARE" => "Сравнить",
            "MESS_BTN_DETAIL" => "Подробнее",
            "MESS_BTN_SUBSCRIBE" => "Подписаться",
            "MESS_NOT_AVAILABLE" => "Нет в наличии",
            "META_DESCRIPTION" => "-",
            "META_KEYWORDS" => "-",
            "OFFERS_LIMIT" => "5",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Товары",
            "PAGE_ELEMENT_COUNT" => "8",
            "PARTIAL_PRODUCT_PROPERTIES" => "Y",
            "PRICE_CODE" => array(
                0 => "BASE",
            ),
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
            "PRODUCT_SUBSCRIPTION" => "Y",
            "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
            "RCM_TYPE" => "personal",
            "SECTION_CODE" => "",
            "SECTION_ID" => $_REQUEST["SECTION_ID"],
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "SECTION_URL" => "",
            "SECTION_USER_FIELDS" => array(
                0 => "",
                1 => "",
            ),
            "SEF_MODE" => "N",
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SHOW_ALL_WO_SECTION" => "Y",
            "SHOW_CLOSE_POPUP" => "Y",
            "SHOW_DISCOUNT_PERCENT" => "N",
            "SHOW_FROM_SECTION" => "N",
            "SHOW_MAX_QUANTITY" => "N",
            "SHOW_OLD_PRICE" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "SHOW_SLIDER" => "Y",
            "SLIDER_INTERVAL" => "3000",
            "SLIDER_PROGRESS" => "N",
            "TEMPLATE_THEME" => "blue",
            "USE_ENHANCED_ECOMMERCE" => "N",
            "USE_MAIN_ELEMENT_SECTION" => "N",
            "USE_PRICE_COUNT" => "N",
            "USE_PRODUCT_QUANTITY" => "N",
            "PROPERTY_CODE" => array(
                0 => "BRAND",
                1 => "",
            ),
            "COMPONENT_TEMPLATE" => "slider",
            "PROPERTY_CODE_MOBILE" => array(),
            "PRODUCT_PROPERTIES" => array(),
        ),
        false
    );
    echo ' <div class="article-content">';
    echo ' <div class="container">';
}


$item2 = ob_get_clean();

ob_start();
if ($arResult['PROPERTIES']['ITEM_3']['VALUE']) {

    echo '</div>';
    echo '</div>';

    global $filter3;
    $filter3['ID'] = $arResult['PROPERTIES']['ITEM_3']['VALUE'];

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "slider",
        array(
            "ACTION_VARIABLE" => "action",
            "ADD_PICT_PROP" => "-",
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "ADD_TO_BASKET_ACTION" => "ADD",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "BACKGROUND_IMAGE" => "-",
            "BASKET_URL" => "/basket/",
            "BROWSER_TITLE" => "-",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "A",
            "USE_COMPARE_LIST" => "Y",
            "COMPARE_PATH" => "/catalog/compare.php?action=#ACTION_CODE#",
            "COMPARE_ELEMENT_SORT_FIELD" => "sort",
            "COMPARE_ELEMENT_SORT_ORDER" => "asc",
            "COMPARE_FIELD_CODE" => array(
                0 => "NAME",
                1 => "DETAIL_PICTURE",
                2 => "",
            ),
            "COMPARE_NAME" => "CATALOG_COMPARE_LIST",
            "COMPARE_POSITION" => "top left",
            "COMPARE_POSITION_FIXED" => "Y",
            "COMPARE_PROPERTY_CODE" => array(
                0 => "ARTICLE",
                1 => "BRAND",
                2 => "SEX",
                3 => "",
            ),
            "COMPATIBLE_MODE" => "Y",
            "CONVERT_CURRENCY" => "N",
            "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
            "DETAIL_URL" => "",
            "DISABLE_INIT_JS_IN_COMPONENT" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "USE_COMPARE" => "Y",
            "DISPLAY_COMPARE" => "Y",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_SORT_FIELD" => "sort",
            "ELEMENT_SORT_FIELD2" => "id",
            "ELEMENT_SORT_ORDER" => "asc",
            "ELEMENT_SORT_ORDER2" => "desc",
            "ENLARGE_PRODUCT" => "STRICT",
            "FILTER_NAME" => "filter3",
            "HIDE_NOT_AVAILABLE" => "Y",
            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
            "IBLOCK_ID" => "5",
            "IBLOCK_TYPE" => "catalog",
            "INCLUDE_SUBSECTIONS" => "Y",
            "LABEL_PROP" => array(),
            "LAZY_LOAD" => "N",
            "LINE_ELEMENT_COUNT" => "3",
            "LOAD_ON_SCROLL" => "N",
            "MESSAGE_404" => "",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "MESS_BTN_BUY" => "Купить",
            "MESS_BTN_COMPARE" => "Сравнить",
            "MESS_BTN_DETAIL" => "Подробнее",
            "MESS_BTN_SUBSCRIBE" => "Подписаться",
            "MESS_NOT_AVAILABLE" => "Нет в наличии",
            "META_DESCRIPTION" => "-",
            "META_KEYWORDS" => "-",
            "OFFERS_LIMIT" => "5",
            "PAGER_BASE_LINK_ENABLE" => "N",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => ".default",
            "PAGER_TITLE" => "Товары",
            "PAGE_ELEMENT_COUNT" => "8",
            "PARTIAL_PRODUCT_PROPERTIES" => "Y",
            "PRICE_CODE" => array(
                0 => "BASE",
            ),
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
            "PRODUCT_SUBSCRIPTION" => "Y",
            "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
            "RCM_TYPE" => "personal",
            "SECTION_CODE" => "",
            "SECTION_ID" => $_REQUEST["SECTION_ID"],
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "SECTION_URL" => "",
            "SECTION_USER_FIELDS" => array(
                0 => "",
                1 => "",
            ),
            "SEF_MODE" => "N",
            "SET_BROWSER_TITLE" => "N",
            "SET_LAST_MODIFIED" => "N",
            "SET_META_DESCRIPTION" => "N",
            "SET_META_KEYWORDS" => "N",
            "SET_STATUS_404" => "N",
            "SET_TITLE" => "N",
            "SHOW_404" => "N",
            "SHOW_ALL_WO_SECTION" => "Y",
            "SHOW_CLOSE_POPUP" => "Y",
            "SHOW_DISCOUNT_PERCENT" => "N",
            "SHOW_FROM_SECTION" => "N",
            "SHOW_MAX_QUANTITY" => "N",
            "SHOW_OLD_PRICE" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "SHOW_SLIDER" => "Y",
            "SLIDER_INTERVAL" => "3000",
            "SLIDER_PROGRESS" => "N",
            "TEMPLATE_THEME" => "blue",
            "USE_ENHANCED_ECOMMERCE" => "N",
            "USE_MAIN_ELEMENT_SECTION" => "N",
            "USE_PRICE_COUNT" => "N",
            "USE_PRODUCT_QUANTITY" => "N",
            "PROPERTY_CODE" => array(
                0 => "BRAND",
                1 => "",
            ),
            "COMPONENT_TEMPLATE" => "slider",
            "PROPERTY_CODE_MOBILE" => array(),
            "PRODUCT_PROPERTIES" => array(),
        ),
        false
    );


    echo ' <div class="article-content">';
    echo ' <div class="container">';
}


$item3 = ob_get_clean();

ob_start();
echo '</div>';
echo '</div>';
?>
    <div class="article-content">
        <div class="container">
            <div class="article-slider">
                <?
                foreach ($arResult['PROPERTIES']['SLIDER_1']['DATA'] as $arSlider): ?>
                    <div class="article-slider__item">
                        <picture>
                            <source type="image/webp"
                                    srcset="<?= $arSlider['1X']['SRC_WEBP']?> 1x, <?= $arSlider['2X']['SRC_WEBP']?> 2x">
                            <img srcset="<?= $arSlider['1X']['SRC']?> 1x, <?= $arSlider['2X']['SRC']?> 2x"
                                 src="<?= $arSlider['1X']['SRC']?>">
                        </picture>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>

<?
echo ' <div class="article-content">';
echo ' <div class="container">';
$slider1 = ob_get_clean();

ob_start();
echo '</div>';
echo '</div>';
?>
    <div class="article-content">
        <div class="container">
            <div class="article-slider">
                <?
                foreach ($arResult['PROPERTIES']['SLIDER_2']['DATA'] as $arSlider): ?>
                    <div class="article-slider__item">
                        <picture>
                            <source type="image/webp"
                                    srcset="<?= $arSlider['1X']['SRC_WEBP']?> 1x, <?= $arSlider['2X']['SRC_WEBP']?> 2x">
                            <img srcset="<?= $arSlider['1X']['SRC']?> 1x, <?= $arSlider['2X']['SRC']?> 2x"
                                 src="<?= $arSlider['1X']['SRC']?>">
                        </picture>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    </div>

<?
echo ' <div class="article-content">';
echo ' <div class="container">';
$slider2 = ob_get_clean();

ob_start();

if ($arResult['PROPERTIES']['VIDEO']['VALUE'] & $arResult['PROPERTIES']['VIDEO_PICTURE']['VALUE']):?>

    <div>
        <a class="video-link" data-fancybox="" href="<?=$arResult['PROPERTIES']['VIDEO']['VALUE']?>">
            <picture>
                <source type="image/webp" srcset="<?= $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_1X_WEBP']?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_2X_WEBP']?> 2x">
                <img srcset="<?= $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_1X']?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_2X']?> 2x" src="<?= $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_1X']?>">
            </picture>
        </a>
    </div>

<?php endif;?>
<?php $video1 = ob_get_clean();

ob_start();

if ($arResult['PROPERTIES']['VIDEO_2']['VALUE'] && $arResult['PROPERTIES']['VIDEO_PICTURE_2']['VALUE']):?>

    <div>
        <a class="video-link" data-fancybox="" href="<?=$arResult['PROPERTIES']['VIDEO_2']['VALUE']?>">
            <picture>
                <source type="image/webp" srcset="<?= $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_1X_WEBP']?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_2X_WEBP']?> 2x">
                <img srcset="<?= $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_1X']?> 1x, <?= $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_2X']?> 2x" src="<?= $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_1X']?>">
            </picture>
        </a>
    </div>

<?php endif;?>
<?php $video2 = ob_get_clean();

ob_start();

if ($arResult['PROPERTIES']['PICTURE_1']['VALUE']):?>
    <div>
        <picture>
            <source type="image/webp" srcset="<?= $arResult['PROPERTIES']['PICTURE_1']['SRC_1X_WEBP']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_1']['SRC_2X_WEBP']?> 2x">
            <img srcset="<?= $arResult['PROPERTIES']['PICTURE_1']['SRC_1X']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_1']['SRC_2X']?> 2x" src="<?= $arResult['PROPERTIES']['PICTURE_1']['SRC_1X']?>">
        </picture>
    </div>
<?php endif;?>
<?php $picture1 = ob_get_clean();

ob_start();

if ($arResult['PROPERTIES']['PICTURE_2']['VALUE']):?>
    <div>
        <picture>
            <source type="image/webp" srcset="<?= $arResult['PROPERTIES']['PICTURE_2']['SRC_1X_WEBP']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_2']['SRC_2X_WEBP']?> 2x">
            <img srcset="<?= $arResult['PROPERTIES']['PICTURE_2']['SRC_1X']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_2']['SRC_2X']?> 2x" src="<?= $arResult['PROPERTIES']['PICTURE_2']['SRC_1X']?>">
        </picture>
    </div>
<?php endif;?>
<?php $picture2 = ob_get_clean();

ob_start();

if ($arResult['PROPERTIES']['PICTURE_3']['VALUE']):?>
    <div>
        <picture>
            <source type="image/webp" srcset="<?= $arResult['PROPERTIES']['PICTURE_3']['SRC_1X_WEBP']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_3']['SRC_2X_WEBP']?> 2x">
            <img srcset="<?= $arResult['PROPERTIES']['PICTURE_3']['SRC_1X']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_3']['SRC_2X']?> 2x" src="<?= $arResult['PROPERTIES']['PICTURE_3']['SRC_1X']?>">
        </picture>
    </div>
<?php endif;?>
<?php $picture3 = ob_get_clean();

ob_start();

if ($arResult['PROPERTIES']['PICTURE_4']['VALUE']):?>
    <div>
        <picture>
            <source type="image/webp" srcset="<?= $arResult['PROPERTIES']['PICTURE_4']['SRC_1X_WEBP']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_4']['SRC_2X_WEBP']?> 2x">
            <img srcset="<?= $arResult['PROPERTIES']['PICTURE_4']['SRC_1X']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_4']['SRC_2X']?> 2x" src="<?= $arResult['PROPERTIES']['PICTURE_4']['SRC_1X']?>">
        </picture>
    </div>
<?php endif;?>
<?php $picture4 = ob_get_clean();



ob_start();

if ($arResult['PROPERTIES']['PICTURE_5']['VALUE']):?>
    <div>
        <picture>
            <source type="image/webp" srcset="<?= $arResult['PROPERTIES']['PICTURE_5']['SRC_1X_WEBP']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_5']['SRC_2X_WEBP']?> 2x">
            <img srcset="<?= $arResult['PROPERTIES']['PICTURE_5']['SRC_1X']?> 1x, <?= $arResult['PROPERTIES']['PICTURE_5']['SRC_2X']?> 2x" src="<?= $arResult['PROPERTIES']['PICTURE_5']['SRC_1X']?>">
        </picture>
    </div>
<?php endif;?>
<?php $picture5 = ob_get_clean();

//printer($arResult['PROPERTIES']);

$content = $arResult["CACHED_TPL"];
$content = str_replace('#ITEM_1#', $item1, $content);
$content = str_replace('#ITEM_2#', $item2, $content);
$content = str_replace('#ITEM_3#', $item3, $content);
$content = str_replace('#SLIDER_1#', $slider1, $content);
$content = str_replace('#SLIDER_2#', $slider2, $content);
$content = str_replace('#VIDEO_1#', $video1, $content);
$content = str_replace('#VIDEO_2#', $video2, $content);
$content = str_replace('#PICTURE_1#', $picture1, $content);
$content = str_replace('#PICTURE_2#', $picture2, $content);
$content = str_replace('#PICTURE_3#', $picture3, $content);
$content = str_replace('#PICTURE_4#', $picture4, $content);
$content = str_replace('#PICTURE_5#', $picture5, $content);
echo $content;