<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Debug {
	
	protected static $fMemory = null;
	
	protected static $bDebugMode = false;

	/**
	 *	Debug print
	 */
	public static function P($arData, $bJust=false, $bRemoveFunctions=false) {
		if($bJust){
			Helper::obRestart();
		}
		if($bRemoveFunctions && is_array($arData)){
			$arData = static::debugRemoveFunctions($arData);
		}
		$strId = 'pre_'.Helper::randString();
		$strResult = '<style>pre#'.$strId.'{background:none repeat scroll 0 0 #FAFAFA; border-color:#AAB4BE #AAB4BE #AAB4BE #B4B4B4; border-style:dotted dotted dotted solid; border-width:1px 1px 1px 20px; font:normal 11px \"Courier New\",\"Courier\",monospace; margin:10px 0; padding:5px 0 5px 10px; position:relative; text-align:left; white-space:pre-wrap; word-break: break-all; -webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box;}</style>';
		if(is_array($arData) && empty($arData))
			$arData = '--- Array is empty ---';
		if($arData === false)
			$arData = '[false]';
		elseif ($arData === true)
			$arData = '[true]';
		elseif ($arData === null)
			$arData = '[null]';
		$strResult .= '<pre id="'.$strId.'">'.print_r($arData, true).'</pre>';
		print $strResult;
		if($bJust){
			die();
		}
	}
	
	/**
	 *	Log
	 */
	function L($mMessage, $strFilename=false){
		if(is_array($mMessage)) {
			$mMessage = print_r($mMessage,true);
		}
		$intTime = microtime(true);
		$strMicroTime = sprintf('%06d',($intTime - floor($intTime)) * 1000000);
		$obDate = new \DateTime(date('d.m.Y H:i:s.'.$strMicroTime, $intTime));
		$strTime = $obDate->format('d.m.Y H:i:s.u');
		if(!is_string($strFilename)) {
			if(defined('LOG_FILENAME') && strlen(LOG_FILENAME)) {
				$strFilename = LOG_FILENAME;
			}
			else {
				$strDir = Helper::getUploadDir('log', true);
				$strFilename = $strDir.Helper::getOption('main', 'server_uniq_id').'.txt';
			}
		}
		$resHandle = fopen($strFilename, 'a+');
		@flock($resHandle, LOCK_EX);
		fwrite($resHandle, '['.$strTime.'] '.$mMessage.PHP_EOL);
		@flock($resHandle, LOCK_UN);
		fclose($resHandle);
		unset($obDate, $resHandle, $intTime, $strMicroTime, $strTime);
	}
	
	/**
	 *	Turn debug mode on | off
	 */
	public static function D($bDebug=null){
		// Check mode (without parameters)
		if(is_null($bDebug)){
			return static::$bDebugMode;
		}
		// Default mode - true | false
		static::$bDebugMode = !!$bDebug;
	}
	
	/**
	 *	Set current memory consumption
	 */
	public static function startMemoryTest(){
		static::$fMemory = memory_get_usage();
	}
	
	/**
	 *	Get memory consumption from last static::setMemory()
	 */
	public static function getMemoryTest(){
		if(is_null(static::$fMemory)){
			static::$fMemory = 0;
		}
		return Helper::formatSize(memory_get_usage() - static::$fMemory);
	}
	
	/**
	 *	Remove all functions from array for debug output
	 */
	public static function debugRemoveFunctions(array $arData){
		foreach($arData as $key => $mItem){
			if(is_array($mItem)){
				$mItem = static::debugRemoveFunctions($mItem);
			}
			elseif(is_object($mItem)){
				$mItem = sprintf('[*** Object: %s ***]', get_class($mItem));
			}
			$arData[$key] = $mItem;
		}
		return $arData;
	}

}
