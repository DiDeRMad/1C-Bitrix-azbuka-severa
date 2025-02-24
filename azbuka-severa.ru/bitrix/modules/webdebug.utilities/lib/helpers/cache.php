<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Cache {
	
	/**
	 *	Universal cached GetList
	 *	Example:
	 *	$arTestResult = cacheExec('\CIBlockElement::getList', [['SORT' => 'ASC'], ['IBLOCK_ID' => 4], false, false, 
	 *		['ID','NAME']], 60, false, false, false, ['iblock']);
	 */
	public static function cacheExec($strFuncName, $arArguments=null, $intCacheTime=null, $strCacheId=null, 
			$strCacheDir=null, $arCacheTags=null, $arModules=null, $strMethod=null){
		$arResult = [];
		$obPhpCache = new \CPHPCache;
		if(!is_array($arArguments)){
			$arArguments = [];
		}
		if(!is_array($arModules)){
			$arModules = [];
		}
		if(!is_numeric($intCacheTime) || $intCacheTime < 0){
			$intCacheTime = 3600;
		}
		if(!strlen($strCacheId)){
			$strCacheId = serialize([$strFuncName, $arArguments, $Modules, $arCacheTags]);
		}
		if(!strlen($strCacheDir)){
			$strCacheDir = '/wdu/cache_exec/'.str_replace(['::', '\\'], '_', $strFuncName);
		}
		if($obPhpCache->initCache($intCacheTime, $strCacheId, $strCacheDir)) {
			$arResult = $obPhpCache->getVars();
		}
		elseif($obPhpCache->startDataCache()) {
			if(preg_match('#^[\\\]?CIBlock#i', $strFuncName)){
				$arModules[] = 'iblock';
			}
			if(!empty($arModules)) {
				foreach($arModules as $strModuleId) {
					\Bitrix\Main\Loader::includeModule($strModuleId);
				}
			}
			$resItems = call_user_func_array($strFuncName, $arArguments);
			if(!strlen($strMethod)){
				$strMethod = method_exists($resItems, 'getNext') ? 'getNext' : 'fetch';
			}
			while($arItem = call_user_func([$resItems, $strMethod])){
				if(is_numeric($arItem['ID']) && $arItem['ID']) {
					$arResult[$arItem['ID']] = $arItem;
				}
				else {
					$arResult[] = $arItem;
				}
			}
			if(is_array($arCacheTags) && !empty($arCacheTags)) {
				$GLOBALS['CACHE_MANAGER']->startTagCache($strCacheDir);
				foreach($arCacheTags as $strCacheTag){
					$GLOBALS['CACHE_MANAGER']->eegisterTag($strCacheTag);
				}
				$GLOBALS['CACHE_MANAGER']->endTagCache();
			}
			$obPhpCache->endDataCache($arResult);
		}
		unset($obPhpCache, $strModuleId, $resItems, $arItem);
		return $arResult;
	}
}
