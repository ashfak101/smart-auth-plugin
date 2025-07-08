<?php
namespace AK\SMARTAUTH\Services;

defined('ABSPATH') || exit; // Prevent direct access

class StripePayment
{
    protected $secret_key;

    public function __construct($secret_key)
    {
        $this->secret_key = $secret_key;
    }

    public function createCustomer( $email, $name )
    {
        $response = wp_remote_post('https://api.stripe.com/v1/customers', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secret_key,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'email' => sanitize_email($email),
                'name' => sanitize_text_field($name),
            ],
        ]);

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['id'])) {
            return $data['id'];
        } else {
            throw new \Exception('Failed to create customer: ' . $data['error']['message']);
        }
    }

    public function createPaymentIntent( $amount, $currency, $customer_id )
    {
        $response = wp_remote_post('https://api.stripe.com/v1/payment_intents', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secret_key,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'amount' => intval($amount),
                'currency' => sanitize_text_field($currency),
                'customer' => sanitize_text_field($customer_id),
                'automatic_payment_methods[enabled]' => 'true',
            ],
        ]);

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ( isset($data['id']) ) {
            return $data;
        } else {
            throw new \Exception('Failed to create payment intent: ' . $data['error']['message']);
        }
    }

    public function createRefund($payment_intent_id, $amount = null) {
        $body = [
            'payment_intent' => sanitize_text_field($payment_intent_id),
        ];

        if ($amount !== null) {
            $body['amount'] = intval($amount);
        }


        $response = wp_remote_post('https://api.stripe.com/v1/refunds', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secret_key,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => $body,
        ]);

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['id'])) {
            return $data;
        } else {
            throw new \Exception('Failed to create refund: ' . $data['error']['message']);
        }
    }
}