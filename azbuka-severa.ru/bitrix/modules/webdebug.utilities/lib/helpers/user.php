<?
namespace WD\Utilities\Helpers;

use
	\WD\Utilities\Helper;

Helper::loadMessages();

final class User {
	
	/**
	 *	Get user title
	 */
	public static function getUserTitle($intUserId, $intMode=1){
		$intUserId = intVal($intUserId);
		$intMode = $intMode >= 1 && $intMode <= 4 ? $intMode : 1;
		if($intUserId > 0){
			$resUser = \CUser::getByID($intUserId);
			if($arUser = $resUser->fetch()){
				$strTitle = \CUser::FormatName(\CSite::getNameFormat(), $arUser, true, false);
				$strLink = '<a title="'.Helper::getMessage('MAIN_EDIT_USER_PROFILE').'" href="/bitrix/admin/user_edit.php?ID='.$arUser['ID'].'&lang='.LANGUAGE_ID.'" target="_blank">'.$arUser['ID'].'</a>';
				switch($intMode){
					case 1:
						$strTitle = sprintf('%s',$strTitle);
						break;
					case 2:
						$strTitle = sprintf('(%s) %s', $arUser['LOGIN'], $strTitle);
						break;
					case 3:
						$strTitle = sprintf('[%s] (%s) %s', $arUser['ID'], $arUser['LOGIN'], $strTitle);
						break;
					case 4:
						$strTitle = sprintf('[%s] (%s) %s', $strLink, $arUser['LOGIN'], $strTitle);
						break;
				}
				return $strTitle;
			}
		}
		return false;
	}

}
