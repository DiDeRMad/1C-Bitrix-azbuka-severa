<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

global $superFilter;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 *
 *  _________________________________________________________________________
 * |    Attention!
 * |    The following comments are for system use
 * |    and are required for the component to work correctly in ajax mode:
 * |    <!-- items-container -->
 * |    <!-- pagination-container -->
 * |    <!-- component-end -->
 */

$this->setFrameMode(true);
//printer($superFilter);
$navParams = [];

if (!empty($arResult['NAV_RESULT'])) {
    $navParams = array(
        'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
        'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
        'NavNum'       => $arResult['NAV_RESULT']->NavNum
    );
} else {
    $navParams = array(
        'NavPageCount' => 1,
        'NavPageNomer' => 1,
        'NavNum'       => $this->randString()
    );
}
$showed = 0;

$arResult['ELEMENTS_COUNT'] = $arResult['NAV_RESULT']->NavRecordCount;


if ($arParams['PAGE_ELEMENT_COUNT'] <= $arResult['ELEMENTS_COUNT']) {
    if ($arParams['PAGE_ELEMENT_COUNT'] * $navParams['NavPageNomer'] < $arResult['ELEMENTS_COUNT']) {
        $percent = round($arParams['PAGE_ELEMENT_COUNT'] * $navParams['NavPageNomer'] / $arResult['ELEMENTS_COUNT'] * 100);
        $showed  = $arParams['PAGE_ELEMENT_COUNT'] * $navParams['NavPageNomer'];
    } else {
        $percent = 100;
        $showed  = $arResult['ELEMENTS_COUNT'];
    }
} else {
    if ($arResult['ELEMENTS_COUNT'] != 0) {
        $percent = round($arResult['ELEMENTS_COUNT'] * $navParams['NavPageNomer'] / $arResult['ELEMENTS_COUNT'] * 100);
    }
    $showed  = $arResult['ELEMENTS_COUNT'] * $navParams['NavPageNomer'];
}


$priceFilterValue = [];
$curPage          = $APPLICATION->GetCurPage();
$arFilterParams   = [];

$tmpPage = explode('filter', $curPage)[1];
$tmpPage = explode('/', $tmpPage);


foreach ($superFilter as $arFilter) {
    if (stripos($curPage, strtolower($arFilter['CODE'])) !== false) {
        if ($arFilter['CODE'] == 'SORT_PRICE') {
            $arTmp         = [];
            $arTmp['NAME'] = 'Цена: от ' . $arFilter['VALUES']['MIN']['HTML_VALUE'] . ' до ' . $arFilter['VALUES']['MAX']['HTML_VALUE'];
            foreach ($tmpPage as $item) {
                if (stripos($item, $arFilter['CODE']) !== false)
                    $arTmp['FILTER'] = '/' . $item;
            }
            $arFilterParams[] = $arTmp;
        } else {
            foreach ($arFilter['VALUES'] as $arValue) {
                if ($arValue['CHECKED']) {
                    $arTmp         = [];
                    $arTmp['NAME'] = $arValue['VALUE'];
                    foreach ($tmpPage as $item) {
                        if (stripos($item, $arFilter['CODE']) !== false)
                            $arTmp['FILTER'] = '/' . $item;
                    }
                    $arFilterParams[] = $arTmp;
                }
            }
        }
    }
}

$lastModified = gmdate("D, d M Y H:i:s \G\M\T", strtotime($arResult['TIMESTAMP_X']));
$ifModified   = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '', 5));

