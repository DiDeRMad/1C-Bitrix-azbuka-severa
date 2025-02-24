<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper;

define('WDU_MODULE', 'webdebug.utilities');

$arAutoload = [
	'WD\Utilities\Adv' => 'lib/adv.php',
	'WD\Utilities\Backup' => 'lib/backup.php',
	'WD\Utilities\BxHelper' => 'lib/bxhelper.php',
	'WD\Utilities\Cli' => 'lib/cli.php',
	'WD\Utilities\DirSize' => 'lib/dirsize.php',
	'WD\Utilities\DirScanner' => 'lib/dirscanner.php',
	'WD\Utilities\EventHandler' => 'lib/eventhandler.php',
	'WD\Utilities\FastSql' => 'lib/fastsql.php',
	'WD\Utilities\Finder' => 'lib/finder.php',
	'WD\Utilities\GotoObject' => 'lib/gotoobject.php',
	'WD\Utilities\Helper' => 'lib/helper.php',
		'WD\Utilities\Helpers\ArrayUtil' => 'lib/helpers/arrayutil.php',
		'WD\Utilities\Helpers\Cache' => 'lib/helpers/cache.php',
		'WD\Utilities\Helpers\Catalog' => 'lib/helpers/catalog.php',
		'WD\Utilities\Helpers\Database' => 'lib/helpers/database.php',
		'WD\Utilities\Helpers\Date' => 'lib/helpers/date.php',
		'WD\Utilities\Helpers\Debug' => 'lib/helpers/debug.php',
		'WD\Utilities\Helpers\Encoding' => 'lib/helpers/encoding.php',
		'WD\Utilities\Helpers\File' => 'lib/helpers/file.php',
		'WD\Utilities\Helpers\Html' => 'lib/helpers/html.php',
		'WD\Utilities\Helpers\IBlock' => 'lib/helpers/iblock.php',
		'WD\Utilities\Helpers\JsCss' => 'lib/helpers/jscss.php',
		'WD\Utilities\Helpers\Language' => 'lib/helpers/language.php',
		'WD\Utilities\Helpers\Main' => 'lib/helpers/main.php',
		'WD\Utilities\Helpers\Mbstring' => 'lib/helpers/mbstring.php',
		'WD\Utilities\Helpers\Number' => 'lib/helpers/number.php',
		'WD\Utilities\Helpers\Option' => 'lib/helpers/option.php',
		'WD\Utilities\Helpers\Site' => 'lib/helpers/site.php',
		'WD\Utilities\Helpers\Text' => 'lib/helpers/text.php',
		'WD\Utilities\Helpers\User' => 'lib/helpers/user.php',
		'WD\Utilities\Helpers\Version' => 'lib/helpers/version.php',
		'WD\Utilities\Helpers\Visual' => 'lib/helpers/visual.php',
	'WD\Utilities\HttpHeader' => 'lib/httpheader.php',
	'WD\Utilities\IBlockHelper' => 'lib/iblockhelper.php',
	'WD\Utilities\JsHelper' => 'lib/jshelper.php',
	'WD\Utilities\Json' => 'lib/json.php',
	'WD\Utilities\MenuManager' => 'lib/menumanager.php',
	'WD\Utilities\Option' => 'lib/option.php',
	'WD\Utilities\Options' => 'lib/options.php',
	'WD\Utilities\PageProp' => 'lib/pageprop.php',
	'WD\Utilities\PagePropBase' => 'lib/pagepropbase.php',
	'WD\Utilities\PropSorterTable' => 'lib/propsorter.php',
	'WD\Utilities\AdminAuthNotifier' => 'lib/adminauthnotifier.php',
];
\Bitrix\Main\Loader::registerAutoLoadClasses(WDU_MODULE, $arAutoload);

Helper::loadMessages();

\CJSCore::registerExt('wdupopup', [
	'js' => '/bitrix/js/'.WDU_MODULE.'/wdu_popup.js', 
	'rel' => ['popup'],
]);
