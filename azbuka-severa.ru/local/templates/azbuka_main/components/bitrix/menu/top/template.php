<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

if (empty($arResult["ALL_ITEMS"]))
	return;

CUtil::InitJSCore();

?>

<nav class="header-menu" itemscope="" itemtype="https://schema.org/SiteNavigationElement">
    <div itemprop="about" itemscope="" itemtype="https://schema.org/ItemList">
	<?foreach ($arResult['ALL_ITEMS'] as $arItem):?>
    <div itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ItemList">
		<a href="<?= $arItem['LINK']?>" class="header-menu__item" itemprop="url"><?= $arItem['TEXT']?></a>
        <meta itemprop="name" content="<?= $arItem['TEXT']?>" />
    </div>
	<?endforeach;?>
    </div>
</nav>