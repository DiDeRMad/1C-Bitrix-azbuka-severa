<?
//Подключаем ядро Битрикс и главный модуль
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader,
    Dev\Helpers\HLBlockHelper as HLBlockHelper,
    Dev\Helpers\FavoriteHelper as FavoriteHelper;

global $USER;

$entity = HLBlockHelper::GetEntityDataClass(HIGHLOAD_FAVORITE_ID);
$uuid = $_COOKIE['PHPSESSID'] ?: '';
$userId = $USER->IsAuthorized() ? $USER->GetID() : 0;
$arOldItems = FavoriteHelper::getItems($uuid, $userId);
if ($arOldItems) {
    $result = $entity::update($arOldItems['ID'], array(
        'UF_UUID' => $uuid,
        'UF_USERID' => $userId,
        'UF_ITEMS' => '',
    ));
} else {
    $result = $entity::add(array(
        'UF_UUID' => $uuid,
        'UF_USERID' => $userId,
        'UF_ITEMS' => '',
    ));
    $message = "add";
}