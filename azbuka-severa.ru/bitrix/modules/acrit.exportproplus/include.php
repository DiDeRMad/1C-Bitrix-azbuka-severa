<?
use
	\Acrit\Core\Helper;

# Definitions
$strCoreId = 'acrit.core';
$strModuleId = end(explode('/', str_replace('\\', '/', pathinfo(__FILE__, PATHINFO_DIRNAME))));
$strModuleCode = toUpper(preg_replace('#^.*?\.(.*?)$#', '$1', $strModuleId));

# General
IncludeModuleLangFile(__FILE__);
if(\Bitrix\Main\Loader::includeModule($strCoreId)){
	$arAutoload = [
		'OrdersProfilesTable',
		'CrmProfilesTable',
		'ProfileTable',
		'ProfileGroupTable',
		'ProfileIBlockTable',
		'ProfileFieldTable',
		'ProfileValueTable',
		'AdditionalFieldTable',
		'CategoryCustomNameTable',
		'CategoryRedefinitionTable',
		'ExportDataTable',
		'ExternalIdTable',
		'HistoryTable',
		//
		'ProfileFieldFeature',
		'Backup',
	];
	Helper::setModuleAutoloadClasses($strModuleId, $arAutoload, __NAMESPACE__);
	$GLOBALS['ACRIT_'.$strModuleCode.'_AUTOLOAD_CLASSES'] = &$arAutoload;
	if(Helper::getOption($strModuleId, 'disable_old_core') == 'Y'){
		return;
	}
}

# Prevent continue on PHP8 (if old core is not disabled)
if(checkVersion(PHP_VERSION, '8.0.0')){
	Helper::setOption($strModuleId, 'disable_old_core', 'Y');
	return;
}

#
if(!defined('ACRIT_CORE')){
	define('ACRIT_CORE', $strCoreId);
}

# Old module code
IncludeModuleLangFile( __FILE__ );
use Bitrix\Highloadblock\HighloadBlockTable;

global $DBType;

$arClasses = array(
    "AcritLicence" => "classes/general/licence.php",
    "CExportproplusProfileDB" => "classes/mysql/cexportproplusprofiledb.php",
    "CExportproplusMarketDB" => "classes/mysql/cexportpropluspro_marketdb.php",
    "CExportproplusMarketTiuDB" => "classes/mysql/cexportpropluspro_markettiudb.php",
    "CExportproplusMarketPromuaDB" => "classes/mysql/cexportpropluspro_marketpromuadb.php",
	"CExportproplusMarketMailruDB" => "classes/mysql/cexportpropluspro_marketmailrudb.php",
    "CExportproplusProfile" => "classes/general/cexportproplusprofile.php",
    "CExportproplusVariant" => "classes/general/cexportproplusprofile.php",

    "CAcritGlobalCondCtrl" => "classes/general/cexportpropluscond.php",
    "CAcritGlobalCondCtrlComplex" => "classes/general/cexportpropluscond.php",
    "CAcritGlobalCondCtrlAtoms" => "classes/general/cexportpropluscond.php",
    "CAcritGlobalCondCtrlGroup" => "classes/general/cexportpropluscond.php",
    "CAcritGlobalCondTree" => "classes/general/cexportpropluscond.php",
    "CAcritCatalogCondCtrl" => "classes/general/cexportpropluscond.php",
    "CAcritCatalogCondCtrlComplex" => "classes/general/cexportpropluscond.php",
    "CAcritCatalogCondCtrlGroup" => "classes/general/cexportpropluscond.php",
    "CAcritCatalogCondCtrlIBlockFields" => "classes/general/cexportpropluscond.php",
    "CAcritCatalogCondCtrlIBlockProps" => "classes/general/cexportpropluscond.php",
    "CAcritCatalogCondTree" => "classes/general/cexportpropluscond.php",

    "CAcritExportproplusCatalog" => "classes/general/cexportproplusfilter.php",
    "CAcritExportproplusPrices" => "classes/general/cexportproplusfilter.php",
    "CAcritExportproplusProps" => "classes/general/cexportproplusfilter.php",
    "CAcritExportproplusCatalogCond" => "classes/general/cexportproplusfilter.php",
    "CAcritExportproplusLog" => "classes/general/cexportpropluslog.php",
    "AcritExportproplusSession" => "classes/general/cexportproplussession.php",
    "CAcritExportproplusUrlRewrite" => "classes/general/cexportproplusurlrewrite.php",
    "CAcritExportproplusTools" => "classes/general/cexportproplustools.php",
    "CAcritExportproplusStringProcess" => "classes/general/cexportproplustools.php",
    "CAcritExportproplusMarketCategories" => "classes/general/cexportproplustools.php",

    "CAcritExportproplusExport" => "classes/general/cexportproplusexport.php",
    "CExportproplusAgent" => "classes/general/cexportproplusagent.php",
    "CExportproplusInformer" => "classes/general/cexportproplusinformer.php",
    "CExportproplusMarketEbayDB" => "classes/mysql/cexportpropluspro_marketebaydb.php",
    "Threads" => "classes/general/threads.php",
    "ThreadsSession" => "classes/general/threads.php",
    "CExportproplusOzon" => "classes/general/ozon.php",
    "OdnoklassnikiSDK" => "classes/general/odnoklassniki_sdk.php",
    "CAcritExportproplusVkTools" => "classes/general/cexportproplusvktools.php",
    "CAcritExportproplusFbTools" => "classes/general/cexportproplusfbtools.php",
    "CAcritExportproplusOkTools" => "classes/general/cexportproplusoktools.php",
    "CAcritExportproplusInstagramTools" => "classes/general/cexportproplusinstagramtools.php",
);

CModule::AddAutoloadClasses( "acrit.exportproplus", $arClasses );

require_once( "classes/general/Facebook/autoload.php" );
require_once( "classes/general/FacebookAds/autoload.php" );

if( class_exists( "XMLWriter" ) ){
    if( !class_exists( "PHPExcel" ) && !class_exists( "PHPExcel_IOFactory" ) ){
        require_once( __DIR__."/classes/general/PHPExcel.php" );
        require_once( __DIR__."/classes/general/PHPExcel/IOFactory.php" );
    }
}

class CAcritExportproplusMenu{
    public static function OnBuildGlobalMenu( &$aGlobalMenu, &$aModuleMenu ){
        global $USER, $APPLICATION, $adminMenu, $adminPage;
				if(\Bitrix\Main\Config\Option::get('acrit.exportproplus', 'disable_old_core') == 'Y'){
					return;
				}
        if( is_array( $adminMenu->aGlobalMenu ) && key_exists( "global_menu_acrit", $adminMenu->aGlobalMenu ) ){
            return;
        }

        $acritMenuGroupName = COption::GetOptionString( "acrit.exportproplus", "acritmenu_groupname" );
        if( strlen( trim( $acritMenuGroupName ) ) <= 0 ){
            $acritMenuGroupName = GetMessage( "ACRITMENU_GROUPNAME_DEFAULT" );
        }

        $aMenu = array(
            "menu_id" => "acrit",
            "sort" => 150,
            "text" => $acritMenuGroupName,
            "title" => GetMessage( "ACRIT_MENU_TITLE" ),
            "icon" => "clouds_menu_icon",
            "page_icon" => "clouds_page_icon",
            "items_id" => "global_menu_acrit",
            "items" => array()
        );
        $aGlobalMenu["global_menu_acrit"] = $aMenu;
    }
}

class CAcritExportproplusElement{
    public $profile = null;
    public $DEMO = 2;
    public $isDemo = true;
    public $DEMO_CNT;
    public $MODULEID = "acrit.exportproplus";
    public $stepElements = 50;
    public $dateFields = array();
    public $arCsvSpecials = array();
    public $log;
    public $session;
    public $baseDateTimePatern;
    public $basePriceId;

    public $bApiExport;

    public $obVkTools;
    public $obOkTools;
    public $obInstagramTools;
    public $obFb;
    public $obOzonTools;

    public $dbProfile;
    public $obProfileUtils;
    public $arMarketCategory;

    protected $profileEncoding = array(
        "utf8" => "utf-8",
        "cp1251" => "windows-1251",
    );

