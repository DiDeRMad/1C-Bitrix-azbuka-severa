<?php
/**
 * Created by PhpStorm.
 * @author Karikh Dmitriy <demoriz@gmail.com>
 * @date 14.08.2020
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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
?>
<? if (!empty($arResult['ITEMS']) && ($arParams['HIDE'] != "Y")): ?>
    <div class="inner-header__tag-list">
        <?php foreach ($arResult['ITEMS'] as $arItem):?>
            <a href="<?= $arItem['DETAIL_LINK']?>" class="inner-header__tag-item">
                <span><?= InteriorHelper::ucfirst($arItem['NAME'])?></span>
                <span><?= $arItem['CNT']?></span>
            </a>
        <?endforeach;?>
        <!--<a href="#!" class="inner-header__tag-item inner-header__tag-item--special">
            <span>Акция 2+1</span>
            <span>19</span>
        </a>-->
    </div>
<? endif ?>
