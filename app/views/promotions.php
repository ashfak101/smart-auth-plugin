
<?php
$sub_title  = isset($post_meta['_promotion_subtitle']) ? $post_meta['_promotion_subtitle'] : '';
$logo       = isset($post_meta['_promotion_logo']) ? $post_meta['_promotion_logo'] : '';
$banner     = isset($post_meta['_promotion_banner']) ? $post_meta['_promotion_banner'] : '';
$location   = isset($post_meta['_promotion_location']) ? $post_meta['_promotion_location'] : '';
$terms      = isset($post_meta['_promotion_terms']) ? $post_meta['_promotion_terms'] : '';
$coupon     = isset($post_meta['_promotion_coupon']) ? $post_meta['_promotion_coupon'] : '';
$layout_class = $atts['layout'] === 'vertical' ? 'promotion-vertical' : 'promotion-horizontal';
?>
<div class="promotion <?php echo esc_attr($layout_class)?>">
    <div class="promotion-thumnnail">
        <img src="<?php echo esc_url($featured_image_url); ?>" alt="Promotion Thumbnail">
    </div>
    <div class="promotion-content-wrapper">
    <div class="promotion-content">
        <span class="promotion-tag">
            <?php
            if ($tags) { ?>
                <?php foreach ($tags as $tag): ?>
                    <?php
                        //Get the first word of tag as name
                        $tag_name = current(explode('-', $tag));
                        $tag_class = 'tag-' . strtolower(sanitize_title($tag_name));
                    ?>
                    <span class="promotion-tag-item <?php echo esc_attr($tag_class); ?>"><?php echo esc_html(ucfirst($tag_name)); ?></span>
                <?php endforeach; ?>
            <?php }
            ?>
        </span>
        <h2 class="promotion-title"><?php echo get_the_title(); ?></h2>
        <div class="promotion-sub-title">
            <?php echo esc_html( $sub_title ); ?>
        </div>
        <div class="call-to-action">
            <a href="<?php echo esc_url($permalink); ?>" class="btn btn-primary">Redeem with Yocard</a>
        </div>
    </div>
    </div>
</div>