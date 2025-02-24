<?php if (!empty($arProfileBlock)) { ?>
    <div class="lk-order-item" id="lk-wishlist">
        <div class="form-group">
            <a class="link-default" href="javascript:void(0);" onclick="removeAllFromWish();">Очистить все</a>
        </div>
        <div class="product-list row">
            <?php foreach ($arProfileBlock as $arFav) {
                //echo '<pre>';  print_r($arFav); echo '</pre>';
                $image           = CFile::GetFileArray($arFav['DETAIL_PICTURE']);
                $imageWebp       = preg_replace('/(png|jpg|jpeg|PNG|JPG|JPEG)/', 'webp', $image['SRC']);
                $imageResize     = CFile::ResizeImageGet($arFav['DETAIL_PICTURE'], ['width' => '303', 'height' => '303'], BX_RESIZE_IMAGE_PROPORTIONAL, false);
                $imageResizeWebp = preg_replace('/(png|jpg|jpeg|PNG|JPG|JPEG)/', 'webp', $imageResize['src']); ?>
                <div class="product-list__item-wrapper col-6 col-md-auto">
                    <div class="product-list__item">
                        <div class="product-list__img-wrapper">
                            <a class="product-list__img-link"
                               href="<?= $arFav['DETAIL_PAGE_URL'] ?>">
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
                            <?php if ($arFav['CATALOG_REALPRICE'] != $arFav['CATALOG_PRICE_1']) { ?>
                                <div class="product-list__discount">-<?= 100 - ceil($arFav['CATALOG_REALPRICE'] / $arFav['CATALOG_PRICE_1'] * 100) ?>%</div>
                            <?php } ?>
                            <button type="button" data-id="<?= $arFav['ID'] ?>" id="favorite_<?= $arFav['ID'] ?>"
                                    class="product-list__favorite active"
                                    onclick="add2wish('<?= $arFav['ID'] ?>',this)"></button>
                        </div>
                        <div class="product-list__content">
                            <div class="product-list__options"><?= round($arFav['CATALOG_WEIGHT'] / 1000, 2) ?> кг</div>
                            <div class="product-list__price-wrapper">
                                <span class="product-list__price"><?= $arFav['CATALOG_REALPRICE'] ?> ₽</span>
                                <?php if ($arFav['CATALOG_REALPRICE'] != $arFav['CATALOG_PRICE_1']) { ?>
                                    <span class="product-list__price-old"><?= $arFav['CATALOG_PRICE_1'] ?> ₽</span>
                                <?php } ?>
                            </div>
                            <a href="<?= $arFav['DETAIL_PAGE_URL'] ?>"
                               class="product-list__name"><?= $arFav['NAME'] ?></a>
                        </div>
                        <button data-id="<?= $arFav['ID'] ?>" id="_basket-btn_<?= $arFav['ID'] ?>" onclick="addToBasket(<?= $arFav['ID'] ?>,<?= (float) $arFav['CATALOG_RATIO'] ?>,<?= $arFav['CATALOG_REALPRICE'] ?>)" type="button"
                                class="product-list__btn btn btn--blue"></button>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button type="button" class="lk-order-item__btn btn btn--blue">Оформить заказ</button>
    </div>
<?php } else { ?>
    <div class="form-group">
        <div class="form-text form-text_lg">В списке пока нет ни одного избранного товара</div>
    </div>
    <a href="/catalog/" class="btn btn--blue">Перейти в каталог</a>
<?php } ?>