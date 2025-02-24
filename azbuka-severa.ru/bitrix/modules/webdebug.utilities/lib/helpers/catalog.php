<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Catalog {
	
	protected static $arCacheCatalogArray = [];

	/**
	 *	CCatalog::GetById with tmp cache
	 */
	public static function getCatalogArray($intIBlockId) {
		$intIBlockId = IntVal($intIBlockId);
		if($intIBlockId > 0) {
			$arCatalog = &static::$arCacheCatalogArray[$intIBlockId];
			if(Helper::isNonEmptyArray($arCatalog)){
				return $arCatalog;
			}
			elseif(\Bitrix\Main\Loader::includeModule('catalog')) {
				$arCatalog = \CCatalog::getByID($intIBlockId);
				if(Helper::isNonEmptyArray($arCatalog)) {
					return $arCatalog;
				}
				else { // Каталог теперь может не быть торговым каталогом, но может иметь торговые предложения
					$resCatalogs = \CCatalog::getList([], ['PRODUCT_IBLOCK_ID' => $intIBlockId]);
					if($arCatalog = $resCatalogs->getNext(false, false)){
						if(\Bitrix\Main\Loader::includeModule('iblock')) {
							$resIBlock = \CIBlock::getList([], ['ID' => $intIBlockId]);
							if($arIBlock = $resIBlock->getNext()) {
								$arResult = [
									'IBLOCK_ID' => $intIBlockId,
									'YANDEX_EXPORT' => 'N',
									'SUBSCRIPTION' => 'N',
									'VAT_ID' => 0,
									'PRODUCT_IBLOCK_ID' => 0,
									'SKU_PROPERTY_ID' => 0,
									'ID' => $intIBlockId,
									'IBLOCK_TYPE_ID' => $arIBlock['IBLOCK_TYPE_ID'],
									'LID' => $arIBlock['LID'],
									'NAME' => $arIBlock['NAME'],
									'OFFERS_IBLOCK_ID' => $arCatalog['IBLOCK_ID'],
									'OFFERS_PROPERTY_ID' => $arCatalog['SKU_PROPERTY_ID'],
									'OFFERS' => 'N',
								];
								return $arResult;
							}
						}
					}
				}
			}
		}
		return false;
	}
	
	/**
	 *	Is stores available?
	 */
	public static function isCatalogStoresAvailable(){
		return \Bitrix\Main\Loader::includeModule('catalog') && class_exists('\CCatalogStoreProduct');
	}
	
	/**
	 *	Is barcode available?
	 */
	public static function isCatalogBarcodeAvailable(){
		return \Bitrix\Main\Loader::includeModule('catalog') && class_exists('\CCatalogStoreBarCode');
	}

}
