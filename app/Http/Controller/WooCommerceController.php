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
}