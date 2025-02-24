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
?>



<div class="wishlist">
    <div class="container">
        <div class="wishlist__wrapper">
            <div class="wishlist-product-list">

                <?foreach ($arResult['ITEMS'] as $arItem):?>
                    <?php

                    $APPLICATION->IncludeComponent(
                        'bitrix:catalog.item',
                        'wishlist',
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
                <?endforeach;?>
                
            </div>
        </div>
    </div>
</div>