<?php

namespace AK\SMARTAUTH\Hooks\Handler;
use AK\SMARTAUTH\Database\DBMigrate;

/**
 * Class ActivationHandler
 *
 * Handles plugin activation tasks.
 */
class ActivationHandler
{
    /**
     * Handle plugin activation.
     *
     * This method is called when the plugin is activated.
     * It runs database migrations and updates the plugin version option.
     */
    public function handle()
    {
        // Run database migrations.
        DBMigrate::run();

        // Activate the plugin.
        $this->activate();

        /*if (! wp_next_scheduled('sa_student_discount_card_hourly_tasks')) {
            wp_schedule_event(time(), 'hourly', 'sa_student_discount_card_hourly_tasks');
        }

        if (! wp_next_scheduled('sa_student_discount_card_hourly_tasks')) {
            wp_schedule_event(time(), 'daily', 'sa_student_discount_card_daily_tasks');
        }*/
    }

    /**
     * Activate the plugin.
     *
     * Sets the plugin version option if it doesn't exist.
     */
    public function activate()
    {
        $version = get_option('sa_student_discount_card_plugin_version');

        // Check if the plugin version option doesn't exist and set it.
        if (!$version) {
            update_option('sa_student_discount_card_plugin_version', SMART_AUTH_VERSION);
        }
    }
}