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
$itemsIds = [];
$itemsIds = array_keys($basketItems);
$basketItems2 = [];
if (!empty($itemsIds)) {
    $resDB = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, 'ID' => $itemsIds],
        false,
        false,
        ['ID', 'PROPERTY_STEP_TO_ADD']
    );


    while ($item = $resDB->Fetch()) {
        $measureData = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($item['ID']);
        $item['RATIO'] = $item['PROPERTY_STEP_TO_ADD_VALUE'] ?: $measureData[$item['ID']];

        $basketItems2[$item['ID']] = $basketItems[$item['ID']] / $item['RATIO'];
    }
}

?>

<script>
    function add_delay_prod() {
        var basketItem = <?= CUtil::PhpToJSObject($basketItems, false, true)?>;
        var basketItem2 = <?= CUtil::PhpToJSObject($basketItems2, false, true)?>;
        var delyedItems = <?= CUtil::PhpToJSObject($itInDelay, false, true)?>;

        console.log(basketItem2);
        console.log(basketItem);

        $(".product-item__favorite").each(function (index, el) {
            if ($.inArray("" + $(el).data('id') + "", delyedItems) >= 0) {
                $(el).addClass('active');
            }
        });

        $(".product-item__btn-cart").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                $(el).removeClass('active');
            }
        });

        $(".product-item__counter-block").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                $(el).addClass('active');
            }
        });


       /* $(".js-really-quantity").each(function (index, el) {
            if (basketItem2[$(el).data('id')] !== undefined) {
                $(el).val(basketItem2[$(el).data('id')]);
            }
        });*/

        $(".product-counter__input").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                var type = $(el).data('type');

                $(el).val(basketItem[$(el).data('id')] + ' ' + type);
            }
        });
    }

    add_delay_prod();
</script>
