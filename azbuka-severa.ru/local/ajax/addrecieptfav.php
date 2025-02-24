<?php
//Подключаем ядро Битрикс и главный модуль
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
global $USER;
$userId = $USER->IsAuthorized() ? $USER->GetID() : 0;
$session = $_COOKIE['PHPSESSID'];

$message = \Dev\Helpers\FavoriteRecieptsHelper::add($session, $userId, $_POST['recId']);

echo json_encode($message);