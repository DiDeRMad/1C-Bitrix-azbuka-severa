<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$width = 1903;
$height = 640;

$file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array('width'=>$width, 'height'=>$height), BX_RESIZE_IMAGE_PROPORTIONAL, true);
if ($file['src']) {
    $webp = webpExtension($file['src']);
    $arResult['DETAIL_PICTURE']['SRC_1X'] = $file['src'];
    if ($webp) {
        $arResult['DETAIL_PICTURE']['SRC_1X_WEBP'] = $webp;
    }
}

$file = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array('width'=>$width * 2, 'height'=>$height * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
if ($file['src']) {
    $webp = webpExtension($file['src']);
    $arResult['DETAIL_PICTURE']['SRC_2X'] = $file['src'];
    if ($webp) {
        $arResult['DETAIL_PICTURE']['SRC_2X_WEBP'] = $webp;
    }
}

$file = CFile::ResizeImageGet($arResult['PROPERTIES']['VIDEO_PICTURE']['VALUE'], array('width'=>910, 'height'=>522), BX_RESIZE_IMAGE_PROPORTIONAL, true);
if ($file['src']) {
    $webp = webpExtension($file['src']);
    $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_1X'] = $file['src'];
    if ($webp) {
        $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_1X_WEBP'] = $webp;
    }
}

$file = CFile::ResizeImageGet($arResult['PROPERTIES']['VIDEO_PICTURE']['VALUE'], array('width'=>910 * 2, 'height'=>522 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
if ($file['src']) {
    $webp = webpExtension($file['src']);
    $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_2X'] = $file['src'];
    if ($webp) {
        $arResult['PROPERTIES']['VIDEO_PICTURE']['SRC_2X_WEBP'] = $webp;
    }
}

$file = CFile::ResizeImageGet($arResult['PROPERTIES']['VIDEO_PICTURE_2']['VALUE'], array('width'=>910, 'height'=>522), BX_RESIZE_IMAGE_PROPORTIONAL, true);
if ($file['src']) {
    $webp = webpExtension($file['src']);
    $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_1X'] = $file['src'];
    if ($webp) {
        $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_1X_WEBP'] = $webp;
    }
}

$file = CFile::ResizeImageGet($arResult['PROPERTIES']['VIDEO_PICTURE_2']['VALUE'], array('width'=>910 * 2, 'height'=>522 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
if ($file['src']) {
    $webp = webpExtension($file['src']);
    $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_2X'] = $file['src'];
    if ($webp) {
        $arResult['PROPERTIES']['VIDEO_PICTURE_2']['SRC_2X_WEBP'] = $webp;
    }
}

for ($i = 1; $i <= 5; $i++) {
    $file = CFile::ResizeImageGet($arResult['PROPERTIES']['PICTURE_'.$i]['VALUE'], array('width'=>910, 'height'=>522), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    if ($file['src']) {
        $webp = webpExtension($file['src']);
        $arResult['PROPERTIES']['PICTURE_'.$i]['SRC_1X'] = $file['src'];
        if ($webp) {
            $arResult['PROPERTIES']['PICTURE_'.$i]['SRC_1X_WEBP'] = $webp;
        }
    }

    $file = CFile::ResizeImageGet($arResult['PROPERTIES']['PICTURE_'.$i]['VALUE'], array('width'=>910 * 2, 'height'=>522 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
    if ($file['src']) {
        $webp = webpExtension($file['src']);
        $arResult['PROPERTIES']['PICTURE_'.$i]['SRC_2X'] = $file['src'];
        if ($webp) {
            $arResult['PROPERTIES']['PICTURE_'.$i]['SRC_2X_WEBP'] = $webp;
        }
    }
}

//printer($arResult['PROPERTIES']);

if ($arResult['PROPERTIES']['SLIDER_1']['VALUE']) {
    $arSilder1 = [];
    foreach ($arResult['PROPERTIES']['SLIDER_1']['VALUE'] as $arSlider) {
        $fil1x = CFile::ResizeImageGet($arSlider, array('width'=>1220, 'height'=>700), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($fil1x['src']) {
            $arResult['PROPERTIES']['SLIDER_1']['PICTURE']['1X']['SRC'] = $fil1x['src'];
            $webp = webpExtension($fil1x['src']);
            if ($webp) {
                $arResult['PROPERTIES']['SLIDER_1']['PICTURE']['1X']['SRC_WEBP'] = $webp;
            }
        }

        $fil2x = CFile::ResizeImageGet($arSlider, array('width'=>1220*2, 'height'=>700*2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($fil2x['src']) {
            $arResult['PROPERTIES']['SLIDER_1']['PICTURE']['2X']['SRC'] = $fil2x['src'];
            $webp = webpExtension($fil2x['src']);
            if ($webp) {
                $arResult['PROPERTIES']['SLIDER_1']['PICTURE']['2X']['SRC_WEBP'] = $webp;
            }
        }
        $arSilder1[] = $arResult['PROPERTIES']['SLIDER_1']['PICTURE'];
    }
    $arResult['PROPERTIES']['SLIDER_1']['DATA'] = $arSilder1;
}

if ($arResult['PROPERTIES']['SLIDER_2']['VALUE']) {
    $arSilder2 = [];
    foreach ($arResult['PROPERTIES']['SLIDER_2']['VALUE'] as $arSlider) {
        $fil1x = CFile::ResizeImageGet($arSlider, array('width'=>1220, 'height'=>700), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($fil1x['src']) {
            $arResult['PROPERTIES']['SLIDER_2']['PICTURE']['1X']['SRC'] = $fil1x['src'];
            $webp = webpExtension($fil1x['src']);
            if ($webp) {
                $arResult['PROPERTIES']['SLIDER_2']['PICTURE']['1X']['SRC_WEBP'] = $webp;
            }
        }

        $fil2x = CFile::ResizeImageGet($arSlider, array('width'=>1220*2, 'height'=>700*2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($fil2x['src']) {
            $arResult['PROPERTIES']['SLIDER_2']['PICTURE']['2X']['SRC'] = $fil2x['src'];
            $webp = webpExtension($fil2x['src']);
            if ($webp) {
                $arResult['PROPERTIES']['SLIDER_2']['PICTURE']['2X']['SRC_WEBP'] = $webp;
            }
        }

        $arSilder2[] = $arResult['PROPERTIES']['SLIDER_2']['PICTURE'];
    }
    $arResult['PROPERTIES']['SLIDER_2']['DATA'] = $arSilder2;
}

//Получение шагов
$arSteps = [];

$resDB = CIBlockElement::GetList(
    ['SORT' => 'ASC'],
    ['IBLOCK_ID' => IBLOCK_CATALOG_RECEPT_STEPS_ID, 'PROPERTY_RECEPT' => $arResult['ID']],
    false,
    false,
    ['ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE']
);

while ($step = $resDB->Fetch()) {
    $arStep = [];
    $arStep['ID'] = $step['ID'];
    $arStep['NAME'] = $step['NAME'];
    $arStep['TEXT'] = $step['PREVIEW_TEXT'];

    if ($step['PREVIEW_PICTURE']) {
        $file = CFile::ResizeImageGet($step['PREVIEW_PICTURE'], array('width'=>830 * 1, 'height'=>360 * 1), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($file['src']) {
            $webp = webpExtension($file['src']);
            $arStep['PICTURE']['SRC_1X'] = $file['src'];
            if ($webp) {
                $arStep['PICTURE']['SRC_1X_WEBP'] = $webp;
            }
        }

        $file = CFile::ResizeImageGet($step['PREVIEW_PICTURE'], array('width'=>830 * 2, 'height'=>360 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($file['src']) {
            $webp = webpExtension($file['src']);
            $arStep['PICTURE']['SRC_2X'] = $file['src'];
            if ($webp) {
                $arStep['PICTURE']['SRC_2X_WEBP'] = $webp;
            }
        }
    }

    $arSteps[] = $arStep;
}

$arResult['STEPS'] = $arSteps;

//Похожие статьи
$arSimilarArticles = [];
if ($arResult['PROPERTIES']['SIMILAR']['VALUE']) {
    $resDB = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => IBLOCK_CATALOG_ARTICLES, 'ID' => $arResult['PROPERTIES']['SIMILAR']['VALUE'], 'ACTIVE' => 'Y'],
        false,
        false,
        ['ID', 'NAME', 'PREVIEW_PICTURE', "TIMESTAMP_X", 'DETAIL_PAGE_URL']
    );

    while ($recept = $resDB->Fetch()) {
        $arSimilarArticle['ID'] = $recept['ID'];
        $arSimilarArticle['NAME'] = $recept['NAME'];
        $arSimilarArticle['TIMESTAMP_X'] = $recept['TIMESTAMP_X'];
        $arSimilarArticle['DETAIL_PAGE_URL'] = $recept['DETAIL_PAGE_URL'];

        if ($recept['PREVIEW_PICTURE']) {
            $file = CFile::ResizeImageGet($recept['PREVIEW_PICTURE'], array('width'=>901 * 1, 'height'=>400 * 1), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            if ($file['src']) {
                $webp = webpExtension($file['src']);
                $arSimilarArticle['PICTURE']['SRC_1X'] = $file['src'];
                if ($webp) {
                    $arSimilarArticle['PICTURE']['SRC_1X_WEBP'] = $webp;
                }
            }

            $file = CFile::ResizeImageGet($recept['PREVIEW_PICTURE'], array('width'=>901 * 2, 'height'=>400 * 2), BX_RESIZE_IMAGE_PROPORTIONAL, true);
            if ($file['src']) {
                $webp = webpExtension($file['src']);
                $arSimilarArticle['PICTURE']['SRC_2X'] = $file['src'];
                if ($webp) {
                    $arSimilarArticle['PICTURE']['SRC_2X_WEBP'] = $webp;
                }
            }
        }
        $arSimilarArticles[] = $arSimilarArticle;
    }
}

$arResult['SIMILAR'] = $arSimilarArticles;
$arResult['IBLOCK']['LIST_ITEMS'] = str_replace('#SITE_DIR#', '', $arResult['IBLOCK']['SECTION_PAGE_URL']);

$cp = $this->__component; // объект компонента

if (is_object($cp))
{
    $cp->arResult['PROPERTIES'] = $arResult['PROPERTIES'];
    $cp->SetResultCacheKeys(['PROPERTIES']);
}