    public function __construct( $profile ){
        global $APPLICATION;

        $this->iblockIncluded = @CModule::IncludeModule( "iblock" );
        $this->hlBlockIncluded = @CModule::IncludeModule( "highloadblock" );
        $this->saleIncluded = @CModule::IncludeModule( "sale" );
        $this->catalogIncluded = @CModule::IncludeModule( "catalog" );

        $this->DEMO = CModule::IncludeModuleEx( $this->MODULEID );
        if( $this->DEMO == 1 ){
            $this->isDemo = false;
        }

        $this->DEMO_CNT = 50;
        $this->profile = $profile;

        $this->dbProfile = new CExportproplusProfileDB();

        $this->obProfileUtils = new CExportproplusProfile();
        $this->profile["PROFILE_CATEGORIES"] = $this->obProfileUtils->GetSections(
            $this->profile["IBLOCK_ID"],
            $this->profile["CHECK_INCLUDE"] == "Y",
            true
        );

        $this->obVkTools = new CAcritExportproplusVkTools( $profile );

        $this->bApiExport = false;
        if( ( $this->profile["TYPE"] == "ozon_api" ) || ( $this->profile["TYPE"] == "vk_trade" ) || ( $this->profile["TYPE"] == "fb_trade" ) || ( $this->profile["TYPE"] == "instagram_trade" ) || ( $this->profile["TYPE"] == "ok_trade" ) ){
            $this->bApiExport = true;
        }

        if( $profile["TYPE"] == "ozon_api" ){
            $this->obOzonTools = new CExportproplusOzon( $profile["OZON_APPID"], $profile["OZON_APPKEY"] );
        }

        $this->obOkTools = new CAcritExportproplusOkTools( $profile );
        $this->obInstagramTools = new CAcritExportproplusInstagramTools( $profile );
        $this->obFb = new CAcritExportproplusFb( $profile );

        if( intval( $this->profile["SETUP"]["EXPORT_STEP"] ) > 0 )
            $this->stepElements = $this->profile["SETUP"]["EXPORT_STEP"];

        $this->dateFields = array(
            "TIMESTAMP_X",
            "DATE_CREATE",
            "DATE_ACTIVE_FROM",
            "DATE_ACTIVE_TO"
        );

        $this->arCsvSpecials = array(
            "advantshop",
            "pulscen_csv",
            "ym_simple_csv"
        );

        $this->log = new CAcritExportproplusLog( $this->profile["ID"] );

        $this->baseDateTimePatern = "Y-m-dTh:i:s±h:i";

        $paternCharset = CAcritExportproplusTools::GetStringCharset( $this->baseDateTimePatern );

        if( $paternCharset == "cp1251" ){
            $this->baseDateTimePatern = $APPLICATION->ConvertCharset( $this->baseDateTimePatern, "windows-1251", "UTF-8" );
        }

        $dateGenerate = ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ? CAcritExportproplusTools::GetYandexDateTime( date( "d.m.Y H:i:s" ) ) : date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), time() );

        $this->defaultFields = array(
            "#ENCODING#" => $this->profileEncoding[$this->profile["ENCODING"]],
            //"#DATE#" => $this->profile["DATEFORMAT"],
            "#SHOP_NAME#" => $this->profile["SHOPNAME"],
            "#COMPANY_NAME#" => $this->profile["COMPANY"],
            "#SITE_URL#" => $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"],
            "#PROFILE_DESCRIPTION#" => $this->profile["DESCRIPTION"],
            "#DATE#" => $dateGenerate,
        );

        $this->basePriceId = CAcritExportproplusTools::GetProcessPriceId( $this->profile );

        if( ( $this->profile["TYPE"] == "tiu_standart" ) || ( $this->profile["TYPE"] == "tiu_standart_vendormodel" ) ){
            $obMarketCategory = new CExportproplusMarketTiuDB();
        }
        elseif( $this->profile["TYPE"] == "ua_prom_ua" ){
            $obMarketCategory = new CExportproplusMarketPromuaDB();
        }
        elseif( $this->profile["TYPE"] == "mailru" || $this->profile["TYPE"] == "mailru_clothing"){
	        $obMarketCategory = new CExportproplusMarketMailruDB();
        }
        else{
            $obMarketCategory = new CExportproplusMarketDB();
        }

        $this->arMarketCategory = $obMarketCategory->GetMarketList( $this->profile["MARKET_CATEGORY"]["CATEGORY"] );
    }

    public static function OnBeforePropertiesSelect( &$arFields ){
        foreach( $arFields as $Key => &$arValue ){
            if( is_array( $arValue ) ){
                foreach( $arValue as &$Value ){
                    $arProperty = explode( "-", $Value );
                    $cProperty = count( $arProperty );
                    if( $cProperty == 3 ){
                        $Value = "PROPERTY_".$arProperty[2]."_DISPLAY_VALUE";
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                $cProperty = count( $arProperty );
                if( $cProperty == 3 ){
                    $arValue = "PROPERTY_".$arProperty[2]."_DISPLAY_VALUE";
                }
            }
        }
    }

    public function GetElementCount(){
        return $this->elementCount;
    }

    protected function DemoCount(){
        $arSessionData = AcritExportproplusSession::GetAllSession( $this->profile["ID"] );
        $demoCnt = 0;
        if( !empty( $arSessionData ) ){
            foreach( $arSessionData as $arSessionDataItem ){
                $demoCnt += $arSessionDataItem["EXPORTPROPLUS"][$this->profile["ID"]]["DEMO_COUNT"];
            }
        }

        return ( $demoCnt > $this->DEMO_CNT );
    }

    protected function DemoCountInc(){
        $sessionData = AcritExportproplusSession::GetSession( $this->profile["ID"] );
        if( !isset( $sessionData["EXPORTPROPLUS"][$this->profile["ID"]]["DEMO_COUNT"] ) )
            $sessionData["EXPORTPROPLUS"][$this->profile["ID"]]["DEMO_COUNT"] = 0;

        $sessionData["EXPORTPROPLUS"][$this->profile["ID"]]["DEMO_COUNT"]++;
        AcritExportproplusSession::SetSession( $this->profile["ID"], $sessionData );
    }

    public function ExportConvertCharset( $field ){
        global $APPLICATION;
        $result = "";

        $paternCharset = CAcritExportproplusTools::GetStringCharset( $field );
        $result = $APPLICATION->ConvertCharset( $field, $paternCharset, $this->profileEncoding[$this->profile["ENCODING"]] );

        return $result;
    }

    public function CalcProcessXMLLoadingByOneProduct(){
        $calcTimeStart = getmicrotime();

        $dbElements = self::PrepareProcess();
        if( !is_object( $dbElements ) ) return false;

        $sessionData = AcritExportproplusSession::GetSession( $this->profile["ID"] );
        $sessionData["EXPORTPROPLUS"]["LOG"][$this->profile["ID"]]["STEPS"] = $this->isDemo ? 1 : $dbElements->NavPageCount;
        AcritExportproplusSession::SetSession( $this->profile["ID"], $sessionData );

        while( $arElement = $dbElements->GetNextElement() ){
            $variantItems = array();
            $arItem = $this->ProcessElement( $arElement );

            if( !$arItem )
                continue;

            if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                $variantContainerId = $arItem["IBLOCK_ID"];
            }
            elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                $variantContainerId = $arItem["IBLOCK_PRODUCT_SECTION_ID"];
            }
            else{
                $variantContainerId = $arItem["IBLOCK_SECTION_ID"];
            }

            if( CAcritExportproplusTools::isVariant( $this->profile, $variantContainerId ) ){
                if( !$arItem["SKIP"] ){
                    $variantItems[$arItem["ITEM"][$variantPrice]][] = $arItem;
                }

                $arItem = $arItem["ITEM"];
            }
            unset( $arItem );
        }

        unset( $arElement, $arItem );

        CAcritExportproplusTools::SaveCurrencies( $this->profile, $this->currencyList );

        return round( getmicrotime() - $calcTimeStart, 3 );
    }

    public function Process( $page = 1, $cronrun = false, $fileType = "xml", $fileExport = false, $fileExportName = false, $arOzonCategories = false , &$_ProcessEnd = false, $bStepExport = false, $iLastSessionExportProductsCnt = 0, $processId = 0 ){
        global $fileExportDataSize, $fileExportData, $ProcessEnd;
        $fileThread = false;

        $this->SetProcessStart( $fileThread );
        if( $fileType == "csv" ){
            $ret = self::ProcessCSV( $page, $cronrun, $fileExport, $fileExportName );
        }
        elseif( $fileType == "xlsx" ){
            $ret = self::ProcessCSV( $page, $cronrun, $fileExport, $fileExportName, true );
        }
        else{
            $ret = self::ProcessXML( $page, $cronrun, $arOzonCategories, $bStepExport, $iLastSessionExportProductsCnt, $processId );
        }

        $this->SetProcessEnd( $fileThread );
        while( true !== $ProcessEnd ){
        }
        $_ProcessEnd = $ProcessEnd;

        return $ret;
    }

    public function PrepareProcess( $page = 1, $bStepExport = false ){
        if( $page == 1 ){
            $this->log->Init( $this->profile );
            $this->page = $page;
        }

        $this->currencyRates = CExportproplusProfile::LoadCurrencyRates();
        $iblockList = $this->PrepareIBlock();

        if( empty( $iblockList ) ){
            return true;
        }

        $pregMatchExp = GetMessage( "ACRIT_EXPORTPROPLUS_A_AA_A" );

        preg_match_all( "/.*(<[\w\d_-]+).*(#[\w\d_-]+:*[\w\d_-]+#).*(<\/.+>)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches );
        preg_match_all( "/(#[\w\d_-]+:*[\w\d_-]+#)/", $this->profile["OFFER_TEMPLATE"], $this->arMatches["ALL_TAGS"] );

        // install for all templates #EXAMPLE# null value, so that you can remove
        $this->templateValuesDefaults = array();
        foreach( $this->arMatches["ALL_TAGS"][0] as $match ){
            $this->templateValuesDefaults[$match] = "";
        }
        $this->templateValuesDefaults["#MARKET_CATEGORY#"] = "";

        // get the properties used in the templates
        $this->useProperties = array(
            "ID" => array()
        );

        $this->usePrices = array();
        foreach( $this->profile["XMLDATA"] as $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] )
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) || ( $field["TYPE"] == "composite" ) || ( $field["TYPE"] == "arithmetics" ) || ( $field["TYPE"] == "stack" ) ){

                if( $field["TYPE"] == "composite" ){
                    foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }

                    foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                        if( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                            $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }
                }
                elseif( $field["TYPE"] == "arithmetics" ){
                    foreach( $field["ARITHMETICS_TRUE"] as $arithmeticsFieldIndex => $arithmeticsField ){
                        if( $arithmeticsField["ARITHMETICS_TRUE_TYPE"] == "field" ){
                            $arValue = explode( "-", $arithmeticsField["ARITHMETICS_TRUE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }

                    foreach( $field["ARITHMETICS_FALSE"] as $arithmeticsFieldIndex => $arithmeticsField ){
                        if( $arithmeticsField["ARITHMETICS_FALSE_TYPE"] == "field" ){
                            $arValue = explode( "-", $arithmeticsField["ARITHMETICS_FALSE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }
                }
                elseif( $field["TYPE"] == "stack" ){
                    foreach( $field["STACK_TRUE"] as $stackFieldIndex => $stackField ){
                        if( $stackField["STACK_TRUE_TYPE"] == "field" ){
                            $arValue = explode( "-", $stackField["STACK_TRUE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }

                    foreach( $field["STACK_FALSE"] as $stackFieldIndex => $stackField ){
                        if( $stackField["STACK_FALSE_TYPE"] == "field" ){
                            $arValue = explode( "-", $stackField["STACK_FALSE_VALUE"] );

                            switch( count( $arValue ) ){
                                case 1:
                                    $this->useFields[] = $arValue[0];
                                    break;
                                case 2:
                                    $this->usePrices[] = $arValue[1];
                                    break;
                                case 3:
                                    $this->useProperties["ID"][] = $arValue[2];
                                    break;
                            }
                        }
                    }
                }
                else{
                    if( $field["TYPE"] == "field" ){
                        $fieldValue = $field["VALUE"];
                        $arValue = explode( "-", $fieldValue );

                        switch( count( $arValue ) ){
                            case 1:
                                $this->useFields[] = $arValue[0];
                                break;
                            case 2:
                                $this->usePrices[] = $arValue[1];
                                break;
                            case 3:
                                $this->useProperties["ID"][] = $arValue[2];
                                break;
                        }
                    }
                    elseif( $field["TYPE"] == "complex" ){
                        $fieldValue = $field["COMPLEX_TRUE_VALUE"];
                        $arValue = explode( "-", $fieldValue );

                        switch( count( $arValue ) ){
                            case 1:
                                $this->useFields[] = $arValue[0];
                                break;
                            case 2:
                                $this->usePrices[] = $arValue[1];
                                break;
                            case 3:
                                $this->useProperties["ID"][] = $arValue[2];
                                break;
                        }

                        $fieldValue = $field["COMPLEX_FALSE_VALUE"];
                        $arValue = explode( "-", $fieldValue );

                        switch( count( $arValue ) ){
                            case 1:
                                $this->useFields[] = $arValue[0];
                                break;
                            case 2:
                                $this->usePrices[] = $arValue[1];
                                break;
                            case 3:
                                $this->useProperties["ID"][] = $arValue[2];
                                break;
                        }
                    }

                    if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                        $arElementConfig["DELAY"] = true;
                    }
                }

                if( $field["CONDITION"]["CHILDREN"] ){
                    if( !function_exists( findChildren ) ){
                        function findChildren( $children ){
                            $retVal = array();
                            foreach( $children as $child ){
                                if( strstr( $child["CLASS_ID"], "CondIBProp" ) ){
                                    $arProp = explode( ":", $child["CLASS_ID"] );
                                    $retVal[] = $arProp[2];
                                }
                                if( $child["CHILDREN"] ){
                                    $retVal = array_merge( $retVal, findChildren( $child["CHILDREN"] ) );
                                }
                            }
                            return $retVal;
                        }
                    }
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], findChildren( $field["CONDITION"]["CHILDREN"] ) );
                }
            }

            if( $field["EVAL_FILTER"] ){
                preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );
                if( is_array( $filterProps[1] ) ){
                    $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
                }
                if( is_array( $filterProps[2] ) ){
                    $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
                }
            }
        }
        preg_match_all( "/.*?PROPERTY_(\d+)|(CATALOG_PRICE_[\d]+_WD|CATALOG_PRICE_[\d]+_D).*?/", $this->profile["EVAL_FILTER"], $filterProps );

        if( is_array( $filterProps[1] ) ){
            $this->useProperties["ID"] = array_merge( $this->useProperties["ID"], $filterProps[1] );
        }
        if( is_array( $filterProps[2] ) ){
            $this->usePrices = array_merge( $this->usePrices, $filterProps[2] );
        }
        $dbEvents = GetModuleEvents( "acrit.exportproplus", "OnBeforePropertiesSelect" );
        $eventResult = array();
        while( $arEvent = $dbEvents->Fetch() ){
            ExecuteModuleEventEx(
                $arEvent,
                array(
                    array(
                        "ID" => $this->profile["ID"],
                        "CODE" => $this->profile["CODE"],
                        "NAME" => $this->profile["NAME"]
                    ),
                    &$eventResult
                )
            );
        }

        foreach( $eventResult as $arValue ){
            if( is_array( $arValue ) ){
                foreach( $arValue as $Value ){
                    $arProperty = explode( "-", $Value );
                    if( count( $arProperty ) == 3 ){
                        $this->useProperties["ID"][] = $arProperty[2];
                    }
                }
            }
            else{
                $arProperty = explode( "-", $arValue );
                if( count( $arProperty ) == 3 ){
                    $this->useProperties["ID"][] = $arProperty[2];
                }
            }
        }
        $this->useProperties["ID"] = array_unique( $this->useProperties["ID"] );
        $this->useProperties["ID"] = array_filter( $this->useProperties["ID"] );

        $this->currencyList = array();

        // variant properties
        $variantPrice = str_replace( "-", "_", $this->profile["VARIANT"]["PRICE"] );
        $variantPropCode = array(
            "SEX_VALUE" => "SEX",
            "COLOR_VALUE" => "COLOR",
            "SIZE_VALUE" => "SIZE",
            "WEIGHT_VALUE" => "WEIGHT",
            "SEXOFFER_VALUE" => "SEXOFFER",
            "COLOROFFER_VALUE" => "COLOROFFER",
            "SIZEOFFER_VALUE" => "SIZEOFFER",
            "WEIGHTOFFER_VALUE" => "WEIGHTOFFER"
        );

        if( is_array( $this->profile["VARIANT"] ) && !empty( $this->profile["VARIANT"] ) ){
            foreach( $this->profile["VARIANT"] as $vpKey => $vpValue ){
                if( key_exists( $vpKey, $variantPropCode ) ){
                    $variantProperty = explode( "-", $vpValue );
                    if( count( $variantProperty ) == 3 ){
                        $this->useProperties["ID"][] = $variantProperty[2];
                        $this->variantProperties[$variantPropCode[$vpKey]] = "PROPERTY_".$variantProperty[2]."_DISPLAY_VALUE";
                    }
                }
            }
        }

        $arOrder = array(
            "IBLOCK_ID" => "ASC",
            "ID" => "ASC",
        );

        $arFilter = array(
            "IBLOCK_ID" => $iblockList,
            "SECTION_ID" => $this->profile["CATEGORY"],
        );

        if( $this->profile["CHECK_INCLUDE"] != "Y" ){
            $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
        }

        $arNavStartParams = array(
            "nPageSize" => $this->stepElements,
            "iNumPage" => $page
        );

        $dbElements = CIBlockElement::GetList(
            $arOrder,
            $arFilter,
            false,
            (  $bStepExport ) ? false : $arNavStartParams,
            array()
        );

        return $dbElements;
    }

    public function ProcessBasicCsv( $dbElements, $fileExport, $page, $navPageCount, $bXls = false ){
        if( $bXls && !class_exists( "XMLWriter" ) ){
            return false;
        }
				
					if( class_exists( "XMLWriter" ) ){
							if( !class_exists( "PHPExcel" ) && !class_exists( "PHPExcel_IOFactory" ) ){
									require_once( __DIR__."/classes/general/PHPExcel.php" );
									require_once( __DIR__."/classes/general/PHPExcel/IOFactory.php" );
							}
					}

        if( !$dbElements->SelectedRowsCount() || !$fileExport ){
            return false;
        }

        if( $page == 1 ){
            $arPaternFields = array();
        }

        if( $this->profile["TYPE"] == "advantshop" ){
            $specialColumn = "sku_size_color_price_purchaseprice_amount";
        }
												
        $bSchemeUseOffer = false;
        $bSchemeUseOfferSku = false;
        $bSchemeUseSku = false;
        $bSchemeUseSkuByOffer = false;

        if( ( $this->profile["EXPORT_DATA_OFFER"] == "Y" ) ){
            $bSchemeUseOffer = true;
        }

        if( $this->profile["EXPORT_DATA_OFFER_WITH_SKU_DATA"] == "Y" ){
            $bSchemeUseOfferSku = true;
        }

        if( $this->profile["EXPORT_DATA_SKU"] == "Y" ){
            $bSchemeUseSku = true;
        }

        if( $this->profile["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ){
            $bSchemeUseSkuByOffer = true;
        }

        while( $dbElement = $dbElements->GetNextElement() ){
            $arRowToCsv = $this->ProcessElement( $dbElement, false, true );
            if( $arRowToCsv ){
                if( empty( $arPaternFields ) && ( $page == 1 ) ){
                    foreach( $arRowToCsv as $colIndex => $colValue ){
                        if( ( $this->profile["TYPE"] == "advantshop" ) && ( $colIndex == $specialColumn ) ){
                            $colIndex = str_replace( "_", ":", $colIndex );
                        }
                        $arPaternFields[] = $colIndex;
                    }
                }

                if( ( $bSchemeUseOffer || $bSchemeUseOfferSku || $bSchemeUseSkuByOffer ) && $this->profile["TYPE"] != "advantshop" ){
                    $arProcess[] = $arRowToCsv;
                }

                $arItem = $this->GetElementProperties( $dbElement );

                if( $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y") && ( $bSchemeUseOfferSku || $bSchemeUseSku ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) ){
                    $arOfferFilter = array(
                        "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                        "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
                    );

                    $dbOfferElements = CIBlockElement::GetList(
                        array(),
                        $arOfferFilter,
                        false,
                        false,
                        array()
                    );

                    if( $this->profile["TYPE"] == "advantshop" ){
                        $sAdvantShopOffersRow = "";
                    }

                    while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                        $arOfferRowToCsv = $this->ProcessElement( $arOfferElement, $arItem, true );

                        if( !$arOfferRowToCsv ) continue;

                        if( empty( $arPaternFields ) && ( $page == 1 ) ){
                            foreach( $arOfferRowToCsv as $colIndex => $colValue ){
                                $arPaternFields[] = $colIndex;
                            }
                        }

                        if( $this->profile["TYPE"] != "advantshop" ){
                            $arProcess[] = $arOfferRowToCsv;
                        }
                        else{
                            $sAdvantShopOffersRow .= ( ( strlen( $sAdvantShopOffersRow ) > 0 ) ? ";" : "" ).$arOfferRowToCsv[$specialColumn];
                        }

                        if( $this->isDemo && $this->DemoCount() ){
                            break;
                        }
                    }

                    if( $this->isDemo && $this->DemoCount() ){
                        break;
                    }
                }

                if( $this->profile["TYPE"] == "advantshop" ){
                    if( $this->profile["XMLDATA"][$specialColumn]["CONVERT_DATA_REGEXP"] == "Y" ){
                        if( !empty( $this->profile["XMLDATA"][$specialColumn]["CONVERT_DATA"] ) ){
                            foreach( $this->profile["XMLDATA"][$specialColumn]["CONVERT_DATA"] as $arConvertBlock ){
                                $sAdvantShopOffersRow = preg_replace( $arConvertBlock[0], $arConvertBlock[1], $sAdvantShopOffersRow );
                            }
                        }
                    }
                    else{
                        if( !empty( $this->profile["XMLDATA"][$specialColumn]["CONVERT_DATA"] ) ){
                            foreach( $this->profile["XMLDATA"][$specialColumn]["CONVERT_DATA"] as $arConvertBlock ){
                                $sAdvantShopOffersRow = str_replace( $arConvertBlock[0], $arConvertBlock[1], $sAdvantShopOffersRow );
                            }
                        }
                    }

                    $arRowToCsv[$specialColumn] = $sAdvantShopOffersRow;
                    $arProcess[] = $arRowToCsv;
                }
            }
        }

        if( !$bXls ){
            $csvFile = new CCSVData();
            $csvFile->SetFieldsType( "R" );
            $delimiter_r_char = ";";
            $csvFile->SetDelimiter( $delimiter_r_char );
        }

        $arResFields = array();

        if( $page == 1 ){
            $arTuple = array();
            $arTupleXls = array();
            $arTupleXls["HEADER"] = array();
            $arTupleXls["ROWS"] = array();
            foreach( $arPaternFields as $paternField ){
                if( $paternField == "ID" ){
                    $paternField = "Id"; //!!excel csv id fix - hello Billy!
                }
                $arTuple[] = $this->ExportConvertCharset( $paternField );
            }

            if( !$bXls ){
                CAcritExportproplusTools::ExportArrayMultiply( $arResFields, $arTuple );
            }
            else{
                $arTupleXls["HEADER"] = $arTuple;
                foreach( $arTupleXls["HEADER"] as $tupleXlsHeaderItemIndex => $arTupleXlsHeaderItem ){
                    $arTupleXls["HEADER"][$tupleXlsHeaderItemIndex] = array(
                        "NAME" => $arTupleXlsHeaderItem,
                        "TYPE" =>  PHPExcel_Cell_DataType::TYPE_STRING2
                    );
                }
            }
        }

        foreach( $arProcess as $arRow ){
            $arTuple = array();
            foreach( $arRow as $colValue ){
                if( is_array( $colValue ) && empty( $colValue ) ){ //!!some fix
                    $colValue = "";
                }
                $arTuple[] = $this->ExportConvertCharset( $colValue );
            }

            if( !$bXls ){
                CAcritExportproplusTools::ExportArrayMultiply( $arResFields, $arTuple );
            }
            else{
                CAcritExportproplusTools::ExportArrayMultiply( $arTupleXls["ROWS"], $arTuple );
            }
        }

        if( !$bXls ){
            foreach( $arResFields as $arTuple ){
                $csvFile->SaveFile( $fileExport, $arTuple );
            }

            $csvFile->CloseFile();
        }
        else{
            if( is_array( $arTupleXls ) && !empty( $arTupleXls ) ){
                $fileExportPath = $_SERVER["DOCUMENT_ROOT"].$this->profile["SETUP"]["URL_DATA_FILE"];
                CAcritExportproplusTools::ArrayToExcel( $fileExportPath, $this->profile["CODE"], $arTupleXls, $this->profile, $page );
            }
        }
    }

    public function ProcessCSV( $page = 1, $cronrun = false, $fileExport = false, $fileExportName = false, $bXls = false ){
        global $APPLICATION;
        if( !$fileExport || !$fileExportName ) return false;

        $dbElements = self::PrepareProcess( $page );
        if( !is_object( $dbElements ) ) return false;

        $navPageCount = ( intval( $dbElements->NavPageCount ) > 0 ) ? $dbElements->NavPageCount : ceil( $dbElements->SelectedRowsCount() / $this->stepElements );

        $sessionData = AcritExportproplusSession::GetSession( $this->profile["ID"] );
        $sessionData["EXPORTPROPLUS"]["LOG"][$this->profile["ID"]]["STEPS"] = $this->isDemo ? 1 : $navPageCount;

        AcritExportproplusSession::SetSession( $this->profile["ID"], $sessionData );

        if( $this->profile["TYPE"] == "advantshop" ){
            self::ProcessBasicCsv( $dbElements, $fileExport, $page, $navPageCount, $bXls );
        }
        else{
            self::ProcessBasicCsv( $dbElements, $fileExport, $page, $navPageCount, $bXls );
        }

        if( !$cronrun ){
            echo '<div id="csv_process" style="width: 100%; text-align: center; font-size: 18px; margin: 40px 0; padding: 40px 0; border: 1px solid #ccc; border-radius: 6px; background: #f5f5f5;">',
            GetMessage( "ACRIT_EXPORTPROPLUS_RUN_EXPORT_RUN" ), "<br/>",
            str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), GetMessage( "ACRIT_EXPORTPROPLUS_RUN_STEP_PROFILE" ) ), "<br/>",
            str_replace( array( "#STEP#", "#COUNT#" ), array( $page, $navPageCount ), GetMessage( "ACRIT_EXPORTPROPLUS_RUN_STEP_RUN" ) ),
            "</div>";
        }

        if( $page >= $navPageCount ){
            return true;
        }

        return false;
    }

    public function ProcessVariantDataXML( $variantItems, $variantContainerId ){
        if( is_array( $variantItems ) && !empty( $variantItems ) ){
            $dbEvents = GetModuleEvents( "acrit.exportproplus", "OnBeforePropertiesSelect" );
            $eventResult = array();
            while( $arEvent = $dbEvents->Fetch() ){
                ExecuteModuleEventEx(
                    $arEvent,
                    array(
                        array(
                            "ID" => $this->profile["ID"],
                            "CODE" => $this->profile["CODE"],
                            "NAME" => $this->profile["NAME"]
                        ),
                        &$eventResult
                    )
                );
            }
            CAcritExportproplusElement::OnBeforePropertiesSelect( $eventResult );

            $productExport = 0;
            foreach( $variantItems as $price => $items ){
                $itemTemplate = $items[0]["XML"];
                $colorsize = array();
                $variantItemTemplate = "";

                foreach( $items as $item ){
                    $arItem = $item["ITEM"];
                    $isOffer = $item["OFFER"];
                    $eventProperty = array();
                    foreach( array( "SIZE", "WEIGHT", "COLOR", "SIZEOFFER", "WEIGHTOFFER", "COLOROFFER" ) as $name ){
                        if( isset( $eventResult[$name] ) ){
                            foreach( $eventResult[$name] as $prop ){
                                if( !empty( $arItem[$prop] ) ){
                                   $eventProperty[$name][] = $prop;
                                }
                            }
                        }
                    }

                    $gender = $this->profile["VARIANT"]["SEX_CONST"] ? $this->profile["VARIANT"]["SEX_CONST"] : $arItem[$this->variantProperties["SEX"]];
                    $arSize = explode( "-", $this->profile["VARIANT"]["CATEGORY"][$variantContainerId] );
                    $arSizeExt = explode( "-", $this->profile["VARIANT"]["CATEGORY_EXT"][$variantContainerId] );

                    $itemSize = $this->variantProperties["SIZE"];
                    if( empty( $arItem[$itemSize] ) && count( $eventProperty["SIZE"] ) ){
                        $ar = $eventProperty["SIZE"];
                        $itemSize = current( $ar );

                    }

                    $itemWeight = $this->variantProperties["WEIGHT"];
                    if( empty( $arItem[$itemWeight] ) && count( $eventProperty["WEIGHT"] ) ){
                        $ar = $eventProperty["WEIGHT"];
                        $itemWeight = current( $ar );
                    }

                    $itemColor = $this->variantProperties["COLOR"];
                    if( empty( $arItem[$itemColor] ) && count( $eventProperty["COLOR"] ) ){
                        $ar = $eventProperty["COLOR"];
                        $itemColor = current( $ar );
                    }

                    if( $isOffer ){
                        // if trade offer, replace property values by trade offer values
                        $gender = $this->profile["VARIANT"]["SEX_CONST"] ? $this->profile["VARIANT"]["SEX_CONST"] : $arItem[$this->variantProperties["SEXOFFER"]];
                        $itemSize = $this->variantProperties["SIZEOFFER"];
                        if( empty( $arItem[$itemSize] ) && count( $eventProperty["SIZEOFFER"] ) ){
                            $ar = $eventProperty["SIZEOFFER"];
                            $itemSize = current( $ar );
                        }

                        $itemWeight = $this->variantProperties["WEIGHTOFFER"];
                        if( empty( $arItem[$itemWeight] ) && count( $eventProperty["WEIGHTOFFER"] ) ){
                            $ar = $eventProperty["WEIGHTOFFER"];
                            $itemWeight = current( $ar );
                        }

                        $itemColor = $this->variantProperties["COLOROFFER"];
                        if( empty( $arItem[$itemColor] ) && count( $eventProperty["COLOROFFER"] ) ){
                            $ar = $eventProperty["COLOROFFER"];
                            $itemColor = current( $ar );
                        }
                    }
                    $variantHash = $arSize[1] == "OZ" ?
                        $arItem[$itemColor].$gender.$arItem[$itemWeight] :
                        $arItem[$itemColor].$arItem[$itemSize].$gender;

                    if( $arSize[1] == "OZ" ){
                        if( !$arItem[$itemWeight] && !$arItem[$itemSize] )
                            continue;
                    }

                    if( in_array( $variantHash, $colorsize ) )
                        continue;

                    $colorsize[] = $variantHash;
                    $variatType = array();

                    if( $arItem[$itemColor] )
                        $variatType[] = "color";

                    if( $arSize[1] == "OZ" ){
                        if( $arItem[$itemSize] || $arItem[$itemWeight] )
                            $variatType[] = "size";
                    }
                    else{
                        if( $arItem[$itemSize])
                            $variatType[] = "size";
                    }

                    if( !empty( $variatType ) ){
                        $variatTypeStr = implode( "_and_", $variatType );
                        $retVariant = "<variant type=\"$variatTypeStr\">".PHP_EOL;
                        if( in_array( "color", $variatType ) )
                            $retVariant .= "<color>{$arItem[$itemColor]}</color>".PHP_EOL;

                        if( in_array( "size", $variatType ) ){
                            if( $arSize[1] == "OZ" ){
                                if( !$arItem[$itemWeight] ){
                                    $arItem[$itemWeight] = $arItem[$itemSize];
                                    $arSize[1] = $arSizeExt[1];
                                }
                                else{
                                    $arItem[$itemWeight] = floatval( $arItem[$itemWeight] );
                                }
                                $retVariant .= "<size category=\"{$arSize[0]}\" gender=\"{$gender}\" system=\"{$arSize[1]}\">"
                                .$arItem[$itemWeight].
                                "</size>".PHP_EOL;
                            }
                            else{
                                $retVariant .= "<size category=\"{$arSize[0]}\" gender=\"{$gender}\" system=\"{$arSize[1]}\">"
                                .$arItem[$itemSize].
                                "</size>".PHP_EOL;
                            }
                        }

                        $retVariant .= "<offerId>{$arItem["ID"]}</offerId>";
                        $retVariant .= "</variant>".PHP_EOL;
                        $variantItemTemplate .= $retVariant;
                        $productExport++;
                    }
                }

                if( strlen( $variantItemTemplate ) > 0 ){
                    $itemTemplate = str_replace( "</offer>", "<variantList>$variantItemTemplate</variantList></offer>", $itemTemplate );
                }

                CAcritExportproplusExport::Save( $itemTemplate );

                // increase the count statistics for export goods and set last export item id
                $this->log->IncProductExport();
            }
            if( ( $productExport == 0 ) && count( $variantCatalogProducts ) ){
                foreach( $variantCatalogProducts as $catalogProduct ){
                    CAcritExportproplusExport::Save( $catalogProduct["XML"] );
                    $this->log->IncProductExport();
                }
            }
            unset( $variantItems );
            unset( $variantCatalogProducts );
        }
    }

    public function ProcessXML( $page = 1, $cronrun = false, $arOzonCategories = false, $bStepExport = false, $iLastSessionExportProductsCnt = 0, $processId = 0 ){
        $profileCategoryType = CAcritExportproplusTools::GetProfileMarketCategoryType( $this->profile["TYPE"] );
        if( $profileCategoryType  == "CExportproplusMarketTiuDB" ){
            $marketCategory = new CExportproplusMarketTiuDB();
            $marketCategory = $marketCategory->GetList();
            $this->marketCategory = $marketCategory;
        }
        elseif( $profileCategoryType == "CExportproplusMarketPromuaDB" ){
            $marketCategory = new CExportproplusMarketPromuaDB();
            $marketCategory = $marketCategory->GetList();
            $this->marketCategory = $marketCategory;
        }
        elseif( $profileCategoryType == "CExportproplusMarketMailruDB" ){
	        $marketCategory = new CExportproplusMarketMailruDB();
	        $marketCategory = $marketCategory->GetList();
	        $this->marketCategory = $marketCategory;
        }

        $dbElements = self::PrepareProcess( $page, $bStepExport );

        if( !is_object( $dbElements ) ) return false;

        $navPageCount = ( intval( $dbElements->NavPageCount ) > 0 ) ? $dbElements->NavPageCount : ceil( $dbElements->SelectedRowsCount() / $this->stepElements );

        $sessionData = AcritExportproplusSession::GetSession( $this->profile["ID"] );
        $sessionData["EXPORTPROPLUS"]["LOG"][$this->profile["ID"]]["STEPS"] = $navPageCount;
        AcritExportproplusSession::SetSession( $this->profile["ID"], $sessionData );

        $bSchemeUseOffer = false;
        $bSchemeUseOfferSku = false;
        $bSchemeUseSku = false;
        $bSchemeUseSkuByOffer = false;

        if( ( $this->profile["EXPORT_DATA_OFFER"] == "Y" ) ){
            $bSchemeUseOffer = true;
        }

        if( $this->profile["EXPORT_DATA_OFFER_WITH_SKU_DATA"] == "Y" ){
            $bSchemeUseOfferSku = true;
        }

        if( $this->profile["EXPORT_DATA_SKU"] == "Y" ){
            $bSchemeUseSku = true;
        }

        if( $this->profile["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ){
            $bSchemeUseSkuByOffer = true;
        }

        if( $dbElements->SelectedRowsCount() ){
            if( ( $this->profile["TYPE"] == "vk_trade" ) && ( $page == 1 ) ){
                //$this->obVkTools->ClearSyncData();
            }
            elseif( ( $this->profile["TYPE"] == "ok_trade" ) && ( $page == 1 ) ){
                $this->obOkTools->ClearSyncData();
            }
            elseif( ( $this->profile["TYPE"] == "instagram_trade" ) && ( $page == 1 ) ){
                //$this->obInstagramTools->ClearSyncData();
            }
        }

        if( $this->bApiExport ){
            $iLastSessionExportProductsCnt = 0;
        }

        while( $arElement = $dbElements->GetNextElement() ){
            $processSessionData = AcritExportproplusSession::GetSession( $this->profile["ID"] );

            if( $bStepExport ){
                if( ( $processSessionData["EXPORTPROPLUS"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= ( $iLastSessionExportProductsCnt + $this->profile["SETUP"]["CRON"][$processId]["STEP_EXPORT_CNT"] ) )
                    || ( ( intval( $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) > 0 ) && ( $processSessionData["EXPORTPROPLUS"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) )
                    || ( $page != 1 )
                ){
                    break;
                }
            }

            if( $bSchemeUseOffer || $bSchemeUseOfferSku || $bSchemeUseSkuByOffer ){
                $variantItems = array();
                $variantCatalogProducts = array();
                $arOfferElementResult = array();
                $this->delay = "";
                $arItem = $this->ProcessElement( $arElement, false, false, $arOzonCategories, $arElementConfig );

                if( !$arItem )
                    continue;

                if( CAcritExportproplusTools::isVariant( $this->profile, $variantContainerId ) ){
                    if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                        $variantContainerId = $arItem["ITEM"]["IBLOCK_ID"];
                    }
                    elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                        $variantContainerId = $arItem["ITEM"]["IBLOCK_PRODUCT_SECTION_ID"];
                    }
                    else{
                        $variantContainerId = $arItem["ITEM"]["IBLOCK_SECTION_ID"];
                    }

                    if( isset( $arItem["SKIP"] ) && !$arItem["SKIP"] ){
                        $variantItems[$arItem["ITEM"][$variantPrice]][] = $arItem;
                    }
                    if( isset( $arItem["ITEM"] ) ){
                        if( isset( $arItem["ITEM"]["GROUP_ITEM_ID"] ) && ( $arItem["ITEM"]["GROUP_ITEM_ID"] == $arItem["ITEM"]["ID"] ) ){
                            $variantCatalogProducts[] = $arItem;
                        }
                        $arItem = $arItem["ITEM"];
                    }
                }
            }

            // if you enable the processing trade offers, we look for and process trade offers
            if( $this->catalogIncluded && ( $this->profile["USE_SKU"] == "Y" ) && ( $bSchemeUseSku || $bSchemeUseSkuByOffer ) ){
                if( !$bSchemeUseOffer && !$bSchemeUseOfferSku && !$bSchemeUseSkuByOffer ){
                    $arItem = $arElement->GetFields();
                }

                if( ( $arItem["ACTIVE"] == "Y" ) && ( $this->catalogSKU[$arItem["IBLOCK_ID"]] ) ){
                    if( isset( $arElementConfig["DELAY"] ) && ( $arElementConfig["DELAY"] == true ) )
                        $arElementConfig["DELAY_SKU"] = true;

                    $arOfferFilter = array(
                        "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                        "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
                    );

                    if( CAcritExportproplusTools::isVariant( $this->profile, $variantContainerId ) ){
                        $arOfferFilter = array_merge(
                            $arOfferFilter,
                            array(
                                "CATALOG_AVAILABLE" => "Y"
                            )
                        );
                    }

                    $dbOfferElements = CIBlockElement::GetList(
                        array(),
                        $arOfferFilter,
                        false,
                        false,
                        array()
                    );

                    $bExportStepFinish = false;
                    while( $arOfferElement = $dbOfferElements->GetNextElement() ){
                        $processSessionData = AcritExportproplusSession::GetSession( $this->profile["ID"] );

                        if( $bStepExport ){
                            if( ( $processSessionData["EXPORTPROPLUS"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= ( $iLastSessionExportProductsCnt + $this->profile["SETUP"]["CRON"][$processId]["STEP_EXPORT_CNT"] ) )
                                || ( ( intval( $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) > 0 ) && ( $processSessionData["EXPORTPROPLUS"]["LOG"][$this->profile["ID"]]["PRODUCTS_EXPORT"] >= $this->profile["SETUP"]["CRON"][$processId]["MAXIMUM_PRODUCTS"] ) )
                                || ( $page != 1 )
                            ){
                                $bExportStepFinish = true;
                                break;
                            }
                        }

                        $arOfferItem = $this->ProcessElement( $arOfferElement, $arItem, false, $arOzonCategories, $arElementConfig, $arOfferElementResult );

                        if( CAcritExportproplusTools::isVariant( $this->profile, $variantContainerId ) ){
                            $variantItems[$arOfferItem["ITEM"][$variantPrice]][] = $arOfferItem;
                        }
                        unset( $arOfferItem );

                        if( $this->isDemo && $this->DemoCount() ){
                            break;
                        }
                    }

                    if( $bExportStepFinish ){
                        break;
                    }
                }
            }

            // activizm.ru profile
            if( CAcritExportproplusTools::isVariant( $this->profile, $variantContainerId ) ){
                self::ProcessVariantDataXML( $variantItems, $variantContainerId );
            }

            if( $this->isDemo && $this->DemoCount() ){
                break;
            }

            unset( $arItem );

            if( isset( $arElementConfig["DELAY"] ) && $arElementConfig["DELAY"] == true ){
                $arElementConfig["DELAY_FLUSH"] = true;
                if( isset( $field["MINIMUM_OFFER_PRICE"] ) && $field["MINIMUM_OFFER_PRICE"] == "Y" ){
                    $arElementConfig["MINIMUM_OFFER_PRICE"] = "Y";
                }
                $this->ProcessElement( $arElement, false, false, $arOzonCategories, $arElementConfig, $arOfferElementResult );

                unset( $arElementConfig["DELAY_SKU"] );
                unset( $arElementConfig["DELAY_FLUSH"] );
                if( isset( $arElementConfig["MINIMUM_OFFER_PRICE"] ) )
                    unset( $arElementConfig["MINIMUM_OFFER_PRICE"] );
            }
        }

        unset( $arElement, $arItem );

        if( !$cronrun ){
            echo '<div style="width: 100%; text-align: center; font-size: 18px; margin: 40px 0; padding: 40px 0; border: 1px solid #ccc; border-radius: 6px; background: #f5f5f5;">',
            GetMessage( "ACRIT_EXPORTPROPLUS_RUN_EXPORT_RUN" ), "<br/>",
            str_replace( array( "#PROFILE_ID#", "#PROFILE_NAME#" ), array( $this->profile["ID"], $this->profile["NAME"] ), GetMessage( "ACRIT_EXPORTPROPLUS_RUN_STEP_PROFILE" ) ), "<br/>",
            str_replace( array( "#STEP#", "#COUNT#" ), array( $page, $navPageCount ), GetMessage( "ACRIT_EXPORTPROPLUS_RUN_STEP_RUN" ) ),
            "</div>";
        }

        CAcritExportproplusTools::SaveCurrencies( $this->profile, $this->currencyList );

        if( $this->isDemo && $this->DemoCount() ){
            return true;
        }

        if( $page >= $navPageCount ){
            return true;
        }

        return false;
    }

    private function PrepareItemToProcess( $arElement, $arProductSKU = false )
    {
        $this->AddResolve();
        $arItem = $this->GetElementProperties( $arElement );

				// Get offers count
				if(!$arProductSKU
						&& ( intval( $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"] ) > 0 )
            && ( intval( $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] ) > 0 )) {
            $arCheckOfferFilter = array(
                "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"]
            );
            $dbCheckOfferElements = CIBlockElement::GetList(
                array(),
                $arCheckOfferFilter,
                false,
                false,
                array()
            );
						$arItem['_OFFERS_COUNT'] = $dbCheckOfferElements->SelectedRowsCount();
				}
        if( !$arProductSKU && ( $this->profile["EXPORT_DATA_OFFER_WITH_SKU_DATA"] != "Y" )){
            if( $arItem['_OFFERS_COUNT'] > 0 ){
                return $arItem;
            }
        }

        if( ( $this->profile["EXPORT_DATA_OFFER_WITH_SKU_DATA"] == "Y" ) && !$arProductSKU && $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"] ){
					
						$arOfferWithSkuSort = array(
								"CATALOG_PRICE_".$this->basePriceId => "ASC",
						);
					
            $arOfferFilter = array(
                "IBLOCK_ID" => $this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_IBLOCK_ID"],
                "PROPERTY_".$this->catalogSKU[$arItem["IBLOCK_ID"]]["OFFERS_PROPERTY_ID"] => $arItem["ID"],
                "!CATALOG_PRICE_".$this->basePriceId => false,
                "ACTIVE" => "Y"
            );
						
						if($this->profile["SETUP"]["OFFER_WITH_SKU_USE_QUANTITY"]=='Y') {
								$arOfferFilter['!CATALOG_QUANTITY'] = false;
						}

            $dbOfferElements = CIBlockElement::GetList(
                $arOfferWithSkuSort,
                $arOfferFilter,
                false,
                false,
                array()
            );

	        $bStoresSum = false;

            if( $arOfferElement = $dbOfferElements->GetNextElement() ){
                $arOfferItem = $this->GetElementProperties( $arOfferElement );

                foreach( $arOfferItem as $itemIndex => $itemValue ){
                    if( ( is_array( $arItem[$itemIndex] ) && empty( $arItem[$itemIndex] ) )
                        || ( !is_array( $arItem[$itemIndex] ) && ( strlen( trim( $arItem[$itemIndex] ) ) <= 0 ) )
                        || ( !$arItem[$itemIndex] )
                        || !isset( $arItem[$itemIndex] )
                    ) {
                        $arItem[$itemIndex] = $itemValue;

	                    if (strpos($itemIndex, 'STORE_AMOUNT') !== false) {
		                    $bStoresSum = true;
	                    }
                    }
                }
            }

	        if ($bStoresSum) {
		        while ($arOfferElement = $dbOfferElements->GetNextElement()) {
			        $arOfferItem = $this->GetElementProperties( $arOfferElement );

			        foreach( $arOfferItem as $itemIndex => $itemValue ) {
				        if (isset($arItem[$itemIndex]) && strpos($itemIndex, 'STORE_AMOUNT') !== false) {
					        $arItem[$itemIndex] += $itemValue;
				        }
			        }
		        }
	        }
        }

        // add product properties and fields to product offers
        if( $this->catalogIncluded && is_array( $arProductSKU ) ){
            $excludeFields = array(
                "NAME",
                "PREVIEW_TEXT",
                "DETAIL_TEXT",
                "PREVIEW_PICTURE",
                "DETAIL_PICTURE",
                "CATALOG_QUANTITY",
                "CATALOG_QUANTITY_RESERVED",
                "CATALOG_WEIGHT",
                "CATALOG_WIDTH",
                "CATALOG_LENGTH",
                "CATALOG_HEIGHT",
                "CATALOG_PURCHASING_PRICE",
                "CATALOG_BARCODE",
            );

	        if ($this->profile["SETUP"]["SKU_USE_CANONICAL"] == 'Y' && trim($arProductSKU['CANONICAL_PAGE_URL']) != '') {
		        $arProductSKU['CANONICAL_PAGE_URL'] = preg_replace('#https?://[^/]+#i', '', $arProductSKU['CANONICAL_PAGE_URL']);
		        $arProductSKU['DETAIL_PAGE_URL'] = $arProductSKU['CANONICAL_PAGE_URL'];
		        $arItem['DETAIL_PAGE_URL'] = $arProductSKU['DETAIL_PAGE_URL'];
	        }

            foreach( $arProductSKU as $key => $value ){
                if( !isset( $arItem[$key] ) || empty( $arItem[$key] ) ){
                    if (!in_array( $key, $excludeFields ) && strpos($key, 'STORE_AMOUNT') === false) {
                        $arItem[$key] = $value;
                    }
                }
            }

            if( array_key_exists( "DETAIL_PICTURE", $arProductSKU ) ){
                $arProductSKU["DETAIL_PICTURE"] = CFile::GetPath( $arProductSKU["~DETAIL_PICTURE"] );
            }
            if( array_key_exists( "PREVIEW_PICTURE", $arProductSKU ) ){
                $arProductSKU["PREVIEW_PICTURE"] = CFile::GetPath( $arProductSKU["~PREVIEW_PICTURE"] );
            }

            $arItem["ELEMENT_ID"] = $arProductSKU["ID"];
            $arItem["IBLOCK_SECTION_ID"] = $arProductSKU["IBLOCK_SECTION_ID"];
            foreach( $this->profile["NAMESCHEMA"] as $key => $value ){
                switch( $value ){
                    case $key."_OFFER":
                        if( $key == "CATALOG_PRICE" ){
                            foreach( $this->usePrices as $priceType ){
                                $arItem[$key."_".$priceType] = $arProductSKU[$key."_".$priceType];
                                $arItem[$key."_".$priceType] = ( $arItem[$key."_".$priceType] );
                            }
                        }
                        else{
                            $arItem[$key] = $arProductSKU[$key];
                            $arItem[$key] = ( $arItem[$key] );
                        }
                        break;
                    case $key."_OFFER_SKU":
                        if( $key == "CATALOG_PRICE" ){
                            foreach( $this->usePrices as $priceType ){
                                $arItem[$key."_".$priceType] = $arProductSKU[$key."_".$priceType];
                                $arItem[$key."_".$priceType] = ( $arItem[$key."_".$priceType] );
                            }
                        }
                        $arItem[$key] = implode( " ", array( $arProductSKU[$key], $arItem[$key] ) );
                        $arItem[$key] = ( $arItem[$key] );
                        break;
                    case $key."_OFFER_IF_SKU_EMPTY":
                        if( $key == "CATALOG_PRICE" ){
                            foreach( $this->usePrices as $priceType ){
                                if( !isset( $arItem[$key."_".$priceType] ) || empty( $arItem[$key."_".$priceType] ) ){
                                    if( isset( $arProductSKU[$key."_".$priceType] ) && !empty( $arProductSKU[$key."_".$priceType] ) ){
                                        $value = $arProductSKU[$key."_".$priceType];
                                        if( is_array( $value ) ){
                                            foreach( $value as $_key => $_value )
                                                $arItem[$key."_".$priceType][$_key] = ( $_value );
                                        }
                                        else{
                                            $arItem[$key."_".$priceType] = $value;
                                            $arItem[$key."_".$priceType] = ( $arItem[$key."_".$priceType] );
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            if( !isset( $arItem[$key] ) || empty( $arItem[$key] ) ){
                                if( isset( $arProductSKU[$key] ) && !empty( $arProductSKU[$key] ) ){
                                    $value = $arProductSKU[$key];
                                    if( is_array( $value ) ){
                                        foreach( $value as $_key => $_value )
                                            $arItem[$key][$_key] = ( $_value );
                                    }
                                    else{
                                        $arItem[$key] = $value;
                                        $arItem[$key] = ( $arItem[$key] );
                                    }
                                }
                            }
                        }

                        break;
                }
            }
        }
        else{
            $arItem["GROUP_ITEM_ID"] = $arItem["ID"];
        }

        return $arItem;
    }

    // get product properties, template creation, set fields values, write it in file
    private function ProcessElement( $arElement, $arProductSKU = false, $bCsvMode = false, $arOzonCategories = false, $arItemConfig = array(), &$arOfferElementResult = array() ){
        static $arSectionCache;
        global $DB, $USER;
        $skipElement = false;
        $this->xmlCode = false;
        $_arOfferElementResult = array();

        $arItem = self::PrepareItemToProcess( $arElement, $arProductSKU );
				
        // check element on basic profile conditions
        if( $this->catalogIncluded ){
            $profileData = $this->profile;
						
						if($profileData['SKIP_WITH_SKU']=='Y' && !$arProductSKU && $arItem['_OFFERS_COUNT']>0) {
							$skipElement = true;
						}

            if( $profileData["SETUP"]["VALIDATE_CONDITIONS"] == "Y" ){
                $sBeforeConditions = serialize( $profileData["CONDITION"] );

                foreach( $profileData["CONDITION"]["CHILDREN"] as $condtionIndex => $arCondtion ){
                    if( isset( $arCondtion["CHILDREN"] ) && is_array( $arCondtion["CHILDREN"] ) && !empty( $arCondtion["CHILDREN"] ) ){
                        foreach( $arCondtion["CHILDREN"] as $condtionChildIndex => $arChildCondtion ){
                            if( isset( $arChildCondtion["CLASS_ID"] ) && !empty( $arChildCondtion["CLASS_ID"] ) ){
                                if( stripos( $arChildCondtion["CLASS_ID"], "CondIBProp" ) !== false ){
                                    $aConditionPartData = explode( ":", $arChildCondtion["CLASS_ID"] );

                                    if( $aConditionPartData[1] != $arItem["IBLOCK_ID"] ){
                                        unset( $profileData["CONDITION"]["CHILDREN"][$condtionIndex]["CHILDREN"][$condtionChildIndex] );
                                        if( empty( $profileData["CONDITION"]["CHILDREN"][$condtionIndex]["CHILDREN"] ) ){
                                            unset( $profileData["CONDITION"]["CHILDREN"][$condtionIndex] );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else{
                        if( isset( $arCondtion["CLASS_ID"] ) && !empty( $arCondtion["CLASS_ID"] ) ){
                            if( stripos( $arCondtion["CLASS_ID"], "CondIBProp" ) !== false ){
                                $aConditionPartData = explode( ":", $arCondtion["CLASS_ID"] );

                                if( $aConditionPartData[1] != $arItem["IBLOCK_ID"] ){
                                    unset( $profileData["CONDITION"]["CHILDREN"][$condtionIndex] );
                                    if( empty( $profileData["CONDITION"]["CHILDREN"] ) ){
                                        unset( $profileData["CONDITION"]["CHILDREN"] );
                                    }
                                }
                            }
                        }
                    }
                }

                $sAfterConditions = serialize( $profileData["CONDITION"] );

                if( $sBeforeConditions !== $sAfterConditions ){
                    $obCond = new CAcritExportproplusCatalogCond();
                    CAcritExportproplusProps::$arIBlockFilter = CExportproplusProfile::PrepareIBlock( $profileData["IBLOCK_ID"], $profileData["USE_SKU"] );
                    $obCond->Init( BT_COND_MODE_GENERATE, BT_COND_BUILD_CATALOG, array() );
                    $profileData["EVAL_FILTER"] = $obCond->Generate( $profileData["CONDITION"], array( "FIELD" => '$GLOBALS["CHECK_COND"]' ) );
                }
            }

            if( !CAcritExportproplusTools::CheckCondition( $arItem, $profileData["EVAL_FILTER"] ) ){
                if( !$arProductSKU && ( $profileData["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ) ){
                    $returnResult = false;
                    return $returnResult;
                }
                else{
                    $returnResult = ( $bCsvMode ) ? false : $arItem;
                    return $returnResult;
                }
            }
            elseif( !$arProductSKU && ( $profileData["EXPORT_DATA_SKU_BY_OFFER"] == "Y" ) ){
                $returnResult = ( $bCsvMode ) ? false : $arItem;
                return $returnResult;
            }
        }
				
        // inc statistic product counter
        $this->log->IncProduct();

        if( $bCsvMode ){
            $templateValues = array();
        }
        else{
            $itemTemplate = $this->profile["OFFER_TEMPLATE"];
            $templateValues = $this->templateValuesDefaults;
        }


        if( empty( $arSectionCache[$arItem["IBLOCK_ID"]] ) ){
            $rs = CIBlockSection::GetList(
                array(
                    "LEFT_MARGIN" => "ASC"
                ),
                array(
                    "IBLOCK_ID" => $arItem["IBLOCK_ID"]
                )
            );
            while( $ar = $rs->GetNext( false, false ) ){
                if( intval( $ar["PICTURE"] ) ){
                    $ar["PICTURE"] = CAcritExportproplusTools::GetFilePath( $ar["PICTURE"] );
                }
                if( intval( $ar["DETAIL_PICTURE"] ) ){
                    $ar["DETAIL_PICTURE"] = CAcritExportproplusTools::GetFilePath( $ar["DETAIL_PICTURE"] );
                }

                $arSectionCache[$arItem["IBLOCK_ID"]][$ar["ID"]] = $ar;
            }
        }

        if( !$bCsvMode ){
            $arItemSections = array();
            if( $this->profile["EXPORT_PARENT_CATEGORIES_TO_OFFER"] == "Y" ){
                $arItemSections = $arItem["SECTION_PARENT_ID"];

                if( $this->profile["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ){
                    foreach( $arItem["SECTION_ID"] as $itemSectionId ){
                        if( !in_array( $itemSectionId, $arItemSections ) ){
                            $arItemSections[] = $itemSectionId;
                        }
                    }
                }
            }
            elseif( $this->profile["EXPORT_OFFER_CATEGORIES_TO_OFFER"] == "Y" ){
                $arItemSections = $arItem["SECTION_ID"];
            }

            $sectionExportRow = "";
            if( !empty( $arItemSections ) ){
                foreach( $arItemSections as $arItemSectionsId ){
                    $sectionExportRow .= "<categoryId>".$arItemSectionsId."</categoryId>".PHP_EOL;
                }

                $itemTemplate = str_replace( "<categoryId>#CATEGORYID#</categoryId>", $sectionExportRow, $itemTemplate );
            }

            $templateValues["#GROUP_ITEM_ID#"] = $arItem["GROUP_ITEM_ID"];
        }

        $arItemMain = $arItem;
        $fieldPrePostfix = ( $bCsvMode ) ? "" : "#";

        foreach( $this->profile["XMLDATA"] as $xmlCode => $field ){
            $this->xmlCode = $xmlCode;
            $arItem = $arItemMain;
            $fieldIndex = $fieldPrePostfix.$field["CODE"].$fieldPrePostfix;

            $useCondition = ( $field["USE_CONDITION"] == "Y" );
            if( $useCondition ){
                $conditionTrue = ( CAcritExportproplusTools::CheckCondition( $arItem, $field["EVAL_FILTER"] ) == true );
            }

            if( $useCondition && !$conditionTrue ){
                if( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_FALSE_TYPE"] == "const" ) ) ){

                    $field["CONTVALUE_FALSE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_FALSE"] : $field["COMPLEX_FALSE_CONTVALUE"];
                    $templateValues[$fieldIndex] = $field["CONTVALUE_FALSE"];
                }
                else{
                    if( $field["TYPE"] == "composite" ){
                        $compositeValue = "";
                        $compositeFalseDivider = ( strlen( $field["COMPOSITE_FALSE_DIVIDER"] ) > 0 ) ? $field["COMPOSITE_FALSE_DIVIDER"] : " ";
                        foreach( $field["COMPOSITE_FALSE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeFalseDivider;
                            }
                            if( $compositeField["COMPOSITE_FALSE_TYPE"] == "const" ){
                                $compositeValue .= CAcritExportproplusTools::RoundNumber( $compositeField["COMPOSITE_FALSE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_FALSE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_FALSE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_FALSE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_FALSE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $arItem[$compositeField["COMPOSITE_FALSE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;

                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $compositeValueTmp,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $compositeValueTmp - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $compositeValueTmp;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $compositeValueTmp = $discountPrice;
                                                $values = $compositeValueTmp;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $compositeValueTmp = CAcritExportproplusTools::BitrixRoundNumber( $compositeValueTmp, $arValue[1] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                    $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                    $compositeValueTmp = array();
                                                    foreach( $arProcessValues as $val ){
                                                        if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                            if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                                $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }

                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeFalseDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }

                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }

                                                    if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                        $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                        $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                    }
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $compositeValueTmp = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                $compositeValueTmpStr = "";
                                                if( !empty( $compositeValueTmp ) ){
                                                    foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                        if( $compositeValueTmpIndex ){
                                                            $compositeValueTmpStr .= $compositeFalseDivider;
                                                        }
                                                        $compositeValueTmpStr .= $compositeValueTmpItem;
                                                    }
                                                }

                                                if( strlen( $compositeValueTmpStr ) > 0 ){
                                                    $compositeValueTmp = $compositeValueTmpStr;
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                }
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues[$fieldIndex] =  CAcritExportproplusTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                    }
                    elseif( $field["TYPE"] == "arithmetics" ){
                        $arithmeticsFalseFormula = trim( $field["ARITHMETICS_FALSE_DIVIDER"] );
                        $field["ARITHMETICS_FALSE"] = array_reverse( $field["ARITHMETICS_FALSE"], true );

                        $bNeedFalseFormulaCalc = true;
                        foreach( $field["ARITHMETICS_FALSE"] as $arithmeticsFieldIndex => $arithmeticsField ){
                            if( $arithmeticsField["ARITHMETICS_FALSE_TYPE"] == "const" ){
                                $arithmeticsFalseFormula = str_replace( "x".$arithmeticsFieldIndex, CAcritExportproplusTools::RoundNumber( $arithmeticsField["ARITHMETICS_FALSE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] ), $arithmeticsFalseFormula );
                            }
                            elseif( $arithmeticsField["ARITHMETICS_FALSE_TYPE"] == "field" ){
                                $arithmeticsValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $arithmeticsValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $arithmeticsField["ARITHMETICS_FALSE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $arithmeticsField["ARITHMETICS_FALSE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $arithmeticsField["ARITHMETICS_FALSE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arItem[$arithmeticsField["ARITHMETICS_FALSE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;

                                            $arithmeticsValueTmp = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $arithmeticsValueTmp = $convertFrom;
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $arithmeticsValueTmp;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $arithmeticsValueTmp = $convertTo;
                                                        $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $arithmeticsValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $arithmeticsValueTmp;
                                                            }
                                                        }
                                                        else{
                                                            $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $arithmeticsValueTmp;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $arithmeticsValueTmp += $arithmeticsValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $arithmeticsValueTmp;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $arithmeticsValueTmp,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $arithmeticsValueTmp - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $arithmeticsValueTmp;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $arithmeticsValueTmp = $discountPrice;
                                                $values = $arithmeticsValueTmp;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $arithmeticsValueTmp = CAcritExportproplusTools::BitrixRoundNumber( $arithmeticsValueTmp, $arValue[1] );
                                            }
                                            else{
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $arithmeticsValueTmp;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues[0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                                else{
                                                    $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues[0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            break;
                                    }
                                }
                                $arithmeticsFalseFormula = str_replace( "x".$arithmeticsFieldIndex, CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] ), $arithmeticsFalseFormula );
                                if( !strlen( trim( $arithmeticsValueTmp ) ) ){
                                    $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", "x".$arithmeticsFieldIndex, GetMessage( "ACRIT_EXPORTPROPLUS_ARITHMETICS_FIELD_NO_OPERAND" ) ) );
                                    $this->log->IncProductError();
                                    $bNeedFalseFormulaCalc = false;
                                }
                            }
                        }
                        if( $bNeedFalseFormulaCalc ){
                            $templateValues[$fieldIndex] =  CAcritExportproplusTools::RoundNumber( CAcritExportproplusTools::CalculateString( $arithmeticsFalseFormula ), $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                        }
                        else{
                            $templateValues[$fieldIndex] =  "";
                        }
                    }
                    elseif( $field["TYPE"] == "stack" ){
                        $stackValue = "";
                        foreach( $field["STACK_FALSE"] as $stackFieldIndex => $stackField ){
                            if( $stackField["STACK_FALSE_TYPE"] == "const" ){
                                $stackValue = CAcritExportproplusTools::RoundNumber( $stackField["STACK_FALSE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $stackField["STACK_FALSE_TYPE"] == "field" ){
                                $stackValue = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $stackValue = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $stackField["STACK_FALSE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $stackField["STACK_FALSE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $stackField["STACK_FALSE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $stackValue = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $arItem[$stackField["STACK_FALSE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;

                                            $stackValue = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $stackValue = $convertFrom;
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $stackValue;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $stackValue = $convertTo;
                                                        $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $stackValue;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $stackValue = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $stackValue;
                                                            }
                                                        }
                                                        else{
                                                            $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $stackValue;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $stackValue += $stackValue * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $stackValue;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $stackValue,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $stackValue - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $stackValue;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $stackValue = $discountPrice;
                                                $values = $stackValue;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $stackValue = CAcritExportproplusTools::BitrixRoundNumber( $stackValue, $arValue[1] );
                                            }
                                            else{
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $stackValue;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                    $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                    $stackValue = array();
                                                    foreach( $arProcessValues as $val ){
                                                        if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                            if( count( $stackValue ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                                $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            }
                                                        }
                                                        else{
                                                            $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }

                                                    if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                        $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                        $stackValue = implode( $fieldMultipropDivider, $stackValue );
                                                    }
                                                }
                                                else{
                                                    $stackValue = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $stackValue = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $stackValue ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $stackValue = implode( $fieldMultipropDivider, $stackValue );
                                                }
                                            }
                                            else{
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            break;
                                    }
                                }
                            }

                            if(
                                ( is_array( $stackValue ) && !empty( $stackValue ) )
                                || ( strlen( trim( $stackValue ) ) > 0 )
                            ){
                                $templateValues[$fieldIndex] =  CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                break;
                            }
                        }
                    }
                    else{
                        $field["VALUE"] = $field["COMPLEX_FALSE_VALUE"];
                        if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                            $templateValues[$fieldIndex] = detailLink( $arItem["ID"] );
                            if( !$bCsvMode ){
                                $linkParamSymbolIndex = stripos( $itemTemplate, "?" );
                                $linkUtmSymbolIndex = stripos( $itemTemplate, "?utm_source" );
                                if( $linkParamSymbolIndex != $linkUtmSymbolIndex ){
                                    $itemTemplate = str_replace( "?utm_source", "&amp;utm_source", $itemTemplate );
                                }
                            }
                        }
                        else{
                            if( function_exists( "acritRedefine" ) ){
                                $templateValues[$fieldIndex] = acritRedefine( $fieldIndex, $arItem["ID"], $this->profile["ID"] );
                            }

                            if( !$templateValues[$fieldIndex] ){
                                $arValue = explode( "-", $field["VALUE"] );

                                switch( count( $arValue ) ){
                                    case 1:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( strpos( $field["VALUE"], "." ) !== false ){
                                            $arField = explode( ".", $field["VALUE"] );
                                            switch( $arField[0] ){
                                                case "SECTION":
                                                    $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                    $value = $curSection[$arField[1]] ? : "";
                                                    break;
                                                default:
                                                    $value = "";
                                            }
                                            unset( $arField );
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 2:
                                        $values = null;
                                        $templateValues[$fieldIndex] = $arItem["CATALOG_".$arValue[1]];

                                        if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                            preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                        }
                                        else{
                                            preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                        }

                                        $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                        if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                            $templateValues[$fieldIndex] = $convertFrom;
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $templateValues[$fieldIndex];
                                            }

                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    $templateValues[$fieldIndex] = $convertTo;
                                                    $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $templateValues[$fieldIndex];
                                                    }
                                                }
                                            }
                                        }
                                        elseif( !empty( $arPriceCode[0] ) ){
                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                        $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                $arItem["CATALOG_".$arValue[1]],
                                                                $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                $convertTo
                                                            ),
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values=$templateValues[$fieldIndex];
                                                        }

                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $templateValues[$fieldIndex];
                                                        }
                                                    }
                                                }
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            else{
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $templateValues[$fieldIndex] += $templateValues[$fieldIndex] *
                                                floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if(is_array( $arProductSKU )){
                                                    $values = $templateValues[$fieldIndex];
                                                }
                                            }
                                        }

                                        if( stripos( $arValue[1], "_WD" ) !== false ){
                                            if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                    $templateValues[$fieldIndex],
                                                    $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                    $arDiscounts
                                                );

                                                $discount = $templateValues[$fieldIndex] - $discountPrice;
                                            }
                                            else{
                                                $discountPrice = $templateValues[$fieldIndex];
                                                $discount = 0;
                                            }

                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                            $templateValues[$fieldIndex] = $discountPrice;
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::BitrixRoundNumber( $templateValues[$fieldIndex], $arValue[1] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }

                                        if( is_array( $arProductSKU ) ){
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( is_array( $arProductSKU )&& !is_null( $values ) )
                                            $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                        if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) && ( $arItemConfig["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                            if( isset( $arOfferElementResult[$xmlCode][$field["CODE"]] ) && count( $arOfferElementResult[$xmlCode][$field["CODE"]] ) ){
                                                if( isset( $field["MINIMUM_OFFER_PRICE_CODE"] ) && strlen( $field["MINIMUM_OFFER_PRICE_CODE"] ) ){
                                                    $templateValues[$fieldPrePostfix.$field["MINIMUM_OFFER_PRICE_CODE"].$fieldPrePostfix] = min( $arOfferElementResult[$xmlCode][$field["CODE"]] );
                                                }
                                            }
                                        }
                                        elseif( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                        }
                                        break;
                                    case 3:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( $arValue[0] == $arItem["IBLOCK_ID"] || $arValue[0] == $arProductSKU["IBLOCK_ID"] ){
                                            if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                            }
                                            else{
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                            }

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $templateValues[$fieldIndex] = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                                }
                                            }
                                            else{
                                                $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 5:
                                        $arItem = $arItemMain;

                                        $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                        $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                        if( is_array( $arProcessValues ) ){
                                            $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                            $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                            $templateValues[$fieldIndex] = array();
                                            foreach( $arProcessValues as $val ){
                                                if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                    if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                        $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }
                                                else{
                                                    $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }

                                            if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                            }
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }

                                        break;
                                }
                            }
                        }
                    }
                }
            }
            else{
                // field or property
                if( ( $field["TYPE"] == "field" )
                    || ( $field["TYPE"] == "composite" )
                    || ( $field["TYPE"] == "arithmetics" )
                    || ( $field["TYPE"] == "stack" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "field" ) ) ){

                    if( $field["TYPE"] == "composite" ){
                        $compositeValue = "";
                        $compositeTrueDivider = ( strlen( $field["COMPOSITE_TRUE_DIVIDER"] ) > 0 ) ? $field["COMPOSITE_TRUE_DIVIDER"] : " ";
                        foreach( $field["COMPOSITE_TRUE"] as $compositeFieldIndex => $compositeField ){
                            if( $compositeFieldIndex > 1 ){
                                $compositeValue .= $compositeTrueDivider;
                            }
                            if( $compositeField["COMPOSITE_TRUE_TYPE"] == "const" ){
                                $compositeValue .= CAcritExportproplusTools::RoundNumber( $compositeField["COMPOSITE_TRUE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $compositeField["COMPOSITE_TRUE_TYPE"] == "field" ){
                                $compositeValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $compositeValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $compositeField["COMPOSITE_TRUE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $compositeField["COMPOSITE_TRUE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $compositeField["COMPOSITE_TRUE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $arItem[$compositeField["COMPOSITE_TRUE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $compositeValueTmp = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $compositeValueTmp = $convertFrom;
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $compositeValueTmp;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $compositeValueTmp = $convertTo;
                                                        $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $compositeValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $compositeValueTmp = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $compositeValueTmp;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $compositeValueTmp += $compositeValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $compositeValueTmp;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $compositeValueTmp,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $compositeValueTmp - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $compositeValueTmp;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $compositeValueTmp = $discountPrice;
                                                $values = $compositeValueTmp;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $compositeValueTmp = CAcritExportproplusTools::BitrixRoundNumber( $compositeValueTmp, $arValue[1] );
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $compositeValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $compositeValueTmp;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                    $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                    $compositeValueTmp = array();
                                                    foreach( $arProcessValues as $val ){
                                                        if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                            if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                                $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            }
                                                        }
                                                        else{
                                                            $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }

                                                    $compositeValueTmpStr = "";
                                                    if( !empty( $compositeValueTmp ) ){
                                                        foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                            if( $compositeValueTmpIndex ){
                                                                $compositeValueTmpStr .= $compositeTrueDivider;
                                                            }
                                                            $compositeValueTmpStr .= $compositeValueTmpItem;
                                                        }
                                                    }

                                                    if( strlen( $compositeValueTmpStr ) > 0 ){
                                                        $compositeValueTmp = $compositeValueTmpStr;
                                                    }

                                                    if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                        $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                        $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                    }
                                                }
                                                else{
                                                    $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $compositeValueTmp = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $compositeValueTmp ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $compositeValueTmp[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                $compositeValueTmpStr = "";
                                                if( !empty( $compositeValueTmp ) ){
                                                    foreach( $compositeValueTmp as $compositeValueTmpIndex => $compositeValueTmpItem ){
                                                        if( $compositeValueTmpIndex ){
                                                            $compositeValueTmpStr .= $compositeTrueDivider;
                                                        }
                                                        $compositeValueTmpStr .= $compositeValueTmpItem;
                                                    }
                                                }

                                                if( strlen( $compositeValueTmpStr ) > 0 ){
                                                    $compositeValueTmp = $compositeValueTmpStr;
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $compositeValueTmp = implode( $fieldMultipropDivider, $compositeValueTmp );
                                                }
                                            }
                                            else{
                                                $compositeValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            $arItem = $arItemMain;
                                            break;
                                    }
                                }
                                $compositeValue .= $compositeValueTmp;
                            }
                        }
                        $templateValues[$fieldIndex] =  CAcritExportproplusTools::RoundNumber( $compositeValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                    }
                    elseif( $field["TYPE"] == "arithmetics" ){
                        $arithmeticsTrueFormula = trim( $field["ARITHMETICS_TRUE_DIVIDER"] );
                        $field["ARITHMETICS_TRUE"] = array_reverse( $field["ARITHMETICS_TRUE"], true );

                        $bNeedTrueFormulaCalc = true;
                        foreach( $field["ARITHMETICS_TRUE"] as $arithmeticsFieldIndex => $arithmeticsField ){
                            if( $arithmeticsField["ARITHMETICS_TRUE_TYPE"] == "const" ){
                                $arithmeticsTrueFormula = str_replace( "x".$arithmeticsFieldIndex, CAcritExportproplusTools::RoundNumber( $arithmeticsField["ARITHMETICS_TRUE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] ), $arithmeticsTrueFormula );
                            }
                            elseif( $arithmeticsField["ARITHMETICS_TRUE_TYPE"] == "field" ){
                                $arithmeticsValueTmp = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $arithmeticsValueTmp = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $arithmeticsField["ARITHMETICS_TRUE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $arithmeticsField["ARITHMETICS_TRUE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $arithmeticsField["ARITHMETICS_TRUE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arItem[$arithmeticsField["ARITHMETICS_TRUE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;
                                            $arithmeticsValueTmp = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $arithmeticsValueTmp = $convertFrom;
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $arithmeticsValueTmp;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $arithmeticsValueTmp = $convertTo;
                                                        $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $arithmeticsValueTmp;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $arithmeticsValueTmp;
                                                            }
                                                        }
                                                        else{
                                                            $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $arithmeticsValueTmp;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $arithmeticsValueTmp += $arithmeticsValueTmp * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $arithmeticsValueTmp;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $arithmeticsValueTmp,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $arithmeticsValueTmp - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $arithmeticsValueTmp;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $arithmeticsValueTmp = $discountPrice;
                                                $values = $arithmeticsValueTmp;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $arithmeticsValueTmp = CAcritExportproplusTools::BitrixRoundNumber( $arithmeticsValueTmp, $arValue[1] );
                                            }
                                            else{
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $arithmeticsValueTmp;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues[0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                                else{
                                                    $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues[0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $arithmeticsValueTmp = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            $arItem = $arItemMain;
                                            break;
                                    }
                                }
                                $arithmeticsTrueFormula = str_replace( "x".$arithmeticsFieldIndex, CAcritExportproplusTools::RoundNumber( $arithmeticsValueTmp, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] ), $arithmeticsTrueFormula );
                                if( !strlen( trim( $arithmeticsValueTmp ) ) ){
                                    $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", "x".$arithmeticsFieldIndex, GetMessage( "ACRIT_EXPORTPROPLUS_ARITHMETICS_FIELD_NO_OPERAND" ) ) );
                                    $this->log->IncProductError();
                                    $bNeedTrueFormulaCalc = false;
                                }
                            }
                        }

                        if( $bNeedTrueFormulaCalc ){
                            $templateValues[$fieldIndex] =  CAcritExportproplusTools::RoundNumber( CAcritExportproplusTools::CalculateString( $arithmeticsTrueFormula ), $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                        }
                        else{
                            $templateValues[$fieldIndex] =  "";
                        }
                    }
                    elseif( $field["TYPE"] == "stack" ){
                        $stackValue = "";
                        foreach( $field["STACK_TRUE"] as $stackFieldIndex => $stackField ){
                            if( $stackField["STACK_TRUE_TYPE"] == "const" ){
                                $stackValue = CAcritExportproplusTools::RoundNumber( $stackField["STACK_TRUE_CONTVALUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                            }
                            elseif( $stackField["STACK_TRUE_TYPE"] == "field" ){
                                $stackValue = "";
                                if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                                    $stackValue = detailLink( $arItem["ID"] );
                                }
                                else{
                                    $arValue = explode( "-", $stackField["STACK_TRUE_VALUE"] );

                                    switch( count( $arValue ) ){
                                        case 1:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( strpos( $stackField["STACK_TRUE_VALUE"], "." ) !== false ){
                                                $arField = explode( ".", $stackField["STACK_TRUE_VALUE"] );
                                                switch( $arField[0] ){
                                                    case "SECTION":
                                                        $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                        $value = $curSection[$arField[1]] ? : "";
                                                        break;
                                                    default:
                                                        $value = "";
                                                }
                                                unset( $arField );

                                                $stackValue = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            else{
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $arItem[$stackField["STACK_TRUE_VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 2:
                                            $values = null;

                                            $stackValue = $arItem["CATALOG_".$arValue[1]];

                                            if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                                preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                            }
                                            else{
                                                preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                            }

                                            $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                            if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                                $stackValue = $convertFrom;
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $stackValue;
                                                }

                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        $stackValue = $convertTo;
                                                        $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $stackValue;
                                                        }
                                                    }
                                                }
                                            }
                                            elseif( !empty( $arPriceCode[0] ) ){
                                                if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                    if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                        $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                        if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                            $stackValue = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                    $arItem["CATALOG_".$arValue[1]],
                                                                    $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                    $convertTo
                                                                ),
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $stackValue;
                                                            }
                                                        }
                                                        else{
                                                            $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                                $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                                $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                            );
                                                            if( is_array( $arProductSKU ) ){
                                                                $values = $stackValue;
                                                            }
                                                        }
                                                    }
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                else{
                                                    if( !in_array( $convertFrom, $this->currencyList ) )
                                                        $this->currencyList[] = $convertFrom;
                                                }
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $stackValue += $stackValue * floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                    $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $stackValue;
                                                    }
                                                }
                                            }

                                            if( stripos( $arValue[1], "_WD" ) !== false ){
                                                if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                    in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                        $stackValue,
                                                        $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                        $arDiscounts
                                                    );

                                                    $discount = $stackValue - $discountPrice;
                                                }
                                                else{
                                                    $discountPrice = $stackValue;
                                                    $discount = 0;
                                                }

                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                                $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                                $stackValue = $discountPrice;
                                                $values = $stackValue;
                                            }

                                            if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                                $stackValue = CAcritExportproplusTools::BitrixRoundNumber( $stackValue, $arValue[1] );
                                            }
                                            else{
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                            if( is_array( $arProductSKU ) ){
                                                $values = $stackValue;
                                            }

                                            if( is_array( $arProductSKU )&& !is_null( $values ) )
                                                $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                            break;
                                        case 3:
                                            $arItem = $arItemMain;
                                            if( isset( $this->useResolve[$xmlCode] ) ){
                                                $arItem = $this->GetElementProperties( $arElement );
                                            }
                                            if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                                if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                    $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }

                                                if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                                }
                                                else{
                                                    $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                                }

                                                if( is_array( $arProcessValues ) ){
                                                    $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                    $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                    $stackValue = array();
                                                    foreach( $arProcessValues as $val ){
                                                        if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                            if( count( $stackValue ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                                $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                            }
                                                        }
                                                        else{
                                                            $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }

                                                    if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                        $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                        $stackValue = implode( $fieldMultipropDivider, $stackValue );
                                                    }
                                                }
                                                else{
                                                    $stackValue = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }
                                            $arItem = $arItemMain;
                                            break;
                                        case 5:
                                            $arItem = $arItemMain;

                                            $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                            $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $stackValue = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $stackValue ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $stackValue[] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $stackValue = implode( $fieldMultipropDivider, $stackValue );
                                                }
                                            }
                                            else{
                                                $stackValue = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            break;
                                    }
                                }
                            }

                            if(
                                ( is_array( $stackValue ) && !empty( $stackValue ) )
                                || ( strlen( trim( $stackValue ) ) > 0 )
                            ){
                                $templateValues[$fieldIndex] =  CAcritExportproplusTools::RoundNumber( $stackValue, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                break;
                            }
                        }
                    }
                    else{
                        $field["VALUE"] = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];

                        if( ( $field["CODE"] == "URL" ) && function_exists( "detailLink" ) ){
                            $templateValues[$fieldIndex] = detailLink( $arItem["ID"] );
                            if( !$bCsvMode ){
                                $linkParamSymbolIndex = stripos( $itemTemplate, "?" );
                                $linkUtmSymbolIndex = stripos( $itemTemplate, "?utm_source" );
                                if( $linkParamSymbolIndex != $linkUtmSymbolIndex ){
                                    $itemTemplate = str_replace( "?utm_source", "&amp;utm_source", $itemTemplate );
                                }
                            }
                        }
                        else{
                            if( function_exists( "acritRedefine" ) ){
                                $templateValues[$fieldIndex] = acritRedefine( $fieldIndex, $arItem["ID"], $this->profile["ID"] );
                            }

                            if( !$templateValues[$fieldIndex] ){
                                $arValue = explode( "-", $field["VALUE"] );

                                switch( count( $arValue ) ){
                                    case 1:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( strpos( $field["VALUE"], "." ) !== false ){
                                            $arField = explode( ".", $field["VALUE"] );
                                            switch( $arField[0] ){
                                                case "SECTION":
                                                    $curSection = $arSectionCache[$arItemMain["IBLOCK_ID"]][$arItemMain["IBLOCK_SECTION_ID"]];
                                                    $value = $curSection[$arField[1]] ?: "";
                                                    break;
                                                default:
                                                    $value = "";
                                            }
                                            unset( $arField );
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $value, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $arItem[$field["VALUE"]], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 2:
                                        $values = null;
                                        $templateValues[$fieldIndex] = $arItem["CATALOG_".$arValue[1]];

                                        if( ( $field["VALUE"] == "CATALOG-PURCHASING_PRICE" ) && isset( $arItem["CATALOG_PURCHASING_PRICE"] ) ){
                                            preg_match( "/PURCHASING_PRICE/", $arValue[1], $arPriceCode );
                                        }
                                        else{
                                            preg_match( "/PRICE_[\d]+/", $arValue[1], $arPriceCode );
                                        }

                                        $convertFrom = $arItem["CATALOG_{$arPriceCode[0]}_CURRENCY"];

                                        if( strpos( $arValue[1], "_CURRENCY" ) > 0 ){
                                            $templateValues[$fieldIndex] = $convertFrom;
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            if( is_array( $arProductSKU ) ){
                                                $values = $templateValues[$fieldIndex];
                                            }

                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    $templateValues[$fieldIndex] = $convertTo;
                                                    $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    if( is_array( $arProductSKU ) ){
                                                        $values = $templateValues[$fieldIndex];
                                                    }
                                                }
                                            }
                                        }
                                        elseif( !empty( $arPriceCode[0] ) ){
                                            if( $this->profile["CURRENCY"]["CONVERT_CURRENCY"] == "Y" ){
                                                if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                    $convertTo = $this->profile["CURRENCY"][$convertFrom]["CONVERT_TO"];
                                                    if( $this->profile["CURRENCY"][$convertFrom]["RATE"] == "SITE" ){
                                                        $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( CCurrencyRates::ConvertCurrency(
                                                                $arItem["CATALOG_".$arValue[1]],
                                                                $this->profile["CURRENCY"][$convertFrom]["CONVERT_FROM"],
                                                                $convertTo
                                                            ),
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );
                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $templateValues[$fieldIndex];
                                                        }
                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE"] /
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertFrom]["RATE_CNT"] *
                                                            $this->currencyRates[$this->profile["CURRENCY"][$convertFrom]["RATE"]][$convertTo]["RATE_CNT"],
                                                            $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"], 0 //!!2
                                                        );

                                                        if( is_array( $arProductSKU ) ){
                                                            $values = $templateValues[$fieldIndex];
                                                        }
                                                    }
                                                }
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            else{
                                                if( !in_array( $convertFrom, $this->currencyList ) )
                                                    $this->currencyList[] = $convertFrom;
                                            }
                                            if( $this->profile["CURRENCY"][$convertFrom]["CHECK"] ){
                                                $templateValues[$fieldIndex] += $templateValues[$fieldIndex] *
                                                floatval( $this->profile["CURRENCY"][$convertFrom]["PLUS"] ) / 100;
                                                $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                if( is_array( $arProductSKU ) ){
                                                    $values = $templateValues[$fieldIndex];
                                                }
                                            }
                                        }

                                        if( stripos( $arValue[1], "_WD" ) !== false ){
                                            if( in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_WD", $this->usePrices ) ||
                                                in_array( "PRICE_".$arItem["CATALOG_".$arValue[1]."_PRICEID"]."_D", $this->usePrices ) ){
                                                $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arItem["CATALOG_".$arValue[1]."_ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );

                                                $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                                                    $templateValues[$fieldIndex],
                                                    $arItem["CATALOG_".$arValue[1]."_CURRENCY"],
                                                    $arDiscounts
                                                );

                                                $discount = $templateValues[$fieldIndex] - $discountPrice;
                                            }
                                            else{
                                                $discountPrice = $templateValues[$fieldIndex];
                                                $discount = 0;
                                            }

                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_D"] = $discount;
                                            $arItem["CATALOG_PRICE_{$arItem["CATALOG_".$arValue[1]."_PRICEID"]}_WD"] = $discountPrice;
                                            $templateValues[$fieldIndex] = $discountPrice;
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( $field["BITRIX_ROUND_MODE"] == "Y" ){
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::BitrixRoundNumber( $templateValues[$fieldIndex], $arValue[1] );
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $templateValues[$fieldIndex], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }
                                        if( is_array( $arProductSKU ) ){
                                            $values = $templateValues[$fieldIndex];
                                        }

                                        if( is_array( $arProductSKU )&& !is_null( $values ) )
                                            $_arOfferElementResult[$xmlCode][$field["CODE"]][] = $values;

                                        if( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) && ( $arItemConfig["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                            if( count( $arOfferElementResult[$xmlCode][$field["CODE"]] ) ){
                                                if( isset( $field["MINIMUM_OFFER_PRICE_CODE"] ) && strlen( $field["MINIMUM_OFFER_PRICE_CODE"] ) ){
                                                    $templateValues[$fieldPrePostfix.$field["MINIMUM_OFFER_PRICE_CODE"].$fieldPrePostfix] = min( $arOfferElementResult[$xmlCode][$field["CODE"]] );
                                                }
                                            }
                                        }
                                        elseif( isset( $field["MINIMUM_OFFER_PRICE"] ) && ( $field["MINIMUM_OFFER_PRICE"] == "Y" ) ){
                                        }
                                        break;
                                    case 3:
                                        $arItem = $arItemMain;
                                        if( isset( $this->useResolve[$xmlCode] ) ){
                                            $arItem = $this->GetElementProperties( $arElement );
                                        }
                                        if( ( $arValue[0] == $arItem["IBLOCK_ID"] ) || ( $arValue[0] == $arProductSKU["IBLOCK_ID"] ) ){
                                            if( $this->catalogSKU[$arValue[0]]["OFFERS_PROPERTY_ID"] == $arValue[2] ){
                                                $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"] = CAcritExportproplusTools::RoundNumber( $arItem["PROPERTY_{$arValue[2]}_VALUE"][0], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }

                                            if( $this->profile["XMLDATA"]["{$field["CODE"]}"]["PROCESS_LOGIC"] == "N" ){
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_VALUE"];
                                            }
                                            else{
                                                $arProcessValues = $arItem["PROPERTY_{$arValue[2]}_DISPLAY_VALUE"];
                                            }

                                            if( is_array( $arProcessValues ) ){
                                                $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                                $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                                $templateValues[$fieldIndex] = array();
                                                foreach( $arProcessValues as $val ){
                                                    if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                        if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                            $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                        }
                                                    }
                                                    else{
                                                        $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }

                                                if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                    $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                    $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                                }
                                            }
                                            else{
                                                $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                            }
                                        }
                                        $arItem = $arItemMain;
                                        break;
                                    case 5:
                                        $arItem = $arItemMain;

                                        $arItem["HL"] = $this->GetElementHLProperties( $arValue[0], $arValue[3], $arItem );
                                        $arProcessValues = $arItem["HL"][$arValue[4]]["VALUE"];

                                        if( is_array( $arProcessValues ) ){
                                            $arProcessValuesMultiproFormat = CAcritExportproplusTools::ParseMultiproFormat( $arProcessValues, $this->profile, $field["CODE"] );
                                            $arProcessValues = ( is_array( $arProcessValuesMultiproFormat ) && !empty( $arProcessValuesMultiproFormat ) ) ? $arProcessValuesMultiproFormat : $arProcessValues;

                                            $templateValues[$fieldIndex] = array();
                                            foreach( $arProcessValues as $val ){
                                                if( intval( $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ) > 0 ){
                                                    if( count( $templateValues[$fieldIndex] ) < $this->profile["XMLDATA"][$field["CODE"]]["MULTIPROP_LIMIT"] ){
                                                        $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                    }
                                                }
                                                else{
                                                    $templateValues[$fieldIndex][] = CAcritExportproplusTools::RoundNumber( $val, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                                }
                                            }

                                            if( $field["MULTIPROP_TO_STRING"] == "Y" ){
                                                $fieldMultipropDivider = ( strlen( $field["MULTIPROP_DIVIDER"] ) > 0 ) ? $field["MULTIPROP_DIVIDER"] : " ";
                                                $templateValues[$fieldIndex] = implode( $fieldMultipropDivider, $templateValues[$fieldIndex] );
                                            }
                                        }
                                        else{
                                            $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( $arProcessValues, $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                                        }

                                        break;
                                }
                            }
                        }
                    }
                }
                elseif( ( $field["TYPE"] == "const" )
                    || ( ( $field["TYPE"] == "complex" ) && ( $field["COMPLEX_TRUE_TYPE"] == "const" ) ) ){ // const

                    $field["CONTVALUE_TRUE"] = ( $field["TYPE"] == "const" ) ? $field["CONTVALUE_TRUE"] : $field["COMPLEX_TRUE_CONTVALUE"];
                    $templateValues[$fieldIndex] =  CAcritExportproplusTools::RoundNumber( $field["CONTVALUE_TRUE"], $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );
                }
                else{
                    $templateValues[$fieldIndex] = "";
                }
            }

            if( $field["EXPORT_ROWCATEGORY_PARENT_LIST"] == "Y" ){
                $dbSectionRowCategoryParentList = CIBlockSection::GetNavChain( false, $templateValues[$fieldIndex] );
                if( $dbSectionRowCategoryParentList->SelectedRowsCount() ){
                    $sRowCategoryParentList = "";
                    while( $arSectionRowCategoryParentList = $dbSectionRowCategoryParentList->GetNext() ){
                        $sRowCategoryParentList = ( strlen( $sRowCategoryParentList ) <= 0 ) ? $arSectionRowCategoryParentList["NAME"] : $sRowCategoryParentList." > ".$arSectionRowCategoryParentList["NAME"];
                    }

                    if( strlen( $sRowCategoryParentList ) > 0 ){
                        $templateValues[$fieldIndex] = $sRowCategoryParentList;
                    }
                }
            }
            else{
                if( $DB->IsDate( $templateValues[$fieldIndex] ) && ( $this->profile["DATEFORMAT"] == $this->baseDateTimePatern ) ){
                    $templateValues[$fieldIndex] = CAcritExportproplusTools::RoundNumber( CAcritExportproplusTools::GetYandexDateTime( $templateValues[$fieldIndex] ), $field["ROUND"]["PRECISION"], $field["ROUND"]["MODE"] );

                    $dateTimeValue = MakeTimeStamp( "" );
                    $dateTimeFormattedValue = date( "Y-m-d", $dateTimeValue );
                    if( stripos( $templateValues[$fieldIndex], $dateTimeFormattedValue ) !== false ){
                        $skipElement = true;
                        $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", $fieldIndex, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_SKIP" ) ) );
                        $this->log->IncProductError();
                    }
                }

                if( $bCsvMode ){
                    $templateValues = CAcritExportproplusStringProcess::ProcessTagOptions(
                        $templateValues,
                        $field,
                        $fieldIndex
                    );
                }
                else{
                    $templateValues = CAcritExportproplusStringProcess::ProcessTagOptions(
                        $templateValues,
                        $field,
                        $fieldIndex,
                        true,
                        $itemTemplate,
                        $this->arMatches
                    );
                }
            }

            if( ( $field["REQUIRED"] == "Y" ) && ( empty( $templateValues[$fieldIndex] ) || !isset( $templateValues[$fieldIndex] ) ) ){
                $skipElement = true;
                $this->log->AddMessage( "{$arItem["NAME"]} (ID:{$arItem["ID"]}) : ".str_replace( "#FIELD#", $fieldIndex, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_SKIP" ) ) );
                $this->log->IncProductError();
            }
        }
        $arItem = $arItemMain;

        array_walk( $templateValues, function( &$value ){
            if( is_array( $value ) ){
                foreach( $value as $id => $val )
                    $value[$id] = $val;
            }
            else
            $value = $value;
        });

        if( !$bCsvMode ){
            // set market category if it checked
            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = "";

            if( function_exists( "acritRedefine" ) ){
                $acritCategoryRedefine = acritRedefine( $fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix, $arItem["ID"], $this->profile["ID"] );
                if( $acritCategoryRedefine ){
                    $templateValues[$fieldPrePostfix."CATEGORYID".$fieldPrePostfix] = $acritCategoryRedefine;
                    $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->arMarketCategory[$templateValues[$fieldPrePostfix."CATEGORYID".$fieldPrePostfix] - 1];
                }
            }

            if( !$templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ){
                switch( $this->profile["TYPE"] ){
                    case "ebay":
                    case "ebay_1":
                    case "ebay_2":
                        if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]];
                        }
                        elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                        }
                        else{
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                        }

                        $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["EBAY"]["CATEGORY_LIST"];

                        break;
                    case "google":
                    case "google_online":
                        if( ( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ) && ( $this->profile["USE_MARKET_CATEGORY"] == "Y" ) ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]];
                        }
                        elseif( ( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ) && ( $this->profile["USE_MARKET_CATEGORY"] == "Y" ) ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                        }
                        else{
                            if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                            }
                        }

                        $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"];

                        break;
                    case "ozon":
                    case "ozon_api":
                        if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                            if( strlen( trim( $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]] ) ) <= 0 ){
                                return $arItem;
                            }

                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]];
                            if( !empty( $arOzonCategories ) ){
                                foreach( $arOzonCategories as $arOzonCategoriesItem ){
                                    if( $arOzonCategoriesItem["ProductTypeId"] == $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]] ){
                                        $templateValues[$fieldPrePostfix."CAPABILITY_TYPE".$fieldPrePostfix] = $arOzonCategoriesItem["Name"];
                                    }
                                }
                            }
                        }
                        elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                            if( strlen( trim( $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]] ) ) <= 0 ){
                                return $arItem;
                            }

                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                            if( !empty( $arOzonCategories ) ){
                                foreach( $arOzonCategories as $arOzonCategoriesItem ){
                                    if( $arOzonCategoriesItem["ProductTypeId"] == $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]] ){
                                        $templateValues[$fieldPrePostfix."CAPABILITY_TYPE".$fieldPrePostfix] = $arOzonCategoriesItem["Name"];
                                    }
                                }
                            }
                        }
                        else{
                            if( strlen( trim( $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ) ) <= 0 ){
                                return $arItem;
                            }

                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                            if( !empty( $arOzonCategories ) ){
                                foreach( $arOzonCategories as $arOzonCategoriesItem ){
                                    if( $arOzonCategoriesItem["ProductTypeId"] == $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ){
                                        $templateValues[$fieldPrePostfix."CAPABILITY_TYPE".$fieldPrePostfix] = $arOzonCategoriesItem["Name"];
                                    }
                                }
                            }
                        }

                        $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["OZON"]["CATEGORY_LIST"];

                        break;
                    case "vk_trade":
                        if( !$bCsvMode ){
                            if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["VK"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]];
                            }
                            elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["VK"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                            }
                            else{
                                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["VK"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                            }

                            $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["VK"]["CATEGORY_LIST"];

                            break;
                        }
                    case "y_realty":
                        break;
                    case "tiu_standart":
                    case "tiu_standart_vendormodel":
                        $bUseCategoryRedefine = false;
                        if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]];
                            if( !empty( $this->marketCategory ) && !empty( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ){
                                foreach( $this->marketCategory as $arCategoriesItem ){
                                    if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]] ){
                                        $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
                                        $templateValues[$fieldPrePostfix."PORTAL_URL".$fieldPrePostfix] = $arCategoriesItem["PORTAL_URL"];
                                        break;
                                    }
                                }
                                $bUseCategoryRedefine = true;
                            }
                        }
                        elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                            if( !empty( $this->marketCategory ) && !empty( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ){
                                foreach( $this->marketCategory as $arCategoriesItem ){
                                    if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]] ){
                                        $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
                                        $templateValues[$fieldPrePostfix."PORTAL_URL".$fieldPrePostfix] = $arCategoriesItem["PORTAL_URL"];
                                        break;
                                    }
                                }
                                $bUseCategoryRedefine = true;
                            }
                        }
                        else{
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                            if( !empty( $this->marketCategory ) && !empty($templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix])){
                                foreach( $this->marketCategory as $arCategoriesItem ){
                                    if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ){
                                        $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
                                        $templateValues[$fieldPrePostfix."PORTAL_URL".$fieldPrePostfix] = $arCategoriesItem["PORTAL_URL"];
                                        break;
                                    }
                                }
                                $bUseCategoryRedefine = true;
                            }
                        }

                        if( ( strlen( trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ) > 0 )
                            && is_array( $this->arMarketCategory ) && !empty( $this->arMarketCategory )
                            && $bUseCategoryRedefine ){

                            $marketCategory = trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] );
                            $templateValues[$fieldPrePostfix."CATEGORYID".$fieldPrePostfix] = array_search( $marketCategory, $this->arMarketCategory ) + 1;
                        }

                        $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"];

                        break;

	                case "mailru":
	                case "mailru_clothing":
		                $bUseCategoryRedefine = false;
		                if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
			                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]];
			                if( !empty( $this->marketCategory ) && !empty( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ){
				                foreach( $this->marketCategory as $arCategoriesItem ){
					                if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]] ){
						                $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
						                break;
					                }
				                }
				                $bUseCategoryRedefine = true;
			                }
		                }
		                elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
			                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
			                if( !empty( $this->marketCategory ) && !empty( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ){
				                foreach( $this->marketCategory as $arCategoriesItem ){
					                if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]] ){
						                $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
						                break;
					                }
				                }
				                $bUseCategoryRedefine = true;
			                }
		                }
		                else{
			                $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
			                if( !empty( $this->marketCategory ) && !empty($templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix])){
				                foreach( $this->marketCategory as $arCategoriesItem ){
					                if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ){
						                $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
						                break;
					                }
				                }
				                $bUseCategoryRedefine = true;
			                }
		                }

		                if( ( strlen( trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ) > 0 )
			                && is_array( $this->arMarketCategory ) && !empty( $this->arMarketCategory )
			                && $bUseCategoryRedefine ){

			                $marketCategory = trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] );



			                $templateValues[$fieldPrePostfix."CATEGORYID".$fieldPrePostfix] = array_search( $marketCategory, $this->arMarketCategory ) + 1;
		                }

		                $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"];

		                break;

                    case "ua_prom_ua":
                        $bUseCategoryRedefine = false;
                        if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]];
                            if( !empty( $this->marketCategory ) && !empty( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ){
                                foreach( $this->marketCategory as $arCategoriesItem ){
                                    if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]] ){
                                        $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
                                        $templateValues[$fieldPrePostfix."PORTAL_URL".$fieldPrePostfix] = $arCategoriesItem["PORTAL_URL"];
                                        break;
                                    }
                                }
                                $bUseCategoryRedefine = true;
                            }
                        }
                        elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                            if( !empty( $this->marketCategory ) && !empty( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ){
                                foreach( $this->marketCategory as $arCategoriesItem ){
                                    if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]] ){
                                        $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
                                        $templateValues[$fieldPrePostfix."PORTAL_URL".$fieldPrePostfix] = $arCategoriesItem["PORTAL_URL"];
                                        break;
                                    }
                                }
                                $bUseCategoryRedefine = true;
                            }
                        }
                        else{
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]];
                            if( !empty( $this->marketCategory ) && !empty($templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix])){
                                foreach( $this->marketCategory as $arCategoriesItem ){
                                    if( $arCategoriesItem["NAME"] == $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] ){
                                        $templateValues[$fieldPrePostfix."PORTAL_ID".$fieldPrePostfix] = $arCategoriesItem["PORTAL_ID"];
                                        $templateValues[$fieldPrePostfix."PORTAL_URL".$fieldPrePostfix] = $arCategoriesItem["PORTAL_URL"];
                                        break;
                                    }
                                }
                                $bUseCategoryRedefine = true;
                            }
                        }

                        if( ( strlen( trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ) > 0 )
                            && is_array( $this->arMarketCategory ) && !empty( $this->arMarketCategory )
                            && $bUseCategoryRedefine ){

                            $marketCategory = trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] );
                            $templateValues[$fieldPrePostfix."CATEGORYID".$fieldPrePostfix] = array_search( $marketCategory, $this->arMarketCategory ) + 1;
                        }

                        $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"];

                        break;
                    default:
                        $bUseCategoryRedefine = false;
                        if( ( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ) && ( $this->profile["USE_MARKET_CATEGORY"] == "Y" ) ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = htmlspecialcharsbx( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_ID"]] );
                            $bUseCategoryRedefine = true;
                        }
                        elseif( ( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ) && ( $this->profile["USE_MARKET_CATEGORY"] == "Y" ) ){
                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = htmlspecialcharsbx( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]] );
                            $bUseCategoryRedefine = true;
                        }
                        else{
                            if( $this->profile["USE_MARKET_CATEGORY"] == "Y" ){
                                if( is_array( $arItem["SECTION_ID"] ) && !empty( $arItem["SECTION_ID"] ) ){
                                    $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = "";
                                    foreach( $arItem["SECTION_ID"] as $itemSectionId ){
                                        $tmpMarketCategory = htmlspecialcharsbx( trim( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$itemSectionId] ) );
                                        if( strlen( $tmpMarketCategory ) > 0 ){
                                            $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = $tmpMarketCategory;
                                            break;
                                        }
                                    }

                                    if( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] == "" ){
                                        $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = htmlspecialcharsbx( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] );
                                    }
                                }
                                else{
                                    $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] = htmlspecialcharsbx( $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"][$arItem["IBLOCK_SECTION_ID"]] );
                                }
                                $bUseCategoryRedefine = true;
                            }
                        }

                        if( ( strlen( trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) ) > 0 )
                            && is_array( $this->arMarketCategory ) && !empty( $this->arMarketCategory )
                            && $bUseCategoryRedefine ){

                            $marketCategory = trim( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] );
                            $templateValues[$fieldPrePostfix."CATEGORYID".$fieldPrePostfix] = array_search( $marketCategory, $this->arMarketCategory ) + 1;
                        }

                        $arMarketCategoryList = $this->profile["MARKET_CATEGORY"]["CATEGORY_LIST"];

                        break;
                }
            }

            if( $this->profile["SETUP"]["USE_CATEGORY_REDEFINE_TAG"] == "Y" ){
                if( strlen( trim( $this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"] ) ) > 0 ){
                    $marketCategoryValue = $arMarketCategoryList[$templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix]];
                    $profileCategoryValue = $this->profile["PROFILE_CATEGORIES"][$templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix]]["NAME"];

                    if( strlen( trim( $marketCategoryValue ) ) > 0 ){
                        $templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix] = trim( $marketCategoryValue );
                    }
                    elseif( strlen( trim( $profileCategoryValue ) ) > 0 ){
                        $templateValues[$fieldPrePostfix.$this->profile["SETUP"]["CATEGORY_REDEFINE_TAG"].$fieldPrePostfix] = trim( $profileCategoryValue );
                    }
                }
            }

            $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix] = "";
            switch( $this->profile["TYPE"] ){
                case "vk_trade":
                    if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix] = $this->profile["VK"]["VK_MARKET_ALBUMS"][$arItem["IBLOCK_ID"]];
                    }
                    elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix] = $this->profile["VK"]["VK_MARKET_ALBUMS"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                    }
                    elseif( $this->profile["VK"]["VK_IS_MARKET_ALBUMS_MULTICATEGORIES"] == "Y" ){
                        $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix] = array();
                        foreach( $arItem["SECTION_EXDATA"] as $multiSectionId => $arSectionData ){
                            $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix][$multiSectionId] = array(
                                "ID" => $arItem["IBLOCK_SECTION_ID"],
                                "NAME" => $this->profile["VK"]["VK_MARKET_ALBUMS"][$multiSectionId],
                                "PICTURE" => ( intval( $arSectionData["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["PICTURE"] ) ) : false,
                                "DETAIL_PICTURE" => ( intval( $arSectionData["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["DETAIL_PICTURE"] ) ) : false
                            );
                        }
                    }
                    else{                                                                                       
                        $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix] = array();
                        $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix][] = array(
                            "ID" => $arItem["IBLOCK_SECTION_ID"],
                            "NAME" => $this->profile["VK"]["VK_MARKET_ALBUMS"][$arItem["IBLOCK_SECTION_ID"]],
                            "PICTURE" => ( intval( $arSectionData[0]["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["PICTURE"] ) ) : false,
                            "DETAIL_PICTURE" => ( intval( $arSectionData[0]["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["DETAIL_PICTURE"] ) ) : false
                        );
                    }
                    break;
                default:
                    break;
            }

            $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix] = "";
            switch( $this->profile["TYPE"] ){
                case "vk_trade":
                    if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix] = $this->profile["VK"]["VK_ALBUMS"][$arItem["IBLOCK_ID"]];
                    }
                    elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix] = $this->profile["VK"]["VK_ALBUMS"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                    }
                    elseif( $this->profile["VK"]["VK_IS_ALBUMS_MULTICATEGORIES"] == "Y" ){
                        $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix] = array();
                        foreach( $arItem["SECTION_EXDATA"] as $multiSectionId => $arSectionData ){
                            $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix][$multiSectionId] = array(
                                "ID" => $arItem["IBLOCK_SECTION_ID"],
                                "NAME" => $this->profile["VK"]["VK_ALBUMS"][$multiSectionId],
                                "PICTURE" => ( intval( $arSectionData["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["PICTURE"] ) ) : false,
                                "DETAIL_PICTURE" => ( intval( $arSectionData["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["DETAIL_PICTURE"] ) ) : false
                            );
                        }
                    }
                    else{
                        $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix] = array();
                        $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix][] = array(
                            "ID" => $arItem["IBLOCK_SECTION_ID"],
                            "NAME" => $this->profile["VK"]["VK_ALBUMS"][$arItem["IBLOCK_SECTION_ID"]],
                            "PICTURE" => ( intval( $arSectionData[0]["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["PICTURE"] ) ) : false,
                            "DETAIL_PICTURE" => ( intval( $arSectionData[0]["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["DETAIL_PICTURE"] ) ) : false
                        );
                    }
                    break;
                default:
                    break;
            }

            $templateValues[$fieldPrePostfix."OK_ALBUMS".$fieldPrePostfix] = "";
            switch( $this->profile["TYPE"] ){
                case "ok_trade":
                    if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."OK_ALBUMS".$fieldPrePostfix] = $this->profile["OK"]["OK_ALBUMS"][$arItem["IBLOCK_ID"]];
                    }
                    elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."OK_ALBUMS".$fieldPrePostfix] = $this->profile["OK"]["OK_ALBUMS"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                    }
                    elseif( $this->profile["OK"]["OK_IS_ALBUMS_MULTICATEGORIES"] == "Y" ){
                        $templateValues[$fieldPrePostfix."OK_ALBUMS".$fieldPrePostfix] = array();
                        foreach( $arItem["SECTION_EXDATA"] as $multiSectionId => $arSectionData ){
                            $templateValues[$fieldPrePostfix."OK_ALBUMS".$fieldPrePostfix][$multiSectionId] = array(
                                "ID" => $arItem["IBLOCK_SECTION_ID"],
                                "NAME" => $this->profile["OK"]["OK_ALBUMS"][$multiSectionId],
                                "PICTURE" => ( intval( $arSectionData["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["PICTURE"] ) ) : false,
                                "DETAIL_PICTURE" => ( intval( $arSectionData["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["DETAIL_PICTURE"] ) ) : false
                            );
                        }
                    }
                    else{
                        $templateValues[$fieldPrePostfix."OK_ALBUMS".$fieldPrePostfix][] = array(
                            "ID" => $arItem["IBLOCK_SECTION_ID"],
                            "NAME" => $this->profile["OK"]["OK_ALBUMS"][$arItem["IBLOCK_SECTION_ID"]],
                            "PICTURE" => ( intval( $arSectionData[0]["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["PICTURE"] ) ) : false,
                            "DETAIL_PICTURE" => ( intval( $arSectionData[0]["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["DETAIL_PICTURE"] ) ) : false
                        );
                    }
                    break;
                default:
                    break;
            }

            $templateValues[$fieldPrePostfix."OK_CATALOGS".$fieldPrePostfix] = "";
            switch( $this->profile["TYPE"] ){
                case "ok_trade":
                    if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."OK_CATALOGS".$fieldPrePostfix] = $this->profile["OK"]["OK_CATALOGS"][$arItem["IBLOCK_ID"]];
                    }
                    elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                        $templateValues[$fieldPrePostfix."OK_CATALOGS".$fieldPrePostfix] = $this->profile["OK"]["OK_CATALOGS"][$arItem["IBLOCK_PRODUCT_SECTION_ID"]];
                    }
                    elseif( $this->profile["OK"]["OK_IS_ALBUMS_MULTICATEGORIES"] == "Y" ){
                        $templateValues[$fieldPrePostfix."OK_CATALOGS".$fieldPrePostfix] = array();
                        foreach( $arItem["SECTION_EXDATA"] as $multiSectionId => $arSectionData ){
                            $templateValues[$fieldPrePostfix."OK_CATALOGS".$fieldPrePostfix][$multiSectionId] = array(
                                "ID" => $arItem["IBLOCK_SECTION_ID"],
                                "NAME" => $this->profile["OK"]["OK_CATALOGS"][$multiSectionId],
                                "PICTURE" => ( intval( $arSectionData["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["PICTURE"] ) ) : false,
                                "DETAIL_PICTURE" => ( intval( $arSectionData["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData["DETAIL_PICTURE"] ) ) : false
                            );
                        }
                    }
                    else{
                        $templateValues[$fieldPrePostfix."OK_CATALOGS".$fieldPrePostfix][] = array(
                            "ID" => $arItem["IBLOCK_SECTION_ID"],
                            "NAME" => $this->profile["OK"]["OK_CATALOGS"][$arItem["IBLOCK_SECTION_ID"]],
                            "PICTURE" => ( intval( $arSectionData[0]["PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["PICTURE"] ) ) : false,
                            "DETAIL_PICTURE" => ( intval( $arSectionData[0]["DETAIL_PICTURE"] ) > 0 ) ? array( $_SERVER["DOCUMENT_ROOT"].CFile::GetPath( $arSectionData[0]["DETAIL_PICTURE"] ) ) : false
                        );
                    }
                    break;
                default:
                    break;
            } 

            if( $this->profile["TYPE"] == "vk_trade" ){
                if( ( strlen( $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix] ) > 0 )
                    && ( intval( $templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."URL".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) > 0 )
                    && ( intval( $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix] ) > 0 ) ){
                        $arData = array();
                        $arData["ID"] = $templateValues[$fieldPrePostfix."ID".$fieldPrePostfix];
                        $arData["NAME"] = $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix];
                        $arData["DESCRIPTION"] = $templateValues[$fieldPrePostfix."DESCRIPTION".$fieldPrePostfix];

                        $sUrlParams = "";
                        if( strlen( $templateValues[$fieldPrePostfix."UTM_SOURCE".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_source=".$templateValues[$fieldPrePostfix."UTM_SOURCE".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_MEDIUM".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_medium=".$templateValues[$fieldPrePostfix."UTM_MEDIUM".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_TERM".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_term=".$templateValues[$fieldPrePostfix."UTM_TERM".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_CONTENT".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_content=".$templateValues[$fieldPrePostfix."UTM_CONTENT".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_CAMPAIGN".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_campaign=".$templateValues[$fieldPrePostfix."UTM_CAMPAIGN".$fieldPrePostfix];
                        }

                        $arData["URL"] = $templateValues[$fieldPrePostfix."URL".$fieldPrePostfix].( ( strlen( $sUrlParams ) > 0 ) ? $sUrlParams : "" );
                        $arData["URL_LABEL"] = $templateValues[$fieldPrePostfix."URL_LABEL".$fieldPrePostfix];
                        $arData["DESCRIPTION_PREFIX"] = GetMessage( "ACRIT_FB_PRICE" ).$templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix]." ".GetMessage( "ACRIT_FB_PRICE_CURRENCY" )."\n\n";
                        $arData["PRICE"] = $templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix];
                        $arData["CATEGORYID"] = $templateValues[$fieldPrePostfix."CATEGORYID".$fieldPrePostfix];
                        $arData["MARKET_CATEGORY"] = $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix];
                        $arData["VK"]["VK_MARKET_ALBUMS"] = $templateValues[$fieldPrePostfix."VK_MARKET_ALBUMS".$fieldPrePostfix];
                        $arData["VK"]["VK_ALBUMS"] = $templateValues[$fieldPrePostfix."VK_ALBUMS".$fieldPrePostfix];
                        $arData["PHOTO"] = array();
                        $arData["PHOTO"][] = $_SERVER["DOCUMENT_ROOT"].$templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix];

                        foreach( $templateValues[$fieldPrePostfix."ADDITIONAL_PHOTOS".$fieldPrePostfix] as $additionalPhotoIndex => $additionalPhotoValue ){
                            $templateValues[$fieldPrePostfix."ADDITIONAL_PHOTOS".$fieldPrePostfix][$additionalPhotoIndex] = $_SERVER["DOCUMENT_ROOT"].$additionalPhotoValue;
                        }

                        $arData["ADDITIONAL_PHOTOS"] = $templateValues[$fieldPrePostfix."ADDITIONAL_PHOTOS".$fieldPrePostfix];
                        $arData["IS_DELETED"] = 0;
                        //$arData["IS_DELETED"] = if( $templateValues[$fieldPrePostfix."AVAILABLE".$fieldPrePostfix] ) ? 1 : 0;

                        $this->obVkTools->SaveMarketItem( $arData );                        
                        
                        $this->DemoCountInc();
                }
            }
            elseif( $this->profile["TYPE"] == "fb_trade" ){
                if( ( strlen( $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix] ) > 0 )
                    && ( intval( $templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."URL".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) > 0 ) ){
                        $arData = array();
                        $arData["ID"] = $templateValues[$fieldPrePostfix."ID".$fieldPrePostfix];
                        $arData["NAME"] = $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix];
                        $arData["URL"] = $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$templateValues[$fieldPrePostfix."URL".$fieldPrePostfix];
                        $arData["MESSAGE"] = GetMessage( "ACRIT_FB_PRICE" ).$templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix]." ".$templateValues[$fieldPrePostfix."CURRENCYID".$fieldPrePostfix]."\n\n".$templateValues[$fieldPrePostfix."URL_LABEL".$fieldPrePostfix].$arData["URL"]."\n\n".$templateValues[$fieldPrePostfix."MESSAGE".$fieldPrePostfix];
                        $arData["DESCRIPTION"] = $templateValues[$fieldPrePostfix."DESCRIPTION".$fieldPrePostfix];

                        if( is_array( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) && !empty( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) ){
                            $arData["PHOTO"] = $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix][0];
                        }
                        else{
                            $arData["PHOTO"] = $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix];
                        }
                        $this->obFb->ProcessData( $arData );

                        $this->DemoCountInc();
                }
            }
            elseif( $this->profile["TYPE"] == "instagram_trade" ){
                if( ( strlen( $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."MESSAGE".$fieldPrePostfix] ) > 0 )
                    && ( intval( $templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."URL".$fieldPrePostfix] ) > 0 )
                    && (
                        ( strlen( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) > 0 )
                        || is_array( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] )
                    )
                ){
                    $arData = array();
                    $arData["ID"] = $templateValues[$fieldPrePostfix."ID".$fieldPrePostfix];
                    $arData["NAME"] = $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix];
                    $arData["URL"] = $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$templateValues[$fieldPrePostfix."URL".$fieldPrePostfix];
                    $arData["DESCRIPTION"] = $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix]."\n\n".GetMessage( "ACRIT_FB_PRICE" ).$templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix]." ".$templateValues[$fieldPrePostfix."CURRENCYID".$fieldPrePostfix]."\n\n".$templateValues[$fieldPrePostfix."URL_LABEL".$fieldPrePostfix].$arData["URL"]."\n\n".$templateValues[$fieldPrePostfix."MESSAGE".$fieldPrePostfix];

                    if( is_array( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) && !empty( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) ){
                        $arData["PHOTO"] = array();
                        foreach( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] as $photoItem ){
                            $arData["PHOTO"][] = $_SERVER["DOCUMENT_ROOT"].$photoItem;
                        }
                    }
                    else{
                        $arData["PHOTO"] = $_SERVER["DOCUMENT_ROOT"].$templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix];
                    }
                    
                    $this->obInstagramTools->SavePost( $arData );

                    $this->DemoCountInc();
                }
            }
            elseif( $this->profile["TYPE"] == "ok_trade" ){
                if( ( strlen( $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix] ) > 0 )
                    && ( intval( $templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."URL".$fieldPrePostfix] ) > 0 )
                    && ( strlen( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) > 0 ) ){
                        $arData = array();
                        $arData["ID"] = $templateValues[$fieldPrePostfix."ID".$fieldPrePostfix];
                        $arData["NAME"] = $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix];

                        $sUrlParams = "";
                        if( strlen( $templateValues[$fieldPrePostfix."UTM_SOURCE".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_source=".$templateValues[$fieldPrePostfix."UTM_SOURCE".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_MEDIUM".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_medium=".$templateValues[$fieldPrePostfix."UTM_MEDIUM".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_TERM".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_term=".$templateValues[$fieldPrePostfix."UTM_TERM".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_CONTENT".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_content=".$templateValues[$fieldPrePostfix."UTM_CONTENT".$fieldPrePostfix];
                        }

                        if( strlen( $templateValues[$fieldPrePostfix."UTM_CAMPAIGN".$fieldPrePostfix] ) > 0 ){
                            $sUrlParams .= ( strlen( $sUrlParams ) > 0 ) ? "&amp;" : "?";
                            $sUrlParams .= "utm_campaign=".$templateValues[$fieldPrePostfix."UTM_CAMPAIGN".$fieldPrePostfix];
                        }

                        $arData["URL"] = $this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$templateValues[$fieldPrePostfix."URL".$fieldPrePostfix].( ( strlen( $sUrlParams ) > 0 ) ? $sUrlParams : "" );
                        $arData["DESCRIPTION"] = GetMessage( "ACRIT_OK_PRICE" ).$templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix]." ".$templateValues[$fieldPrePostfix."CURRENCYID".$fieldPrePostfix]."\n\n".$templateValues[$fieldPrePostfix."URL_LABEL".$fieldPrePostfix].$arData["URL"]."\n\n".$templateValues[$fieldPrePostfix."DESCRIPTION".$fieldPrePostfix];
                        $arData["DESCRIPTION_MARKET"] = $templateValues[$fieldPrePostfix."URL_LABEL".$fieldPrePostfix]."<a href=\"".$arData["URL"]."\">".$arData["URL"]."</a>\n\n".$templateValues[$fieldPrePostfix."DESCRIPTION".$fieldPrePostfix];
                        $arData["PRICE"] = $templateValues[$fieldPrePostfix."PRICE".$fieldPrePostfix];
                        $arData["CURRENCY"] = $templateValues[$fieldPrePostfix."CURRENCYID".$fieldPrePostfix];
                        $arData["OK"]["OK_ALBUMS"] = $templateValues[$fieldPrePostfix."OK_ALBUMS".$fieldPrePostfix];
                        $arData["OK"]["OK_CATALOGS"] = $templateValues[$fieldPrePostfix."OK_CATALOGS".$fieldPrePostfix];

                        if( is_array( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) && !empty( $templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix] ) ){
                            $arData["PHOTO"][] = $_SERVER["DOCUMENT_ROOT"].$templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix][0];
                        }
                        else{
                            $arData["PHOTO"][] = $_SERVER["DOCUMENT_ROOT"].$templateValues[$fieldPrePostfix."PHOTO".$fieldPrePostfix];
                        }
                        $this->obOkTools->ProcessData( $arData );

                        $this->DemoCountInc();
                }
            }
            else{
                //for some realty
                if( isset( $templateValues[$fieldPrePostfix."PRICE_VALUE".$fieldPrePostfix] ) ){
                    $templateValues[$fieldPrePostfix."PRICE_VALUE".$fieldPrePostfix] = intval( $templateValues[$fieldPrePostfix."PRICE_VALUE".$fieldPrePostfix] );
                }

                if( isset( $templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix] ) ){
                    if( !file_exists( $templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix] ) ){
                        $templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix] = $this->defaultFields[$fieldPrePostfix."SITE_URL".$fieldPrePostfix].$templateValues[$fieldPrePostfix."OBJECT_IMAGE".$fieldPrePostfix];
                    }
                }

                // set values
                $itemTemplate = str_replace( array_keys( $this->defaultFields ), array_values( $this->defaultFields ), $itemTemplate );
                $itemTemplate = str_replace( array_keys( $templateValues ), array_values( $templateValues ), $itemTemplate );

                // removes empty first level tags, if there is no nesting
                $itemTemplate = preg_replace( "/(\r\n[\t]*\r\n)/", "\r\n", $itemTemplate );
                $itemTemplate = preg_replace( "/(\r\n\r\n)/", "\r\n", $itemTemplate );

                $itemTemplate = preg_replace( "/\s\w+\W*\w*=\"\"/", "", $itemTemplate );
                if( $this->profile["USE_EMPTY_TAG_CUT"] == "Y" ){
                    $itemTemplate = preg_replace( "#(<\S+/>)#i", "", $itemTemplate );
										$strPattern = "#(<(.*)>\s*<\/\\2>)#is";
										while(preg_match( $strPattern, $itemTemplate )) {
											$itemTemplate = preg_replace( $strPattern,  "", $itemTemplate );
										}
                }

                if( $this->profile["SETUP"]["CONVERT_DATA_REGEXP"] == "Y" ){
                    if( !empty( $this->profile["CONVERT_DATA"] ) ){
                        foreach( $this->profile["CONVERT_DATA"] as $arConvertBlock ){
                            $itemTemplate = preg_replace( $arConvertBlock[0], $arConvertBlock[1], $itemTemplate );
                        }
                    }
                }
                else{
                    if( !empty( $this->profile["CONVERT_DATA"] ) ){
                        foreach( $this->profile["CONVERT_DATA"] as $arConvertBlock ){
                            $itemTemplate = str_replace( $arConvertBlock[0], $arConvertBlock[1], $itemTemplate );
                        }
                    }
                }

                if( $this->profile["USE_IBLOCK_CATEGORY"] == "Y" ){
                    $variantContainerId = $arItem["IBLOCK_ID"];
                }
                elseif( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
                    $variantContainerId = $arItem["IBLOCK_PRODUCT_SECTION_ID"];
                }
                else{
                    $variantContainerId = $arItem["IBLOCK_SECTION_ID"];
                }
								
                if( !$skipElement ){
                    if( $this->profile["TYPE"] == "ozon_api" ){
                        preg_match( "/.*?(<description>.*?<\/description>).*?/is", $itemTemplate, $arDescriptionMatches );

                        $arOzonData = array(
                            "SKU" => array(
                                "Name" => $templateValues[$fieldPrePostfix."NAME".$fieldPrePostfix],
                                "ManufacturerIdentifier" => $templateValues[$fieldPrePostfix."MANUFACTURER_IDENTIFIER".$fieldPrePostfix],
                                "GrossWeight" => $templateValues[$fieldPrePostfix."GROSS_WEIGHT".$fieldPrePostfix],
                            ),
                            "Price" => array(
                                "SellingPrice" => $templateValues[$fieldPrePostfix."SELLING_PRICE".$fieldPrePostfix],
                            ),
                            "Availability" => array(
                                "DaysForShippingDelay" => $templateValues[$fieldPrePostfix."SUPPLY_PERIOD".$fieldPrePostfix],
                                "SupplyState" => $templateValues[$fieldPrePostfix."SUPPLY_STATE".$fieldPrePostfix],
                                "SellingState" => $templateValues[$fieldPrePostfix."SELLING_STATE".$fieldPrePostfix],
                            ),
                            "Description" => $arDescriptionMatches[1],
                            "MerchantSKU" => $templateValues[$fieldPrePostfix."ID".$fieldPrePostfix],
                            "ProductTypeID" => $templateValues[$fieldPrePostfix."MARKET_CATEGORY".$fieldPrePostfix],
                        );

                        $sOzonData = \Bitrix\Main\Web\Json::encode( $arOzonData );
                        $arAddJobData = $this->obOzonTools->SaveProduct( $sOzonData );

                        if( !isset( $this->profile["SETUP"]["OZON_JOBS"] ) ){
                            $this->profile["SETUP"]["OZON_JOBS"] = array();
                            $this->profile["SETUP"]["OZON_DATA"] = array();
                        }

                        $this->profile["SETUP"]["OZON_JOBS"][] = $arAddJobData["JobId"];
                        $this->profile["SETUP"]["OZON_DATA"][$templateValues[$fieldPrePostfix."ID".$fieldPrePostfix]] = $sOzonData;

                        $this->dbProfile->Update( $this->profile["ID"], $this->profile );
                    }
                    else{
                        if( is_array( $_arOfferElementResult ) && count( $_arOfferElementResult ) ){
                            $arOfferElementResult = array_merge_recursive( $arOfferElementResult, $_arOfferElementResult );
                        }
                        $processElementId = ( intval( $arItem["ELEMENT_ID"] ) > 0 ) ? $arItem["ELEMENT_ID"] : $arItem["ID"];
                        $dbElementGroups = CIBlockElement::GetElementGroups( $processElementId, true );
                        $arItemSections = array();
                        while( $arElementGroups = $dbElementGroups->Fetch() ){
                            $arItemSections[] = $arElementGroups["ID"];
                        }

                        CAcritExportproplusTools::SaveSections( $this->profile, $arItemSections );
                        $this->DemoCountInc();

                        if( !CAcritExportproplusTools::isVariant( $this->profile, $variantContainerId ) ){
                            if( isset( $arItemConfig["DELAY_FLUSH"] ) && ( $arItemConfig["DELAY_FLUSH"] === true ) ){
                                CAcritExportproplusExport::Save( $itemTemplate.$this->delay );
                                $this->delay = "";
                            }
                            elseif( isset( $arItemConfig["DELAY_SKU"] ) && ( $arItemConfig["DELAY_SKU"] === true ) ){
                                $this->delay .= $itemTemplate;
                                $this->log->IncProductExport();
                            }
                            elseif( isset( $arItemConfig["DELAY"] ) && ( $arItemConfig["DELAY"] === true ) ){
                                $this->log->IncProductExport();
                            }
                            else{
                                CAcritExportproplusExport::Save( $itemTemplate );
                                $this->log->IncProductExport();
                            }
                        }
                    }

                }
                unset( $arElement, $dbPrices, $arQuantity );
                if( CAcritExportproplusTools::isVariant( $this->profile, $variantContainerId ) )
                    return array( "ITEM" => $arItem, "XML" => $itemTemplate, "SKIP" => $skipElement, "OFFER" => is_array( $arProductSKU ) );
            }
            return $arItem;
        }
        else{
            if( !$skipElement ){
                $this->DemoCountInc();
                $this->log->IncProductExport();
            }
            return !empty( $templateValues ) ? $templateValues : false;
        }
    }

    // searching product offers IB and remove them from list if them active and isset parent product offers IB
    protected function PrepareIBlock(){
        $excludeIBlock = array();
        $this->catalogSKU = array();

        if(
            ( ( $this->profile["USE_SKU"] == "Y" ) || ( $this->profile["TYPE"] == "advantshop" ) )
            && ( CAcritExportproplusTools::ArrayValidate( $this->profile["IBLOCK_ID"] ) )
        ){
            foreach( $this->profile["IBLOCK_ID"] as $iblocID ){
                if( $this->catalogIncluded ){
                    if( $arIBlock = CCatalog::GetByID( $iblocID ) ){
                        if( intval( $arIBlock["PRODUCT_IBLOCK_ID"] ) > 0 && in_array( $arIBlock["PRODUCT_IBLOCK_ID"], $this->profile["IBLOCK_ID"] ) )
                            $excludeIBlock[] = $arIBlock["IBLOCK_ID"];
                        if( intval( $arIBlock["OFFERS_IBLOCK_ID"] ) > 0 )
                            $this->catalogSKU[$arIBlock["IBLOCK_ID"]] = $arIBlock;
                    }
                }
            }
        }

        return array_diff( ( is_array( $this->profile["IBLOCK_ID"] ) ? $this->profile["IBLOCK_ID"] : array() ), $excludeIBlock );
    }

    // get product hl properties by hlBlockId
    private function GetElementHLProperties( $hlBlockId, $hlBasePropId = false, $arItem = false ){
        if( !isset( $hlBlockId ) || ( intval( $hlBlockId ) <= 0 ) ){
            return false;
        }

        $arEntityHLFieldsList = array();

        $dbHlElemProp = CIBlockElement::GetProperty( $arItem["IBLOCK_ID"], $arItem["ID"], array(), array( "ID" => $hlBasePropId ) );
        if( $arHlElemProp = $dbHlElemProp->GetNext() ){
            $arHLBlock = HighloadBlockTable::getList(
                array(
                    "filter" => array(
                        "=ID" => $hlBlockId,
                    )
                )
            )->fetch();

            $dbEntityHLFields = CUserTypeEntity::GetList(
                array(
                    "ID" => "ASC"
                ),
                array(
                    "ENTITY_ID" => "HLBLOCK_".$hlBlockId
                )
            );


            $iii = 0;
            while( $arEntityHLFieldsRow = $dbEntityHLFields->Fetch() ){
                $arEntityHLFieldsList[$arEntityHLFieldsRow["FIELD_NAME"]] = $arEntityHLFieldsRow;
                $iii++;
            }

            $obEntity = HighloadBlockTable::compileEntity( $arHLBlock );
            $strEntityDataClass = $obEntity->getDataClass();

            $dbHLBlockRow = $strEntityDataClass::getList( array( "filter" => array( "UF_XML_ID" => $arHlElemProp["VALUE"] ) ) );

            while( $arHLBlockRow = $dbHLBlockRow->fetch() ){
                foreach( $arEntityHLFieldsList as $entityHLFieldsListIndex => $arEntityHLFields ){
                    if( $arEntityHLFields["USER_TYPE_ID"] == "file" ){
                        $arEntityHLFieldsList[$entityHLFieldsListIndex]["VALUE"] = CFile::GetPath( $arHLBlockRow[$entityHLFieldsListIndex] );
                    }
                    else{
                        $arEntityHLFieldsList[$entityHLFieldsListIndex]["VALUE"] = $arHLBlockRow[$entityHLFieldsListIndex];
                    }
                }
            }
        }

        return $arEntityHLFieldsList;
    }

    // get product fields and properties used in template and conditions
    private function GetElementProperties( $arElement ){
        global $DB, $USER;

        if( !is_object( $USER ) )
            $USER = new CUser();

        $arItem = $arElement->GetFields();
        foreach( $arItem as $itemElementIndex => $itemElementValue ){
            if( isset( $arItem["~".$itemElementIndex] ) ){
                $arItem[$itemElementIndex] = $arItem["~".$itemElementIndex];
            }
        }

        if( in_array( "DETAIL_PICTURE", $this->useFields ) ){
            $arItem["DETAIL_PICTURE"] = CFile::GetPath($arItem["DETAIL_PICTURE"]);
        }
        if( in_array( "PREVIEW_PICTURE", $this->useFields ) ){
            $arItem["PREVIEW_PICTURE"] = CFile::GetPath( $arItem["PREVIEW_PICTURE"] );
        }

        foreach( $arItem as $key => &$value ){
            if( in_array( $key, $this->dateFields ) ){
                $value = date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), strtotime( $value ) );
            }
        }

        if( $this->profile["USE_IBLOCK_PRODUCT_CATEGORY"] == "Y" ){
            $arOfferIBlock = CCatalog::GetByID( $arItem["IBLOCK_ID"] );
            $linkPropertyToProduct = $arOfferIBlock["SKU_PROPERTY_ID"];
            $productIblockId = $arOfferIBlock["PRODUCT_IBLOCK_ID"];

            $arPropertyLinks = CAcritExportproplusTools::GetProperties( $arItem, array( "ID" => $linkPropertyToProduct ) );
                foreach( $arPropertyLinks as $propertyLinkCode => $arPropertyLinksItem ){
                    if( intval( $arPropertyLinksItem["VALUE"] ) > 0 ){
                    $dbProductItem = CIBlockElement::GetList(
                        array(),
                        array(
                            "IBLOCK_ID" => $productIblockId,
                            "ID" => $arPropertyLinksItem["VALUE"]
                        ),
                        false,
                        false,
                        array(
                            "ID",
                            "IBLOCK_ID",
                            "IBLOCK_SECTION_ID"
                        )
                    );

                    if( $arProductItem = $dbProductItem->GetNext() ){
                        $arItem["IBLOCK_PRODUCT_SECTION_ID"] = $arProductItem["IBLOCK_SECTION_ID"];
                    }
                }
            }
        }

        $arItem["SECTION_ID"] = array();
        $arItem["SECTION_EXDATA"] = array();
        $arItem["IBLOCK_SECTION_NAME"] = array();

        $dbSomeSections = CIBlockElement::GetElementGroups( $arItem["ID"], true );
        while( $arSection = $dbSomeSections->Fetch() ){
            if( in_array( "IBLOCK_SECTION_NAME", $this->useFields ) ){
                $arItem["IBLOCK_SECTION_NAME"] = $arSection["NAME"];
            }
            $arItem["SECTION_ID"][] = $arSection["ID"];
            $arItem["SECTION_EXDATA"][$arSection["ID"]] = $arSection;
        }

        $arItem["ALL_SECTION_ID"] = $arItem["SECTION_ID"];

        if( is_array( $arItem["SECTION_ID"] ) && !empty( $arItem["SECTION_ID"] ) && is_array( $this->profile["CATEGORY"] ) && !empty( $this->profile["CATEGORY"] ) ){
            $arSectionsToProcess = $arItem["SECTION_ID"];

            foreach( $arSectionsToProcess as $sectionIdIndex => $sectionId ){
                $dbProcessSection = CIBlockSection::GetNavChain( false, $sectionId );
                $bSectionSelected = false;
                while( $arProcessSection = $dbProcessSection->GetNext() ){
                    if( in_array( $arProcessSection["ID"], $this->profile["CATEGORY"] ) ){
                        $bSectionSelected = true;
                        break;
                    }
                }

                if( !$bSectionSelected ){
                    unset( $arItem["SECTION_ID"][$sectionIdIndex] );
                }
            }
        }

        $arItem["SECTION_PARENT_ID"] = array();
        $dbParentSection = CIBlockSection::GetNavChain( false, $arItem["IBLOCK_SECTION_ID"] );
        while( $arParentSection = $dbParentSection->GetNext() ){
            $arItem["SECTION_PARENT_ID"][] = $arParentSection["ID"];
        }

        $arSectionFilter = array(
            "IBLOCK_ID" => $arItem["IBLOCK_ID"],
            "ID" => $arItem["IBLOCK_SECTION_ID"],
        );

        $dbSectionList = CIBlockSection::GetList(
            array(),
            $arSectionFilter,
            false,
            array(
                "ID",
                "IBLOCK_ID",
                "IBLOCK_SECTION_ID",
                "NAME",
                "UF_*",
            )
        );

        $arSectionUserFields = CAcritExportproplusTools::GetIblockUserFields( $arItem["IBLOCK_ID"] );
        if( ( $arSectionList = $dbSectionList->GetNext() ) && is_array( $arSectionUserFields ) && !empty( $arSectionUserFields ) ){
            foreach( $arSectionUserFields as $arSectionUserFieldsItem ){
                if( in_array( $arSectionUserFieldsItem["FIELD_NAME"], $this->useFields ) ){
                    $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                    $value = $arSectionList[$arSectionUserFieldsItem["FIELD_NAME"]];
                    if( $this->GetResolveProperties( $arSectionUserFieldsItem, $arSectionUserFieldsItem["FIELD_NAME"], "FIELDS", $value ) ){
                        $arItem[$arSectionUserFieldsItem["FIELD_NAME"]] = $value;
                    }
                }
            }
        }

        if( count( $this->useProperties["ID"] ) ){
            $arProperties = CAcritExportproplusTools::GetProperties( $arItem, array( "ID" => $this->useProperties["ID"] ) );
            foreach( $this->useProperties["ID"] as $usePropID ){
                if( !isset( $arProperties[$usePropID] ) ){
                    $arItem["PROPERTY_{$usePropID}_VALUE"] = array();
                }
            }

            foreach( $arProperties as $property ){
                if( $property["USER_TYPE"] == "DateTime" ){
                    $property["DISPLAY_VALUE"] = date( str_replace( "_", " ", $this->profile["DATEFORMAT"] ), strtotime( $property["VALUE"] ) );
                }
                elseif( $property["PROPERTY_TYPE"] == "E" ){
                    $property["ORIGINAL_VALUE"] = array();
                    if( !empty( $property["VALUE"] ) ){
                        $dbPropE = CIBlockElement::GetList(
                            array(),
                            array(
                                "ID" => $property["VALUE"]
                            ),
                            false,
                            false,
                            array( "ID", "NAME" )
                        );
                        while( $arPropE = $dbPropE->GetNext() ){
                            $property["DISPLAY_VALUE"][] = $arPropE["NAME"];
                            $property["ORIGINAL_VALUE"][] = $arPropE["ID"];
                        }
                    }
                }
                elseif( $property["PROPERTY_TYPE"] == "G" ){
                    $property["ORIGINAL_VALUE"] = array();
                    if( !empty( $property["VALUE"] ) ){
                        $dbPropE = CIBlockSection::GetList(
                            array(),
                            array(
                                "ID" => $property["VALUE"]
                            ),
                            false,
                            array( "ID", "NAME" )
                        );
                        while( $arPropE = $dbPropE->GetNext() ){
                            $property["DISPLAY_VALUE"][] = $arPropE["NAME"];
                            $property["ORIGINAL_VALUE"][] = $arPropE["ID"];
                        }
                    }
                }
                elseif( $this->GetResolveProperties( $property, $property["ID"], "PROPERTIES" ) ){
                }
                else{
                    $property = CIBlockFormatProperties::GetDisplayValue( $arItem, $property, "acrit_exportproplus_event" );
                    if( empty( $property["VALUE_ENUM_ID"] ) ){
                        if( !is_array( $property["DISPLAY_VALUE"] ) )
                            $property["ORIGINAL_VALUE"] = array( $property["DISPLAY_VALUE"] );
                        else
                            $property["ORIGINAL_VALUE"] = $property["DISPLAY_VALUE"];
                    }
                    else{
                        if( !is_array( $property["VALUE_ENUM_ID"] ) )
                            $property["ORIGINAL_VALUE"] = array( $property["VALUE_ENUM_ID"] );
                        else
                            $property["ORIGINAL_VALUE"] = $property["VALUE_ENUM_ID"];
                    }
                }
                if( $property["PROPERTY_TYPE"] == "F" ){
                    $property["DISPLAY_VALUE"] = array();
                    if( count( $property["ORIGINAL_VALUE"] ) > 1 ){
                        if( is_array( $property["FILE_VALUE"] ) && !empty( $property["FILE_VALUE"] ) ){
                            foreach( $property["FILE_VALUE"] as $file ){
                                $property["DISPLAY_VALUE"][] = $file["SRC"];
                            }
                        }
                        elseif( is_array( $property["VALUE"] ) && !empty( $property["VALUE"] ) ){
                            foreach( $property["VALUE"] as $file ){
                                $property["DISPLAY_VALUE"][] = CFile::GetPath( $file );
                            }
                        }
                    }
                    else{
                        if( isset( $property["VALUE"] ) && !empty( $property["VALUE"] ) ){
                            $property["DISPLAY_VALUE"] = $property["FILE_VALUE"]["SRC"];
                        }
                        elseif( isset( $property["VALUE"] ) && !empty( $property["VALUE"] ) ){
                            $property["DISPLAY_VALUE"] = CFile::GetPath( $property["VALUE"] );
                        }
                    }
                }
                $arItem["PROPERTY_{$property["ID"]}_DISPLAY_VALUE"] = $property["DISPLAY_VALUE"];
                $arItem["PROPERTY_{$property["CODE"]}_DISPLAY_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
                $arItem["PROPERTY_{$property["ID"]}_VALUE"] = $property["ORIGINAL_VALUE"];
                $arItem["PROPERTY_{$property["CODE"]}_VALUE"] = $arItem["PROPERTY_{$property["ID"]}_VALUE"];
            }
        }
        if( $this->catalogIncluded ){
            $arProduct = CCatalogProduct::GetByID( $arItem["ID"] );

            $arItem["CATALOG_QUANTITY"] = $arProduct["QUANTITY"];
            $arItem["CATALOG_QUANTITY_RESERVED"] = $arProduct["QUANTITY_RESERVED"];
            $arItem["CATALOG_WEIGHT"] = $arProduct["WEIGHT"];
            $arItem["CATALOG_WIDTH"] = $arProduct["WIDTH"];
            $arItem["CATALOG_LENGTH"] = $arProduct["LENGTH"];
            $arItem["CATALOG_HEIGHT"] = $arProduct["HEIGHT"];

            $dbPrices = CPrice::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arItem["ID"]
                )
            );

            while( $arPrice = $dbPrices->Fetch() ){
                if( ( ( intval( $arPrice["QUANTITY_FROM"] ) > 0 ) || ( intval( $arPrice["QUANTITY_TO"] ) > 0 ) )
                    && ( intval( $arPrice["QUANTITY_FROM"] ) !== 1 )
                ){
                    continue;
                }

                if( in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_WD", $this->usePrices ) ||
                    in_array( "PRICE_".$arPrice["CATALOG_GROUP_ID"]."_D", $this->usePrices ) ){
                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice( $arPrice["ID"], $USER->GetUserGroupArray(), "N", $this->profile["LID"] );
                    $discountPrice = CCatalogProduct::CountPriceWithDiscount(
                        $arPrice["PRICE"],
                        $arPrice["CURRENCY"],
                        $arDiscounts
                    );
                    $discount = $arPrice["PRICE"] - $discountPrice;
                }
                else{
                    $discountPrice = $arPrice["PRICE"];
                    $discount = 0;
                }

                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_ID"] = $arPrice["ID"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD_ID"] = $arPrice["ID"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_PRICEID"] = $arPrice["CATALOG_GROUP_ID"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD_PRICEID"] = $arPrice["CATALOG_GROUP_ID"];
                //$arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD"] = $discountPrice;
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_D"] = $discount;
                $arItem["CATALOG_PRICE{$arPrice["CATALOG_GROUP_ID"]}"] = $arPrice["PRICE"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_WD_CURRENCY"] = $arPrice["CURRENCY"];
                $arItem["CATALOG_PRICE_{$arPrice["CATALOG_GROUP_ID"]}_CURRENCY"] = $arPrice["CURRENCY"];
            }

            if( !isset( $arItem["CATALOG_PRICE_{$this->basePriceId}"] ) && ( $this->profile["USE_AUTOPRICE"] == "Y" ) ){
                if( $arMinimalPrice = CCatalogProduct::GetOptimalPrice( $arItem["ID"], 1, array( 2 ), "N", array(), $this->profile["LID"], array() ) ){
                    $checkedPriceId = $arMinimalPrice["PRICE"]["CATALOG_GROUP_ID"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}"] = $arItem["CATALOG_PRICE_{$checkedPriceId}"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_ID"] = $arMinimalPrice["ID"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD_ID"] = $arMinimalPrice["ID"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_PRICEID"] = $this->basePriceId;
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD_PRICEID"] = $this->basePriceId;
                    //$arItem["CATALOG_PRICE_{$this->basePriceId}_WD"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_WD"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD"] = $arItem["CATALOG_PRICE_{$checkedPriceId}"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_D"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_D"];
                    $arItem["CATALOG_PRICE{$this->basePriceId}"] = $arItem["CATALOG_PRICE{$checkedPriceId}"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_WD_CURRENCY"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_CURRENCY"];
                    $arItem["CATALOG_PRICE_{$this->basePriceId}_CURRENCY"] = $arItem["CATALOG_PRICE_{$checkedPriceId}_CURRENCY"];
                }
            }

            if( in_array( "PURCHASING_PRICE", $this->usePrices ) ){
                $arItem["CATALOG_PURCHASING_PRICE"] = $arProduct["PURCHASING_PRICE"];
                $arItem["CATALOG_PURCHASING_PRICE_CURRENCY"] = $arProduct["PURCHASING_CURRENCY"];
            }

            $dbStoreProduct = CCatalogStoreProduct::GetList(
                array(),
                array(
                    "PRODUCT_ID" => $arProduct["ID"]
                ),
                false,
                false,
                array()
            );
            while( $arStore = $dbStoreProduct->Fetch() ){
                $arItem["CATALOG_STORE_AMOUNT_".$arStore["STORE_ID"]] = $arStore["AMOUNT"];
            }

            $dbBarCode = CCatalogStoreBarCode::getList(
                array(),
                array(
                    "PRODUCT_ID" => $arProduct["ID"]
                ),
                false,
                false,
                array()
            );
            if( $arBarCode = $dbBarCode->Fetch() ){
                $arItem["CATALOG_BARCODE"] = $arBarCode["BARCODE"];
            }
        }
        unset( $arProperties, $arProduct, $dbPrices, $arPrice );

        return $arItem;
    }

    protected function GetResolveProperties( &$item, $id, $type, &$value = "" ){
        if( ( $this->xmlCode === false ) || !isset( $this->useResolve[$this->xmlCode][$type][$id] ) ) return false;
        $resolve = $this->useResolve[$this->xmlCode][$type][$id];

        switch( $type ){
            case "PROPERTIES":
                if( ( $item["PROPERTY_TYPE"] == "S" ) && ( $item["USER_TYPE"] == "UserID" ) ){
                    $rsUser = CUser::GetByID( $item["VALUE"] );
                    $arUser = $rsUser->Fetch();
                    if( array_key_exists( $resolve, $arUser ) ){
                        $item["VALUE"] = $arUser[$resolve];
                        $item["~VALUE"] = $arUser[$resolve];
                        $item["DISPLAY_VALUE"] = $arUser[$resolve];
                        $item["ORIGINAL_VALUE"] = $arUser[$resolve];
                    }
                    return true;
                }
                break;
        }
    }

    protected function AddResolve(){
        foreach( $this->profile["XMLDATA"] as $xmlCode => $field ){
            if( !empty( $field["VALUE"] ) || !empty( $field["CONTVALUE_FALSE"] ) || !empty( $field["CONTVALUE_TRUE"] )
                || !empty( $field["COMPLEX_TRUE_VALUE"] ) || !empty( $field["COMPLEX_FALSE_VALUE"] )
                || !empty( $field["COMPLEX_TRUE_CONTVALUE"] ) || !empty( $field["COMPLEX_FALSE_CONTVALUE"] ) ){

                $fieldValue = ( $field["TYPE"] == "field" ) ? $field["VALUE"] : $field["COMPLEX_TRUE_VALUE"];
                $arValue = explode( "-", $fieldValue );
                switch( count( $arValue ) ){
                    case 1:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) > 0 ){
                            $this->useResolve[$xmlCode]["FIELDS"][$arValue[0]] = $field["RESOLVE"];
                        }
                        break;
                    case 2:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) ){
                            $this->useResolve[$xmlCode]["PRICES"][$arValue[1]] = $field["RESOLVE"];
                        }
                        break;
                    case 3:
                        if( !is_null( $field["RESOLVE"] ) && strlen( $field["RESOLVE"] ) ){
                            $this->useResolve[$xmlCode]["PROPERTIES"][$arValue[2]] = $field["RESOLVE"];
                        }
                        break;
                }
            }
        }
    }

    public function SetCronPage( $cronpage ){
        $this->cronpage = $cronpage;
    }

    public function SetProcessEnd( $fileExport ){
        global $ProcessEnd;
        $ProcessEnd = true;
    }

    public function SetProcessStart( $fileExport ){
        if( false === $fileExport ) return;
    }
}

