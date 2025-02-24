<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Number {
	
	const TEXT_PRODUCT = 'PRODUCT';
	const TEXT_OFFER = 'OFFER';
	const TEXT_ELEMENT = 'ELEMENT';
	const TEXT_SECTION = 'SECTION';
	const TEXT_CATEGORY = 'CATEGORY';
	const TEXT_IBLOCK = 'IBLOCK';
	const TEXT_PROPERTY = 'PROPERTY';
	const TEXT_CHARACTERISTICS = 'CHARACTERISTICS';
	const TEXT_TYPE = 'TYPE';
	const TEXT_USER = 'USER';
	const TEXT_CLIENT = 'CLIENT';
	const TEXT_ERROR = 'ERROR';
	const TEXT_REQUEST = 'REQUEST';
	const TEXT_INDEX = 'INDEX';
	const TEXT_BLOCK = 'BLOCK';
	const TEXT_SET_1 = 'SET_1';
	const TEXT_SET_2 = 'SET_2';
	#
	const TEXT_SITE = 'SITE';
	const TEXT_PAGE = 'PAGE';
	const TEXT_REVIEW = 'REVIEW';
	const TEXT_COMMENT = 'COMMENT';
	const TEXT_ANSWER = 'ANSWER';
	const TEXT_QUESTION = 'QUESTION';
	const TEXT_COMPONENT = 'COMPONENT';
	const TEXT_MODULE = 'MODULE';
	const TEXT_ENTITY = 'ENTITY';
	const TEXT_OBJECT = 'OBJECT';
	const TEXT_RECORD = 'RECORD';
	const TEXT_FIELD = 'FIELD';
	const TEXT_ROW = 'ROW';
	const TEXT_COLUMN = 'COLUMN';
	const TEXT_CELL = 'CELL';
	const TEXT_SYMBOL = 'SYMBOL';
	const TEXT_LETTER = 'LETTER';
	const TEXT_SIGN = 'SIGN';
	const TEXT_ARTICLE = 'ARTICLE';
	const TEXT_NEWS = 'NEWS';
	#
	const TEXT_NOTICE = 'NOTICE';
	const TEXT_MESSAGE = 'MESSAGE';
	const TEXT_NOTE = 'NOTE';
	const TEXT_ITEM = 'ITEM';
	const TEXT_ORDER = 'ORDER';
	const TEXT_PAYMENT = 'PAYMENT';
	const TEXT_COUPON = 'COUPON';
	const TEXT_BONUS = 'BONUS';
	const TEXT_SERVICE = 'SERVICE';
	const TEXT_ADS = 'ADS';
	const TEXT_APP = 'APP';
	const TEXT_PROMO = 'PROMO';
	const TEXT_PARTNER = 'PARTNER';
	const TEXT_PARTNER_1 = 'PARTNER_1';
	const TEXT_SHOP = 'SHOP';
	const TEXT_OFFICE = 'OFFICE';
	const TEXT_POINT = 'POINT';
	const TEXT_PHONE = 'PHONE';
	const TEXT_NUMBER = 'NUMBER';
	const TEXT_ACCOUNT = 'ACCOUNT';
	const TEXT_BILL = 'BILL';
	const TEXT_BILL_1 = 'BILL_1';
	const TEXT_FILTER = 'FILTER';
	const TEXT_FILE = 'FILE';
	const TEXT_FOLDER = 'FOLDER';
	const TEXT_DIRECTORY = 'DIRECTORY';
	#
	const TEXT_LINK = 'LINK';
	const TEXT_TABLE = 'TABLE';
	const TEXT_IMAGE = 'IMAGE';
	#
	const TEXT_ACCESSORIES = 'ACCESSORIES';
	const TEXT_SIMILAR = 'SIMILAR';
	const TEXT_SIMILAR_PRODUCT = 'SIMILAR_PRODUCT';
	#
	const TEXT_SECOND = 'SECOND';
	const TEXT_MINUTE = 'MINUTE';
	const TEXT_HOUR = 'HOUR';
	const TEXT_DAY = 'DAY';
	const TEXT_WEEK = 'WEEK';
	const TEXT_MONTH = 'MONTH';
	const TEXT_YEAR = 'YEAR';
	#
	const TEXT_THING = 'THING';
	const TEXT_RUBLE = 'RUBLE';
	const TEXT_DOLLAR = 'DOLLAR';
	#
	const TEXT_PIXEL = 'PIXEL';
	const TEXT_MM = 'MM';
	const TEXT_CM = 'CM';
	const TEXT_M = 'M';
	const TEXT_KM = 'KM';
	#
	const TEXT_G = 'G';
	const TEXT_KG = 'KG';
	#
	const TEXT_PC = 'PC';
	
	
	/**
	 *	Word form for russian (1 tevelizor, 2 tevelizora, 5 tevelizorov)
	 */
	public static function numberText($intValue, $mWords, $bShowValue=true) {
		$intValue = intVal($intValue);
		$strValue = (string)$intValue;
		$strResult = null;
		if(!is_array($mWords)){
			$mWords = static::getNumberTextValues($mWords);
		}
		$strForm1 = $mWords[0];
		$strForm2 = $mWords[1];
		$strForm5 = $mWords[2];
		$strLastSymbol = substr($strValue, -1);
		$strSubLastSymbol = substr($strValue, -2, 1);
		if(strlen($strValue) >= 2 && $strSubLastSymbol == '1') {
			$strResult = $strForm5;
		}
		else {
			if($strLastSymbol == '1')
				$strResult= $strForm1;
			elseif ($strLastSymbol >= 2 && $strLastSymbol <= 4)
				$strResult = $strForm2;
			else
				$strResult = $strForm5;
		}
		if($bShowValue){
			$strResult = sprintf('%d %s', $strValue, $strResult);
		}
		return $strResult;
	}
	
	/**
	 *	Get array for static::numberText()
	 */
	public static function getNumberTextValues($strType){
		$arResult = [];
		$strLang = 'WDU_NUMBER_TEXT_';
		if(strlen($strType) && ($strMessage = Helper::getMessage($strLang.$strType))){
			$arResult = Helper::splitCommaValues($strMessage);
		}
		else{
			$arResult = ['', '', ''];
		}
		return $arResult;
	}
	
	/**
	 *	Format size (kilobytes, megabytes, ...)
	 */
	public static function formatSize($intSize, $intPrecision=2, $bRussian=false){
		$bNegative = $intSize < 0;
		if($bNegative){
			$intSize = abs($intSize);
		}
		$arLabels = Helper::splitCommaValues(Helper::getMessage('WDU_NUMBER_FORMAT_SIZE_'.($bRussian ? 'RU' : 'EN')));
		$intPos = 0;
		while($intSize >= 1024 && $intPos < 4){
			$intSize /= 1024;
			$intPos++;
		}
		$strResult = round($intSize, $intPrecision).' '.$arLabels[$intPos];
		if($intPos > 0) {
			// replace '2 Mb' to '2.00 Mb'
			$strResult = preg_replace('#^([\d]+)[\s]#', '$1.00 ', $strResult);
			// replace '2.1 Mb' to '2.10 Mb'
			$strResult = preg_replace('#^([\d]+)\.([\d]{1})[\s]#', '${1}.${2}0 ', $strResult);
		}
		# Negative
		if($bNegative){
			$strResult = sprintf('-%s', $strResult);
		}
		return $strResult;
	}
	
	/**
	 *	Round numeric value
	 */
	public static function roundEx($fValue, $intPrecision=0, $strFunc=null) {
		$intPow = pow(10, $intPrecision);
		$strFunc = in_array($strFunc, ['round', 'floor', 'ceil']) ? $strFunc : 'round';
		return call_user_func($strFunc, $fValue * $intPow) / $intPow;
	}
	
	/**
	 *	Format elapsed time from 121 to 2:01
	 */
	public static function formatElapsedTime($intSeconds){
		$strResult = '';
		if(is_numeric($intSeconds)){
			$intHours = floor($intSeconds / (60*60));
			$intSeconds -= $intHours * 60 * 60;
			$intMinutes = floor($intSeconds / 60);
			$intMinutes = sprintf('%02d', $intMinutes);
			if($intMinutes > 0) {
				$intSeconds = $intSeconds - $intMinutes * 60;
			}
			$intSeconds = sprintf('%02d', $intSeconds);
			$strResult = ($intHours ? $intHours.':' : '').$intMinutes.':'.$intSeconds;
		}
		return $strResult;
	}

	/**
	 *	Convert '10.075' and '10,075' to '10.075' (or '10,075' considering of locale settings)
	 */
	public static function convertDecimalPoint($strFloatValue){
		$arReplace = ['.' => ','];
		$arLocale = localeConv();
		$strPoint = $arLocale['decimal_point'];
		if($strPoint == '.'){
			$arReplace = array_flip($arReplace);
		}
		return str_replace(array_keys($arReplace), array_values($arReplace), $strFloatValue);
	}

}
