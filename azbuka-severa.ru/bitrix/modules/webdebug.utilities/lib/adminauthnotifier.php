<?
namespace WD\Utilities;

use
	 \WD\Utilities\Helper;

Helper::loadMessages();

/**
 * Class AdminAuthNotifier
 * @package WD\Utilities
 */
class AdminAuthNotifier {

	protected static $arUser = [];
	
	/**
	 * Handler 'onBeforeProlog'
	 */
	public static function onBeforeProlog(){
		if(!isset($_SESSION['WDU_ADMIN_AUTH']['REFERER'])){
			$_SESSION['WDU_ADMIN_AUTH']['REFERER'] = $_SERVER['HTTP_REFERER'];
		}
		if($strSessionId = session_id()){
			if($_SESSION['WDU_ADMIN_AUTH']['ID'] != $strSessionId){
				$_SESSION['WDU_ADMIN_AUTH']['ID'] = $strSessionId;
				$_SESSION['WDU_ADMIN_AUTH']['DATE'] = (new \Bitrix\Main\Type\Datetime)->toString();
			}
		}
	}

	/**
	 * Handler 'OnAfterUserAuthorize'
	 */
	public static function OnAfterUserAuthorize($arUser){
		global $USER;
		if(Helper::getOption(WDU_MODULE, 'admin_auth_notify') == 'Y'){
			if(is_object($USER) && get_class($USER) == 'CUser' && $USER->isAdmin()){
				if(!static::isLoginByCookies()){
					static::$arUser = $arUser['user_fields'];
					if(!static::$arUser){
						if($intUserId = $USER->getId()){
							static::$arUser = $USER->getById($intUserId)->fetch();
						}
					}
					static::sendEmail();
					static::sendHttpRequest();
				}
			}
		}
	}

	/**
	 * Check login from cookie [restore auth]
	 */
	protected static function isLoginByCookies(){
		$fromCookie = array_filter(array_map(function($item){
			return $item['class'] == 'CAllUser' && $item['function'] == 'LoginByCookies';
		}, debug_backtrace(2)));
		return is_array($fromCookie) && !empty($fromCookie);
	}

	/**
	 * Do send email
	 */
	protected static function sendEmail(){
		$bResult = false;
		$arEmails = Helper::splitCommaValues(Helper::getOption(WDU_MODULE, 'admin_auth_email'));
		foreach($arEmails as $strEmail){
			$arFields = [
				'EMAIL_TO' => $strEmail,
				'SITE_ID' => SITE_ID,
				'USER_ID' => static::$arUser['ID'],
				'LOGIN' => static::$arUser['LOGIN'],
				'NAME' => static::$arUser['NAME'].' '.static::$arUser['LAST_NAME'],
				'EMAIL' => static::$arUser['EMAIL'],
				'IP' => $_SERVER['REMOTE_ADDR'],
				'URL' => (Helper::isHttps() ? 'http://' : 'http://').Helper::getCurrentDomain().$_SERVER['REQUEST_URI'],
				'USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
				'REFERER' => isset($_SESSION['WDU_ADMIN_AUTH']['REFERER']) ? $_SESSION['WDU_ADMIN_AUTH']['REFERER']
					: $_SERVER['HTTP_REFERER'],
				'DATETIME' => (new \Bitrix\Main\Type\Datetime)->toString(),
				'SERVER' => static::getServer(),
				'TRACE' => static::getTrace(5),
			];
			$arEvent = [
				'EVENT_NAME' => 'WDU_ADMIN_AUTH',
				'C_FIELDS' => $arFields,
				'LID' => Helper::getSiteByDomain(),
			];
			if(\Bitrix\Main\Mail\Event::send($arEvent)->isSuccess()){
				Helper::checkEvents();
				$bResult = true;
			}
		}
		return $bResult;
	}

	/**
	 * Get $_SERVER for email
	 */
	protected static function getServer(){
		$arServer = $_SERVER;
		array_walk($arServer, function(&$item, $key){
			if($key == 'SERVER_SIGNATURE'){
				$item = strip_tags($item);
			}
			$item = sprintf('%s: %s', $key, is_array($item) ? print_r($item, true) : trim($item));
		});
		return implode(PHP_EOL, array_values($arServer));
	}

	/**
	 * Get trace for email
	 */
	protected static function getTrace($skip=null){
		$trace = debug_backtrace(2);
		if($skip > 0){
			$trace = array_slice($trace, $skip);
		}
		$traceLines = array();
		foreach ($trace as $traceNum => $traceInfo){
			$traceLine = '';
			if (array_key_exists('class', $traceInfo)){
				$traceLine .= $traceInfo['class'].$traceInfo['type'];
			}
			if (array_key_exists('function', $traceInfo)){
				$traceLine .= $traceInfo['function'].'()';
			}
			if (array_key_exists('file', $traceInfo)){
				$traceLine .= ' '.$traceInfo['file'];
				if (array_key_exists('line', $traceInfo))
					$traceLine .= ':'.$traceInfo['line'];
			}
			if ($traceLine){
				$traceLines[] = $traceLine;
			}
		}
		return implode("\n", $traceLines);
	}

	/**
	 * Send custom HTTP-request
	 */
	protected static function sendHttpRequest(){
		if(Helper::strlen($strUrl = Helper::getOption(WDU_MODULE, 'admin_auth_http_request'))){
			$arMacro = [
				'SUBJECT' => Helper::getMessage($strLang.'HTTP_TITLE'),
				'MESSAGE' => Helper::getMessage($strLang.'HTTP_CONTENT', [
					'#ID#' => static::$arUser['ID'],
					'#LOGIN#' => static::$arUser['LOGIN'],
					'#NAME#' => static::$arUser['NAME'].' '.static::$arUser['LAST_NAME'],
					'#EMAIL#' => static::$arUser['EMAIL'],
					'#IP#' => $_SERVER['REMOTE_ADDR'],
					'#SITE_ID#' => SITE_ID,
					'#URL#' => (Helper::isHttps() ? 'http://' : 'http://').Helper::getCurrentDomain().$_SERVER['REQUEST_URI'],
					'#USER_AGENT#' => $_SERVER['HTTP_USER_AGENT'],
					'#REFERER#' => isset($_SESSION['WDU_ADMIN_AUTH']['REFERER']) ? $_SESSION['WDU_ADMIN_AUTH']['REFERER']
						: $_SERVER['HTTP_REFERER'],
					'#DATETIME#' => (new \Bitrix\Main\Type\Datetime)->toString(),
				]),
				'USER_ID' => static::$arUser['ID'],
				'USER_LOGIN' => static::$arUser['LOGIN'],
				'USER_EMAIL' => static::$arUser['EMAIL'],
				'USER_NAME' => static::$arUser['NAME'].' '.static::$arUser['LAST_NAME'],
				'USER_IP' => $_SERVER['REMOTE_ADDR'],
				'SITE_ID' => SITE_ID,
			];
			$arMacroTmp = [];
			foreach($arMacro as $key => $strMacro){
				if(!Helper::isUtf()){
					$strMacro = Helper::convertEncoding($strMacro, 'CP1251', 'UTF-8');
				}
				$arMacroTmp[sprintf('#%s#', $key)] = urlencode($strMacro);
			}
			$arMacro = $arMacroTmp;
			$strUrl = str_replace(array_keys($arMacro), array_values($arMacro), $strUrl);
			$obHttpClient = new \Bitrix\Main\Web\HttpClient;
			$obHttpClient->setTimeout(5);
			$obHttpClient->disableSslVerification();
			$obHttpClient->get($strUrl);
			return true;
		}
		return false;
	}
	
}
