<?php
defined('ABSPATH') || exit; // Prevent direct access
/**
 * Plugin Name:       Smart Auth
 * Description:       A simple plugin to manage login registar page.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Md Ashfakul karim rahi
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-auth
 */

const SMART_AUTH_PLUGIN_ASSET_ID = 'smart-auth';
const SMART_AUTH_PLUGIN_ASSET_VARS = 'smart_auth';

// Define the plugin constant PLUGIN_VERSION if not already defined.
defined('SMART_AUTH_VERSION') || define('SMART_AUTH_VERSION', '1.0.0');

// Define the plugin constant PLUGIN_PATH if not already defined.
defined('SMART_AUTH_PLUGIN_PATH') || define('SMART_AUTH_PLUGIN_PATH', __FILE__);

// Define the plugin constant PLUGIN_DIR if not already defined.
defined('SMART_AUTH_PLUGIN_DIR') || define('SMART_AUTH_PLUGIN_DIR', __DIR__);

// Define the plugin constant PLUGIN_URL if not already defined.
defined('SMART_AUTH_PLUGIN_URL') || define('SMART_AUTH_PLUGIN_URL',  plugin_dir_url(__FILE__));

// Define the plugin constant PLUGIN_BASE_PATH if not already defined.
defined('SMART_AUTH_PLUGIN_BASE_PATH') || define('SMART_AUTH_PLUGIN_BASE_PATH',  plugin_dir_path(__FILE__));

// Load the autoloader
if (file_exists(__DIR__ . '/autoloader.php')) {
    require __DIR__ . '/autoloader.php';
} else {
    wp_die(__('Autoloader file is missing.', 'smart-auth'));
}

// Register activation hook immediately
register_activation_hook( __FILE__, [new \AK\SMARTAUTH\Config\Bootstrap(), 'activateMe'] );
// Boot the plugin on 'plugins_loaded'
add_action('plugins_loaded', function () {
    (new \AK\SMARTAUTH\Config\Bootstrap())->boot();
});