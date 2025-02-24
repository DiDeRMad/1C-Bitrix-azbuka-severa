<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $templateData
 * @var string $templateFolder
 * @var CatalogSectionComponent $component
 */

Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");

use Bitrix\Sale;

global $APPLICATION;
global $USER;

$userId = $USER->IsAuthorized() ? $USER->GetID() : 0;

$itInDelay = \Dev\Helpers\FavoriteHelper::getItems($_COOKIE['PHPSESSID'], $userId ?: "")['UF_ITEMS'];

$basketIds = [];

$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
$basketItems = [];

foreach ($basket as $basketItem) {
    $basketIds[] = $basketItem->getField('PRODUCT_ID');
    $basketItems[$basketItem->getField('PRODUCT_ID')] =  $basketItem->getQuantity();
}
?>
