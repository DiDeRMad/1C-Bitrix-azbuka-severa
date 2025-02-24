<?php

use Bitrix\Main\Loader;

class InteriorHelper
{
    const SITE_ID = 's1';

    /**
     * Список закэшированных полученных данных из инфоблоков и highblock
     * @var array
     */

    public static $cache = array();

    /**
     * Список закэшированных инфоблоков (настройки и свойства инфоблока)
     * @var array
     */

    private static $iblockInfo = array();

    /**
     * Список закэшированных разделов
     * @var array
     */

    private static $sectionInfo = array();

    /**
     * Список закэшированных подразделов
     * @var array
     */

    private static $subSectionsInfo = array();

    /**
     * Список закэшированных товаров и торговых предложений
     * @var array
     */

    private static $elementInfo = array();

    /**
     * Список закэшированных складов (настройки складов)
     * @var array
     */

    private static $storeInfo = array();

    /**
     * Список закэшированных полученных типов цен (настройки типов цен)
     * @var array
     */

    private static $priceInfo = array();

    /**
     * Список выведенных разделов в специальном меню
     * @var array
     */

    private static $specialMenuIds = array();

    /**
     * Список выведенных разделов в выпадающем меню
     * @var array
     */

    private static $showMenuIds = array();

    /**
     * Получение всей информации по инфоблоку
     * @param string $XML_ID - ид внешнего кода
     * @return array
     */

    public static function getIblockInfo($XML_ID)
    {
        if (!self::$iblockInfo[$XML_ID]) {
            $res = CIBlock::GetList(array(), array('SITE_ID' => self::SITE_ID, 'XML_ID' => $XML_ID, 'CHECK_PERMISSIONS' => 'N'), false);
            $arIblock = $res->Fetch();
            if ($arIblock['ID']) {
                // Получаем поля блока
                $arIblock['FIELDS'] = CIBlock::GetFields($arIblock['ID']);
                // Получаем свойства блока
                $arIblock['PROPERTIES'] = array();
                $resProp = CIBlock::GetProperties($arIblock['ID'], array(), array());
                while ($arProp = $resProp->Fetch()) {

                    // Highloadblock
                    if ($arProp['USER_TYPE'] == 'directory' && $arProp['USER_TYPE_SETTINGS']['TABLE_NAME']) {
                        $arProp['LIST_VALUE'] = self::getHighloadElements($arProp['USER_TYPE_SETTINGS']['TABLE_NAME']);
                    }

                    // Список
                    if ($arProp['PROPERTY_TYPE'] == 'L') {
                        $arProp['LIST_VALUE'] = array();
                        $db_enum_list = CIBlockProperty::GetPropertyEnum($arProp['CODE'], array(), array("IBLOCK_ID" => $arIblock['ID']));
                        while ($prop_fields = $db_enum_list->GetNext()) {
                            $arProp['LIST_VALUE'][$prop_fields["ID"]] = $prop_fields;
                        }
                    }

                    $arIblock['PROPERTIES'][($arProp['XML_ID'] ? $arProp['XML_ID'] : $arProp['CODE'])] = $arProp;
                }

                self::$iblockInfo[$XML_ID] = $arIblock;
            }
        }

        return self::$iblockInfo[$XML_ID];
    }

    /**
     * Получение списка записей из Highload
     * @param string $table - название таблицы
     * @return array
     */

    public static function getHighloadElements($table, $update = false)
    {
        CModule::IncludeModule('highloadblock');
        if ($update === true) {
            unset(self::$cache[$table]);
        }
        if (!array_key_exists($table, self::$cache)) {
            $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
                "filter" => array(
                    "=TABLE_NAME" => $table,
                )))->fetch();

            $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();

            $rsData = $entity_data_class::getList(array(
                "select" => array("*"),
            ));

