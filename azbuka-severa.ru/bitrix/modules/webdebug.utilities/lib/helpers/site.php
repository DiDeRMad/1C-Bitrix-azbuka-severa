<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Site {
	
	/**
	 *	Get all sites
	 */
	public static function getSitesList($bActive=false, $bSimple=false, $strField=null, $strOrder=null, $bIcons=false) {
		$arResult = [];
		$arFilter = [];
		if($bActive) {
			$arFilter['ACTIVE'] = 'Y';
		}
		$strField = strlen($strField) ? $strField : 'SORT';
		$strOrder = strlen($strOrder) ? $strOrder : 'ASC';
		$resSites = \CSite::getList($strField, $strOrder, $arFilter);
		while($arSite = $resSites->getNext(false, false)) {
			$arSite['TEXT'] = static::formatSiteName($arSite);
			if(!$bSimple && $bIcons && strlen($arSite['SERVER_NAME'])){
				$arSite['ICON'] = static::getSiteIcon($arSite);
			}
			$arResult[$arSite['ID']] = $bSimple ? sprintf('[%s] %s', $arSite['ID'], $arSite['NAME']) : $arSite;
		}
		return $arResult;
	}
	
	/**
	 *	Determine site by domain
	 */
	public static function getSiteByDomain($strDomain=null){
		static $arCurrentSiteId;
		if(!is_array($arCurrentSiteId)){
			$arCurrentSiteId = [];
		}
		if(!is_string($strDomain)){
			$strDomain = Helper::getCurrentDomain(true);
		}
		if(!isset($arCurrentSiteId[$strDomain])){
			$arSites = static::getSitesList($bActiveOnly=true);
			$strFoundSiteId = null;
			$arDefaultSite = null;
			foreach($arSites as $strSiteId => $arSite){
				if(is_null($arDefaultSite) || $arSite['DEF'] == 'Y'){
					$arDefaultSite = $arSite;
				}
				$arDomains = Helper::splitSpaceValues(toLower($arSite['DOMAINS']));
				foreach($arDomains as $strSiteDomain){
					if($strSiteDomain == $strDomain){ // domain
						$strFoundSiteId = $strSiteId;
						break 2;
					}
					elseif(substr($strDomain, -1 * strlen($strSiteDomain) - 1) == '.'.$strSiteDomain){// subdomain
						$strFoundSiteId = $strSiteId;
						break 2;
					}
				}
			}
			if(is_null($strFoundSiteId) && $arDefaultSite){
				$strFoundSiteId = $arDefaultSite['ID'];
			}
			if($strFoundSiteId){
				$arCurrentSiteId[$strDomain] = $strFoundSiteId;
			}
		}
		return $arCurrentSiteId[$strDomain];
	}
	
	/**
	 *	Get site favicon
	 */
	public static function getSiteIcon($arSite){
		$strDomain = $arSite['SERVER_NAME'];
		$strFolder = strlen($arSite['DOC_ROOT']) ? $arSite['DOC_ROOT'] : 
			(strlen($arSite['ABS_DOC_ROOT']) ? $arSite['ABS_DOC_ROOT'] : '');
		$strContent = null;
		$strMime = 'image/x-icon';
		$strFile = 'favicon.ico';
		if(strlen($strFolder) && is_dir($strFolder) && is_file($strFolder.'/'.$strFile)){
			$strContent = file_get_contents($strFolder.'/'.$strFile);
		}
		if(!strlen($strContent)){
			$strUrl = sprintf('https://%s/%s', $arSite['SERVER_NAME'], $strFile);
			$obHttpClient = new \Bitrix\Main\Web\HttpClient;
			$obHttpClient->setTimeout(1);
			$obHttpClient->disableSslVerification();
			$strContent = $obHttpClient->get($strUrl);
			if($strContent === false){
				$strUrl = sprintf('http://%s/%s', $arSite['SERVER_NAME'], $strFile);
				$strContent = $obHttpClient->get($strUrl);
			}
		}
		if(strlen($strContent)){
			$strContent = base64_encode($strContent);
			/*
			if(in_array($obHttpClient->getHeaders()->getContentType(), ['image/png'])){
				$strMime = 'image/png';
			}
			elseif(in_array($obHttpClient->getHeaders()->getContentType(), ['image/gif'])){
				$strMime = 'image/gif';
			}
			*/
		}
		else{
			//
		}
		return strlen($strContent) ? sprintf('data:%s;base64,%s', $strMime, $strContent) : '';
	}
	
	/**
	 *	Format site name
	 */
	public static function formatSiteName($arSite){
		return sprintf('[%s] %s (%s)', $arSite['ID'], $arSite['SITE_NAME'], $arSite['SERVER_NAME']);
	}

	/**
	 *	Format site URL
	 */
	public static function formatSiteUrl($strDomain, $bSSL, $strUrl=null) {
		$strResult = ($bSSL ? 'https://' : 'http://').$strDomain;
		if(is_string($strUrl)){
			if(preg_match('#^([a-z-]+)://#i', $strUrl)){
				$strResult = $strUrl;
			}
			else{
				$strResult .= $strUrl;
			}
		}
		return $strResult;
	}

}
