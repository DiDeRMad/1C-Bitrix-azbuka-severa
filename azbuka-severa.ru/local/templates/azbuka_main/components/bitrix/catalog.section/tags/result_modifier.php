<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$arResult['ELEMENTS_COUNT'] = CIBlockSection::GetSectionElementsCount($arResult['ID'], ['CNT_ACTIVE' => 'Y']);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['PREVIEW_PICTURE']['SRC_1X'] = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=> 303, 'height'=> 303), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];
    $arItem['PREVIEW_PICTURE']['SRC_WEBP_1X'] = webpExtension($arItem['PREVIEW_PICTURE']['SRC_1X']);
    $arItem['PREVIEW_PICTURE']['SRC_2X'] = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=> 303 * 2, 'height'=> 303 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];
    $arItem['PREVIEW_PICTURE']['SRC_WEBP_2X'] = webpExtension($arItem['PREVIEW_PICTURE']['SRC_2X']);

    // Добавляем скидку товарам
    if ($arItem['PROPERTIES']['DISCOUNT_PERCENT']['VALUE']) {
        $percent = $arItem['PROPERTIES']['DISCOUNT_PERCENT']['VALUE'] / 100;
        $arItem['MIN_PRICE']['DISCOUNT_VALUE'] = $arItem['MIN_PRICE']['VALUE'] - $arItem['MIN_PRICE']['VALUE'] * $percent;
    }

    if ($arItem['PROPERTIES']['weight']['VALUE']) {
        $arItem['PROPERTIES']['weight']['VALUE'] *= 1000;
        if ($arItem['PROPERTIES']['weight']['VALUE'] % 1000 == 0) {
            $arItem['PROPERTIES']['weight']['VALUE'] = $arItem['PROPERTIES']['weight']['VALUE'] / 1000;
            $arItem['PROPERTIES']['weight']['VALUE_TYPE'] = 'кг';
        } else {
            $arItem['PROPERTIES']['weight']['VALUE_TYPE'] = 'гр.';
        }
    }
    $arItem['NAME'] = $arItem['PROPERTIES']['NAME_H1']['VALUE'] ?: $arItem['NAME'];

    // Добавляем поле для сортировки
    $arItem['SORT_AVAILABLE'] = ($arItem['PRODUCT']['QUANTITY'] > 0) ? 1 : 0;

}
unset($arItem);

// Сортируем массив
usort($arResult['ITEMS'], function($a, $b) {
    if ($a['SORT_AVAILABLE'] == $b['SORT_AVAILABLE']) {
        // Если доступность одинаковая, сортируем по полю sort
        return ($a['SORT'] < $b['SORT']) ? -1 : 1;
    }
    // Сортируем по доступности (в наличии идут первыми)
    return ($a['SORT_AVAILABLE'] > $b['SORT_AVAILABLE']) ? -1 : 1;
});

// SEO текст для страницы
$arResult["SEO_TEXT"] = [];
$curPage = $APPLICATION->GetCurPage();
$res = CIBlockElement::GetList(
    ["SORT" => "ASC"],
    [
        "IBLOCK_ID" => IBLOCK_SEO_TEXT,
        "ACTIVE" => "Y",
        "PROPERTY_PAGE" => $curPage
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "DETAIL_TEXT"]
);
while($arFields = $res->GetNext()) {
    $arResult["SEO_TEXT"][] = $arFields["~DETAIL_TEXT"];
}
