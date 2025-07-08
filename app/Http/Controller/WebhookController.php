<?php

namespace AK\SMARTAUTH\Http\Controller;

defined('ABSPATH') || exit; // Prevent direct access

use \WP_REST_Request;
use \WP_REST_Response;
use AK\SMARTAUTH\Http\Model\OrderHistory;

class WebhookController extends BaseController
{
    /**
     * Handle webhook events from Stripe and PayPal.
     *
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response The response object.
     */
    public function handle_webhook_refund_stripe( \WP_REST_Request $request )
    {
        $body = $request->get_body();
        $event = json_decode($body, true);

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return $this->response([
                'message' => 'Invalid JSON'
            ], 400);
        }

        // Handle Stripe webhook events
        $customer_id    = $event['data']['object']['customer'];
        $payment_id     = $event['data']['object']['payment_intent'];
        $amount         = intval($event['data']['object']['amount']) / 100;

        $order_history = new OrderHistory();
        $order = $order_history->get_order_row($customer_id, $payment_id);

        if( ! $order ) {
            return $this->response([
                'message' => 'Order not found'
            ], 404);
        }

        $oh = OrderHistory::getInstance();

        $oh->update([
            'refund_status' => 'refunded',
            'refund_date' => current_time('mysql'),
        ], $order->id );

        //Update user meta for membership
        $membership        = get_user_meta( $order->user_id, 'yo_student_card_membership', true );
        $membership_data   = unserialize( $membership );
        $card_no           = isset($membership_data['_yo_card_no']) ? $membership_data['_yo_card_no'] : '';

        //Update user meta
        update_user_meta( $order->user_id , 'yo_student_card_membership', serialize( array(
            '_is_purchased' => 'no',
            '_membership_type' => '',
            '_yo_card_no' => $card_no,
            '_valid_till' => date('Y-m-d', strtotime("-1 day")),
        ) ) );

        return $this->response([
            'data' => [
                'customer_id' => $customer_id,
                'payment_id' => $payment_id,
                'amount' => $amount,
                'table_id' => $order->id
            ],
            'message' => 'Refund processed successfully'
        ], 200);
    }


    public function handle_webhook_refund_paypal( \WP_REST_Request $request )
    {
        $body = $request->get_body();
        $event = json_decode($body, true);

        // Validate JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response([
                'message' => 'Invalid JSON payload'
            ], 400);
        }

        // Extract resource and validate required fields
        $resource = $event['resource'] ?? null;
        if (!$resource || empty($resource['links'])) {
            return $this->response([
                'message' => 'Invalid webhook data: missing resource or links'
            ], 400);
        }

        // Extract payment ID from "rel: up" link
        $capture_id = null;
        foreach ($resource['links'] as $link) {
            if ($link['rel'] === 'up') {
                $capture_id = basename($link['href']);
                break;
            }
        }

        if (!$capture_id) {
            return $this->response([
                'message' => 'Capture ID not found in webhook data'
            ], 400);
        }

        // Fetch order using the payment ID
        $order_history = new OrderHistory();
        $order = $order_history->get_order_row_by_capture_id($capture_id);

        if (!$order) {
            return $this->response([
                'message' => 'Order not found for the provided Payment ID'
            ], 404);
        }

        // Update order history with refund details
        $order_history->update([
            'refund_status' => 'refunded',
            'refund_date' => current_time('mysql'),
        ], $order->id);

        // Update user meta for membership
        $membership       = get_user_meta( $order->user_id, 'yo_student_card_membership', true );
        $membership_data  = unserialize( $membership );
        $card_no          = isset($membership_data['_yo_card_no']) ? $membership_data['_yo_card_no'] : '';

        update_user_meta($order->user_id, 'yo_student_card_membership', serialize([
            '_is_purchased' => 'no',
            '_membership_type' => '',
            '_yo_card_no' => $card_no,
            '_valid_till' => date('Y-m-d', strtotime('-1 day')),
        ]));

        // Return successful response
        return $this->response([
            'data' => [
                'paypal_capture_id' => $capture_id,
                'order_id' => $order->id,
            ],
            'message' => 'Refund processed successfully'
        ], 200);
    }
}
