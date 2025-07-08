<?php

namespace AK\SMARTAUTH\Http\Controller;

defined('ABSPATH') || exit; // Prevent direct access

use AK\SMARTAUTH\Http\Request\ValidatorHandler;
use AK\SMARTAUTH\Http\Model\OrderHistory;
use AK\SMARTAUTH\Http\Model\User;
use AK\SMARTAUTH\Services\CsvWriter;

class AdminController extends BaseController
{
    public function general_settings()
    {
        // check is the route is called by admin user
        if ( ! current_user_can( 'manage_options' ) ) {
            return $this->response([
                'message' => 'You do not have permission to access this page.',
                'status' => 'error',
            ], 403);
        }
        $defaults = [
            'auth_page_id'                  => '',
            'profile_page_id'               => '',
            'card_front_img'                => esc_url( SMART_AUTH_PLUGIN_URL . 'assets/img/yo-card-back.jpg' ),
            'card_back_img'                 => esc_url( SMART_AUTH_PLUGIN_URL . 'assets/img/yo-card-front.jpg' ),
            'standard_card_yearly_amount'   => 10,
            'standard_card_biennial_amount' => 18,
            'vip_card_yearly_amount'        => 15,
            'vip_card_biennial_amount'      => 25,
            'wp_pages'                      => self::getWPPages(),
            'currencies'                    => self::getAllowedCurrency(),
            'selected_currency'             => 'GBP',
        ];

        $existingSettings = self::getOption('smart_auth_general_settings', []);
        // print_r($existingSettings);
        // exit;
        if ( !$existingSettings ) {
            return $this->response([
                'settings' => $defaults,
                'status' => 'success',
            ], 200);
        }

        $settings = wp_parse_args($existingSettings, $defaults);

        if( empty($settings['card_front_img']) ) {
            $settings['card_front_img'] = esc_url( SMART_AUTH_PLUGIN_URL . 'assets/img/yo-card-back.jpg' );
        }

        if( empty($settings['card_back_img']) ) {
            $settings['card_back_img'] =  esc_url( SMART_AUTH_PLUGIN_URL . 'assets/img/yo-card-front.jpg' );
        }

        return $this->response([
            'settings' => $settings,
            'status' => 'success',
        ], 200);
    }

    /**
     * Update general settings and store them in the options table.
     *
     * @return mixed The response of the update action, either errors or success message.
     */
    public function update_settings()
    {
        // Validate the input data using the ValidatorHandler class.
        $errors = ValidatorHandler::validateGeneralSettings($this->request);

        // If there are validation errors, return the errors with a 403 status code.
        if (count($errors) > 0) {
            return $this->response($errors, 403);
        }

        $existingSettings = self::getOption('smart_auth_general_settings', []);

        if (! empty($_FILES['card_front_img']['name'])) {
            // Get the WordPress upload directory
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . SMART_AUTH_PLUGIN_ASSET_ID;

            if (isset($existingSettings['card_front_img']) && !empty($existingSettings['card_front_img'])) {
                $file = $upload_path . '/' . $existingSettings['card_front_img'];
                unlink($file);
            }

            // Upload the new file
            $card_front_img = self::handle_file_upload('card_front_img');
            if (is_wp_error($card_front_img)) {
                return $this->response($card_front_img->get_error_message(), 400);
            }
        } else {
            $card_front_img = isset( $existingSettings['card_front_img'] ) && ! empty( $existingSettings['card_front_img'] ) ? $existingSettings['card_front_img'] : '';
        }

        if (! empty($_FILES['card_back_img']['name'])) {
            // Get the WordPress upload directory
            $upload_dir = wp_upload_dir();
            $upload_path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . SMART_AUTH_PLUGIN_ASSET_ID;

            if (isset($existingSettings['card_back_img']) && !empty($existingSettings['card_back_img'])) {
                $file = $upload_path . '/' . $existingSettings['card_back_img'];
                unlink($file);
            }

            // Upload the new file
            $card_back_img = self::handle_file_upload('card_back_img');
            if (is_wp_error($card_back_img)) {
                return $this->response($card_back_img->get_error_message(), 400);
            }
        } else {
            $card_back_img = isset( $existingSettings['card_back_img'] ) && ! empty( $existingSettings['card_back_img'] ) ? $existingSettings['card_back_img'] : '';
        }

        // Prepare the data array to store the settings.
        $data = array(
            'auth_page_id'                  => intval($this->request->get('auth_page_id')),
            'profile_page_id'               => intval($this->request->get('profile_page_id')),
            'card_front_img'                => sanitize_text_field( $card_front_img ),
            'card_back_img'                 => sanitize_text_field(  $card_back_img ),
            'standard_card_yearly_amount'   => intval($this->request->get('standard_card_yearly_amount')),
            'standard_card_biennial_amount' => intval($this->request->get('standard_card_biennial_amount')),
            'vip_card_yearly_amount'        => intval($this->request->get('vip_card_yearly_amount')),
            'vip_card_biennial_amount'      => intval($this->request->get('vip_card_biennial_amount')),
            'selected_currency'             => sanitize_text_field($this->request->get('selected_currency')),
        );

        // Update or insert the settings in the wp_options table.
        self::updateOption('smart_auth_general_settings', $data);

        $settings = $data;
        $settings['wp_pages'] = self::getWPPages();

        return $this->response([
            'settings' => $settings,
            'message' => 'Settings updated successfully.',
            'status' => 'success',
        ], 200);
    }


}
