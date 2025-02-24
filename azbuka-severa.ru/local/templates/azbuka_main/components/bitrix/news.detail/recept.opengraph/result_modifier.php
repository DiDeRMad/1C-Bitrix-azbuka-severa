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
$arResult['SCHEMA_PICTURE'] = CFile::ResizeImageGet($arResult['DETAIL_PICTURE']['ID'], array('width'=> 540, 'height'=> 540), BX_RESIZE_IMAGE_PROPORTIONAL, true)['src'];

//Получение шагов
$arSteps = [];

$resDB = CIBlockElement::GetList(
    ['SORT' => 'ASC'],
    ['IBLOCK_ID' => IBLOCK_CATALOG_RECEPT_STEPS_ID, 'PROPERTY_RECEPT' => $arResult['ID']],
    false,
    false,
    ['ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'PROPERTY_VIDEO']
);

while ($step = $resDB->Fetch()) {
    $arStep = [];
    $arStep['ID'] = $step['ID'];
    $arStep['NAME'] = $step['NAME'];
    $arStep['TEXT'] = $step['PREVIEW_TEXT'];
    $arStep['VIDEO'] = $step['PROPERTY_VIDEO_VALUE'];

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
if ($arResult['PROPERTIES']['SIMILAR_RECEPTIES']['VALUE']) {
    $resDB = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => IBLOCK_CATALOG_RECEPT_ID, 'ID' => $arResult['PROPERTIES']['SIMILAR_RECEPTIES']['VALUE'], 'ACTIVE' => 'Y'],
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

$cp = $this->__component; // объект компонента

if (is_object($cp))
{
    $cp->arResult['DESCRIPTION'] = $arResult['PREVIEW_TEXT'] ?: $arResult['DETAIL_TEXT'];
    $cp->arResult['PICTURE'] = $arResult['SCHEMA_PICTURE'];
    $cp->arResult['DETAIL_URL'] = $arResult['DETAIL_PAGE_URL'];
    $cp->SetResultCacheKeys(array('DESCRIPTION','PICTURE', 'DETAIL_URL'));
}