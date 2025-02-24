<?
//Подключаем ядро Битрикс и главный модуль
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
global $USER;

echo \Dev\Helpers\FavoriteHelper::add($_COOKIE['PHPSESSID'] ?: '', $USER->IsAuthorized() ? $USER->GetID() : 0, $_POST['itemId']);

?>