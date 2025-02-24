<?
namespace WD\Utilities;

Helper::loadMessages();

final class Helper {
	
	
	/*** MAIN ***********************************************************************************************************/
	
	// Is site works in UTF-8
	public static function isUtf(){
		return Helpers\Main::isUtf();
	}
	
	// Get document root
	public static function root(){
		return Helpers\Main::root();
	}
	
	// Check if site works via HTTPS
	public static function isHttps() {
		return Helpers\Main::isHttps();
	}

	// Check if current request is GET
	public static function isGet() {
		return Helpers\Main::isGet();
	}
	
	// Check if current request is POST
	public static function isPost() {
		return Helpers\Main::isPost();
	}
	
	// Check we are in admin section
	public static function isAdminSection() {
		return Helpers\Main::isAdminSection();
	}
	
	// Get $_SERVER array
	public static function getServer($strKey=null) {
		return Helpers\Main::getServer($strKey);
	}
	
	// Check if current request is AJAX
	public static function isAjax() {
		return Helpers\Main::isAjax();
	}
	
	// Get current request url
	public static function getUrl() {
		return Helpers\Main::getUrl();
	}

	// Get current domain (without port)
	public static function getCurrentDomain($bConvertToPuny=false){
		return Helpers\Main::getCurrentDomain($bConvertToPuny);
	}
	
	// Restart buffering
	public static function obRestart(){
		return Helpers\Main::obRestart();
	}
	
	// Stop buffering
	public static function obStop(){
		return Helpers\Main::obStop();
	}
	
	// Is value empty?
	public static function isEmpty($mValue) {
		return Helpers\Main::isEmpty($mValue);
	}
	
	// Is managed cache on ?
	public static function isManagedCacheOn(){
		return Helpers\Main::isManagedCacheOn($strValue);
	}
	
	// Replace "\" to "/" in paths
	public static function path($strPath){
		return Helpers\Main::path($strPath);
	}
	
	// Remove slashes at the end of text
	public static function removeTrailingBackslash($strText){
		return Helpers\Main::removeTrailingBackslash($strText);
	}
	
	// Get event handlers all
	public static function getEventHandlers($strModuleId, $strEvent){
		return Helpers\Main::getEventHandlers($strModuleId, $strEvent);
	}
	
	// Get $_GET and $_POST
	public static function getRequestQuery(){
		return Helpers\Main::getRequestQuery();
	}
	
	// Wrapper for unserialize(), but as array() anyway
	public static function unserialize($strValue, $arOptions=[]){
		return Helpers\Main::unserialize($strValue, $arOptions);
	}
	
	// Send prepared events from b_event
	public static function checkEvents(){
		return Helpers\Main::checkEvents();
	}
	
	// Add agent (remove it first, if exists)
	public static function addAgent(array $arAgent){
		return Helpers\Main::addAgent($arAgent);
	}
	
	// Remove agent
	public static function removeAgent(array $arAgent){
		return Helpers\Main::removeAgent($arAgent);
	}

	// Get Bitrix license hash (md5)
	public static function getLicenseHash(){
		return Helpers\Main::getLicenseHash();
	}
	
	/*** MBSTRING *******************************************************************************************************/
	
	// strlen
	public static function strlen($string){
		return Helpers\Mbstring::strlen($string);
	}

	// strpos
	public static function strpos($haystack, $needle, $offset=0){
		return Helpers\Mbstring::strpos($haystack, $needle, $offset);
	}

	// strrpos
	public static function strrpos($haystack, $needle, $offset=0){
		return Helpers\Mbstring::strrpos($haystack, $needle, $offset);
	}

	// substr
	public static function substr($string, $start, $length=null){
		return Helpers\Mbstring::substr($string, $start, $length);
	}

	// strtolower
	public static function strtolower($string){
		return Helpers\Mbstring::strtolower($string);
	}

	// strtoupper
	public static function strtoupper($string){
		return Helpers\Mbstring::strtoupper($string);
	}

	// stripos
	public static function stripos($haystack, $needle, $offset=0){
		return Helpers\Mbstring::stripos($haystack, $needle, $offset);
	}

	// strripos
	public static function strripos($haystack, $needle, $offset=0){
		return Helpers\Mbstring::strripos($haystack, $needle, $offset);
	}

