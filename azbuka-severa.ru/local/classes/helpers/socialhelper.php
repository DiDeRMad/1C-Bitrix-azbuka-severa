<?php
namespace Dev\Helpers;

/**
 * Class SocialHelper
 * @package Dev\Helpers
 */
class SocialHelper
{
    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * Получаем соц. сети
     */
    public static function getSocial()
    {
        return self::getInfo(HIGHLOAD_SOCIAL_TYPE_ICONS);
    }

    /**
     * @param int $type
     * @param string $paramName
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException Общий метод по получению инфы из hl-блока
     */
    private static function getInfo(int $type = HIGHLOAD_SOCIAL_TYPE_DEFAULT, string $paramName = "")
    {
        $arResult = [];
        $arFilter = ['UF_TYPE' => $type];
        if ($type == HIGHLOAD_SOCIAL_TYPE_DEFAULT) {
            $arFilter['UF_CODE'] = $paramName;
        }

        $arData = self::exec(HIGHLOAD_SOCIAL_ID, $arFilter);

        if ($arData) {
            foreach ($arData as $arItem) {
                $arResult[] = [
                    'TEXT' => $arItem['UF_TEXT'],
                    'LINK' => $arItem['UF_LINK'],
                    'ICON' => \CFile::GetPath($arItem['UF_ICON'])
                ];
            }
        }

        if ($arResult && $type == HIGHLOAD_SOCIAL_TYPE_DEFAULT) {
            $arResult = $arResult[0];
        }

        return $arResult ?: "Результатов нет";
    }

    public static function exec($hlblockId, $arFilter = [])
    {
        $arResult = [];
        $hlEntity = HLBlockHelper::GetEntityDataClass($hlblockId);
        $resDB = $hlEntity::getList(
            [
                'select' => ['*'],
                'filter' => $arFilter,
                'cache' => array(
                    'ttl' => 36000000,
                    'cache_joins' => true,
                )
            ]
        );

        while ($arData = $resDB->fetch()) {
            $arResult[] = $arData;
        }

        return $arResult;
    }

    /**
     * @param $paramName
     * Получаем отдельный элемент
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getDefault($paramName)
    {
        return self::getInfo(HIGHLOAD_SOCIAL_TYPE_DEFAULT, $paramName);
    }

    public static function getAdvantages()
    {
        return self::exec(HIGHLOAD_ADVANTAGES_ID);
    }
}