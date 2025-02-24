<?php

namespace Dev\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Entity\DataManager;

class AbstractDataManager extends DataManager
{
    public static function createTable()
    {
        $connection = Application::getInstance()->getConnection();

        if (!$connection->isTableExists(static::getTableName())) {
            static::getEntity()->createDbTable();
            return true;
        } else {
            return false;
        }
    }

    public static function dropTable()
    {
        $connection = Application::getInstance()->getConnection();

        if ($connection->isTableExists(static::getTableName())) {
            $connection->dropTable(static::getTableName());
            return true;
        } else {
            return false;
        }
        return true;
    }
}