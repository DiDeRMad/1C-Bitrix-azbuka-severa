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

$navParams = array(
    'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
    'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
    'NavNum' => $arResult['NAV_RESULT']->NavNum
);
$showed = 0;

if ($arParams['PAGE_ELEMENT_COUNT'] <= $arResult['ELEMENTS_COUNT']) {
    if ($arParams['PAGE_ELEMENT_COUNT'] * $navParams['NavPageNomer'] < $arResult['ELEMENTS_COUNT']) {
        $percent = round($arParams['PAGE_ELEMENT_COUNT'] * $navParams['NavPageNomer'] / $arResult['ELEMENTS_COUNT'] * 100);
        $showed = $arParams['PAGE_ELEMENT_COUNT'] * $navParams['NavPageNomer'];
    } else {
        $percent = 100;
        $showed = $arResult['ELEMENTS_COUNT'];
    }
} else {
    $percent = round($arResult['ELEMENTS_COUNT'] * $navParams['NavPageNomer'] / $arResult['ELEMENTS_COUNT'] * 100);
    $showed = $arResult['ELEMENTS_COUNT'] * $navParams['NavPageNomer'];
}


$priceFilterValue = [];
$curPage = $APPLICATION->GetCurPage();
$arFilterParams = [];

$tmpPage = explode('filter', $curPage)[1];
$tmpPage = explode('/', $tmpPage);


foreach ($superFilter as $arFilter) {
    if (stripos($curPage, strtolower($arFilter['CODE'])) !== false) {
        if ($arFilter['CODE'] == 'COST_DISCOUNT') {
            $arTmp = [];
            $arTmp['NAME'] = 'Цена: от ' . $arFilter['VALUES']['MIN']['HTML_VALUE'] . ' до ' . $arFilter['VALUES']['MAX']['HTML_VALUE'];
            foreach ($tmpPage as $item) {
                if (stripos($item, $arFilter['CODE']) !== false)
                    $arTmp['FILTER'] = '/' . $item;
            }
            $arFilterParams[] = $arTmp;
        } else {
            foreach ($arFilter['VALUES'] as $arValue) {
                if ($arValue['CHECKED']) {
                    $arTmp = [];
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

$totalCount = $_SESSION['SEARCH_TOTAL_COUNT'] ?? 0;
$searchResultIds = $_SESSION['SEARCH_RESULT_IDS'] ?? array();

// Модифицируем $searchFilter, чтобы использовать все найденные ID
if (!empty($searchResultIds) && is_array($searchResultIds)) {
    $searchFilter = array(
        "ID" => $searchResultIds,
    );
}


?>
<div class="search-page-header">
    <div class="search-page-header__info">
        <span>По запросу</span>
        <a href="#!"><?= $_REQUEST['q']?></a>
        <span>найдено <?= num_word($totalCount, ['товар', 'товара', 'товаров']) ?> </span>
    </div>
    <div class="sort-block">
        <div class="sort-type">
            <p class="sort-type__title">Сортировать:</p>
            <div class="sort-type__wrapper">
                <label for="types1" class="sort-type__label">
                    <input type="radio" class="sort_catalog"
                           data-link="<?= $APPLICATION->GetCurPage() ?>?sort=price&order=asc&q=<?= $_REQUEST['q']?>" name="filter-name"
                           id="types1"
                           value="Сначала дешевые" <?php if ($_REQUEST['sort'] == 'price' && $_REQUEST['order'] == 'asc') echo 'checked' ?>>
                    <span>Сначала дешевые</span>
                </label>
                <label for="types2" class="sort-type__label">
                    <input type="radio" class="sort_catalog"
                           data-link="<?= $APPLICATION->GetCurPage() ?>?sort=price&order=desc&q=<?= $_REQUEST['q']?>" name="filter-name"
                           id="types2"
                           value="Сначала дорогие" <?php if ($_REQUEST['sort'] == 'price' && $_REQUEST['order'] == 'desc') echo 'checked' ?>>
                    <span>Сначала дорогие</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!--  нужно удалить disabled по клику на "загрузить еще"  -->
<div class="catalog-list disabled">

    <?php foreach ($arResult['ITEMS'] as $arItem): ?>


        <?php

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
                ),

            ),
            $component,
            array('HIDE_ICONS' => 'Y')
        );
        ?>


    <?php endforeach; ?>

    <div class="bs-wide">
        <div class="bs-wide__img">
            <img srcset="<?= SITE_TEMPLATE_PATH ?>/assets/img/bs-wide.jpg 1x, <?= SITE_TEMPLATE_PATH ?>/assets/img/bs-wide@2x.jpg 2x"
                 src="<?= SITE_TEMPLATE_PATH ?>/assets/img/bs-wide.jpg">
        </div>
        <div class="bs-wide__content">
            <div class="bs-wide__info">
                <p class="bs-wide__title">Прямо из коптильни</p>
                <p class="bs-wide__description">При оформлении заказа выберите опцию доставки «Блиц» и мы тут же
                    свяжемся с вами</p>
            </div>

            <div class="bs-wide__form">
                <input type="tel" class="input bs-wide__form-input" id="cat-koptil-phone"
                      name="test" placeholder="+7 999 999-99-99">
                <button id="cat-koptil-btn" class="btn btn--blue">Отправить</button>
            </div>
        </div>
    </div>
</div>

<div class="catalog-list-paginator">
        
    <div class="catalog-list-paginator__counter">
        Просмотрено <?= min($showed, $totalCount) ?> из <?= $totalCount ?>
    </div>
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
                        <sv g class="paging__icon" viewBox="0 0 45 32">
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
                        <sv g class="paging__icon" viewBox="0 0 45 32">
                            <path d="M25.030 26.406c-0.665 0.59-1.082 1.446-1.082 2.4 0 1.769 1.434 3.203 3.203 3.203 0.815 0 1.559-0.305 2.125-0.806l-0.003 0.003 14.448-12.8c0.666-0.589 1.083-1.446 1.083-2.4s-0.418-1.811-1.080-2.397l-0.003-0.003-14.448-12.8c-0.562-0.499-1.306-0.803-2.122-0.803-1.769 0-3.203 1.434-3.203 3.203 0 0.954 0.417 1.81 1.078 2.397l0.003 0.003 8.131 7.194h-29.962c-1.767 0-3.2 1.433-3.2 3.2s1.433 3.2 3.2 3.2v0h29.962z"/>
                        </svg>
                    </a>
                    <?}?>
                </nav>
            </div>
        <??>
    </div>



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

    })

</script>

<?php if (stripos($_SERVER['REQUEST_URI'], 'PAGEN') !== false): ?>
    <!-- <script>
        $(document).ready(function () {
            var toScroll = $('#last_item').offset().top - 150;
            console.log(toScroll);
            $('html, body').animate({
                    scrollTop: toScroll,
                },
                1000,
            );
        })
    </script> -->

<?php endif ?>


