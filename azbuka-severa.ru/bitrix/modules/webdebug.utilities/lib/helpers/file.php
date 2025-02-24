<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Cli,
	\WD\Utilities\Helper;

Helper::loadMessages();

final class File {
	
	/**
	 *	Scan directory [can run recursively]
	 *	Params:
	 *		CALLBACK($strFileName, $arParams), default null
	 *		EXT [string|array]
	 *		RECURSIVELY [true|false], default true
	 *		FILES [true|false], default true
	 *		DIRS [true|false], default false
	 *		RELATIVE [true|false], default false
	 */
	public static function scandir($strDir, array $arParams=[]) {
		$strDirOriginal = $strDir;
		$bRelative = $arParams['RELATIVE'] ? true : false;
		$arResult = [];
		$strDir = Helper::path($strDir);
		$strDir = Helper::removeTrailingBackslash($strDir);
		$strPath = ($bRelative ? Helper::root() : '').$strDir;
		if(!is_array($arParams)){
			$arParams = [];
		}
		if($arParams['RECURSIVELY'] !== false){
			$arParams['RECURSIVELY'] = true;
		}
		if($arParams['FILES'] !== false){
			$arParams['FILES'] = true;
		}
		if(strlen($strDirOriginal) && is_dir($strPath)){
			$resHandle = opendir($strPath);
			while(($strItem = readdir($resHandle)) !== false)  {
				if(!in_array($strItem, ['.', '..'])) {
					if(is_file($strPath.'/'.$strItem)) {
						if($arParams['FILES']){
							if(isset($arParams['EXT'])){
								$strExt = toUpper(pathinfo($strItem, PATHINFO_EXTENSION));
								$bAppropriate = (is_string($arParams['EXT']) && toUpper($arParams['EXT']) == $strExt) 
									|| is_array($arParams['EXT']) && in_array($strExt, array_map(function($strItem){
										return toUpper($strItem);
									}, $arParams['EXT']));
								if(!$bAppropriate){
									continue;
								}
							}
							$mCallbackResult = null;
							if(is_callable($arParams['CALLBACK'])){
								$mCallbackResult = call_user_func_array($arParams['CALLBACK'], 
									[$strDir.'/'.$strItem, $arParams]);
							}
							if($mCallbackResult === false){
								continue;
							}
							$arResult[] = $strDir.'/'.$strItem;
						}
					}
					elseif(is_dir($strPath.'/'.$strItem)) {
						if($arParams['DIRS']){
							$arResult[] = $strDir.'/'.$strItem;
						}
						if($arParams['RECURSIVELY']){
							$arResult = array_merge($arResult, static::scandir($strDir.'/'.$strItem, $arParams));
						}
					}
				}
			}
			closedir($resHandle);
		}
		sort($arResult);
		return $arResult;
	}

	/**
	 *	Get filename whereis class defined
	 */
	public static function getClassFilename($strClass){
		$obReflectionClass = new \ReflectionClass($strClass);
		$strFileClass = $obReflectionClass->getFileName();
		unset($obReflectionClass);
		return $strFileClass;
	}

	/**
	 *	Get module relative dir
	 *	$strRelativeDir is a child dir in module
	 */
	public static function getModuleDir($strModuleId, $strChildDir=null){
		$strResult = getLocalPath('modules/'.$strModuleId);
		if(is_string($strChildDir) && strlen($strChildDir)){
			$strResult .= '/'.$strChildDir;
			$strResult = str_replace('//', '/', $strResult);
		}
		return $strResult;
	}
	
	/**
	 *	Include file from /include/ of choosen module
	 */
	public static function includeFile($strModuleId, $strFile, $arParams=[]){
		$strFile = static::getModuleDir($strModuleId, '/include/'.$strFile);
		ob_start();
		if(is_file(Helper::root().$strFile)){
			$arParams = is_array($arParams) ? $arParams : [];
			$GLOBALS['arParams'] = $arParams; // If in demo-mode, 2nd argument is not '$arParams' :( - this looks like $_1565435424
			$strModuleCode = str_replace('.', '_', $strModuleId);
			Helper::loadMessages(Helper::root().$strFile);
			include(Helper::root().$strFile);
		}
		return ob_get_clean();
	}
	
	/**
	 *	Clear file name from special chars (allowed just A-z, 0-9, _, -)
	 */
	public static function clearFilename($strFilename){
		return preg_replace('#[^A-z0-9_-]#', '', $strFilename);
	}
	
