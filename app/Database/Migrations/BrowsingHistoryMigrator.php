<?php

namespace AK\SMARTAUTH\Database\Migrations;

class BrowsingHistoryMigrator
{
    protected static $tableName = 'yo_card_browsing_history';

    public static function migrate()
    {
        global $wpdb;

        // Get charset and collation for the table
        $charsetCollate = $wpdb->get_charset_collate();

        // Escape the table name properly
        $table = $wpdb->prefix . esc_sql(static::$tableName);

        // Check if the table already exists
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) ) != $table ) {

            // SQL query to create the table if it doesn't exist
            $sql = "CREATE TABLE $table (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT(20) UNSIGNED NOT NULL,
                `post_id` BIGINT(20) UNSIGNED NOT NULL,
                `visited_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                FOREIGN KEY (`user_id`) REFERENCES {$wpdb->prefix}users(`ID`) ON DELETE CASCADE,
                FOREIGN KEY (`post_id`) REFERENCES {$wpdb->prefix}posts(`ID`) ON DELETE CASCADE
            ) $charsetCollate;";

            dbDelta($sql);
        }
    }
}