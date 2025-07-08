<?php

namespace AK\SMARTAUTH\Hooks\Handler;
defined('ABSPATH') || exit; // Prevent direct access

use AK\SMARTAUTH\Foundation\AppHelper;

class ShortCodeHandler
{
    use AppHelper;

    public function __construct()
    {
        add_shortcode('sa_student_profile', [$this, 'render_student_profile'] );
        add_shortcode('sa_student_auth', [$this, 'sa_render_student_auth' ] );
        add_shortcode('sa_promotions', [ $this, 'render_promotions'] );
    }

    public function render_student_profile()
    {
        $this->load_student_profile_assets();
        ob_start(); // Start output buffering
        echo '<div id="student_profile_all_tabs"></div>';
        return ob_get_clean(); // Return the buffered content
    }

    public function load_student_profile_assets()
    {
        $setings = self::getOption('staff_asia_yo_card_payment_settings', []);
        $payment_gateway = isset($setings['payment_gateway']) ? $setings['payment_gateway'] : '';

        // Enqueue user and shortcode area
        wp_enqueue_style(SMART_AUTH_PLUGIN_ASSET_ID . '-style', SMART_AUTH_PLUGIN_URL . 'assets/css/student_profile.css');

        // Enqueue JavaScript for  user and shortcode area
        wp_enqueue_script(SMART_AUTH_PLUGIN_ASSET_ID, SMART_AUTH_PLUGIN_URL . 'assets/js/student_profile.js', array('jquery', 'moment'), '1.0.0', true);
        wp_localize_script(SMART_AUTH_PLUGIN_ASSET_ID, SMART_AUTH_PLUGIN_ASSET_VARS . '_app_vars', $this->smart_auth_app_vars());


        if( 'PayPal' === $payment_gateway || 'Both' === $payment_gateway ) {
            $paypal_client_id = isset($setings['paypal_client_id']) ? $setings['paypal_client_id'] : '';
            $yo_card_general_settings = self::getOption('smart_auth_general_settings', []);
            $currency           = isset($yo_card_general_settings['selected_currency']) ? $yo_card_general_settings['selected_currency'] : 'GBP';

            wp_enqueue_script(
                SMART_AUTH_PLUGIN_ASSET_ID . '-paypal-sdk',
                htmlspecialchars_decode("https://www.paypal.com/sdk/js?client-id={$paypal_client_id}&currency={$currency}"),
                [],
                null,
                true
            );
        }
    }

    public function load_student_auth_assets()
    {

        // Enqueue user and shortcode area
        wp_enqueue_style(SMART_AUTH_PLUGIN_ASSET_ID . '-style', SMART_AUTH_PLUGIN_URL . 'assets/css/student_auth.css');

        // Enqueue JavaScript for  user and shortcode area
        wp_enqueue_script(SMART_AUTH_PLUGIN_ASSET_ID, SMART_AUTH_PLUGIN_URL . 'assets/js/student_auth.js', array('jquery', 'moment'), '1.0.0', true);
        wp_localize_script(SMART_AUTH_PLUGIN_ASSET_ID, SMART_AUTH_PLUGIN_ASSET_VARS . '_app_vars', $this->smart_auth_app_vars());
    }

    public function sa_render_student_auth()
    {
        $this->load_student_auth_assets();
        ob_start(); // Start output buffering
        echo '<div id="sa_student_profile_auth"></div>';
        return ob_get_clean(); // Return the buffered content
    }

    public function render_promotions($atts)
    {
        $atts = shortcode_atts([
            'brand'             => '',
            'category'          => '',
            'tag'               => '',
            'items'             => -1,
            'layout'            => 'horizontal',
            'except-id'         => '',
            'hide-load-more'    => 'no',
        ], $atts, 'sa_promotions');

        $args =  self::get_promotions_query_args($atts);

        $hide_load_more = $atts['hide-load-more'] === 'yes' ? true : false;

        // Enqueue the necessary scripts and styles
        $this->enqueue_promotions_scripts($args);

        $query = new \WP_Query($args);

        ob_start();
        if ($query->have_posts()) {
            echo '<div class="promotions-wrapper"><div class="promotions" id="promotions_list">';
            while ($query->have_posts()) {
                $query->the_post();

                $meta_data = get_post_meta( get_the_ID(), 'promotion_post_meta', true );
                $post_meta = maybe_unserialize( $meta_data );

                if (!is_array($post_meta)) {
                    $post_meta = [];
                }

                // Get post tags
                $post_tags = get_the_terms(get_the_ID(), 'post_tag');
                $tags = [];
                if ($post_tags && !is_wp_error($post_tags)) {
                    foreach ($post_tags as $tag) {
                        $tags[] = $tag->name;
                    }
                }

                // Get post categories
                $post_categories = get_the_terms(get_the_ID(), 'category');
                $categories = [];
                if ($post_categories && !is_wp_error($post_categories)) {
                    foreach ($post_categories as $category) {
                        $categories[] = $category->name;
                    }
                }

                $featured_image_url = get_the_post_thumbnail_url(get_the_ID());
                $permalink = get_the_permalink();

                include(dirname(SMART_AUTH_PLUGIN_PATH) . '/app/views/promotions.php');
            }
            echo '</div>';
            // Add Load More button
            if( ! $hide_load_more ) {
                echo '<div class="yo-card-load-more-button"><button id="load-more-promotions" class="btn btn-primary" data-page="1" data-nonce="' . wp_create_nonce('sa_promotions_nonce') . '">Load More</button></div>';
            }
            echo '</div>';
        } else {
            echo '<p>No promotions found.</p>';
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Enqueue the necessary scripts and styles for the frontend.
     */
    public function enqueue_promotions_scripts($atts)
    {
        wp_enqueue_style( SMART_AUTH_PLUGIN_ASSET_ID . '-style', SMART_AUTH_PLUGIN_URL . 'assets/css/sa_promotions.css', [], SMART_AUTH_VERSION );
        wp_enqueue_script( SMART_AUTH_PLUGIN_ASSET_ID. '-load-more-promotions', SMART_AUTH_PLUGIN_URL . 'assets/js/load-more-promotions.js', [ 'jquery' ], null, true );

        // Localize script to pass AJAX data
        wp_localize_script( SMART_AUTH_PLUGIN_ASSET_ID. '-load-more-promotions', 'saPromotionsParams', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('sa_promotions_nonce'),
            'atts'    => $atts
        ]);
    }
}
