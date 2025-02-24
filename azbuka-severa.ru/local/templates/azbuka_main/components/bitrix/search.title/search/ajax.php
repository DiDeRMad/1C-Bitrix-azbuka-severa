<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?php
if (!empty($arResult["CATEGORIES"]) && $arResult['CATEGORIES_ITEMS_EXISTS']):
    unset($arResult['CATEGORIES']['all']);

    $arItemIds = [];
    $arItemsData = [];

    // Collecting product IDs
    foreach ($arResult['CATEGORIES'] as $arCategory) {
        foreach ($arCategory['ITEMS'] as $arItem) {
            $arItemIds[] = $arItem['ITEM_ID'];
        }
    }

    // Getting product data from the database
    $resDB = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => IBLOCK_CATALOG_MAIN_ID, '=ID' => $arItemIds, 'ACTIVE' => 'Y'],
        false,
        false,
        ['ID', 'IBLOCK_ID', 'PROPERTY_weight', 'PROPERTY_DISCOUNT_PERCENT', 'CATALOG_PRICE_1', 'PROPERTY_NAME_H1', 'NAME', 'PROPERTY_QUANTITY']
    );

    while ($item = $resDB->Fetch()) {
        // Processing weight
        if ($item['PROPERTY_WEIGHT_VALUE']) {
            $item['PROPERTY_WEIGHT_VALUE'] *= 1000;
            if ($item['PROPERTY_WEIGHT_VALUE'] % 1000 == 0) {
                $item['PROPERTY_WEIGHT_VALUE'] = $item['PROPERTY_WEIGHT_VALUE'] / 1000;
                $item['PROPERTY_WEIGHT_VALUE_TYPE'] = 'кг';
            } else {
                $item['PROPERTY_WEIGHT_VALUE_TYPE'] = 'г';
            }
        }

        // Set base price
        $item['PRICE'] = $item['CATALOG_PRICE_1'];

        // Processing discount
        if ($item['PROPERTY_DISCOUNT_PERCENT_VALUE']) {
            $percent = $item['PROPERTY_DISCOUNT_PERCENT_VALUE'] / 100;
            $item['DISCOUNT_PRICE'] = $item['PRICE'] - $item['PRICE'] * $percent;
        }

        // Adding product data
        $arItemsData[$item['ID']] = [
            'WEIGHT' => [
                'VALUE' => $item['PROPERTY_WEIGHT_VALUE'],
                'VALUE_TYPE' => $item['PROPERTY_WEIGHT_VALUE_TYPE']
            ],
            'PRICE' => CurrencyFormat($item['PRICE'], 'RUB'),
            'DISCOUNT_PRICE' => isset($item['DISCOUNT_PRICE']) ? CurrencyFormat($item['DISCOUNT_PRICE'], 'RUB') : null,
            'NAME' => $item['PROPERTY_NAME_H1_VALUE'] ?: $item['NAME'],
            'AVAILABLE' => ($item['PROPERTY_QUANTITY_VALUE'] > 0) ? 1 : 0 // Adding availability field
        ];
    }

    // Exclude items with 'TYPE' == 'all' before sorting
    foreach ($arResult['CATEGORIES'] as &$arCategory) {
        // Filter out items with 'TYPE' == 'all'
        $arCategory['ITEMS'] = array_filter($arCategory['ITEMS'], function($arItem) {
            return $arItem['TYPE'] != 'all';
        });

        // Proceed with sorting
        usort($arCategory['ITEMS'], function($a, $b) use ($arItemsData) {
            $availA = $arItemsData[$a['ITEM_ID']]['AVAILABLE'];
            $availB = $arItemsData[$b['ITEM_ID']]['AVAILABLE'];

            if ($availA != $availB) {
                // Sort by availability (available items first)
                return $availB - $availA;
            }

            // If availability is the same, compare 'SORT'
            $sortA = $a['SORT'];
            $sortB = $b['SORT'];

            if ($sortA == $sortB) return 0;
            return ($sortA < $sortB) ? -1 : 1;
        });
    }
    unset($arCategory); // Clear reference

    // Output results
    ?>
    <div class="header-search-dropdown__list">
        <?php foreach ($arResult['CATEGORIES'] as $arCategory): ?>
            <?php foreach ($arCategory['ITEMS'] as $arItem): ?>
                <div class="header-search-dropdown__item">
                    <a href="<?= $arItem['URL'] ?>" class="header-search-dropdown__title"><?= $arItemsData[$arItem['ITEM_ID']]['NAME'] ?></a>
                    <div class="header-search-dropdown__meta">
                       <?php if (isset($arItemsData[$arItem['ITEM_ID']]['WEIGHT']['VALUE_TYPE']) && $arItemsData[$arItem['ITEM_ID']]['WEIGHT']['VALUE'] > 0): ?>
							<p class="header-search-dropdown__counter">
								<?= $arItemsData[$arItem['ITEM_ID']]['WEIGHT']['VALUE'] ?> <?= $arItemsData[$arItem['ITEM_ID']]['WEIGHT']['VALUE_TYPE'] ?>
							</p>
						<?php elseif (isset($arItemsData[$arItem['ITEM_ID']]['WEIGHT']['VALUE']) && $arItemsData[$arItem['ITEM_ID']]['WEIGHT']['VALUE'] > 0): ?>
							<p class="header-search-dropdown__counter"><?= $arItemsData[$arItem['ITEM_ID']]['WEIGHT']['VALUE'] ?></p>
						<?php endif; ?>

                        <div class="header-search-dropdown__price-wrapper">
                            <?php if ($arItemsData[$arItem['ITEM_ID']]['DISCOUNT_PRICE'] && $arItemsData[$arItem['ITEM_ID']]['DISCOUNT_PRICE'] < $arItemsData[$arItem['ITEM_ID']]['PRICE']): ?>
                                <p class="header-search-dropdown__price-current"><?= $arItemsData[$arItem['ITEM_ID']]['DISCOUNT_PRICE'] ?></p>
                                <p class="header-search-dropdown__price-old"><?= $arItemsData[$arItem['ITEM_ID']]['PRICE'] ?></p>
                            <?php else: ?>
                                <p class="header-search-dropdown__price-current"><?= $arItemsData[$arItem['ITEM_ID']]['PRICE'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
    <a href="/search/?q=<?= $arResult['query'] ?>" class="header-search-dropdown__all">
        <span>Все результаты</span>
        <svg class="icon--24">
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/assets/img/sprite.svg#caret-right-icon"></use>
        </svg>
    </a>

<?php endif; ?>