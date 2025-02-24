<?php
/**
 * Created by PhpStorm.
 * @author Karikh Dmitriy <demoriz@gmail.com>
 * @date 14.08.2020
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Application as Application;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Page\Asset as Asset;

Loader::includeModule('iblock');
Loader::includeModule('search');

class CSiartTagsSelected extends CBitrixComponent
{
    function __construct($component = null)
    {
        parent::__construct($component);
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams['IBLOCK_ID'] = (int)$arParams['IBLOCK_ID'];
        if (empty($arParams['TAGS_LIST'])) $arParams['TAGS_LIST'] = array();
        if (!is_array($arParams['TAGS_LIST'])) $arParams['TAGS_LIST'] = array($arParams['TAGS_LIST']);

        if (empty($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 3600;
        }
        $arParams['CACHE_GROUPS'] = trim($arParams['CACHE_GROUPS']);
        if ($arParams['CACHE_GROUPS'] != 'N') {
            $arParams['CACHE_GROUPS'] = 'Y';
        }

        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->StartResultCache()) {
            $this->arResult['ITEMS'] = array();
            $this->getItems();

            $this->SetResultCacheKeys(array('ITEMS'));
            $this->IncludeComponentTemplate();
        }
    }

    private function getItems()
    {
        $arTags = HelperTags::getTags($this->arParams['IBLOCK_ID']);
        foreach ($arTags as $arFields) {
            if (in_array($arFields['NAME'], $this->arParams['TAGS_LIST'])) {
                $this->arResult['ITEMS'][] = $arFields;
            }
        }
    }
}
