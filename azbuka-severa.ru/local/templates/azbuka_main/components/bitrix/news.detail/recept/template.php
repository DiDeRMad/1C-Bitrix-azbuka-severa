<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$themeClass = isset($arParams['TEMPLATE_THEME']) ? ' bx-' . $arParams['TEMPLATE_THEME'] : '';
CUtil::InitJSCore(array('fx'));

//printer($arResult);
$scheme = [];
$scheme['cookTime'] = new DateTime($arResult['TIMESTAMP_X']);
$scheme['cookTime'] = $scheme['cookTime']->format(DateTime::ATOM);
foreach ($arResult['PROPERTIES']['INGREDIENS']['VALUE'] as $key => $INGREDIEN) {
    $scheme['recipeIngredient'][] = $INGREDIEN . ' ' . $arResult['PROPERTIES']['INGREDIENS']['DESCRIPTION'][$key];
}
$scheme['arRecipeIngredient'] = $scheme['recipeIngredient'];
$scheme['recipeIngredient'] = json_encode($scheme['recipeIngredient']);
?>
<div style="display: none;" itemscope itemtype="https://schema.org/Recipe">
    <span itemprop="name"><?= $arResult['NAME']?></span>
    <span itemprop="author">Azbuka-Severa</span>
    <meta itemprop="datePublished" content="<?= $arResult['TIMESTAMP_X']?>"><?= $arResult['TIMESTAMP_X']?>
    <img itemprop="image" src="https://azbuka-severa/<?= $arResult['SCHEMA_PICTURE']?>"
         alt="<?= $arResult['NAME']?>" />
    <meta itemprop="cookTime" content="<?= $scheme['cookTime']?>">
    <span itemprop="recipeYield">3 servings</span>
    <?foreach ($scheme['arRecipeIngredient'] as $arRecept):?>
        <span itemprop="recipeIngredient"><?= $arRecept?></span>
    <?endforeach;?>
    <span itemprop="recipeInstructions">
        <?= $arResult['DETAIL_TEXT']?>
    </span>
</div>
<div class="header-img">
    <div class="header-img__img">
        <picture>
            <? if ($arResult['DETAIL_PICTURE']['SRC_1X_WEBP'] && $arResult['DETAIL_PICTURE']['SRC_2X_WEBP']) ?>
            <source type="image/webp"
                    srcset="<?= $arResult['DETAIL_PICTURE']['SRC_1X_WEBP'] ?> 1x, <?= $arResult['DETAIL_PICTURE']['SRC_2X_WEBP'] ?> 2x">
            <img srcset="<?= $arResult['DETAIL_PICTURE']['SRC_1X'] ?> 1x, <?= $arResult['DETAIL_PICTURE']['SRC_2X'] ?> 2x"
                 src="<?= $arResult['DETAIL_PICTURE']['SRC_1X'] ?>">
        </picture>
    </div>
    <div class="header-img__content">
        <a href="/community/recepties/" class="header-img__btn btn btn--white">Рецепты</a>

        <div class="header-img__info">
            <div class="header-img__date"><?= $arResult["TIMESTAMP_X"] ?></div>
            <h1 class="header-img__title"><?= $arResult['NAME'] ?></h1>
        </div>
    </div>
</div>

