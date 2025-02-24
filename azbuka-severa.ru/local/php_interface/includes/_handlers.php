<?php
AddEventHandler("main", "OnBuildGlobalMenu", "CustomMenu");
function CustomMenu (&$adminMenu, &$moduleMenu)
{
    global $USER;

    $arFilter = array
    (
        "STRING_ID" => "SUBDOMAIN_MANAGER",
        "ACTIVE"    => "Y",
    );

    $by = "SORT";
    $order = "asc";

    $rsGroup = CGroup::GetList($by, $order, $arFilter);
    if ($arGroup = $rsGroup->Fetch()) {
        $arGroups = $USER->GetUserGroupArray();

        if (in_array($arGroup["ID"], $arGroups) || in_array('1', $arGroups)) {
            $moduleMenu[] = array(
                "parent_menu" => "global_menu_services", // поместим в раздел "Сервис"
                "section"     => "Управление сортировкой-ценой",
                "sort"        => 1,                    // сортировка пункта меню
                'url'         => '/bitrix/admin/informula_storage-control.php?lang=' . SITE_ID,
                'text'        => 'Управление поддоменами',
                'title'       => 'Управление подоменами',
                'icon'        => 'global_menu_informula_icon',
                'page_icon'   => 'global_menu_informula_icon',
                'items_id'    => 'menu_informula_subdomain',
            );
        }
    }
    $moduleMenu[] = array(
        "parent_menu" => "global_menu_services", // поместим в раздел "Сервис"
        "section"     => "Управление сортировкой-ценой",
        "sort"        => 2,                    // сортировка пункта меню
        "url"         => "informula_sortprice_control.php?lang=" . LANG,  // ссылка на пункте меню
        "text"        => 'Управление сортировкой-ценой',       // текст пункта меню
        "title"       => 'Управление сортировкой-ценой', // текст всплывающей подсказки
        "icon"        => "update_menu_icon_partner", // малая иконка
        "page_icon"   => "form_page_icon", // большая иконка
    );

}