<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Date {
	
	/**
	 *	Format date interval (example: «from 27 to 28 june 2020» or «from 10 may to 10 june 2020»)
	 */
	public static function formatDateInterval($strDateActiveFrom, $strDateActiveTo=false){
		static $arMonth;
		if(is_null($arMonth)){
			$strMonth = Helper::getMessage('WDU_DATE_MONTHS_RU');
			$arMonth = explode(', ', $strMonth);
			array_unshift($arMonth, true);
			unset($arMonth[0]);
		}
		#
		$obDateFrom = new \Bitrix\Main\Type\DateTime($strDateActiveFrom);
		$intDateFromD = $obDateFrom->format('j');
		$intDateFromM = $obDateFrom->format('n');
		$intDateFromY = $obDateFrom->format('Y');
		$strDateFromMonth = $arMonth[$intDateFromM];
		unset($obDateFrom);
		#
		$bDateTo = !!strlen($strDateActiveTo);
		if($bDateTo){
			$obDateTo = new \Bitrix\Main\Type\DateTime($strDateActiveTo);
			$intDateToD = $obDateTo->format('j');
			$intDateToM = $obDateTo->format('n');
			$intDateToY = $obDateTo->format('Y');
			$strDateToMonth = $arMonth[$intDateToM];
			unset($obDateTo);
		}
		#
		$strResult = Helper::getMessage('WDU_DATE_FROM').' '.$intDateFromD;
		if(!$bDateTo || $intDateFromM != $intDateToM){
			$strResult .= ' '.$strDateFromMonth;
		}
		if(!$bDateTo || $intDateFromY != $intDateToY){
			$strResult .= ' '.$intDateFromY.' '.Helper::getMessage('WDU_DATE_YEAR');
		}
		if($bDateTo){
			$strResult = sprintf('%s %s %s %s %s %s', $strResult, Helper::getMessage('WDU_DATE_TO'), $intDateToD, 
				$strDateToMonth, $intDateToY, Helper::getMessage('WDU_DATE_YEAR'));
			/*
			$strResult .= ' '.Helper::getMessage('WDU_DATE_TO');
			$strResult .= ' '.$intDateToD;
			$strResult .= ' '.$strDateToMonth;
			$strResult .= ' '.$intDateToY.' '.Helper::getMessage('WDU_DATE_YEAR');
			*/
		}
		return $strResult;
	}

}
