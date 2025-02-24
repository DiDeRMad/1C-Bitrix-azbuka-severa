<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Encoding {

	/**
	 * Convert charset (CP1251->UTF-8 || UTF-8->CP1251)
	 */
	public static function convertEncoding($mText, $strFrom='UTF-8', $strTo='CP1251') {
		$error = '';
		if(is_array($mText)) {
			foreach($mText as $key => $value){
				$mText[$key] = static::convertEncoding($value, $strFrom, $strTo);
			}
		}
		else {
			$mText = \Bitrix\Main\Text\Encoding::convertEncoding($mText, $strFrom, $strTo, $error);
		}
		return $mText;
	}
	
	/**
	 * Convert charset from site charset to specified charset
	 */
	public static function convertEncodingTo($mText, $strTo) {
		if(strlen($strTo)){
			$strFrom = Helper::isUtf() ? 'UTF-8' : 'CP1251';
			$strTo = ToLower($strTo) == 'windows-1251' ? 'CP1251' : $strTo;
			if($strTo != $strFrom){
				$mText = static::convertEncoding($mText, $strFrom, $strTo);
			}
		}
		return $mText;
	}
	
	/**
	 * Convert charset from specified charset to site charset
	 */
	public static function convertEncodingFrom($mText, $strFrom) {
		if(strlen($strFrom)){
			$strFrom = ToLower($strFrom) == 'windows-1251' ? 'CP1251' : $strFrom;
			$strTo = Helper::isUtf() ? 'UTF-8' : 'CP1251';
			if($strFrom != $strTo){
				$mText = static::convertEncoding($mText, $strFrom, $strTo);
			}
		}
		return $mText;
	}

}
