<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

global $USER;

/**
 * @var array $arParams
 */
?>
<script id="basket-total-template" type="text/html">
    <div class="cart__sidebar cart-sidebar">
        <div class="cart-sidebar__price-wrapper">
            <div>

                    <p class="cart-sidebar__price-description">Стоимость</p>
                {{#DISCOUNT_PRICE_FORMATED}}
                <div class="basket-coupon-block-total-price-old">

                    {{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}

                </div>
                {{/DISCOUNT_PRICE_FORMATED}}
                    <p data-entity="basket-total-price" class="cart-sidebar__price"> {{{PRICE_FORMATED}}}</p>
                {{#DISCOUNT_PRICE_FORMATED}}
                <!-- <div class="basket-coupon-block-total-price-difference">
                    <?=Loc::getMessage('SBB_BASKET_ITEM_ECONOMY')?>
                    <span style="white-space: nowrap;">{{{DISCOUNT_PRICE_FORMATED}}}</span>
                </div> -->
                {{/DISCOUNT_PRICE_FORMATED}}
               
            </div>
            <button type="button">
                <svg class="icon--24">
                    <use xlink:href="<?= SITE_TEMPLATE_PATH?>/assets/img/sprite.svg#caret-down-icon"></use>
                </svg>
            </button>
        </div>

        <div class="cart-sidebar__mobile-toggle">
            <?/*<div class="cart-sidebar__meta">
                <div class="cart-sidebar__meta-row">
                    {{#WEIGHT_FORMATED}}
                        <span>Вес</span>
                        <span>{{{WEIGHT_FORMATED}}}</span>
                    {{/WEIGHT_FORMATED}}
                </div>
            </div>*/?>

            <a href="/<?= $USER->IsAuthorized() ? 'order' : 'login'?>/" class="btn btn--green cart-sidebar__btn">Оформить заказ</a>
            <br>
            <a href="/catalog/" class="btn btn--blue cart-sidebar__btn">Продолжить покупки</a>

            <div class="cart-sidebar__description">Доступные способы доставки и оплаты можно выбрать при оформлении заказа</div>
        </div>
        <div class="basket-checkout-container" data-entity="basket-checkout-aligner">
            <?
            if ($arParams['HIDE_COUPON'] !== 'Y')
            {
                ?>
                <div class="basket-coupon-section">
                    <div class="basket-coupon-block-field">
                        <!-- <div class="basket-coupon-block-field-description">
                            <?=Loc::getMessage('SBB_COUPON_ENTER')?>:
                        </div> -->
                        <div class="form">
                            <div class="form-group" style="position: relative;">
                                <input type="text" class="form-control" id="" placeholder="Промокод" data-entity="basket-coupon-input">
                                <span class=" basket-coupon-block-coupon-btn"></span>
                            </div>
                        </div>

                    </div>
                </div>
                <?
            }
            ?>

            <?
            if ($arParams['HIDE_COUPON'] !== 'Y')
            {
                ?>
                <div class="basket-coupon-alert-section">
                    <div class="basket-coupon-alert-inner">
                        {{#COUPON_LIST}}
                        <div class="basket-coupon-alert text-{{CLASS}}">
						<span class="basket-coupon-text">
							<strong>{{COUPON}}</strong> -
                            <?=Loc::getMessage('SBB_COUPON')?>
                            {{JS_CHECK_CODE}}

<!--							{{#DISCOUNT_NAME}}({{DISCOUNT_NAME}}){{/DISCOUNT_NAME}}-->
						</span>
                            <span class="close-link fa fa-times" data-entity="basket-coupon-delete" data-coupon="{{COUPON}}">
                                <svg class="icon--24">
                <use xlink:href="/local/templates/azbuka_main/assets/img/sprite.svg#close-icon"></use>
            </svg>
<!--							--><?//=Loc::getMessage('SBB_DELETE')?>
						</span>
                        </div>
                        {{/COUPON_LIST}}
                    </div>
                </div>
                <?
            }
            ?>
        </div>
    </div>

</script>
