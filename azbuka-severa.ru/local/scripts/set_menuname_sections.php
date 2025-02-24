<?php
//$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/ext_www/dev1.azbuka-severa.dev-v.ru';
require ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule('iblock');

/**
    Получаем все разделы у которых не заполнено поле UF_MENU_NAME
 */
$resDB = CIBlockSection::GetList(
    [],
    ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, 'UF_MENU_NAME' => false, '!SECTION_ID' => false],
    false,
    ['ID', 'NAME', 'IBLOCK_SECTION_ID']
);

$arRootSections = [];
$arSections = [];


while ($section = $resDB->Fetch()) {
    $arSections[] = $section;
    $arRootSections[$section['IBLOCK_SECTION_ID']] = $section['IBLOCK_SECTION_ID'];
}

$arRootSections = array_unique($arRootSections);


/**
 * Получвем имена родительных разделов для последующего вырезания из названия
 * */
$resDB = CIBlockSection::GetList(
    [],
    ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, 'ID' => $arRootSections],
    false,
    ['ID', 'NAME']
);

while ($section = $resDB->Fetch()) {
    $arRootSections[$section['ID']] = $section['NAME'];
}

$bs = new CIBlockSection();
foreach ($arSections as &$arSection) {
    $rootName = $arRootSections[$arSection['IBLOCK_SECTION_ID']];
    /**
     * Привод к единому регистру
     * */
    $rootName = mb_strtolower($rootName);
    $arSection['NAME'] = mb_strtolower($arSection['NAME']);
    /**
     * Вырезаем название родительного раздела
     * */
    $arSection['NEW_NAME'] = str_replace($rootName, '', $arSection['NAME']);
    $arSection['NEW_NAME'] = trim($arSection['NEW_NAME']);
    /**
     * Делаем первый символ заглавным. Испрользуется кастомная ф-ция mb_ucfirst, так как стандартные php ф-ции нормальне не работают с кириллицей
     * */
    $arSection['NEW_NAME'] = mb_ucfirst($arSection['NEW_NAME']);

    $arFields = [
        'UF_MENU_NAME' => $arSection['NEW_NAME']
    ];

    if ($arSection['ID'])
        $bs->Update($arSection['ID'], $arFields);
}

printer($arSections);