	// strstr
	public static function strstr($haystack, $needle, $before_needle=false){
		return Helpers\Mbstring::strstr($haystack, $needle, $before_needle);
	}

	// stristr
	public static function stristr($haystack, $needle, $before_needle=false){
		return Helpers\Mbstring::stristr($haystack, $needle, $before_needle);
	}

	// strrchr
	public static function strrchr($haystack, $needle, $part=false){
		return Helpers\Mbstring::strrchr($haystack, $needle, $part);
	}

	// substr_count
	public static function substr_count($haystack, $needle, $offset=0, $length=null){
		return Helpers\Mbstring::substr_count($haystack, $needle, $offset, $length);
	}
	
	/*** OPTION *********************************************************************************************************/
	
	// Get option value
	public static function getOption($strModuleId, $strOption, $mDefaultValue=null){
		return Helpers\Option::getOption($strModuleId, $strOption, $mDefaultValue);
	}
	
	// Set option value
	public static function setOption($strModuleId, $strOption, $mValue){
		return Helpers\Option::setOption($strModuleId, $strOption, $mValue);
	}
	
	// Remove single option
	public static function removeOption($strModuleId, $strOption){
		return Helpers\Option::removeOption($strModuleId, $strOption);
	}
	
	// Remove all options
	public static function removeAllOptions($strModuleId){
		return Helpers\Option::removeAllOptions($strModuleId);
	}

	// Get default options for module
	public static function getDefaults($strModuleId){
		return Helpers\Option::getDefaults($strModuleId);
	}
	
	// Get user option value
	public static function getUserOption($strModuleId, $strOption, $mDefaultValue=null){
		return Helpers\Option::getUserOption($strModuleId, $strOption, $mDefaultValue);
	}
	
	// Set user option value
	public static function setUserOption($strModuleId, $strOption, $mValue){
		return Helpers\Option::setUserOption($strModuleId, $strOption, $mValue);
	}
	
	/*** DATABASE *******************************************************************************************************/
	
	// Prepare string for use in SQL
	public static function forSql($strValue){
		return Helpers\Database::forSql($strValue);
	}
	
	// Execute SQL query
	public static function query($strSql){
		return Helpers\Database::query($strSql);
	}

	// Transaction: start
	public static function startTransaction(){
		return Helpers\Database::startTransaction();
	}

	// Transaction: commit (apply)
	public static function commitTransaction(){
		return Helpers\Database::commitTransaction();
	}

	// Transaction: rollback (cancel)
	public static function rollbackTransaction(){
		return Helpers\Database::rollbackTransaction();
	}
	
	// Get database table size
	public static function getTableSize($mTable=false){
		return Helpers\Database::getTableSize($mTable);
	}
	
	// Get table fields with extended data
	public static function getTableFields($strTableName){
		return Helpers\Database::getTableFields($strTableName);
	}
	
	// Get table fields for selected ORM class
	public static function getEntityFields($strClass){
		return Helpers\Database::getEntityFields($strClass);
	}
	
	/*** LANGUAGE *******************************************************************************************************/
	
	// Wrapper for Loc::loadMessages()
	public static function loadMessages($strFile=false){
		return Helpers\Language::loadMessages($strFile);
	}
	
	// nalog for Loc::getMessage()
	public static function getMessage($strMessage, $strPrefix=null, $arReplace=null, $bDebug=false){
		return Helpers\Language::getMessage($strMessage, $strPrefix, $arReplace, $bDebug);
	}
	
	/*** ENCODING *******************************************************************************************************/
	
	// Convert charset (CP1251->UTF-8 || UTF-8->CP1251)
	public static function convertEncoding($mText, $strFrom='UTF-8', $strTo='CP1251') {
		return Helpers\Encoding::convertEncoding($mText, $strFrom, $strTo);
	}
	
	// Convert charset from site charset to specified charset
	public static function convertEncodingTo($mText, $strTo) {
		return Helpers\Encoding::convertEncodingTo($mText, $strTo);
	}
	
	// Convert charset from specified charset to site charset
	public static function convertEncodingFrom($mText, $strFrom) {
		return Helpers\Encoding::convertEncodingFrom($mText, $strFrom);
	}
	
	/*** VISUAL *********************************************************************************************************/
	
	// Show note
	public static function showNote($strNote, $bCompact=false, $bCenter=false, $bReturn=false) {
		return Helpers\Visual::showNote($strNote, $bCompact, $bCenter, $bReturn);
	}
	