class CAcritExportproplusComponent{
    private $__componentId = false;
    private $__path = false;
    private $__data = false;
    private $__siteDocRoot = false;
    private $__Class = false;

    private static $__components = array(
        "catalog" => "CAcritExportproplusRemarketingCatalog",
        "catalog.element" => "CAcritExportproplusRemarketingCatalogElement",
    );

    private static $__component_name = array(
        "catalog",
        "catalog.element"
    );

    function __construct(){
        $this->__siteDocRoot = \CSite::GetSiteDocRoot( SITE_ID );
    }

    public function execute(){
        if( $this->__path !== false ){
            global $APPLICATION;

            $__components = \PHPParser::ParseScript( $APPLICATION->GetFileContent( $this->__siteDocRoot.$this->__path ) );
            foreach( $__components as $component ){
                list( $__componentNamecpace, $__componentName ) = explode( ":", trim( $component["DATA"]["COMPONENT_NAME"] ) );
                if( !in_array( $__componentName, static::$__component_name ) )
                    continue;

                $this->__componentId = trim( $component["DATA"]["COMPONENT_NAME"] );

                if( !empty( $this->__data["DATA"]["PARAMS"] ) )
                    $component["DATA"]["PARAMS"] = array_merge_recursive( $component["DATA"]["PARAMS"], $this->__data["DATA"]["PARAMS"] );

                $Ob = $this->__initComponent( $component );
                if( !$Ob )
                    continue;

                $__component_result = call_user_func( array( $this->__Class, "execute" ), $Ob );

                if( !$__component_result )
                    continue;

                if( 0 < $__component_result["params"]["ELEMENT_ID"] )
                    return $__component_result;
            }
        }

        return false;
    }

