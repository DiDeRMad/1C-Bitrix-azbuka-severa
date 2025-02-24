<?php

namespace Dev\Helpers;

class FavoriteRecieptsHelper extends FavoriteHelper
{
    public static function getItems($uuid = "", $userId = "")
    {
        if ($uuid && !$userId) {
            $arFilter['UF_UUID'] = $uuid;
        }

        if ($userId) {
            $arFilter['UF_USERID'] = $userId;
        }

        if (!$uuid && !$userId) {
            return false;
        }

        $arData = SocialHelper::exec(9, $arFilter)[0];
        $arData = self::formatData($arData);
        return $arData;
    }



    public static function add($uuid = "", $userId = "", $arItems = 0)
    {
        $result = "";
        $message = "";
        if ($arItems != 0 && ($userId || $uuid)) {
            $entity = HLBlockHelper::GetEntityDataClass(9);
            $arOldItems = FavoriteRecieptsHelper::getItems($uuid, $userId);
            if ($arOldItems) {
                if (!in_array($arItems, $arOldItems['UF_ITEMS'])) {
                    $arOldItems['UF_ITEMS'] = $arOldItems['UF_ITEMS'] ?: [];
                    $arOldItems['UF_ITEMS'][] = $arItems;
                    $arOldItems['UF_ITEMS'] = array_unique( $arOldItems['UF_ITEMS']);
                    $message = "add";
                } else {
                    unset($arOldItems['UF_ITEMS'][array_search($arItems, $arOldItems['UF_ITEMS'])]);
                    $message = "delete";
                }
                $arOldItems['UF_ITEMS'] = implode(";", $arOldItems['UF_ITEMS']);
                $result = $entity::update($arOldItems['ID'], array(
                    'UF_UUID' => $uuid,
                    'UF_USERID' => $userId,
                    'UF_ITEMS' => $arOldItems['UF_ITEMS'],
                ));
            } else {
                $result = $entity::add(array(
                    'UF_UUID' => $uuid,
                    'UF_USERID' => $userId,
                    'UF_ITEMS' => $arItems,
                ));
                $message = "add";
            }

            return $message;
        }
    }

}