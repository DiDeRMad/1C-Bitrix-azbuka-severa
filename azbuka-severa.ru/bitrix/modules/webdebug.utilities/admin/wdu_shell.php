<?
use
	\WD\Utilities\Helper,
	\WD\Utilities\Adv,
	\WD\Utilities\Cli,
	\WD\Utilities\Json;

$strModuleId = 'webdebug.utilities';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$strModuleId.'/prolog.php');
if(!\Bitrix\Main\Loader::includeModule($strModuleId)) {
	die('Module is not found!');
}
Helper::loadMessages();
$strLang = 'WDU_SHELL_';
\CJSCore::init(['jquery2', 'wdupopup']);
$APPLICATION->addHeadScript('/bitrix/js/'.$strModuleId.'/wdu_popup.js');
$APPLICATION->addHeadScript('/bitrix/js/'.$strModuleId.'/helper.js');
$APPLICATION->addHeadScript('/bitrix/js/'.$strModuleId.'/jquery.webdebug.hotkeys.js');
$APPLICATION->addHeadScript('/bitrix/js/'.$strModuleId.'/jquery.fullscreen/jquery.fullscreen-min.js');
$APPLICATION->addHeadScript('/bitrix/js/'.$strModuleId.'/jquery.terminal/js/jquery.terminal.min.js');
$APPLICATION->addHeadScript('/bitrix/js/'.$strModuleId.'/shell.js');
$APPLICATION->setAdditionalCss('/bitrix/js/'.$strModuleId.'/jquery.terminal/css/jquery.terminal.min.css');

if(!$USER->isAdmin()){
	$APPLICATION->authForm(Helper::getMessage("ACCESS_DENIED"));
}

list($arGet, $arPost) = Helper::getRequestQuery();

if(strlen($arGet['wdu_ajax_option'])){
	$arJsonResult = Json::prepare();
	$arJsonResult['jsonrpc'] = '2.0';
	#
	switch($arGet['wdu_ajax_option']){
		case 'shell_execute':
			$strKey = 'HTTP_RAW_POST_DATA';
			$arJson = [];
			$strJson = isset($GLOBALS[$strKey]) ? $GLOBALS[$strKey] : file_get_contents('php://input');
			if(strlen($strJson)){
				$arJson = Json::decode($strJson);
				$strCommand = $arJson['method'].(!empty($arJson['params']) ? ' '.implode(' ', $arJson['params']) : '');
				if(!preg_match('#\s*2\s*>\s*&1\s*$#', $strCommand)){
					$strCommand .= ' 2>&1';
				}
				$strResult = Cli::exec($strCommand, true);
				$strError = null;
				$arJsonResult = [
					'id' => 0,
					'result' => $strResult,
					'error' => $strError,
					'command' => $strCommand,
				];
			}
			break;
	}
	Json::output($arJsonResult);
	die();
}

$APPLICATION->setTitle(GetMessage($strLang.'PAGE_TITLE'));
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

# Notice for Alt+Enter
?>
<div class="wdu_shell_fullscreen_note">
	<div>
		<?Helper::showNote(Helper::getMessage($strLang.'FULLSCREEN'), true);?>
	</div>
</div>
<?

# Advertising
Adv::showAdv();

# Info
$strVersionUtil = Helper::getModuleVersion('webdebug.utilities');
$strDateUtil = \Bitrix\Main\Type\Datetime::tryParse(Helper::getModuleVersion('webdebug.utilities', true), 'Y-m-d')
	->format('d.m.Y');
$strVersionMain = Helper::getModuleVersion('main');
$strVersionPhp = PHP_VERSION;
$strEncoding = Helper::isUtf() ? 'UTF-8' : 'windows-1251';
$strUser = Cli::exec('whoami', true);
$intUser = getmypid();
$arInfo = [
	sprintf('Webdebug Shell Client %s [%s]', $strVersionUtil, $strDateUtil),
	sprintf("Bitrix: %s, PHP: %s, CHARSET: %s, USER: %s [%d], DOCUMENT_ROOT: %s",
		$strVersionMain, $strVersionPhp, $strEncoding, $strUser, $intUser, Helper::root()),
	php_uname(),
];
$strGreetings = implode(PHP_EOL, $arInfo);

# Display console
if(Cli::isExec()){
	?>
		<div data-role="wdu_terminal" data-greetings="<?=$strGreetings;?>"></div>
		<br/>
	<?
	print Helper::showNote(Helper::getMessage($strLang.'NOTES'), true);
}
else{
	print Helper::showError(Helper::getMessage($strLang.'NO_EXEC'), Helper::getMessage($strLang.'NO_EXEC_DETAILS'));
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>