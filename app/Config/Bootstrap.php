<?php

namespace AK\SMARTAUTH\Config;
use AK\SMARTAUTH\Foundation\Application;
use AK\SMARTAUTH\Hooks\Handler\ActivationHandler;

/**
 * Class Bootstrap
 *
 * Bootstraps the WPSS Social Share plugin.
 */
class Bootstrap
{
    /**
     * Bootstraps the plugin.
     */
    public function boot()
    {
        // Include REST API routes.
        add_action('rest_api_init', function () {
            require_once SMART_AUTH_PLUGIN_BASE_PATH . 'app/Http/Router/Api.php';
        });



        $app = Application::instance();
        $app->load();
    }

    /**
     * Handles plugin activation.
     */
    public function activateMe()
    {
        // Handle plugin activation.
        (new ActivationHandler())->handle();
    }
}