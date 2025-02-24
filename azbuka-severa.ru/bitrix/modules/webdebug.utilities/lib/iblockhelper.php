<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

/**
 * Class IBlockHelper
 * @package WD\Utilities
 */
class IBlockHelper {
	
	public static function addContextDetailLink(&$arMenuItems, $strValue=null) {
		$strUrl = $GLOBALS['APPLICATION']->getCurPage();
		$arUrl = [
			'/bitrix/admin/iblock_element_edit.php',
			'/bitrix/admin/cat_product_edit.php',
			'/shop/settings/iblock_element_edit.php',
		];
		if(in_array($strUrl, $arUrl) && $_GET['IBLOCK_ID'] > 0 && $_GET['ID'] > 0) {
			if(is_array($arMenuItems) && in_array($strValue, ['Y', 'S'])) {
				$bAdded = false;
				$arButton = static::getDetailLinkButton();
				if($arButton){
					if($strValue == 'Y'){
						foreach($arMenuItems as $key => $arMenuItem) {
							if(is_array($arMenuItem['MENU']) && $arMenuItem['ICON'] == 'btn_new') {
								if($arButton) {
									$arMenuItems[$key]['MENU'][] = $arButton;
									$bAdded = true;
								}
								break;
							}
						}
					}
					if(!$bAdded){
						$arMenuItems[] = $arButton;
					}
				}
			}
		}
	}
	
	protected static function getDetailLinkButton(){
		$arResult = null;
		if(\Bitrix\Main\Loader::includeModule('iblock')){
			$arFilter = [
				'IBLOCK_ID' => intVal($_GET['IBLOCK_ID']),
				'ID' => intVal($_GET['ID']),
			];
			$arSelect = ['DETAIL_PAGE_URL'];
			$resItem = \CIBlockElement::getList([], $arFilter, false, false, $arSelect);
			if($arItem = $resItem->getNext(false, false)) {
				if(strlen($strUrl = $arItem['DETAIL_PAGE_URL'])) {
					$strIBlockSiteId = static::getIBlockSite(intVal($_GET['IBLOCK_ID']));
					if(strlen($strIBlockSiteId)){
						$arItem['LID'] = $strIBlockSiteId;
					}
					$resSites = \CSite::getList($by='ID', $order='ASC', ['ID' => $arItem['LID']]);
					if($arSite = $resSites->fetch()){
						$strServerName = $arSite['SERVER_NAME'];
						$bHttps = false;
						if(!strlen($strServerName)){
							$strServerName = \Bitrix\Main\Config\Option::get('main', 'server_name');
							$bHttps = \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps() ? true : false;
						}
						if(!strlen($strServerName)){
							$strServerName = \Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost();
							$bHttps = \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps() ? true : false;
						}
						if(strlen($strServerName)){
							$arResult = [
								'TEXT' => Helper::getMessage('WDU_SHOW_ON_SITE'),
								'ONCLICK' => 'window.open("'.($bHttps ? 'https' : 'http').'://'.$strServerName.$strUrl.'");',
								'ICON' => 'view',
							];
						}
					}
				}
			}
		}
		return $arResult;
	}
	
	public static function getIBlockSite($intIBlockId){
		$arSites = [];
		$resSites = \CIBlock::getSite($intIBlockId);
		while($arSite = $resSites->fetch()){
			$arSites[$arSite['LID']] = $arSite['LID'];
		}
		if(count($arSites) > 1){
			$strCrmSiteId = static::getCrmSiteId();
			if(strlen($strCrmSiteId) && isset($arSites[$strCrmSiteId])){
				unset($arSites[$strCrmSiteId]);
			}
		}
		return reset($arSites);
	}
	
	public static function getCrmSiteId(){
		$arSites = Helper::getSitesList(true, true);
		foreach($arSites as $strSiteId => $strSiteName){
			$resSiteTemplate = \CSite::getTemplateList($strSiteId);
			while($arSiteTemplate = $resSiteTemplate->fetch()){
				if($arSiteTemplate['TEMPLATE'] == 'bitrix24' && !strlen(trim($arSiteTemplate['CONDITION']))){
					return $strSiteId;
				}
			}
		}
		return false;
	}
	
