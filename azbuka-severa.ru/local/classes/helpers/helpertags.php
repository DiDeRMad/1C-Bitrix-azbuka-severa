<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Application as Application;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Page\Asset as Asset;

class HelperTags
{
    /**
     * Идентификатор последнего использованного инфоблока
     * @var string|boolean
     */
    private static $iblockId = false;

    /**
     * Последняя использованная маска URL для детаельной страницы тегов
     * @var string|boolean
     */
    private static $detailUrlMask = false;

    /**
     * Массив с информацией по тегам
     * @var mixed
     */
    private static $tags = array();

    /**
     * Массив с информацией о разделах
     * @var mixed
     */
    private static $sections = array();

    /**
     * Массив со списком идентификаторов разделов
     * @var mixed
     */
    private static $sectionsId = array();

    /**
     * Получение списка всех разделов инфоблока и информации по ним
     * @param integer $iblockId - идентификатор инфоблока
     * @return mixed
     */

    public static function getSections($iblockId = 5)
    {
        if (count(self::$sections) <= 0 || self::$iblockId != $iblockId) {
            Loader::includeModule('iblock');
            self::$iblockId = $iblockId;
            self::$sections = array();
            $rsSections = CIBlockSection::GetList(
                array(
                    'SORT' => 'ASC'
                ),
                array(
                    'IBLOCK_ID' => $iblockId,
                    'GLOBAL_ACTIVE' => 'Y'
                ),
                false,
                array(
                    'UF_*'
                )
            );

            while ($arSection = $rsSections->GetNext()) {
                self::$sections[$arSection['ID']] = $arSection;
            }
        }

        return self::$sections;
    }

    /**
     * Получение раздела инфоблока
     * @param integer $sectionId - идентификатор раздела
     * @param integer $iblockId - идентификатор инфоблока
     * @return mixed
     */

    public static function getSection($sectionId, $iblockId = 5)
    {
        if (count(self::$sections) <= 0 || self::$iblockId != $iblockId) {
            self::getSections($iblockId);
        }

        return self::$sections[$sectionId];
    }

    /**
     * Получение всех id разделов инфоблока
     * @param integer $iblockId - идентификатор инфоблока
     * @return mixed
     */

    public static function getSectionsId($iblockId = 5)
    {
        $sections = self::getSections($iblockId);
        self::$sectionsId = array();
        foreach ($sections as $section) {
            self::$sectionsId[] = $section['ID'];
        }

        return self::$sectionsId;
    }

    /**
     * Получение информации о теге по его форме в транслите
     * @param string $tagTranslit - тег в транслите
     * @param integer $iblockId - идентификатор инфоблока
     * @param string $detailUrlMask - макса URL страницы тега
     * @return mixed|boolean
     */

    public static function getUntranslitTag($tagTranslit, $iblockId = 5, $detailUrlMask = '/tags/#ELEMENT_CODE#/')
    {
        self::$detailUrlMask = $detailUrlMask;
        $tags = self::getTags($iblockId, false, self::$detailUrlMask);
        foreach ($tags as $tag) {
            if ($tag['CODE'] == $tagTranslit) {
                return $tag;
            }
        }

        return false;
    }

    /**
     * Преобразования строк со списком тегов в массив
     * @param mixed $tagsAr - массив, в значении которого должны содержаться списки тегов через запятую: array('тег 1, тег 2', 'тег 3')
     * @return mixed
     */

    public static function strToArray($tagsAr = array())
    {
        $tags = array();
        foreach ($tagsAr as $tagsStr) {
            $tagsStr = explode(',', $tagsStr);
            foreach ($tagsStr as $tag) {
                $tag = trim($tag);
                if ($tag) {
                    $tags[] = trim($tag);
                }
            }
        }

        return $tags;
    }

    /**
     * Получение ссылки на тег
     * @param string $code - код тега (можно получить через InteriorHelper::translitRuToEn('имя тега'))
     * @param string $detailUrlMask - макса URL страницы тега
     * @return string
     */

