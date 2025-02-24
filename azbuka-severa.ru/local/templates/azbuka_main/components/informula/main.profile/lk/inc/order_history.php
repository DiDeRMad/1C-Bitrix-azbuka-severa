<?php
use Bitrix\Main,
    Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Loader as Loader,
    Bitrix\Main\Config\Option as Option,
    Bitrix\Sale\Delivery as Delivery,
    Bitrix\Sale\PaySystem as PaySystem,
    Bitrix\Sale\Basket as Basket,
    Bitrix\Sale as Sale,
    Bitrix\Sale\Order as Order,
    Bitrix\Sale\DiscountCouponsManager as DiscountCouponsManager,
    Bitrix\Main\Context as Context;

if (!empty($arProfileBlock)) { ?>
    <?php foreach ($arProfileBlock as $arOrder) {
        $repeat = false;
       // echo '<pre>'; print_r($arOrder); echo '</pre>'; ?>
        <div class="lk-order-item" data-id="<?= $arOrder['ID'] ?>">
            <div class="lk-order-item__date"><?= FormatDate('d F Y', MakeTimeStamp($arOrder['DATE_INSERT'])) ?></div>
            <div class="product-list row">
                <?php foreach ($arOrder['BASKET'] as $arItem) {
                    if (!$arItem['CATITEM']['DETAIL_PAGE_URL']) continue;
                    $image           = CFile::GetFileArray($arItem['CATITEM']['DETAIL_PICTURE']);
                    $imageWebp       = preg_replace('/(png|jpg|jpeg|PNG|JPG|JPEG)/', 'webp', $image['SRC']);
                    $imageResize     = CFile::ResizeImageGet($arItem['CATITEM']['DETAIL_PICTURE'], ['width' => '303', 'height' => '303'], BX_RESIZE_IMAGE_PROPORTIONAL, false);
                    $imageResizeWebp = preg_replace('/(png|jpg|jpeg|PNG|JPG|JPEG)/', 'webp', $imageResize['src']); ?>
                    <div class="product-list__item-wrapper col-6 col-md-auto">
                        <div class="product-list__item">
                            <div class="product-list__img-wrapper">
                                <a class="product-list__img-link"
                                   href="<?= $arItem['CATITEM']['DETAIL_PAGE_URL'] ?>">
                                    <picture>
                                        <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imageWebp) && file_exists($_SERVER['DOCUMENT_ROOT'] . $imageResizeWebp)) { ?>
                                            <source srcset="<?= $imageResizeWebp ?> 1x, <?= $imageWebp ?> 2x"
                                                    type="image/webp">
                                        <?php } ?>
                                        <img class="product-list__img"
                                             data-src="<?= $imageResize['src'] ?>"
                                             srcset="<?= $imageResize['src'] ?> 1x, <?= $image['src'] ?> 2x"
                                             src="<?= $imageResize['src'] ?>">
                                    </picture>
                                </a>
                                <?php if ($arItem['CATITEM']['CATALOG_REALPRICE'] != $arItem['CATITEM']['CATALOG_PRICE_1']) { ?>
                                    <div class="product-list__discount">
                                        -<?= 100 - ceil($arItem['CATITEM']['CATALOG_REALPRICE'] / $arItem['CATITEM']['CATITEM']['CATALOG_PRICE_1'] * 100) ?>
                                        %
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="product-list__content">
                                <div class="product-list__options"><?= round($arItem['CATITEM']['CATALOG_WEIGHT'] / 1000, 2) ?>кг
                                </div>
                                <div class="product-list__price-wrapper">
                                    <span class="product-list__price"><?= $arItem['CATITEM']['CATALOG_REALPRICE'] ?> ₽</span>
                                    <?php if ($arItem['CATITEM']['CATALOG_REALPRICE'] != $arItem['CATITEM']['CATALOG_PRICE_1']) { ?>
                                        <span class="product-list__price-old"><?= $arItem['CATITEM']['CATALOG_PRICE_1'] ?> ₽</span>
                                    <?php } ?>
                                </div>
                                <a href="<?= $arItem['CATITEM']['DETAIL_PAGE_URL'] ?>"
                                   class="product-list__name"><?= $arItem['NAME'] ?></a>
                            </div>
                            <?php if ($arItem['CATITEM']['ACTIVE'] == 'Y' && $arItem['CATITEM']['CATALOG_CAN_BUY_1']) {
                                $repeat = true;?>
                            <button data-id="<?= $arItem['PRODUCT_ID'] ?>" id="_basket-btn_<?= $arItem['PRODUCT_ID'] ?>"
                                    onclick="addToBasket(<?= $arItem['PRODUCT_ID'] ?>,<?= $arItem['CATITEM']['CATALOG_RATIO'] ?>,<?= $arItem['CATITEM']['CATALOG_REALPRICE'] ?>)" type="button"
                                    class="product-list__btn btn btn--blue"></button>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if ($repeat) { ?>
                <button type="button" class="lk-order-item__btn btn btn--blue btn_icon_refresh js-order-repeat">Повторить заказ</button>
            <?php } ?>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="form-group">
        <div class="form-text form-text_lg">В списке пока нет ни одного заказа</div>
    </div>
    <a href="/catalog/" class="btn btn--blue">Перейти в каталог</a>
<?php } ?>