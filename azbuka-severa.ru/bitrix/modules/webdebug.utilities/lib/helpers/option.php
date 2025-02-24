<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Option {
	
	/**
	 *	Get option value
	 */
	public static function getOption($strModuleId, $strOption, $mDefaultValue=null){
		return \Bitrix\Main\Config\Option::get($strModuleId, $strOption, $mDefaultValue);
	}
	
	/**
	 *	Set option value
	 */
	public static function setOption($strModuleId, $strOption, $mValue){
		return \Bitrix\Main\Config\Option::set($strModuleId, $strOption, $mValue);
	}
	
	/**
	 *	Remove single option
	 */
	public static function removeOption($strModuleId, $strOption){
		return \Bitrix\Main\Config\Option::delete($strModuleId, ['name' => $strOption]);
	}
	
	/**
	 *	Remove all options
	 */
	public static function removeAllOptions($strModuleId){
		return \Bitrix\Main\Config\Option::delete($strModuleId);
	}
	
	/**
	 *	Get default options for module
	 */
	public static function getDefaults($strModuleId){
		return \Bitrix\Main\Config\Option::getDefaults($strModuleId);
	}
	
	/**
	 *	Get user option value
	 */
	public static function getUserOption($strModuleId, $strOption, $mDefaultValue=null){
		return \CUserOptions::getOption($strModuleId, $strOption, $mDefaultValue);
	}
	
	/**
	 *	Set user option value
	 */
	public static function setUserOption($strModuleId, $strOption, $mValue){
		return \CUserOptions::setOption($strModuleId, $strOption, $mValue);
	}

}