if ($ifModified && $ifModified >= $lastModified) {
    // страница не изменилась, отдача http статуса 304
    header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
    exit;
}
header('Last-Modified: ' . $lastModified); ?>
<div class="catalog__content">
    <div class="sort-block">

        <div class="sort-tag__extra-wrapper">
            <button class="btn btn--outline mobile-btn-filter">Категории</button>
            <? if (!empty($arFilterParams)): ?>
                <div class="sort-tag__wrapper">
                    <div class="sort-tag">
                        <? foreach ($arFilterParams as $arFilterParam): ?>
                            <?
                            $link = str_replace($arFilterParam['FILTER'], '', $curPage);
                            if (stripos($link, '/filter/apply/') !== false)
                                $link = str_replace('/filter/apply', '', $link);
                            ?>
                            <div class="sort-tag__item">
                                <span class="sort-tag__title"><?= $arFilterParam['NAME'] ?></span>
                                <span onclick="window.location.href='<?= $link ?>'" class="sort-tag__remove">
                                                <svg class="icon--16">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#close-icon"></use>
                                                </svg>
                                            </span>
                            </div>
                        <? endforeach; ?>
                        <? if (!empty($arFilterParams)): ?>
                            <div class="sort-tag__item sort-tag__item--outline">
                                <span class="sort-tag__title">Очистить все</span>
                                <span onclick="$('#del_filter').trigger('click')" class="sort-tag__remove">
                                                <svg class="icon--16">
                                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#close-icon"></use>
                                                </svg>
                                            </span>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            <? endif; ?>
        </div>


        <div class="sort-type">
            <p class="sort-type__title">Сортировать:</p>
            <div class="sort-type__wrapper">
                <label for="types1" class="sort-type__label">
                    <input type="radio" class="sort_catalog"
                           data-link="<?= $APPLICATION->GetCurPage() ?>?sort=price&order=asc" name="filter-name"
                           id="types1"
                           value="Сначала дешевые" <?php if ($_REQUEST['sort'] == 'price' && $_REQUEST['order'] == 'asc') echo 'checked' ?>>
                    <span>Сначала дешевые</span>
                </label>
                <label for="types2" class="sort-type__label">
                    <input type="radio" class="sort_catalog"
                           data-link="<?= $APPLICATION->GetCurPage() ?>?sort=price&order=desc" name="filter-name"
                           id="types2"
                           value="Сначала дорогие" <?php if ($_REQUEST['sort'] == 'price' && $_REQUEST['order'] == 'desc') echo 'checked' ?>>
                    <span>Сначала дорогие</span>
                </label>
            </div>
        </div>
    </div>

    <!--  нужно удалить disabled по клику на "загрузить еще"  -->
    <div class="catalog-list">
        <? if (empty($arResult['ITEMS'])): ?>
            <p>Товары не найдены</p>
        <? endif ?>

        <?php foreach ($arResult['ITEMS'] as $key => $arItem): ?>


            <?php

            if (array_key_last($arResult['ITEMS']) == $key) {
                $arItem['IS_LAST'] = 'Y';
            }
            $APPLICATION->IncludeComponent(
                'bitrix:catalog.item',
                'slider',
                array(
                    'RESULT' => array(
                        'ITEM' => $arItem,
                        //'AREA_ID' => $areaIds[$item['ID']],
                        'TYPE' => 'CARD',
                        //'BIG_LABEL' => 'N',
                        //'BIG_DISCOUNT_PERCENT' => 'N',
                        //'BIG_BUTTONS' => 'N',
                        //'SCALABLE' => 'N'
                        'LAST' => array_key_last($arResult['ITEMS']) == $key ? 'Y' : 'N'
                    ),

                ),
                $component,
                array('HIDE_ICONS' => 'Y')
            );
            ?>


        <?php endforeach; ?>


    </div>

    <div class="catalog-list-paginator">
        <div class="catalog-list-paginator__counter">
            Просмотрено <?= $showed ?>
            из <?= $arResult['ELEMENTS_COUNT'] ?></div>
        <div class="catalog-list-paginator__progress-wrapper">
            <div style="width: <?= $percent ?>px;" class="catalog-list-paginator__progress"></div>
        </div>
        <?php if ($navParams['NavPageNomer'] < $navParams['NavPageCount']): ?>
            <?
            $url = $_SERVER['REQUEST_URI'];
            if ($_SERVER['QUERY_STRING']) {
                $url .= '&';
            } else {
                $url .= '?';
            }
            ?>
            <button data-nextpage="<?= $APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($navParams['NavPageNomer']+1)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum'])) ?>"
                    class="btn btn--outline show_more">Загрузить еще
            </button>
        <?php endif ?>
        <??>
            <div class="catalog-list-paginator__pages">
                <nav class="paging">
				<?if(($navParams['NavPageNomer']-1)>0 && ($navParams['NavPageNomer']-1)<$navParams['NavPageCount']){?>
                    <a href="<?= $APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($navParams['NavPageNomer']-1)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum'])) ?>" class="paging__control paging__control--prev">
                        <svg class="paging__icon" viewBox="0 0 45 32">
                            <path d="M19.782 5.594c0.665-0.59 1.082-1.446 1.082-2.4 0-1.769-1.434-3.203-3.203-3.203-0.815 0-1.559 0.305-2.125 0.806l0.003-0.003-14.448 12.8c-0.666 0.589-1.083 1.446-1.083 2.4s0.418 1.811 1.080 2.397l0.003 0.003 14.448 12.8c0.562 0.499 1.306 0.803 2.122 0.803 1.769 0 3.203-1.434 3.203-3.203 0-0.954-0.417-1.81-1.078-2.397l-0.003-0.003-8.131-7.194h29.962c1.767 0 3.2-1.433 3.2-3.2s-1.433-3.2-3.2-3.2v0h-29.962z"/>
                        </svg>
                    </a>
				<?}?>
				<?if($navParams['NavPageCount']>1){?>
                    <div class="paging__area">
						<?
						$pageNum=1;
						while($pageNum<=$navParams['NavPageCount']){
							if($pageNum==$navParams['NavPageNomer']){
								?>
								<span class="paging__item paging__item--active"><?= $pageNum?></span>
								<?
							}elseif($pageNum<$navParams['NavPageNomer']){
								?>
								<a class="paging__item" href="<?=$APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($pageNum)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum']))?>"><?= $pageNum?></a>
								<?
							}elseif($pageNum>$navParams['NavPageNomer']){
								?>
								<a class="paging__item" href="<?=$APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($pageNum)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum']))?>"><?= $pageNum?></a>
								<?
							}
							$pageNum++;
						}?>
                        
                        
                    </div>
				<?}?>
					<?if(($navParams['NavPageNomer']+1)>1 && ($navParams['NavPageNomer']+1)<=$navParams['NavPageCount']){?>
                    <a href="<?= $APPLICATION->GetCurPageParam("PAGEN_".$navParams['NavNum']."=".($navParams['NavPageNomer']+1)."&SIZEN_".$navParams['NavNum']."=16", array("PAGEN_".$navParams['NavNum'], "SIZEN_".$navParams['NavNum'])) ?>" class="paging__control paging__control--next">
                        <svg class="paging__icon" viewBox="0 0 45 32">
                            <path d="M25.030 26.406c-0.665 0.59-1.082 1.446-1.082 2.4 0 1.769 1.434 3.203 3.203 3.203 0.815 0 1.559-0.305 2.125-0.806l-0.003 0.003 14.448-12.8c0.666-0.589 1.083-1.446 1.083-2.4s-0.418-1.811-1.080-2.397l-0.003-0.003-14.448-12.8c-0.562-0.499-1.306-0.803-2.122-0.803-1.769 0-3.203 1.434-3.203 3.203 0 0.954 0.417 1.81 1.078 2.397l0.003 0.003 8.131 7.194h-29.962c-1.767 0-3.2 1.433-3.2 3.2s1.433 3.2 3.2 3.2v0h29.962z"/>
                        </svg>
                    </a>
					<?}?>
                </nav>
            </div>
        <??>
    </div>
    <?
    // SEO текст для страницы
    if (!empty($arResult["SEO_TEXT"])) {
        echo implode("<br>", $arResult["SEO_TEXT"]);
    }
    ?>
