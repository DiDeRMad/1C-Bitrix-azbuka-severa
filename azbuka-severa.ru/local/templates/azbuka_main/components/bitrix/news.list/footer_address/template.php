<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
<div class="footer-shop">
    <p class="footer-shop__title">Наши магазины</p>

    <div class="footer-shop__list">
        <? foreach ($arResult['ITEMS'] as $arItem): ?>
            <a href="/contacts/" class="footer-shop__item"><?= $arItem['NAME'] ?></a>
        <? endforeach; ?>
    </div>
    <a href="/contacts/" class="footer-shop__all">Смотреть все адреса</a>
    <iframe style="display: block;margin-top: 10px;border: none;" src="https://yandex.ru/sprav/widget/rating-badge/2641741120" width="150" height="50"  ></iframe>
</div>