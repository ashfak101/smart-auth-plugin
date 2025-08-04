<?php
// Prevent direct access
namespace AK\SMARTAUTH\Http\Controller;
defined('ABSPATH') || exit;


class WooCommerceController extends BaseController
{
   public function get_all_products()
   {
       // Get all products from WooCommerce
       $args = [
           'post_type' => 'product',
           'post_status' => 'publish',
           'posts_per_page' => -1, // Get all products
       ];

       $query = new \WP_Query($args);
       $products = [];

       if ($query->have_posts()) {
           while ($query->have_posts()) {
               $query->the_post();
               global $product;

               $products[] = [
                   'id' => get_the_ID(),
                   'title' => get_the_title(),
                   'price' => $product->get_price(),
                   'image' => wp_get_attachment_url($product->get_image_id()),
                   'link' => get_permalink(),
               ];
           }
           wp_reset_postdata();
       }

       return $this->response($products);
   }
//    get woocommerce orders by user id

       public function get_orders_by_user_id($request)
{
    // Get the user ID from the query parameters
    $user_id = $request->get_param('user_id');

    // Validate the user ID
    if (!is_numeric($user_id) || $user_id <= 0) {
        return [
            'status' => 'error',
            'message' => 'Invalid user ID',
        ];
    }

    // Get orders for the specified user
    $args = [
        'customer_id' => $user_id,
        'status' => 'any', // Get all statuses
        'limit' => -1, // Get all orders
    ];

    $orders = wc_get_orders($args);
    $order_data = [];

    // Get WooCommerce currency
    $currency = get_woocommerce_currency();

    foreach ($orders as $order) {
        $order_data[] = [
            'id' => $order->get_id(),
            'date' => $order->get_date_created()->date('Y-m-d H:i:s'),
            'total' => $order->get_total(),
            'currency' => $currency, // Add currency to the response
            'status' => $order->get_status(),
        ];
    }

    return [
        'status' => 'success',
        'data' => $order_data,
    ];
}
public function get_single_order_details($request)
{
    // Get the order ID from the query parameters
    $order_id = $request->get_param('order_id');

    // Validate the order ID
    if (!is_numeric($order_id) || $order_id <= 0) {
        return [
            'status' => 'error',
            'message' => 'Invalid order ID',
        ];
    }

    // Fetch the order using the correct order ID
    $order = wc_get_order($order_id);
    if (!$order) {
        return [
            'status' => 'error',
            'message' => 'Order not found',
        ];
    }

    // Get order items
    $order_items = $order->get_items();
    $items = [];
    foreach ($order_items as $item_id => $item) {
        $product = $item->get_product();
        $items[] = [
            'product_id' => $product ? $product->get_id() : null,
            'product_name' => $product ? $product->get_name() : null,
            'quantity' => $item->get_quantity(),
            'total' => $item->get_total(),
            'subtotal' => $item->get_subtotal(),
            'price' => $product ? $product->get_price() : null,
            'image' => $product ? wp_get_attachment_url($product->get_image_id()) : null,
        ];
    }

    // Return the order details
    return [
        'status' => 'success',
        'data' => [
            'order_id' => $order->get_id(),
            'date_created' => $order->get_date_created()->date('Y-m-d H:i:s'),
            'total' => $order->get_total(),
            'currency' => get_woocommerce_currency(),
            'status' => $order->get_status(),
            'items' => $items,
        ],
    ];
}
        public function download_order_invoice($request)
        {
            $order_id = $request->get_param('order_id');

            // Validate the order ID
            if (!is_numeric($order_id) || $order_id <= 0) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid order ID',
                ];
            }

            $order = wc_get_order($order_id);
            if (!$order) {
                return [
                    'status' => 'error',
                    'message' => 'Order not found',
                ];
            }

            // Generate a simple invoice (as plain text for demo; use a PDF library for real invoices)
            $invoice = "Invoice for Order #" . $order->get_id() . "\n";
            $invoice .= "Date: " . $order->get_date_created()->date('Y-m-d H:i:s') . "\n";
            $invoice .= "Total: " . $order->get_total() . " " . get_woocommerce_currency() . "\n";
            $invoice .= "Status: " . $order->get_status() . "\n";
            $invoice .= "Items:\n";
            foreach ($order->get_items() as $item) {
                $product = $item->get_product();
                $invoice .= "- " . ($product ? $product->get_name() : 'Product') . " x " . $item->get_quantity() . " = " . $item->get_total() . "\n";
            }

            // Set headers for file download
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="invoice-order-' . $order->get_id() . '.txt"');

            echo $invoice;
            exit;
        }
}