<?
namespace WD\Utilities;

use
	\WD\Utilities\Helper,
	\WD\Utilities\Cli,
	\WD\Utilities\Json;

Helper::loadMessages();

/**
 * Class Backup
 * @package WD\Utilities
 */
class Backup {
	
	const DIR_BACKUP = 'backup';
	const SUBDIR_ARCHIVES = 'archives';
	const SUBDIR_DATA = 'data';
	const INTERVAL_CUSTOM = 'CUSTOM';
	
	/**
	 *	Main process
	 */
	public static function execute(){
		$strBackupDir = static::getBackupDir(static::SUBDIR_ARCHIVES);
		$strZipFilePath = sprintf('%s%s/%s', Helper::root(), $strBackupDir, static::generateFilename());
		static::cleanPreparedData();
		$arFiles = [
			[
				'NAME' => 'Files',
				'FILES' => static::getBackupFiles(),
				'REMOVE_PATH' => false,
			],
			[
				'NAME' => 'Data',
				'FILES' => static::prepareAdditionalData(),
				'REMOVE_PATH' => static::getBackupDir(static::SUBDIR_DATA),
			],
		];
		if(static::archiveFiles($arFiles, $strZipFilePath)){
			static::sendEmail($arFiles, $strZipFilePath);
		}
		static::cleanPreparedData();
		static::removeOldBackups();
	}
	
	/**
	 *	Check backup is turned ON
	 */
	public static function isEnabled(){
		return Helper::getOption(WDU_MODULE, 'backups_enabled') == 'Y';
	}
	
	/**
	 *	Generate name for file
	 */
	public static function generateFilename(){
		return sprintf('%s_%s.zip', date('Y-m-d_H-i-s'), randString(8, 'abcdefghijklnmopqrstuvwxyz'));
	}
	