    public function setPath( $path ){
        $this->__path = $path;

        return $this;
    }

    public function setData( $data ){
        $this->__data = array(
            "DATA" => array(
                "TEMPLATE_NAME" => $data["templateName"],
                "PARAMS" => $data["params"]
            )
        );

        return $this;
    }

    private function __initComponent( $component ){
        list( $__componentNamecpace, $__componentName ) = explode( ":", trim( $this->__componentId ) );
        $class_name = static::$__components[$__componentName];
        if( !class_exists( $class_name ) ){
            return false;
        }
        $this->__Class = static::$__components[$__componentName];

        $obComponent = new \CBitrixComponent();

        $obComponent->initComponent( $this->__componentId, $component["DATA"]["TEMPLATE_NAME"] );
        $obComponent->arParams = $component["DATA"]["PARAMS"];

        return $obComponent;
    }
}

class CAcritExportproplusGoogleTagManager{
    public static function OnEndBufferContent( &$bufferContent ){
				if(\Bitrix\Main\Config\Option::get('acrit.exportproplus', 'disable_old_core') == 'Y'){
					return;
				}
        if( defined( "ADMIN_SECTION" ) && ( ADMIN_SECTION == true ) )
            return;

        $obProfile = new CExportproplusProfileDB();
        $dbProfile = $obProfile->GetList(
            array(),
            array(
                "ACTIVE" => "Y",
                "USE_GOOGLETAGMANAGER" => "Y"
            )
        );

        while( $arProfileItem = $dbProfile->Fetch() ){
            $arProfile = $obProfile->GetByID( $arProfileItem["ID"] );

            if( strlen( trim( $arProfile["GOOGLETAGMANAGER_ID"] ) ) <= 0 ){
                continue;
            }

            $arSiteDomains = self::GetSiteDomains( $arProfile["LID"] );
            if( !in_array( $_SERVER["HTTP_HOST"], $arSiteDomains ) ){
                continue;
            }

            $arTagManagerTemplates = self::GetGoogleTagManagerTemplates( $arProfile["GOOGLETAGMANAGER_ID"] );

            foreach( $arTagManagerTemplates as $tagManagerTemplatesIndex => $tagManagerTemplatesItem ){
                if( !self::CheckInsertedTemplate( $tagManagerTemplatesIndex, $bufferContent ) ){
                    $bufferContent =  preg_replace( '/(<'.$tagManagerTemplatesIndex.'.*?>)/i', '$1'.PHP_EOL.$tagManagerTemplatesItem, $bufferContent );
                }
            }
        }
    }

