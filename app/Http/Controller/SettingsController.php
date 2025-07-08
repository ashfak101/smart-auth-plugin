<?php

namespace AK\SMARTAUTH\Http\Controller;

defined('ABSPATH') || exit; // Prevent direct access

class SettingsController extends BaseController
{
    public function get_membership_data()
    {
    

        if ( is_user_logged_in() ) {
            // Get the user ID
            $user_id = get_current_user_id();

            $yo_card_general_settings = self::getOption('smart_auth_general_settings', []);
            $currency           = isset($yo_card_general_settings['selected_currency']) ? $yo_card_general_settings['selected_currency'] : 'GBP';
            $currency_symbol    = self::getAllowedCurrencySymble();
            $selected_symbol    = isset($currency_symbol[$currency]) ? $currency_symbol[$currency] : '£';
            $membership_plan = [
                array(
                    'id'    => '1',
                    'title' => 'YO Student Discount',
                    'type'  => 'Digital Membership',
                    'image' => SMART_AUTH_PLUGIN_URL . 'assets/img/yo-card-front.jpg',
                    'yearly_amount' => isset($yo_card_general_settings['standard_card_yearly_amount']) ? $yo_card_general_settings['standard_card_yearly_amount'] : 10,
                    'biennial_amount' => isset($yo_card_general_settings['standard_card_biennial_amount']) ? $yo_card_general_settings['standard_card_biennial_amount'] : 18,
                    'currency' => $selected_symbol,
                    'currency_Name' => $currency
                ),
                array(
                    'id' => '1',
                    'title' => 'YO Student Discount',
                    'currency' => $selected_symbol,
                    'type' => 'VIP Membership',
                    'image'=> SMART_AUTH_PLUGIN_URL . 'assets/img/yo-card-back.jpg',
                    'yearly_amount' => isset($yo_card_general_settings['vip_card_yearly_amount']) ? $yo_card_general_settings['vip_card_yearly_amount'] : 15,
                    'biennial_amount' => isset($yo_card_general_settings['vip_card_biennial_amount']) ? $yo_card_general_settings['vip_card_biennial_amount'] : 25,
                    'currency' => $selected_symbol,
                    'currency_Name' => $currency
                ),
            ];

            $data = [
                'currency' => $currency,
                'currency_symbol' => $selected_symbol,
                'membership_plan' => $membership_plan,
                'user' => [
                    'id' => $user_id,
                    'name' => get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true),
                    'email' => get_userdata($user_id)->user_email,
                    'phone' => get_user_meta($user_id, 'phone', true),
                    'address' => get_user_meta($user_id, 'address', true),
                    'city' => get_user_meta($user_id, 'city', true),
                    'country' => get_user_meta($user_id, 'country', true),
                    'zip' => get_user_meta($user_id, 'zip', true),
                    'membership_type' => get_user_meta($user_id, 'membership_type', true),
                    'membership_plan' => get_user_meta($user_id, 'membership_plan', true),
                    'membership_start_date' => get_user_meta($user_id, 'membership_start_date', true),
                    'membership_end_date' => get_user_meta($user_id, 'membership_end_date', true),
                    'membership_status' => get_user_meta($user_id, 'membership_status', true),
                ],
            ];

            return $this->response([
                'data' => $data,
                'status' => 'success',
            ], 200);
        } else {
            return $this->response('You must need to logged in', 400);
        }
    }
}