<div class="article-recipe">
    <div class="container">
        <div class="article-recipe__wrapper">
            <? if ($arResult['PROPERTIES']['INGREDIENS']['VALUE']): ?>
                <div class="article-recipe-block">
                    <p class="article-recipe-block__title">Ингредиенты</p>
                    <!--<p class="article-recipe-block__description">на 3 порции</p>-->
                    <div class="article-recipe-block__list">
                        <? foreach ($arResult['PROPERTIES']['INGREDIENS']['VALUE'] as $key => $INGREDIEN): ?>
                            <div class="article-recipe-block__row">
                                <span><?= $INGREDIEN ?></span>
                                <span><?= $arResult['PROPERTIES']['INGREDIENS']['DESCRIPTION'][$key] ?></span>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            <? endif ?>

            <div class="article-recipe__content">
                <div class="article-recipe__content-header">
                    <!--<p class="article-recipe__content-title">О рецепте</p>-->
                    <?php
                        $userId = $USER->IsAuthorized() ? $USER->GetID() : 0;
                        $session = $_COOKIE['PHPSESSID'];
                        $favReciepts = \Dev\Helpers\FavoriteRecieptsHelper::getItems($session, $userId)['UF_ITEMS']; ?>
                        <button type="button" class="btn btn--blue add-reciept" data-reciept="<?= $arResult['ID'] ?>" style="margin-bottom:20px;">
                            <span><?= in_array($arResult['ID'], $favReciepts) ? 'Убрать из избранного' : 'Добавить в избранное' ?></span>
                        </button>
                    <p><?= $arResult['DETAIL_TEXT']?></p>
                </div>

                <? if ($arResult['PROPERTIES']['TIME_TO_COOK']['VALUE']): ?>
                <div class="article-recipe-timing">
                    <? if ($arResult['PROPERTIES']['TIME_TO_COOK']['VALUE']): ?>
                        <div class="article-recipe-timing__item">
                            <div class="article-recipe-timing__caption">Время приготовления</div>
                            <div class="article-recipe-timing__info">
                                <svg class="icon--24">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#clock-icon"></use>
                                </svg>
                                <span><?= $arResult['PROPERTIES']['TIME_TO_COOK']['VALUE'] ?></span>
                            </div>
                        </div>
                    <? endif ?>

                    <? if ($arResult['PROPERTIES']['LEVEL']['VALUE_XML_ID']): ?>
                        <div class="article-recipe-timing__item">
                            <div class="article-recipe-timing__caption">уровень
                                сложности
                            </div>
                            <div class="article-recipe-timing__info">
                                <svg class="icon--24">
                                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#level-<?= $arResult['PROPERTIES']['LEVEL']['VALUE_XML_ID'] ?>"></use>
                                </svg>
                                <span><?= $arResult['PROPERTIES']['LEVEL']['VALUE_ENUM'] ?></span>
                            </div>
                        </div>
                    <? endif ?>
                </div>
                <? endif ?>

                <?if (!empty($arResult['STEPS'])):?>
                    <?foreach ($arResult['STEPS'] as $arStep):?>
                        <div class="article-recipe-step">
                            <?if ($arStep['PICTURE']['SRC_1X_WEBP'] && $arStep['PICTURE']['SRC_2X_WEBP'] && $arStep['PICTURE']['SRC_1X_WEBP'] !== "/upload/iblock/da7/da7dbbf0b142e0563400f4d99848165d.jpg" && $arStep['PICTURE']['SRC_1X_WEBP'] !== null) {?>
                            <div class="article-recipe-step__img">
                                <source type="image/webp"
                                        srcset="<?= $arStep['PICTURE']['SRC_1X_WEBP']?> 1x, <?= $arStep['PICTURE']['SRC_2X_WEBP']?> 2x">
                                <img srcset="<?= $arStep['PICTURE']['SRC_1X']?> 1x, <?= $arStep['PICTURE']['SRC_2X']?> 2x"
                                     src="<?= $arStep['PICTURE']['SRC_1X']?>">
                                
                                <div class="article-recipe-step__img-content">
                                    <span><?= $arStep['NAME']?></span>

                                    <?if ($arStep['VIDEO']):?>
                                        <div class="article-recipe-step__img-video">
                                            <a href="<?= $arStep['VIDEO']?>" data-fancybox="" class="about-product__img">
                                                <svg class="icon--48">
                                                    <use xlink:href="<?=SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#video-play"></use>
                                                </svg>
                                            </a>
                                        </div>
                                    <?endif;?>
                                </div>
                            </div>
                            <?php } ?>

                            <p><?=$arStep['TEXT']?></p>
                        </div>

                    <?endforeach;?>
                <?endif?>


            </div>
        </div>
		<?php
			$iblockId = 8; 
			$properties = CIBlockProperty::GetList([], ["IBLOCK_ID" => $iblockId]);
			
			while ($prop = $properties->Fetch()) {
				echo "Свойство: " . $prop["NAME"] . " (Код: " . $prop["CODE"] . ")<br>";
			}
		?>

		<pre><?php print_r($arResult['PROPERTIES']['VIDEO_INSTRUCTION']); ?></pre>


		<?php if (!empty($arResult['PROPERTIES']['VIDEO_INSTRUCTION']['VALUE'])): ?>
			<div class="video-instruction">
				<h3>Видеоинструкция</h3>
				<video width="100%" controls>
					<source src="<?= CFile::GetPath($arResult['PROPERTIES']['VIDEO_INSTRUCTION']['VALUE']); ?>" type="video/mp4">
					Ваш браузер не поддерживает видео.
				</video>
			</div>
		<?php endif; ?>

    </div>