    private static function CheckInsertedTemplate( $tagType, $buffer ){
        $result = false;

        if( $tagType == "head" ){
            if( stripos( $buffer, "www.googletagmanager.com/gtm.js" ) !== false ){
                $result = true;
            }
        }
        elseif( $tagType == "body" ){
            if( stripos( $buffer, "www.googletagmanager.com/ns.html" ) !== false ){
                $result = true;
            }
        }

        return $result;
    }

    private static function GetSiteDomains( $siteLid ){
        $arDomains = false;

        $dbSite = CSite::GetList(
            $by = "sort",
            $order = "asc",
            array(
                "ID" => $siteLid,
            )
        );

        while( $arSite = $dbSite->Fetch() ){
            $arDomains = explode( PHP_EOL, $arSite["DOMAINS"] );
        }

        return $arDomains;
    }

    private static function GetGoogleTagManagerTemplates( $tagManagerId ){
        $arTemplates = array(
            "head" =>
                "<!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','$tagManagerId');</script>
                <!-- End Google Tag Manager -->",
            "body" =>
                '<!-- Google Tag Manager (noscript) -->
                <noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$tagManagerId.'"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
                <!-- End Google Tag Manager (noscript) -->'
        );

        return  $arTemplates;
    }
}

class CAcritExportproplusRemarketing{
    public static function OnEndBufferContent( &$bufferContent ){
				if(\Bitrix\Main\Config\Option::get('acrit.exportproplus', 'disable_old_core') == 'Y'){
					return;
				}
        if( defined( "ADMIN_SECTION" ) && ( ADMIN_SECTION == true ) )
            return;

        $arVersion = explode( ".", phpversion() );
        $version = floatval( implode( ".", array( $arVersion[0], $arVersion[1] ) ) );
        if( $version < 5.4 )
            return;

        $profiles = array();

        $obProfile = new CExportproplusProfileDB();
        $dbProfile = $obProfile->GetList(
            array(),
            array(
                "ACTIVE" => "Y",
                "USE_REMARKETING" => "Y"
            )
        );

        while( $arProfileListItem = $dbProfile->Fetch() ){
            if( $arProfile = $obProfile->GetByID( $arProfileListItem["ID"] ) ){
                $profiles[$arProfile["ID"]] = $arProfile;
            }
        }

        if( !count( $profiles ) )
            return;

        $result = self::execute();

        if( !$result )
            return;

        if( ( !isset( $result["variables"]["SECTION_ID"] ) || ( $result["variables"]["SECTION_ID"] <= 0 ) ) ) {
            $rsElement = CIBlockElement::GetByID( $result["variables"]["ELEMENT_ID"] );
            if( $arElement = $rsElement->Fetch() ){
                $result["variables"]["SECTION_ID"] = $arElement["IBLOCK_SECTION_ID"];
                $result["params"]["SECTION_ID"] = $arElement["IBLOCK_SECTION_ID"];
            }
        }

        $arProfileCategory = array();
        foreach( $profiles as $profile ){
            self::setProfileCategory( $arProfileCategory, $profile, $result );
        }

        if( !count( $arProfileCategory ) )
            return;

        $jcode = self::getRemarketingTemplate( $arProfileCategory, $result );

        if( strlen( $jcode ) <= 0 )
            return;

        $arSettings = self::GetSettings();
        $tag = $arSettings["html_tag"]["name"];

        if( $arSettings["html_tag"]["position"] == "before" ){
            $bufferContent = str_replace( "<".$tag, $jcode.PHP_EOL."<".$tag, $bufferContent );
        }
        elseif( $arSettings["html_tag"]["position"] == "after" ){
            $bufferContent = str_replace( $tag.">", $tag.">".PHP_EOL.$jcode, $bufferContent );
        }
    }