	// Show success
	public static function showSuccess($strMessage=null, $strDetails=null) {
		return Helpers\Visual::showSuccess($strMessage, $strDetails);
	}
	
	// Show error
	public static function showError($strMessage=null, $strDetails=null) {
		return Helpers\Visual::showError($strMessage, $strDetails);
	}
	
	// Show error
	public static function showHeading($strMessage, $bNoMargin=false){
		return Helpers\Visual::showHeading($strMessage, $bNoMargin);
	}
	
	// Show hint
	public static function showHint($strText) {
		return Helpers\Visual::showHint($strText);
	}
	
	// Add notify
	public static function addNotify($strModuleId, $strMesage, $strTag, $bClose=true){
		return Helpers\Visual::addNotify($strModuleId, $strMesage, $strTag, $bClose);
	}
	
	// Delete notify
	public static function deleteNotify($strTag){
		return Helpers\Visual::deleteNotify($strTag);
	}
	
	// Get notify list
	public static function getNotifyList($strModuleId){
		return Helpers\Visual::getNotifyList($strModuleId);
	}
	
	/*** TEXT ***********************************************************************************************************/
	
	// Split 1, 2, 3 => [1, 2, 3]
	public static function splitCommaValues($strValues){
		return Helpers\Text::splitCommaValues($strValues);
	}
	
	// Split 1 2 3 => [1, 2, 3]
	public static function splitSpaceValues($strValues){
		return Helpers\Text::splitSpaceValues($strValues);
	}
	
	// Get rand string (32 chars)
	public static function randString($bWithPrefix=false){
		return Helpers\Text::randString($bWithPrefix);
	}
	
	// Translate ru -> en [by Yandex]
	public static function translate($strText){
		return Helpers\Text::translate($strText);
	}
	
	// Convert myTestValue to my_test_value
	public static function toUnderlineCase($strText, $bUpper=true){
		return Helpers\Text::toUnderlineCase($strText, $bUpper);
	}
	
	// Convert my_test_value to myTestValue
	public static function toCamelCase($strText){
		return Helpers\Text::toCamelCase($strText);
	}
	
	// Convert first symbol to upper case
	public static function ucFirst($strText){
		return Helpers\Text::ucFirst($strText);
	}
	
	/*** NUMBER *********************************************************************************************************/
	
	// Word form for russian (1 tevelizor, 2 tevelizora, 5 tevelizorov)
	public static function numberText($intValue, $mWords, $bShowValue=true) {
		return Helpers\Number::numberText($intValue, $mWords, $bShowValue);
	}
	
	// Format size (kilobytes, megabytes, ...)
	public static function formatSize($intSize, $intPrecision=2, $bRussian=false){
		return Helpers\Number::formatSize($intSize, $intPrecision, $bRussian);
	}
	
	// Round numeric value
	public static function roundEx($fValue, $intPrecision=0, $strFunc=null) {
		return Helpers\Number::roundEx($fValue, $intPrecision, $strFunc);
	}
	
	// Format elapsed time from 121 to 2:01
	public static function formatElapsedTime($intSeconds){
		return Helpers\Number::formatElapsedTime($intSeconds);
	}
	
	// Convert '10.075' and '10,075' to '10.075' (or '10,075' considering of locale settings)
	public static function convertDecimalPoint($strFloatValue){
		return Helpers\Number::convertDecimalPoint($strFloatValue);
	}
	
	/*** DEBUG **********************************************************************************************************/
	
	// Debug print
	public static function P($arData, $bJust=false, $bRemoveFunctions=false) {
		return Helpers\Debug::P($arData, $bJust, $bRemoveFunctions);
	}
	
	// Log
	public static function L($mMessage, $strFilename=false) {
		return Helpers\Debug::L($mMessage, $strFilename);
	}
	
	// Debug
	public static function D($bDebug=null){
		return Helpers\Debug::D($bDebug);
	}
	
	// Set current memory consumption
	public static function startMemoryTest(){
		return Helpers\Debug::startMemoryTest();
	}
	
	// Get memory consumption from last static::setMemory()
	public static function getMemoryTest(){
		return Helpers\Debug::getMemoryTest();
	}
	
