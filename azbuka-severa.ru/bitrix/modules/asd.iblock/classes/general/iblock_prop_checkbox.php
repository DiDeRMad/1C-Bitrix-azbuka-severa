<?php
IncludeModuleLangFile(__FILE__);

define ('ASD_UT_CHECKBOX', 'SASDCheckbox');
if (!defined('ASD_UT_CHECKBOX_VAL_FALSE'))
{
	define ('ASD_UT_CHECKBOX_VAL_FALSE', 'N');
}
if (!defined('ASD_UT_CHECKBOX_VAL_TRUE'))
{
	define ('ASD_UT_CHECKBOX_VAL_TRUE', 'Y');
}

class CASDiblockPropCheckbox {
	public static function GetUserTypeDescription() {
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => ASD_UT_CHECKBOX,
			'DESCRIPTION' => GetMessage('ASD_UT_CHECKBOX_DESCR'),
			'ConvertToDB' => array(__CLASS__, 'ConvertToDB'),
			'GetPropertyFieldHtml' => array(__CLASS__, 'GetPropertyFieldHtml'),
			'GetAdminListViewHTML' => array(__CLASS__,'GetAdminListViewHTML'),
			'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML'),
			'GetPublicEditHTML' => array(__CLASS__, 'GetPublicEditHTML'),
			'GetPublicFilterHTML' => array(__CLASS__, 'GetPublicFilterHTML'),
			'GetAdminFilterHTML' => array(__CLASS__,'GetAdminFilterHTML'),
			'GetSettingsHTML' => array(__CLASS__,'GetSettingsHTML'),
			'PrepareSettings' => array(__CLASS__,'PrepareSettings'),
			'GetUIFilterProperty' => array(__CLASS__, 'GetUIFilterProperty')
		);
	}

	public static function ConvertToDB($arProperty, $value) {
		if (empty($value['VALUE']) || $value['VALUE'] != ASD_UT_CHECKBOX_VAL_TRUE) {
			$value['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}
		return $value;
	}

	public static function GetSettingsHTML($arFields,$strHTMLControlName, &$arPropertyFields) {
		$arPropertyFields = array(
			'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'MULTIPLE_CNT', 'WITH_DESCRIPTION'),
			'USER_TYPE_SETTINGS_TITLE' => GetMessage('ASD_UT_CHECKBOX_SETTING_TITLE'),
		);

		$arSettings = self::PrepareSettings($arFields);

		ob_start();
		?><tr>
			<td><?php echo GetMessage('ASD_UT_CHECKBOX_SETTING_VALUE_N'); ?></td>
			<td><input type="text" name="<?php echo $strHTMLControlName['NAME'];?>[VIEW][<?php echo ASD_UT_CHECKBOX_VAL_FALSE; ?>]" value="<?php echo htmlspecialcharsbx($arSettings['VIEW'][ASD_UT_CHECKBOX_VAL_FALSE]); ?>"></td>
			</tr>
			<tr>
			<td><?php echo GetMessage('ASD_UT_CHECKBOX_SETTING_VALUE_Y'); ?></td>
			<td><input type="text" name="<?php echo $strHTMLControlName['NAME'];?>[VIEW][<?php echo ASD_UT_CHECKBOX_VAL_TRUE; ?>]" value="<?php echo htmlspecialcharsbx($arSettings['VIEW'][ASD_UT_CHECKBOX_VAL_TRUE]); ?>"></td>
		</tr><?php
		$strResult = ob_get_contents();
		ob_end_clean();

		return $strResult;
	}

	public static function GetPropertyFieldHtml($arProperty, $arValue, $strHTMLControlName) {
		if (empty($arValue['VALUE'])) {
			$newID = (!isset($_REQUEST['ID']) || (int)$_REQUEST['ID'] <= 0 || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'copy'));
			if ($newID) {
				$arValue['VALUE'] = $arProperty['DEFAULT_VALUE'];
			} else {
				$arValue['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
			}
			unset($index);
		}
		if ($arValue['VALUE'] != ASD_UT_CHECKBOX_VAL_TRUE) {
			$arValue['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}

		return '<input type="hidden" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_N" value="'.ASD_UT_CHECKBOX_VAL_FALSE.'" />'.
			'<input type="checkbox" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_Y" value="'.ASD_UT_CHECKBOX_VAL_TRUE.'" '.($arValue['VALUE'] == ASD_UT_CHECKBOX_VAL_TRUE ? 'checked="checked"' : '').'/>';
	}

	public static function GetAdminListViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$arSettings = static::PrepareSettings($arProperty);
		if ($arValue['VALUE'] != ASD_UT_CHECKBOX_VAL_TRUE && $arValue['VALUE'] != ASD_UT_CHECKBOX_VAL_FALSE) {
			return GetMessage('ASD_UT_CHECKBOX_VALUE_ABSENT');
		}
		return htmlspecialcharsex($arSettings['VIEW'][$arValue['VALUE']]);
	}

	public static function GetAdminFilterHTML($arProperty, $strHTMLControlName) {

		$arSettings = static::PrepareSettings($arProperty);

		$strCurValue = '';
		if (array_key_exists($strHTMLControlName['VALUE'], $_REQUEST) && ($_REQUEST[$strHTMLControlName['VALUE']] == ASD_UT_CHECKBOX_VAL_TRUE || $_REQUEST[$strHTMLControlName['VALUE']] == ASD_UT_CHECKBOX_VAL_FALSE)) {
			$strCurValue = $_REQUEST[$strHTMLControlName['VALUE']];
		} elseif (isset($GLOBALS[$strHTMLControlName['VALUE']]) && ($GLOBALS[$strHTMLControlName['VALUE']] == ASD_UT_CHECKBOX_VAL_TRUE || $GLOBALS[$strHTMLControlName['VALUE']] == ASD_UT_CHECKBOX_VAL_FALSE)) {
			$strCurValue = $GLOBALS[$strHTMLControlName['VALUE']];
		}

		$strResult = '<select name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="filter_'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'">';
		$strResult .= '<option value=""'.(empty($strCurValue) ? ' selected="selected"' : '').'>'.htmlspecialcharsex(GetMessage('ASD_UT_CHECKBOX_VALUE_EMPTY')).'</option>';
		foreach ($arSettings['VIEW'] as $key => $value) {
			$strResult .= '<option value="'.htmlspecialcharsbx($key).'"'.($key == $strCurValue ? ' selected="selected"' : '').'>'.htmlspecialcharsex($value).'</option>';
		}
		$strResult .= '</select>';

		return $strResult;
	}

	public static function GetPublicViewHTML($arProperty, $arValue, $strHTMLControlName) {
		$arSettings = static::PrepareSettings($arProperty);
		if ($arValue['VALUE'] != ASD_UT_CHECKBOX_VAL_TRUE && $arValue['VALUE'] != ASD_UT_CHECKBOX_VAL_FALSE) {
			return GetMessage('ASD_UT_CHECKBOX_VALUE_ABSENT');
		}
		return htmlspecialcharsex($arSettings['VIEW'][$arValue['VALUE']]);
	}

	public static function GetPublicEditHtml($arProperty, $arValue, $strHTMLControlName) {
		if (empty($arValue['VALUE'])) {
			$arValue['VALUE'] = $arProperty['DEFAULT_VALUE'];
		}
		if ($arValue['VALUE'] != ASD_UT_CHECKBOX_VAL_TRUE) {
			$arValue['VALUE'] = ASD_UT_CHECKBOX_VAL_FALSE;
		}

		if (CASDiblockVersion::isIblockNewGridv18()) {
			$strResult = '<input type="checkbox" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_Y" value="'.ASD_UT_CHECKBOX_VAL_TRUE.'" '.($arValue['VALUE'] == ASD_UT_CHECKBOX_VAL_TRUE ? 'checked="checked"' : '').'/>';
		} else {
			$strResult = '<input type="hidden" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_N" value="'.ASD_UT_CHECKBOX_VAL_FALSE.'" />'.
				'<input type="checkbox" name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="'.$strHTMLControlName['VALUE'].'_Y" value="'.ASD_UT_CHECKBOX_VAL_TRUE.'" '.($arValue['VALUE'] == ASD_UT_CHECKBOX_VAL_TRUE ? 'checked="checked"' : '').'/>';
		}
		return $strResult;
	}

	public static function GetPublicFilterHTML($arProperty, $strHTMLControlName)
	{
		$arSettings = static::PrepareSettings($arProperty);
		$strCurValue = '';
		if (isset($_REQUEST[$strHTMLControlName['VALUE']]) && ($_REQUEST[$strHTMLControlName['VALUE']] == ASD_UT_CHECKBOX_VAL_TRUE || $_REQUEST[$strHTMLControlName['VALUE']] == ASD_UT_CHECKBOX_VAL_FALSE)) {
			$strCurValue = $_REQUEST[$strHTMLControlName['VALUE']];
		}
		elseif (
			isset($strHTMLControlName['GRID_ID'])
			&& isset($_SESSION['main.interface.grid'][$strHTMLControlName['GRID_ID']]['filter'][$strHTMLControlName['VALUE']])
		) {
			$strCurValue = $_SESSION['main.interface.grid'][$strHTMLControlName['GRID_ID']]['filter'][$strHTMLControlName['VALUE']];
		}

		$strResult = '<select name="'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'" id="filter_'.htmlspecialcharsbx($strHTMLControlName['VALUE']).'">';
		$strResult .= '<option value=""'.(empty($strCurValue) ? ' selected="selected"' : '').'>'.htmlspecialcharsex(GetMessage('ASD_UT_CHECKBOX_VALUE_EMPTY')).'</option>';
		foreach ($arSettings['VIEW'] as $key => $value) {
			$strResult .= '<option value="'.htmlspecialcharsbx($key).'"'.($key == $strCurValue ? ' selected="selected"' : '').'>'.htmlspecialcharsex($value).'</option>';
		}
		$strResult .= '</select>';

		return $strResult;
	}

	public static function PrepareSettings($arFields) {
		$arDefView = self::GetDefaultListValues();
		$arView = array();
		if (
			array_key_exists('USER_TYPE_SETTINGS', $arFields) && is_array($arFields['USER_TYPE_SETTINGS']) &&
			array_key_exists('VIEW', $arFields['USER_TYPE_SETTINGS']) &&
			!empty($arFields['USER_TYPE_SETTINGS']['VIEW']) && is_array($arFields['USER_TYPE_SETTINGS']['VIEW'])
		) {
			$arView = $arFields['USER_TYPE_SETTINGS']['VIEW'];
		}

		if (empty($arView)) {
			$arView = $arDefView;
		}

		return array(
			'VIEW' => $arView
		);
	}

	public static function GetUIFilterProperty($arProperty, $strHTMLControlName, &$fields)
	{
		$settings = static::PrepareSettings($arProperty);
		if (empty($settings['VIEW'])) {
			return;
		}
		$fields['type'] = 'list';
		$fields['items'] = array();
		foreach ($settings['VIEW'] as $index => $value) {
			$fields['items'][$index] = $value;
		}
		unset($index, $value, $settings);
	}

	protected static function GetDefaultListValues() {
		return array(
			ASD_UT_CHECKBOX_VAL_FALSE => GetMessage('ASD_UT_CHECKBOX_VALUE_N'),
			ASD_UT_CHECKBOX_VAL_TRUE => GetMessage('ASD_UT_CHECKBOX_VALUE_Y')
		);
	}
}