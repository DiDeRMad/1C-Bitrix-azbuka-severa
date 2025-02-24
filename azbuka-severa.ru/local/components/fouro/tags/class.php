<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;
use Bitrix\Main\Application as Application;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Page\Asset as Asset;

Loader::includeModule('iblock');
Loader::includeModule('search');

class CSiartTags extends CBitrixComponent
{
    private $path;
    private $action = 'default';
    private $viewTag;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->path = pathinfo($this->request->getScriptFile(), PATHINFO_DIRNAME);
    }

    /**
     * Получение списка разделов с тегами
     * return mixed
     */

    public function getCompileList()
    {
        $list = array();
        $sections = HelperTags::getSections($this->arParams['IBLOCK_ID']);
        $tags = HelperTags::getTags($this->arParams['IBLOCK_ID'], false, $this->arParams['SEF_URL_TEMPLATES']['detail']);
        foreach ($sections as $key => $section) {
            if (!$section['UF_TAGS_TOP'] && !$section['UF_TAGS_BOTTOM']) {
                continue;
            }

            $findTags = HelperTags::strToArray(array(
                $section['UF_TAGS_TOP'],
                $section['UF_TAGS_BOTTOM']
            ));

            if (count($findTags) <= 0) {
                continue;
            }

            foreach ($tags as $key => $tag) {
                if ($tag['CNT'] <= 0) {
                    continue;
                }
                if (in_array($tag['NAME'], $findTags)) {
                    if (!isset($list[$section['ID']])) {
                        $list[$section['ID']] = $section;
                        $list[$section['ID']]['LIST_TAGS'] = array();
                    }

                    $list[$section['ID']]['LIST_TAGS'][] = $tag;
                    unset($tags[$key]);
                }
            }
        }

        return $list;
    }


    public function checkAction()
    {
        $curPage = explode('/', str_replace($this->path, '', $this->request->getRequestedPageDirectory()));

        foreach ($curPage as $page) {
            if (!empty($page)){
                $testPages[] = $page;
            }
        }

        if(is_array($testPages) && count($testPages) > 2) {
            $this->show404();
        } else {
            if ($curPage[1]) {
                $this->action = 'detail';
                $this->viewTag = $curPage[1];
            }
        }
    }

    public function defaultAction()
    {
        global $APPLICATION;
        if ($this->StartResultCache()) {
            $APPLICATION->SetTitle('Все теги');
            $APPLICATION->SetPageProperty('title', 'Все теги');
            $this->arResult['ITEMS'] = $this->getCompileList();
            $this->SetResultCacheKeys(array('ITEMS'));
            $this->IncludeComponentTemplate('sections');
        }
    }

    public function detailAction()
    {
        global $APPLICATION;

        $this->arResult['TAG'] = HelperTags::getUntranslitTag($this->viewTag);

        $curPage = explode('/', str_replace($this->path, '', $this->request->getRequestedPageDirectory()));

        $arPatch = array();
        $isDo = false;
        foreach ($curPage as $strItem) {
            if ($isDo) {
                $arPatch[] = $strItem;
            }
            if ($strItem == 'filter') {
                $isDo = true;
            }
        }

        $this->arResult['SMART_FILTER_PATH'] = '';
        if (!empty($arPatch)) {
            $this->arResult['SMART_FILTER_PATH'] = implode('/', $arPatch);
        }

        if ($this->arResult['TAG'] === false) {
            $this->show404();
        }

        $APPLICATION->SetTitle(ucfirst($this->arResult['TAG']['NAME']));
        $APPLICATION->SetPageProperty("title", ucfirst($this->arResult['TAG']['NAME']));
        $APPLICATION->AddChainItem(ucfirst($this->arResult['TAG']['NAME']), $this->arResult['TAG']['DETAIL_LINK']);

        $this->IncludeComponentTemplate('detail');
    }

    public function show404()
    {
        \Bitrix\Iblock\Component\Tools::process404(
            $this->arParams['MESSAGE_404'], //Сообщение
            true, // Нужно ли определять 404-ю константу
            true, // Устанавливать ли статус
            true, // Показывать ли 404-ю страницу
            false // Ссылка на отличную от стандартной 404-ю
        );
    }

    public function executeComponent()
    {
        $this->checkAction();
        $methodName = $this->action . 'Action';
        if (method_exists($this, $methodName)) {
            $this->$methodName();
        } else {
            $this->show404();
        }
    }
}