	// Remove all functions from array for debug output
	public static function debugReplaceFunctions(array $arData){
		return Helpers\Debug::debugReplaceFunctions($arData);
	}
	
	/*** FILE ***********************************************************************************************************/
	
	// Scan directory [can run recursively]
	public static function scandir($strDir, $arParams=[]) {
		return Helpers\File::scandir($strDir, $arParams);
	}

	// Get filename whereis class defined
	public static function getClassFilename($strClass){
		return Helpers\File::getClassFilename($strClass);
	}

	// Get module absolute dir
	public static function getModuleDir($strModuleId, $strRelativeDir=null){
		return Helpers\File::getModuleDir($strModuleId, $strRelativeDir);
	}

	// Include file from /include/ of choosen module
	public static function includeFile($strModuleId, $strFile, $arParams=[]){
		return Helpers\File::includeFile($strModuleId, $strFile, $arParams);
	}
	
	// Clear file name from special chars (allowed just A-z, 0-9, _, -)
	public static function clearFilename($strFilename){
		return Helpers\File::clearFilename($strFilename);
	}
	
	// Create directories path for file
	public static function createDirectoriesForFile($strFileName, $bAutoChangeOwner=false){
		return Helpers\File::createDirectoriesForFile($strFileName, $bAutoChangeOwner);
	}
	
	// Get file directory
	public static function getFileDir($strFileName){
		return Helpers\File::getFileDir($strFileName);
	}
	
	// Get upload dir from config
	public static function getUploadDir($strSubdir=null, $bAbsolute=false){
		return Helpers\File::getUploadDir($strSubdir, $bAbsolute);
	}
	
	// Change file owner
	public static function changeFileOwner($strFilename){
		return Helpers\File::changeFileOwner($strFileName);
	}
	
	// Make directory
	public static function mkDir($strDirectory){
		return Helpers\File::mkDir($strDirectory);
	}
	
	// Try to get relative filename from absolute
	public static function getFilenameRel($strFilename){
		return Helpers\File::getFilenameRel($strFilename);
	}
	
	// Remove empty directories recursively to root
	public static function removeEmptyDirectories($strDirectory, $strTreshold=null){
		return Helpers\File::removeEmptyDirectories($strDirectory, $strTreshold);
	}
	
	// Delete file and its empty parent folders
	public static function deleteFileAndEmptyParents($strFilename, $strTreshold=null){
		return Helpers\File::deleteFileAndEmptyParents($strFilename, $strTreshold);
	}
	
	/*** ARRAY **********************************************************************************************************/
	
	// Is array associative?
	public static function isArrayAssociative(array $arData, $bDefaultResult){
		return Helpers\ArrayUtil::isArrayAssociative($arData, $bDefaultResult);
	}
	
	// Is array and not empty?
	public static function isNonEmptyArray($mValue) {
		return Helpers\ArrayUtil::isNonEmptyArray($mValue);
	}
	
	// Insert new key into array (in a selected place)
	public static function arrayInsert(array &$arData, $strKey, $mItem, $strAfter=null, $strBefore=null){
		return Helpers\ArrayUtil::arrayInsert($arData, $strKey, $mItem, $strAfter, $strBefore);
	}
	
	// Remove empty values from array (check by strlen(trim()))
	public static function arrayRemoveEmptyValues(&$arValues, $bTrim=true) {
		return Helpers\ArrayUtil::arrayRemoveEmptyValues($arValues, $bTrim);
	}
	
	// Remove empty values from array (check by strlen(trim()))
	public static function arrayRemoveEmptyValuesRecursive(&$arValues) {
		return Helpers\ArrayUtil::arrayRemoveEmptyValuesRecursive($arValues);
	}
	
	// Exec custom action for each element of array (or single if it is not array)
	public static function execAction($arData, $callbackFunction, $arParams=false){
		return Helpers\ArrayUtil::execAction($arData, $callbackFunction, $arParams);
	}
	
	// Get first non-empty value
	public static function getFirstValue($arValues, $bInteger=false){
		return Helpers\ArrayUtil::getFirstValue($arValues, $bInteger);
	}
	
	/*** SITE ***********************************************************************************************************/
	
	// Get all sites
	public static function getSitesList($bActive=false, $bSimple=false, $strField=null, $strOrder=null, $bIcons=false) {
		return Helpers\Site::getSitesList($bActive, $bSimple, $strField, $strOrder, $bIcons);
	}
	
