<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Language {

	/**
	 *	Wrapper for Loc::loadMessages()
	 */
	public static function loadMessages($strFile=false){
		if(!is_string($strFile) || !strlen($strFile)){
			$arDebug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
			$strFile = $arDebug[0]['file'];
			if($strFile == realpath(__DIR__.'/../helper.php')){
				$strFile = $arDebug[1]['file'];
			}
		}
		\Bitrix\Main\Localization\Loc::loadMessages($strFile);
	}
	
	/**
	 *	Analog for Loc::getMessage()
	 */
	public static function getMessage($strMessage, $strPrefix=null, $arReplace=null, $bDebug=false){
		if(is_array($strPrefix) && !empty($strPrefix)){ // If there ewre passed only two arguments
			$arReplace = $strPrefix;
		}
		if(is_string($strPrefix) && strlen($strPrefix)){
			$strMessage = $strPrefix.'_'.$strMessage;
		}
		if($bDebug){
			Helper::P($strMessage);
		}
		return \Bitrix\Main\Localization\Loc::getMessage($strMessage, $arReplace);
	}

}
