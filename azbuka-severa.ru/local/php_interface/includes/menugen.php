<?php

AddEventHandler("iblock", "OnAfterIBlockElementAdd", "generateStaticMenu");
AddEventHandler("iblock", "OnAfterIBlockSectionAdd", "generateStaticMenu");
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "generateStaticMenu");
AddEventHandler("iblock", "OnAfterIBlockSectionUpdate", "generateStaticMenu");

function generateStaticMenu(&$arFields) {
    global $APPLICATION;
    if(!CModule::IncludeModule("iblock")){
        return $arFields;
    }

    if ($arFields['IBLOCK_ID'] == IBLOCK_CATALOG_MAIN_ID) {
        BXClearCache(true);
        $GLOBALS["CACHE_MANAGER"]->CleanAll();
        $GLOBALS["stackCacheManager"]->CleanAll();
        $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();
        $taggedCache->deleteAllTags();
        $page = \Bitrix\Main\Composite\Page::getInstance();
        $page->deleteAll();
        $htmlMenu = file_get_contents('https://azbuka-severa.ru/headermenuonly.php');
        $ptf = print_r($htmlMenu, true);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/local/components/fouro/menu/templates/.default/static_version_gen.php', $ptf);
    }
}