	/**
	 *	Get files to backup (raw: as string, separated by "\n")
	 */
	public static function getBackupFilesRaw(){
		$arResult = Helper::splitSpaceValues(Helper::getOption(WDU_MODULE, 'backups_files'));
		foreach($arResult as $key => $strFile){
			$strFile = trim($strFile);
			if(!strlen($strFile) || Helper::substr($strFile, 0, 1) != '/'){
				unset($arResult[$key]);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get files to backup
	 */
	public static function getBackupFiles(){
		$arResult = [];
		$arFilesRaw = static::getBackupFilesRaw();
		foreach($arFilesRaw as $strFile){
			if(Helper::strpos($strFile, '*') === false && Helper::strpos($strFile, '?') === false){
				if(is_dir(Helper::root().$strFile)){
					$arResult = array_merge($arResult, static::getDirFiles($strFile));
				}
				elseif(is_file(Helper::root().$strFile)){
					$arResult[] = $strFile;
				}
			}
			else{
				$arResult = array_merge($arResult, static::getMaskFiles($strFile));
			}
		}
		sort($arResult);
		return $arResult;
	}
	
	/**
	 *	Get files and directories in selected directory
	 *	$strDir is a relative directory
	 */
	public static function getDirFiles($strDir, $bRecursive=true){
		$arResult = [];
		if(strlen($strDir) && is_dir(Helper::root().$strDir)){
			$arResult = Helper::scandir($strDir, ['RECURSIVELY' => $bRecursive, 'RELATIVE' => true, 'FILES' => true, 
				'DIRS' => true]);
		}
		return $arResult;
	}
	
	/**
	 *	Get files by simple mask
	 *	Supported just asterisk symbol (*)
	 */
	public static function getMaskFiles($strMask){
		$arResult = [];
		$bRecursive = true;
		$strMaskTech = str_replace(['*', '?'], '*', $strMask);
		$strDir = explode('*', $strMaskTech)[0];
		$strDir = implode('/', array_slice(explode('/', $strDir), 0, -1));
		if(!strlen($strDir)){
			$strDir = '/';
			$bRecursive = false;
		}
		if(is_dir(Helper::root().$strDir)){
			$arReplace = [
				preg_quote('*') => '[^/]*',
				preg_quote('?') => '.*?',
			];
			$strPregPattern = str_replace(array_keys($arReplace), array_values($arReplace), preg_quote($strMask));
			$strPregPattern = sprintf('#%s#', $strPregPattern);
			$arDirFiles = static::getDirFiles(strlen($strDir) ? $strDir : '/', $bRecursive);
			foreach($arDirFiles as $strFile){
				if(preg_match($strPregPattern, $strFile)){
					$arResult[] = $strFile;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Archive files to the specified file
	 *	$arFiles is an array of relative filenames
	 */
	public static function archiveFiles($arFileGroups, $strZipFile){
		if(is_file($strZipFile)){
			unlink($strZipFile);
		}
		$obAchiver = \CBXArchive::getArchive($strZipFile);
		foreach($arFileGroups as $arFileGroup){
			$arOptions = [
				'remove_path' => Helper::root().(strlen($arFileGroup['REMOVE_PATH']) ? $arFileGroup['REMOVE_PATH'] : ''),
				'add_path' => $arFileGroup['NAME'],
			];
			$arFiles = array_map(function($strFile){
				return Helper::root().$strFile;
			}, $arFileGroup['FILES']);
			foreach($arFiles as $key => $strFile){
				if(!is_file($strFile) || !filesize($strFile)){
					unset($arFiles[$key]);
				}
			}
			@$obAchiver->add($arFiles, $arOptions);
		}
		unset($obAchiver);
		return is_file($strZipFile) && filesize($strZipFile);
	}
	
	/**
	 *	Get backup dir (relative)
	 */
	public static function getBackupDir($strSubSubdir=null){
		$strSubdir = sprintf('%s/%s/%s', WDU_MODULE, static::DIR_BACKUP, Helper::getOption('main', 'server_uniq_id'));
		if(is_string($strSubSubdir) && strlen($strSubSubdir)){
			$strSubdir = sprintf('%s/%s', $strSubdir, $strSubSubdir);
		}
		return Helper::getUploadDir($strSubdir);
	}
	
	/**
	 *	Save data file
	 */
	public static function saveBackupDataFile($strFile, $mContents){
		$strDataDir = static::getBackupDir(static::SUBDIR_DATA);
		$strFilename = $strDataDir.'/'.$strFile;
		$strFilePath = Helper::root().$strFilename;
		Helper::createDirectoriesForFile($strFilePath);
		if(is_array($mContents)){
			$mContents = static::prepareJson($mContents);
		}
		if(!file_put_contents($strFilePath, $mContents)){
			return false;
		}
		return $strFilename;
	}
	
	/**
	 *	Prepare json for save
	 */
	public static function prepareJson($arJson){
		$intOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR;
		if(Helper::checkVersion(PHP_VERSION, '7.2.0')){
			$intOptions = $intOptions | JSON_INVALID_UTF8_IGNORE;
		}
		return Json::encode($arJson, $intOptions);
	}
	
	/**
	 *	Clean old prepared data (remove whold folder and then create new)
	 */
	public static function cleanPreparedData(){
		$strDataDir = static::getBackupDir(static::SUBDIR_DATA);
		deleteDirFilesEx($strDataDir);
		Helper::mkDir(Helper::root().$strDataDir);
	}
	
	/**
	 *	Get additional backup data items
	 */
	public static function getAdditionals($strResultKey=null){
		static $arResult = [];
		if(empty($arResult)){
			$arValue = Helper::splitSpaceValues(Helper::getOption(WDU_MODULE, 'backups_additional'));
			$arResult = ['Options', 'OptionsSite', 'Crontab', 'BackupPassword', 'Modules', 'Handlers', 'Agents', 
				'Tables', 'Sites', 'Templates', 'Events', 'Undo', 'Stickers', 'Admins', 'System'];
			$arResult = array_flip($arResult);
			foreach($arResult as $strKey => &$arItem){
				$arItem = [
					'NAME' => Helper::getMessage('WDU_BACKUP_DATA_'.toUpper($strKey)),
					'ON' => in_array($strKey, $arValue),
				];
			}
			unset($arItem);
		}
		if(is_string($strResultKey) && strlen($strResultKey)){
			return $arResult[$strResultKey];
		}
		return $arResult;
	}
	
	/**
	 *	Prepare additional data as files
	 */
	public static function prepareAdditionalData(){
		$arResult = [];
		$arOptions = Backup::getAdditionals();
		foreach($arOptions as $strItem => $arItem){
			if($arItem['ON']){
				$arResult[] = call_user_func([__CLASS__, 'prepareAdditionalData_'.$strItem]);
			}
		}
		foreach($arResult as $key => $strFile){
			if(!is_string($strFile) || !strlen($strFile)){
				unset($arResult[$key]);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Prepare data from b_option
	 */
	protected static function prepareAdditionalData_Options(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_option` ORDER BY MODULE_ID ASC, NAME ASC;");
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['MODULE_ID']][$arItem['NAME']] = $arItem['VALUE'];
		}
		return static::saveBackupDataFile('options.json', $arResult);
	}
	
	/**
	 *	Prepare data from b_option_site
	 */
	protected static function prepareAdditionalData_OptionsSite(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_option_site` ORDER BY MODULE_ID ASC, NAME ASC;");
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['SITE_ID']][$arItem['MODULE_ID']][$arItem['NAME']] = $arItem['VALUE'];
		}
		return static::saveBackupDataFile('options_site.json', $arResult);
	}
	
	/**
	 *	Save crontab jobs
	 */
	protected static function prepareAdditionalData_Crontab(){
		$arTasks = Cli::getCronTasks();
		$arTasks = array_column($arTasks, 'COMMAND_FULL');
		return static::saveBackupDataFile('crontab.txt', implode("\n", $arTasks));
	}
	
	/**
	 *	Save bitrix backup password
	 */
	protected static function prepareAdditionalData_BackupPassword(){
		$strPassword = '';
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/backup.php');
		if(class_exists('CPasswordStorage')){
			$strPassword = \CPasswordStorage::get('dump_temporary_cache');
		}
		return static::saveBackupDataFile('backup_password.txt', $strPassword);
	}
	
	/**
	 *	Save modules data
	 */
	protected static function prepareAdditionalData_Modules(){
		$arResult = [];
		foreach(\Bitrix\Main\ModuleManager::getInstalledModules() as $strModuleId => $arModule){
			$arResult[$strModuleId] = Helper::getModuleVersion($strModuleId, null);
		}
		return static::saveBackupDataFile('modules.json', $arResult);
	}
	
	/**
	 *	Save handlers (b_module_to_module)
	 */
	protected static function prepareAdditionalData_Handlers(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_module_to_module` ORDER BY ID ASC;");
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['ID']] = $arItem;
		}
		return static::saveBackupDataFile('handlers.json', $arResult);
	}
	
	/**
	 *	Save agents
	 */
	protected static function prepareAdditionalData_Agents(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_agent` ORDER BY ID DESC;");
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['ID']] = $arItem;
		}
		return static::saveBackupDataFile('agents.json', $arResult);
	}
	
	/**
	 *	Save db tables
	 */
	protected static function prepareAdditionalData_Tables(){
		$arResult = \WD\Utilities\Helper::getTableSize();
		foreach($arResult as $strTable => $arTable){
			$arResult[$strTable]['SIZE_FORMATTED'] = Helper::formatSize($arTable['SIZE']);
		}
		ksort($arResult);
		return static::saveBackupDataFile('tables.json', $arResult);
	}
	
	/**
	 *	Save sites
	 */
	protected static function prepareAdditionalData_Sites(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_lang` ORDER BY SORT ASC;");
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['LID']] = $arItem;
		}
		return static::saveBackupDataFile('sites.json', $arResult);
	}
	
	/**
	 *	Save templates
	 */
	protected static function prepareAdditionalData_Templates(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_site_template` ORDER BY ID ASC;");
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['ID']] = $arItem;
		}
		return static::saveBackupDataFile('templates.json', $arResult);
	}
	
	/**
	 *	Save templates
	 */
	protected static function prepareAdditionalData_Events(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_event_type` ORDER BY EVENT_NAME ASC, ID ASC;");
		while($arItem = $resItems->fetch()){
			$arItem['TEMPLATES'] = [];
			$arResult[$arItem['EVENT_NAME']][$arItem['ID']] = $arItem;
		}
		$resItems = Helper::query("SELECT * FROM `b_event_message` ORDER BY ID ASC;");
		while($arItem = $resItems->fetch()){
			$arResult[$arItem['EVENT_NAME']]['TEMPLATES'][$arItem['ID']] = $arItem;
		}
		return static::saveBackupDataFile('events.json', $arResult);
	}
	
	/**
	 *	Save undo
	 */
	protected static function prepareAdditionalData_Undo(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_undo` ORDER BY TIMESTAMP_X DESC LIMIT 500;");
		while($arItem = $resItems->fetch()){
			$arItem['TEMPLATES'] = [];
			$arResult[] = $arItem;
		}
		return static::saveBackupDataFile('undo.json', $arResult);
	}
	
	/**
	 *	Save stickers
	 */
	protected static function prepareAdditionalData_Stickers(){
		$arResult = [];
		$resItems = Helper::query("SELECT * FROM `b_sticker` ORDER BY ID DESC LIMIT 500;");
		while($arItem = $resItems->fetch()){
			$arResult[] = $arItem;
		}
		return static::saveBackupDataFile('stickers.json', $arResult);
	}
	
	/**
	 *	Save admins
	 */
	protected static function prepareAdditionalData_Admins(){
		$arResult = [];
		$arSelect = ['ID', 'LOGIN', 'PASSWORD', 'CHECKWORD', 'ACTIVE', 'NAME', 'LAST_NAME', 'EMAIL', 'LAST_LOGIN', 
			'DATE_REGISTER', 'LID', 'XML_ID', 'EXTERNAL_AUTH_ID', 'CHECKWORD_TIME', 'SECOND_NAME', 'LOGIN_ATTEMPTS', 
			'LAST_ACTIVITY_DATE', 'TIME_ZONE', 'TIME_ZONE_OFFSET', 'BX_USER_ID', 'BLOCKED', 'IS_ONLINE'];
		$resUsers = \CUser::getList($by='ID', $order='ASC', ['GROUPS_ID' => [1]], ['FIELDS' => $arSelect]);
		while($arUser = $resUsers->fetch()){
			$arResult[$arUser['LOGIN']] = $arUser;
		}
		return static::saveBackupDataFile('admins.json', $arResult);
	}
	
	/**
	 *	Save system info
	 */
	protected static function prepareAdditionalData_System(){
		$arResult = [
			'PHP_VERSION' => static::getConst('PHP_VERSION'),
			'CONFIG' => ini_get_all(),
			'SERVER' => $_SERVER,
			'CONST' => get_defined_constants(true),
			'EXTENSIONS' => get_loaded_extensions(),
			'UNAME' => php_uname(),
			'ENV' => getenv(),
			'EXEC' => [
				'php -i',
				'uname -a',
				'cat /proc/cpuinfo',
				'df -h',
				'whoami',
				'whereis php',
				'uptime',
			],
		];
		unset($arResult['CONST']['standard']['INF']);
		unset($arResult['CONST']['standard']['NAN']);
		unset($arResult['CONST']['Core']['STDIN']);
		unset($arResult['CONST']['Core']['STDOUT']);
		unset($arResult['CONST']['Core']['STDERR']);
		$arCommands = [];
		foreach($arResult['EXEC'] as $key => $strCommand){
			$arCommands[$strCommand] = static::exec($strCommand);
		}
		$arResult['EXEC'] = $arCommands;
		return static::saveBackupDataFile('system.json', $arResult);
	}
	
	/**
	 *	Get constant value (if not defined => empty string)
	 */
	protected static function getConst($strConst){
		return defined($strConst) ? constant($strConst) : '';
	}
	
	/**
	 *	Execute command
	 */
	protected static function exec($strCommand){
		$arResult = [];
		if(function_exists('exec')){
			exec($strCommand, $arResult);
			if(!is_array($arResult)){
				$arResult = [];
			}
		}
		return $arResult;
	}
	
	/**
	 *	Send email with backup file
	 */
	protected static function sendEmail($arBackupFiles, $strZipFilePath){
		$bResult = false;
		if(is_file($strZipFilePath) && filesize($strZipFilePath)){
			if(Helper::getOption(WDU_MODULE, 'backups_send_to_email') == 'Y'){
				$arEmails = Helper::splitCommaValues(Helper::getOption(WDU_MODULE, 'backups_email'));
				foreach($arEmails as $strEmail){
					$arFields = [
						'EMAIL_TO' => $strEmail,
						'FILESIZE' => Helper::formatSize(filesize($strZipFilePath), 2, true),
						'FILENAME' => Helper::getFilenameRel($strZipFilePath),
						'SITE_ID' => SITE_ID,
						'LANGUAGE_ID' => LANGUAGE_ID,
					];
					$arEvent = [
						'EVENT_NAME' => 'WDU_BACKUP',
						'C_FIELDS' => $arFields,
						'LID' => Helper::getSiteByDomain(),
						'FILE' => [
							$strZipFilePath,
						],
					];
					if(\Bitrix\Main\Mail\Event::send($arEvent)->isSuccess()){
						$bResult = true;
					}
					Helper::checkEvents();
				}
			}
		}
		return $bResult;
	}
	
	/**
	 *	Remove old backups
	 */
	public static function removeOldBackups(){
		$strBackupCount = Helper::getOption(WDU_MODULE, 'backups_count');
		if(strlen($strBackupCount) && $strBackupCount != '0'){
			$arBackupsAll = static::getBackupsList();
			$arDeleteBackups = [];
			if(is_numeric($strBackupCount) && $strBackupCount > 0){
				# By count
				$arDeleteBackups = array_slice($arBackupsAll, $strBackupCount);
			}
			else{
				# By size
				$strBackupCount = str_replace(' ', '', $strBackupCount);
				if(preg_match('#^([\d]*)(Kb|Mb|Gb)$#i', $strBackupCount, $arMatch)){
					$arMapSize = ['KB' => 1024, 'MB' => pow(1024, 2), 'GB' => pow(1024, 3)];
					$strValue = floatVal($arMatch[1]);
					$strUnit = toUpper($arMatch[2]);
					if($intMaxSize = $strValue * $arMapSize[$strUnit]){
						$intCurSize = 0;
						foreach($arBackupsAll as $strFile){
							if($intCurSize > $intMaxSize){
								$arDeleteBackups[] = $strFile;
								continue;
							}
							$intCurSize += filesize(Helper::root().$strFile);
							if($intCurSize > $intMaxSize){
								$arDeleteBackups[] = $strFile;
							}
						}
					}
				}
				# By time
				elseif(preg_match('#^([\d]*)(h|d|m)$#i', $strBackupCount, $arMatch)){
					$arMapSize = ['H' => 60*60, 'D' => 24*60*60, 'M' => 30*24*60*60];
					$strValue = floatVal($arMatch[1]);
					$strUnit = toUpper($arMatch[2]);
					if($intMaxTime = $strValue * $arMapSize[$strUnit]){
						foreach($arBackupsAll as $strFile){
							if(time() - filemtime(Helper::root().$strFile) > $intMaxTime){
								$arDeleteBackups[] = $strFile;
							}
						}
					}
				}
			}
			# Delete!
			if(!empty($arDeleteBackups)){
				foreach($arDeleteBackups as $strFile){
					if(is_file(Helper::root().$strFile)){
						unlink(Helper::root().$strFile);
					}
				}
			}
		}
	}
	
	/**
	 *	Get list of backups
	 */
	public static function getBackupsList(){
		$arResult = [];
		$strDir = static::getBackupDir(static::SUBDIR_ARCHIVES);
		$arFiles = Helper::scandir($strDir, ['RECURSIVELY' => false, 'RELATIVE' => true, 'FILES' => true, 'DIRS' => false]);
		foreach($arFiles as $strFile){
			if(toUpper(pathinfo($strFile, PATHINFO_EXTENSION)) == 'ZIP'){
				$arResult[] = $strFile;
			}
		}
		usort($arResult, function($a, $b){
			$timeA = filemtime(Helper::root().$a);
			$timeB = filemtime(Helper::root().$b);
			if($timeA == $timeB){
				return 0;
			}
			return ($timeA < $timeB) ? 1 : -1;
		});
		return $arResult;
	}
	
	/**
	 *	Execute backup by agent
	 */
	public static function agent(){
		if(static::isEnabled()){
			static::execute();
		}
		return static::getAgentFunc();
	}
	
	/**
	 *	Get function for agent
	 */
	public static function getAgentFunc(){
		return sprintf('%s::%s();', __CLASS__, 'agent');
	}
	
	/**
	 *	Add backup agent
	 */
	public static function addAgent(){
		$bResult = false;
		$arAgent = [
			'FUNC' => static::getAgentFunc(),
			'MODULE_ID' => WDU_MODULE,
		];
		if(static::isEnabled()){
			if($intInterval = static::getAgentInterval()){
				$arAgent['INTERVAL'] = $intInterval;
				$bResult = !!Helper::addAgent($arAgent);
			}
		}
		else{
			Helper::removeAgent($arAgent);
		}
		return $bResult;
	}
	
	/**
	 *	Get time interval for agent
	 */
	public static function getAgentInterval(){
		$intResult = 0;
		# Get real value (considering custom or not)
		$strInterval = Helper::getOption(WDU_MODULE, 'backups_interval');
		if($strInterval == static::INTERVAL_CUSTOM){
			$strInterval = Helper::getOption(WDU_MODULE, 'backups_interval_custom');
		}
		# Process obtained value
		if(is_numeric($strInterval) && $strInterval > 0){
			$intResult = intVal($strInterval);
		}
		elseif(preg_match('#^([\d]*)(h|d|m)$#i', $strInterval, $arMatch)){
			$arMapSize = ['H' => 60*60, 'D' => 24*60*60, 'M' => 30*24*60*60];
			$strValue = floatVal($arMatch[1]);
			$strUnit = toUpper($arMatch[2]);
			$intResult = $strValue * $arMapSize[$strUnit];
		}
		return $intResult;
	}
	
}
