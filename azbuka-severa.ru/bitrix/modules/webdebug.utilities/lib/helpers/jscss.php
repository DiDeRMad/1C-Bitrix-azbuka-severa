<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class JsCss {
	
	/**
	 *	jQuery select2
	 */
	public static function addJsSelect2(){
		$strLangFile = Helper::isUtf() ? 'ru_utf8.js' : 'ru_cp1251.js';
		\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/'.WDU_MODULE.'/jquery.select2/dist/js/select2.min.js');
		\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/'.WDU_MODULE.'/jquery.select2/'.$strLangFile);
		\Bitrix\Main\Page\Asset::getInstance()->addJs('/bitrix/js/'.WDU_MODULE.'/jquery.select2/select2.wdu.js');
		$GLOBALS['APPLICATION']->setAdditionalCss('/bitrix/js/'.WDU_MODULE.'/jquery.select2/dist/css/select2.css');
		$GLOBALS['APPLICATION']->setAdditionalCss('/bitrix/js/'.WDU_MODULE.'/jquery.select2/select2.wdu.css');
	}

}
