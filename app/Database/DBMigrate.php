<?php

namespace AK\SMARTAUTH\Database;

use AK\SMARTAUTH\Database\Migrations\SavedOffersMigrator;
use AK\SMARTAUTH\Database\Migrations\BrowsingHistoryMigrator;
use AK\SMARTAUTH\Database\Migrations\OrderHistoryMigrator;

require_once( ABSPATH.'wp-admin/includes/upgrade.php' );

class DBMigrate
{
    protected static $migrators = [
        SavedOffersMigrator::class,
        BrowsingHistoryMigrator::class,
        OrderHistoryMigrator::class,
    ];

    public static function run(){
        self::migrate();
    }

    public static function migrate()
    {
        foreach ( self::$migrators as $class ) {
            $class::migrate();
        }
    }
}