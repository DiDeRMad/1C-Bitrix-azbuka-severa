<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

global $isMobile;

$width = 521;
$height = 495;
if ($isMobile) {
    $width = 260;
    $height = 248;
}

$arSections = [];

$resDB = CIBlockSection::GetList(
    ["SORT" => 'ASC'],
    ['IBLOCK_ID' => IBLOCK_MAIN_CATEGORY_ID, 'ACTIVE' => 'Y'],
    false,
    ['ID', 'NAME', 'PICTURE', 'CODE']
);
while ($section = $resDB->Fetch()) {
    $arItems = [];
    foreach ($arResult['ITEMS'] as $key => $arItem) {
        if ($arItem['IBLOCK_SECTION_ID'] == $section['ID']) {
            $arItems[] = [
                'ID' => $arItem['ID'],
                'NAME' => $arItem['NAME'],
                'URL' => $arItem['PROPERTIES']['URL']['VALUE']
            ];
        }
    }
    $arSections[] = [
        'ID' => $section['ID'],
        'NAME' => $section['NAME'],
        'URL' => $section['CODE'],
        'PICTURE_1X' => $section['PICTURE'] ? CFile::ResizeImageGet($section['PICTURE'], array('width'=> $width, 'height'=> $height), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'] : "",
        'PICTURE_2X' => $section['PICTURE'] ? CFile::ResizeImageGet($section['PICTURE'], array('width'=> $width * 2, 'height'=> $height * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'] : "",
        'PICTURE_1X_WEBP' => $section['PICTURE'] ? webpExtension(CFile::ResizeImageGet($section['PICTURE'], array('width'=> $width, 'height'=> $height), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src']) : "",
        'PICTURE_2X_WEBP' => $section['PICTURE'] ? webpExtension(CFile::ResizeImageGet($section['PICTURE'], array('width'=> $width * 2, 'height'=> $height * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src']) : "",
        'ITEMS' => $arItems
    ];
}

$arResult['ITEMS'] = $arSections;
