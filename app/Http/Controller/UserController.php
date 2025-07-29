<?php

namespace AK\SMARTAUTH\Http\Controller;

defined('ABSPATH') || exit; // Prevent direct access

use AK\SMARTAUTH\Http\Request\ValidatorHandler;
use AK\SMARTAUTH\Http\Model\BrowsingHistory;
use AK\SMARTAUTH\Http\Model\SavedOffer;

class UserController extends BaseController
{
    public function get_profile()
    {
        // Get the current user
        $current_user = wp_get_current_user();

        // Check if the user is logged in
        if ( is_user_logged_in() ) {
            $user_email     = $current_user->user_email;
            $user_id        = $current_user->ID;
            $first_name     = get_user_meta($user_id, 'first_name', true);
            $last_name      = get_user_meta($user_id, 'last_name', true);

            // Get the user meta data
            $data = [
                'first_name'        => $first_name,
                'last_name'         => $last_name,
                'name'              => $first_name . ' ' . $last_name,
                'email'             => $user_email,
                'dob'               => get_user_meta($user_id, 'dob', true),
                'gender'            => get_user_meta($user_id, 'gender', true),
                'house_name'        => get_user_meta($user_id, 'house_name', true),
                'address_line_two'  => get_user_meta($user_id, 'address_line_two', true),
                'town'              => get_user_meta($user_id, 'town', true),
                'postcode'          => get_user_meta($user_id, 'postcode', true),
                'country'           => get_user_meta($user_id, 'country', true),
                'profile_picture'   => get_user_meta($user_id, 'profile_picture', true),
            ];
        } else {
            $data = [];
        }

        return $this->response($data, 200);

    }

    public function profile_picture()
    {
        if ( is_user_logged_in() ) {
            // Handle event file upload
            $profile_picture = self::handle_file_upload('file');

            if ( is_wp_error( $profile_picture ) ) {
                return $this->response($profile_picture->get_error_message(), 400);
            }

            // Get the user ID
            $user_id = get_current_user_id();

            update_user_meta($user_id, 'profile_picture', $profile_picture);

            return $this->response('Profile picture updated successfully', 200);
        } else {
            return $this->response('You must need to logged in', 400);
        }
    }

    public function save_profile()
    {
        // Validate the input fields
        $errors = ValidatorHandler::validateSaveProfileRequest( $this->request );

        if (count($errors) > 0) {
            return $this->response($errors, 400);
        }

        // Get form data from the request
        $first_name    = sanitize_text_field( $this->request->get( 'first_name' ) );
        $last_name     = sanitize_text_field( $this->request->get( 'last_name' ) );
        $email         = sanitize_email( $this->request->get( 'email' ) );

        $house_name    = sanitize_text_field( $this->request->get( 'house_name' ) );
        $address_line_two = sanitize_text_field( $this->request->get( 'address_line_two' ) );
        $town          = sanitize_text_field( $this->request->get( 'town' ) );
        $postcode      = sanitize_text_field( $this->request->get( 'postcode' ) );
        $country       = sanitize_text_field( $this->request->get( 'country' ) );

        // Get the current user
        $current_user = wp_get_current_user();

        // Check if the user is logged in
        if ( is_user_logged_in() ) {
            $user_email = $current_user->user_email;
            $user_id = $current_user->ID;

            // Check if the email address has changed
            if ($email !== $user_email) {
                // Check if the email address is already in use
                return $this->response('You can not change the email address', 400);
            } else {
                update_user_meta($user_id, 'first_name', $first_name);
                update_user_meta($user_id, 'last_name', $last_name);
                update_user_meta($user_id, 'dob', $dob);
                update_user_meta($user_id, 'gender', $gender);
                update_user_meta($user_id, 'house_name', $house_name);
                update_user_meta($user_id, 'address_line_two', $address_line_two);
                update_user_meta($user_id, 'town', $town);
                update_user_meta($user_id, 'postcode', $postcode);
                update_user_meta($user_id, 'country', $country);

                return $this->response('Profile information updated successfully', 200);
            }
        } else {
            return $this->response('You must need to logged in', 400);
        }
    }

