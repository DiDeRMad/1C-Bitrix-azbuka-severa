<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

use Bitrix\Main\Loader;

$this->setFrameMode(true);

global $searchFilter;

$elementOrder = array();
if ($arParams['USE_SEARCH_RESULT_ORDER'] === 'N')
{
    $elementOrder = array(
        "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
        "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
    );
}
echo '<div class="page search-page">';
echo '<div class="container">';
if (Loader::includeModule('search'))
{
    echo '<div style="display:none">';
    $searchQuery = $_REQUEST['q'];
    $obSearch = new CSearch();
    $obSearch->Search(array(
        "QUERY" => $searchQuery,
        "SITE_ID" => SITE_ID,
        "MODULE_ID" => "iblock",
        "PARAM1" => $arParams["IBLOCK_TYPE"],
        "PARAM2" => $arParams["IBLOCK_ID"],
    ));
    $totalCount = $obSearch->SelectedRowsCount();

    // Сохраняем общее количество найденных элементов в сессию
    $_SESSION['SEARCH_TOTAL_COUNT'] = $totalCount;
    $arElements = $APPLICATION->IncludeComponent(
        "bitrix:search.page",
        ".default",
        Array(
            "RESTART" => $arParams["RESTART"],
            "NO_WORD_LOGIC" => $arParams["NO_WORD_LOGIC"],
            "USE_LANGUAGE_GUESS" => "N",
            "CHECK_DATES" => $arParams["CHECK_DATES"],
            "arrFILTER" => array("iblock_".$arParams["IBLOCK_TYPE"]),
            "arrFILTER_iblock_".$arParams["IBLOCK_TYPE"] => array($arParams["IBLOCK_ID"]),
            "USE_TITLE_RANK" => $arParams['USE_TITLE_RANK'],
            "DEFAULT_SORT" => "rank",
            "FILTER_NAME" => "",
            "SHOW_WHERE" => "N",
            "arrWHERE" => array(),
            "SHOW_WHEN" => "N",
            "PAGE_RESULT_COUNT" => 9999,
            "CACHE_TIME" => 0,
            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "N",
            "PAGER_TITLE" => "",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "N",
        ),
        $component,
        array('HIDE_ICONS' => 'Y')
    );
// Сохраняем все найденные ID элементов в сессию
$_SESSION['SEARCH_RESULT_IDS'] = $arElements;
    echo '</div>';
    if (!empty($arElements) && is_array($arElements))
    {
        $searchFilter = array(
            "ID" => $arElements,
        );
        if ($arParams['USE_SEARCH_RESULT_ORDER'] === 'Y')
        {
            $elementOrder = array(
                "ELEMENT_SORT_FIELD" => "ID",
                "ELEMENT_SORT_ORDER" => $arElements
            );
        }
    }
    else
    {
        if (is_array($arElements))
        {
            ?>
            <div style="margin-top: 100px; margin-bottom: 150px;" class="subscribe">
                <div class="container">
                    <div class="subscribe__block">
                        <br>
                        <h2 class="caption--h2">Ничего не найдено</h2>
                        <p class="caption__description">Воспользуйтесь поиском или перейдите в <a style="text-decoration: underline" href="/catalog/">каталог</a> товаров</p>
                        <br>
                    </div>
                </div>
            </div>
            <?return;
        }
    }
}
else
{
    $searchQuery = '';
    if (isset($_REQUEST['q']) && is_string($_REQUEST['q']))
        $searchQuery = trim($_REQUEST['q']);
    if ($searchQuery !== '')
    {
        $searchFilter = array(
            '*SEARCHABLE_CONTENT' => $searchQuery
        );
    }
    unset($searchQuery);
}

    $sortField1 = 'QUANTITY';   
    $sortField2= 'SORT';

    $orderFields1 = 'DESC';
    $orderFields2 = 'ASC';
    if ($_REQUEST['sort'] == 'price') {
        
        $sortField1 = 'QUANTITY';
        $sortField2 = 'catalog_PRICE_1';
        $orderFields1 = 'DESC';
        $orderFields2 = $_REQUEST['order'] ?: 'ASC';

        $elementOrder = array(
            "ELEMENT_SORT_FIELD" => $sortField1,
            "ELEMENT_SORT_ORDER" => $orderFields1,
            "ELEMENT_SORT_FIELD2" => $sortField2,
            "ELEMENT_SORT_ORDER2" => $orderFields2,
        );
    }

