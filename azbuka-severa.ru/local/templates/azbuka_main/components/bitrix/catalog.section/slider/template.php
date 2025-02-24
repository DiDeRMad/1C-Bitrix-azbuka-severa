<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
use Bitrix\Sale;

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
 * |	Attention!
 * |	The following comments are for system use
 * |	and are required for the component to work correctly in ajax mode:
 * |	<!-- items-container -->
 * |	<!-- pagination-container -->
 * |	<!-- component-end -->
 */

$this->setFrameMode(true);
//printer($arResult['ITEM_ROWS']);
//printer($APPLICATION->GetPagePropertyList());
foreach ($arResult['ITEMS'] as $item)
{
    $uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
    $areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
}
if (empty($arResult['ITEMS'])) {
    return false;
}
?>
<input type="hidden" class="get-title-class" value="<?= $arParams['CLASS'] ?: 'default'?>">
<div class="product-slider-block">
    <div class="container">
        <h3 class="caption--h2"><?= $arParams['TITLE']?></h3>

        <div class="product-slider">
            <?php  foreach ($arResult['ITEMS'] as $arItem):?>


                <?

                $APPLICATION->IncludeComponent(
                    'bitrix:catalog.item',
                    'slider',
                    array(
                        'RESULT' => array(
                            'ITEM' => $arItem,
                            'AREA_ID' => $areaIds[$item['ID']],
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


            <?php endforeach;?>
        </div>
    </div>
</div>

<!--<script>
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
                $("#smallCartPrice").html(data + '₽');
            }
        });
    }

    function changeQuantity(id, quantity, action) {
        $.ajax({
            url: '/local/ajax/changequantity.php',
            method: 'post',
            dataType: 'html',
            async: false,
            data: {productId: id, quantity: quantity, action: action},
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
                $("#smallCartPrice").html(data + '₽');
            }
        });
    }
</script>
-->