    private static function GetSettings(){
        $arSettings = array(
            "html_tag" => array(
                "name" => "body",
                "position" => "after"
            )
        );

        $dbEvents = GetModuleEvents( "acrit.exportproplus", "OnGetRemarketingSettings" );
        while( $arEvent = $dbEvents->Fetch() ){
            ExecuteModuleEventEx( $arEvent, array( &$arSettings ) );
        }

        return $arSettings;
    }

    private static function setProfileCategory( &$arProfileCategory, $profile, $result ){
        $remarketing_type = self::getRemarketingType( $profile["TYPE"] );
        if( !$remarketing_type )
            return false;

        $arCategory = array();
        if( $profile["CHECK_INCLUDE"] != "Y" )
            $resultSection = CAcritExportproplusTools::GetSectionNavChain( $result["variables"]["SECTION_ID"] );

        foreach( $profile["CATEGORY"] as $category ){
            if( $profile["CHECK_INCLUDE"] != "Y" ){
                if( in_array( $category, $resultSection ) ){
                    if( in_array( $remarketing_type, $arProfileCategory ) )
                        continue;

                    $arProfileCategory[] = $remarketing_type;
                }
            }
            elseif( $profile["CHECK_INCLUDE"] == "Y" ){
                if( $category == $result["variables"]["SECTION_ID"] ){
                    if( in_array( $remarketing_type, $arProfileCategory ) )
                        continue;

                    $arProfileCategory[] = $remarketing_type;
                }
            }
        }
    }

    private static function getRemarketingType( $type = null, $profile = null ){
        switch( $type ) {
            case "google":
            case "google_online":
                return "google";
                break;
            case "mailru":
            case "mailru_clothing":
                return "mail";
                break;
        }

        return false;
    }

    private static function getRemarketingTemplate( $arProfileCategory, $result ){
        $return = "";
        $arTemplate = array(
            "google" => '
            <script type="text/javascript">
                var google_tag_params = {
                  ecomm_pagetype:\'product\',
                  ecomm_prodid:#ID#,
                };
            </script>',
            "mail" => '
            <script type="text/javascript">
            var _tmr = _tmr || [];
            _tmr.push({
                type: \'itemView\',
                productid:#ID#,
                pagetype:\'product\',
            });
            </script>'
        );

        $dbEvents = GetModuleEvents( "acrit.exportproplus", "OnGetRemarketingTemplate" );
        while( $arEvent = $dbEvents->Fetch() ){
            ExecuteModuleEventEx( $arEvent, array( &$arTemplate ) );
        }

        foreach( $arProfileCategory as $type ){
            if( array_key_exists( $type, $arTemplate ) )
                $return .= str_replace( array( "#ID#" ), array( $result["params"]["ELEMENT_ID"] ), $arTemplate[$type] ).PHP_EOL;
        }

        return  $return;
    }

    private static function _getJCodeType( $arTypes ){
        $return = array();
        foreach( $arTypes as $type ){
            $remarketing_type = self::getRemarketingType( $type );
            if( !$remarketing_type ) continue;
            $return[] = $remarketing_type;
        }

        return array_filter( $return );
    }

    private static function execute(){
        $componentsOnPage = CAcritExportproplusUrlRewrite::getInstance();
        $component = new CAcritExportproplusComponent();

        foreach( $componentsOnPage->getUrlRewrite() as $componentId => $componentVal ){
            if( ( $rule = $componentsOnPage->getRuleByComponentId( $componentId ) ) !== false ){
                $component->setPath( $rule["PATH"] );
                $result = $component->execute();

                if( $result !== false )
                    return $result;
            }
        }

        return false;
    }
}

class CAcritExportproplusRemarketingCatalog{
    protected static function __onPrepareComponentParams( &$params ){}

    protected static function __component( &$component ){
        if( $component->arParams["SEF_MODE"] == "Y" )
            $result = static::__sefMode( $component );
        else
            $result = static::__standartMode( $component );

        if( $result !== false ){
            if( $result["componentPage"] == "element" ){
                if( ( !isset($result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) ) && strlen( $result["variables"]["ELEMENT_CODE"] ) ){
                    $findFilter = array(
                        "IBLOCK_ID" => $component->arParams["IBLOCK_ID"],
                        "IBLOCK_LID" => SITE_ID,
                        "IBLOCK_ACTIVE" => "Y",
                        "ACTIVE_DATE" => "Y",
                        "CHECK_PERMISSIONS" => "Y",
                        "MIN_PERMISSION" => "R",
                    );

                    if( $component->arParams["SHOW_DEACTIVATED"] !== "Y" )
                        $findFilter["ACTIVE"] = "Y";

                    $result["variables"]["ELEMENT_ID"] = \CIBlockFindTools::GetElementID(
                        ( isset( $result["variables"]["ELEMENT_ID"] ) && strlen( $result["variables"]["ELEMENT_ID"] ) ) ? $result["variables"]["ELEMENT_ID"] : false,
                        $result["variables"]["ELEMENT_CODE"],
                        ( isset( $result["variables"]["SECTION_ID"] ) && strlen( $result["variables"]["SECTION_ID"] ) ) ? $result["variables"]["SECTION_ID"] : false,
                        ( isset( $result["variables"]["SECTION_CODE"] ) && strlen( $result["variables"]["SECTION_CODE"] ) ) ? $result["variables"]["SECTION_CODE"] : false,
                        $findFilter
                    );
                }

                if( !isset( $result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) )
                    return false;

                foreach( $result["variables"] as $code => $val )
                    $result["params"][$code] = $val;
            }
            else
                $result = false;
        }

        return $result;
    }

    public static function execute( \CBitrixComponent $component ){
        static::__onPrepareComponentParams( $component->arParams );
        $result = static::__component( $component );

        return $result;
    }

    public static function remarketingCheckPath4Template( $pageTemplate, $currentPageUrl, &$arVariables ){
        $pageTemplateReg = preg_replace( "'#[^#]+?#'", "([^/]+?)", $pageTemplate );

        if( substr( $pageTemplateReg, -1, 1 ) == "/" )
            $pageTemplateReg .= "index\\.php";

        $arValues = array();
        if( preg_match( "'^".$pageTemplateReg."$'", $currentPageUrl, $arValues ) ){
            $arMatches = array();
            if( preg_match_all( "'#([^#]+?)#'", $pageTemplate, $arMatches ) ){
                for( $i = 0, $cnt = count( $arMatches[1] ); $i < $cnt; $i++ )
                    $arVariables[$arMatches[1][$i]] = $arValues[$i + 1];
            }
            return true;
        }

        return false;
    }

    private static function __sefMode( &$component ){
        $arVariables = array();

        $smartBase = ( $component->arParams["SEF_URL_TEMPLATES"]["section"] ? $component->arParams["SEF_URL_TEMPLATES"]["section"] : "#SECTION_ID#/" );
        $arDefaultUrlTemplates404 = array(
            "sections" => "",
            "section" => "#SECTION_ID#/",
            "element" => "#SECTION_ID#/#ELEMENT_ID#/",
            "compare" => "compare.php?action=COMPARE",
            "smart_filter" => $smartBase."filter/#SMART_FILTER_PATH#/apply/"
        );

        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        );

        $engine = new \CComponentEngine( $component );
        if( \Bitrix\Main\Loader::includeModule( "iblock" ) ){
            $engine->addGreedyPart( "#SECTION_CODE_PATH#" );
            $engine->addGreedyPart( "#SMART_FILTER_PATH#" );
            $engine->setResolveCallback( array( "\CIBlockFindTools", "resolveComponentEngine" ) );
        }

        $arUrlTemplates = \CComponentEngine::MakeComponentUrlTemplates( $arDefaultUrlTemplates404, $component->arParams["SEF_URL_TEMPLATES"] );
        $arVariableAliases = \CComponentEngine::MakeComponentVariableAliases( $arDefaultVariableAliases404, $component->arParams["VARIABLE_ALIASES"] );

        $requestURL = Bitrix\Main\Context::getCurrent()->getRequest()->getRequestedPage();
        $currentPageUrl = substr( $requestURL, strlen( $component->arParams["SEF_FOLDER"] ) );

        $arVariablesTmp = array();
        foreach ($arUrlTemplates as $pageID => $pageTemplate){
            if( self::remarketingCheckPath4Template( $pageTemplate, $currentPageUrl, $arVariablesTmp ) ){
                if( strpos( $pageTemplate, "#" ) !== false ){
                    $pageCandidates[$pageID] = $arVariablesTmp;
                }
            }
        }

        if( array_key_exists( "element", ( is_array( $pageCandidates ) ? $pageCandidates : array() ) ) ){
            $componentPage = $engine->guessComponentPath(
                $component->arParams["SEF_FOLDER"],
                $arUrlTemplates,
                $arVariables
            );

            \CComponentEngine::InitComponentVariables( $componentPage, $arComponentVariables, $arVariableAliases, $arVariables );

            return array(
                "componentPage" => $componentPage,
                "variables" => $arVariables,
                "params" => $component->arParams
            );
        }
    }

    private static function __standartMode( &$component ){
        $arVariables = array();
        $arDefaultVariableAliases = array();
        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        );

        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases( $arDefaultVariableAliases, $component->arParams["VARIABLE_ALIASES"] );
        CComponentEngine::InitComponentVariables( false, $arComponentVariables, $arVariableAliases, $arVariables );

        $componentPage = "";
        $arCompareCommands = array(
            "COMPARE",
            "DELETE_FEATURE",
            "ADD_FEATURE",
            "DELETE_FROM_COMPARE_RESULT",
            "ADD_TO_COMPARE_RESULT",
            "COMPARE_BUY",
            "COMPARE_ADD2BASKET",
        );

        if( isset( $arVariables["action"] ) && in_array( $arVariables["action"], $arCompareCommands ) )
            $componentPage = "compare";
        elseif( isset( $arVariables["ELEMENT_ID"] ) && intval( $arVariables["ELEMENT_ID"] ) > 0 )
            $componentPage = "element";
        elseif( isset( $arVariables["ELEMENT_CODE"] ) && strlen( $arVariables["ELEMENT_CODE"] ) > 0 )
            $componentPage = "element";
        elseif( isset( $arVariables["SECTION_ID"] ) && intval( $arVariables["SECTION_ID"] ) > 0 )
            $componentPage = "section";
        elseif( isset( $arVariables["SECTION_CODE"] ) && strlen( $arVariables["SECTION_CODE"] ) > 0 )
            $componentPage = "section";
        elseif( isset( $_REQUEST["q"] ) )
            $componentPage = "search";
        else
            $componentPage = "sections";

        return array(
            "componentPage" => $componentPage,
            "variables" => $arVariables,
            "params" => $component->arParams
        );
    }
}

class CAcritExportproplusRemarketingCatalogElement{
    protected static function __onPrepareComponentParams( &$params ){}

    protected static function __component( &$component ){
        if( $component->arParams["SEF_MODE"] == "Y" )
            $result = static::__sefMode( $component );
        else
            $result = static::__standartMode( $component );

        if( $result !== false ){
            if( $result["componentPage"] == "element" ){
                if( ( !isset( $result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) ) && ( strlen( $result["variables"]["ELEMENT_CODE"] ) > 0 ) ){
                    $findFilter = array(
                        "IBLOCK_ID" => $component->arParams["IBLOCK_ID"],
                        "IBLOCK_LID" => SITE_ID,
                        "IBLOCK_ACTIVE" => "Y",
                        "ACTIVE_DATE" => "Y",
                        "CHECK_PERMISSIONS" => "Y",
                        "MIN_PERMISSION" => "R",
                    );

                    if( $component->arParams["SHOW_DEACTIVATED"] !== "Y" )
                        $findFilter["ACTIVE"] = "Y";

                    $result["variables"]["ELEMENT_ID"] = \CIBlockFindTools::GetElementID(
                        ( isset( $result["variables"]["ELEMENT_ID"] ) && strlen( $result["variables"]["ELEMENT_ID"] ) ) ? $result["variables"]["ELEMENT_ID"] : false,
                        $result["variables"]["ELEMENT_CODE"],
                        ( isset( $result["variables"]["SECTION_ID"] ) && strlen( $result["variables"]["SECTION_ID"] ) ) ? $result["variables"]["SECTION_ID"] : false,
                        ( isset( $result["variables"]["SECTION_CODE"] ) && strlen( $result["variables"]["SECTION_CODE"] ) ) ? $result["variables"]["SECTION_CODE"] : false,
                        $findFilter
                    );
                }

                if( !isset( $result["variables"]["ELEMENT_ID"] ) || ( $result["variables"]["ELEMENT_ID"] <= 0 ) )
                    return false;

                foreach( $result["variables"] as $code => $val )
                    $result["params"][$code] = $val;
            }
            else
                $result = false;
        }

        return $result;
    }

    public static function execute( \CBitrixComponent $component ){
        static::__onPrepareComponentParams( $component->arParams );
        $result = static::__component( $component );

        return $result;
    }

    private static function __sefMode( &$component ){
        return false;
    }

    private static function __standartMode( &$component ){
        $arVariables = array();
        $arComponentVariables = array(
            "SECTION_ID",
            "SECTION_CODE",
            "ELEMENT_ID",
            "ELEMENT_CODE",
            "action",
        );

        $arVariableAliases = array();
        CComponentEngine::InitComponentVariables( false, $arComponentVariables, $arVariableAliases, $arVariables );

        $componentPage = "";
        $arCompareCommands = array(
            "ADD_TO_COMPARE_LIST",
        );

        if( isset( $arVariables["action"] ) && in_array( $arVariables["action"], $arCompareCommands ) )
            $componentPage = "compare";
        elseif( isset( $arVariables["ELEMENT_ID"] ) && ( intval( $arVariables["ELEMENT_ID"] ) > 0 ) )
            $componentPage = "element";
        elseif( isset( $arVariables["ELEMENT_CODE"] ) && ( strlen( $arVariables["ELEMENT_CODE"] ) > 0 ) )
            $componentPage = "element";
        else
            $componentPage = false;

        if( !$componentPage )
            return false;

        return array(
            "componentPage" => $componentPage,
            "variables" => $arVariables,
            "params" => $component->arParams
        );
    }
}

class CAcritExportproplusExternApiTools{
    const CURL_ENCTYPE_APPLICATION = "application/x-www-form-urlencoded";

    public function GetCurlFilename( $fileName ){
        if( version_compare( PHP_VERSION, "5.6.0", "<" ) ){
            return "@".$fileName;
        }
        else{
            return new \CURLFile( $fileName );
        }
    }

    public function CurlPost( $url, $postData, $params = array() ){
        if( $url == "" )
            return false;

        $isHttps = ( strpos( $url, "https" ) === 0 );

        if( is_array( $cookiePostfix ) ){
            $cookieType = $cookiePostfix[0];
            $cookiePostfix = $cookiePostfix[1];
        }
        else
            $cookieType = ( empty( $cookiePostfix )? 0 : 1 );

        $cookieFile = "/upload/tmp/acrit.exportproplus/";
        if( $params["cookie_type"] and ( is_dir( $cookieFile ) or mkdir( $cookieFile, 0744, true ) ) ){
            if( empty( $params["cookie_postfix"] ) ){
                $cookieFile .= "cookie.txt";
            }
            else{
                $cookieFile .= "cookie_".$params["cookie_postfix"].".txt";
            }
        }
        else{
            $cookieFile .= "cookie.txt";
        }

        $c = curl_init( $url );
        if( $params["CUSTOM_REQUEST"] ){
            curl_setopt( $c, CURLOPT_CUSTOMREQUEST, $params["CUSTOM_REQUEST"] );
        }

        curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );

        if( !!$params["user_agent"] ){
            curl_setopt( $c, CURLOPT_USERAGENT, $params["user_agent"] );
        }

        if( $params["cookie_type"] == 1 ){
            curl_setopt( $c, CURLOPT_COOKIEFILE, $cookieFile );
            curl_setopt( $c, CURLOPT_COOKIEJAR, $cookieFile );
        }
        elseif( $params["cookie_type"] == 2 ){
            curl_setopt( $c, CURLOPT_COOKIEJAR, $cookieFile );
        }
        elseif( $params["cookie_type"] == 3 ){
            curl_setopt( $c, CURLOPT_COOKIEFILE, $cookieFile );
        }

        if( $isHttps ){
            curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt( $c, CURLOPT_SSL_VERIFYHOST, 0 );
        }

        if( !empty( $postData ) ){
            if( !$params["CUSTOM_REQUEST"] or ( $params["CUSTOM_REQUEST"] == "POST" ) ){
                curl_setopt( $c, CURLOPT_POST, true );
            }

            if( isset( $params["enctype"] ) && ( $params["enctype"] == self::CURL_ENCTYPE_APPLICATION ) ){
                $postData = http_build_query( $postData );
                curl_setopt( $c, CURLOPT_HTTPHEADER, array( "Content-Length: " . strlen( $postData ) ) );
            }
            curl_setopt( $c, CURLOPT_POSTFIELDS, $postData );
        }

        curl_setopt( $c, CURLOPT_TIMEOUT, $params["timeout"] );
        $res = curl_exec( $c );
        curl_close( $c );
        usleep( 500000 );

        return $res;
    }

    public function _CurlPost( $url, $postData, $cookiePostfix = "", $userAgent = false, $timeout = 120 ){
        $params = array();
        if( is_array( $cookiePostfix ) ){
            $params["cookie_type"] = $cookiePostfix[0] ?: $cookiePostfix["type"];
            $params["cookie_postfix"] = $cookiePostfix[1] ?: $cookiePostfix["postfix"];
            $params["cookies"] = $cookiePostfix["cookies"];
        }
        elseif( !empty( $cookiePostfix ) ){
            $params["cookie_type"] = 1;
            $params["cookie_postfix"] = $cookiePostfix;
        }

        $params["user_agent"] = $userAgent;
        $params["timeout"] = $timeout;

        return self::CurlPost( $url, $postData, $params );
    }

    public function PreparePostText( $inputText, $outputCharset = false, $bSave = false ){
        global $APPLICATION;

        $outputText = false;
        if( strlen( $inputText ) > 0 ){
            $outputCharset = ( $outputCharset ) ? $outputCharset : "UTF-8";
            $outputText = strip_tags( $inputText );
            if( $bSave ){
                $outputText = $APPLICATION->ConvertCharset( $outputText, $outputCharset, SITE_CHARSET );
            }
            else{
                $outputText = $APPLICATION->ConvertCharset( $outputText, SITE_CHARSET, $outputCharset );
            }
            $outputText = html_entity_decode( $outputText );
        }

        return $outputText;
    }

    public function GetAccessUrl(){
        $accessUrl = false;
        $accessUrl = "https://oauth.vk.com/authorize?client_id=".GetMessage( "ACRIT_VK_APPLICATION_ID" )."&scope=friends,wall,groups,offline,photos,video,market&redirect_uri=https://oauth.vk.com/blank.html&response_type=token&v=5.53&display=page";

        return $accessUrl;
    }
}

class CAcritExportproplusVkModel{
    public $profile = null;
    public $vkAccount = null;

    public function __construct( $profile ){
        global $APPLICATION;

        $this->iblockIncluded = @CModule::IncludeModule( "iblock" );

        $this->profile = $profile;

        $this->vkAccount = self::GetAccessAccountData();
    }

    public function GetAccessAccountData(){
        $arAccount = array(
            "ACCESS_TOKEN" => $this->profile["VK"]["VK_ACCESS_TOKEN"],
            "GROUP_PUBLISH" => $this->profile["VK"]["VK_GROUP_PUBLISH"],
            "USER_PUBLISH" => $this->profile["VK"]["VK_USER_PUBLISH"],
            "API_VERSION" => ( $this->profile["SITE_PROTOCOL"] != "https" ) ? GetMessage( "ACRIT_VK_API_HTTP_VERSION" ) : GetMessage( "ACRIT_VK_API_HTTPS_VERSION" ),
        );

        return $arAccount;
    }

    public function GetAccountInfoData( $arParams = false ){
        $accessUrl = "https://api.vk.com/method/account.getInfo";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];

        if( is_array( $arParams ) && !empty( $arParams ) ){
            foreach( $arParams as $paramIndex => $arParamsItem ){
                $postData[$paramIndex] = $arParamsItem;
            }
        }

