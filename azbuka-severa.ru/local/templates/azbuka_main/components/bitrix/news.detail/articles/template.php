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
CUtil::InitJSCore(array('fx'));

ob_start();
?>

<div class="header-img">
    <div class="header-img__img">
       <!-- <picture>
            <source type="image/webp" srcset="<?= $arResult['DETAIL_PICTURE']['SRC_1X_WEBP']?> 1x, <?= $arResult['DETAIL_PICTURE']['SRC_2X_WEBP']?> 2x">
            <img srcset="<?= $arResult['DETAIL_PICTURE']['SRC_1X']?> 1x, <?= $arResult['DETAIL_PICTURE']['SRC_2X']?> 2x" src="<?= $arResult['DETAIL_PICTURE']['SRC_1X']?>">
        </picture>-->
		<img src="<?= $arResult['DETAIL_PICTURE']['SRC_1X']?>">
    </div>
    <div class="header-img__content">
        <a href="<?= $arResult['IBLOCK']['LIST_ITEMS']?>" class="header-img__btn btn btn--white"><?= $arResult['IBLOCK']['NAME']?></a>

        <div class="header-img__info">
            <!--<div class="header-img__date"><?= $arResult['TIMESTAMP_X']?></div>-->
            <h1 class="header-img__title"><?= $arResult['NAME']?></h1>
        </div>
    </div>
</div>
<div class="article-content">
    <div class="container">
        <?= $arResult['DETAIL_TEXT'] ?>
    </div>
</div>


<?php if (!empty($arResult['SIMILAR'])):?>
    <div class="container">
        <div class="similar-recipe">
            <h2 class="caption--h2">Похожие статьи</h2>
            <div class="blog-list">
                <?foreach ($arResult['SIMILAR'] as $arSimilar):?>
                    <a href="<?= $arSimilar['DETAIL_PAGE_URL']?>" class="community__item">
                        <div class="community__item-img">
                            <img srcset="<?= $arSimilar['PICTURE']['SRC_1X']?> 1x, <?= $arSimilar['PICTURE']['SRC_2X']?> 2x" src="<?= $arSimilar['PICTURE']['SRC_1X']?>">
                        </div>
                        <div class="community__item-content">
                            <p class="community__item-date"><?= $arSimilar['TIMESTAMP_X']?></p>
                            <p class="community__item-title"><?= $arSimilar['NAME']?></p>
                        </div>
                    </a>
                <?endforeach;?>
            </div>
        </div>
    </div>
<?php endif;?>

<?php
$this->__component->SetResultCacheKeys(array("CACHED_TPL"));
$this->__component->arResult["CACHED_TPL"] = @ob_get_contents();
ob_get_clean();
?>
