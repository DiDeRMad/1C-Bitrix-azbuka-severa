<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $item
 * @var array $actualItem
 * @var array $minOffer
 * @var array $itemIds
 * @var array $price
 * @var array $measureRatio
 * @var bool $haveOffers
 * @var bool $showSubscribe
 * @var array $morePhoto
 * @var bool $showSlider
 * @var bool $itemHasDetailUrl
 * @var string $imgTitle
 * @var string $productTitle
 * @var string $buttonSizeClass
 * @var CatalogSectionComponent $component
 */
$uid = uniqid();
?>

<div <?php if ($item['IS_LAST'] == 'Y') echo 'id="last_item"' ?> <?php /*href="<?= $item['DETAIL_PAGE_URL']?>"*/ ?> class="product-item__img">
	<a href="<?= $item['DETAIL_PAGE_URL'] ?>">
		<img loading="lazy" srcset="<?= $item['PREVIEW_PICTURE']['SRC_WEBP_1X'] ?: SITE_TEMPLATE_PATH . '/assets/img/product-item1.jpg' ?> 1x, <?= $item['PREVIEW_PICTURE']['SRC_WEBP_2X'] ?: SITE_TEMPLATE_PATH . '/assets/img/product-item1@2x.jpg' ?> 2x" src="<?= $item['PREVIEW_PICTURE']['SRC_WEBP_1X'] ?>" alt="<?= $item['NAME'] ?>">
	</a>
	<button type="button" data-id="<?= $item['ID'] ?>" id="<?= 'favorite_' . $item['ID'] . '_' . $uid ?>" class="product-item__favorite <?php /*if ($item['IS_FAVORITE'] == 'Y') echo 'active'*/ ?>" onclick="add2wish(
                '<?= $item["ID"] ?>',
                this)"></button>
	<?php if ($item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']) : ?>
		<span class="product-item__label"><?= $item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE'] ?>%</span>
	<?php endif; ?>
	<?php if ($item['PROPERTIES']['HIT']['VALUE']) : ?>
		<span class="product-item__label product-item__label-hit" <?= $item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE'] ? 'style="bottom:35px;"' : '' ?>>Хит продаж</span>
	<?php endif; ?>
</div>
<div class="product-item__content">
	<div class="product-item__meta">
		<?php if ($item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']) : ?>
			<p class="product-item__price-new"><?= $item['MIN_PRICE']['DISCOUNT_VALUE'] ?> ₽</p>
			<p class="product-item__price-old"><?= $item['MIN_PRICE']['VALUE'] ?> ₽</p>
		<?php else : ?>
			<p class="product-item__price-now"><?= $item['ITEM_PRICES'][0]['PRICE'] ?> ₽</p>
		<?php endif ?>
		<?php if ($item['PROPERTIES']['weight']['VALUE']) : ?>
			<p class="product-item__options"><?= $item['PROPERTIES']['weight']['VALUE'] ?> <?= $item['PROPERTIES']['weight']['VALUE_TYPE'] ?></p>
		<?php endif ?>
	</div>
	<a href="<?= $item['DETAIL_PAGE_URL'] ?>" class="product-item__title"><?= $item['NAME'] ?></a>
	<?
	$measureData = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($item['ID']);
	?>
	<div class="product-item__control">
		<?php if ($item['PRODUCT']['QUANTITY'] > 0) : ?>
			<button data-id="<?= $item['ID'] ?>" id="<?= $arParams['CLASS'] . '_' ?>basket-btn_<?= $item['ID'] . '_' . $uid ?>" onclick="addToBasket(
                <?= $item['ID'] ?>,
                <?= $measureData[$item['ID']] ?>,
                <?= $item['MIN_PRICE']['DISCOUNT_VALUE'] ?>)" type="button" class="btn btn--gray product-item__btn-cart active">В корзину</button>
			<div data-id="<?= $item['ID'] ?>" id="<?= $arParams['CLASS'] . '_' ?>basket-btn2_<?= $item['ID'] . '_' . $uid ?>" class="product-counter product-item__counter-block">
				<button type="button" class="product-counter__btn js-product-minus" onclick="changeQuantity(
                    <?= $item['ID'] ?>,
                    <?= $measureData[$item['ID']] ?>,
                            'minus'
                            )"></button>
				<div class="product-counter__all-in">
					<!-- ед. измерения и шаг -->
					<input type="hidden" class="js-product-units" value="<?= $item['PROPERTIES']['measureunit']['VALUE'] ?>">
					<input type="hidden" class="js-product-step" value="<?= $measureData[$item['ID']] ?>">
					<!-- ед. измерения и шаг -->
					<input data-id="<?= $item['ID'] ?>" type="hidden" id="<?= $arParams['CLASS'] . '_' ?>basket-quan1_<?= $item['ID']  . '_' . $uid ?>" class="js-really-quantity" value="1">
					<input data-id="<?= $item['ID'] ?>" type="text" id="<?= $arParams['CLASS'] . '_' ?>basket-quan2_<?= $item['ID']  . '_' . $uid ?>" class="product-counter__input" data-type="<?= $item['PROPERTIES']['measureunit']['VALUE'] ?>" value="<?= $item['QUANTITY_BASKET'] . ' ' . $item['PROPERTIES']['measureunit']['VALUE'] ?>" readonly="">
				</div>
				<button type="button" class="product-counter__btn js-product-plus" onclick="changeQuantity(
                    <?= $item['ID'] ?>,
                    <?= $measureData[$item['ID']] ?>,
                            'plus'
                            )"></button>
			</div>
		<?php else : ?>
			<button type="button" data-modal="true" onclick="$('#send-item').val(<?= $item['ID'] ?>)" data-modal-id="#modal-item-buy" class="btn btn--outline-red product-item__btn-order active">Под заказ</button>
		<?php endif ?>
	</div>