        $responseGetAccountInfoData = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $accessUrl, $postData, false ), 1 );

        return $responseGetAccountInfoData;
    }

    public function GetMarketUploadServerUrl( $bMainPhoto = false ){
        $accessUrl = "https://api.vk.com/method/photos.getMarketUploadServer";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];
        if( $bMainPhoto ){
            $postData["main_photo"] = 1;
        }

        $responseGetMarketUploadServerUrl = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $accessUrl, $postData, false ), 1 );

        return $responseGetMarketUploadServerUrl["response"]["upload_url"];
    }

    public function PreparePhotoToSaveOnServer( $arFiles, $bMainPhoto = false, $bAlbum = false, $photoAlbumId = false, $bWallPhoto = false ){
        $responsePreparePhotoToSaveOnServer = false;
        if( is_array( $arFiles ) && !empty( $arFiles ) ){
            if( $photoAlbumId ){
                $resonseUrl = self::GetGroupUploadServerUrl( $photoAlbumId );
            }
            elseif( $bAlbum ){
                $resonseUrl = self::GetMarketAlbumUploadServerUrl();
            }
            elseif( $bWallPhoto ){
                $resonseUrl = self::GetWallUploadServerUrl();
            }
            else{
                $resonseUrl = self::GetMarketUploadServerUrl( $bMainPhoto );
            }

            if( is_array( $arFiles ) && !empty( $arFiles ) ){
                $arPreparedFiles = array();
                if( $bAlbum ){
                    $arPreparedFiles["file"] = CAcritExportproplusExternApiTools::GetCurlFilename( $arFiles[0] );
                }
                else{
                    if( $photoAlbumId ){
                        $iPhotoLimitIndex = 5;
                    }
                    elseif( $bWallPhoto ){
                        $iPhotoLimitIndex = 6;
                    }
                    else{
                        $iPhotoLimitIndex = 4;
                    }

                    foreach( $arFiles as $key => $fileName ){
                        if( count( $arPreparedFiles ) >= $iPhotoLimitIndex - 1 )
                            break;

                        $arPreparedFiles["file".( count( $arPreparedFiles ) + 1)] = CAcritExportproplusExternApiTools::GetCurlFilename( $fileName );
                    }
                }
            }
            $responsePreparePhotoToSaveOnServer = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $resonseUrl, $arPreparedFiles, false ), 1 );
        }

        return $responsePreparePhotoToSaveOnServer;
    }

    public function SaveMarketPhoto( $arFiles, $bMainPhoto = false ){
        $responseSaveMarketPhoto = false;
        if( is_array( $arFiles ) && !empty( $arFiles ) ){
            $arPreparedPhotos = self::PreparePhotoToSaveOnServer( $arFiles, $bMainPhoto );

            $saveUrl = "https://api.vk.com/method/photos.saveMarketPhoto";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];
            $postData["server"] = $arPreparedPhotos["server"];
            $postData["photo"] = $arPreparedPhotos["photo"];
            $postData["hash"] = $arPreparedPhotos["hash"];
            if( $bMainPhoto ){
                $postData["crop_data"] = $arPreparedPhotos["crop_data"];
                $postData["crop_hash"] = $arPreparedPhotos["crop_hash"];
            }
            $responseSaveMarketPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $saveUrl, $postData, false ), 1 );
        }

        return $responseSaveMarketPhoto;
    }

    public function GetMarketCategories(){
        global $APPLICATION;

        $getUrl = "https://api.vk.com/method/market.getCategories";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["count"] = 100;

        $responseGetMarketCategories = $APPLICATION->ConvertCharsetArray( json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 ), "UTF-8", SITE_CHARSET );

        return $responseGetMarketCategories;
    }

    public function GetMarketItems( $arParams = false ){
        global $APPLICATION;

        $accessUrl = "https://api.vk.com/method/market.get";
        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];

        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ALBUM_ID"] ) > 0 ) ){
            $postData["album_id"] = $arParams["ALBUM_ID"];
        }
        
        $responseGetMarketItems = $APPLICATION->ConvertCharsetArray( json_decode( CAcritExportproplusExternApiTools::_CurlPost( $accessUrl, $postData, false ), 1 ), "UTF-8", SITE_CHARSET );
        
        return $responseGetMarketItems;
    }

    public function GetMarketItemsById( $arProductIds, $bExtended = false ){
        $responseGetMarketItemsById = false;

        if( CAcritExportproplusTools::ArrayValidate( $arProductIds ) ){
            foreach( $arProductIds as $productIdIndex => $productId ){
                $arProductIds[$productIdIndex] = "-".$this->vkAccount["GROUP_PUBLISH"]."_".$productId;
            }
            $sProductIds = implode( ", ", $arProductIds );

            $getUrl = "https://api.vk.com/method/market.getById";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["item_ids"] = $sProductIds;
            if( $bExtended ){
                $postData["extended"] = 1;
            }
            $responseGetMarketItemsById = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 );
        }

        return $responseGetMarketItemsById;
    }

    public function AddMarketItem( $arProduct, $bEdit = false ){
        $responseAddMarketItem = false;
        if( is_array( $arProduct ) && !empty( $arProduct ) ){
            $arSavedMainPhoto = self::SaveMarketPhoto( $arProduct["PHOTO"], true );
            if( is_array( $arProduct["ADDITIONAL_PHOTOS"] ) && !empty( $arProduct["ADDITIONAL_PHOTOS"] ) ){
                $arSavedAdditionalPhotosTmp = self::SaveMarketPhoto( $arProduct["ADDITIONAL_PHOTOS"], false );
                if( is_array( $arSavedAdditionalPhotosTmp ) && !empty( $arSavedAdditionalPhotosTmp ) ){
                    $arSavedAdditionalPhotos = "";
                    foreach( $arSavedAdditionalPhotosTmp["response"] as $arPhotoItem ){
                        if( $arSavedAdditionalPhotos == "" ){
                            $arSavedAdditionalPhotos .= $arPhotoItem["id"];
                        }
                        else{
                            $arSavedAdditionalPhotos .= ", ".$arPhotoItem["id"];
                        }
                    }
                }
            }

            if( $bEdit ){
                $addUrl = "https://api.vk.com/method/market.edit";
            }
            else{
                $addUrl = "https://api.vk.com/method/market.add";
            }

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            if( $bEdit ){
                $postData["item_id"] = $arProduct["ID"];
            }
            $postData["name"] = CAcritExportproplusExternApiTools::PreparePostText( $arProduct["NAME"] );
            $postData["description"] = CAcritExportproplusExternApiTools::PreparePostText( $arProduct["URL_LABEL"] )."  ".$this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$arProduct["URL"]."\n\n".CAcritExportproplusExternApiTools::PreparePostText( $arProduct["DESCRIPTION"] );
            $postData["category_id"] = $arProduct["MARKET_CATEGORY"];

            $postData["price"] = $arProduct["PRICE"];
            $postData["deleted"] = $arProduct["IS_DELETED"];
            $postData["main_photo_id"] = $arSavedMainPhoto["response"][0]["id"];
            if( isset( $arSavedAdditionalPhotos ) && ( $arSavedAdditionalPhotos != "" ) ){
                $postData["photo_ids"] = $arSavedAdditionalPhotos;
            }

            $responseAddMarketItem = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $addUrl, $postData, false ), 1 );
        }

        return $responseAddMarketItem;
    }

    public function DeleteMarketItem( $productId ){
        $responseDeleteMarketItem = false;
        if( intval( $productId ) > 0 ){
            $deleteUrl = "https://api.vk.com/method/market.delete";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["item_id"] = $productId;

            $responseDeleteMarketItem = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $deleteUrl, $postData, false ), 1 );
        }

        return $responseDeleteMarketItem;
    }

    public function RestoreMarketItem( $productId ){
        $responseRestoreMarketItem = false;
        if( intval( $productId ) > 0 ){
            $restoreUrl = "https://api.vk.com/method/market.restore";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["item_id"] = $productId;

            $responseRestoreMarketItem = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $restoreUrl, $postData, false ), 1 );
        }

        return $responseRestoreMarketItem;
    }

    /*
    array $arReasonAvailables -
    0 - spam,
    1 - child pornography,
    2 - extremism,
    3 - violence,
    4 - drug promotion,
    5 - adult,
    6 - insult
    */

    public function ReportMarketItem( $productId, $reason ){
        $responseReportMarketItem = false;
        $arReasonAvailables = array( 0, 1, 2, 3, 4, 5, 6 );
        if( ( intval( $productId ) > 0 ) && in_array( $reason, $arReasonAvailables ) ){
            $reportUrl = "https://api.vk.com/method/market.report";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["item_id"] = $productId;
            $postData["reason"] = $reason;

            $responseReportMarketItem = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $reportUrl, $postData, false ), 1 );
        }

        return $responseReportMarketItem;
    }

    public function ReorderMarketItems( $arParams ){
        $responseReorderMarketItems = false;
        if( is_array( $arParams ) && !empty( $arParams ) ){
            $reorderUrl = "https://api.vk.com/method/market.reorderItems";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["item_id"] = $arParams["ID"];

            if( intval( $arParams["ALBUM_ID"] ) > 0 ){
                $postData["album_id"] = $arParams["ALBUM_ID"];
            }
            if( intval( $arParams["BEFORE_ID"] ) > 0 ){
                $postData["before"] = $arParams["BEFORE_ID"];
            }
            if( intval( $arParams["AFTER_ID"] ) > 0 ){
                $postData["after"] = $arParams["AFTER_ID"];
            }

            $responseReorderMarketItems = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $reorderUrl, $postData, false ), 1 );
        }

        return $responseReorderMarketItems;
    }

    public function AddMarketItemToAlbums( $arParams ){
        $responseAddMarketItemToAlbums = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) && ( strlen( $arParams["ALBUMS"] ) > 0 ) ){
            $addUrl = "https://api.vk.com/method/market.addToAlbum";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["item_id"] = $arParams["ID"];
            $postData["album_ids"] = $arParams["ALBUMS"];

            $responseAddMarketItemToAlbums = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $addUrl, $postData, false ), 1 );
        }

        return $responseAddMarketItemToAlbums;
    }

    public function RemoveMarketItemFromAlbums( $arParams ){
        $responseRemoveMarketItemFromAlbums = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) && ( strlen( $arParams["ALBUMS"] ) > 0 ) ){
            $removeUrl = "https://api.vk.com/method/market.removeFromAlbum";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["item_id"] = $arParams["ID"];
            $postData["album_ids"] = $arParams["ALBUMS"];

            $responseRemoveMarketItemFromAlbums = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $removeUrl, $postData, false ), 1 );
        }

        return $responseRemoveMarketItemFromAlbums;
    }

    /*
    array $arSortAvailables -
    0 - user sort,
    1 - product date create,
    2 - price,
    3 - popularity,
    */

    public function SearchMarketItems( $arParams ){
        $responseSearchMarketItems = false;
        if( is_array( $arParams ) && !empty( $arParams ) ){
            $arSortAvailables = array( 0, 1, 2, 3 );
            $searchUrl = "https://api.vk.com/method/market.search";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];

            if( intval( $arParams["ALBUM_ID"] ) > 0 ){
                $postData["album_id"] = $arParams["ALBUMS"];
            }

            if( strlen( $arParams["QUERY_STRING"] ) > 0 ){
                $postData["q"] = $arParams["QUERY_STRING"];
            }

            if( intval( $arParams["PRICE_FROM"] ) > 0 ){
                $postData["price_from"] = $arParams["PRICE_FROM"];
            }

            if( intval( $arParams["PRICE_TO"] ) > 0 ){
                $postData["price_to"] = $arParams["PRICE_TO"];
            }

            if( strlen( $arParams["TAGS"] ) > 0 ){
                $postData["tags"] = $arParams["TAGS"];
            }

            if( ( intval( $arParams["SORT"] ) > 0 ) && in_array( $arParams["SORT"], $arSortAvailables ) ){
                $postData["sort"] = $arParams["SORT"];
            }

            if( !$arParams["REV"] ){
                $postData["rev"] = 0;
            }

            $postData["count"] = ( intval( $arParams["COUNT"] ) > 0 ) ? $arParams["COUNT"] : 100;

            if( $arParams["EXTENDED"] ){
                $postData["extended"] = 1;
            }

            $responseSearchMarketItems = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $searchUrl, $postData, false ), 1 );
        }

        return $responseSearchMarketItems;
    }

    public function GetMarketAlbumUploadServerUrl(){
        $accessUrl = "https://api.vk.com/method/photos.getMarketAlbumUploadServer";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];

        $responseGetMarketAlbumUploadServerUrl = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $accessUrl, $postData, false ), 1 );

        return $responseGetMarketAlbumUploadServerUrl["response"]["upload_url"];
    }

    public function SaveMarketAlbumPhoto( $arFiles ){
        $responseSaveMarketAlbumPhoto = false;
        if( is_array( $arFiles ) && !empty( $arFiles ) ){
            $arPreparedPhotos = self::PreparePhotoToSaveOnServer( $arFiles, false, true );

            $saveUrl = "https://api.vk.com/method/photos.saveMarketAlbumPhoto";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];
            $postData["server"] = $arPreparedPhotos["server"];
            $postData["photo"] = $arPreparedPhotos["photo"];
            $postData["hash"] = $arPreparedPhotos["hash"];

            $responseSaveMarketAlbumPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $saveUrl, $postData, false ), 1 );
        }

        return $responseSaveMarketAlbumPhoto;
    }

    public function GetMarketAlbums(){
        global $APPLICATION;

        $getUrl = "https://api.vk.com/method/market.getAlbums";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
        $postData["count"] = 100;

        $responseGetMarketAlbums = $APPLICATION->ConvertCharsetArray( json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 ), "UTF-8", SITE_CHARSET );

        return $responseGetMarketAlbums;
    }

    public function GetMarketAlbumsById( $sAlbumIds ){
        $responseGetMarketAlbumsById = false;
        if( trim( strlen( $sAlbumIds ) ) > 0 ){
            $getUrl = "https://api.vk.com/method/market.getAlbumById";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["album_ids"] = $sAlbumIds;
            $responseGetMarketAlbumsById = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 );
        }

        return $responseGetMarketAlbumsById;
    }

    public function AddMarketAlbum( $arAlbum, $bMainAlbum = false, $bEdit = false ){
        global $APPLICATION;

        $responseAddMarketAlbum = false;
        if( is_array( $arAlbum ) && !empty( $arAlbum ) ){
            if( $bEdit ){
                $addUrl = "https://api.vk.com/method/market.editAlbum";
            }
            else{
                $addUrl = "https://api.vk.com/method/market.addAlbum";
            }

            if( is_array( $arAlbum["PICTURE"] ) && !empty( $arAlbum["PICTURE"] ) ){
                $arSavedPhoto = self::SaveMarketAlbumPhoto( $arAlbum["PICTURE"] );
            }

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            if( $bEdit ){
                $postData["album_id"] = $arAlbum["ID"];
            }
            $postData["title"] = CAcritExportproplusExternApiTools::PreparePostText( $arAlbum["NAME"] );
            if( intval( $arSavedPhoto["response"][0]["id"] ) > 0 ){
                $postData["photo_id"] = $arSavedPhoto["response"][0]["id"];
            }
            if( $bMainAlbum ){
                $postData["main_album"] = 1;
            }
            
            $responseAddMarketAlbum = $APPLICATION->ConvertCharsetArray( json_decode( CAcritExportproplusExternApiTools::_CurlPost( $addUrl, $postData, false ), 1 ), "UTF-8", SITE_CHARSET );
        }

        return $responseAddMarketAlbum;
    }

    public function DeleteMarketAlbum( $albumId, $bMainAlbum = false ){
        global $APPLICATION;

        $responseDeleteMarketAlbum = false;
        if( intval( $albumId ) > 0 ){
            $deleteUrl = "https://api.vk.com/method/market.deleteAlbum";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["album_id"] = $albumId;

            $responseDeleteMarketAlbum = $APPLICATION->ConvertCharsetArray( json_decode( CAcritExportproplusExternApiTools::_CurlPost( $deleteUrl, $postData, false ), 1 ), "UTF-8", SITE_CHARSET );
        }

        return $responseDeleteMarketAlbum;
    }

    public function ReorderMarketAlbums( $arParams ){
        $responseReorderMarketAlbums = false;
        if( is_array( $arParams ) && !empty( $arParams ) ){
            $reorderUrl = "https://api.vk.com/method/market.reorderAlbums";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["album_id"] = $arParams["ID"];

            if( intval( $arParams["BEFORE_ID"] ) > 0 ){
                $postData["before"] = $arParams["BEFORE_ID"];
            }
            if( intval( $arParams["AFTER_ID"] ) > 0 ){
                $postData["after"] = $arParams["AFTER_ID"];
            }

            $responseReorderMarketAlbums = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $reorderUrl, $postData, false ), 1 );
        }

        return $responseReorderMarketAlbums;
    }

    //group albums
    public function GetGroupUploadServerUrl( $albumId ){
        $responseGetGroupUploadServerUrl = false;
        if( intval( $albumId ) > 0 ){
            $getUrl = "https://api.vk.com/method/photos.getUploadServer";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];
            $postData["album_id"] = $albumId;

            $responseGetGroupUploadServerUrl = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 );
        }

        return $responseGetGroupUploadServerUrl["response"]["upload_url"];
    }

    public function GetGroupAlbums( $arParams = false ){
        global $APPLICATION;
                                                       
        $getUrl = "https://api.vk.com/method/photos.getAlbums";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];

        if( strlen( $arParams["ALBUMS"] ) > 0 ){
            $postData["album_ids"] = $arParams["ALBUMS"];
        }

        $postData["need_system"] = 0; //not need system
        $postData["need_covers"] = 1; //album cover image
        $postData["photo_sizes"] = 1; //get photo sizes
              
        $responseGetGroupAlbums = $APPLICATION->ConvertCharsetArray( json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 ), "UTF-8", SITE_CHARSET );

        return $responseGetGroupAlbums;
    }

    public function GetGroupAlbumsCount(){
        $getUrl = "https://api.vk.com/method/photos.getAlbumsCount";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];

        $responseGetGroupAlbumsCount = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 );

        return $responseGetGroupAlbumsCount;
    }

    public function CreateGroupAlbum( $arParams, $bEdit = false ){
        $responseCreateGroupAlbum = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( strlen( $arParams["TITLE"] ) > 0 ) && ( !$bEdit || ( $bEdit && ( intval( $arParams["ID"] ) > 0 ) ) ) ){
            if( $bEdit ){
                $createUrl = "https://api.vk.com/method/photos.editAlbum";
            }
            else{
                $createUrl = "https://api.vk.com/method/photos.createAlbum";
            }

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];

            if( $bEdit ){
                $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
                $postData["album_id"] = $arParams["ID"];
            }
            else{
                $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];
            }

            $postData["title"] = CAcritExportproplusExternApiTools::PreparePostText( $arParams["TITLE"] );
            if( strlen( $arParams["DESCRIPTION"] ) > 0 ){
                $postData["description"] = CAcritExportproplusExternApiTools::PreparePostText( $arParams["DESCRIPTION"] );
            }
            $postData["upload_by_admins_only"] = 1; //only for redactors & admins
            $postData["comments_disabled"] = 0; //has comments

            $responseCreateGroupAlbum = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $createUrl, $postData, false ), 1 );
        }

        return $responseCreateGroupAlbum;
    }

    public function DeleteGroupAlbum( $arParams ){
        $responseDeleteGroupAlbum = false;
        
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) ){
            $deleteUrl = "https://api.vk.com/method/photos.deleteAlbum";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];
            $postData["album_id"] = $arParams["ID"];
            
            $responseDeleteGroupAlbum = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $deleteUrl, $postData, false ), 1 );
        }

        return $responseDeleteGroupAlbum;
    }

    public function ReorderGroupAlbums( $arParams ){
        $responseReorderGroupAlbums = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) ){
            $reorderUrl = "https://api.vk.com/method/photos.reorderAlbums";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["album_id"] = $arParams["ID"];

            if( intval( $arParams["BEFORE_ID"] ) > 0 ){
                $postData["before"] = $arParams["BEFORE_ID"];
            }
            if( intval( $arParams["AFTER_ID"] ) > 0 ){
                $postData["after"] = $arParams["AFTER_ID"];
            }

            $responseReorderGroupAlbums = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $reorderUrl, $postData, false ), 1 );
        }

        return $responseReorderGroupAlbums;
    }

    public function GetGroupPhotos( $arParams ){
        $responseGetGroupPhotos = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ALBUM_ID"] ) > 0 ) ){
            $getUrl = "https://api.vk.com/method/photos.get";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["album_id"] = $arParams["ALBUM_ID"];
            $postData["rev"] = 0; // chronological

            if( $arParams["EXTENDED"] ){
                $postData["extended"] = 1;
            }

            $postData["photo_sizes"] = 1; // get sizes
            $postData["count"] = 100;

            $responseGetGroupPhotos = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 );
        }

        return $responseGetGroupPhotos;
    }

    public function GetGroupPhotosById( $arParams ){
        $responseGetGroupPhotosById = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( strlen( $arParams["PHOTOS"] ) > 0 ) ){
            $getUrl = "https://api.vk.com/method/photos.getById";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["photos"] = $arParams["PHOTOS"];

            if( $arParams["EXTENDED"] ){
                $postData["extended"] = 1;
            }

            $postData["photo_sizes"] = 1; // get sizes

            $responseGetGroupPhotosById = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 );
        }

        return $responseGetGroupPhotosById;
    }

    public function AddGroupPhoto( $arParams ){
        $responseAddGroupPhoto = false;
        if( is_array( $arParams ) && !empty( $arParams ) && is_array( $arParams["FILES"] ) && !empty( $arParams["FILES"] ) && ( strlen( $arParams["ALBUM_ID"] ) > 0 ) ){
            $saveUrl = "https://api.vk.com/method/photos.save";

            $responseAddGroupPhoto = array();
            foreach( $arParams["FILES"] as $arFilesItem ){
                $arPreparedPhotos = self::PreparePhotoToSaveOnServer( $arFilesItem["PHOTO"], false, false, $arParams["ALBUM_ID"] );

                $postData = array();
                $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
                $postData["v"] = $this->vkAccount["API_VERSION"];
                $postData["group_id"] = $this->vkAccount["GROUP_PUBLISH"];
                $postData["server"] = $arPreparedPhotos["server"];
                $postData["album_id"] = $arParams["ALBUM_ID"];
                $postData["photos_list"] = $arPreparedPhotos["photos_list"];
                $postData["hash"] = $arPreparedPhotos["hash"];
                $postData["caption"] = CAcritExportproplusExternApiTools::PreparePostText( $arParams["URL_LABEL"] ).$this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$arFilesItem["URL"]."\n\n".CAcritExportproplusExternApiTools::PreparePostText( $arFilesItem["DESCRIPTION"] );

                $responseAddGroupPhoto[] = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $saveUrl, $postData, false ), 1 );
            }
        }

        return $responseAddGroupPhoto;
    }

    public function EditGroupPhoto( $arParams ){
        $responseEditGroupPhoto = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) ){
            $editUrl = "https://api.vk.com/method/photos.edit";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["photo_id"] = $arParams["ID"];
            $postData["caption"] = CAcritExportproplusExternApiTools::PreparePostText( $arParams["URL_LABEL"] ).$this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$arParams["URL"]."\n\n".CAcritExportproplusExternApiTools::PreparePostText( $arParams["DESCRIPTION"] );

            $responseEditGroupPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $editUrl, $postData, false ), 1 );
        }

        return $responseEditGroupPhoto;
    }

    public function DeleteGroupPhoto( $arParams ){
        $responseDeleteGroupPhoto = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) ){
            $deleteUrl = "https://api.vk.com/method/photos.delete";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["photo_id"] = $arParams["ID"];

            $responseDeleteGroupPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $deleteUrl, $postData, false ), 1 );
        }

        return $responseDeleteGroupPhoto;
    }

    public function RestoreGroupPhoto( $arParams ){
        $responseRestoreGroupPhoto = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) ){
            $restoreUrl = "https://api.vk.com/method/photos.restore";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["photo_id"] = $arParams["ID"];

            $responseRestoreGroupPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $restoreUrl, $postData, false ), 1 );
        }

        return $responseRestoreGroupPhoto;
    }

    public function MakeCoverGroupPhoto( $arParams ){
        $responseMakeCoverGroupPhoto = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) && ( intval( $arParams["ALBUM_ID"] ) > 0 ) ){
            $makeUrl = "https://api.vk.com/method/photos.makeCover";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["photo_id"] = $arParams["ID"];
            $postData["album_id"] = $arParams["ALBUM_ID"];

            $responseMakeCoverGroupPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $makeUrl, $postData, false ), 1 );
        }

        return $responseMakeCoverGroupPhoto;
    }

    public function MoveGroupPhoto( $arParams ){
        $responseMoveGroupPhoto = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) && ( intval( $arParams["ALBUM_ID"] ) > 0 ) ){
            $moveUrl = "https://api.vk.com/method/photos.move";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["photo_id"] = $arParams["ID"];
            $postData["target_album_id"] = $arParams["ALBUM_ID"];

            $responseMoveGroupPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $moveUrl, $postData, false ), 1 );
        }

        return $responseMoveGroupPhoto;
    }

    public function ReorderGroupPhotosInAlbum( $arParams ){
        $responseReorderGroupPhotosInAlbum = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( intval( $arParams["ID"] ) > 0 ) ){
            $reorderUrl = "https://api.vk.com/method/photos.reorderPhotos";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["photo_id"] = $arParams["ID"];

            if( intval( $arParams["BEFORE_ID"] ) > 0 ){
                $postData["before"] = $arParams["BEFORE_ID"];
            }
            if( intval( $arParams["AFTER_ID"] ) > 0 ){
                $postData["after"] = $arParams["AFTER_ID"];
            }

            $responseReorderGroupPhotosInAlbum = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $reorderUrl, $postData, false ), 1 );
        }

        return $responseReorderGroupPhotosInAlbum;
    }

    public function SearchGroupPhotos( $arParams ){
        global $APPLICATION;

        $responseSearchGroupPhotos = false;
        if( is_array( $arParams ) && !empty( $arParams ) && ( strlen( $arParams["QUERY_STRING"] ) > 0 ) ){
            $searchUrl = "https://api.vk.com/method/photos.search";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = ( ( intval( $this->vkAccount["GROUP_PUBLISH"] ) > 0 ) ? "-" : "" ).$this->vkAccount["GROUP_PUBLISH"];
            $postData["q"] = $arParams["QUERY_STRING"];
            $postData["sort"] = 0; // by date

            $responseSearchGroupPhotos = $APPLICATION->ConvertCharsetArray( json_decode( CAcritExportproplusExternApiTools::_CurlPost( $searchUrl, $postData, false ), 1 ), "UTF-8", SITE_CHARSET );
        }

        return $responseSearchGroupPhotos;
    }

    //user wall actions
    public function GetWallUploadServerUrl(){
        $accessUrl = "https://api.vk.com/method/photos.getWallUploadServer";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["group_id"] = $this->vkAccount["USER_PUBLISH"];

        $responseGetWallUploadServerUrl = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $accessUrl, $postData, false ), 1 );

        return $responseGetWallUploadServerUrl["response"]["upload_url"];
    }

    public function SaveWallPhoto( $arFiles ){
        $responseSaveWallPhoto = false;
        if( is_array( $arFiles ) && !empty( $arFiles ) ){
            $arPreparedPhotos = self::PreparePhotoToSaveOnServer( $arFiles, false, false, false, true );

            $saveUrl = "https://api.vk.com/method/photos.saveWallPhoto";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["group_id"] = $this->vkAccount["USER_PUBLISH"];
            $postData["server"] = $arPreparedPhotos["server"];
            $postData["photo"] = $arPreparedPhotos["photo"];
            $postData["hash"] = $arPreparedPhotos["hash"];

            $responseSaveWallPhoto = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $saveUrl, $postData, false ), 1 );
        }

        return $responseSaveWallPhoto;
    }

    public function GetWallItemsById( $arProductIds, $bExtended = false ){
        $responseGetWallItemsById = false;

        $arPreparedPosts = array();
        foreach( $arProductIds as $productId ){
            $arPreparedPosts[] = $this->vkAccount["USER_PUBLISH"]."_".$productId;
        }

        $sProductIds = implode( ",", $arPreparedPosts );

        $getUrl = "https://api.vk.com/method/wall.getById";

        $postData = array();
        $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
        $postData["v"] = $this->vkAccount["API_VERSION"];
        $postData["posts"] = $sProductIds;

        $responseGetWallItemsById = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $getUrl, $postData, false ), 1 );

        return $responseGetWallItemsById;
    }

    public function AddWallItem( $arProduct, $bEdit = false ){
        $responseAddWallItem = false;
        if( is_array( $arProduct ) && !empty( $arProduct ) ){
            $arSavedPhotosTmp = self::SaveWallPhoto( $arProduct["PHOTO"] );

            if( is_array( $arSavedPhotosTmp ) && !empty( $arSavedPhotosTmp ) ){
                $arSavedPhotos = array();
                foreach( $arSavedPhotosTmp["response"] as $arPhotoItem ){
                    $arSavedPhotos[] = "photo".$this->vkAccount["USER_PUBLISH"]."_".$arPhotoItem["id"];
                }
                $sSavedPhotos = implode( ",", $arSavedPhotos );
            }

            if( $bEdit ){
                $addUrl = "https://api.vk.com/method/wall.edit";
            }
            else{
                $addUrl = "https://api.vk.com/method/wall.post";
            }

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = $this->vkAccount["USER_PUBLISH"];
            $postData["friends_only"] = 0;
            $postData["from_group"] = 0;
            $postData["signed"] = 0;
            $postData["mark_as_ads"] = 0;
            if( $bEdit ){
                $postData["post_id"] = $arProduct["ID"];
            }
            $postData["message"] = CAcritExportproplusExternApiTools::PreparePostText( $arProduct["URL_LABEL"] ).$this->profile["SITE_PROTOCOL"]."://".$this->profile["DOMAIN_NAME"].$arProduct["URL"]."\n\n".CAcritExportproplusExternApiTools::PreparePostText( $arProduct["DESCRIPTION"] );
            $postData["attachments"] = $sSavedPhotos;

            $responseAddWallItem = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $addUrl, $postData, false ), 1 );
        }

        return $responseAddWallItem;
    }

    public function DeleteWallItem( $productId ){
        $responseDeleteWallItem = false;
        if( intval( $productId ) > 0 ){
            $deleteUrl = "https://api.vk.com/method/wall.delete";

            $postData = array();
            $postData["access_token"] = $this->vkAccount["ACCESS_TOKEN"];
            $postData["v"] = $this->vkAccount["API_VERSION"];
            $postData["owner_id"] = $this->vkAccount["USER_PUBLISH"];
            $postData["post_id"] = $productId;

            $responseDeleteWallItem = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $deleteUrl, $postData, false ), 1 );
        }

        return $responseDeleteWallItem;
    }
}

class CAcritExportproplusFb{
    public $profile = null;
    public $fbAccount = null;
    public $log;
    public $dbProfile = null;

    public function __construct( $profile ){
        global $APPLICATION;
				
				require_once( "classes/general/Facebook/autoload.php" );
				require_once( "classes/general/FacebookAds/autoload.php" );

        $this->dbProfile = new CExportproplusProfileDB();
        $this->iblockIncluded = @CModule::IncludeModule( "iblock" );
        $this->profile = $profile;
        $this->fbAccount = self::GetAccessAccountData();
        $this->log = new CAcritExportproplusLog( $this->profile["ID"] );
    }

    public function GetAccessAccountData(){
        $arAccount = array(
            "PAGE_PUBLISH" => $this->profile["FB"]["FB_PAGE_PUBLISH"],
            "APP_ID" => $this->profile["FB"]["FB_APP_ID"],
            "APP_SECRET" => $this->profile["FB"]["FB_APP_SECRET"],
            "ACCESS_TOKEN" => $this->profile["FB"]["FB_ACCESS_TOKEN"],
        );

        return $arAccount;
    }

    function CheckDataAndAccess( $arData, $obFacebook ){
        $bAccessStatus = false;

        try{
            $result = $obFacebook->get( "/".$this->fbAccount["PAGE_PUBLISH"]."?fields=access_token" )->getGraphObject()->asArray();

            if( isset( $result["access_token"] ) ){
                $obFacebook->setDefaultAccessToken( $result["access_token"] );
                $bAccessStatus = true;
            }
        }
        catch( Facebook\Exceptions\FacebookResponseException $e ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#FB_ERROR#", "CODE: ".$e, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_FB_SKIP" ) ) );
            $this->log->IncProductError();
        }
        catch( Facebook\Exceptions\FacebookSDKException $e ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#FB_ERROR#", "CODE: ".$e, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_FB_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $bAccessStatus;
    }

    function PreparePostData( $arData ){
        global $APPLICATION;

        $postData = array();

        $postData["access_token"] = $this->fbAccount["ACCESS_TOKEN"];

        $postData["message"] = $arData["MESSAGE"];
        $postData["message"] = strip_tags( $postData["message"] );
        $postData["message"] = $APPLICATION->ConvertCharset( $postData["message"], SITE_CHARSET, "UTF-8" );
        $postData["message"] = trim( html_entity_decode( $postData["message"] ) );

        if( empty( $postData["message"] ) )
            unset( $postData["message"] );

        $postData["link"] = $arData["URL"];
        $postData["picture"] = $arData["PHOTO"];

        $postData["name"] = $arData["NAME"];
        $postData["name"] = strip_tags( $postData["name"] );
        $postData["name"] = $APPLICATION->ConvertCharset( $postData["name"], SITE_CHARSET, "UTF-8" );
        $postData["name"] = html_entity_decode( $postData["name"] );
        $postData["name"] = substr( $postData["name"], 0, 255 );

        $postData["description"] = $arData["DESCRIPTION"];
        $postData["description"] = strip_tags( $postData["description"] );
        $postData["description"] = $APPLICATION->ConvertCharset( $postData["description"], SITE_CHARSET, "UTF-8" );
        $postData["description"] = html_entity_decode( $postData["description"] );

        return $postData;
    }

    function ProcessData( $arData ){
        $fb = new Facebook\Facebook(
            array(
                "app_id" => $this->fbAccount["APP_ID"],
                "app_secret" => $this->fbAccount["APP_SECRET"],
                "default_graph_version" => "v2.8",
                "default_access_token" => $this->fbAccount["ACCESS_TOKEN"],
            )
        );

        $arPostData = self::PreparePostData( $arData );

        $arProductRelations = $this->profile["FB"]["FB_RELATIONS"];
        $fbMarketItemId = false;

        if( $this->profile["FB"]["FB_RELATIONS"] != null ){
            $fbMarketItemId = isset( $arProductRelations[$arData["ID"]] ) ? $arProductRelations[$arData["ID"]] : false;
        }

        if( $fbMarketItemId ){
            $arUpdateMarketItemData = $arData;
            $arUpdateMarketItemData["ID"] = $fbMarketItemId;

            try{
                $rs = $fb->post( "/".$arData["ID"], $arPostData )->getDecodedBody();
            }
            catch( Facebook\Exceptions\FacebookResponseException $e ){
                $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#FB_ERROR#", "CODE: ".$e, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_FB_SKIP" ) ) );
                $this->log->IncProductError();
            }
            catch( Facebook\Exceptions\FacebookSDKException $e ){
                $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#FB_ERROR#", "CODE: ".$e, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_FB_SKIP" ) ) );
                $this->log->IncProductError();
            }
        }
        else{
            if( $this->profile["FB"]["FB_RELATIONS"] == null ){
                $this->profile["FB"]["FB_RELATIONS"] = array();
            }

            try{
                $rs = $fb->post( "/".$this->fbAccount["PAGE_PUBLISH"]."/feed", $arPostData )->getDecodedBody();
                $this->profile["FB"]["FB_RELATIONS"][$arData["ID"]] = $rs["id"];

                $this->dbProfile->Update( $this->profile["ID"], $this->profile );
            }
            catch( Facebook\Exceptions\FacebookResponseException $e ){
                $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#FB_ERROR#", "CODE: ".$e, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_FB_SKIP" ) ) );
                $this->log->IncProductError();
            }
            catch( Facebook\Exceptions\FacebookSDKException $e ){
                $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#FB_ERROR#", "CODE: ".$e, GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_FB_SKIP" ) ) );
                $this->log->IncProductError();
            }
        }
    }
}

class CAcritExportproplusOk{
    public $profile = null;
    public $okAccount = null;
    public $log;
    public $dbProfile = null;

    public function __construct( $profile ){
        $this->dbProfile = new CExportproplusProfileDB();
        $this->iblockIncluded = @CModule::IncludeModule( "iblock" );
        $this->profile = $profile;
        $this->okAccount = self::GetAccessAccountData();
        $this->log = new CAcritExportproplusLog( $this->profile["ID"] );

        \OdnoklassnikiSDK::init(
            $this->okAccount["APP_ID"],
            $this->okAccount["APP_PUBLIC_KEY"],
            $this->okAccount["APP_SECRET_KEY"],
            $this->okAccount["ACCESS_TOKEN"]
        );
    }

    public function GetAccessAccountData(){
        $arAccount = array(
            "IS_GROUP_PUBLISH" => $this->profile["OK"]["OK_IS_GROUP_PUBLISH"],
            "GROUP" => $this->profile["OK"]["OK_GROUP"],
            "APP_ID" => $this->profile["OK"]["OK_APP_ID"],
            "APP_PUBLIC_KEY" => $this->profile["OK"]["OK_APP_PUBLIC_KEY"],
            "APP_SECRET_KEY" => $this->profile["OK"]["OK_APP_SECRET_KEY"],
            "ACCESS_TOKEN" => $this->profile["OK"]["OK_ACCESS_TOKEN"],
        );

        return $arAccount;
    }

    function PreparePostData( $arData ){
        global $APPLICATION;

        $postData = array();

        $postData["access_token"] = $this->okAccount["ACCESS_TOKEN"];

        $postData["link"] = $arData["URL"];
        $postData["picture"] = $arData["PHOTO"];

        $postData["name"] = CAcritExportproplusExternApiTools::PreparePostText( $arData["NAME"], "UTF-8" );

        $postData["description"] = CAcritExportproplusExternApiTools::PreparePostText( $arData["DESCRIPTION"], "UTF-8" );
        $postData["description_market"] = CAcritExportproplusExternApiTools::PreparePostText( $arData["DESCRIPTION_MARKET"], "UTF-8" );

        $postData["price"] = CAcritExportproplusExternApiTools::PreparePostText( $arData["PRICE"], "UTF-8" );
        $postData["currency"] = CAcritExportproplusExternApiTools::PreparePostText( $arData["CURRENCY"], "UTF-8" );

        return $postData;
    }

    function AddMediatopic( $arData ){
        $responseFeedPost = false;

        $arPostData = self::PreparePostData( $arData );

        $obFeedPost = (object)array( "media" => array() );

        $postMessage = $arPostData["description"];
        $obFeedPost->media[] = (object)array(
            "type" => "text",
            "text" => $postMessage
        );

        if( $this->okAccount["IS_GROUP_PUBLISH"] == "Y" ){
            $obFeedPost->onBehalfOfGroup = true;
        }
        else{
            $obFeedPost->onBehalfOfGroup = false;
        }

        $arPhotoList = self::UploadPhotos( $arPostData["picture"] );

        if( !empty( $arPhotoList ) ){
            $obFeedPost->media[] = (object)array(
                "type" => "photo",
                "list" => $arPhotoList["attach"],
            );
        }

        $postLink = $arPostData["link"];
        if( !empty( $postLink ) ){
            $obFeedPost->media[] = (object)array(
                "type" => "link",
                "url" => &$postLink
            );
            $postLinkKey = count( $obFeedPost->media ) - 1;
        }

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "type" => "GROUP_THEME",
            "attachment" => json_encode( $obFeedPost )
        );

        $responseFeedPost = \OdnoklassnikiSDK::makeRequest( "mediatopic.post", $arPostParams );

        if( isset( $responseFeedPost["error_code"] ) && ( $responseFeedPost["error_code"] == 5000 ) ){
            if( strpos( $postLink, "https" ) === 0 ){
                $postLink = "http".substr( $postLink, strlen( "https" ) );
            }
            else{
                $postLink = "https".substr( $postLink, strlen( "http" ) );
            }

            $arPostParams = array(
                "gid" => $this->okAccount["GROUP"],
                "type" => "GROUP_THEME",
                "attachment" => json_encode( $obFeedPost )
            );

            $responseFeedPost = \OdnoklassnikiSDK::makeRequest( "mediatopic.post", $arPostParams );

            if( isset( $responseFeedPost["error_code"] ) && ( $responseFeedPost["error_code"] == 5000 ) && ( $postLinkKey >= 0 ) ){
                unset( $obFeedPost->media[$postLinkKey] );
                $arPostParams = array(
                    "gid" => $this->okAccount["GROUP"],
                    "type" => "GROUP_THEME",
                    "attachment" => json_encode( $obFeedPost )
                );
                $responseFeedPost = \OdnoklassnikiSDK::makeRequest( "mediatopic.post", $arPostParams );
            }
        }

        if( isset( $responseFeedPost["error_code"] ) ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseFeedPost["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseFeedPost;
    }

    function GetTopics(){
        $arTopics = array();

        $arGetTopicResponse = self::GetTopicsPart();
        $arTopicsData = $arGetTopicResponse["media_topics"];
        while( count( $arGetTopicResponse["media_topics"] ) ){
            $arGetTopicResponse = self::GetTopicsPart( $arGetTopicResponse["anchor"] );

            if( count( $arGetTopicResponse["media_topics"] ) ){
                $arTopicsData = array_merge( $arTopicsData, $arGetTopicResponse["media_topics"] );
            }
        }

        foreach( $arTopicsData as $arTopicsDataItem ){
            $arTopics[] = $arTopicsDataItem["delete_id"];
        }

        return $arTopics;
    }

    function GetTopicsPart( $anchor = false ){
        $responseGetTopicsPart = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
        );

        if( $anchor ){
            $arPostParams["anchor"] = $anchor;
        }

        $responseGetTopicsPart = \OdnoklassnikiSDK::makeRequest( "mediatopic.getTopics", $arPostParams );

        return $responseGetTopicsPart;
    }

