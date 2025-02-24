<?php
namespace Dev\Helpers;

/**
 * Class HLBlockHelper
 * @package Dev\Helpers
 */
class HLBlockHelper
{
    /**
     * @param $HlBlockId
     * @return \Bitrix\Main\ORM\Data\DataManager|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function GetEntityDataClass($HlBlockId)
    {
        \CModule::IncludeModule('highloadblock');

        if (empty($HlBlockId) || $HlBlockId < 1)
        {
            return false;
        }
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($HlBlockId)->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }
}