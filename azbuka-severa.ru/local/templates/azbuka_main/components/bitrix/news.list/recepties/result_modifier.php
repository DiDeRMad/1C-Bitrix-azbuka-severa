<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */


foreach ($arResult['ITEMS'] as $key => &$arItem) {
    switch ($key) {
        case 0:
            $width = 1823;
            $height = 600;
            break;
        case 1:
        case 2:
        $width = 902;
        $height = 400;
            break;
        default:
            $width = 600;
            $height = 400;
            break;
    }

    if ($arItem['PREVIEW_PICTURE']['ID']) {
        $file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($file['src']) {
            $webp = webpExtension($file['src']);
            $arItem['PREVIEW_PICTURE']['SRC_1X'] = $file['src'];
            if ($webp) {
                $arItem['PREVIEW_PICTURE']['SRC_1X_WEBP'] = $webp;
            }
        }

        $file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], array('width'=>$width * 2, 'height'=>$height * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($file['src']) {
            $webp = webpExtension($file['src']);
            $arItem['PREVIEW_PICTURE']['SRC_2X'] = $file['src'];
            if ($webp) {
                $arItem['PREVIEW_PICTURE']['SRC_2X_WEBP'] = $webp;
            }
        }
    }

    $arItem['DATE_CREATE'] = FormatDate(
        array(
            //"d" => 'j F',                   // выведет "9 июля", если месяц прошел
            "" => 'j F Y',                    // выведет "9 июля 2012", если год прошел
        ),
        MakeTimeStamp($arItem['TIMESTAMP_X']),
        time()
    );
}
