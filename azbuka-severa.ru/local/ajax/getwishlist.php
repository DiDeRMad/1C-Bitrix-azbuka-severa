<?
//Подключаем ядро Битрикс и главный модуль
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
global $USER;

$itInDelay = \Dev\Helpers\FavoriteHelper::getItems($_POST['cookie'], $_POST['userId'] ?: "")['UF_ITEMS'];

echo json_encode($itInDelay);

