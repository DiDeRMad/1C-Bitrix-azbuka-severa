<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class FouroWishlist extends CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        $this->arResult['ITEMS'] = $this->getFavorite();
        $this->includeComponentTemplate();
    }

    private function getFavorite()
    {
        global $USER;
        $userId = $USER->IsAuthorized() ? $USER->GetID() : 0;
        $session = $_COOKIE['PHPSESSID'];

        $itInDelay = \Dev\Helpers\FavoriteHelper::getItems($session, $userId ?: "")['UF_ITEMS'];

        return $itInDelay;
    }

    /*private function getItemInfo($itemIds)
    {
        CModule::IncludeModule('iblock');

        $arSort = [];
        $arFilter = ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'] ?: IBLOCK_CATALOG_MAIN_ID];
        $arOrder = false;
        $arNav = false;
        $arSelect = ['ID'];

        $resDB = CIBlockElement::GetList(

        );
    }*/
}