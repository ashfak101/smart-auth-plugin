<?php

namespace AK\SMARTAUTH\Database\Migrations;

class OrderHistoryMigrator
{
    protected static $tableName = 'yo_card_order_history';

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
                `membership_plan` VARCHAR(50) NOT NULL,
                `duration` VARCHAR(50) NOT NULL,
                `amount` DECIMAL(10, 2) NOT NULL,
                `customer_id` VARCHAR(50) NOT NULL,
                `payment_id` VARCHAR(50) NOT NULL,
                `payment_status` VARCHAR(50) DEFAULT NULL,
                `payment_gateway` VARCHAR(50) NOT NULL,
                `paypal_capture_id` VARCHAR(50) DEFAULT NULL,
                `refund_status` VARCHAR(50) DEFAULT NULL,
                `refund_date` DATETIME DEFAULT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) $charsetCollate;";

            dbDelta($sql);
        } else {
            // Check and add new columns if they don't exist
            self::addMissingColumns($table);
        }
    }

    protected static function addMissingColumns($table)
    {
        global $wpdb;

        // List of new columns to add
        $newColumns = [
            'payment_status' => "VARCHAR(50) DEFAULT NULL AFTER `payment_id`",
            'paypal_capture_id' => "VARCHAR(50) DEFAULT NULL AFTER `payment_gateway`",
            'refund_status' => "VARCHAR(50) DEFAULT NULL AFTER `paypal_capture_id`",
            'refund_date'   => "DATETIME DEFAULT NULL AFTER `refund_status`",
        ];

        // Check and add each column if it doesn't exist
        foreach ($newColumns as $columnName => $columnDefinition) {
            $columnExists = $wpdb->get_results(
                $wpdb->prepare(
                    "SHOW COLUMNS FROM `$table` LIKE %s",
                    $columnName
                )
            );

            if (empty($columnExists)) {
                $wpdb->query("ALTER TABLE `$table` ADD `$columnName` $columnDefinition");
            }
        }
    }
}