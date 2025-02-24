<?
namespace WD\Utilities;

use
	 \WD\Utilities\Helper,
	 \WD\Utilities\JsHelper;

Helper::loadMessages();

/**
 * Class HttpHeader
 * @package WD\Utilities
 */
class HttpHeader {
	
	const SKIP_KEY = 'wdu_skip_headers';
	
	/**
	 *	Remove headers, add headers
	 */
	public static function processHeaders(){
		static::removeHeaders();
		static::addHeaders();
	}
	
	/**
	 *	Remove headers
	 */
	public static function removeHeaders(){
		if(static::skip()){
			return;
		}
		$strHeaders = Helper::getOption(WDU_MODULE, 'server_headers_remove');
		if(is_string($strHeaders) && strlen($strHeaders)){
			if(is_array($arHeaders = unserialize($strHeaders))){
				foreach($arHeaders as $strHeader){
					if(strlen($strHeader)){
						header_remove($strHeader);
					}
				}
			}
		}
	}
	
	/**
	 *	Add headers
	 */
	public static function addHeaders(){
		if(static::skip()){
			return;
		}
		$arHeaders = Helper::getOption(WDU_MODULE, 'server_headers_add');
		if(strlen($arHeaders)){
			$arHeaders = unserialize($arHeaders);
		}
		if(is_array($arHeaders)){
			foreach($arHeaders as $strHeader){
				if(strlen($strHeader)){
					header($strHeader);
				}
			}
		}
	}
	
	/**
	 *	Check to skip set/delete headers
	 */
	public static function skip(){
		$bResult = false;
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/'.static::SKIP_KEY.'.txt')){
			$bResult = true;
		}
		elseif(isset($_GET[static::SKIP_KEY]) && $_GET[static::SKIP_KEY] == 'Y' || isset($_COOKIE[static::SKIP_KEY]) && $_COOKIE[static::SKIP_KEY] == 'Y'){
			if(is_object($GLOBALS['USER'])){
				$bResult = $GLOBALS['USER']->isAdmin();
			}
			else{
				$obUser = new \CUser;
				$bResult = $obUser->isAdmin();
				unset($obUser);
			}
		}
		return $bResult;
	}
	
}
