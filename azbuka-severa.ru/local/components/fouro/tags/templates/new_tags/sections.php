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

$arIbTags = [];
$arRootSections = [];
$arNewTags = [];

$resDB = CIBlockElement::GetList(
    [],
    ['IBLOCK_ID' => 26, 'ACTIVE' => 'Y', '!IBLOCK_SECTION_ID' => false],
    false,
    false,
    ['ID', 'NAME', "IBLOCK_SECTION_ID"]
);

/*if($_GET['debug'] == 'Y'){
    echo "<pre>"; print_r($arResult['ITEMS']); echo "</pre>";
}*/

while ($tag = $resDB->Fetch()) {
    $find = 0;

    $find = array_search($tag['NAME'], array_column($arResult['ITEMS'], 'NAME'));
    if ($find) {
        $db_old_groups = CIBlockElement::GetElementGroups($tag['ID'], true);
        while ($arGrp = $db_old_groups->Fetch()) {
            $arRootSections[] = $arGrp['IBLOCK_SECTION_ID'];
            $arIbTags[$arGrp['ID']]['NAME'] = $arGrp['NAME'];
            $arIbTags[$arGrp['ID']]['SORT'] = $arGrp['SORT'];
            $arIbTags[$arGrp['ID']]['IBLOCK_SECTION_ID'] = $arGrp['IBLOCK_SECTION_ID'];
            $arIbTags[$arGrp['ID']]['TAGS'][$tag['ID']]['NAME'] = $tag['NAME'];
            $arIbTags[$arGrp['ID']]['TAGS'][$tag['ID']]['CNT'] = $arResult['ITEMS'][$find]['CNT'];
            $arIbTags[$arGrp['ID']]['TAGS'][$tag['ID']]['LINK'] = $arResult['ITEMS'][$find]['DETAIL_LINK'];
        }
    }

}

$arRootSections = array_unique($arRootSections);

$resDB = CIBlockSection::GetList(
    [],
    ['IBLOCK_ID' => 26, 'ID' => $arRootSections],
    false,
    ['ID', 'NAME', 'SORT']
);

while ($section = $resDB->Fetch()) {
    foreach ($arIbTags as $id => $arIbTag) {
        if ($arIbTag['IBLOCK_SECTION_ID'] == $section['ID']) {
            $arNewTags[$section['ID']]['NAME'] = $section['NAME'];
            $arNewTags[$section['ID']]['SORT'] = $section['SORT'];
            $arNewTags[$section['ID']]['SECTIONS'][$id] = $arIbTag;
        }
    }
}

function cmp_function_desc($a, $b){
    return ($a['CNT'] < $b['CNT']);
}

function cmp_function($a, $b){

    return ($a['SORT'] > $b['SORT']);

}

uasort($arNewTags, 'cmp_function');
foreach ($arNewTags as $newTag => $arNewTag) {
    uasort($arNewTags[$newTag]['SECTIONS'], 'cmp_function');
    foreach ($arNewTag['SECTIONS'] as $section => $arSection) {
        uasort($arNewTags[$newTag]['SECTIONS'][$section]['TAGS'], 'cmp_function_desc');
    }
}

/*if($_GET['debug'] == 'Y'){
    echo "<pre>"; print_r($arNewTags); echo "</pre>";
}*/


?>

<div class="tags-page">
    <div class="content">
        <div class="tags-page__wrapper">
            <div class="tags-page__nav">
                <h2 class="tags-page__title">Навигация</h2>
                <ul class="tags-page__nav-list">
                    <li class="tags-page__nav-item"><a href="#?" class="active" data-id="all">Все</a></li>
                    <?foreach ($arNewTags as $id => $arNewTag):?>
                        <li class="tags-page__nav-item"><a href="#?" data-id="tag-<?= $id?>"><?= $arNewTag['NAME']?></a></li>
                    <?endforeach;?>
                </ul>
            </div>
            <div class="tags-page__grid">
                <?foreach ($arNewTags as $id => $arNewTag):?>
                <div id="tag-<?= $id?>" class="tags-page__tag-block">
                    <h3 class="tags-page__title"><?= $arNewTag['NAME']?></h3>
                    <div class="tags-page__tag">
                        <?foreach ($arNewTag['SECTIONS'] as $arSection):?>
                            <div class="tags-page__tag">
                                <h4 class="tags-page__tag-subtitle"><?= $arSection['NAME']?>:</h4>
                                <div class="tags-page__tag-list">
                                    <?foreach ($arSection['TAGS'] as $arTag):?>
                                        <a href="<?= $arTag['LINK']?>" class="s_product__tag"><?= $arTag['NAME'] . ' ' . $arTag['CNT']?></a>
                                    <?endforeach;?>
                                </div>
                            </div>
                        <?endforeach;?>
                    </div>
                </div>
                <?endforeach;?>
            </div>
        </div>
    </div>
</div>