<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Html {
	
	/**
	 *	Wrapper for SelectBoxFromArray()
	 */
	public static function selectBox($strName, $arValues, $strSelected=null, $strDefault=null, $strAttr=null, $strInputId=null, $bSelect2=true, $bSelect2Icons=false, $arSelect2Config=null){
		$strId = Helper::randString(true);
		$arValues = [
			'REFERENCE' => array_values($arValues),
			'REFERENCE_ID' => array_keys($arValues),
		];
		if(is_null($strSelected)){
			$strSelected = reset($arValues['REFERENCE_ID']);
		}
		if(strlen($strInputId)){
			$strAttr .= sprintf(' id="%s"', $strInputId);
		}
		$strHtml = static::selectBoxFromArray($strName, $arValues, $strSelected, $strDefault, $strAttr, $bSelect2Icons);
		$strHtml = sprintf('<div id="%s">%s</div>', $strId, $strHtml);
		if($bSelect2){
			Helper::addJsSelect2();
			$arSelect2ConfigResult = [];
			if($bSelect2Icons){
				$arSelect2ConfigResult['withIcons'] = true;
			}
			if(is_array($arSelect2Config)){
				$arSelect2ConfigResult['extConfig'] = $arSelect2Config;
			}
			$strHtml .= sprintf('<script>wduSelect2($(\'#%s > select\'), %s);</script>', $strId,
				\Bitrix\Main\Web\Json::encode($arSelect2ConfigResult));
		}
		return $strHtml;
	}
	
	/**
	 *	
	 */
	public static function selectBoxEx($strName, $arValues, $arParams=[]){
		$strSelected = isset($arParams['SELECTED']) ? $arParams['SELECTED'] : null; // $strSelected=null
		$strDefault = isset($arParams['DEFAULT']) ? $arParams['DEFAULT'] : null; // $strDefault = null
		$strAttr = isset($arParams['ATTR']) ? $arParams['ATTR'] : null; // $strAttr=null
		$strInputId = isset($arParams['INPUT_ID']) ? $arParams['INPUT_ID'] : null; // $strInputId=null
		$bSelect2 = $arParams['SELECT2'] === false ? false : true; // $bSelect2=true
		$bIcons = $arParams['WITH_ICONS'] === true ? true : false; // $bSelect2icons=false
		$arSelect2Config = $arParams['SELECT2_CONFIG'] ? $arParams['SELECT2_CONFIG'] : null; // $arSelect2Config=null
		return static::selectBox($strName, $arValues, $strSelected, $strDefault, $strAttr, $strInputId, $bSelect2, $bIcons,
			$arSelect2Config);
	}
	
	/**
	 *	
	 */
	public static function selectBoxFromArray($strBoxName, $db_array, $mSelectedVal='', $strDetText='', $field1='', $bSelect2Icons=null){
		$boxName = htmlspecialcharsbx($strBoxName);
		$strReturnBox = '<select '.$field1.' name="'.$boxName.'">';
		if(isset($db_array["reference"]) && is_array($db_array["reference"]))
			$ref = $db_array["reference"];
		elseif(isset($db_array["REFERENCE"]) && is_array($db_array["REFERENCE"]))
			$ref = $db_array["REFERENCE"];
		else
			$ref = [];
		if(isset($db_array["reference_id"]) && is_array($db_array["reference_id"]))
			$ref_id = $db_array["reference_id"];
		elseif(isset($db_array["REFERENCE_ID"]) && is_array($db_array["REFERENCE_ID"]))
			$ref_id = $db_array["REFERENCE_ID"];
		else
			$ref_id = [];
		if($strDetText <> '')
			$strReturnBox .= '<option value="">'.$strDetText.'</option>';
		foreach($ref as $i => $val){
			$val = is_array($val) ? $val : ['TEXT' => $val];
			$icon = $bSelect2Icons && strlen($val['ICON']) ? $val['ICON'] : '';
			$strReturnBox .= '<option';
			if(is_array($mSelectedVal)){
				if(in_array($ref_id[$i], $mSelectedVal)){
					$strReturnBox .= ' selected';
				}
			}
			elseif(strcasecmp($ref_id[$i], $mSelectedVal) == 0) {
				$strReturnBox .= ' selected';
			}
			if(strlen($icon)){
				$strReturnBox .= ' data-icon="'.$icon.'"';
			}
			$strReturnBox .= ' value="'.htmlspecialcharsbx($ref_id[$i]).'">'.htmlspecialcharsbx($val['TEXT']).'</option>';
		}
		return $strReturnBox.'</select>';
	}

}
