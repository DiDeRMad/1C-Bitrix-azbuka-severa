<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/prolog.php");

$APPLICATION->SetTitle("Управление сортировкой-ценой");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

CJSCore::Init(array("jquery"));
\Bitrix\Main\UI\Extension::load("ui.buttons");

$arSections = [
        '' => 'Не выбрано'
];
$dbSections = CIBlockSection::GetList(['ID' => 'ASC'], ['IBLOCK_ID' => '5', 'ACTIVE' => 'Y'], false, ['IBLOCK_ID', 'ID', 'ACTIVE', 'NAME']);
while ($arSec = $dbSections->fetch()) {
    $arSections[$arSec['ID']] = '[' . $arSec['ID'] . '] ' . $arSec['NAME'];
}

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.filter',
    '',
    [
        'FILTER_ID'          => 'elements_list',
        'GRID_ID'            => 'elements_list',
        'FILTER'             => [
            ['id' => 'ID', 'name' => 'ID', 'type' => 'number', 'default' => true],
            ['id' => 'NAME', 'name' => 'Название', 'type' => 'string', 'default' => true],
            ['id' => 'SORT', 'name' => 'Сортировка', 'type' => 'number', 'default' => true],
            ['id' => 'PRICE', 'name' => 'Цена', 'type' => 'number', 'default' => true],
            ['id' => 'IBLOCK_SECTION_ID', 'name' => 'Раздел', 'type' => 'list','default' => true, 'items' => $arSections]
        ],
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL'       => true,
    ]
);
$arSort = ['SORT' => 'ASC'];
$arFilter = ['IBLOCK_ID' => '5'];
$nav_params = false;
// Получаем данные для фильтрации.
$filterOptions = new \Bitrix\Main\UI\Filter\Options("elements_list");
$filterFields = $filterOptions->getFilter([
    ['id' => 'ID', 'name' => 'ID', 'type' => 'number', 'default' => true],
    ['id' => 'NAME', 'name' => 'Название', 'type' => 'string', 'default' => true],
    ['id' => 'SORT', 'name' => 'Сортировка', 'type' => 'number', 'default' => true],
    ['id' => 'PRICE', 'name' => 'Цена', 'type' => 'number', 'default' => true],
    ['id' => 'IBLOCK_SECTION_ID', 'name' => 'Раздел', 'type' => 'list', 'default' => true]
]);
$filter = [];
foreach ($filterFields as $key => $value)
{
    if ($key === 'FIND') {
        $arFilter['NAME'] = '%' . $value . '%';
    }
    if ($key === 'IBLOCK_SECTION_ID') {
        $arFilter['IBLOCK_SECTION_ID'] = $value;
    }
    if ($key === 'ID_numsel') {
        $arFilter[] = [
            'LOGIC' => 'AND',
            [
                '>=' . 'ID' => $filterFields['ID_from'] != '' ? $filterFields['ID_to'] : 0
            ],
            [
                '<=' . 'ID' => $filterFields['ID_to']
            ]
        ];
    }
    if ($key === 'NAME') {
        $arFilter[$key] = '%' . $value . '%';
    }
    if ($key === 'SORT_numsel') {
        $arFilter[] = [
            'LOGIC' => 'AND',
            [
                '>=' . 'SORT' => $filterFields['SORT_from'] != '' ? $filterFields['SORT_from'] : 0
            ],
            [
                '<=' . 'SORT' => $filterFields['SORT_to']
            ]
        ];
    }
    if ($key === 'PRICE_numsel') {
        $arFilter[] = [
            'LOGIC' => 'AND',
            [
                '>=' . 'CATALOG_PRICE_1' => $filterFields['PRICE_from']  != '' ? $filterFields['PRICE_from'] : 0
            ],
            [
                '<=' . 'CATALOG_PRICE_1' => $filterFields['PRICE_to']
            ]
        ];
    }
}
$grid_options     = new Bitrix\Main\Grid\Options('elements_list');
$nav_params = $grid_options->GetNavParams();
$sort             = $grid_options->GetSorting();
$arSort           = [array_keys($sort['sort'])[0] => $sort['sort'][array_keys($sort['sort'])[0]]];

$dbElements = CIBlockElement::GetList($arSort, $arFilter, false, $nav_params, ['IBLOCK_ID', 'ID', 'NAME', 'SORT', 'CATALOG_GROUP_1']);
while ($arElem = $dbElements->fetch()) {
    $list[$key++]['data'] = [
        'ID' => $arElem['ID'],
        'NAME' => '<a href="/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=5&type=catalog&lang=ru&ID=' . $arElem['ID'] . '" target="_blank">' . $arElem['NAME'] . '</a>',
        'SORT' => '<input type="number" class="valchange" value="' . $arElem['SORT'] . '" name="sort-' . $arElem['ID'] . '" data-elid="' . $arElem['ID'] . '" />',
        'PRICE' =>  '<input type="number" class="valchange" value="' . $arElem['CATALOG_PRICE_1'] . '" name="price-' . $arElem['ID'] . '" data-elid="' . $arElem['ID'] . '" />',
        'COUNT' => '<input type="number" class="valchange" value="' . $arElem['CATALOG_QUANTITY'] . '" name="count-' . $arElem['ID'] . '" data-elid="' . $arElem['ID'] . '" />',
    ];
}
$countElements = CIBlockElement::GetList([], $arFilter, false, false, ['IBLOCK_ID', 'ID', 'NAME', 'SORT', 'CATALOG_GROUP_1'])->AffectedRowsCount();?>

