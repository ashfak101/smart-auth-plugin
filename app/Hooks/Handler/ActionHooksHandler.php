<?php

namespace AK\SMARTAUTH\Hooks\Handler;
defined('ABSPATH') || exit; // Prevent direct access

use AK\SMARTAUTH\Foundation\AppHelper;
use AK\SMARTAUTH\Http\Model\BrowsingHistory;
use AK\SMARTAUTH\Services\Mailer;
/**
 * ActionHooksHandler class define all action hooks methods
 */
class ActionHooksHandler
{
    use AppHelper;

    /**
     * Store the ID of the visited offer by the user.
     *
     * This function checks if the current page is a single post of the custom post type 'promotion'.
     * If it is, it retrieves the post ID and stores it in the user's browsing history.
     */
    public static function store_offer_visited_by_the_user() {
        // Check if we're viewing a single post of the custom post type
        if ( is_singular( 'promotion' ) ) {
            global $post;

            // Get the current post ID
            $post_id = $post->ID;

            // Your logic to store the post ID
            // For example, storing in user meta or an option
            $user_id = get_current_user_id();
            if ( $user_id ) {
                $browsing_history = new BrowsingHistory();

                $exist = $browsing_history->is_exist( $user_id, $post_id );

                if( $exist ) {
                    $id = $exist->id;
                    // Update the existing browsing history record
                    $browsing_history->update(
                        [
                            'visited_at' => current_time( 'mysql' )
                        ],
                        [
                            'id' => $id
                        ]
                    );

                } else {
                    // Insert a new browsing history record
                    $browsing_history->store(
                        [
                            'user_id' => $user_id,
                            'post_id' => $post_id,
                            'visited_at' => current_time( 'mysql' )
                        ]
                    );
                }
            }
        }
    }

    /**
     * Add custom rewrite rules for promotions.
     *
     * This function adds rewrite rules for custom query variables to handle URLs
     * like promotions/brand/samsung and promotions/brand/samsung/new-offers.
     */
    public static function sa_student_discount_card_rewrite_rules() {
        // Add rewrite rules for custom query vars i.e promotions?barnd_name=smasung
        add_rewrite_rule(
            '^promotions/brand/([^/]+)/?$',
            'index.php?pagename=promotions&brand_name=$matches[1]',
            'top'
        );
    }


    public static function yo_card_load_more_promotions() {
        check_ajax_referer('sa_promotions_nonce', 'nonce'); // Verify nonce

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $args = isset($_POST['atts']) ? $_POST['atts'] : [];
        $args['paged'] = $page;

        $args['posts_per_page'] = isset($args['posts_per_page']) ? intval($args['posts_per_page']) : 4;

        if( $args['posts_per_page'] <= 0 ) {
            $args['posts_per_page'] = 4;
        }

        $atts['layout'] = isset($args['layout']) ? $args['layout'] : 'horizontal';
        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $meta_data = get_post_meta(get_the_ID(), 'promotion_post_meta', true);
                $post_meta = maybe_unserialize($meta_data);

                if (!is_array($post_meta)) {
                    $post_meta = [];
                }

                // Include your promotions view
                include(dirname(SMART_AUTH_PLUGIN_PATH) . '/app/views/promotions.php');
            }
        } else {
            echo ''; // No more posts
        }

        wp_reset_postdata();
        wp_die();
    }

    public static function send_user_registration_confirmation_email( $data ) {

        Mailer::send_registration_confirmation_email([
            'user_name'       => $data->user_name,
            'user_email'      => $data->user_email,
            'user_password'   => $data->user_password,
            'profile_link'    => esc_url( $data->profile_link ),
        ]);
    }

    public static function send_invoice_to_user_email ($data){
        Mailer::send_invoice_to_user([
            'user_name'      => $data->user_name,
            'to_email'       => $data->user_email,
            'payment_id'     => $data->payment_id,
            'order_date'     => $data->order_date ,
            'item_name'      => $data->item_name ,
            'item_amount'    => $data->item_amount ,
        ]);
    }

    public static function send_invoice_notification_to_admin_email ($data){
        Mailer::send_invoice_notification_to_admin([
            'user_name'       => $data->user_name,
            'user_email'      => $data->user_email,
            'payment_id'      => $data->payment_id,
            'order_date'      => $data->order_date ,
            'item_name'       => $data->item_name ,
            'item_amount'     => $data->item_amount ,
            'payment_method'  => $data->payment_method
        ]);
    }

}
