<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

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
$APPLICATION->SetTitle('Все теги');
$APPLICATION->SetPageProperty('title', 'Теги мебели и аксессуаров в наличии и на заказ в Интерьер Маркет');
$APPLICATION->SetPageProperty('description', 'Мебель и аксессуары - в наличии и на заказ. Большой выбор в нашем каталоге, консультация дизайнера и гарантия качества.');
$arResult['ITEMS'] = HelperTags::getTags();
//echo "<pre>"; print_r($arResult['ITEMS']); echo "</pre>";
?>

<?php if (count($arResult['ITEMS']) > 0): ?>
	<section class="p_catalog">
		<div class="content">
            <div class="page_title__wrapper">
                <h1 class="page_title page_title__in_section"><a href="/catalog/">Все теги</a></h1>
            </div>
			<div class="p_catalog__list">
				<?php foreach ($arResult['ITEMS'] as $item) : ?>
				<div class="p_catalog__itm">
					<!--<a href="<?php /*echo $item['SECTION_PAGE_URL']; */?>" class="p_catalog__itm__title"><?php /*echo $item['NAME']; */?></a>-->
					<ul class="p_catalog__itm__list">
						<?php /*foreach ($item['LIST_TAGS'] as $tag) : */?>
						<li><a href="<?php echo $item['DETAIL_LINK']; ?>"><?php echo InteriorHelper::ucfirst($item['NAME']); ?> (<?php echo $item['CNT']; ?>)</a></li>
						<?php /*endforeach; */?>
					</ul>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif ?>
