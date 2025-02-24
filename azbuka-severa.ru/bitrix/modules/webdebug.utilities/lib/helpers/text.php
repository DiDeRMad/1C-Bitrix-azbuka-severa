<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper,
	\WD\Utilities\HttpRequest;

Helper::loadMessages();

final class Text {
	
	const RAND_ID_PREFIX = 'rand_id_';
	
	/**
	 *	Split 1, 2, 3 => [1, 2, 3]
	 */
	public static function splitCommaValues($strValues, $bDeleteEmpty=true){
		$arResult = preg_split('#,[\s]*#', trim($strValues));
		if($bDeleteEmpty){
			foreach($arResult as $key => $strValue){
				if(!strlen(trim($strValue))){
					unset($arResult[$key]);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Split 1 2 3 => [1, 2, 3]
	 */
	public static function splitSpaceValues($strValues, $bDeleteEmpty=true){
		$arResult =  preg_split('#\s+#s', trim($strValues));
		if($bDeleteEmpty){
			foreach($arResult as $key => $strValue){
				if(!strlen(trim($strValue))){
					unset($arResult[$key]);
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get rand string (32 chars)
	 */
	public static function randString($bWithPrefix=false){
		return ($bWithPrefix === true ? static::RAND_ID_PREFIX : '').'a'.substr(md5(randString(16).microtime(true)), 1);
	}
	
	/**
	 *	Translate ru -> en [by Yandex]
	 *	Need the key in main module's settings
	 */
	public static function translate($strText){
		$strResult = $strText;
		$strKey = Helper::getOption('main', 'translate_key_yandex', null);
		$strLang = 'ru-en';
		$strClientId = 'bitrix';
		if(!Helper::isUtf()){
			$strText = Helper::convertEncoding($strText, 'CP1251', 'UTF-8');
		}
		$strText = urlencode($strText);
		$strUrl = sprintf('https://translate.yandex.net/api/v1.5/tr.json/translate?key=%s&lang=%s&clientId=%s&text=%s',
			$strKey, $strLang, $strClientId, $strText);
		$strResponse = HttpRequest::getHttpContent($strUrl);
		if(strlen($strResponse)){
			print $strResponse;
			$arResponse = \Bitrix\Main\Web\Json::decode($strResponse);
			if(is_array($arResponse)){
				$strResult = reset($arResponse['text']);
			}
		}
		return $strResult;
	}
	
	/**
	 *	Convert myTestValue to my_test_value
	 */
	public static function toUnderlineCase($strText, $bUpper=true){
		$strText = preg_replace('#([a-z]{1})([A-Z]{1})#', '$1_$2', $strText);
		if($bUpper){
			$strText = toUpper($strText);
		}
		else{
			$strText = toLower($strText);
		}
		return $strText;
	}
	
	/**
	 *	Convert my_test_value to myTestValue
	 */
	public static function toCamelCase($strText){
		return preg_replace_callback('#_([A-z])#', function($arMatch){
			return toUpper($arMatch[1]);
		}, toLower($strText));
	}
	
	/**
	 *	Convert first symbol to upper case
	 */
	public static function ucFirst($strText){
		return Helper::strToUpper(Helper::substr($strText, 0, 1)).Helper::substr($strText, 1);
	}

}
