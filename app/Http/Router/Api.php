<?php

defined('ABSPATH') || exit; // Prevent direct access

use AK\SMARTAUTH\Http\Router\Router;

//Setup settings
Router::get('general-settings', 'SettingsController@index');
//Student logina and register
Router::post('register', 'AuthController@register');
Router::post('login', 'AuthController@login');
Router::post('forgot-password', 'AuthController@forgot_password');
Router::post('reset-password', 'AuthController@reset_password');
//Membership purchase and payment
// Router::get('get-membership-settings', 'SettingsController@get_membership_data');
// Router::post('create-payment-intent', 'PaymentController@make_payment_intents');
// Router::post('create-payment-refund', 'PaymentController@make_payment_intents_refund');
// Router::post('save-membership', 'PaymentController@save_membership');

//Student Profile functionality
Router::get('get-profile', 'UserController@get_profile');
Router::post('profile-picture', 'UserController@profile_picture');
Router::post('save-profile', 'UserController@save_profile');
Router::get('get-my-card', 'UserController@get_my_card');
Router::post('change-password', 'UserController@change_password');

Router::get('get-history', 'UserController@browsing_history');
Router::get('get-saved-offers', 'UserController@get_saved_offers');
Router::post('save-favourite/{postId}', 'UserController@save_favourite');
Router::post('remove-favourite/{id}', 'UserController@remove_favourite');

//Admin Features
Router::get('admin-settings', 'AdminController@general_settings');
Router::post('update-settings', 'AdminController@update_settings');
// Router::get('get-payment-settings', 'PaymentController@get_payment_settings');
// Router::post('save-payment-settings', 'PaymentController@save_payment_settings');
Router::get('purchased-list', 'AdminController@purchased_list');
Router::get('search-purchased', 'AdminController@search_purchased_list');
Router::get('subscribers', 'AdminController@subscribers_list');
Router::get('search-subscribers', 'AdminController@search_subscribers_list');
Router::post('permit-access', 'AdminController@permit_user_access');
Router::post('revoke-access', 'AdminController@revoke_user_access');

// wooCommerce
Router::publicGet('get-all-products', 'WooCommerceController@get_all_products');
Router::get('get_orders_by_user_id', 'WooCommerceController@get_orders_by_user_id');
//Webhook
Router::publicPost('webhook-refund-stripe', 'WebhookController@handle_webhook_refund_stripe');
Router::publicPost('webhook-refund-paypal', 'WebhookController@handle_webhook_refund_paypal');