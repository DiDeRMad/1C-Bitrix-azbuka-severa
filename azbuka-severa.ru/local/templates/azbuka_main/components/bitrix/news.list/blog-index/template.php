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

$themeClass = isset($arParams['TEMPLATE_THEME']) ? ' bx-' . $arParams['TEMPLATE_THEME'] : '';
?>

<div class="community">
	<div class="container">
		<div class="community__control">
			<h3 class="caption--h2">Сообщество</h3>
			<div class="community__link-list">
				<? foreach ($arResult['IBLOCK_ITEMS_COUNT'] as $name => $arItem) : ?>
					<a href="<?= $arItem['LINK'] ?>" class="community__list-item"><?= $name ?></a>
				<? endforeach; ?>
			</div>
			<a href="/community/" class="community__show-more">Подробнее<span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M8.46968 18.5303C8.17677 18.2374 8.17677 17.7626 8.46968 17.4697L13.9393 12L8.46968 6.53031C8.17677 6.23741 8.17677 5.76261 8.46968 5.46971C8.76258 5.17681 9.23738 5.17681 9.53028 5.46971L15.5303 11.4697C15.8232 11.7626 15.8232 12.2374 15.5303 12.5303L9.53028 18.5303C9.23738 18.8232 8.76258 18.8232 8.46968 18.5303Z" fill="#12266B"></path>
					</svg></span>
			</a>
		</div>

		<div class="community__list-wrapper">
			<div class="community__list">
				<? foreach ($arResult['ITEMS'] as $arItem) : ?>
					<a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="community__item">
						<div class="community__item-img">
							<?php if ($arItem['PLUG']) { ?>
								<img loading="lazy" src="<?= SITE_TEMPLATE_PATH . '/assets/img/plug.png' ?>" alt="<?= SITE_TEMPLATE_PATH . '/assets/img/plug.png' ?>" />
							<?php } else { ?>
								<img loading="lazy" srcset="<?= $arItem['PREVIEW_PICTURE']['SRC_1X_WEBP'] ?> 1x, <?= $arItem['PREVIEW_PICTURE']['SRC_2X_WEBP'] ?> 2x" src="<?= $arItem['PREVIEW_PICTURE']['SRC_1X_WEBP'] ?>" alt="<?= $arItem['NAME'] ?>">
							<?php } ?>
						</div>
						<div class="community__item-content">
							<?php /*<p class="community__item-date" <?php if ($arItem['TEXTCOLOR'] != 'white') { ?>style="color: <?= $arItem['TEXTCOLOR'] ?>;"<?php } ?>><?= $arItem['DATE_CREATE']?></p>*/ ?>
							<p class="community__item-title" <?php if ($arItem['TEXTCOLOR'] != 'white') { ?>style="color: <?= $arItem['TEXTCOLOR'] ?>;" <?php } ?>><?= $arItem['NAME'] ?></p>
						</div>
					</a>
				<? endforeach; ?>
				<? if ($arResult['NEED_FAKE'] == 'Y') : ?>
					<a href="#!" class="community__item">
						<div class="community__item-img">
							<img loading="lazy" srcset="<?= SITE_TEMPLATE_PATH ?>/assets/img/community-6.jpg 1x, <?= SITE_TEMPLATE_PATH ?>/assets/img/community-6@2x.jpg 2x" src="<?= SITE_TEMPLATE_PATH ?>/assets/img/community-6.jpg">
						</div>
						<div class="community__item-content">
							<p class="community__item-date">25 ноября</p>
							<p class="community__item-title">Мы любим вас! Анонс мероприятий декабря</p>
						</div>
					</a>
					<a href="#!" class="community__item">
						<div class="community__item-img">
							<img loading="lazy" srcset="<?= SITE_TEMPLATE_PATH ?>/assets/img/community-7.jpg 1x, <?= SITE_TEMPLATE_PATH ?>/assets/img/community-7@2x.jpg 2x" src="<?= SITE_TEMPLATE_PATH ?>/assets/img/community-7.jpg">
						</div>
						<div class="community__item-content">
							<p class="community__item-date">29 ноября</p>
							<p class="community__item-title">маринуем креветки правильно и вкусно</p>
						</div>
					</a>
					<a href="#!" class="community__item">
						<div class="community__item-img">
							<img loading="lazy" srcset="<?= SITE_TEMPLATE_PATH ?>/assets/img/community-8.jpg 1x, <?= SITE_TEMPLATE_PATH ?>/assets/img/community-8@2x.jpg 2x" src="<?= SITE_TEMPLATE_PATH ?>/assets/img/community-8.jpg">
						</div>
						<div class="community__item-content">
							<p class="community__item-date">27 октября</p>
							<p class="community__item-title">топ-10 деликатесов за полярныем кругом</p>
						</div>
					</a>
				<? endif; ?>
			</div>
		</div>
	</div>
</div>