    public function change_password()
    {
        // Validate the input fields
        $errors = ValidatorHandler::validateChangePasswordRequest( $this->request );

        if (count($errors) > 0) {
            return $this->response($errors, 400);
        }

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $current_password = sanitize_text_field( $this->request->get( 'current_password' ) );
            $new_password =sanitize_text_field( $this->request->get( 'new_password' ) );

            // Get the current user object
            $user = get_userdata( $user_id );

            // Check if the current password is correct
            if ( ! wp_check_password( $current_password, $user->user_pass, $user_id ) ) {
                return $this->response('Current password is incorrect', 400);
            }

            // Update the user's password
            wp_set_password( $new_password, $user_id );

            return $this->response('Password changed successfully', 200);
        } else {
            return $this->response('You must need to logged in', 400);
        }
    }

    public function browsing_history( \WP_REST_Request $request )
    {
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $page   = intval($request->get_param('page') ) ?: 1;
            $limit  = intval($request->get_param('per_page') ) ?: 10;
            $offset = ($page - 1) * $limit;
            $browsing_history = new BrowsingHistory();
            $results = $browsing_history->getBrowsingHistoryByUserId( $user_id, $limit, $offset );
            $total_items = $browsing_history->countBrowsingHistoryByUserId( $user_id );
            $data = [];
            // Loop through results
            foreach ( $results as $row ) {
                $post_id = $row['post_id'];
                $post_title = get_the_title( $post_id );
                $permalink = get_the_permalink($post_id);
                $visited_at = $row['visited_at'];
                $data[] = [
                    'deal' => $post_title,
                    'permalink' => $permalink,
                    'date' => date_i18n(get_option('date_format'), strtotime($visited_at))
                ];
            }

            return $this->response([
                'total_items' => $total_items,
                'history_data' => $data,
                'current_page' => $page,
                'per_page' => $limit
            ]);

            return $this->response($data, 200);
        } else {
            return $this->response('You must need to logged in', 400);
        }
    }

    public function save_favourite( \WP_REST_Request $request )
    {
        $postId = intval($request->get_param('postId'));

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();

            $exist = SavedOffer::getByPostId( $postId );

            if ( $exist ) {
                return $this->response(
                    [
                        'message' => 'This offer is already saved',
                        'status' => 'error',
                    ], 200);
            } else {
                $data = [
                    'user_id' => $user_id,
                    'post_id' => $postId,
                    'saved_at' => current_time('mysql')
                ];

                $saved_offer = new SavedOffer();
                $saved_offer->store($data);
            }
            return $this->response([
                'message' => 'Favourite saved successfully',
                'status' => 'success',
            ], 200);

        } else {
            return $this->response([
                'message' => 'You must need to logged in',
                'status' => 'error',
            ], 200);
        }
    }

    public function remove_favourite( \WP_REST_Request $request )
    {
        $id = intval($request->get_param('id'));

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $saved_offer = new SavedOffer();
            $saved_offer->delete($id, $user_id);
            return $this->response([
                'message' => 'Favourite removed successfully',
                'status' => 'success',
            ], 200);
        } else {
            return $this->response([
                'message' => 'You must need to logged in',
                'status' => 'error',
            ], 200);
        }
    }

    public function get_saved_offers( \WP_REST_Request $request )
    {
        $page   = intval(sanitize_text_field($request->get_param('page')));
        $limit  = intval(sanitize_text_field($request->get_param('per_page')));

        $page = isset($page) ? $page : 1;
        $limit = isset($limit) ? $limit : 10;
        $offset = ($page - 1) * $limit;

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $saved_offer = new SavedOffer();
            $results = $saved_offer->getSavedOffersByUserId( $user_id, $limit, $offset );

            $data = [];
            // Loop through results
            foreach ( $results as $row ) {
                $post_id = $row->post_id;
                $post_title = get_the_title( $post_id );
                $saved_at = $row->saved_at;
                // Retrieve existing values from the database.
                $meta_data = get_post_meta( $post_id, 'promotion_post_meta', true );
                $post_meta = maybe_unserialize( $meta_data );
                if ( ! is_array( $post_meta ) ) {
                    $post_meta = [];
                }

                $logo       = isset($post_meta['_promotion_logo']) ? $post_meta['_promotion_logo'] : '';
                $location   = isset($post_meta['_promotion_location']) ? $post_meta['_promotion_location'] : '';
                $terms      = isset($post_meta['_promotion_terms']) ? $post_meta['_promotion_terms'] : '';
                $coupon     = isset($post_meta['_promotion_coupon']) ? $post_meta['_promotion_coupon'] : '';
                $featured_image_url = get_the_post_thumbnail_url($post_id);
                //Post Permalink
                $permalink = get_the_permalink($post_id);
                $data[] = [
                    'id' => $row->id,
                    'title' => $post_title,
                    'logo' => $logo,
                    'thumbnail' => $featured_image_url,
                    'location' => $location,
                    'terms' => $terms,
                    'coupon' => $coupon,
                    'permalink' => $permalink,
                    'saved_at' => date_i18n(get_option('date_format'), strtotime($saved_at))
                ];
            }

            return $this->response([
                'data' => $data,
                'status' => 'success',
            ], 200);

        } else {
            return $this->response([
                'message' => 'You must need to logged in',
                'status' => 'error',
            ], 200);
        }
    }
    public function user_logout()
    {
        if ( is_user_logged_in() ) {
            wp_logout();
            return $this->response([
                'message' => 'You have been logged out successfully',
                'status' => 'success',
            ], 200);
        } else {
            return $this->response('You must need to logged in', 400);
        }
    }


}
