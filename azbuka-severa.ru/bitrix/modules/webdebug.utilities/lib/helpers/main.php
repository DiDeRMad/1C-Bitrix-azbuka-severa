<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Main {

	/**
	 *	Is site working in UTF-8
	 */
	public static function isUtf(){
		return defined('BX_UTF') && constant('BX_UTF') === true;
	}
	
	/**
	 *	Get document root
	 */
	public static function root(){
		return \Bitrix\Main\Loader::getDocumentRoot();
	}
	
	/**
	 *	Check if site works via HTTPS
	 */
	public static function isHttps() {
		return \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps();
	}
	
	/**
	 *	Check if current request is GET
	 */
	public static function isGet() {
		return toUpper(\Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->getRequestMethod()) == 'GET';
	}
	
	/**
	 *	Check if current request is POST
	 */
	public static function isPost() {
		return toUpper(\Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->getRequestMethod()) == 'POST';
	}
	
	/**
	 *	Check we are in admin section
	 */
	public static function isAdminSection() {
		return \Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->isAdminSection();
	}
	
	/**
	 *	Get $_SERVER array
	 */
	public static function getServer($strKey=null) {
		if(strlen($strKey)){
			return \Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->getServer()->get($strKey);
		}
		return \Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->getServer()->toArray();
	}
	
	/**
	 *	Check if current request is AJAX
	 */
	public static function isAjax() {
		return \Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->isAjaxRequest();
	}
	
	/**
	 *	Get current request url
	 */
	public static function getUrl() {
		return \Bitrix\Main\Application::GetInstance()->getContext()->getRequest()->getRequestUri();
	}

	/**
	 *	Get current domain (without port)
	 */
	public static function getCurrentDomain($bConvertToPuny=false){
		$strDomain = preg_replace('#:(\d+)$#', '', toLower(\Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost()));
		if($bConvertToPuny){
			$strDomain = \CBXPunycode::toUnicode($strDomain, $arEncodingErrors);
		}
		return $strDomain;
	}
	
	/**
	 *	Restart buffering
	 */
	public static function obRestart(){
		$GLOBALS['APPLICATION']->restartBuffer();
	}
	
	/**
	 *	Stop buffering
	 */
	public static function obStop(){
		while(ob_get_level()){
			ob_clean();
		}
	}
	
	/**
	 *	Is value empty?
	 */
	public static function isEmpty($mValue) {
		if(empty($mValue)){
			return true;
		}
		return false;
	}
	
	/**
	 *	Is managed cache on ?
	 */
	public static function isManagedCacheOn(){
		return Helper::getOption('main', 'component_managed_cache_on', 'N') != 'N' || defined('BX_COMP_MANAGED_CACHE');
	}
	
	/**
	 *	Replace \ to /
	 */
	public static function path($strPath){
		return str_replace('\\', '/', $strPath);
	}
	
	/**
	 *	Remove slashes at the end of text
	 */
	public static function removeTrailingBackslash($strText){
		return preg_replace('#[/]*$#', '', $strText);
	}
	
	/**
	 *	Get event handlers all
	 */
	public static function getEventHandlers($strModuleId, $strEvent){
		return \Bitrix\Main\EventManager::getInstance()->findEventHandlers($strModuleId, $strEvent);
	}
	
	/**
	 *	Get $_GET and $_POST
	 */
	public static function getRequestQuery(){
		return [
			\Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->toArray(),
			\Bitrix\Main\Context::getCurrent()->getRequest()->getPostList()->toArray(),
			\Bitrix\Main\Context::getCurrent()->getRequest()->getFileList()->toArray(),
			\Bitrix\Main\Context::getCurrent()->getRequest()->getCookieList()->toArray(),
		];
	}

	/**
	 *	Wrapper for unserialize(), but as array() anyway
	 */
	public static function unserialize($strValue, $arOptions=[]){
		$arResult = unserialize($strValue, $arOptions);
		return is_array($arResult) ? $arResult : [];
	}

	/**
	 *	Send prepared events from b_event
	 */
	public static function checkEvents(){
		$obManagedCache = \Bitrix\Main\Application::getInstance()->getManagedCache();
		if(CACHED_b_event !== false && $obManagedCache->read(CACHED_b_event, 'events')){
			return '';
		}
		return \Bitrix\Main\Mail\EventManager::executeEvents();
	}
	
	/**
	 *	Add agent (remove it first, if exists)
	 */
	public static function addAgent(array $arAgent){
		$strFunc = $arAgent['FUNC'];
		$strModuleId = is_string($arAgent['MODULE_ID']) ? $arAgent['MODULE_ID'] : '';
		$strPeriod = $arAgent['PERIOD'] == true ? 'Y' : 'N';
		$intInterval = is_numeric($arAgent['INTERVAL']) && $arAgent['INTERVAL'] ? $arAgent['INTERVAL'] : 24*60*60;
		$strNextExec = (\Bitrix\Main\Type\DateTime::createFromTimestamp(time() + $intInterval - 60))->toString();
		$intSort = is_numeric($arAgent['SORT']) && $arAgent['SORT'] > 0 ? $arAgent['SORT'] : 100;
		static::removeAgent($arAgent);
		return \CAgent::addAgent($strFunc, $strModuleId, $strPeriod, $intInterval, '', 'Y', $strNextExec, $intSort);
	}
	
	/**
	 *	Remove agent
	 */
	public static function removeAgent(array $arAgent){
		return \CAgent::removeAgent($arAgent['FUNC'], is_string($arAgent['MODULE_ID']) ? $arAgent['MODULE_ID'] : '');
	}
	
	/**
	 *	Get Bitrix license hash (md5)
	 */
	public static function getLicenseHash(){
		$strLicense = defined('LICENSE_KEY') ? LICENSE_KEY : '';
		if(!strlen($strLicense)){
			require($_SERVER['DOCUMENT_ROOT'].'/bitrix/license_key.php');
			$strLicense = $LICENSE_KEY;
		}
		return md5(sprintf('BITRIX%sLICENCE', $strLicense));
	}

}