	public static function displayElementIdInTabFoot(&$obTabControl) {
		$strUrl = $GLOBALS['APPLICATION']->getCurPage();
		$arUrl = ['/bitrix/admin/iblock_element_edit.php', '/bitrix/admin/cat_product_edit.php'];
		if(in_array($strUrl, $arUrl) && $_GET['IBLOCK_ID'] > 0 && $_GET['ID'] > 0) {
			$strContent = sprintf('<span>&nbsp;<b>ID</b>: %s</span>', $_GET['ID']);
			if(defined('BX_PUBLIC_MODE')) {
				?>
				<script>
				setTimeout(function(){
					let divButtons = document.getElementById('save_and_add').parentNode;
					if(divButtons) {
						let tmpDiv = document.createElement('div');
						tmpDiv.innerHTML = '<?=$strContent;?>';
						divButtons.appendChild(tmpDiv.firstChild);
					}
				}, 500);
				</script>
				<?
			}
			elseif(preg_match('#form_element_(\d+)#', $obTabControl->name)) {
				$obTabControl->sButtonsContent .= $strContent;
			}
		}
	}

	/**
	 * 	Add meta data to property names (ID and CODE), example:
	 * 		Article:
	 * 		123, CML2_ARTICLE
	 * 	From OnEndBufferContent
	 */
	public static function editFormAddIdCode(&$strContent){
		$arAllowedUrls = ['/bitrix/admin/cat_product_edit.php', '/bitrix/admin/iblock_element_edit.php'];
		if(!is_object($GLOBALS['USER']) || !$GLOBALS['USER']->isAuthorized()){
			return;
		}
		if(in_array($GLOBALS['APPLICATION']->getCurPage(), $arAllowedUrls)){
			$intIBlockId = intVal(\Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->get('IBLOCK_ID'));
			if($intIBlockId){
				$arProps = [];
				$resProps = \CIBlockProperty::getList([], ['IBLOCK_ID' => $intIBlockId]);
				while($arProp = $resProps->fetch()){
					$arProps[$arProp['ID']] = $arProp;
				}
				unset($resProps);
				$strRegExp = '#(<tr [^>]*id="tr_PROPERTY_(\d+)"[^>]*>\s*<td class="[^>]+" width="40%">)(\s*<span[^>]+>.*?</script>&nbsp;)?(\s*[^>]+:\s*)(</td>)#is';
				$strContent = preg_replace_callback($strRegExp, function($arMatch)use($arProps){
					$strName = sprintf('%s%s <span class="wdu_iblock_prop_meta">%d, %s</span>', $arMatch[3], $arMatch[4], 
						$arMatch[2], $arProps[$arMatch[2]]['CODE']);
					return $arMatch[1].$strName.$arMatch[5];
				}, $strContent);
				//
				$strCss = '
				<style>
					.wdu_iblock_prop_meta {
						color:#999!important;
						display:block!important;
						font-size:11px!important;
						padding-right:3px!important;
						text-align:right!important;
					}
				</style>
				';
				if(\Bitrix\Main\Context::getCurrent()->getRequest()->getQueryList()->get('bxpublic') == 'Y'){
					$strContent = $strCss.$strContent;
				}
				else{
					$strContent = str_ireplace('</head>', $strCss.'</head>', $strContent);
				}
			}
		}
	}
	
