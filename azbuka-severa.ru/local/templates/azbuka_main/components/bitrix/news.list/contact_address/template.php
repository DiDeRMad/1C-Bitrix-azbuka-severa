<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

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
ob_start();
//printer($arResult);
?>

<div class="contact-page__wrapper">
    <div class="contact-page__sidebar">
        <h1 class="caption--h2">Адреса магазинов</h1>
        <div class="contact-tabs__control">
            <?foreach ($arResult['ITEMS'] as $key => $arItem):?>
            <div class="contact-tabs__control-item <?= $key == 0 ? 'active' : ''?>" id="tabs<?=$key?>">
                <p class="contact-tabs__control-title"><svg class="icon--24"><use xlink:href="/img/sprite.svg#place-icon"></use></svg>
                    <?= $arItem['NAME']?>
                </p>
                <p class="contact-tabs__control-description"><?= $arItem['PREVIEW_TEXT']?></p>
                <p class="contact-tabs__control-description"><?= Loc::getMessage('CT_BNL_PARKING')?></p>
            </div>
            <?endforeach;?>
        </div>
    </div>

    <div class="contact-page__content">
        <div class="contact-tabs__content">
            <?foreach ($arResult['ITEMS'] as $key => $arItem):?>
                <div class="contact-tabs__content-item <?= $key == 0 ? 'active' : ''?>" data-id="tabs<?= $key?>">
                    <?/*= '#iframe_map_' . $key . '#'*/?>
                    <?echo $arItem['~DETAIL_TEXT']?>
                </div>
            <?endforeach;?>
        </div>
    </div>
    <div class="contact-page__sidebar-bottom">
        <div class="contact-page__sidebar-item">
            <p class="contact-page__sidebar-description">Телефон горячей линии</p>
            <a class="contact-page__sidebar-link" href="tel:+74951210110">+7 (495) 121-01-10</a>
        </div>

        <div class="contact-page__sidebar-item">
            <p class="contact-page__sidebar-description">Электронная почта</p>
            <a class="contact-page__sidebar-link" href="mailto:as@azbuka-severa.ru">as@azbuka-severa.ru</a>
        </div>
    </div>
</div>
<?php
$this->__component->SetResultCacheKeys(array("CACHED_TPL"));
$this->__component->arResult["CACHED_TPL"] = @ob_get_contents();
ob_get_clean();
?>