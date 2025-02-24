<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

$themeClass = isset($arParams['TEMPLATE_THEME']) ? ' bx-'.$arParams['TEMPLATE_THEME'] : '';
?>

<div class="inner-header">
    <div class="container">
        <div class="inner-header__wrapper">
            <div class="inner-header__caption">
                <h1 class="caption--h1">Сообщество</h1>
            </div>

            <div class="inner-header__tag-list">
                <?foreach ($arResult['IBLOCK_ITEMS_COUNT'] as $name => $arItem):?>
                    <a href="<?= $arItem['LINK']?>" class="inner-header__tag-item">
                        <span><?= $name?></span>
                        <span><?= $arItem['COUNT']?></span>
                    </a>
                <?endforeach;?>
            </div>
        </div>
    </div>
</div>

<div class="blog">
    <div class="container">
        <div class="blog-list">
            <?foreach ($arResult['ITEMS'] as $arItem):?>
                <a href="<?= $arItem['DETAIL_PAGE_URL']?>" class="community__item">
                    <div class="community__item-img">
                        <?php if ($arItem['PLUG']) { ?>
                            <img src="<?= SITE_TEMPLATE_PATH . '/assets/img/plug.png'?>" />
                        <?php } else { ?>
                            <img srcset="<?=$arItem['PREVIEW_PICTURE']['SRC_1X']?> 1x, <?=$arItem['PREVIEW_PICTURE']['SRC_2X']?> 2x" src="<?=$arItem['PREVIEW_PICTURE']['SRC_1X']?>">
                        <?php } ?>
                    </div>
                    <div class="community__item-content">
                        <p class="community__item-title"  <?php if ($arItem['TEXTCOLOR'] != 'white') { ?>style="color: <?= $arItem['TEXTCOLOR'] ?>;"<?php } ?>><?= $arItem['NAME']?></p>
                    </div>
                </a>
            <?endforeach;?>
        </div>

        <!--<div class="catalog-list-paginator">
            <div class="catalog-list-paginator__counter">Просмотрено 16 из 874</div>
            <div class="catalog-list-paginator__progress-wrapper">
                <div class="catalog-list-paginator__progress"></div>
            </div>
            <button class="btn btn--outline">Загрузить еще</button>
        </div>-->
    </div>
</div>
