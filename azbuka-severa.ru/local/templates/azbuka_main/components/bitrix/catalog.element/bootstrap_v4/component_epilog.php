<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

/**
 * @var array $templateData
 * @var array $arParams
 * @var string $templateFolder
 * @global CMain $APPLICATION
 */

global $APPLICATION;
/*
if (!empty($templateData['TEMPLATE_LIBRARY']))
{
	$loadCurrency = false;

	if (!empty($templateData['CURRENCIES']))
	{
		$loadCurrency = Loader::includeModule('currency');
	}

	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency)
	{
		*/?><!--
		<script>
			BX.Currency.setCurrencies(<?/*=$templateData['CURRENCIES']*/?>);
		</script>
		<?/*
	}
}

if (isset($templateData['JS_OBJ']))
{
	*/?>
	<script>
		BX.ready(BX.defer(function(){
			if (!!window.<?/*=$templateData['JS_OBJ']*/?>)
			{
				window.<?/*=$templateData['JS_OBJ']*/?>.allowViewedCount(true);
			}
		}));
	</script>

	<?/*
	// check compared state
	if ($arParams['DISPLAY_COMPARE'])
	{
		$compared = false;
		$comparedIds = array();
		$item = $templateData['ITEM'];

		if (!empty($_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]))
		{
			if (!empty($item['JS_OFFERS']))
			{
				foreach ($item['JS_OFFERS'] as $key => $offer)
				{
					if (array_key_exists($offer['ID'], $_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]['ITEMS']))
					{
						if ($key == $item['OFFERS_SELECTED'])
						{
							$compared = true;
						}

						$comparedIds[] = $offer['ID'];
					}
				}
			}
			elseif (array_key_exists($item['ID'], $_SESSION[$arParams['COMPARE_NAME']][$item['IBLOCK_ID']]['ITEMS']))
			{
				$compared = true;
			}
		}

		if ($templateData['JS_OBJ'])
		{
			*/?>
			<script>
				BX.ready(BX.defer(function(){
					if (!!window.<?/*=$templateData['JS_OBJ']*/?>)
					{
						window.<?/*=$templateData['JS_OBJ']*/?>.setCompared('<?/*=$compared*/?>');

						<?/* if (!empty($comparedIds)): */?>
						window.<?/*=$templateData['JS_OBJ']*/?>.setCompareInfo(<?/*=CUtil::PhpToJSObject($comparedIds, false, true)*/?>);
						<?/* endif */?>
					}
				}));
			</script>
			<?/*
		}
	}

	// select target offer
	$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
	$offerNum = false;
	$offerId = (int)$this->request->get('OFFER_ID');
	$offerCode = $this->request->get('OFFER_CODE');

	if ($offerId > 0 && !empty($templateData['OFFER_IDS']) && is_array($templateData['OFFER_IDS']))
	{
		$offerNum = array_search($offerId, $templateData['OFFER_IDS']);
	}
	elseif (!empty($offerCode) && !empty($templateData['OFFER_CODES']) && is_array($templateData['OFFER_CODES']))
	{
		$offerNum = array_search($offerCode, $templateData['OFFER_CODES']);
	}

	if (!empty($offerNum))
	{
		*/?>
		<script>
			BX.ready(function(){
				if (!!window.<?/*=$templateData['JS_OBJ']*/?>)
				{
					window.<?/*=$templateData['JS_OBJ']*/?>.setOffer(<?/*=$offerNum*/?>);
				}
			});
		</script>
		--><?/*
	}
}*/


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

$itemsIds = array_keys($basketItems);
$basketItems2 = [];
if ($itemsIds) {

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

//$APPLICATION->SetPageProperty('og:type', 'website');
$APPLICATION->SetPageProperty('og:title', $arResult['NAME']);
$APPLICATION->SetPageProperty('og:description', htmlspecialchars(strip_tags($arResult['DESCRIPTION'])));
$APPLICATION->SetPageProperty('og:image', 'https://' . $_SERVER['SERVER_NAME'] . $arResult['PICTURE']);
//$APPLICATION->SetPageProperty('og:url', 'https://' . $_SERVER['SERVER_NAME'] . $arResult['DETAIL_URL']);

?>

<script>
    function add_delay_prod_detail() {
        var basketItem = <?= CUtil::PhpToJSObject($basketItems, false, true)?>;
        var basketItem2 = <?= CUtil::PhpToJSObject($basketItems2, false, true)?>;
        var delyedItems = <?= CUtil::PhpToJSObject($itInDelay, false, true)?>;

        $(".product-card__favorite").each(function (index, el) {
            if ($.inArray("" + $(el).data('id') + "", delyedItems) >= 0) {
                $(el).addClass('active');
                $(el).text('В избранном');
            }
        });

        $(".product-card__buy").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                $(el).removeClass('active');
            }
        });

        $(".go-to-basket").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                $(el).addClass('active');
            }
        });

        $(".product-card__counter-block").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                $(el).addClass('active');
            }
        });

        $(".product-card__control").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                $(el).addClass('active');
            }
        });

        $(".js-really-quantity").each(function (index, el) {
            if (basketItem2[$(el).data('id')] !== undefined) {
                console.log(basketItem2);
                $(el).val(basketItem2[$(el).data('id')]);
            }
        });

        $(".product-counter__input").each(function (index, el) {
            if (basketItem[$(el).data('id')] !== undefined) {
                var type = $(el).data('type');

                $(el).val(basketItem[$(el).data('id')] + ' ' + type);
            }
        });
    }

    add_delay_prod_detail();
</script>
<?php
/*ob_start();
$APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "catalog",
    array(
        "PATH" => "",
        "SITE_ID" => "s1",
        "START_FROM" => "0"
    )
);
$bread = ob_get_clean();

$content = $arResult["CACHED_TPL"];
$content = str_replace("<!-- breadplace -->", $bread, $content);

echo $content;*/