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
$counter = 0;
?>

<div class="extra-category">
	<div class="extra-category__list">
		<? foreach ($arResult['ITEMS'] as $arItem) : ?>
			<? if ($counter <= 2) : ?>
				<div style="cursor: pointer" data-url="<?= $arItem['URL'] ?>" class="extra-category__item">
					<a href="<?= $arItem['URL'] ?>" class="extra-category__caption"><?= $arItem['NAME'] ?></a>
					<? if (!empty($arItem['ITEMS'])) : ?>
						<div class="extra-category__link-wrapper">
							<? foreach ($arItem['ITEMS'] as $arUrl) : ?>
								<a href="<?= $arUrl['URL'] ?>" class="extra-category__link"><?= $arUrl['NAME'] ?></a>
							<? endforeach; ?>
						</div>
					<? endif ?>

					<div class="extra-category__decor">
						<img loading="lazy" srcset="<?= $arItem['PICTURE_1X_WEBP'] ?> 1x, <?= $arItem['PICTURE_2X_WEBP'] ?> 2x" src="<?= $arItem['PICTURE_1X_WEBP'] ?>" alt="<?= $arItem['NAME'] ?>">
					</div>
				</div>
			<? endif; ?>
			<? if ($counter == 2) : ?>
				<div class="extra-category__list--add-wrapper">
					<div class="extra-category__list--add">
					<? endif; ?>
					<? if ($counter > 2) : ?>
						<div style="cursor: pointer" data-url="<?= $arItem['URL'] ?>" class="extra-category__item">
							<a href="<?= $arItem['URL'] ?>" class="extra-category__caption"><?= $arItem['NAME'] ?></a>
							<div class="extra-category__decor">
								<img loading="lazy" srcset="<?= $arItem['PICTURE_1X_WEBP'] ?> 1x, <?= $arItem['PICTURE_2X_WEBP'] ?> 2x" src="<?= $arItem['PICTURE_1X_WEBP'] ?>" alt="<?= $arItem['NAME'] ?>">
							</div>
						</div>
					<? endif ?>

					<? $counter++ ?>
					<? if ($counter == count($arResult['ITEMS'])) : ?>
					</div>
				</div>
			<? endif ?>
		<? endforeach; ?>
	</div>
	<div class="extra-category__control">
		<button type="button" class="extra-category__all js-extra-category">Все категории <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M18.5303 8.46971C18.2374 8.17681 17.7625 8.17681 17.4696 8.46971L12 13.9393L6.53028 8.46971C6.23738 8.17681 5.76258 8.17681 5.46968 8.46971C5.17677 8.76261 5.17677 9.23741 5.46968 9.53031L11.4697 15.5303C11.7626 15.8232 12.2374 15.8232 12.5303 15.5303L18.5303 9.53031C18.8232 9.23741 18.8232 8.76261 18.5303 8.46971Z" fill="#12266B"></path>
				</svg></span></button>
	</div>
</div>

<script>
	$('.extra-category__item').on('click', function() {
		window.location.href = $(this).data('url');
	})
</script>