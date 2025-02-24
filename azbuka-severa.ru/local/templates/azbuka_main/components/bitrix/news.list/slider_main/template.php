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

global $isMobile;

$this->setFrameMode(true);

//printer($arResult['ITEMS']);

?>
<div class="big-bs">
	<? foreach ($arResult['ITEMS'] as $index => $arItem) : ?>
		<div style="cursor: pointer" data-url="<?= $arItem['PROPERTIES']['LINK']['VALUE'] ?>" class="big-bs__item">
			<img <?php if ($index !== 0) : ?>loading="lazy"<?php endif; ?> src="<?= $arItem['PREVIEW_PICTURE']['WEBP_1X'] ?>" alt="<?php $arItem['NAME']; ?>">
			<div class="big-bs__item-wrapper">
				<div class="container">
					<div class="big-bs__item-content">
						<p class="big-bs__item-title"><?= $arItem['NAME'] ?></p>
						<p class="big-bs__item-description"><?= $arItem['PREVIEW_TEXT'] ?></p>
					</div>
				</div>
			</div>
		</div>
	<? endforeach; ?>
</div>

<script>
	$('.big-bs__item').on('click', function() {
		window.location.href = $(this).data('url');
	})
</script>