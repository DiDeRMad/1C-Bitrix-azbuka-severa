<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

/**
 * Class JsHelper
 * @package WD\Utilities
 */
class JsHelper {
	
	public static function addJsDebugFunctions() {
		global $APPLICATION;
		$strFilename = $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.WDU_MODULE.'/debug_functions.js';
		if(is_file($strFilename)) {
			$strJs = file_get_contents($strFilename);
			$GLOBALS['APPLICATION']->addHeadString('<script>'.$strJs.'</script>');
		}
	}
	
	public static function addJsPreventLogout(){
		$strFilename = $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.WDU_MODULE.'/prevent_logout.js';
		if(is_file($strFilename)) {
			$strJs = file_get_contents($strFilename);
			$strJs = 'BX.message({"WD_UTILITIES_LOGOUT_CONFIRM":"'.Helper::getMessage('WDU_LOGOUT_CONFIRM').'"});'.$strJs;
			$GLOBALS['APPLICATION']->addHeadString('<script>'.$strJs.'</script>');
		}
	}
	
	public static function applySelect2toModules(){
		\CJSCore::init(['jquery','jquery2','jquery3']);
		Helper::addJsSelect2();
		ob_start();
		?>
		<script>
		$(document).ready(function(){
			wduSelect2($('form > select[name="mid"]'));
		});
		</script>
		<?
		$GLOBALS['APPLICATION']->addHeadString(ob_get_clean());
	}
	
	public static function applyCustomSelect2(){
		static $bProcessed = false;
		if($bProcessed == true){
			return;
		}
		$bProcessed = true;
		$arData = unserialize(Helper::getOption(WDU_MODULE, 'custom_pages_for_select2'));
		if(!is_array($arData)){
			$arData = [];
		}
		if(!is_array($arData['url']) || empty($arData['url'])){
			return;
		}
		#
		$arSelectorsAll = [];
		$strCurrentUrl = $GLOBALS['APPLICATION']->getCurPage();
		foreach($arData['url'] as $key => $strUrl){
			if(Helper::strlen($strUrl) && $strUrl == $strCurrentUrl){
				if(Helper::strlen($arData['selector'][$key])){
					$arSelectors = explode("\n", $arData['selector'][$key]);
					foreach($arSelectors as $strSelector){
						if(Helper::strlen($strSelector)){
							$arSelectorsAll[] = trim($strSelector);
						}
					}
				}
			}
		}
		if(!empty($arSelectorsAll)){
			\CJSCore::init(['jquery','jquery2','jquery3']);
			Helper::addJsSelect2();
			ob_start();
			?>
			<script>
			$(document).ready(function(){
				window.wduSelect2Selectors = {};
				<?foreach($arSelectorsAll as $strSelector):?>
					window.wduSelect2Selectors[`<?=$strSelector;?>`] = setInterval(function(){
						if($(`<?=$strSelector;?>`).is(':visible')){
							clearInterval(window.wduSelect2Selectors[`<?=$strSelector;?>`]);
							wduSelect2($(`<?=$strSelector;?>`));
						}
					}, 500);
				<?endforeach?>
			});
			</script>
			<?
			$GLOBALS['APPLICATION']->addHeadString(ob_get_clean());
		}
	}
	
}
