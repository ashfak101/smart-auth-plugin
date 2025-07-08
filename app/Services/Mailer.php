<?php

namespace AK\SMARTAUTH\Services;

defined( 'ABSPATH' ) || exit; // Prevent direct access

use AK\SMARTAUTH\Foundation\AppHelper;

/**
 * Class Mailer
 *
 * This class is responsible for sending emails within the xStudentDiscountCard application.
 * It uses the AppHelper trait to leverage common application functionalities.
 *
 * @package AK\SMARTAUTH\Services
 */
class Mailer {
    use AppHelper;

    /**
     * Sends a registration confirmation email to the user.
     *
     * This function takes an array of user data, sanitizes the input, and sends a registration
     * confirmation email to the user with their registration details.
     *
     * @param array $user_data Associative array containing user data. Expected keys:
     * - 'user_name' (string): Name of the user registering.
     * - 'user_email' (string): Email address of the user.
     * - 'user_password' (string): Password of the user.
     * - 'profile_link' (string): URL to the user's profile.
     *
     * @return void
     */
    public static function send_registration_confirmation_email( $user_data ) {
        // Sanitize incoming data
        $user_name    = sanitize_text_field( $user_data['user_name'] );
        $user_email   = sanitize_email( $user_data['user_email'] );
        $user_password = sanitize_text_field( $user_data['user_password'] );
        $profile_link = esc_url( $user_data['profile_link'] );

        // Start output buffering and include the email template
        ob_start();
        include_once( SMART_AUTH_PLUGIN_DIR . '/app/Mail/user-registration.php' );
        $message = ob_get_clean();

        // Set email subject
        $subject = 'Welcome! Your YO Discount Card Registration is Complete';

        // Get headers for the email
        $headers = self::get_email_headers();

        // Attempt to send the email
        wp_mail( $user_email, $subject, $message, $headers );
    }

    /**
     * Sends an invoice to the user after a successful purchase.
     *
     * This function takes an array of invoice data, sanitizes the input, and sends an invoice
     * email to the user with purchase details.
     *
     * @param array $invoice_data Associative array containing invoice data. Expected keys:
     * - 'user_name' (string): Name of the user making the purchase.
     * - 'user_email' (string): Email address of the user.
     * - 'payment_id' (string): Payment ID for the transaction.
     * - 'order_date' (string): Date the order was placed.
     * - 'item_name' (string): Name of the purchased item.
     * - 'item_amount' (float): Amount of the purchased item.
     *
     * @return void
     */
    public static function send_invoice_to_user( $invoice_data ) {
        // Sanitize incoming data
        $user_name   = sanitize_text_field( $invoice_data['user_name'] );
        $to_email  = sanitize_email( $invoice_data['to_email'] );
        $payment_id  = sanitize_text_field( $invoice_data['payment_id'] );
        $order_date  = sanitize_text_field( $invoice_data['order_date'] );
        $item_name   = sanitize_text_field( $invoice_data['item_name'] );
        $item_amount = floatval( $invoice_data['item_amount'] );

        // Start output buffering and include the email template
        ob_start();
        include_once( SMART_AUTH_PLUGIN_DIR . '/app/Mail/invoice-to-user.php' );
        $message = ob_get_clean();

        // Set email subject
        $subject = 'Your YO Discount Card Purchase Confirmation';

        // Get headers for the email
        $headers = self::get_email_headers();

        // Attempt to send the email
        wp_mail( $to_email, $subject, $message, $headers );
    }

    /**
     * Sends an invoice notification email to the admin after a successful purchase.
     *
     * This function takes an array of invoice data, sanitizes the input, and sends an email to the admin
     * with the details of the purchase, including the user's name, email, payment ID, item purchased,
     * payment method, and order date.
     *
     * @param array $invoice_data Associative array containing invoice data. Expected keys:
     * - 'user_name' (string): Name of the user making the purchase.
     * - 'user_email' (string): Email address of the user.
     * - 'payment_id' (string): Payment ID for the transaction.
     * - 'order_date' (string): Date the order was placed.
     * - 'item_name' (string): Name of the purchased item.
     * - 'item_amount' (float): Amount of the purchased item.
     * - 'payment_method' (string): Payment method used for the transaction.
     *
     * @return void
     */
    public static function send_invoice_notification_to_admin( $invoice_data ) {
        // Sanitize incoming data to ensure safe use
        $user_name      = sanitize_text_field( $invoice_data['user_name'] );
        $user_email     = sanitize_email( $invoice_data['user_email'] );
        $payment_id     = sanitize_text_field( $invoice_data['payment_id'] );
        $order_date     = sanitize_text_field( $invoice_data['order_date'] );
        $item_name      = sanitize_text_field( $invoice_data['item_name'] );
        $item_amount    = floatval( $invoice_data['item_amount'] );
        $payment_method = sanitize_text_field( $invoice_data['payment_method'] );

        // Start output buffering and include the email template
        ob_start();
        include_once( SMART_AUTH_PLUGIN_DIR . '/app/Mail/admin-notification.php' );
        $message = ob_get_clean();

        // Set email subject and recipient
        $subject    = 'New Purchase Notification: YO Discount Card';
        ##TODO
        $admin_mail = 'info@yocard.co.uk'; // Replace with the actual admin email address

        // Get the headers for the email (e.g., from, content-type)
        $headers = self::get_email_headers();

        // Attempt to send the email to the admin
        wp_mail( $admin_mail, $subject, $message, $headers );
    }

    /**
     * Get the email headers.
     *
     * @return array The email headers.
     */
    private static function get_email_headers() {
        return array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: Your Offer Discount Card <info@yocard.co.uk>',
            'From: Your Offer Discount Card <info@yocard.co.uk>',
        );
    }
}