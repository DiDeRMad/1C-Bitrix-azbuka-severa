<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper,
	\WD\Utilities\Json;

Helper::loadMessages();

final class Adv {
	
	const ADV_URL = 'https://www.webdebug.ru/wdu_adv/';
	const PARAM = 'wd_advertising_show';
	
	/**
	 *	Display html for ajax-request to show adv
	 */
	public static function showAdv(array $arParams=[]) {
		if(!static::isActive()){
			return;
		}
		$intId = Helper::randString();
		?>
		<div id="<?=$intId;?>" style="display:none;"></div>
		<script>
		setTimeout(function(){
			wduAjax(null, {'<?=static::PARAM;?>':'Y'}, {}, function(arJson){
				$('#<?=$intId;?>').html(arJson.Content).show();
			}, function(){}, true);
		}, 250);
		</script>
		<?
	}
	
	/**
	 *	Get HTML for adv
	 */
	public static function getAdvContent(){
		$obHttp = new \Bitrix\Main\Web\HttpClient();
		$obHttp->setTimeout(3);
		$obHttp->setHeader('Content-Type', 'application/json', true);
		$obHttp->setHeader('User-Agent', static::compileUserAgent(), true);
		$obHttp->setHeader('Referer', \Bitrix\Main\Context::getCurrent()->getRequest()->getHeader('Referer'), true);
		$obHttp->disableSslVerification();
		$strResponse = $obHttp->post(static::ADV_URL, Json::encode($arParams));
		return [
			'CONTENT' => $strResponse,
			'HEADERS' => $obHttp->getHeaders()->toArray(),
		];
	}
	
	/**
	 *	Create HTTP_USER_AGENT for requests
	 */
	protected static function compileUserAgent(){
		return sprintf('%s [%s] on Bitrix [%s] in %s at %s://%s [%s]',
			WDU_MODULE,
			Helper::getModuleVersion(WDU_MODULE), 
			Helper::getModuleVersion('main'),
			Helper::isUtf() ? 'UTF-8' : 'windows-1251', 
			\Bitrix\Main\Context::getCurrent()->getRequest()->isHttps() ? 'https' : 'http',
			\Bitrix\Main\Context::getCurrent()->getServer()->getHttpHost(),
			Helper::getLicenseHash());
	}
	
	/**
	 *	Handle onBeforeProlog for ajax-request to show adv
	 */
	public static function onBeforeProlog(){
		if(defined('ADMIN_SECTION') && ADMIN_SECTION === true && $_GET[static::PARAM] == 'Y'){
			$arJson = Json::prepare();
			$arJson['Success'] = false;
			if(static::isActive()){
				$arResponse = static::getAdvContent();
				if($arResponse['HEADERS']['wd-adv-shown']['values'][0] == 'Y'){
					if(strlen($arResponse['CONTENT'])){
						$arJson['Success'] = true;
						$arJson['Content'] = $arResponse['CONTENT'];
						if(!Helper::isUtf()){
							$arJson['Content'] = Helper::convertEncoding($arJson['Content'], 'UTF-8', 'CP1251');
						}
					}
				}
			}
			Json::output($arJson);
			die();
		}
	}
	
	/**
	 *	Check the user has not opted out of advertising
	 */
	public static function isActive(){
		return Helper::getUserOption(WDU_MODULE, 'hide_advertising') != 'Y';
	}

}
