<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Version {

	/**
	 *	Get module version (or version date, or both [if $bDate is null])
	 */
	public static function getModuleVersion($strModuleId, $bDate=false){
		$arModuleVersion = [];
		if($strModuleId == 'main'){
			include_once(Helper::root().'/bitrix/modules/'.$strModuleId.'/classes/general/version.php');
			$arModuleVersion = [
				'VERSION' => SM_VERSION,
				'VERSION_DATE' => SM_VERSION_DATE,
			];
		}
		else{
			$strModuleDir = Helper::getModuleDir($strModuleId);
			if(strlen($strModuleDir) && is_dir(Helper::root().$strModuleDir)){
				include(Helper::root().$strModuleDir.'/install/version.php');
			}
			if(!is_array($arModuleVersion)){
				$arModuleVersion = [];
			}
		}
		if(is_null($bDate)){
			return $arModuleVersion;
		}
		return $bDate ? $arModuleVersion['VERSION_DATE'] : $arModuleVersion['VERSION'];
	}
	
	/**
	 *	Is $strTestVersion equal (or more) than $strBaseVersion?
	 */
	public static function checkVersion($strTestVersion, $strBaseVersion){
		return checkVersion($strTestVersion, $strBaseVersion);
	}
	
	/**
	 *	Is catalog based on new filter? 
	 *	https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=12183#iblock_18_6_200
	 */
	public static function isCatalogNewFilter(){
		return static::checkVersion(SM_VERSION, '18.6.200');
	}

}