    public static function getUrl($code, $detailUrlMask = '/tags/#ELEMENT_CODE#/')
    {
        self::$detailUrlMask = $detailUrlMask;
        if (self::$detailUrlMask) {
            return str_replace('#ELEMENT_CODE#', $code, self::$detailUrlMask);
        } else {
            $request = \Bitrix\Main\Context::getCurrent()->getRequest();
            $path = pathinfo($request->getScriptFile(), PATHINFO_DIRNAME);
            return $path . '/?tag=' . $code;
        }
    }

    /**
     * Обработка инфорации о теге
     * @param mixed $arTag - информация о теге
     * @return mixed
     */

    public static function parseTag($arTag)
    {
        $arTag['CODE'] = InteriorHelper::translitRuToEn($arTag['NAME']);
        $arTag['DETAIL_LINK'] = self::getUrl($arTag['CODE'], self::$detailUrlMask);
        return $arTag;
    }

    /**
     * Сортирует массив с тегами согласно highload блоку `b_tags_sort`
     * @param mixed $tags - массив с тегами
     * @return mixed
     */

    public static function sortTags($tags)
    {
        $topTags = InteriorHelper::getHighloadElements('b_tags_sort');

        // чистим пустые
        unset($topTags['']);


        usort($tags, function ($a, $b) use ($topTags) {
            if (isset($topTags[$a['NAME']]) && isset($topTags[$b['NAME']])) {
                return $topTags[$a['NAME']]['UF_SORT'] > $topTags[$b['NAME']]['UF_SORT'] ? 1 : -1;

            }

            if (isset($topTags[$a['NAME']]) || isset($topTags[$b['NAME']])) {
                return 1;
            }

            if ($a['CNT'] == $b['CNT']) return 0;

            return $a['CNT'] > $b['CNT'] ? -1 : 1;
        });

        return $tags;
    }

    /**
     * Получение информации по тегам из списка
     * @param string $str - строка с тегами разделёнными через запятую
     * @param integer $iblockId - идентификатор инфоблока
     * @param mixed|boolean $sectionsId - массив со списком идентификаторов разделов инфоблока. Если будет @boolean false - то заполнится автоматически
     * @param string $detailUrlMask - макса URL страницы тега
     * @return mixed - список всех найденных тегов с информацией по ним
     */

    public static function getTagsFromStr($str, $iblockId = 5, $sectionsId = false, $detailUrlMask = '/tags/#ELEMENT_CODE#/')
    {
        $tags = self::strToArray(array($str));
        return self::getTagsByName($tags, $iblockId, $sectionsId, $detailUrlMask);
    }

    /**
     * Получение информации о тегах по имени
     * @param mixed $tags - массив с названиями тегов
     * @param integer $iblockId - идентификатор инфоблока
     * @param mixed|boolean $sectionsId - массив со списком идентификаторов разделов инфоблока. Если будет @boolean false - то заполнится автоматически
     * @param string $detailUrlMask - макса URL страницы тега
     * @return mixed - список всех найденных тегов с информацией по ним
     */

    public static function getTagsByName($tags, $iblockId = 5, $sectionsId = false, $detailUrlMask = '/tags/#ELEMENT_CODE#/')
    {
        $findTags = array();
        $allTags = self::getTags($iblockId, $sectionsId, $detailUrlMask);
        foreach ($allTags as $tag) {
            if (in_array($tag['NAME'], $tags)) {
                $findTags[] = $tag;
            }
        }

        return $findTags;
    }

    /**
     * Получение списка самых частоиспользуемых тегов
     * @param integer $max - максимальное кол-во тегов
     * @param integer $iblockId - идентификатор инфоблока
     * @param mixed|boolean $sectionsId - массив со списком идентификаторов разделов инфоблока. Если будет @boolean false - то заполнится автоматически
     * @param string $detailUrlMask - макса URL страницы тега
     * @return mixed
     */

