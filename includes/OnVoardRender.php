<?php
if (!defined('ABSPATH')) {
    exit;
}


class OnVoardRender {

    public static function onvoard_loader($args) {
        if (empty($args['loader_script_url'])) { return "[OnVoard Loader Error] Missing loader_script_url"; }

        ?>
            <script>
              (function() {
                window.OnVoard = window.OnVoard || function() {
                  (window.OnVoard.q = window.OnVoard.q || []).push(arguments);
                };
                var script = document.createElement("script");
                var parent = document.getElementsByTagName("script")[0].parentNode;
                script.async = 1;
                script.src = "<?php echo esc_url($args['loader_script_url']); ?>";
                parent.appendChild(script);
              })();
            </script>
        <?php
    }

    public static function onvoard_conversion_tracking($args) {
        if (empty($args['account_id'])) { return "[OnVoard Conversion Tracking Error] Missing account_id"; }

        ?>
          <div class='ov-app ov-app-woocommerce-conversion-tracking' data-app='conversion-tracking' data-account-id='<?php echo esc_attr($args['account_id']); ?>'></div>
        <?php
    }

    public static function onvoard_web_tracking($args) {
        if (empty($args['token'])) { return "[OnVoard Web Tracking Error] Missing token"; }

        ?>
          <div class='ov-app ov-app-web-tracking' data-app='web-tracking' data-token='<?php echo esc_attr($args['token']); ?>'></div>
        <?php

        if ($args['auto_track'] === true) {
            ?>
              <div
                class="ov-app ov-app-web-tracking-woocommerce"
                data-app="web-tracking-woocommerce"
              ></div>

              <script>
                var ovData = window.OnVoardData || {};
                OnVoard("web_tracking_woocommerce", "identify", ovData);
            <?php

            if ($args['track_checkout_started'] === true) {
                ?>
                  OnVoard("web_tracking_woocommerce", "trackCheckoutStarted", ovData);
                <?php
            }

            if ($args['track_product_viewed'] === true) {
                ?>
                  OnVoard("web_tracking_woocommerce", "trackProductViewed", ovData);
                <?php
            }

            if ($args['track_cart_item_added'] === true) {
                ?>
                  OnVoard("web_tracking_woocommerce", "trackCartItemAdded", ovData);
                <?php
            }

            ?>
              </script>
            <?php
        }
    }

    public static function onvoard_aov_progress_bar($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard AOV Progress Bar Error] Missing id"; }

        ?>
          <div class='ov-app ov-app-aov-progress-bar' data-app='aov-progress-bar' data-app-id='<?php echo esc_attr($args['id']); ?>'></div>
        <?php
    }

    public static function onvoard_back_in_stock($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard Back-In-Stock] Missing id"; }

        ?>
          <div class='ov-app ov-app-back-in-stock' data-app='back-in-stock' data-app-id='<?php echo esc_attr($args['id']); ?>'></div>
        <?php
    }

    public static function onvoard_back_in_stock_floating_button($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard Back-In-Stock Floating Button Error] Missing id"; }

        ?>
          <div class='ov-app ov-app-back-in-stock-floating-button' data-app='back-in-stock-floating-button' data-app-id='<?php echo esc_attr($args['id']); ?>'></div>
        <?php
    }

    public static function onvoard_back_in_stock_inline_button($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard Back-In-Stock Inline Button Error] Missing id"; }

        ?>
          <div class='ov-app ov-app-back-in-stock-inline-button' data-app='back-in-stock-inline-button' data-app-id='<?php echo esc_attr($args['id']); ?>'></div>
        <?php
    }

    public static function onvoard_back_in_stock_inline_form($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard Back-In-Stock Inline Form Error] Missing id"; }

        ?>
          <div class='ov-app ov-app-back-in-stock-inline-form' data-app='back-in-stock-inline-form' data-app-id='<?php echo esc_attr($args['id']); ?>'></div>
        <?php
    }

    public static function onvoard_back_in_stock_inline_text($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard Back-In-Stock Inline Text Error] Missing id"; }

        ?>
          <div class='ov-app ov-app-back-in-stock-inline-text' data-app='back-in-stock-inline-text' data-app-id='<?php echo esc_attr($args['id']); ?>'></div>
        <?php
    }

    public static function onvoard_reviews_widget($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard Reviews Widget Error] Missing id"; }

        $product_id = '';
        if (isset($product)) {
            $product_id = $product->get_id();
        }

        if (empty($product_id) && isset($args['product_id'])) { $product_id = $args['product_id']; }
        if (empty($product_id)) { return "[OnVoard Reviews Widget Error] Can't identify product"; }

        ?>
          <div
            class='ov-app ov-app-reviews-widget'
            data-app='reviews-widget'
            data-app-id='<?php echo esc_attr($args['id']); ?>'
            data-product-external-id='<?php echo esc_attr($product_id); ?>'
           ></div>
        <?php
    }

    public static function onvoard_star_rating($args) {
        global $product;
        if (empty($args['id'])) { return "[OnVoard Star Rating Error] Missing id"; }

        $product_id = '';
        if (isset($product)) {
            $product_id = $product->get_id();
        }

        if (empty($product_id) && isset($args['product_id'])) { $product_id = $args['product_id']; }
        if (empty($product_id)) { return "[OnVoard Star Rating Error] Can't identify product"; }

        ?>
          <div
            class='ov-app ov-app-star-rating'
            data-app='star-rating'
            data-app-id='<?php echo esc_attr($args['id']); ?>'
            data-product-external-id='<?php echo esc_attr($product_id); ?>'
           ></div>
        <?php
    }

    public static function onvoard_prompt($args) {
        if (empty($args['account_id'])) { return "[OnVoard Prompt] Missing account_id"; }

        ?>
          <div class='ov-app ov-app-prompt' data-app='prompt' data-account-id='<?php echo esc_attr($args['account_id']); ?>'></div>
        <?php
    }

    public static function onvoard_recommender($args) {
        if (empty($args['id'])) { return "[OnVoard Recommender Error] Missing id"; }

        ?>
          <div class='ov-app ov-app-recommender' data-app='recommender' data-app-id='<?php echo esc_attr($args['id']); ?>'></div>
        <?php
    }

    public static function onvoard_recommender_tracking($args) {
        if (empty($args['account_id'])) { return "[OnVoard Recommender Tracking Error] Missing account_id"; }

        ?>
          <div class='ov-app ov-app-recommender-tracking' data-app='recommender-tracking' data-account-id='<?php echo esc_attr($args['account_id']); ?>'></div>
        <?php
    }
}

