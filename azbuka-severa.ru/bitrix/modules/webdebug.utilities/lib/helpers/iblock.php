<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class IBlock {
	
	/**
	 *	Get list of iblocks
	 */
	public static function getIBlocks($bGroupByType=true, $bShowInactive=false, $strSiteId=null) {
		$arResult = [];
		if(\Bitrix\Main\Loader::includeModule('iblock')) {
			$arSort = [
				'SORT' => 'ASC',
			];
			$arFilter = [
				'CHECK_PERMISSIONS' => 'Y',
				'MIN_PERMISSION' => 'W',
			];
			if($bGroupByType) {
				$resIBlockTypes = \CIBlockType::GetList([], $arFilter);
				while($arIBlockType = $resIBlockTypes->GetNext(false, false)) {
					$arIBlockTypeLang = \CIBlockType::GetByIDLang($arIBlockType['ID'], LANGUAGE_ID, false);
					$arResult[$arIBlockType['ID']] = [
						'NAME' => $arIBlockTypeLang['NAME'],
						'ITEMS' => [],
					];
				}
			}
			if(!$bShowInactive){
				$arFilter['ACTIVE'] = 'Y';
			}
			if(strlen($strSiteId)){
				$arFilter['SITE_ID'] = $strSiteId;
			}
			$resIBlock = \CIBlock::GetList($arSort, $arFilter);
			while($arIBlock = $resIBlock->GetNext(false, false)) {
				if($bGroupByType) {
					$arResult[$arIBlock['IBLOCK_TYPE_ID']]['ITEMS'][] = $arIBlock;
				}
				else {
					$arResult[] = $arIBlock;
				}
			}
		}
		return $arResult;
	}

}