	/**
	 *	Create directories path for file
	 */
	public static function createDirectoriesForFile($strFileName, $bAutoChangeOwner=false){
		$strDirname = static::getFileDir($strFileName);
		if(!is_dir($strDirname)){
			@mkdir($strDirname, BX_DIR_PERMISSIONS, true);
		}
		if($bAutoChangeOwner){
			$strPath = substr(pathinfo($strFileName, PATHINFO_DIRNAME), strlen(Helper::root()));
			$strPath = trim(Helper::path($strPath), '/');
			$arPath = explode('/', $strPath);
			for($i=1; $i <= count($arPath); $i++){
				$strPath = implode('/', array_slice($arPath, 0, $i));
				if(strlen($strPath)){
					$strPath = '/'.$strPath;
					if(is_dir(Helper::root().$strPath)){
						static::changeFileOwner(Helper::root().$strPath);
					}
				}
			}
		}
		return is_dir($strDirname);
	}
	
	/**
	 *	Get file directory
	 */
	public static function getFileDir($strFileName){
		return pathinfo($strFileName, PATHINFO_DIRNAME);
	}
	
	/**
	 *	Get upload dir from config
	 */
	public static function getUploadDir($strSubdir=null, $bAbsolute=false){
		$strUploadDir = Helper::getOption('main', 'upload_dir');
		if(!strlen($strUploadDir)){
			$strUploadDir = 'upload';
		}
		$strUploadDir = '/'.$strUploadDir;
		if(is_string($strSubdir) && $strSubdir){
			$strSubdir = Helper::path($strSubdir);
			if(Helper::substr($strSubdir, 0, 1) != '/'){
				$strSubdir = '/'.$strSubdir;
			}
			$strUploadDir .= $strSubdir;
		}
		if($bAbsolute){
			$strUploadDir = Helper::root().$strUploadDir;
		}
		return $strUploadDir;
	}
	
	/**
	 *	Change log-file owner
	 */
	public static function changeFileOwner($strFilename){
		if(Cli::isCli() && Cli::isRoot() && function_exists('fileowner')){
			if(is_file($strFilename) || is_dir($strFilename)){
				$intBitrixUser = Cli::getBitrixUser();
				if(is_numeric($intBitrixUser)){
					$intOwner = @fileowner($strFilename);
					if($intOwner === 0){
						if(function_exists('chown')){
							if(chown($strFilename, $intBitrixUser)){
								if(function_exists('chgrp')){
									if(chgrp($strFilename, $intBitrixUser)){
										return true;
									}
								}
							}
						}
					}
					elseif($intOwner === $intBitrixUser){
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 *	Make directory
	 */
	public static function mkDir($strDirectory){
		return \Bitrix\Main\IO\Directory::createDirectory($strDirectory)->isExists();
	}

	/**
	 *	Try absolute filename to relative
	 */
	public static function getFilenameRel($strFilename){
		if(Helper::strpos($strFilename, Helper::root()) === 0){
			return Helper::substr($strFilename, Helper::strlen(Helper::root()));
		}
		return false;
	}

	/**
	 *	Remove empty directories recursively to root
	 *	$strDirectory is a relative folder
	 */
	public static function removeEmptyDirectories($strDirectory, $strTreshold=null){
		while(true){
			$bEmpty = true;
			$strPath = Helper::root().$strDirectory;
			if(!is_dir($strPath) || $strDirectory == $strTreshold){
				break;
			}
			$resHandle = opendir($strPath);
			while(($strFile = readdir($resHandle)) !== false){
				if(!in_array($strFile, ['.', '..'])){
					$bEmpty = false;
					break;
				}
			}
			closedir($resHandle);
			if($bEmpty){
				@rmdir($strPath);
			}
			else{
				break;
			}
			$strDirectory = pathinfo($strDirectory, PATHINFO_DIRNAME);
			if(strlen($strDirectory) <= 1){
				break;
			}
		}
	}
	
	/**
	 *	Delete file and its empty parent folders
	 *	$strFilename is a relative filename
	 */
	public static function deleteFileAndEmptyParents($strFilename, $strTreshold=null){
		if(is_file(Helper::root().$strFilename)){
			unlink(Helper::root().$strFilename);
		}
		$strDir = pathinfo($strFilename, PATHINFO_DIRNAME);
		if(is_dir(Helper::root().$strDir)){
			Helper::removeEmptyDirectories($strDir, $strTreshold);
		}
	}

}