</div>


<script>
	function add2wish(itemId, th) {
		$.ajax({
			url: '/local/ajax/wishlist.php',
			method: 'post',
			dataType: 'html',
			data: {
				itemId: itemId
			},
			success: function(data) {
				var countWish = $('.header-bottom__favorite').data('count');
				if (data === 'add') {
					$(th).addClass('active');
					$('.header-bottom__favorite').addClass('active');
					countWish++;
					$('.header-bottom__favorite').data('count', countWish);
				} else {
					$(th).removeClass('active');
					countWish--;
					$('.header-bottom__favorite').data('count', countWish);
					if (countWish <= 0) {
						$('.header-bottom__favorite').removeClass('active');
					}
				}
			}
		});
	};

	function addToBasket(id, quantity, price) {
		$.ajax({
			url: '/local/ajax/addbasketitem.php',
			method: 'post',
			dataType: 'html',
			async: false,
			data: {
				productId: id,
				quantity: quantity,
				price: price
			},
			success: function(data) {

			}
		});
в
		$.ajax({
			url: '/local/ajax/updatesmallbasket.php',
			method: 'post',
			dataType: 'html',
			async: false,
			data: {},
			success: function(data) {
				$("#smallCartPrice").html(data + '₽');
				$('.header-bottom__cart').addClass('active');
			}
		});
	}

	function changeQuantity(id, quantity, action) {
		$.ajax({
			url: '/local/ajax/changequantity.php',
			method: 'post',
			dataType: 'html',
			async: false,
			data: {
				productId: id,
				quantity: quantity,
				action: action
			},
			success: function(data) {

			}
		});

		$.ajax({
			url: '/local/ajax/updatesmallbasket.php',
			method: 'post',
			dataType: 'html',
			async: false,
			data: {},
			success: function(data) {
				$("#smallCartPrice").html(data + '₽');
				if (data > 0) {
					$('.header-bottom__cart').addClass('active');
				} else {
					$('.header-bottom__cart').removeClass('active');
				}
			}
		});
	}
</script>