/*$sortField1 = 'ID';
$sortField2= 'ID';
$orderFields1 = 'ASC';
$orderFields2 = 'ASC';
if ($_REQUEST['sort'] == 'price') {
    $sortField1 = 'PROPERTY_SORT_PRICE';
    $sortField2 = 'PROPERTY_SORT_PRICE';
    $orderFields1 = $_REQUEST['order'] ?? 'ASC';
    $orderFields2 = $_REQUEST['order'] ?? 'ASC';

    $elementOrder = array(
        "ELEMENT_SORT_FIELD" => $sortField1,
        "ELEMENT_SORT_ORDER" => $orderFields1,
        "ELEMENT_SORT_FIELD2" => $sortField2,
        "ELEMENT_SORT_ORDER2" => $orderFields2,
    );
}*/

if (!empty($searchFilter) && is_array($searchFilter))
{
    $componentParams = array(
        "COMPATIBLE_MODE" => "Y",
            "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
            "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
            "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
            "PROPERTY_CODE_MOBILE" => (isset($arParams["PROPERTY_CODE_MOBILE"]) ? $arParams["PROPERTY_CODE_MOBILE"] : []),
            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
            "OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
            "OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
            "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
            "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
            "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
            "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
            "OFFERS_LIMIT" => $arParams["OFFERS_LIMIT"],
            "SECTION_URL" => $arParams["SECTION_URL"],
            "DETAIL_URL" => $arParams["DETAIL_URL"],
            "BASKET_URL" => $arParams["BASKET_URL"],
            "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
            "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
            "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
            "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
            "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => $arParams["CACHE_TIME"],
            "DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
            "PRICE_CODE" => $arParams["~PRICE_CODE"],
            "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
            "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
            "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
            "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
            "USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
            "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
            "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
            "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
            "CURRENCY_ID" => $arParams["CURRENCY_ID"],
            "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
            "HIDE_NOT_AVAILABLE_OFFERS" => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
            "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
            "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
            "PAGER_TITLE" => $arParams["PAGER_TITLE"],
            "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
            "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
            "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
            "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
            "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
            "LAZY_LOAD" => (isset($arParams["LAZY_LOAD"]) ? $arParams["LAZY_LOAD"] : 'N'),
            "MESS_BTN_LAZY_LOAD" => (isset($arParams["~MESS_BTN_LAZY_LOAD"]) ? $arParams["~MESS_BTN_LAZY_LOAD"] : ''),
            "LOAD_ON_SCROLL" => (isset($arParams["LOAD_ON_SCROLL"]) ? $arParams["LOAD_ON_SCROLL"] : 'N'),
            "FILTER_NAME" => "searchFilter",
            "SECTION_ID" => "",
            "SECTION_CODE" => "",
            "SECTION_USER_FIELDS" => array(),
            "INCLUDE_SUBSECTIONS" => "Y",
            "SHOW_ALL_WO_SECTION" => "Y",
            "META_KEYWORDS" => "",
            "META_DESCRIPTION" => "",
            "BROWSER_TITLE" => "",
            "ADD_SECTIONS_CHAIN" => "N",
            "SET_TITLE" => "N",
            "SET_STATUS_404" => "N",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "N",

            'LABEL_PROP' => (isset($arParams['LABEL_PROP']) ? $arParams['LABEL_PROP'] : ''),
            'LABEL_PROP_MOBILE' => (isset($arParams['LABEL_PROP_MOBILE']) ? $arParams['LABEL_PROP_MOBILE'] : ''),
            'LABEL_PROP_POSITION' => (isset($arParams['LABEL_PROP_POSITION']) ? $arParams['LABEL_PROP_POSITION'] : ''),
            'ADD_PICT_PROP' => (isset($arParams['ADD_PICT_PROP']) ? $arParams['ADD_PICT_PROP'] : ''),
            'PRODUCT_DISPLAY_MODE' => (isset($arParams['PRODUCT_DISPLAY_MODE']) ? $arParams['PRODUCT_DISPLAY_MODE'] : ''),
            'PRODUCT_BLOCKS_ORDER' => (isset($arParams['PRODUCT_BLOCKS_ORDER']) ? $arParams['PRODUCT_BLOCKS_ORDER'] : ''),
            'PRODUCT_ROW_VARIANTS' => (isset($arParams['PRODUCT_ROW_VARIANTS']) ? $arParams['PRODUCT_ROW_VARIANTS'] : ''),
            'ENLARGE_PRODUCT' => (isset($arParams['ENLARGE_PRODUCT']) ? $arParams['ENLARGE_PRODUCT'] : ''),
            'ENLARGE_PROP' => (isset($arParams['ENLARGE_PROP']) ? $arParams['ENLARGE_PROP'] : ''),
            'SHOW_SLIDER' => (isset($arParams['SHOW_SLIDER']) ? $arParams['SHOW_SLIDER'] : 'Y'),
            'SLIDER_INTERVAL' => (isset($arParams['SLIDER_INTERVAL']) ? $arParams['SLIDER_INTERVAL'] : '3000'),
            'SLIDER_PROGRESS' => (isset($arParams['SLIDER_PROGRESS']) ? $arParams['SLIDER_PROGRESS'] : 'N'),

            'OFFER_ADD_PICT_PROP' => (isset($arParams['OFFER_ADD_PICT_PROP']) ? $arParams['OFFER_ADD_PICT_PROP'] : ''),
            'OFFER_TREE_PROPS' => (isset($arParams['OFFER_TREE_PROPS']) ? $arParams['OFFER_TREE_PROPS'] : []),
            'PRODUCT_SUBSCRIPTION' => (isset($arParams['PRODUCT_SUBSCRIPTION']) ? $arParams['PRODUCT_SUBSCRIPTION'] : ''),
            'SHOW_DISCOUNT_PERCENT' => (isset($arParams['SHOW_DISCOUNT_PERCENT']) ? $arParams['SHOW_DISCOUNT_PERCENT'] : ''),
            'SHOW_OLD_PRICE' => (isset($arParams['SHOW_OLD_PRICE']) ? $arParams['SHOW_OLD_PRICE'] : ''),
            'SHOW_MAX_QUANTITY' => (isset($arParams['SHOW_MAX_QUANTITY']) ? $arParams['SHOW_MAX_QUANTITY'] : ''),
            'MESS_SHOW_MAX_QUANTITY' => (isset($arParams['~MESS_SHOW_MAX_QUANTITY']) ? $arParams['~MESS_SHOW_MAX_QUANTITY'] : ''),
            'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
            'MESS_RELATIVE_QUANTITY_MANY' => (isset($arParams['~MESS_RELATIVE_QUANTITY_MANY']) ? $arParams['~MESS_RELATIVE_QUANTITY_MANY'] : ''),
            'MESS_RELATIVE_QUANTITY_FEW' => (isset($arParams['~MESS_RELATIVE_QUANTITY_FEW']) ? $arParams['~MESS_RELATIVE_QUANTITY_FEW'] : ''),
            'MESS_BTN_BUY' => (isset($arParams['~MESS_BTN_BUY']) ? $arParams['~MESS_BTN_BUY'] : ''),
            'MESS_BTN_ADD_TO_BASKET' => (isset($arParams['~MESS_BTN_ADD_TO_BASKET']) ? $arParams['~MESS_BTN_ADD_TO_BASKET'] : ''),
            'MESS_BTN_SUBSCRIBE' => (isset($arParams['~MESS_BTN_SUBSCRIBE']) ? $arParams['~MESS_BTN_SUBSCRIBE'] : ''),
            'MESS_BTN_DETAIL' => (isset($arParams['~MESS_BTN_DETAIL']) ? $arParams['~MESS_BTN_DETAIL'] : ''),
            'MESS_NOT_AVAILABLE' => (isset($arParams['~MESS_NOT_AVAILABLE']) ? $arParams['~MESS_NOT_AVAILABLE'] : ''),
            'MESS_BTN_COMPARE' => (isset($arParams['~MESS_BTN_COMPARE']) ? $arParams['~MESS_BTN_COMPARE'] : ''),

            'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
            'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
            'BRAND_PROPERTY' => (isset($arParams['BRAND_PROPERTY']) ? $arParams['BRAND_PROPERTY'] : ''),

            'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
            'ADD_TO_BASKET_ACTION' => (isset($arParams['ADD_TO_BASKET_ACTION']) ? $arParams['ADD_TO_BASKET_ACTION'] : ''),
            'SHOW_CLOSE_POPUP' => (isset($arParams['SHOW_CLOSE_POPUP']) ? $arParams['SHOW_CLOSE_POPUP'] : ''),
            'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),
            'COMPARE_NAME' => (isset($arParams['COMPARE_NAME']) ? $arParams['COMPARE_NAME'] : ''),
            'USE_COMPARE_LIST' => (isset($arParams['USE_COMPARE_LIST']) ? $arParams['USE_COMPARE_LIST'] : ''),
            "ELEMENT_SORT_FIELD" => $sortField1,
            "ELEMENT_SORT_ORDER" => $orderFields1,
            "ELEMENT_SORT_FIELD2" => $sortField2,
            "ELEMENT_SORT_ORDER2" => $orderFields2,
        );

    $APPLICATION->IncludeComponent(
        "bitrix:catalog.section",
        "search",
        $componentParams,
        $arResult["THEME_COMPONENT"],
        array(
            "PAGE_ELEMENT_COUNT" => 9999,
            'HIDE_ICONS' => 'Y'
        )
    );
}

echo '</div>';
echo '</div>';