	/**
	 * Hide some iblock tabs
	 */
	public static function hideEditTabs(&$obTabControl){
		$arUrlSection = ['/bitrix/admin/iblock_section_edit.php', '/bitrix/admin/cat_section_edit.php'];
		$arUrlElement = ['/bitrix/admin/iblock_element_edit.php', '/bitrix/admin/cat_product_edit.php'];
		//
		$bSectionUrl = in_array($GLOBALS['APPLICATION']->getCurPage(), $arUrlSection);
		$bElementUrl = in_array($GLOBALS['APPLICATION']->getCurPage(), $arUrlElement);
		//
		if($bSectionUrl || $bElementUrl){
			$arMess = ['#LANGUAGE_ID#' => LANGUAGE_ID, '#MODULE_ID#' => WDU_MODULE];
			$bPublic = $_GET['bxpublic'] == 'Y';
			$strMessageCss = 'color:maroon;font-weight:bold;margin-bottom:15px;';
			if($bSectionUrl){
				$bHideProps = Helper::getUserOption(WDU_MODULE, 'iblock_hide_section_tab_properties');
				$bHideSeo = Helper::getUserOption(WDU_MODULE, 'iblock_hide_section_tab_seo');
				 // Hide section properties
				 if($bHideProps == 'Y' || ($bHideProps == 'P' && $bPublic) || ($bHideProps == 'A' && !$bPublic)){
					printf('<div style="%s">%s</div>', $strMessageCss, Helper::getMessage('WDU_HIDDEN_SECTION_PROPS', $arMess));
					foreach($obTabControl->tabs as $key => $arTab){
						foreach($obTabControl->tabs[$key]['FIELDS'] as $key2 => $arField){
							if(in_array($arField['id'], ['SECTION_PROPERTY'])){
								unset($obTabControl->tabs[$key]['FIELDS'][$key2]);
							}
						}
					}
				}
				// Hide section SEO
				if($bHideSeo == 'Y' || ($bHideSeo == 'P' && $bPublic) || ($bHideSeo == 'A' && !$bPublic)){
					printf('<div style="%s">%s</div>', $strMessageCss, Helper::getMessage('WDU_HIDDEN_SECTION_SEO', $arMess));
					foreach($obTabControl->tabs as $key => $arTab){
						foreach($obTabControl->tabs[$key]['FIELDS'] as $key2 => $arField){
							if(preg_match('#^IPROPERTY_TEMPLATES_ELEMENT#', $arField['id'])){
								unset($obTabControl->tabs[$key]['FIELDS'][$key2]);
							}
						}
					}
				}
			}
			if($bElementUrl){
				// Hide element SEO
				$bHideSeo = Helper::getUserOption(WDU_MODULE, 'iblock_hide_element_tab_seo');
				if($bHideSeo == 'Y' || ($bHideSeo == 'P' && $bPublic) || ($bHideSeo == 'A' && !$bPublic)){
					printf('<div style="%s">%s</div>', $strMessageCss, Helper::getMessage('WDU_HIDDEN_ELEMENT_SEO', $arMess));
					foreach($obTabControl->tabs as $key => $arTab){
						foreach($obTabControl->tabs[$key]['FIELDS'] as $key2 => $arField){
							if(preg_match('#^IPROPERTY_TEMPLATES_ELEMENT#', $arField['id'])){
								unset($obTabControl->tabs[$key]['FIELDS'][$key2]);
							}
						}
					}
				}
			}
		}
		// Hide tab 'Advertising'
		if($bElementUrl && Helper::getUserOption(WDU_MODULE, 'iblock_hide_element_tab_adv') == 'Y'){
			foreach($obTabControl->tabs as $key => $arTab){
				if($arTab['DIV'] == 'seo_adv_seo_adv') {
					$obTabControl->tabs[$key]['CONTENT'] = '';
				}
			}
		}
		// Hide tab 'Workflow'
		if($bElementUrl && Helper::getUserOption(WDU_MODULE, 'iblock_hide_element_tab_wf') == 'Y'){
			foreach($obTabControl->tabs as $key => $arTab){
				if($arTab['DIV'] == 'edit4') {
					$obTabControl->tabs[$key]['FIELDS'] = [];
				}
			}
		}
		// Hide tab 'Business processes'
		if($bElementUrl && Helper::getUserOption(WDU_MODULE, 'iblock_hide_element_tab_bp') == 'Y'){
			foreach($obTabControl->tabs as $key => $arTab){
				if($arTab['DIV'] == 'edit7') {
					$obTabControl->tabs[$key]['FIELDS'] = [];
				}
			}
		}
	}
	
}