    function GetMediaTopicById( $topicId ){
        $responseGetMediaTopicById = false;

        $arPostParams = array(
            "topic_ids" => $topicId,
            "fields" => "media_topic.*"
        );

        $responseGetMediaTopicById = \OdnoklassnikiSDK::makeRequest( "mediatopic.getByIds", $arPostParams );

        if( isset( $responseGetMediaTopicById["error_code"] ) ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseGetMediaTopicById["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseGetMediaTopicById;
    }

    function DeleteMediaTopicById( $topicId ){
        $responseDeleteMediaTopicById = false;

        $arMediaTopic = self::GetMediaTopicById( $topicId );

        if( !isset( $arMediaTopic["error_code"] ) ){
            $arPostParams = array(
                "delete_id" => $arMediaTopic["media_topics"][0]["delete_id"]
            );

            $responseDeleteMediaTopicById = \OdnoklassnikiSDK::makeRequest( "mediatopic.deleteTopic", $arPostParams );

            if( isset( $responseDeleteMediaTopicById["error_code"] ) ){
                $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseDeleteMediaTopicById["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
                $this->log->IncProductError();
            }
        }

        return $responseDeleteMediaTopicById;
    }

    function DeleteMediaTopic( $deleteTopicId ){
        $responseDeleteMediaTopic = false;

        $arPostParams = array(
            "delete_id" => $deleteTopicId
        );

        $responseDeleteMediaTopic = \OdnoklassnikiSDK::makeRequest( "mediatopic.deleteTopic", $arPostParams );

        if( isset( $responseDeleteMediaTopic["error_code"] ) ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseDeleteMediaTopic["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseDeleteMediaTopic;
    }

    function DeleteMediaTopics(){
        $responseDeleteMediaTopic = false;

        $arResponseGetTopics = self::GetTopics();

        foreach( $arResponseGetTopics as $deleteTopicId ){
            $arPostParams = array(
                "delete_id" => $deleteTopicId
            );

            $responseDeleteMediaTopic = \OdnoklassnikiSDK::makeRequest( "mediatopic.deleteTopic", $arPostParams );
        }

        return $responseDeleteMediaTopic;
    }

    function GetAlbums(){
        $arAlbums = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"]
        );

        $responseGetAlbums = \OdnoklassnikiSDK::makeRequest( "photos.getAlbums", $arPostParams );

        if( is_array( $responseGetAlbums ) && !empty( $responseGetAlbums ) ){
            foreach( $responseGetAlbums["albums"] as $arAlbum ){
                $arAlbums[] = $arAlbum["aid"];
            }
        }

        return $arAlbums;
    }

    function CreateAlbum( $arData ){
        $responseCreateAlbum = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "title" => CAcritExportproplusExternApiTools::PreparePostText( $arData["NAME"], "UTF-8" )
        );

        $responseCreateAlbum = \OdnoklassnikiSDK::makeRequest( "photos.createAlbum", $arPostParams );

        if( isset( $responseCreateAlbum["error_code"] ) ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseCreateAlbum["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseCreateAlbum;
    }

    function DeleteAlbum( $arData ){
        $responseDeleteAlbum = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "aid" => $arData["ID"]
        );

        $responseDeleteAlbum = \OdnoklassnikiSDK::makeRequest( "photos.deleteAlbum", $arPostParams );

        if( isset( $responseDeleteAlbum["error_code"] ) ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseDeleteAlbum["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseDeleteAlbum;
    }

    function GetPhotosPage( $arData ){
        $responseGetPhotosPage = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "aid" => $arData["ID"]
        );

        if( $arData["anchor"] ){
            $arPostParams["anchor"] = $arData["anchor"];
        }

        $responseGetPhotosPage = \OdnoklassnikiSDK::makeRequest( "photos.getPhotos", $arPostParams );

        return $responseGetPhotosPage;
    }

    function GetPhotos( $arData ){
        $arPhotos = array();

        $arGetPhotosPageResponse = self::GetPhotosPage( $arData );
        $arPhotosData = $arGetPhotosPageResponse["photos"];
        while( count( $arGetPhotosPageResponse["photos"] ) ){
            $arGetPhotosPageResponse = self::GetTopicsPart( $arGetPhotosPageResponse["anchor"] );

            if( count( $arGetPhotosPageResponse["photos"] ) ){
                $arPhotosData = array_merge( $arPhotosData, $arGetPhotosPageResponse["photos"] );
            }
        }

        foreach( $arPhotosData as $arPhotosDataItem ){
            $arPhotos[] = $arPhotosDataItem["id"];
        }

        return $arPhotos;
    }

    function PhotosCommit( $arData ){
        $responsePhotosCommit = false;

        $arPostParams = array(
            "photo_id" => $arData["ID"],
            "token" => $arData["TOKEN"],
            "comment" => CAcritExportproplusExternApiTools::PreparePostText( $arData["DESCRIPTION"], "UTF-8" )
        );

        $responsePhotosCommit = \OdnoklassnikiSDK::makeRequest( "photosV2.commit", $arPostParams );

        return $responsePhotosCommit;
    }

    function UploadPhotos( $arPhotos, $aid = false ){
        $arSavedPhotos = array();

        $arGetUploadUrlParams = array(
            "gid" => $this->okAccount["GROUP"],
            "count" => count( $arPhotos ),
        );

        if( intval( $aid ) > 0 ){
            $arGetUploadUrlParams["aid"] = intval( $aid );
        }

        $responseUploadUrl = \OdnoklassnikiSDK::makeRequest( "photosV2.getUploadUrl", $arGetUploadUrlParams );

        if( !isset( $responseUploadUrl["error_code"] ) ){
            $arUploadedFiles = array();
            foreach( $arPhotos as $photoIndex => $photoName ){
                $arUploadedFiles["pic".$photoIndex] = CAcritExportproplusExternApiTools::GetCurlFilename( $photoName );
            }

            $resonseSavedPhotos = json_decode( CAcritExportproplusExternApiTools::_CurlPost( $responseUploadUrl["upload_url"], $arUploadedFiles ) );

            if( isset( $resonseSavedPhotos->photos ) ){
                $arSavedPhotos["fullinfo"] = $resonseSavedPhotos->photos;

                $arSavedPhotos["attach"] = array();
                foreach( $resonseSavedPhotos->photos as $index => $value ){
                    $arSavedPhotos["attach"][] = (object) array( "id" => $value->token );
                }
            }
        }

        return $arSavedPhotos;
    }


    function UploadPhotosToAlbum( $arData ){
        $responseUploadPhotosToAlbum = array();

        $arPhotoList =  self::UploadPhotos( $arData["PHOTO"], $arData["AID"] );

        foreach( $arPhotoList["fullinfo"] as $photoId => $photoToken ){
            $arPhotoToken = (array) $photoToken;
            $responseUploadPhotosToAlbum = self::PhotosCommit(
                array(
                    "ID" => $photoId,
                    "TOKEN" => $arPhotoToken["token"],
                    "DESCRIPTION" => $arData["DESCRIPTION"]
                )
            );
        }

        return $responseUploadPhotosToAlbum;
    }

    function AddMarketItem( $arData, $bEdit = false ){
        $responseMarketAdd = false;

        $arPostData = self::PreparePostData( $arData );

        $obMarketItem = (object)array( "media" => array() );

        $obMarketItem->media[] = (object)array(
            "type" => "text",
            "text" => $arPostData["name"]
        );

        $obMarketItem->media[] = (object)array(
            "type" => "text",
            "text" => $arPostData["description_market"]
        );

        $arPhotoList = self::UploadPhotos( $arPostData["picture"] );

        if( !empty( $arPhotoList ) ){
            $obMarketItem->media[] = (object)array(
                "type" => "photo",
                "list" => $arPhotoList["attach"],
            );
        }

        $obMarketItem->media[] = (object)array(
            "type" => "product",
            "price" => $arPostData["price"],
            "currency" => $arPostData["currency"]
        );

        $arPostParams = array(
            "attachment" => json_encode( $obMarketItem ),
            "gid" => $this->okAccount["GROUP"],
            "type" => "GROUP_PRODUCT",
        );

        if( is_array( $arData["CATALOGS"] ) && !empty( $arData["CATALOGS"] ) ){
            $arPostParams["catalog_ids"] = implode( ",", $arData["CATALOGS"] );
        }

        if( $bEdit ){
            $arPostParams["product_id"] = $arData["ID"];
            $responseMarketAdd = \OdnoklassnikiSDK::makeRequest( "market.edit", $arPostParams );
        }
        else{
            $responseMarketAdd = \OdnoklassnikiSDK::makeRequest( "market.add", $arPostParams );
        }

        if( isset( $responseMarketAdd["error_code"] ) ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseMarketAdd["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseMarketAdd;
    }

    function DeleteMarketItem( $marketItemId ){
        $responseDeleteMarketItem = false;

        $arPostParams = array(
            "product_id" => $marketItemId
        );

        $responseDeleteMarketItem = \OdnoklassnikiSDK::makeRequest( "market.delete", $arPostParams );

        if( isset( $responseDeleteMarketItem["error_code"] ) ){
            $this->log->AddMessage( "(ID:{$marketItemId}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseDeleteMarketItem["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseDeleteMarketItem;
    }

    function DeleteAllMarketItems(){
        $responseDeleteAllMarketItems = false;

        $arMarketItems = self::GetMarketItems();

        if( is_array( $arMarketItems ) && !empty( $arMarketItems ) ){
            foreach( $arMarketItems as $marketItemId ){
                $responseDeleteAllMarketItems = self::DeleteMarketItem( $marketItemId );
            }
        }

        return $responseDeleteAllMarketItems;
    }

    function GetMarketItems(){
        $arMarketItems = array();

        $arGetMarketItems = self::GetMarketItemsPart();

        $arMarketItemsData = $arGetMarketItems["short_products"];
        while( count( $arGetMarketItems["short_products"] ) ){
            $arGetMarketItems = self::GetMarketItemsPart( $arGetMarketItems["anchor"] );

            if( count( $arGetMarketItems["short_products"] ) ){
                $arMarketItemsData = array_merge( $arMarketItemsData, $arGetMarketItems["short_products"] );
            }
        }

        foreach( $arMarketItemsData as $arMarketItemsDataItem ){
            $arMarketItems[] = $arMarketItemsDataItem["id"];
        }

        return $arMarketItems;
    }

    function GetMarketItemsPart( $anchor = false ){
        $responseGetMarketItemsPart = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "tab" => "PRODUCTS",
            "count" => 10,
        );

        if( $anchor ){
            $arPostParams["anchor"] = $anchor;
        }

        $responseGetMarketItemsPart = \OdnoklassnikiSDK::makeRequest( "market.getProducts", $arPostParams );

        return $responseGetMarketItemsPart;
    }

    function GetMarketItemsByCatalog( $catalogId ){
        $arMarketItems = array();

        $arGetMarketItems = self::GetMarketItemsByCatalogPart();

        $arMarketItemsData = $arGetMarketItems["short_products"];
        while( count( $arGetMarketItems["short_products"] ) ){
            $arGetMarketItems = self::GetMarketItemsByCatalogPart( $catalogId, $arGetMarketItems["anchor"] );

            if( count( $arGetMarketItems["short_products"] ) ){
                $arMarketItemsData = array_merge( $arMarketItemsData, $arGetMarketItems["short_products"] );
            }
        }

        foreach( $arMarketItemsData as $arMarketItemsDataItem ){
            $arMarketItems[] = $arMarketItemsDataItem["id"];
        }

        return $arMarketItems;
    }

    function GetMarketItemsByCatalogPart( $catalogId, $anchor = false ){
        $responseGetMarketItemsByCatalogPart = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "catalog_id" => $catalogId,
            "count" => 10,
        );

        if( $anchor ){
            $arPostParams["anchor"] = $anchor;
        }

        $responseGetMarketItemsByCatalogPart = \OdnoklassnikiSDK::makeRequest( "market.getByCatalog", $arPostParams );

        return $responseGetMarketItemsByCatalogPart;
    }

    function AddMarketCatalog( $arData, $bEdit = false ){
        $responseAddMarketCatalog = false;

        $arPhotoList = self::UploadPhotos( $arData["PHOTO"] );
        $arCatalogPhoto = (array)$arPhotoList["attach"][0];

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "name" => CAcritExportproplusExternApiTools::PreparePostText( $arData["NAME"], "UTF-8" ),
            "photo_id" => $arCatalogPhoto["id"],
        );

        if( $bEdit ){
            $arPostParams["catalog_id"] = $arData["ID"];
            $responseAddMarketCatalog = \OdnoklassnikiSDK::makeRequest( "market.editCatalog", $arPostParams );
        }
        else{
            $responseAddMarketCatalog = \OdnoklassnikiSDK::makeRequest( "market.addCatalog", $arPostParams );
        }

        if( isset( $responseAddMarketCatalog["error_code"] ) ){
            $this->log->AddMessage( "{$arData["NAME"]} (ID:{$arData["ID"]}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseAddMarketCatalog["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseAddMarketCatalog;
    }

    function DeleteMarketCatalog( $marketCatalogId, $bDeleteProducts = false ){
        $responseDeleteMarketCatalog = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "catalog_id" => $marketCatalogId,
        );

        if( $bDeleteProducts ){
            $arPostParams["delete_products"] = true;
        }

        $responseDeleteMarketCatalog = \OdnoklassnikiSDK::makeRequest( "market.deleteCatalog", $arPostParams );

        if( isset( $responseDeleteMarketCatalog["error_code"] ) ){
            $this->log->AddMessage( "(ID:{$marketCatalogId}) : ".str_replace( "#OK_ERROR#", "CODE: ".$responseDeleteMarketCatalog["error_msg"], GetMessage( "ACRIT_EXPORTPROPLUS_REQUIRED_FIELD_OK_SKIP" ) ) );
            $this->log->IncProductError();
        }

        return $responseDeleteMarketCatalog;
    }

    function DeleteAllMarketCatalogs(){
        $responseDeleteAllMarketCatalogs = false;

        $arMarketCatalogs = self::GetMarketCatalogsByGroup();

        if( is_array( $arMarketCatalogs ) && !empty( $arMarketCatalogs ) ){
            foreach( $arMarketCatalogs as $marketCatalogId ){
                $responseDeleteAllMarketCatalogs = self::DeleteMarketCatalog( $marketCatalogId );
            }
        }

        return $responseDeleteAllMarketCatalogs;
    }

    function GetMarketCatalogsByGroup(){
        $arMarketCatalogs = array();

        $arGetMarketCatalogs = self::GetMarketCatalogsByGroupPart();

        $arMarketCatalogsData = $arGetMarketCatalogs["catalogs"];
        while( count( $arGetMarketCatalogs["catalogs"] ) ){
            $arGetMarketCatalogs = self::GetMarketCatalogsByGroupPart( $arGetMarketCatalogs["anchor"] );

            if( count( $arGetMarketCatalogs["catalogs"] ) ){
                $arMarketCatalogsData = array_merge( $arMarketCatalogsData, $arGetMarketCatalogs["catalogs"] );
            }
        }

        foreach( $arMarketCatalogsData as $arMarketCatalogsDataItem ){
            $arMarketCatalogs[] = $arMarketCatalogsDataItem["id"];
        }

        return $arMarketCatalogs;
    }

    function GetMarketCatalogsByGroupPart( $anchor = false ){
        $responseGetMarketCatalogsByGroupPart = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "count" => 10,
            "fields" => "id"
        );

        if( $anchor ){
            $arPostParams["anchor"] = $anchor;
        }

        $responseGetMarketCatalogsByGroupPart = \OdnoklassnikiSDK::makeRequest( "market.getCatalogsByGroup", $arPostParams );

        return $responseGetMarketCatalogsByGroupPart;
    }

    function SetMarketItemCatalogsList( $arData ){
        $responseSetMarketItemCatalogsList = false;

        $arPostParams = array(
            "gid" => $this->okAccount["GROUP"],
            "product_id" => $arData["ID"],
        );

        if( isset( $arData["CATALOGS"] ) && ( strlen( trim( $arData["CATALOGS"] ) ) > 0 ) ){
            $arPostParams["catalog_ids"] = $arData["CATALOGS"];
        }

        $responseSetMarketItemCatalogsList = \OdnoklassnikiSDK::makeRequest( "market.updateCatalogsList", $arPostParams );

        return $responseSetMarketItemCatalogsList;
    }
}

class CAcritExportproplusInstagram{
    public $profile = null;
    public $instagramAccount = null;
    public $guid = null;
    public $deviceId = null;
    public $log;
    public $dbProfile = null;

    public function __construct( $profile ){
        $this->dbProfile = new CExportproplusProfileDB();
        $this->iblockIncluded = @CModule::IncludeModule( "iblock" );
        $this->profile = $profile;

        $this->instagramAccount = self::GetAccessAccountData();
        $this->guid = self::GenerateGuid();
        $this->deviceId = "android-".$this->guid;

        $this->log = new CAcritExportproplusLog( $this->profile["ID"] );
    }

    public function GetAccessAccountData(){
        $arAccount = array(
            "LOGIN" => $this->profile["INSTAGRAM"]["INSTAGRAM_LOGIN"],
            "PASSWORD" => $this->profile["INSTAGRAM"]["INSTAGRAM_PASSWORD"],
        );

        return $arAccount;
    }

    function _curl_post( $url, $post_data, $cookie_postfix = "", $user_agent = false, $timeout = 120/*seconds*/ ){
        $params = array();

        if( is_array( $cookie_postfix ) ){
            $params["cookie_type"] = $cookie_postfix[0] ?: $cookie_postfix["type"];
            $params["cookie_postfix"] = $cookie_postfix[1] ?: $cookie_postfix["postfix"];
            $params["cookies"] = $cookie_postfix["cookies"];
        }
        elseif( !empty( $cookie_postfix ) ){
            $params["cookie_type"] = 1;
            $params["cookie_postfix"] = $cookie_postfix;
        }

        $params["user_agent"] = $user_agent;
        $params["timeout"] = $timeout;

        return self::curl_post( $url, $post_data, $params );
    }

    /*
    string $url - àäðåñ çàïðîñà
    string|array $post_data - äàííûå çàïðîñà
    string $params - ïàðàìåòðû
    return resurl curl request
    */
    function curl_post( $url, $post_data, $params = array() ){
        if( $url == "" ){
            return false;
        }

        $is_https = strpos( $url, "https" ) === 0;

        if( is_array( $cookie_postfix ) ){
            $cookie_type = $cookie_postfix[0];
            $cookie_postfix = $cookie_postfix[1];
        }
        else{
            $cookie_type = ( empty( $cookie_postfix ) ? 0 : 1 );
        }

        $cookie_file = $_SERVER["DOCUMENT_ROOT"]."/upload/acrit.exportproplus/";

        if( $params["cookie_type"] and ( is_dir( $cookie_file ) or mkdir( $cookie_file, 0744, true ) ) ){
            if( empty( $params["cookie_postfix"] ) ){
                $cookie_file .= "cookies.txt";
            }
            else{
                $cookie_file .= "cookies_".$params["cookie_postfix"].".txt";
            }
        }
        else{
            $cookie_file .= "cookies.txt";
        }

        $c = curl_init( $url );

        if( $params["CUSTOM_REQUEST"] ){
            curl_setopt( $c, CURLOPT_CUSTOMREQUEST, $params["CUSTOM_REQUEST"] );
        }

        curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );

        if( !!$params["user_agent"] ){
            curl_setopt( $c, CURLOPT_USERAGENT, $params["user_agent"] );
        }

        if( $params["cookie_type"] == 1 ){
            curl_setopt( $c, CURLOPT_COOKIEFILE, $cookie_file );
            curl_setopt( $c, CURLOPT_COOKIEJAR, $cookie_file );
        }
        elseif( $params["cookie_type"] == 2 ){
            curl_setopt( $c, CURLOPT_COOKIEJAR, $cookie_file );
        }
        elseif( $params["cookie_type"] == 3 ){
            curl_setopt( $c, CURLOPT_COOKIEFILE, $cookie_file );
        }

        curl_setopt( $c, CURLOPT_COOKIEFILE, $cookie_file );

        if( $is_https ){
            curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt( $c, CURLOPT_SSL_VERIFYHOST, 0 );
        }

        if( !empty( $post_data ) ){
            if( !$params["CUSTOM_REQUEST"] or $params["CUSTOM_REQUEST"] == "POST" )
                curl_setopt( $c, CURLOPT_POST, true );
            if( isset( $params["enctype"] )
                && $params["enctype"] == CURL_ENCTYPE_APPLICATION ){
                $post_data = http_build_query( $post_data );
                curl_setopt( $c, CURLOPT_HTTPHEADER, array( "Content-Length: ".strlen( $post_data ) ) );
            }
            curl_setopt( $c, CURLOPT_POSTFIELDS, $post_data );
        }

        curl_setopt( $c, CURLOPT_TIMEOUT, $params["timeout"] );
        $res = curl_exec($c);
        curl_close( $c );

        return $res;
    }

    public function ApiMethod( $method, $data, $cookie ){
        $return = false;

        if( $method != "" ){
            $url = "https://instagram.com/api/v1/".$method;
            //$return = CAcritExportproplusExternApiTools::_CurlPost( $url, $data, array( $cookie, "instagram" ), self::GenerateUserAgent() );
            $return = self::_curl_post( $url, $data, array( $cookie, "instagram" ), self::GenerateUserAgent() );
        }

        return $return;
    }

    public function GenerateGuid(){
        return sprintf(
            "%04x%04x-%04x-%04x-%04x-%04x%04x%04x",
            mt_rand( 0, 65535 ),
            mt_rand( 0, 65535 ),
            mt_rand( 0, 65535 ),
            mt_rand( 16384, 20479 ),
            mt_rand( 32768, 49151 ),
            mt_rand( 0, 65535 ),
            mt_rand( 0, 65535 ),
            mt_rand( 0, 65535 )
        );
    }

    public function GenerateUserAgent(){
        $resolutions = array(
            "720x1280",
            "320x480",
            "480x800",
            "1024x768",
            "1280x720",
            "768x1024",
            "480x320"
        );

        $versions = array(
            "GT-N7000",
            "SM-N9000",
            "GT-I9220",
            "GT-I9100"
        );

        $dpis = array(
            "120",
            "160",
            "320",
            "240"
        );

        $ver = $versions[array_rand( $versions )];
        $dpi = $dpis[array_rand( $dpis )];
        $res = $resolutions[array_rand( $resolutions )];

        return "Instagram 4.".mt_rand( 1, 2 ).".".mt_rand( 0, 2 )." Android (".mt_rand( 10, 11 )."/".mt_rand( 1, 3 ).".".mt_rand( 3, 5 ).".".mt_rand( 0, 5 )."; ".$dpi."; ".$res."; samsung; ".$ver."; ".$ver."; smdkc210; en_US)";
    }

    function GenerateSignature( $data ){
        return hash_hmac( "sha256", $data, "b4a23f5e39b5929e0666ac5de94c89d1618a2916" );
    }

    function GetPostData( $photoPath ){
        if( !$photoPath ){
            echo "The image doesn't exist ".$photoPath;
        }
        else{
            $post_data = array(
                "device_timestamp" => time(),
                "photo" => CAcritExportproplusExternApiTools::GetCurlFilename( $photoPath )
            );
            return $post_data;
        }
    }

    function GetImagePathTmp(){
        return $_SERVER["DOCUMENT_ROOT"]."/upload/tmp/acrit.exportproplus.instagram.image.tmp.jpg";
    }

    function GetSquareImage( $imagePath ){
        if( empty( $imagePath ) ){
            return "";
        }

        list( $iw, $ih, $itype ) = getimagesize( $imagePath );

        if( $iw == $ih ){
            return $imagePath;
        }

        $types = array( "", "gif", "jpeg", "png" );
        $ext = $types[$itype];
        if( $ext ){
            $func = "imagecreatefrom".$ext;
            $baseImage = $func( $imagePath );
        }
        else{
            return "";
        }

        $size = min( $iw, $ih );
        $ox = 0;
        if( $size != $iw ){
            $ox = $iw / 2 - $size / 2;
        }

        $procImage = imagecreatetruecolor( $size, $size );
        imagecopy( $procImage, $baseImage, 0, 0, $ox, 0, $size, $size );
        $dest = self::GetImagePathTmp();

        if( $type == 2 ){
            if( imagejpeg( $procImage, $dest, 100 ) ){
                return $dest;
            }
        }
        else{
            $func = "image".$ext;
            if( $func( $procImage, $dest ) ){
                return $dest;
            }
        }

        return "";
    }

    public function Login(){
        $bResult = true;

        $data = ( object )array(
            "device_id" => $this->deviceId,
            "guid" => $this->guid,
            "username" => $this->instagramAccount["LOGIN"],
            "password" => $this->instagramAccount["PASSWORD"],
            "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
        );
        $data = json_encode( $data );

        $sig = self::GenerateSignature( $data );
        $data = "signed_body=".$sig.".".urlencode( $data )."&ig_sig_key_version=4";
        $responseLogin = self::ApiMethod( "accounts/login/", $data, 2 );

        if( strpos( $responseLogin, "Sorry, an error occurred while processing this request." ) ){
            $text = "Request failed, there's a chance that this proxy/ip is blocked";
            $bResult = false;
        }
        else{
            if( empty( $responseLogin ) ){
                $text = "Empty response received from the server while trying to login";
                $bResult = false;
            }
            else{
                $obj = @json_decode( $responseLogin, true );

                if( empty( $obj ) ){
                    $text = "Could not decode the response" ;
                    $bResult = false;
                }
            }
        }

        return $bResult;
    }

    public function AddPhoto( $arData ){
        $responseAddPhoto = false;
        
        $bPhotoArray = is_array( $arData["PHOTO"] ) && !empty( $arData["PHOTO"] );
        if(
            ( ( strlen( $arData["PHOTO"] ) > 0 ) || $bPhotoArray )
            && ( strlen( $arData["DESCRIPTION"] ) > 0 )
            && self::Login()
        ){
            if( $bPhotoArray ){
                $arMedias = array();
                foreach( $arData["PHOTO"] as $photoItem ){
                    $photoItem = self::GetSquareImage( $photoItem );
                    $data = self::GetPostData( $photoItem );
                    $arMedias[] = self::ApiMethod( "media/upload/", $data, 3 );
                }
                
                $arMediaIds = array();
                if( is_array( $arMedias ) && !empty( $arMedias ) ){
                    foreach( $arMedias as $responseMediaAdd ){
                        if( empty( $responseMediaAdd ) ){
                            $text = "Empty response received from the server while trying to post the image";
                        }
                        else{
                            $arMediaAdd = @json_decode( $responseMediaAdd, true );

                            if( empty( $arMediaAdd ) ){
                                $text = "Could not decode the response";
                            }
                            else{
                                $status = $arMediaAdd["status"];
                                if( $status != "ok" ){
                                    $text = "Status isn't okay";
                                }
                                else{
                                    $arMediaIds[] = $arMediaAdd["media_id"];
                                }
                            }
                        }
                    }
                }
                
                if( is_array( $arMediaIds ) && !empty( $arMediaIds ) ){
                    $arData["DESCRIPTION"] = strip_tags( $arData["DESCRIPTION"] );
                    $descriptionCharset = CAcritExportproplusTools::GetStringCharset( $arData["DESCRIPTION"] );
                    if( $descriptionCharset == "cp1251" ){
                        $arData["DESCRIPTION"] = $GLOBALS['APPLICATION']->ConvertCharset( $arData["DESCRIPTION"], "windows-1251", "UTF-8" );
                    }

                    $data = (object) array(
                        "device_id" => $this->deviceId,
                        "guid" => $this->guid,
                        "media_id" => $arMediaIds[0],
                        "caption" => html_entity_decode( trim( $arData["DESCRIPTION"] ) ),
                        "device_timestamp" => time(),
                        "source_type" => "5",
                        "filter_type" => "0",
                        "extra" => "{}",
                        "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
                    );

                    $data = json_encode( $data );
                    $sig = self::GenerateSignature( $data );
                    $new_data = "signed_body=".$sig.".".urlencode( $data )."&ig_sig_key_version=4";

                    $responseAddPhoto = self::ApiMethod( "media/configure/", $new_data, 3 );
                }                                        
            }
            else{
                $arData["PHOTO"] = self::GetSquareImage( $arData["PHOTO"] );
                $data = self::GetPostData( $arData["PHOTO"] );
                $responseMediaAdd = self::ApiMethod( "media/upload/", $data, 3 );
                              
                if( empty( $responseMediaAdd ) ){
                    $text = "Empty response received from the server while trying to post the image";
                }
                else{
                    $arMediaAdd = @json_decode( $responseMediaAdd, true );

                    if( empty( $arMediaAdd ) ){
                        $text = "Could not decode the response";
                    }
                    else{
                        $status = $arMediaAdd["status"];
                        if( $status != "ok" ){
                            $text = "Status isn't okay";
                        }
                        else{
                            $mediaId = $arMediaAdd["media_id"];
                            $arData["DESCRIPTION"] = strip_tags( $arData["DESCRIPTION"] );
                            $descriptionCharset = CAcritExportproplusTools::GetStringCharset( $arData["DESCRIPTION"] );
                            if( $descriptionCharset == "cp1251" ){
                                $arData["DESCRIPTION"] = $GLOBALS['APPLICATION']->ConvertCharset( $arData["DESCRIPTION"], "windows-1251", "UTF-8" );
                            }

                            $data = (object) array(
                                "device_id" => $this->deviceId,
                                "guid" => $this->guid,
                                "media_id" => $mediaId,
                                "caption" => html_entity_decode( trim( $arData["DESCRIPTION"] ) ),
                                "device_timestamp" => time(),
                                "source_type" => "5",
                                "filter_type" => "0",
                                "extra" => "{}",
                                "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
                            );

                            $data = json_encode( $data );
                            $sig = self::GenerateSignature( $data );
                            $new_data = "signed_body=".$sig.".".urlencode( $data )."&ig_sig_key_version=4";

                            $responseAddPhoto = self::ApiMethod( "media/configure/", $new_data, 3 );

                            //!! check answer $responseAddPhoto

                            /*if( empty( $responseAddPhoto ) ){
                                $text .= "Empty response received from the server while trying to configure the image";
                            }
                            else{
                                if( strpos( $responseAddPhoto, "login_required" ) ){
                                    $text .= "You are not logged in. There's a chance that the account is banned";
                                }
                                else{
                                    $obj = @json_decode( $responseAddPhoto, true );
                                    $status = $obj["status"];
                                    if( $status != "fail" ){
                                        $text .= "Success";
                                    }
                                    else{
                                        $text .= "Fail";
                                    }
                                }
                            }*/
                        }
                    }
                }
            }
        }

        return $responseAddPhoto;
    }

    public function UpdatePhoto( $arData ){
        $responseUpdatePhoto = false;

        global $APPLICATION;

        if( ( intval( $arData["POST_ID"] ) > 0 ) && ( strlen( $arData["DESCRIPTION"] ) > 0 ) && self::Login() ){
            $arData["DESCRIPTION"] = strip_tags( $arData["DESCRIPTION"] );
            $descriptionCharset = CAcritExportproplusTools::GetStringCharset( $arData["DESCRIPTION"] );

            if( $descriptionCharset == "cp1251" ){
                $arData["DESCRIPTION"] = $APPLICATION->ConvertCharset( $arData["DESCRIPTION"], "windows-1251", "UTF-8" );
            }

            $data = ( object )array(
                "device_id" => $this->deviceId,
                "guid" => $this->guid,
                "caption_text" => html_entity_decode( trim( $arData["DESCRIPTION"] ) ),
                "device_timestamp" => time(),
                "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
            );
            $data = json_encode( $data );

            $sig = self::GenerateSignature( $data );
            $data = "signed_body=".$sig.".".urlencode( $data )."&ig_sig_key_version=4";
            $responseUpdatePhoto = self::ApiMethod( "media/".$arData["POST_ID"]."/edit_media/", $data, 3 );

            //!! check answer $responseUpdatePhoto
        }

        return $responseUpdatePhoto;
    }

    public function DeletePhoto( $arData ){
        $responseDeletePhoto = false;

        if( ( intval( $arData["POST_ID"] ) > 0 ) && self::Login() ){
            $data = ( object )array(
                "device_id" => $this->deviceId,
                "guid" => $this->guid,
                "media_id" => $arData["POST_ID"],
                "device_timestamp" => time(),
                "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
            );
            $data = json_encode( $data );

            $sig = self::GenerateSignature( $data );
            $data = "signed_body=".$sig.".".urlencode( $data )."&ig_sig_key_version=4";
            $responseDeletePhoto = self::ApiMethod( "media/".$arData["POST_ID"]."/delete/?media_type=1", $data, 3 );

            //!! check answer $responseDeletePhoto
        }

        return $responseDeletePhoto;
    }
}

?>