            self::$cache[$table] = array();
            while ($arData = $rsData->fetch()) {
                if ($arData["UF_XML_ID"] || $arData['UF_OUTER_CODE']) {
                    self::$cache[$table][$arData["UF_XML_ID"]] = $arData;
                    if ($arData['UF_OUTER_CODE']) {
                        self::$cache[$table][$arData['UF_OUTER_CODE']] = $arData;
                    }
                } else {
                    self::$cache[$table][$arData["ID"]] = $arData;
                }
            }
        }

        return self::$cache[$table];
    }

    /**
     * Добавление значения в highload блок
     * @param string $table - название таблицы
     * @param array $params - значения полей
     */

    public static function addHighloadElement($table, $params)
    {
        CModule::IncludeModule('highloadblock');

        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
            "filter" => array(
                "=TABLE_NAME" => $table,
            )))->fetch();

        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        $entity_data_class::add($params);

        $listValues = self::getHighloadElements($table, true);
        return $listValues[$params['UF_XML_ID']];
    }

    /**
     * Метод добавляет новый вариант значения свойства типа "список"
     * @param string $params
     */

    public static function addListEnumElement($params)
    {
        $ibpenum = new CIBlockPropertyEnum;
        $propertyEnumId = $ibpenum->Add($params);
        return $propertyEnumId;
    }

    /**
     * Получение значения свойства типа "список"
     * @param integer $enumId - идентификатор значения
     */

    public static function getListEnumValue($enumId)
    {
        $rsEnum = CUserFieldEnum::GetList(array(), array("ID" => $enumId));
        return $rsEnum->GetNext();
    }

    /**
     * Получение списка записей из инфоблока
     * @param integer $IBLOCK_ID - id инфоблока
     * @return array
     */

    public static function getIblockElements($IBLOCK_ID)
    {
        if (!array_key_exists($IBLOCK_ID, self::$cache)) {
            $arSelect = array();
            $arFilter = array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y", "CHECK_PERMISSIONS" => "Y");
            $arOrder = array("NAME" => "ASC", "ID" => "ASC");
            self::$cache[$IBLOCK_ID] = array();
            $rsItems = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
            while ($obItem = $rsItems->GetNextElement()) {
                $arItem = $obItem->GetFields();
                $arItem['PROPERTIES'] = $obItem->GetProperties();
                // Получение привязанного элемента новости
                if ($arItem['PROPERTY_TYPE_NEWS_VALUE']) {
                    $arSelect = array("ID", "NAME", "CODE", "DETAIL_PAGE_URL");
                    $arFilter = array("ID" => $arItem['PROPERTY_TYPE_NEWS_VALUE'], "ACTIVE" => "Y", "CHECK_PERMISSIONS" => "Y");
                    $arOrder = array("NAME" => "ASC", "ID" => "ASC");
                    $newsItems = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
                    $arItem['NEWS_ITEM'] = $newsItems->GetNext();
                }

                self::$cache[$IBLOCK_ID][$arItem["ID"]] = $arItem;
            }
        }

        return self::$cache[$IBLOCK_ID];
    }

    /**
     * Получение информации о свойстве
     * @param string $XML_ID - ид внешнего кода
     * @return array
     */

    public static function getPropertyInfo($XML_ID, $iblock)
    {
        return $iblock['PROPERTIES'][$XML_ID];
    }

    /**
     * Получение информации о разделе
     * @param string $XML_ID - ид внешнего кода
     * @return array
     */

    public static function getSectionInfo($XML_ID, $iblock)
    {
        if (!self::$sectionInfo[$XML_ID]) {
            $obSection = CIBlockSection::GetList(array(), array('XML_ID' => $XML_ID, 'IBLOCK_ID' => $iblock['ID']));
            $arSection = $obSection->Fetch();
            if ($arSection['ID']) {
                self::$sectionInfo[$XML_ID] = $arSection;
            }
        }

        return self::$sectionInfo[$XML_ID];
    }

    /**
     * Получение информации о разделе
     * @param array $filter
     * @param bool $cnt
     * @param string[] $select
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */

    public static function getSectionsWithChilds($filter = array(), $cnt = false, $select = array('UF_*'))
    {
        $filterKey = md5(serialize($filter) . $cnt);
        if (!self::$subSectionsInfo[$filterKey]) {
            Loader::includeModule('iblock');
            self::$subSectionsInfo[$filterKey] = array();

            $arOrder = array("SORT" => "ASC");
            $arFilter = array();
            $arSelect = $select;

            if (count($filter) > 0) {
                foreach ($filter as $key => $value) {
                    $arFilter[$key] = $value;
                }
            }

            $obSection = CIBlockSection::GetList($arOrder, $arFilter, $cnt, $arSelect);
            while ($arSection = $obSection->GetNext()) {
                if ($arSection['ID']) {
                    unset($filter['CODE'], $filter['XML_ID'], $filter['SECTION_ID']);
                    $filter['SECTION_ID'] = $arSection['ID'];
                    $arSection['CHILDS'] = self::getSectionsWithChilds($filter, $cnt, $select);
                    self::$subSectionsInfo[$filterKey][] = $arSection;
                }
            }
        }

        return self::$subSectionsInfo[$filterKey];
    }

    public static function filterByShowMenu($sections, $first = true)
    {
        if ($first === true) {
            self::$showMenuIds = array();
        }

        $arSections = array();
        foreach ($sections as $section) {
            $subSections = self::filterByShowMenu($section['CHILDS']);
            unset($section['CHILDS']);
            if ($section['UF_MENU_ALLOW'] == 1 && $section['ELEMENT_CNT'] > 0 && !in_array($section['ID'], self::$showMenuIds)) {
                $arSections[$section['UF_MENU_SORT']] = $section;
                self::$showMenuIds[] = $section['ID'];
            }
            foreach ($subSections as $subSection) {
                $arSections[$subSection['UF_MENU_SORT']] = $subSection;
            }
        }

        ksort($arSections);
        return $arSections;
    }

    public static function filterBySpecialMenu($sections, $first = true)
    {
        if ($first === true) {
            self::$specialMenuIds = array();
        }

        $arSections = array();
        foreach ($sections as $section) {
            $subSections = self::filterBySpecialMenu($section['CHILDS'], false);
            unset($section['CHILDS']);
            if ($section['UF_MENU_SPECIAL'] == 1 && $section['ELEMENT_CNT'] > 0 && !in_array($section['ID'], self::$specialMenuIds)) {
                $arSections[$section['UF_MENU_SPECIAL_SORT']] = $section;
                self::$specialMenuIds[] = $section['ID'];
            }
            foreach ($subSections as $subSection) {
                $arSections[$subSection['UF_MENU_SPECIAL_SORT']] = $subSection;
            }
        }

        ksort($arSections);
        return $arSections;
    }

    /**
     * Получение информации о элементе
     * @param string $XML_ID - ид внешнего кода
     * @return array
     */

    public static function getElementInfo($XML_ID, $iblock)
    {
        if (!self::$elementInfo[$XML_ID]) {
            $rsItems = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $iblock['ID'], 'XML_ID' => $XML_ID), false, false, array());
            while ($obItem = $rsItems->GetNextElement()) {
                $arItem = $obItem->GetFields();
                $arItem['PROPERTIES'] = $obItem->GetProperties();
                self::$elementInfo[$XML_ID] = $arItem;
            }
        }

        return self::$elementInfo[$XML_ID];
    }

    /**
     * Получение информации о типе цены
     * @param string $XML_ID - ид внешнего кода
     * @return array
     */

    public static function getTypePrice($XML_ID)
    {
        if (!self::$priceInfo[$XML_ID]) {
            $allPrice = CCatalogGroup::GetListArray();
            foreach ($allPrice as $priceType) {
                self::$priceInfo[($priceType['XML_ID'] ? $priceType['XML_ID'] : $priceType['ID'])] = $priceType;
            }
        }

        return self::$priceInfo[$XML_ID];
    }

    /**
     * Получение информации о складе
     * @param string $XML_ID - ид внешнего кода
     * @return array
     */

    public static function getStore($XML_ID)
    {
        if (!self::$storeInfo[$XML_ID]) {
            $obStoreOffer = CCatalogStore::GetList(array(), array('XML_ID' => $XML_ID), false, false, array());
            while ($arStore = $obStoreOffer->Fetch()) {
                self::$storeInfo[$XML_ID] = $arStore;
            }
        }

        return self::$storeInfo[$XML_ID];
    }

    /**
     * Получение списка первых букв в виде ссылок для фильтрации из списка элементов инфоблока
     * @param integer $iblock_id - id инфоблока
     * @return string
     */

    public static function getListAlpha($iblock_id, $filter = array())
    {
        CModule::IncludeModule("iblock");

        $arOrder = array("NAME" => "ASC");
        $arFilter = array("IBLOCK_ID" => $iblock_id, "ACTIVE" => "Y");
        $arSelect = array("NAME");

        if (count($filter) > 0) {
            foreach ($filter as $key => $value) {
                $arFilter[$key] = $value;
            }
        }

        $rsItems = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            false,
            false,
            $arSelect
        );
        $alpha = array();
        while ($obItem = $rsItems->GetNext()) {
            $alpha[] = toUpper(substr($obItem["NAME"], 0, 1));
        }
        $alpha = array_unique($alpha);
        sort($alpha);

        $html = "";
        $i = 0;
        foreach ($alpha as $al) {
            $html .= '<li class="' . ($al == $_REQUEST["filter"] ? 'active' : '') . '"><a data-start="' . $i . '" href="' . $GLOBALS["APPLICATION"]->GetCurPageParam("filter=" . $al, array("filter")) . '">' . $al . '</a></li>';
            $i++;
        }
        $html .= '<li class="alpha-all ' . (!$_REQUEST["filter"] ? 'active' : '') . '"><a data-start="' . $i++ . '" href="' . $GLOBALS["APPLICATION"]->GetCurPageParam("", array("filter")) . '">' . (!$_REQUEST["filter"] ? 'A-Z' : 'Все') . '</a></li>';

        return $html;
    }

    /**
     * Конвертация расских символов в кирилицу, а также замена спец. символов
     * @param string $str
     * @return string
     */

    public static function translitRuToEn($str)
    {
        return strtolower(Cutil::translit($str, "ru", array("replace_space" => "-", "replace_other" => "-")));
    }

    /**
     * Переводит первый символ строки в верхний регистр с поддержкой кирилицы (аналог функции PHP ucfirst)
     * @param string $string
     * @return string
     */

    public static function ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }

    /**
     * Получение массива разделов с вложенностью дочерних
     * @param mixed $order - параметры сортировки
     * @param mixed $filter - фильтр разделов
     * @param boolean|mixed $data - массив разделов
     * @param integer $parent - родительский раздел
     * @return mixed
     */

    public static function getThreadSection($order = array('SORT' => 'ASC'), $filter = array(), $data = false, $parent = 0)
    {
        $hideSection = array();

        if ($data === false) {
            $checkIds = array();
            $data = array(array());
            Loader::includeModule('iblock');
            $arOrder = $order;
            $arFilter = $filter;
            $arSelect = array('UF_*');
            $tree = CIBlockSection::GetList(
                $arOrder,
                $arFilter,
                true,
                $arSelect
            );
            while ($section = $tree->GetNext()) {
                $parentSection = $section['UF_CATALOG_ISMAIN'] == 1 || !$section['IBLOCK_SECTION_ID'] ? 0 : $section['IBLOCK_SECTION_ID'];
                if (!is_array($data[$parentSection])) {
                    $data[$parentSection] = array();
                }
                $data[$parentSection][] = $section;
                $checkIds[] = $section['ID'];
            }

            foreach ($data as $sectionParent => $sections) {
                if ($sectionParent == 0) {
                    continue;
                }
                if (!in_array($sectionParent, $checkIds)) {
                    foreach ($sections as $section) {
                        $data[0][] = $section;
                    }

                    unset($data[$sectionParent]);
                }
            }

            usort($data[0], function ($a, $b) {
                if ($a['UF_CATALOG_PAGE_SORT'] == $b['UF_CATALOG_PAGE_SORT']) {
                    return 0;
                }

                return ($a['UF_CATALOG_PAGE_SORT'] < $b['UF_CATALOG_PAGE_SORT']) ? -1 : 1;
            });
        }

        $thread = array();
        if (count($data[$parent]) > 0) {
            foreach ($data[$parent] as $section) {
                if (in_array($section['ID'], $hideSection)) {
                    continue;
                }
                $section['CHILDS'] = self::getThreadSection($order, $hideSection, $data, $section['ID']);
                $thread[] = $section;
            }
        }

        return $thread;
    }

    /**
     * Формирование HTML дерева разделов
     * @param mixed $thread - массив со списком разделов InteriorHelper::getThreadSection
     * @param boolean $showEmpty - показывать ли пустые разделы
     * @param boolean $maxLevel - максимальный вложенный уровень
     * @param mixed $tpl - html шаблоны
     * @param integer $level
     * @return string
     */

    public static function compileSectionThread($thread, $showEmpty = true, $maxLevel = false, $tpl = array(), $level = 1)
    {
        if ($maxLevel !== false && $maxLevel < $level) {
            return '';
        }
        $html = array();
        if (count($thread) > 0) {
            $sectList = array();
            switch ($level) {
                case 1:
                    $sectMainHtml = $tpl['level1_main'];
                    $sectListHtml = $tpl['level1_list'];
                    break;

                case 2:
                    $sectMainHtml = $tpl['level2_main'];
                    $sectListHtml = $tpl['level2_list'];
                    break;

                default:
                    $sectMainHtml = $tpl['other_main'];
                    $sectListHtml = $tpl['other_list'];
                    break;
            }
            foreach ($thread as $section) {
                $childs = self::compileSectionThread($section['CHILDS'], $showEmpty, $maxLevel, $tpl, ($level + 1));
                if ($showEmpty !== true) {
                    if ($section['ELEMENT_CNT'] <= 0 && !$childs) {
                        continue;
                    }
                }
                $sectList[] = str_replace(
                    array(
                        '{link}',
                        '{name}',
                        '{cnt}',
                        '{childs}',
                    ),
                    array(
                        $section['SECTION_PAGE_URL'],
                        $section['NAME'],
                        $section['ELEMENT_CNT'],
                        $childs
                    ),
                    $sectListHtml
                );
            }

            if (count($sectList) > 0) {
                $html[] = str_replace('{list}', implode('', $sectList), $sectMainHtml);
            }
        }

        return implode('', $html);
    }
}