<?php

$nav = new Bitrix\Main\UI\PageNavigation('elements_list');
$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    [
        'GRID_ID' => 'elements_list',
        'COLUMNS' => [
            ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
            ['id' => 'NAME', 'name' => 'Название', 'sort' => 'NAME', 'default' => true],
            ['id' => 'SORT', 'name' => 'Сортировка', 'sort' => 'SORT', 'default' => true],
            ['id' => 'PRICE', 'name' => 'Цена', 'sort' => 'CATALOG_PRICE_1', 'default' => true],
            ['id' => 'COUNT', 'name' => 'Доступное количество', 'sort' => 'CATALOG_QUANTITY', 'default' => true]
        ],
        'ROWS' => $list,
        'SHOW_ROW_CHECKBOXES' => true,
        'NAV_OBJECT' => $nav,
        'AJAX_MODE' => 'Y',
        'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
        'PAGE_SIZES' => [
            ['NAME' => "5", 'VALUE' => '5'],
            ['NAME' => '10', 'VALUE' => '10'],
            ['NAME' => '20', 'VALUE' => '20'],
            ['NAME' => '50', 'VALUE' => '50'],
            ['NAME' => '100', 'VALUE' => '100']
        ],
        'AJAX_OPTION_JUMP'          => 'N',
        'TOTAL_ROWS_COUNT'          => $countElements,
        'SHOW_CHECK_ALL_CHECKBOXES' => true,
        'SHOW_ROW_ACTIONS_MENU'     => true,
        'ACTION_PANEL'              => [
            'GROUPS' => [
                'TYPE' => [
                    'ITEMS' => [
                        [
                            'ID'    => 'save',
                            'TYPE'  => 'BUTTON',
                            'TEXT' => 'Сохранить',
                            'CLASS' => 'ui-btn ui-btn-success ui-btn-success-custom',
                            'ONCHANGE' => [
                                [
                                    'ACTION' => 'CALLBACK',
                                    'CONFIRM' => false,
                                    'DATA' => [
                                        [
                                            'JS' => "
                                            var arElems = [];
                                            let elids = BX.Main.gridManager.getInstanceById('elements_list').getRows().getSelectedIds();
                                            for (let id of elids) { 
                                                let sort = $('input[name=\"sort-' + id + '\"]').val();
                                                let price = $('input[name=\"price-' + id + '\"]').val();
                                                let count = $('input[name=\"count-' + id + '\"').val();
                                                arElems[id] = {
                                                    'id': id,
                                                    'sort': sort,
                                                    'price': price,
                                                    'count': count
                                                };
                                               
                                            }
                                            BX.ajax.post('/local/ajax/sortpriceupdate.php', {arElems}, function (res) {
                                                   setTimeout(function() { location.reload(); }, 1000); 
                                                });"
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ],
                ]
            ],
        ],
        'SHOW_GRID_SETTINGS_MENU'   => true,
        'SHOW_NAVIGATION_PANEL'     => true,
        'SHOW_PAGINATION'           => true,
        'SHOW_SELECTED_COUNTER'     => true,
        'SHOW_TOTAL_COUNTER'        => true,
        'SHOW_PAGESIZE'             => true,
        'SHOW_ACTION_PANEL'         => true,
        'ALLOW_COLUMNS_SORT'        => true,
        'ALLOW_COLUMNS_RESIZE'      => true,
        'ALLOW_HORIZONTAL_SCROLL'   => true,
        'ALLOW_SORT'                => true,
        'ALLOW_PIN_HEADER'          => true,
        'AJAX_OPTION_HISTORY'       => 'N'
    ]
);
?>

<script>
    $(document).ready(function() {
        $(document).on('change', '.valchange', function() {
            let elid = $(this).attr('data-elid');

            if (!$('#checkbox_elements_list_' + elid).prop('checked')) {
                $('#checkbox_elements_list_' + elid).click();
                $('#checkbox_elements_list_' + elid).attr('checked', 'checked');
                $('.main-grid-row[data-id="' + elid + '"]').addClass('main-grid-row-checked');
                $('.main-grid-action-panel').removeClass('main-grid-disable');
                $('.main-grid-action-panel').addClass('main-grid-fixed-bottom');
                $('.main-grid-action-panel').attr('style', 'transform: translateY(0px); width: 678px; transition: transform 200ms ease 0s;');
            }
        })
    })
</script>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
