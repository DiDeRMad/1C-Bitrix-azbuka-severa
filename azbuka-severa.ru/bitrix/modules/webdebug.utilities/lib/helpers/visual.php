<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class Visual {

	/**
	 *	Show note
	 */
	public static function showNote($strNote, $bCompact=false, $bCenter=false, $bReturn=false) {
		if($bReturn){
			ob_start();
		}
		$arClass = [];
		if($bCompact){
			$arClass[] = 'wdu_note_compact';
		}
		if($bCenter){
			$arClass[] = 'wdu_note_center';
		}
		print '<div class="'.implode(' ', $arClass).'">';
		print BeginNote();
		print $strNote;
		print EndNote();
		print '</div>';
		if($bReturn){
			return ob_get_clean();
		}
	}
	
	/**
	 *	Show success
	 */
	public static function showSuccess($strMessage=null, $strDetails=null) {
		ob_start();
		\CAdminMessage::showMessage([
			'MESSAGE' => $strMessage,
			'DETAILS' => $strDetails,
			'HTML' => true,
			'TYPE' => 'OK',
		]);
		return ob_get_clean();
	}
	
	/**
	 *	Show error
	 */
	public static function showError($strMessage=null, $strDetails=null) {
		ob_start();
		\CAdminMessage::showMessage([
			'MESSAGE' => $strMessage,
			'DETAILS' => $strDetails,
			'HTML' => true,
		]);
		return ob_get_clean();
	}
	
	/**
	 *	Show error
	 */
	public static function showHeading($strMessage, $bNoMargin=false){
		$strResult = '';
		$strClass = $bNoMargin ? ' class="wdu_table_nomargin"' : '';
		$strResult .= '<table style="width:100%"'.$strClass.'><tbody><tr class="heading"><td>'
			.$strMessage.'</td></tr></tbody></table>';
		return $strResult;
	}
	
	/**
	 *	Show hint
	 */
	public static function showHint($strText) {
		$strCode = toLower(Helper::randString());
		$strText = str_replace('"', '\"', $strText);
		$strText = str_replace("\r", '', $strText);
		$strText = str_replace("\n", ' ', $strText);
		$strResult = '<span id="hint_'.$strCode.'"><span></span></span>'
			.'<script>BX.hint_replace(BX("hint_'.$strCode.'").childNodes[0], "'.$strText.'");</script>';
		return $strResult;
	}
	
	/**
	 *	Show progress
	 */
	public static function showProgress($intValue, $intMax, $strTitle=null, $strText1=null, $strText2=null, $arBtn=[]) {
		ob_start();
		if(is_numeric($intValue) && $intValue >= 0 && is_numeric($intMax) && $intMax >= 0) {
			print '<div class="wdu_message_progress">';
			\CAdminMessage::showMessage([
				'MESSAGE' => static::strlen($strTitle) ? $strTitle : ' ',
				'DETAILS' => sprintf('<div class="%s">%s</div><div class="%s">%s</div><div class="%s">%s</div>', 
					'acrit_core_message_progress_top', $strText1, 
					'acrit_core_message_progress_middle', '#PROGRESS_BAR#', 
					'acrit_core_message_progress_bottom', $strText2),
				'HTML' => true,
				'TYPE' => 'PROGRESS',
				'PROGRESS_TOTAL' => $intMax,
				'PROGRESS_VALUE' => $intValue,
				'BUTTONS' => $arBtn
			]);
			print '</div>';
		}
		else{
			return static::showNote(sprintf('<div style="margin-bottom:4px;"><b>%s</b></div><div>%s</div><div>%s</div>', 
				$strTitle, $strText1, $strText2), true, false, true);
		}
		return ob_get_clean();
	}
	
	/**
	 *	Add notify
	 */
	public static function addNotify($strModuleId, $strMesage, $strTag, $bClose=true){
		$arParams = [
			'MODULE_ID' => $strModuleId,
			'MESSAGE' => $strMesage,
			'TAG' => $strTag,
			'ENABLE_CLOSE' => $bClose ? 'Y' : 'N',
		];
		static::deleteNotify($strTag);
		return \CAdminNotify::add($arParams);
	}
	
	/**
	 *	Delete notify
	 */
	public static function deleteNotify($strTag){
		return \CAdminNotify::deleteByTag($strTag);
	}
	
	/**
	 *	Get notify list
	 */
	public static function getNotifyList($strModuleId){
		$arResult = [];
		$arSort = [
			'ID' => 'ASC',
		];
		$arFilter = [
			'MODULE_ID' => $strModuleId,
		];
		$resItems = \CAdminNotify::getList($arSort, $arFilter);
		while($arItem = $resItems->getNext()){
			$arResult[] = $arItem;
		}
		return $arResult;
	}

}
