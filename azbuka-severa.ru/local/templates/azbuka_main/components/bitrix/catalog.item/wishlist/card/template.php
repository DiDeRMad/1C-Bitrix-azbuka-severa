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
?>

<div class="product-item product-item__id" data-id="<?= $item['ID']?>" id="<?= 'item_' . $item['ID']?>">
    <div href="<?= $item['DETAIL_PAGE_URL']?>" class="product-item__img">
        <a href="<?= $item['DETAIL_PAGE_URL']?>">
            <img loading="lazy" srcset="<?= $item['PREVIEW_PICTURE']['SRC_1X'] ?: SITE_TEMPLATE_PATH . '/assets/img/product-item1.jpg'?> 1x, <?= $item['PREVIEW_PICTURE']['SRC_2X'] ?: SITE_TEMPLATE_PATH . '/assets/img/product-item1@2x.jpg'?> 2x" src="<?= $item['PREVIEW_PICTURE']['SRC_1X'] ?: SITE_TEMPLATE_PATH . '/assets/img/product-item1.jpg'?>" alt="<?php $item['NAME']; ?>">
        </a>
        <button type="button" data-id="<?= $item['ID']?>" id="<?= 'favorite_' . $item['ID']?>" class="product-item__favorite active<?php /*if ($item['IS_FAVORITE'] == 'Y') echo 'active'*/?>" onclick="add2wish(
                '<?=$item["ID"]?>',
                this)"></button>
        <?php if ($item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']):?>
            <span class="product-item__label"><?= $item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']?>%</span>
        <?php endif;?>
    </div>
    <div class="product-item__content">
        <div class="product-item__meta">
            <?php if ($item['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']):?>
                <p class="product-item__price-new"><?= $item['MIN_PRICE']['DISCOUNT_VALUE']?> ₽</p>
                <p class="product-item__price-old"><?= $item['MIN_PRICE']['VALUE']?> ₽</p>
            <?php else:?>
                <p class="product-item__price-now"><?= $item['MIN_PRICE']['VALUE']?> ₽</p>
            <?php endif?>
            <?php if ($item['PROPERTIES']['weight']['VALUE']):?>
                <p class="product-item__options"><?= $item['PROPERTIES']['weight']['VALUE']?> <?=$item['PROPERTIES']['weight']['VALUE_TYPE']?></p>
            <?php endif?>
        </div>
        <a href="<?= $item['DETAIL_PAGE_URL']?>" class="product-item__title"><?= $item['NAME']?></a>
        <div class="product-item__control">
            <?php if ($item['PRODUCT']['QUANTITY'] > 0):?>
                <button data-id="<?= $item['ID']?>" id="<?= $arParams['CLASS'] . '_'?>basket-btn_<?= $item['ID']?>" onclick="addToBasket(
                <?= $item['ID']?>,
                <?= $item['CATALOG_MEASURE_RATIO']?>,
                <?= $item['MIN_PRICE']['DISCOUNT_VALUE']?>)"
                        type="button" class="btn btn--gray product-item__btn-cart active">В корзину</button>
                <div data-id="<?= $item['ID']?>" id="<?= $arParams['CLASS'] . '_'?>basket-btn2_<?= $item['ID']?>" class="product-counter product-item__counter-block">
                    <button type="button" class="product-counter__btn js-product-minus" onclick="changeQuantity(
                    <?= $item['ID']?>,
                    <?= $item['CATALOG_MEASURE_RATIO']?>,
                            'minus'
                            )"></button>
                    <div class="product-counter__all-in">
                        <!-- ед. измерения и шаг -->
                        <input type="hidden" class="js-product-units" value="<?= $item['PROPERTIES']['measureunit']['VALUE']?>">
                        <input type="hidden" class="js-product-step" value="1">
                        <!-- ед. измерения и шаг -->
                        <input data-id="<?= $item['ID']?>" type="hidden" id="<?= $arParams['CLASS'] . '_'?>basket-quan1_<?= $item['ID']?>" class="js-really-quantity" value="<?= $item['CATALOG_MEASURE_RATIO']?>">
                        <input data-id="<?= $item['ID']?>" type="text" id="<?= $arParams['CLASS'] . '_'?>basket-quan2_<?= $item['ID']?>" class="product-counter__input" data-type="<?= $item['PROPERTIES']['measureunit']['VALUE']?>" value="<?= $item['QUANTITY_BASKET'] . ' ' . $item['PROPERTIES']['measureunit']['VALUE']?>" readonly="">
                    </div>
                    <button type="button" class="product-counter__btn js-product-plus" onclick="changeQuantity(
                    <?= $item['ID']?>,
                    <?= $item['CATALOG_MEASURE_RATIO']?>,
                            'plus'
                            )"></button>
                </div>
            <?php else:?>
                <button type="button" class="btn btn--outline-red product-item__btn-order active">Под заказ</button>
            <?php endif?>

        </div>
    </div>
    <button class="product-item__delete-btn" onclick="add2wish(
        <?= $item['ID']?>,
        this
    )">
        <svg class="icon--24">
            <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#close-icon"></use>
        </svg>
    </button>
</div>

<script>
    function add2wish(itemId, th) {
        $.ajax({
            url: '/local/ajax/wishlist.php',
            method: 'post',
            dataType: 'html',
            data: {itemId: itemId},
            success: function(data){
                if (data === 'add')
                    $(th).addClass('active');
                else {
                    $(th).closest(".product-item-container").remove();
                    $(th).removeClass('active');
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
            data: {productId: id, quantity: quantity, price: price},
            success: function(data){

            }
        });

        $.ajax({
            url: '/local/ajax/updatesmallbasket.php',
            method: 'post',
            dataType: 'html',
            async: false,
            data: {},
            success: function(data){
                $("#smallCartPrice").html(data.price + '₽');
                $('.header-bottom__cart').addClass('active');
            }
        });
    }

    function changeQuantity(id, quantity, action) {
		$.ajax({
			url: '/local/ajax/changequantity.php',
			method: 'post',
			dataType: 'json',
			data: { productId: id, quantity: quantity, action: action },
			success: function(state) {
				// Обновляем интерфейс корзины с новым состоянием
				updateCartUI(state);
			},
			error: function(error) {
				console.error('Ошибка при изменении количества:', error);
			}
		});
	
		$.ajax({
			url: '/local/ajax/updatesmallbasket.php',
			method: 'post',
			dataType: 'text', // Ожидаем итоговую сумму
			success: function(totalPrice) {
				$("#smallCartPrice").html(totalPrice + ' ₽');
			},
			error: function(error) {
				console.error('Ошибка при обновлении корзины:', error);
			}
		});
	}

</script>