<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class ArrayUtil {
	
	# Constants
	const ARRAY_INSERT_BEGIN = '_ARRAY_INSERT_BEGIN_';
	const ARRAY_INSERT_AFTER = '_ARRAY_INSERT_AFTER_';
	const ARRAY_INSERT_BEFORE = '_ARRAY_INSERT_BEFORE_';

	/**
	 *	Is array associative?
	 */
	public static function isArrayAssociative(array $arData, $bDefaultResult=true){
		if(empty($arData)){
			return $bDefaultResult;
		}
    return array_keys($arData) !== range(0, count($arData) - 1);
	}
	
	/**
	 *	Is array and not empty?
	 */
	public static function isNonEmptyArray($mValue) {
		if(is_array($mValue) && !empty($mValue)){
			return true;
		}
		return false;
	}
	
	/**
	 *	Insert new key into array (in a selected place)
	 */
	public static function arrayInsert(array &$arData, $strKey, $mItem, $strAfter=null, $strBefore=null){
		$bSuccess = false;
		if($strAfter === static::ARRAY_INSERT_BEGIN) {
			$bSuccess = true;
			$arData = array_merge(array($strKey => $mItem), $arData);
		}
		elseif(!is_null($strAfter)) {
			$intIndex = 0;
			foreach($arData as $key => $value){
				$intIndex++;
				if($key === $strAfter){
					$bSuccess = true;
					$arBefore = array_slice($arData, 0, $intIndex, true);
					$arAfter = array_slice($arData, $intIndex, null, true);
					$arData = array_merge($arBefore, [$strKey => $mItem], $arAfter);
					unset($arBefore, $arAfter);
					break;
				}
			}
		}
		elseif(!is_null($strBefore)) {
			$intIndex = 0;
			foreach($arData as $key => $value){
				if($key === $strBefore){
					$bSuccess = true;
					$arBefore = array_slice($arData, 0, $intIndex, true);
					$arAfter = array_slice($arData, $intIndex, null, true);
					$arData = array_merge($arBefore, [$strKey => $mItem], $arAfter);
					unset($arBefore, $arAfter);
					break;
				}
				$intIndex++;
			}
		}
		if(!$bSuccess){
			$arData[$strKey] = $mItem;
		}
	}
	
	/**
	 *	Remove empty values from array (check by strlen(trim()))
	 */
	public static function arrayRemoveEmptyValues(&$arValues, $bTrim=true) {
    foreach($arValues as $key => $value){
			if($bTrim && !strlen(trim($value)) || !$bTrim && !strlen($value)){
				unset($arValues[$key]);
			}
		}
	}
	
	/**
	 *	Remove empty values from array (check by strlen(trim()))
	 */
	public static function arrayRemoveEmptyValuesRecursive(&$arValues) {
    foreach($arValues as $key => $value){
			if(is_array($value)){
				static::arrayRemoveEmptyValuesRecursive($arValues[$key]);
			}
			else{
				if(!strlen(trim($value))){
					unset($arValues[$key]);
				}
			}
		}
	}
	
	/**
	 *	Exec custom action for each element of array (or single if it is not array)
	 */
	public static function execAction($arData, $callbackFunction, $arParams=false){
		if(is_array($arData)) {
			foreach($arData as $Key => $arItem){
				$arData[$Key] = $callbackFunction($arItem, $arParams);
			}
		}
		else {
			$arData = $callbackFunction($arData, $arParams);
		}
		return $arData;
	}
	
	/**
	 *	Get first non-empty value
	 */
	public static function getFirstValue($arValues, $bInteger=false){
		foreach($arValues as $mValue){
			if($bInteger){
				$mValue = intVal($mValue);
				if($mValue > 0){
					return $mValue;
				}
			}
			elseif(strlen($mValue)){
				return $mValue;
			}
		}
		return false;
	}

}
