<?php

namespace HowMAS\CoreMSBundle\Service;

use Pimcore\Db;

class DatabaseService
{
    const CLASS_TABLE = 'hcore_class';

    public static function createTables()
    {
        $query = "CREATE TABLE " . self::CLASS_TABLE . " (
            `id` varchar(50) NOT NULL,
            `name` varchar(190) NOT NULL DEFAULT '',
            `title` varchar(190) DEFAULT '',
            `icon` varchar(190) DEFAULT NULL,
            `active` tinyint(1) DEFAULT 0,
            `layout` longtext DEFAULT NULL,
            `fields` longtext DEFAULT NULL,
            `gridFields` longtext DEFAULT NULL,
            `keyField` varchar(190) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

        Db::get()->executeQuery($query);
    }

    public static function updateTables()
    {
    }

    public static function listingClass()
    {
        $query = "SELECT `id`, `title`
            FROM " . self::CLASS_TABLE . "
            WHERE `active` = ?
            ORDER BY `title` ASC";

        $listing = Db::get()->fetchAllAssociative($query, [1]);
        return $listing;
    }

    public static function clearDocumentBlockData($blockField)
    {
        $query = "DELETE FROM `documents_editables` WHERE `name` LIKE ?";
        Db::get()->fetchOne($query, ['%' . $blockField . ':%']);
    }
}