</div>
</div>
</div>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/section_description.php'); ?>

<?php
global $arrFilter;
$arrFilter['PROPERTY_POPULAR'] = POPULAR_CHECK;
?>
<?php $APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "slider",
    array(
        "ACTION_VARIABLE"                 => "action",
        "ADD_PICT_PROP"                   => "-",
        "ADD_PROPERTIES_TO_BASKET"        => "Y",
        "ADD_SECTIONS_CHAIN"              => "N",
        "ADD_TO_BASKET_ACTION"            => "ADD",
        "AJAX_MODE"                       => "N",
        "AJAX_OPTION_ADDITIONAL"          => "",
        "AJAX_OPTION_HISTORY"             => "N",
        "AJAX_OPTION_JUMP"                => "N",
        "AJAX_OPTION_STYLE"               => "N",
        "BACKGROUND_IMAGE"                => "-",
        "BASKET_URL"                      => "/personal/basket.php",
        "BROWSER_TITLE"                   => "-",
        "CACHE_FILTER"                    => "Y",
        "CACHE_GROUPS"                    => "Y",
        "CACHE_TIME"                      => "43200",
        "CACHE_TYPE"                      => "A",
        "COMPATIBLE_MODE"                 => "Y",
        "CONVERT_CURRENCY"                => "N",
        "CUSTOM_FILTER"                   => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
        "DETAIL_URL"                      => "",
        "DISABLE_INIT_JS_IN_COMPONENT"    => "N",
        "DISPLAY_BOTTOM_PAGER"            => "N",
        "DISPLAY_COMPARE"                 => "N",
        "DISPLAY_TOP_PAGER"               => "N",
        "ELEMENT_SORT_FIELD"              => "sort",
        "ELEMENT_SORT_FIELD2"             => "id",
        "ELEMENT_SORT_ORDER"              => "asc",
        "ELEMENT_SORT_ORDER2"             => "desc",
        "ENLARGE_PRODUCT"                 => "STRICT",
        "FILTER_NAME"                     => "arrFilter",
        "HIDE_NOT_AVAILABLE"              => "N",
        "HIDE_NOT_AVAILABLE_OFFERS"       => "N",
        "IBLOCK_ID"                       => "5",
        "IBLOCK_TYPE"                     => "catalog",
        "INCLUDE_SUBSECTIONS"             => "Y",
        "LABEL_PROP"                      => array(),
        "LAZY_LOAD"                       => "N",
        "LINE_ELEMENT_COUNT"              => "3",
        "LOAD_ON_SCROLL"                  => "N",
        "MESSAGE_404"                     => "",
        "MESS_BTN_ADD_TO_BASKET"          => "В корзину",
        "MESS_BTN_BUY"                    => "Купить",
        "MESS_BTN_DETAIL"                 => "Подробнее",
        "MESS_BTN_SUBSCRIBE"              => "Подписаться",
        "MESS_NOT_AVAILABLE"              => "Нет в наличии",
        "META_DESCRIPTION"                => "-",
        "META_KEYWORDS"                   => "-",
        "OFFERS_LIMIT"                    => "5",
        "PAGER_BASE_LINK_ENABLE"          => "N",
        "PAGER_DESC_NUMBERING"            => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL"                  => "N",
        "PAGER_SHOW_ALWAYS"               => "N",
        "PAGER_TEMPLATE"                  => ".default",
        "PAGER_TITLE"                     => "Товары",
        "PAGE_ELEMENT_COUNT"              => "18",
        "PARTIAL_PRODUCT_PROPERTIES"      => "N",
        "PRICE_CODE"                      => array("BASE"),
        "PRICE_VAT_INCLUDE"               => "Y",
        "PRODUCT_BLOCKS_ORDER"            => "price,props,sku,quantityLimit,quantity,buttons",
        "PRODUCT_ID_VARIABLE"             => "id",
        "PRODUCT_PROPS_VARIABLE"          => "prop",
        "PRODUCT_QUANTITY_VARIABLE"       => "quantity",
        "PRODUCT_ROW_VARIANTS"            => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
        "PRODUCT_SUBSCRIPTION"            => "Y",
        "RCM_PROD_ID"                     => $_REQUEST["PRODUCT_ID"],
        "RCM_TYPE"                        => "personal",
        "SECTION_CODE"                    => "",
        "SECTION_ID"                      => $_REQUEST["SECTION_ID"],
        "SECTION_ID_VARIABLE"             => "SECTION_ID",
        "SECTION_URL"                     => "",
        "SECTION_USER_FIELDS"             => array("", ""),
        "SEF_MODE"                        => "N",
        "SET_BROWSER_TITLE"               => "N",
        "SET_LAST_MODIFIED"               => "N",
        "SET_META_DESCRIPTION"            => "N",
        "SET_META_KEYWORDS"               => "N",
        "SET_STATUS_404"                  => "N",
        "SET_TITLE"                       => "N",
        "SHOW_404"                        => "N",
        "SHOW_ALL_WO_SECTION"             => "N",
        "SHOW_CLOSE_POPUP"                => "N",
        "SHOW_DISCOUNT_PERCENT"           => "N",
        "SHOW_FROM_SECTION"               => "N",
        "SHOW_MAX_QUANTITY"               => "N",
        "SHOW_OLD_PRICE"                  => "N",
        "SHOW_PRICE_COUNT"                => "1",
        "SHOW_SLIDER"                     => "Y",
        "SLIDER_INTERVAL"                 => "3000",
        "SLIDER_PROGRESS"                 => "N",
        "TEMPLATE_THEME"                  => "blue",
        "USE_ENHANCED_ECOMMERCE"          => "N",
        "USE_MAIN_ELEMENT_SECTION"        => "N",
        "USE_PRICE_COUNT"                 => "N",
        "USE_PRODUCT_QUANTITY"            => "N",
        "TITLE"                           => "Популярное",
        "CLASS"                           => 'popular'
    )
); ?>

