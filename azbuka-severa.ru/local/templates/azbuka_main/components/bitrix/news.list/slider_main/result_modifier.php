<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

global $isMobile;

$width = 1903;
$height = 640;
if ($isMobile) {
    $width = 950;
    $height = 320;
}

$arSizes = [
    '2x' => [
        'width' => $width * 2,
        'height' => $height * 2
    ],
    '1x' => [
        'width' => $width,
        'height' => $height
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $file2x = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>$arSizes['2x']['width'], 'height'=>$arSizes['2x']['height']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $file1x = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>$arSizes['1x']['width'], 'height'=>$arSizes['1x']['height']), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    $arItem['PREVIEW_PICTURE']['2X'] = $file2x['src'];
    $arItem['PREVIEW_PICTURE']['1X'] = $file1x['src'];
    $arItem['PREVIEW_PICTURE']['WEBP_2X'] = webpExtension($file2x['src'], 75);
    $arItem['PREVIEW_PICTURE']['WEBP_1X'] = webpExtension($file1x['src'], 75);

    $arItem['NAME'] = htmlspecialchars_decode($arItem['NAME']);
}


