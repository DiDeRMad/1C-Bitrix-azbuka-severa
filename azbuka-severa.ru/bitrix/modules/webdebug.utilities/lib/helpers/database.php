<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Database {
	
	/**
	 *	Prepare string for use in SQL
	 */
	public static function forSql($strValue){
		return \Bitrix\Main\Application::getConnection()->getSqlHelper()->forSql($strValue);
		return $GLOBALS['DB']->forSql($strValue);
	}

	/**
	 *	SQL-query
	 */
	public static function query($strSql){
		return \Bitrix\Main\Application::getConnection()->query($strSql);
	}

	/**
	 *	Transaction: start
	 */
	public static function startTransaction(){
		return \Bitrix\Main\Application::getConnection()->startTransaction();
	}

	/**
	 *	Transaction: commit (apply)
	 */
	public static function commitTransaction(){
		return \Bitrix\Main\Application::getConnection()->commitTransaction();
	}

	/**
	 *	Transaction: rollback (cancel)
	 */
	public static function rollbackTransaction(){
		return \Bitrix\Main\Application::getConnection()->rollbackTransaction();
	}
	
	/**
	 *	Get database table size
	 */
	public static function getTableSize($mTable=false){
		$arResult = [];
		$strTableSql = '';
		if(!empty($mTable)){
			$arTable = is_array($mTable) ? $mTable : [$mTable];
			$strTableSql = array_map(function($item){return "'{$item}'";}, $arTable);
			$strTableSql = implode(', ', $strTableSql);
			$strTableSql = 'AND TABLE_NAME IN ('.$strTableSql.')';
		}
		$strSql = "
			SELECT
				TABLE_NAME AS 'TABLE',
				DATA_LENGTH+INDEX_LENGTH AS 'SIZE',
				DATA_LENGTH,
				INDEX_LENGTH,
				TABLE_ROWS AS 'ROWS'
			FROM
				INFORMATION_SCHEMA.tables
			WHERE
				TABLE_SCHEMA=DATABASE() {$strTableSql}
			ORDER BY
				SIZE DESC;";
		$resItems =  static::query($strSql);
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['TABLE']] = [
				'SIZE' => $arItem['SIZE'],
				'ROWS' => $arItem['ROWS'],
			];
		}
		if(is_string($mTable) && Helper::strlen($mTable)){
			$arResult = $arResult[$mTable];
		}
		return $arResult;
	}

	/**
	 *	Get table fields with extended data
	 */
	public static function getTableFields($strTableName){
		$arFields = [];
		$strSql = "
			SELECT
				COLUMN_NAME, IS_NULLABLE, UPPER(COLUMN_TYPE) as COLUMN_TYPE, UPPER(DATA_TYPE) as DATA_TYPE, 
				CHARACTER_MAXIMUM_LENGTH as LENGTH, NUMERIC_PRECISION, COLUMN_KEY
			FROM
				INFORMATION_SCHEMA.COLUMNS 
			WHERE
				table_schema=DATABASE()
			AND
				TABLE_NAME = '{$strTableName}';
		";
		$resFields = static::query($strSql);
		while($arField = $resFields->fetch()){
			$arFields[$arField['COLUMN_NAME']] = $arField;
		}
		return $arFields;
	}

	/**
	 *	Get table fields for selected ORM class
	 */
	public static function getEntityFields($strClass){
		$arFields = static::getTableFields($strClass::getTableName());
		$arFieldTitles = array_map(function($obField){
			return $obField->getTitle();
		}, $strClass::getMap());
		foreach($arFieldTitles as $strField => $strTitle){
			if($arFields[$strField]){
				$arFields[$strField]['TITLE'] = $strTitle;
			}
		}
		return $arFields;
	}
	
}
