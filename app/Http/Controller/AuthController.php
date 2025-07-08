<?php

namespace AK\SMARTAUTH\Http\Controller;

defined('ABSPATH') || exit; // Prevent direct access

use SA\StudentDiscountCard\Services\StripePayment;

class AuthController extends BaseController
{
    /**
     * Handles user login.
     *
     * This method retrieves and sanitizes the email, password, and remember_me inputs from the request.
     * It then authenticates the user using WordPress's wp_authenticate function. If authentication is
     * successful, the user is logged in and a success response is returned. If authentication fails,
     * an error response is returned.
     *
     * @return \WP_REST_Response The response object containing the login status and user data.
     */
    public function login() {
        // Check if the user is already logged in
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            return $this->response([
                'message' => __( 'User is already logged in.', 'smart-auth' ),
                'data'    => [
                    'id'       => $current_user->ID,
                    'username' => $current_user->user_login,
                    'email'    => $current_user->user_email,
                    'name'     => $current_user->display_name,
                ],
                'status'  => 'success',
            ], 200);
        }

        // Retrieve and sanitize inputs
        $email = sanitize_email( $this->request->get( 'email' ) );
        $password = sanitize_text_field( $this->request->get( 'password' ) );
        $remember_me = sanitize_text_field( $this->request->get( 'remember_me' ) );

        // Check if required fields are provided
        if ( empty( $email ) || empty( $password ) ) {
            return $this->response([
                'message' => __( 'Email and password are required.', 'smart-auth' ),
                'status'  => 'error',
            ], 200);
        }

        // Authenticate the user
        $user = wp_authenticate( $email, $password );

        // Handle authentication errors
        if ( is_wp_error( $user ) ) {
            $message = $user->get_error_message();
            return $this->response([
                'message' => $message,
                'status'  => 'error',
            ], 200);
        }

        // Log the user in
        wp_set_current_user( $user->ID, $user->user_login );
        wp_set_auth_cookie( $user->ID, $remember_me === 'true' );

        // Prepare success response
        return $this->response([
            'message' => __( 'Login successful.', 'smart-auth' ),
            'data'    => [
                'id'       => $user->ID,
                'username' => $user->user_login,
                'email'    => $user->user_email,
                'name'     => $user->display_name,
            ],
            'status'  => 'success',
        ], 200);
    }

    /**
     * Handles user Registration.
     *
     **/
    public function register() {
        // Retrieve and sanitize inputs
        $email          = sanitize_email( $this->request->get( 'email' ) );
        $password       = sanitize_text_field( $this->request->get( 'password' ) );
        $first_name     = sanitize_text_field( $this->request->get( 'first_name' ) );
        $last_name      = sanitize_text_field( $this->request->get( 'last_name' ) );
        $dob            = sanitize_text_field( $this->request->get( 'date_of_birth' ) );
        $gender         = sanitize_text_field( $this->request->get( 'gender' ) );
        $t_condition    = sanitize_text_field( $this->request->get( 'term_condition' ) );

        // Check if email already exists
        if ( email_exists( $email ) ) {
            return $this->response( [
                'message' => 'Email is already registered.',
                'status'  => 'error',
            ], 200 );
        }



        // Check if username can be generated from full name
        $first_name     = ucfirst( $first_name );
        $last_name      = ucfirst( $last_name );
        $username       = sanitize_user( strtolower($first_name) );

        if ( username_exists( $username ) ) {
            $username = $username . rand( 100, 999 ); // Append random numbers to avoid conflicts
        }

        // Create the user
        $user_id = wp_create_user( $username, $password, $email );

        // Check if user creation was successful
        if ( is_wp_error( $user_id ) ) {
            return $this->response( [
                'message' => $user_id->get_error_message(),
                'status'  => 'error',
            ], 200 );
        }

        // Send user registration confirmation email
        do_action( 'sa_yo_student_discount_card_after_registration', (Object)[
            'user_name'     => $first_name,
            'user_email'    => $email,
            'user_password' => $password,
            'profile_link'  => self::getProfilePageUrl(),
        ] );

        // Update additional user metadata
        update_user_meta( $user_id, 'first_name', $first_name );
        update_user_meta( $user_id, 'last_name', $last_name );
        update_user_meta( $user_id, 'dob', $dob );
        update_user_meta( $user_id, 'gender', $gender );
        update_user_meta( $user_id, 'term_condition', $t_condition );

        // Optionally, log in the user immediately after registration
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id );

        // Return success response
        return $this->response( [
            'message' => 'Registration successful.',
            'status'  => 'success',
            'data'    => [
                'user_id'       => $user_id,
                'email'         => $email,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'name'          => $first_name . ' ' . $last_name,
            ],
        ], 200 );
    }
}
