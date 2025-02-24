<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?php
//printer($arResult);
?>

<div class="header-bottom-menu">
	<div class="header-category">
		<div class="header-category-menu">
			<? foreach ($arResult as $section => $arItem): ?>
				<? if (!$arItem['UF_SHOWMENU']): ?>
					<? if (!empty($arItem['CHILDS'])): ?>
						<div data-category="lvl1_<?= $section ?>"
							class="header-category-menu__item header-category-menu__item">
							<a href="<?= $arItem['SECTION_PAGE_URL'] ?>"
								class="header-category-menu__link"><?= $arItem['NAME'] ?></a>
							<button type="button" class="header-category-menu__item-arrow"></button>
						</div>
					<? else: ?>
						<div class="header-category-menu__item">
							<a href="<?= $arItem['SECTION_PAGE_URL'] ?>"
								class="header-category-menu__link"><?= $arItem['NAME'] ?></a>
							<button type="button" class="header-category-menu__item-arrow"></button>
						</div>
					<? endif; ?>
				<? endif; ?>
			<? endforeach; ?>
		</div>
		<? foreach ($arResult as $section => $arItem): ?>
			<div class="subcategory-menu" data-category-id="lvl1_<?= $section ?>">
				<div class="subcategory-menu__block">
					<div class="subcategory-menu__block-title-wrapper">
						<span class="category-back-btn"></span>
						<a href="<?= $arItem['SECTION_PAGE_URL'] ?>" class="subcategory-menu__block-title"><?= $arItem['NAME'] ?></a>
					</div>
					<div class="subcategory-menu__block-wrapper">
						<div class="subcategory-menu__menu">
							<? foreach ($arItem['CHILDS'] as $CHILD => $arChild): ?>
								<? if (!empty($arChild['TYPES'])): ?>
									<div class="subcategory-menu__item"
										data-sub-category="lvl1_<?= $section . '_' . $CHILD ?>">
										<a href="<?= $arChild['SECTION_PAGE_URL'] ?>"><?= $arChild['NAME'] ?></a>
										<span class="subcategory-to-btn"></span>
									</div>
								<? else: ?>
									<div class="subcategory-menu__item">
										<a href="<?= $arChild['SECTION_PAGE_URL'] ?>"><?= $arChild['NAME'] ?></a>
									</div>
								<? endif; ?>
							<? endforeach; ?>
						</div>
						<? foreach ($arItem['CHILDS'] as $CHILD => $arChild): ?>


							<div class="subcategory-menu__submenu"
								data-sub-category-id="lvl1_<?= $section . '_' . $CHILD ?>">
								<div class="subcategory-menu__submenu-title-wrapper">
									<span class="subcategory-back-btn"></span>
									<a class="subcategory-menu__submenu-title"
										href="<?= $arChild['SECTION_PAGE_URL'] ?>"><?= $arChild['NAME'] ?></a>
								</div>

								<? foreach ($arChild['TYPES'] as $arType): ?>
									<div class="subcategory-menu__submenu-item">
										<a href="<?= $arChild['SECTION_PAGE_URL'] . $arType['LINK'] ?>"
											class="subcategory-menu__submenu-link"><?= $arType['NAME'] ?></a>
									</div>
								<? endforeach; ?>
							</div>

						<? endforeach; ?>
					</div>
				</div>
				<div class="header-category-extra">
					<div class="header-category-extra__header">
						<a href="<?= $arItem['ITEMS']['LINK'] ?>" class="subcategory-menu__block-title"><?= $arItem['ITEMS']['NAME'] ?></a>
						<?/*<a href="#!" class="btn btn--outline btn--outline-carret">Еще 14 товаров</a>*/ ?>
					</div>
					<div class="header-category-extra__product-list">

						<?php
						global $arrFilter;
						$arrFilter['ID'] = $arItem['ITEMS']['ITEMS'] ?: false;
						?>

						<?php $APPLICATION->IncludeComponent(
							"bitrix:catalog.section",
							"menu",
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
								"BASKET_URL" => "/personal/basket.php",
								"BROWSER_TITLE" => "-",
								"CACHE_FILTER" => "Y",
								"CACHE_GROUPS" => "Y",
								"CACHE_TIME" => "36000000",
								"CACHE_TYPE" => "A",
								"COMPATIBLE_MODE" => "Y",
								"CONVERT_CURRENCY" => "N",
								"CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
								"DETAIL_URL" => "",
								"DISABLE_INIT_JS_IN_COMPONENT" => "N",
								"DISPLAY_BOTTOM_PAGER" => "Y",
								"DISPLAY_COMPARE" => "N",
								"DISPLAY_TOP_PAGER" => "N",
								"ELEMENT_SORT_FIELD" => "sort",
								"ELEMENT_SORT_FIELD2" => "id",
								"ELEMENT_SORT_ORDER" => "asc",
								"ELEMENT_SORT_ORDER2" => "desc",
								"ENLARGE_PRODUCT" => "STRICT",
								"FILTER_NAME" => "arrFilter",
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
								"PRICE_CODE" => array('BASE'),
								"PRICE_VAT_INCLUDE" => "Y",
								"PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
								"PRODUCT_ID_VARIABLE" => "id",
								"PRODUCT_PROPS_VARIABLE" => "prop",
								"PRODUCT_QUANTITY_VARIABLE" => "quantity",
								"PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false}]",
								"PRODUCT_SUBSCRIPTION" => "N",
								"RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
								"RCM_TYPE" => "personal",
								"SECTION_CODE" => "",
								"SECTION_CODE_PATH" => "",
								"SECTION_ID" => $_REQUEST["SECTION_ID"],
								"SECTION_ID_VARIABLE" => "SECTION_ID",
								"SECTION_URL" => "",
								"SECTION_USER_FIELDS" => array("", ""),
								"SEF_MODE" => "N",
								"SEF_RULE" => "",
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
								"SHOW_SLIDER" => "N",
								"SLIDER_INTERVAL" => "3000",
								"SLIDER_PROGRESS" => "N",
								"TEMPLATE_THEME" => "blue",
								"USE_ENHANCED_ECOMMERCE" => "N",
								"USE_MAIN_ELEMENT_SECTION" => "N",
								"USE_PRICE_COUNT" => "N",
								"USE_PRODUCT_QUANTITY" => "N"
							)
						); ?>
					</div>
				</div>
			</div>
		<? endforeach; ?>
	</div>
</div>