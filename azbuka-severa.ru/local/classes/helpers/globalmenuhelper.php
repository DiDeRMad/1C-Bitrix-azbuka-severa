<?php

namespace Dev\Helpers;

use Bitrix\Main\Loader;

Loader::includeModule('iblock');

class GlobalMenuHelper
{
    const INFORMULA_GLOBAL_MENU_ID = 'global_menu_informula';
    const INFORMULA_MENU_ID = 'menu_informula';

    public static function buildInformulaGlobalMenu(array &$arGlobalMenu)
    {
        if (!array_key_exists(self::INFORMULA_GLOBAL_MENU_ID, $arGlobalMenu)) {
            $arGlobalMenu[GlobalMenuHelper::INFORMULA_GLOBAL_MENU_ID] = [
                'menu_id' => GlobalMenuHelper::INFORMULA_MENU_ID,
                'text' => 'Acnaucer',
                'title' => 'Acnaucer',
                'sort' => 1,
                'items_id' => GlobalMenuHelper::INFORMULA_GLOBAL_MENU_ID,
                'items' => [
                ]
            ];
        }
    }
}