	// Determine site by domain
	public static function getSiteByDomain($strDomain=null){
		return Helpers\Site::getSiteByDomain($strDomain);
	}
	
	// Get site favicon
	public static function getSiteIcon($arSite){
		return Helpers\Site::getSiteIcon($arSite);
	}
	
	// Format site name
	public static function formatSiteName($arSite){
		return Helpers\Site::formatSiteName($arSite);
	}
	
	// Format site URL
	public static function formatSiteUrl($strDomain, $bSSL, $strUrl=null) {
		return Helpers\Site::formatSiteUrl($strDomain, $bSSL, $strUrl);
	}
	
	/*** VERSION ********************************************************************************************************/
	
	// Get module version
	public static function getModuleVersion($strModuleId, $bDate=false){
		return Helpers\Version::getModuleVersion($strModuleId, $bDate);
	}
	
	// Is $strTestVersion equal (or more) than $strBaseVersion?
	public static function checkVersion($strTestVersion, $strBaseVersion){
		return Helpers\Version::checkVersion($strTestVersion, $strBaseVersion);
	}
	
	// Is catalog based on new filter? 
	public static function isCatalogNewFilter(){
		return Helpers\Version::isCatalogNewFilter();
	}
	
	/*** IBLOCK *********************************************************************************************************/

	// Get list of iblocks
	public static function getIBlocks($bGroupByType=true, $bShowInactive=false, $strSiteId=null) {
		return Helpers\IBlock::getIBlocks($bGroupByType, $bShowInactive, $strSiteId);
	}

	/*** CATALOG ********************************************************************************************************/

	// CCatalog::GetById with cache
	public static function getCatalogArray($intIBlockId) {
		return Helpers\Catalog::getCatalogArray($intIBlockId);
	}
	
	// Is stores available?
	public static function isCatalogStoresAvailable(){
		return Helpers\Catalog::isCatalogStoresAvailable();
	}
	
	// Is barcode available?
	public static function isCatalogBarcodeAvailable(){
		return Helpers\Catalog::isCatalogBarcodeAvailable();
	}
	
	/*** HTML ***********************************************************************************************************/

	// Wrapper for SelectBoxFromArray()
	public static function selectBox($strName, $arValues, $strSelected=null, $strDefault=null, $strAttr=null, 
			$strInputId=null, $bSelect2=true, $bSelect2Icons=false, $arSelect2Config=null){
		return Helpers\Html::selectBox($strName, $arValues, $strSelected, $strDefault, $strAttr, $strInputId, 
			$bSelect2, $bSelect2Icons, $arSelect2Config);
	}
	
	// 
	public static function selectBoxEx($strName, $arValues, $arParams=[]){
		return Helpers\Html::selectBoxEx($strName, $arValues, $arParams);
	}
	
	// 
	public static function selectBoxFromArray($strBoxName, $db_array, $mVal='', $strDefText='', $field1='', 
			$bSelect2Icons=null){
		return Helpers\Html::selectBoxFromArray($strBoxName, $db_array, $mVal, $strDefText, $field1, $bSelect2Icons);
	}

	/*** JS *************************************************************************************************************/

	// jQuery select2
	public static function addJsSelect2(){
		return Helpers\JsCss::addJsSelect2();
	}

	/*** USER ***********************************************************************************************************/

	// Get user title
	public static function getUserTitle($intUserId, $intMode=1){ // ToDo: get rid of magic number
		return Helpers\User::getUserTitle($intUserId, $intMode);
	}


	/*** DATE ***********************************************************************************************************/
	
	// Format date interval (example: «from 27 to 28 june 2020» or «from 10 may to 10 june 2020»)
	public static function formatDateInterval($strActiveFrom, $strActiveTo=false){
		return Helpers\Date::formatDateInterval($strActiveFrom, $strActiveTo);
	}

	/*** CACHE **********************************************************************************************************/
	
	// Universal cached GetList
	public static function cacheExec($strFuncName, $arArguments=null, $intCacheTime=null, $strCacheId=null, 
			$strCacheDir=null, $arCacheTags=null, $arModules=null, $strMethod=null){
		return Helpers\Cache::cacheExec($strFuncName, $arArguments, $intCacheTime, $strCacheId, $strCacheDir, 
			$arCacheTags, $arModules, $strMethod);
	}
	
}
