<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $mobileColumns
 * @var array $arParams
 * @var string $templateFolder
 */

$usePriceInAdditionalColumn = in_array('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$useSumColumn = in_array('SUM', $arParams['COLUMNS_LIST']);
$useActionColumn = in_array('DELETE', $arParams['COLUMNS_LIST']);

$restoreColSpan = 2 + $usePriceInAdditionalColumn + $useSumColumn + $useActionColumn;

$positionClassMap = array(
    'left' => 'basket-item-label-left',
    'center' => 'basket-item-label-center',
    'right' => 'basket-item-label-right',
    'bottom' => 'basket-item-label-bottom',
    'middle' => 'basket-item-label-middle',
    'top' => 'basket-item-label-top'
);

$discountPositionClass = '';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION'])) {
    foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos) {
        $discountPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
}

$labelPositionClass = '';
if (!empty($arParams['LABEL_PROP_POSITION'])) {
    foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos) {
        $labelPositionClass .= isset($positionClassMap[$pos]) ? ' ' . $positionClassMap[$pos] : '';
    }
}

$uid = uniqid();
?>
<script id="basket-item-template" type="text/html">


        {{^SHOW_RESTORE}}
        <div class="product-item" id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
            <div href="" class="product-item__img">
                {{#DETAIL_PAGE_URL}}
                <a href="{{DETAIL_PAGE_URL}}">
                    <img srcset="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?= $templateFolder ?>/images/no_photo.png{{/IMAGE_URL}} 1x, {{{IMAGE_URL}}}{{^IMAGE_URL}}<?= $templateFolder ?>/images/no_photo.png{{/IMAGE_URL}} 2x"
                         src="{{{IMAGE_URL}}}{{^IMAGE_URL}}<?= $templateFolder ?>/images/no_photo.png{{/IMAGE_URL}}">
                </a>
                {{/DETAIL_PAGE_URL}}
                <button
                    data-entity="basket-item_favorite"
                    type="button"
                    class="product-item__favorite"
                    data-id="{{PRODUCT_ID}}"
                    id="favorite_{{PRODUCT_ID}}_<?=$uid?>"
                    onclick="add2wish({{PRODUCT_ID}},this)"
                ></button>
                {{#SHOW_DISCOUNT_PRICE}}
                <span class="product-item__label">{{DISCOUNT_PRICE_PERCENT_FORMATED}}</span>
                {{/SHOW_DISCOUNT_PRICE}}
            </div>
            <div class="basket-item-block-info product-item__content">
                <div class="product-item__meta">
                    {{#SHOW_DISCOUNT_PRICE}}
                    <p id="basket-item-price-{{ID}}" class="product-item__price-new basket-item-price-current">{{{PRICE_WITHOUT_RATIO}}}</p>
                    <p class="product-item__price-old basket-item-price-old">{{{FULL_PRICE_FORMATED}}}</p>
                    {{/SHOW_DISCOUNT_PRICE}}

                    {{#NOT_SHOW_DISCOUNT_PRICE}}
                    <p id="basket-item-price-{{ID}}" class="product-item__price-new basket-item-price-current">{{{PRICE_WITHOUT_RATIO}}}</p>
                    {{/NOT_SHOW_DISCOUNT_PRICE}}
                    <p class="product-item__options">{{IIKO_WEIGHT}} {{IIKO_WEIGHT_TYPE}}</p>
                </div>
                <a href="{{DETAIL_PAGE_URL}}" class="product-item__title">{{NAME}}</a>

                <div class="product-item__control">
                    <div class="product-item__cart-total">
                        <p>Итого</p>
                        <p class="product-item__cart-total-price js-product-cart-total">{{{SUM_PRICE_FORMATED}}}</p>
                        <!-- дополнительно передать сюда цену -->
                        <input type="hidden" class="js-product-price" value="{{{PRICE}}}">
                    </div>
                    <div class="product-counter product-item__counter-block basket-items-list-item-amount" data-entity="basket-item-quantity-block">
                        <button type="button"
                                class="product-counter__btn js-product-minus" data-entity="basket-item-quantity-minus"></button>
                        <div class="product-counter__all-in">
                            <!-- ед. измерения и шаг -->
                            <input type="hidden" class="js-product-units" value="{{MEASURE_UNIT}}">
                            <input type="hidden" class="js-product-step" value="{{MEASURE_RATIO}}">
                            <!-- ед. измерения и шаг -->
                            <input type="hidden" class="js-really-quantity" value="{{QUANTITY_JS}}">
                            <input type="text" data-value="{{QUANTITY}}" data-entity="basket-item-quantity-field"
                                   id="basket-item-quantity-{{ID}}" class="product-counter__input" value="{{QUANTITY}} {{WEIGHT_VALUE_TYPE}}"
                                   readonly="">
                        </div>
                        <button data-entity="basket-item-quantity-plus" type="button" class="product-counter__btn js-product-plus"></button>
                    </div>
                </div>
                {{#NOT_AVAILABLE}}
                <div class="basket-items-list-item-warning-container">
                    <div class="alert alert-warning text-center">
                        <?=Loc::getMessage('SBB_BASKET_ITEM_NOT_AVAILABLE')?>
                    </div>
                </div>
                {{/NOT_AVAILABLE}}
            </div>
            {{#SHOW_LOADING}}
            <div class="basket-items-list-item-overlay"></div>
            {{/SHOW_LOADING}}




        <button data-entity="basket-item-delete" class="product-item__delete-btn">
            <svg class="icon--24">
                <use xlink:href="<?=SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#close-icon"></use>
            </svg>
        </button>
        {{/SHOW_RESTORE}}
    </div>
</script>
