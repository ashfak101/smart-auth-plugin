<?php

namespace AK\SMARTAUTH\Foundation;

trait AppHelper
{
    public static function smart_auth_app_vars()
    {
        $smart_auth_general_settings = self::getOption('smart_auth_general_settings', []);
        $home_url = home_url();
        $upload_dir         = wp_upload_dir();
        $uploadUrl          = esc_url($upload_dir['baseurl']) . DIRECTORY_SEPARATOR . SMART_AUTH_PLUGIN_ASSET_ID;
        $lost_password_url  = wp_lostpassword_url();
        $profile_page_id    = isset($smart_auth_general_settings['profile_page_id']) ? $smart_auth_general_settings['profile_page_id'] : '';
        $auth_page_id       = isset($smart_auth_general_settings['auth_page_id']) ? $smart_auth_general_settings['auth_page_id'] : '';
        $profile_page_url   = get_permalink($profile_page_id);
        $auth_page_url      = get_permalink($auth_page_id);
        $t_c_page_url       = esc_url($home_url . '/terms-and-conditions');
        $currency           = isset($smart_auth_general_settings['selected_currency']) ? $smart_auth_general_settings['selected_currency'] : 'GBP';
        $currency_symbol    = self::getAllowedCurrencySymble();
        $selected_symbol    = isset($currency_symbol[$currency]) ? $currency_symbol[$currency] : '£';

        if ( is_user_logged_in() ) {
            $current_user       = wp_get_current_user();
            $user_id            = $current_user->ID;
            $membership         = get_user_meta( $user_id, 'yo_student_card_membership', true );
            $membership_meta    = maybe_unserialize( $membership );
            if ( ! is_array($membership_meta ) ) {
                $membership_data = [];
            } else {
                $membership_data = $membership_meta;
            }
        } else {
            $membership_data = [];
        }

        $is_purchased      = isset($membership_data['_is_purchased']) && 'yes' === $membership_data['_is_purchased'] ? true : false;
        $membership_type   = isset($membership_data['_membership_type']) ? $membership_data['_membership_type'] : false;
        $valid_till        = isset($membership_data['_valid_till']) ? $membership_data['_valid_till'] : '';//$valid_till        = '2026-12-31';
        $expire_date       = date_i18n(get_option('date_format'), strtotime($valid_till));
        $is_expired        = strtotime($expire_date) > strtotime('now') ? true : false;
        $day_remaining     = floor((strtotime($expire_date) - strtotime('now')) / (60 * 60 * 24));



        return array(
            'ajax_url'          => admin_url('admin-ajax.php'),
            'slug'              => 'smart-auth',
            'plugin_assets'     => SMART_AUTH_PLUGIN_URL . 'assets/',
            'image_url'         => $uploadUrl,
            'is_logged_in'      => is_user_logged_in(),
            'is_purchased'      => $is_purchased,
            'membership_type'   => $membership_type,
            'lost_password'     => $lost_password_url,
            'auth_page'         => $auth_page_url,
            'profile_page'      => $profile_page_url,
            't_c_page'          => $t_c_page_url,
            'remaining_days'    => $day_remaining,
            'currency'          => $selected_symbol,
            'currency_Name'     => $currency,

            'card_expire_date'  => $expire_date,
            'is_card_expired'   => $is_expired,
            'rest_info'         => array (
                'base_url'      => esc_url_raw( rest_url() ),
                'rest_url'      => rest_url('smart-auth' . '/v2'),
                'nonce'         => wp_create_nonce('wp_rest'),
                'namespace'     => 'smart-auth',
                'version'       => 'v2',
            ),
        );
    }


    /**
     * getOption method will return settings using key
     * This method will get a key as parameter, fetch data from the database, and return it.
     *
     * @param string $key The option key to retrieve.
     * @param mixed $default The default value to return if the option is not found.
     * @return mixed The option value or the default value.
     */
    public static function getOption($key, $default = '')
    {
        // Get settings from options table using the key
        $data = get_option($key);

        // If the data exists, return it; otherwise, return the default value
        return $data !== false ? maybe_unserialize($data) : $default;
    }

