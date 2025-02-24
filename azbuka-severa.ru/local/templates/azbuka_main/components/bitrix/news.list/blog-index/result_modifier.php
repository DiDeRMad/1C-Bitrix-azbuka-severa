<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */

unset($arResult['ITEMS']);
$arResult['ITEMS'] = $arItems =[];
$arIblockItemsCounter = [];


$resDB = CIBlockElement::GetList(
    ['SORT' => 'ASC'],
    ['IBLOCK_ID' => [IBLOCK_CATALOG_ARTICLES, IBLOCK_CATALOG_RECEPT_ID, IBLOCK_NEWS_ID], 'ACTIVE' => 'Y'],
    false,
    false,
    ['ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'TIMESTAMP_X', 'IBLOCK_ID', 'DATE_CREATE']
);

while ($item = $resDB->GetNext()) {
    $arItem = [
        'ID' => $item['ID'],
        'NAME' => $item['NAME'],
        'PREVIEW_PICTURE' => ['ID' => $item['PREVIEW_PICTURE']],
        'DETAIL_PAGE_URL' => $item['DETAIL_PAGE_URL'],
        'TIMESTAMP_X' => $item['TIMESTAMP_X'],
        'DATE_CREATE' => $item['DATE_CREATE'],
        'IBLOCK_ID' => $item['IBLOCK_ID'],
        'PLUG' => false,
        'TEXTCOLOR' => 'white'
    ];
    $itemDb = CIBlockElement::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $item['IBLOCK_ID'], 'ID' => $item['ID']], false, false, ['IBLOCK_ID', 'ID', 'PROPERTY_BGPLUG', 'PROPERTY_TEXTCOLOR', 'PROPERTY_VIEW_DATE']);
    if ($ob = $itemDb->fetch()) {
        $arItem['PLUG'] = ($ob['PROPERTY_BGPLUG_VALUE'] != '');
        switch ($ob['PROPERTY_TEXTCOLOR_VALUE']) {
            case 'черный':
                $color = 'black';
                break;
            case 'синий':
                $color = '#1b389e';
                break;
            case 'белый':
            default:
                $color = 'white';
                break;
        }
        $arItem['TEXTCOLOR'] = $color;
        $arItem['DATE_CREATE'] = $ob['PROPERTY_VIEW_DATE_VALUE'];
    }
    $arIblockItemsCounter[$item['IBLOCK_ID']][] = $arItem;

   // $arResult['ITEMS'][] = $arItem;
    $arItems[$item['IBLOCK_ID']][] = $arItem;
}
$counterArticle = 0;
$counterReciept = 1;
$counterNews = 2;
foreach ($arItems as $iblockId => $items) {
    foreach ($items as $item) {
        if (is_array($item) && isset($item['IBLOCK_ID'])) {
            switch ($iblockId) {
                case IBLOCK_CATALOG_ARTICLES:
                    $arResult['ITEMS'][$counterArticle] = $item;
                    $counterArticle += 3;
                    break;
                case IBLOCK_CATALOG_RECEPT_ID:
                    $arResult['ITEMS'][$counterReciept] = $item;
                    $counterReciept += 3;
                    break;
                case IBLOCK_NEWS_ID:
                    $arResult['ITEMS'][$counterNews] = $item;
                    $counterNews += 3;
                    break;
            }
        }
    }
}
ksort($arResult['ITEMS']);
$arResult['ITEMS'] = array_values($arResult['ITEMS']);

foreach ($arIblockItemsCounter as $ib => $item) {
    switch ($ib) {
        case IBLOCK_CATALOG_ARTICLES:
            $name = 'Статьи';
            $url = '/community/articles/';
            break;
        case IBLOCK_CATALOG_RECEPT_ID:
            $name = 'Рецепты';
            $url = '/community/recepties/';
            break;
        case IBLOCK_NEWS_ID:
            $name = 'Новости';
            $url = '/community/news/';
    }

    $arResult['IBLOCK_ITEMS_COUNT'][$name] =  [
            'COUNT' => count($item),
            'LINK'  => $url
    ];
}



unset($arItem);

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
            "d" => 'j F',                   // выведет "9 июля", если месяц прошел
            //"" => 'j F Y',                    // выведет "9 июля 2012", если год прошел
        ),
        MakeTimeStamp($arItem['DATE_CREATE']),
        time()
    );
}

$arResult['NEED_FAKE'] = 'Y';

if (count($arResult['ITEMS']) >= 8) {
    $arItems = $arResult['ITEMS'];
    unset($arResult['ITEMS']);

    $arResult['ITEMS'][] = $arItems[0];
    $arResult['ITEMS'][] = $arItems[1];
    $arResult['ITEMS'][] = $arItems[2];
    $arResult['ITEMS'][] = $arItems[3];
    $arResult['ITEMS'][] = $arItems[4];
    $arResult['ITEMS'][] = $arItems[5];
    $arResult['ITEMS'][] = $arItems[6];
    $arResult['ITEMS'][] = $arItems[7];

    $arResult['NEED_FAKE'] = 'N';
}