</div>

<?php
global $receptFilter;
$receptFilter['ID'] = $arResult['PROPERTIES']['PRODUCTS']['VALUE'];
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "slider",
    Array(
        "ACTION_VARIABLE" => "action",
        "ADD_PICT_PROP" => "-",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_TO_BASKET_ACTION" => "ADD",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_ADDITIONAL" => "",
        "AJAX_OPTION_HISTORY" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "BACKGROUND_IMAGE" => "-",
        "BASKET_URL" => "/personal/basket.php",
        "BROWSER_TITLE" => "-",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "CACHE_TIME" => "43200",
        "CACHE_TYPE" => "A",
        "COMPATIBLE_MODE" => "Y",
        "CONVERT_CURRENCY" => "N",
        "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
        "DETAIL_URL" => "",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
        "DISPLAY_BOTTOM_PAGER" => "N",
        "DISPLAY_COMPARE" => "N",
        "DISPLAY_TOP_PAGER" => "N",
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_FIELD2" => "id",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_ORDER2" => "desc",
        "ENLARGE_PRODUCT" => "STRICT",
        "FILTER_NAME" => "receptFilter",
        "HIDE_NOT_AVAILABLE" => "N",
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
        "PAGE_ELEMENT_COUNT" => "18",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRICE_CODE" => array("BASE"),
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
        "SECTION_USER_FIELDS" => array("", ""),
        "SEF_MODE" => "N",
        "SET_BROWSER_TITLE" => "N",
        "SET_LAST_MODIFIED" => "N",
        "SET_META_DESCRIPTION" => "N",
        "SET_META_KEYWORDS" => "N",
        "SET_STATUS_404" => "N",
        "SET_TITLE" => "N",
        "SHOW_404" => "N",
        "SHOW_ALL_WO_SECTION" => "N",
        "SHOW_CLOSE_POPUP" => "N",
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
        "TITLE" => "Вам понадобится",
        "CLASS" => 'fresh'
    )
);?>

<?php if (!empty($arResult['SIMILAR'])):?>
    <div class="container">
        <div class="similar-recipe">
            <h2 class="caption--h2">Похожие рецепты</h2>
            <div class="blog-list">
                <?foreach ($arResult['SIMILAR'] as $arSimilar):?>
                    <a href="<?= $arSimilar['DETAIL_PAGE_URL']?>" class="community__item">
                        <div class="community__item-img">
                            <img srcset="<?= $arSimilar['PICTURE']['SRC_1X']?> 1x, <?= $arSimilar['PICTURE']['SRC_2X']?> 2x" src="<?= $arSimilar['PICTURE']['SRC_1X']?>">
                        </div>
                        <div class="community__item-content">
                            <p class="community__item-date"><?= $arSimilar['TIMESTAMP_X']?></p>
                            <p class="community__item-title"><?= $arSimilar['NAME']?></p>
                        </div>
                    </a>
                <?endforeach;?>
            </div>
        </div>
    </div>
<?php endif;?>



<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Recipe",
        "author": "Azbuka-Severa",
        "cookTime": "<?= $scheme['cookTime']?>",
        "datePublished": "<?= $arResult['TIMESTAMP_X']?>",
        "image": "https://azbuka-severa.ru/<?= $arResult['SCHEMA_PICTURE']?>",
        "recipeIngredient": <?= $scheme['recipeIngredient']?>,
        "name": "<?= $arResult['NAME']?>",
        "recipeInstructions": "<?= $arResult['DETAIL_TEXT']?>",
        "recipeYield": "3 servings"
    }
</script>
