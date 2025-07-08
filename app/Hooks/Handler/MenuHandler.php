<?php

namespace AK\SMARTAUTH\Hooks\Handler;

defined('ABSPATH') || exit; // Prevent direct access

class MenuHandler
{
    /**
     * Initialize the menu.
     *
     * This method sets up the main menu page and submenu pages.
     */
    public static function init()
    {
        // Add the main menu page.
        add_menu_page(
            __('Smart Auth', 'smart-auth'), // Page Title
            __('Smart Auth', 'smart-auth'), // Menu Title
            'manage_options', // Capability
            'smart-auth', // Menu Slug
            [new self(), 'loadView'], // Callback
            'dashicons-layout', // Dashicon
            56 // Position
        );

        // Add submenus
        self::add_submenus();
    }

    /**
     * Add submenus under the main menu.
     */
    public static function add_submenus()
    {
        // Add home page as a submenu
        add_submenu_page(
            'smart-auth',
            __( 'Student Card', 'smart-auth' ),
            __( 'Dashboard', 'smart-auth' ),
            'manage_options',
            'smart-auth',
            [new self(), 'loadView']
        );

        // Add the custom post type as a submenu







    }

    /**
     * Load the admin view.
     *
     * This method includes the admin view file.
     */
    public function loadView()
    {
        // Load the admin view.
        include_once(dirname(SMART_AUTH_PLUGIN_PATH) . '/app/views/admin.php');
    }
}