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
        $login = sanitize_text_field( $this->request->get( 'email' ) );
        $password = sanitize_text_field( $this->request->get( 'password' ) );
        $remember_me = sanitize_text_field( $this->request->get( 'remember_me' ) );


        // Check if required fields are provided
            if (empty($login) || empty($password)) {
                return $this->response([
                    'message' => __('Username/Email and password are required.', 'smart-auth'),
                    'status'  => 'error',
                ], 200);
            }
         // Check if the input is an email or username
            if (is_email($login)) {
                $user = get_user_by('email', $login);
                if ($user) {
                    $login = $user->user_login; // Use the username for authentication
                }
            }

        // Authenticate the user
        $user = wp_authenticate( $login, $password );

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

    /**
     * Handles user logout.
     *
     * This method logs out the current user and returns a success response.
     *
     * @return \WP_REST_Response The response object containing the logout status.
     */
    public function logout() {
        // Check if the user is logged in
        if ( ! is_user_logged_in() ) {
            return $this->response([
                'message' => __( 'User is not logged in.', 'smart-auth' ),
                'status'  => 'error',
            ], 200);
        }
        // Log out the user
        wp_logout();
        // Return success response
        return $this->response([
            'message' => __( 'Logout successful.', 'smart-auth' ),
            'status'  => 'success',
        ], 200);
    }
    /**
     * Handles user password reset.
     *
     * This method retrieves the email from the request, checks if the user exists,
     * and sends a password reset link if the user is found. It returns a success
     * or error response based on the outcome.
     *
     * @return \WP_REST_Response The response object containing the password reset status.
     */
    public function forgot_password() {
        // Get the general settings to retrieve the auth page ID
          $smart_auth_general_settings = self::getOption('smart_auth_general_settings', []);
          $auth_page_id       = isset($smart_auth_general_settings['auth_page_id']) ? $smart_auth_general_settings['auth_page_id'] : '';
          $auth_page_url      = get_permalink($auth_page_id);
        // Retrieve and sanitize email input
        $email = sanitize_email( $this->request->get( 'email' ) );
        // Check if the email is provided
        if ( empty( $email ) ) {
            return $this->response([
                'message' => __( 'Email is required.', 'smart-auth' ),
                'status'  => 'error',
            ], 200);
        }
        // Check if the user exists
        $user = get_user_by( 'email', $email );
        if ( ! $user ) {
            return $this->response([
                'message' => __( 'No user found with this email address.', 'smart-auth' ),
                'status'  => 'error',
            ], 200);
        }
        // Send password reset email
        $reset_key = get_password_reset_key( $user );
        if ( is_wp_error( $reset_key ) ) {
            return $this->response([
                'message' => $reset_key->get_error_message(),
                'status'  => 'error',
            ], 200);
        }
        // now make the reset URL use the auth page URL
        // get username
        $user_login = $user->user_login;

        $reset_url = $auth_page_url . '#/reset-password?token=' . urlencode($reset_key) . '&login=' . urlencode($user_login);

        // Prepare the email content
        $subject = __( 'Password Reset Request', 'smart-auth' );
        $message = sprintf(
            __( 'Hello %s, you requested a password reset. Click the link below to reset your password: %s', 'smart-auth' ),
            $user->display_name,
            esc_url( $reset_url )
        );
        // Send the email
        $sent = wp_mail( $email, $subject, $message );
        if ( ! $sent ) {
            return $this->response([
                'message' => __( 'Failed to send password reset email.', 'smart-auth' ),
                'status'  => 'error',
            ], 200); }
        // Return success response
        return $this->response([
            'message' => __( 'Password reset email sent successfully.', 'smart-auth' ),
            'status'  => 'success',
            'url' =>$auth_page_url
        ], 200);
    }

    /*
     * Handles user password reset.
     *
     * This method retrieves the token and new password from the request, verifies the reset key,
     * and resets the user's password if the verification is successful. It returns a success or
     * error response based on the outcome.
     *
     * @return \WP_REST_Response The response object containing the password reset status.
     */

    public function reset_password() {
        // Retrieve and sanitize inputs
        $token = sanitize_text_field( $this->request->get( 'token' ) );
        $new_password = sanitize_text_field( $this->request->get( 'password' ) );
         $login = sanitize_text_field($this->request->get('login'));

        // Check if token and new password are provided
        if ( empty( $token ) || empty( $new_password ) ) {
            return $this->response([
                'message' => __( 'Token and new password are required.', 'smart-auth' ),
                'status'  => 'error',
            ], 200);
        }


        // Verify the reset key
        // Verify the reset key and retrieve the user
        $user = check_password_reset_key($token, $login);

        if ( is_wp_error( $user ) ) {
            return $this->response([
                'message' => $user->get_error_message(),
                'status'  => 'error',
            ], 200);
        }

        // Reset the password
        reset_password( $user, $new_password );

        // Return success response
        return $this->response([
            'message' => __( 'Password has been reset successfully.', 'smart-auth' ),
            'status'  => 'success',
        ], 200);
    }


}
