<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class FouroMenu extends CBitrixComponent
{
    private $IBLOCK_ID;

    public function onPrepareComponentParams($arParams)
    {
        $this->IBLOCK_ID = $arParams['IBLOCK_ID'];

        return $arParams;
    }

    public function executeComponent()
    {
        // Если нет валидного кеша (то есть нужно запросить
        // данные и сделать валидный кеш)
        if ($this->StartResultCache()) {
            $this->arResult = $this->getSections();
            $this->includeComponentTemplate();
        }
    }

    private function getSections()
    {
        global $APPLICATION;
        CModule::IncludeModule('iblock');

        $arResult = [];
        $arSectionsToFindTypes = [];
        $arSort = ['SORT' => 'ASC'];
        $arFilter = ['IBLOCK_ID' => $this->IBLOCK_ID, 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'CNT_ACTIVE' => 'Y'];
        $arSelect = ['ID', 'NAME', 'DEPTH_LEVEL', 'IBLOCK_SECTION_ID', 'SECTION_PAGE_URL', 'UF_MENU_NAME', 'UF_SHOWMENU'];

        $cache = Bitrix\Main\Data\Cache::createInstance();
        $cacheTime = 86400;
        $cacheId = serialize(['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID]);
        $cacheDir = 'menu_sections/';

        if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
            $arResult = $cache->getVars();
        } elseif ($cache->startDataCache()) {

            $resDB = CIBlockSection::GetList(
                $arSort,
                $arFilter,
                true,
                $arSelect
            );

            while ($section = $resDB->GetNext()) {
                $section['NAME'] = $section['UF_MENU_NAME'] ?: $section['NAME'];
                if ($section['ELEMENT_CNT'] > 0) {

                    if ($section['DEPTH_LEVEL'] == 1)
                        $arResult[$section['ID']] = $section;

                    if ($section['DEPTH_LEVEL'] == 2) {
                        $arSectionsToFindTypes[] = $section['ID'];
                        $arResult[$section['IBLOCK_SECTION_ID']]['CHILDS'][$section['ID']] = $section;
                    }
                }
            }

            $arTypes = $this->getTypes($arSectionsToFindTypes);
            $arMenuItem = $this->getMenuItems();
            foreach ($arResult as $root => $arItem) {
                $arResult[$root]['ITEMS'] = $arMenuItem[$root];
                foreach ($arItem['CHILDS'] as $child => $arChild) {
                    $arResult[$root]['CHILDS'][$child]['TYPES'] = $arTypes[$arChild['ID']];
                }
            }
            $cache->endDataCache($arResult);
        }

        //printer($arResult);

        //log_to_console($arResult);
        return $arResult;

    }

    private function getMenuItems()
    {
        CModule::IncludeModule('iblock');
        $arItems = [];

        $resDB = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => IBLOCK_CATALOG_MENU_ITEMS_ID, 'ACTIVE' => 'Y'],
            false,
            false,
            ['ID', 'NAME', 'PROPERTY_PRODUCTS', 'PROPERTY_SECTION', 'CODE']
        );

        while ($item = $resDB->Fetch()) {
            $arItems[$item['PROPERTY_SECTION_VALUE']] = [
                'NAME' => $item['NAME'],
                'ITEMS' => $item['PROPERTY_PRODUCTS_VALUE'],
                'LINK' => $item['CODE']
            ];
        }

        return $arItems;
    }



    private function getTypes($arSections)
    {
        global $APPLICATION;
        CModule::IncludeModule('iblock');

        $arResult = [];
        $arProps = [];
        $arSort = [];
        $arFilter = ['IBLOCK_ID' => $this->IBLOCK_ID, 'ACTIVE' => 'Y', 'SECTION_ID' => $arSections];
        $arOrder = false;
        $arNav = false;
        $arSelect = ['ID', 'IBLOCK_ID', 'PROPERTY_VARIANTS', 'IBLOCK_SECTION'];



        $cache = Bitrix\Main\Data\Cache::createInstance();
        $cacheTime = 86400;
        $cacheId = serialize(['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID]);
        $cacheDir = 'types/'.substr(md5($APPLICATION->GetCurPage()), 0, 3);

        if ($cache->initCache($cacheTime, $cacheId, $cacheDir))
        {
            $arResult = $cache->getVars();
        }elseif ($cache->startDataCache()) {
            $resDB = CIBlockElement::GetList(
                $arSort,
                $arFilter,
                $arOrder,
                $arNav,
                $arSelect
            );

            $db_enum_list = CIBlockProperty::GetPropertyEnum("VARIANTS", array(), array("IBLOCK_ID" => $this->IBLOCK_ID));
            while ($ar_enum_list = $db_enum_list->GetNext()) {
                $arProps[$ar_enum_list['VALUE']] = $ar_enum_list['XML_ID'];
            }

            $arTypes = [];


            while ($item = $resDB->Fetch()) {
                $ar_new_groups = [];

                $db_old_groups = CIBlockElement::GetElementGroups($item['ID'], true);
                while ($ar_group = $db_old_groups->Fetch()) {
                    //printer($item);

                    if (in_array($ar_group['ID'], $arSections) && $item['PROPERTY_VARIANTS_VALUE']) {
                        $sort = 500;
                        $db_enum_list = CIBlockProperty::GetPropertyEnum("VARIANTS", array(), array("IBLOCK_ID" => $this->IBLOCK_ID, "VALUE" => $item['PROPERTY_VARIANTS_VALUE']));
                        while ($ar_enum_list = $db_enum_list->GetNext()) {
                            $sort = $ar_enum_list['SORT'];
                        }
                        $arResult[$ar_group['ID']][] = ['SORT' => $sort, 'NAME' => $item['PROPERTY_VARIANTS_VALUE'], 'LINK' => 'filter/variants-is-' . $arProps[$item['PROPERTY_VARIANTS_VALUE']] . '/apply/'];
                        $arTypes[] = $item['PROPERTY_VARIANTS_VALUE'];
                    }
                }
            }
            $cache->endDataCache($arResult);
        }



        foreach ($arResult as $section => $arRes) {
            $tmp = $key_array = array();
            $i = 0;

            foreach($arRes as $val) {
                if (!in_array($val['NAME'], $key_array)) {
                    $key_array[$i] = $val['NAME'];
                    $tmp[$i] = $val;
                }
                $i++;
            }
            $arResult[$section] = $tmp;
        }

        foreach ($arResult as &$arParts) {
            uasort($arParts, 'sortTypesAsc');
        }


//        printer($arResult);

        return $arResult;
    }
}