    public static function getTopTags($max = 40, $iblockId = 5, $sectionsId = false, $detailUrlMask = '/tags/#ELEMENT_CODE#/')
    {
        $tags = self::getTags($iblockId, $sectionsId, $detailUrlMask);
        return array_slice($tags, 0, $max);
    }

    /**
     * Получение списка всех тегов
     * @param integer $iblockId - идентификатор инфоблока
     * @param mixed|boolean $sectionsId - массив со списком идентификаторов разделов инфоблока. Если будет @boolean false - то заполнится автоматически
     * @param string $detailUrlMask - макса URL страницы тега
     * @return mixed - список всех найденных тегов с информацией по ним
     */

    public static function getTags($iblockId = 5, $sectionsId = false, $detailUrlMask = '/tags/#ELEMENT_CODE#/')
    {
        if (count(self::$tags) <= 0 || self::$detailUrlMask != $detailUrlMask || self::$iblockId != $iblockId) {
            Loader::includeModule('search');
            self::$detailUrlMask = $detailUrlMask;
            self::$iblockId = $iblockId;
            self::$tags = array();
            $sectionsId = is_array($sectionsId) && count($sectionsId) > 0 ? $sectionsId : self::getSectionsId($iblockId);

            $rsTags = CSearchTags::GetList(
                array(),
                array(
                    "MODULE_ID" => "iblock",
                    "PARAMS" => array(
                       // "iblock_section" => [156]
                    )
                ),
                array(
                    "CNT" => "DESC",
                ),
                COption::GetOptionInt("search", "max_result_size")
            );


            while ($arTag = $rsTags->Fetch()) {
                $arTag = self::parseTag($arTag);
                self::$tags[] = $arTag;
            }

            self::$tags = self::sortTags(self::$tags);
        }

        return self::$tags;
    }

    /**
     * Получение списка всех тегов для раздела
     * @param mixed $sectionsId - массив со списком идентификаторов разделов инфоблока.
     * @param string $detailUrlMask - макса URL страницы тега
     * @param integer $iblockId - идентификатор инфоблока
     * @return mixed|boolean - список всех найденных тегов с информацией по ним
     */

    public static function getSectionTags($sectionsId = false, $iblockId = 5, $detailUrlMask = '/tags/#ELEMENT_CODE#/')
    {
        if ($sectionsId === false) {
            return false;
        }

        Loader::includeModule('search');

        self::$detailUrlMask = $detailUrlMask;
        self::$iblockId = $iblockId;

        // Возвращаемый список
        $tags = array('TOP' => array(), 'BOTTOM' => array());

        // Информация о разделе
        $section = self::getSection($sectionsId, $iblockId);
        if (!$section['UF_TAGS_TOP'] && !$section['UF_TAGS_BOTTOM']) {
            return false;
        }
        $section['UF_TAGS_TOP'] = self::strToArray(array($section['UF_TAGS_TOP']));
        $section['UF_TAGS_BOTTOM'] = self::strToArray(array($section['UF_TAGS_BOTTOM']));

        $rsTags = CSearchTags::GetList(
            array(),
            array(
                "MODULE_ID" => "iblock",
                "PARAMS" => array(
                    "iblock_section" => $sectionsId
                )
            ),
            array(
                "CNT" => "DESC",
            ),
            COption::GetOptionInt("search", "max_result_size")
        );

        while ($arTag = $rsTags->Fetch()) {
            $arTag = self::parseTag($arTag);

            if (in_array($arTag['NAME'], $section['UF_TAGS_TOP'])) {
                $tags['TOP'][] = $arTag;
            }
            if (in_array($arTag['NAME'], $section['UF_TAGS_BOTTOM'])) {
                $tags['BOTTOM'][] = $arTag;
            }
        }

        $tags['TOP'] = self::sortTags($tags['TOP']);
        $tags['BOTTOM'] = self::sortTags($tags['BOTTOM']);

        return $tags;
    }
}
