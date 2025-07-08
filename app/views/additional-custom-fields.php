<?php
    // Add nonce for security and authentication.
    wp_nonce_field( 'promotion_nonce_action', 'promotion_nonce' );
?>

<div class="form-wrapper">
    <div class="field-wrapper field-row">
        <div class="col-6">
            <div class="sa-input-group">
                <label for="promotion_subtitle" class="input-label"><?php _e( 'Sub Title', 'smart-auth' ); ?></label>
                <div class="form-field">
                    <input type="text" id="promotion_subtitle" name="promotion_subtitle" class="widefat" value="<?php echo esc_attr($sub_title); ?>" />
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="sa-input-group">
                <label for="promotion_brand_name" class="input-label"><?php _e( 'Brand Name', 'smart-auth' ); ?></label>
                <div class="form-field">
                    <input type="text" id="promotion_brand_name" name="promotion_brand_name" class="widefat" value="<?php echo esc_attr($brand_name); ?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="field-wrapper field-row">
        <div class="col-6">
            <div class="image-group">
                <div class="img-selector">
                    <label class="input-label" for="promotion_barnd_logo"><?php _e( 'Logo', 'smart-auth' ); ?></label>
                    <input type="hidden" id="promotion_barnd_logo" name="promotion_barnd_logo" value="<?php echo esc_attr( $logo ); ?>" class="widefat">
                    <button type="button" class="btn btn-img" id="promotion_barnd_logo_button"><?php _e( 'Barnd Logo', 'smart-auth' ); ?></button>
                </div>
                <div class="img-preview-logo">
                    <img id="promotion_barnd_logo_preview" class="img-thumb" src="<?php echo esc_url( $logo ); ?>">
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="image-group">
                <div class="img-selector">
                    <label class="input-label" for="promotion_banner"><?php _e( 'Banner', 'smart-auth' ); ?></label>
                    <input type="hidden" id="promotion_banner" name="promotion_banner" value="<?php echo esc_attr( $banner ); ?>" class="widefat">
                    <button type="button" class="btn btn-img" id="promotion_banner_button"><?php _e( 'Promotion Banner', 'smart-auth' ); ?></button>
                </div>
                <div class="img-preview-banner">
                    <img id="promotion_banner_preview" class="img-thumb" src="<?php echo esc_url( $banner ); ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="field-wrapper field-row">
        <div class="col-6">
            <label for="promotion_location" class="input-label"><?php _e( 'Location', 'smart-auth' ); ?></label>
            <div class="form-field">
                <input type="text" id="promotion_location" name="promotion_location" value="<?php echo esc_attr( $location ); ?>" class="widefat">
            </div>
        </div>

        <div class="col-6">
            <label for="promotion_coupon" class="input-label"><?php _e( 'Coupon Code', 'smart-auth' ); ?></label>
            <div class="form-field">
                <input type="text" id="promotion_coupon" name="promotion_coupon" value="<?php echo esc_attr( $coupon ); ?>" class="widefat">
            </div>
        </div>
    </div>

    <div class="field-wrapper field-row">
        <div class="sa-input-group">
            <label for="promotion_brand_link" class="input-label"><?php _e( 'Brand Link', 'smart-auth' ); ?></label>
            <div class="form-field">
                <input type="url" id="promotion_brand_link" name="promotion_brand_link" style="width: 97%;" value="<?php echo esc_url( $brand_link ); ?>" class="widefat">
            </div>
        </div>
    </div>

    <div class="field-wrapper field-row">
        <div class="sa-input-group">
            <label for="promotion_terms" class="input-label"><?php _e( 'Terms and Conditions', 'smart-auth' ); ?></label>
            <div class="form-field">
                <textarea id="promotion_terms" name="promotion_terms" class="widefat"><?php echo esc_textarea( $terms ); ?></textarea>
            </div>
        </div>
    </div>
</div>