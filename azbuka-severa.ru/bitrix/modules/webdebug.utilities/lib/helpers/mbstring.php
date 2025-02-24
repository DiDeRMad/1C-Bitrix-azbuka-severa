<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Mbstring {
	
	const UTF8 = 'UTF-8';
	
	/**
	 *	Check UTF and function mb_*
	 */
	protected static function exists($strFunc){
		return defined('BX_UTF') && constant('BX_UTF') === true && function_exists($strFunc);
	}

	/**
	 *	strlen
	 */
	public static function strlen($string){
		if(static::exists('mb_strlen')){
			return mb_strlen($string, static::UTF8);
		}
		return strlen($string);
	}

	/**
	 *	strpos
	 */
	public static function strpos($haystack, $needle, $offset=0){
		if(static::exists('mb_strpos')){
			return mb_strpos($haystack, $needle, $offset, static::UTF8);
		}
		return strpos($haystack, $needle, $offset);
	}

	/**
	 *	strrpos
	 */
	public static function strrpos($haystack, $needle, $offset=0){
		if(static::exists('mb_strrpos')){
			return mb_strrpos($haystack, $needle, $offset, static::UTF8);
		}
		return strrpos($haystack, $needle, $offset);
	}

	/**
	 *	substr
	 */
	public static function substr($string, $start, $length=null){
		if(static::exists('mb_substr')){
			return mb_substr($string, $start, $length, static::UTF8);
		}
		if(is_null($length)){
			return substr($string, $start);
		}
		return substr($string, $start, $length);
	}

	/**
	 *	strtolower
	 */
	public static function strtolower($string){
		if(static::exists('mb_strtolower')){
			return mb_strtolower($string, static::UTF8);
		}
		return strtolower($string);
	}

	/**
	 *	strtoupper
	 */
	public static function strtoupper($string){
		if(static::exists('mb_strtoupper')){
			return mb_strtoupper($string, static::UTF8);
		}
		return strtoupper($string);
	}

	/**
	 *	stripos
	 */
	public static function stripos($haystack, $needle, $offset=0){
		if(static::exists('mb_stripos')){
			return mb_stripos($haystack, $needle, $offset, static::UTF8);
		}
		return stripos($haystack, $needle, $offset);
	}

	/**
	 *	strripos
	 */
	public static function strripos($haystack, $needle, $offset=0){
		if(static::exists('mb_strripos')){
			return mb_strripos($haystack, $needle, $offset, static::UTF8);
		}
		return strripos($haystack, $needle, $offset);
	}

	/**
	 *	strstr
	 */
	public static function strstr($haystack, $needle, $before_needle=false){
		if(static::exists('mb_strstr')){
			return mb_strstr($haystack, $needle, $before_needle, static::UTF8);
		}
		return strstr($haystack, $needle, $before_needle);
	}

	/**
	 *	stristr
	 */
	public static function stristr($haystack, $needle, $before_needle=false){
		if(static::exists('mb_stristr')){
			return mb_stristr($haystack, $needle, $before_needle, static::UTF8);
		}
		return stristr($haystack, $needle, $before_needle);
	}

	/**
	 *	strrchr
	 */
	public static function strrchr($haystack, $needle, $part=false){
		if(static::exists('mb_strrchr')){
			return mb_strrchr($haystack, $needle, $part, static::UTF8);
		}
		return strrchr($haystack, $needle);
	}

	/**
	 *	substr_count
	 */
	public static function substr_count($haystack, $needle, $offset=0, $length=null){
		if(static::exists('mb_substr_count')){
			return mb_substr_count($haystack, $needle, static::UTF8);
		}
		return substr_count($haystack, $needle, $offset, $length);
	}

}
