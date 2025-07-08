<?php

namespace AK\SMARTAUTH\Http\Request;

defined('ABSPATH') || exit; // Prevent direct access

/**
 * Class ValidatorHandler
 *
 * This class extends the Validator class and is responsible for handling
 * validation logic for HTTP requests within the xStudentDiscountCard application.
 *
 * @package AK\SMARTAUTH\Http\Request
 */
class ValidatorHandler extends Validator
{
    /**
     * Validate the request to save a user profile.
     *
     * @param Request $request The request object.
     *
     * @return array The validation errors.
     */
    public static function validateSaveProfileRequest( Request $request ) {
        return $request->validate(
            $request->only(['name', 'email', 'dob', 'gender', 'house_name', 'town', 'postcode', 'country']),
            [
                'name'        => 'required',
                'email'       => 'required',
                'dob'         => 'required',
                'gender'      => 'required',
                'house_name'  => 'required',
                'town'        => 'required',
                'postcode'    => 'required',
                'country'     => 'required',
            ],
            [
                'name.required'        => __( 'Student name is required.', 'smart-auth' ),
                'email.required'       => __( 'Email address is required.', 'smart-auth' ),
                'dob.required'         => __( 'Date of Birth is required.', 'smart-auth' ),
                'gender.required'      => __( 'Gender is required.', 'smart-auth' ),
                'house_name.required'  => __( 'House name is required.', 'smart-auth' ),
                'town.required'        => __( 'Town is required.', 'smart-auth' ),
                'postcode.required'    => __( 'Postcode is required.', 'smart-auth' ),
                'country.required'     => __( 'Country is required.', 'smart-auth' ),
            ]
        );
    }

    public static function validateChangePasswordRequest ( Request $request)
    {
        return $request->validate(
            $request->only(['current_password', 'new_password', 'confirm_new_password']),
            [
                'current_password' => 'required',
                'new_password'     => 'required',
                'confirm_new_password' => 'required|same:new_password',
            ],
            [
                'current_password.required' => __( 'Current password is required.', 'smart-auth' ),
                'new_password.required'     => __( 'New password is required.', 'smart-auth' ),
                'confirm_new_password.required' => __( 'Confirm password is required.', 'smart-auth' ),
                'confirm_new_password.same'     => __( 'New password and confirm password must be same.', 'smart-auth' ),
            ]
        );
    }

    public static function validateGeneralSettings( Request $request)
    {
        return $request->validate(
            $request->only(['auth_page_id', 'profile_page_id', 'standard_card_yearly_amount', 'standard_card_biennial_amount', 'vip_card_yearly_amount', 'vip_card_biennial_amount', 'selected_currency']),
            [
                'auth_page_id'                  => 'required',
                'profile_page_id'               => 'required',
                'standard_card_yearly_amount'   => 'required|numeric',
                'standard_card_biennial_amount' => 'required|numeric',
                'vip_card_yearly_amount'        => 'required|numeric',
                'vip_card_biennial_amount'      => 'required|numeric',
                'selected_currency'             => 'required',
            ],
            [
                'auth_page_id.required'                  => __( 'Auth page ID is required.', 'smart-auth' ),
                'profile_page_id.required'               => __( 'Profile page ID is required.', 'smart-auth' ),
                'standard_card_yearly_amount.required'   => __( 'Standard card yearly amount is required.', 'smart-auth' ),
                'standard_card_yearly_amount.numeric'    => __( 'Standard card yearly amount must be a number.', 'smart-auth' ),
                'standard_card_biennial_amount.required' => __( 'Standard card biennial amount is required.', 'smart-auth' ),
                'standard_card_biennial_amount.numeric'  => __( 'Standard card biennial amount must be a number.', 'smart-auth' ),
                'vip_card_yearly_amount.required'        => __( 'VIP card yearly amount is required.', 'smart-auth' ),
                'vip_card_yearly_amount.numeric'         => __( 'VIP card yearly amount must be a number.', 'smart-auth' ),
                'vip_card_biennial_amount.required'      => __( 'VIP card biennial amount is required.', 'smart-auth' ),
                'vip_card_biennial_amount.numeric'       => __( 'VIP card biennial amount must be a number.', 'smart-auth' ),
                'selected_currency.required'             => __( 'Currency is required.', 'smart-auth' ),
            ]
        );
    }
}