<?php
    //Prevent direct access
    defined('ABSPATH') || exit;

    use AK\SMARTAUTH\Hooks\Handler\ActionHooksHandler;

    //Register all action hooks

    add_action( 'template_redirect', [ ActionHooksHandler::class, 'store_offer_visited_by_the_user'] );

    //Add rewrite rules for custom query vars
    add_action( 'init', [ ActionHooksHandler::class, 'sa_student_discount_card_rewrite_rules'] );

    add_action('wp_ajax_load_more_promotions', [ ActionHooksHandler::class, 'yo_card_load_more_promotions'] );

    add_action('wp_ajax_nopriv_load_more_promotions', [ ActionHooksHandler::class, 'yo_card_load_more_promotions'] );

    add_action('sa_yo_student_discount_card_after_registration', [ ActionHooksHandler::class, 'send_user_registration_confirmation_email'] );
    //Send invoice to the user
    add_action('sa_yo_student_discount_card_after_membership_purchased', [ ActionHooksHandler::class, 'send_invoice_to_user_email'] );
    //Send admin notification to the admin
    add_action('sa_yo_student_discount_card_after_membership_purchased', [ ActionHooksHandler::class, 'send_invoice_notification_to_admin_email'] );