<?php
if (strpos($APPLICATION->GetCurDir(), '/filter/') !== false) {
    $APPLICATION->ShowViewContent('catalog_filtered-relink');
} else if ($arResult['DEPTH_LEVEL'] != '1') {
    global $sectionsFilter;

    $arParentSection = CIBlockSection::GetList([], ['IBLOCK_ID' => $arResult['IBLOCK_ID'], 'ID' => $arResult['IBLOCK_SECTION_ID'], 'ACCTIVE' => 'Y'], false, ['IBLOCK_ID', 'ID', 'ACTIVE', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL'])->fetch();

    $sectionsFilter['SECTION_ID'] = $arParentSection['ID'];
    $sectionsFilter['!ID']        = $arResult['ID'];

    if ($arParentSection['DEPTH_LEVEL'] && $arParentSection['DEPTH_LEVEL'] != '') {
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section.list",
            "other",
            array(
                "ADD_SECTIONS_CHAIN"    => "N",
                "CACHE_FILTER"          => "Y",
                "CACHE_GROUPS"          => "Y",
                "CACHE_TIME"            => "36000000",
                "CACHE_TYPE"            => "A",
                "COUNT_ELEMENTS"        => "Y",
                "COUNT_ELEMENTS_FILTER" => "CNT_ACTIVE",
                "FILTER_NAME"           => "sectionsFilter",
                "IBLOCK_ID"             => "5",
                "IBLOCK_TYPE"           => "catalog",
                "SECTION_CODE"          => "",
                "SECTION_FIELDS"        => array("", ""),
                "SECTION_ID"            => $arParentSection['ID'],
                "SECTION_URL"           => "",
                "SECTION_USER_FIELDS"   => array("", ""),
                "SHOW_PARENT_NAME"      => "Y",
                "TOP_DEPTH"             => "3",
                "VIEW_MODE"             => "LINE",
                'USE_FILTER'            => "Y",
            )
        );
    }
}
?>

<div class="inner-footer">
    <div class="container">
        <div class="inner-footer__wrapper">
            <h3 class="caption--h2">Немного <span style="color: inherit !important">о нас</span></h3>

            <p class="inner-footer__description">
                Мы знатоки своего дела. Наша команда надежный ритейлер качественной и невероятно вкусной дикой рыбы и
                отменных северных деликатесов. Став нашими постоянными клиентами, Вы не просто позаботитесь о своем
                здоровье, но и получите экспертную консультацию по выбору и приготовлению продуктов Русского Севера.<br><br>

                Получите настоящее гастрономическое удовольствие! Приходите к нам в гости!
            </p>

            <a href="/about/" class="btn btn--white">Подробнее</a>
        </div>
    </div>
</div>

<script>
    /* $(".smart-filter-parameters-box").each(function (index, el) {
         var nameFilter, paramsFilter;
         nameFilter = ($(this).find(".smart-filter-parameters-box-title-text").text());
         var tmpFilterParams = ($(this).find(".smart-filter-input-group-checkbox-list"));

         console.log(nameFilter);
         tmpFilterParams = ($(tmpFilterParams).find(".form-check"));
         tmpFilterParams.each(function (i, e) {
             console.log($(e).find('.form-check-label').text());
         })
      /!*   $(tmpFilterParams).each(function (i, e){
             console.log($(el));
         })*!/
     });*/

    function setFilter(th) {
        var id = $(th).data('filter-id');
        $("#" + id).trigger('click');
    }

    $("#filter-apply").on("click", function () {
        var startPrice = $("#price-start").data("filter-id");
        var endPrice = $("#price-end").data("filter-id");

        $("#" + startPrice).val($("#price-start").val())
        $("#" + endPrice).val($("#price-end").val())
        $("#" + startPrice).keyup();
        $("#" + endPrice).keyup();


        setInterval(() => {
            $("#set_filter").trigger("click");
        }, 1000);

    });
</script>


<div style="display:none;" itemscope itemtype="http://schema.org/Product">
    <div itemprop="name"><?= $arResult['NAME'] ?></div>
    <div itemtype="http://schema.org/AggregateOffer" itemscope="" itemprop="offers">
        <div><?= $arResult['ELEMENTS_COUNT'] ?> товаров:</div>
        <meta content="<?= $arResult['ELEMENTS_COUNT'] ?>" itemprop="offerCount">
        <div>от <?= $arFilter['VALUES']['MIN']['HTML_VALUE'] ?></div>
        <meta content="<?= $arFilter['VALUES']['MIN']['VALUE'] ?>" itemprop="lowPrice">
        <div>до <?= $arFilter['VALUES']['MAX']['HTML_VALUE'] ?></div>
        <meta content="<?= $arFilter['VALUES']['MAX']['VALUE'] ?>" itemprop="highPrice">
        <meta content="RUB" itemprop="priceCurrency">
    </div>
</div>