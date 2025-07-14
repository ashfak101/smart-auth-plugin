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
   public function get_orders_by_user_id($user_id)
   {
       // Get orders for a specific user
       $args = [
           'customer_id' => $user_id,
           'status' => 'any', // Get all statuses
           'limit' => -1, // Get all orders
       ];

       $orders = wc_get_orders($args);
       $order_data = [];

       foreach ($orders as $order) {
           $order_data[] = [
               'id' => $order->get_id(),
               'date' => $order->get_date_created()->date('Y-m-d H:i:s'),
               'total' => $order->get_total(),
               'status' => $order->get_status(),
           ];
       }

       return [
              'status' => 'success',
              'data' => $order_data,
       ];
   }
}