    /**
     * Update or insert an option in the wp_options table.
     * This method serializes the data if necessary.
     *
     * @param string $key   The option key to update.
     * @param mixed  $value The value to store.
     * @return bool True if the value was updated successfully, false otherwise.
     */
    public static function updateOption($key, $value)
    {
        // Serialize the value if it's an array or object.
        return update_option($key, maybe_serialize($value));
    }

    /**
     * Retrieve all published WordPress pages and format them as an array.
     *
     * This function queries the WordPress database to retrieve all pages
     * with a post type of 'page' and a post status of 'publish'.
     * It returns an array of formatted pages containing their ID and title.
     *
     * @return array $formattedPages An array of formatted pages with 'id' and 'title'.
     */
    public static function getWPPages()
    {
        global $wpdb;

        // Query to get all published pages
        $pages = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title
                FROM {$wpdb->prefix}posts
                WHERE post_type = %s
                AND post_status = %s
                ORDER BY ID DESC",
                'page',
                'publish'
            )
        );

        // Initialize the formatted pages array
        $formattedPages = [];

        // Loop through each page and format the output
        foreach ($pages as $page) {
            $formattedPages[] = [
                'id'    => strval($page->ID),   // Convert ID to string as per requirement
                'title' => $page->post_title
            ];
        }

        return $formattedPages;
    }

    public static function getAllowedCurrency()
    {
        return apply_filters('sa_yo_student_discount_card_allowed_currency', array(
            'USD' => 'USD',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
        ));
    }

    public static function getAllowedCurrencySymble()
    {
        return apply_filters('sa_yo_student_discount_card_allowed_currency_symble', array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ));
    }

    /**
     * getProfilePageUrl will get the Profile Page id and return link of the page
     * @return mixed
     */
    public static function getProfilePageUrl()
    {
        $settings = self::getOption('smart_auth_general_settings', []);

        if (!empty($settings) && isset($settings['gift_card_page_id'])) {
            $page_id = $settings['gift_card_page_id'];
            $baseUrl = get_permalink($page_id);
            $baseUrl = rtrim($baseUrl, '/\\');
        } else {
            $baseUrl = '';
        }

        return apply_filters('sa_yo_student_discount_card_profile_page_url', $baseUrl);
    }

    /**
     * Handle file uploads.
     *
     * @param string $file_key
     * @return string|WP_Error The file name of the uploaded file, or WP_Error on failure
     */
    public static function handle_file_upload( $file_key, $upload_directory = SMART_AUTH_PLUGIN_ASSET_ID )
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // Check if the file was uploaded successfully
        //Nonce already validated in the calling function
        if (!isset($_FILES[$file_key]) || !isset($_FILES[$file_key]['error']) || $_FILES[$file_key]['error'] != UPLOAD_ERR_OK) {// phpcs:ignore WordPress.Security.NonceVerification.Missing
            return new \WP_Error('file_upload_error', 'File upload failed.');
        }

        // Get the WordPress upload directory
        $upload_dir = wp_upload_dir();
        $custom_upload_dir = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $upload_directory;

        // Ensure the directory exists, if not, create it
        if ( ! file_exists( $custom_upload_dir ) ) {
            wp_mkdir_p( $custom_upload_dir );
        }

        // Temporarily change the upload directory
        add_filter('upload_dir', function ($dirs) use ($custom_upload_dir, $upload_dir, $upload_directory) {
            $dirs['path'] = $custom_upload_dir;
            $dirs['url'] = $upload_dir['baseurl'] . DIRECTORY_SEPARATOR . $upload_directory;
            $dirs['subdir'] = '';
            return $dirs;
        });

        // Handle the file upload
        //nonce already validated in the calling function
        $uploaded_file = wp_handle_upload($_FILES[$file_key], ['test_form' => false]); // phpcs:ignore WordPress.Security.NonceVerification.Missing

        // Remove the filter to restore default upload directory
        remove_filter('upload_dir', '__return_empty_array');

        if (isset($uploaded_file['error'])) {
            return new \WP_Error('upload_error', $uploaded_file['error']);
        }

        // File renaming with a unique prefix
        $file_path = $uploaded_file['file'];
        $file_info = pathinfo($file_path);
        $new_file_name = uniqid('xosd_', true) . '.' . $file_info['extension'];
        $new_file_path = $file_info['dirname'] . '/' . $new_file_name;

        // Initialize the WordPress filesystem
        global $wp_filesystem;
        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        WP_Filesystem();

        if (!$wp_filesystem->move($file_path, $new_file_path)) {
            return new WP_Error('move_error', 'Failed to move the uploaded file.');
        }

        return $new_file_name;
    }

    /**
     * Get the query arguments for fetching promotions.
     *
     * This function constructs the query arguments for fetching promotions based on the provided attributes
     * and custom query variables from the URL.
     *
     * @param array $atts The shortcode attributes.
     * @return array The query arguments for WP_Query.
     */
    public static function get_promotions_query_args( $atts )
    {
        // Get custom query variables from URL
        $url_brand      = get_query_var('brand_name');
        $url_category   = get_query_var('category_name');
        $url_tag        = get_query_var('tag');

        $args = [
            'post_type' => 'promotion',
            'posts_per_page' => intval($atts['items']),
            'brand'     => sanitize_text_field($atts['brand']),
            'category'  => sanitize_text_field($atts['category']),
            'tag'       => sanitize_text_field($atts['tag']),
            'layout'    => sanitize_text_field($atts['layout']),
        ];

        $tax_query = [];
        $brand_query = [];

        if ( ! empty( $url_brand ) ) {
            $brand_query = [
                'key'     => '_promotion_brand_name',
                'value'   => $url_brand,
                'compare' => '=',
            ];
        } else if ( ! empty( $args['brand'] ) ) {
            $brand_query = [
                'key'     => '_promotion_brand_name',
                'value'   => $args['brand'],
                'compare' => '=',
            ];
        }

        //Search by category
        if ( ! empty( $url_category ) ) {
            $tax_query[] = [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $url_category,
            ];
        } else if ( !empty( $args['category'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $args['category'],
            ];
        }

        //Search by Tag
        if ( ! empty( $url_tag ) ) {
            $tax_query[] = [
                'taxonomy' => 'post_tag',
                'field' => 'slug',
                'terms' => $url_tag,
            ];
        } else if ( !empty( $args['tag'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'post_tag',
                'field' => 'slug',
                'terms' => $args['tag'],
            ];
        }

        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        if( ! empty( $brand_query ) ) {
            $args['meta_query'] = [
                $brand_query
            ];
        }

        $exclude_ids = !empty($atts['except-id']) ? array_map('intval', explode(',', $atts['except-id'])) : [];

        if ( ! empty( $exclude_ids ) ) {
            $args['post__not_in'] = $exclude_ids;
        }

        unset($args['brand']);
        unset($args['category']);
        unset($args['tag']);

        return $args;
    }

    /**
     * Generate a unique card number.
     *
     * This function generates a unique card number consisting of three groups of random numbers.
     * The first group contains 6 digits, and the second and third groups contain 4 digits each.
     * The groups are concatenated with spaces.
     *
     * @return string The generated unique card number.
     */
    public static function you_card_generate_unique_number() {
        // Generate three groups of random numbers
        $group1 = mt_rand(100000, 999999); // 6 digits
        $group2 = mt_rand(1000, 9999);     // 4 digits
        $group3 = mt_rand(1000, 9999);     // 4 digits

        // Concatenate the groups with spaces
        $unique_number = sprintf('%06d %04d %04d', $group1, $group2, $group3);

        return $unique_number;
    }
}
