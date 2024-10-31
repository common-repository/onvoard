<?php
if (!defined('ABSPATH')) {
    exit;
}


class OnVoardHooks {
    function __construct() {
        $this->sync_hooks();
    }

    function sync_hooks() {
		add_action('wp_head', array($this, "onvoard_wp_head"));
		add_action('wp_footer', array($this, "onvoard_wp_footer"));
		add_action('woocommerce_single_product_summary', array($this, "onvoard_woocommerce_single_product_summary"));
		add_action('woocommerce_after_single_product_summary', array($this, "onvoard_woocommerce_after_single_product_summary"));
		add_action('woocommerce_after_single_product', array($this, "onvoard_woocommerce_after_single_product"));
		add_action('woocommerce_product_meta_start', array($this, "onvoard_woocommerce_product_meta_start"));
		add_action('woocommerce_after_shop_loop_item_title', array($this, "onvoard_woocommerce_after_shop_loop_item_title"));
		add_action('woocommerce_after_shop_loop_item', array($this, "onvoard_woocommerce_after_shop_loop_item"));
		add_action('woocommerce_after_shop_loop', array($this, "onvoard_woocommerce_after_shop_loop"));
		add_action('woocommerce_before_cart', array($this, "onvoard_woocommerce_before_cart"));
        add_action('woocommerce_after_cart', array($this, "onvoard_woocommerce_after_cart"));
        add_action('woocommerce_thankyou', array($this, "onvoard_woocommerce_thankyou"));
    }

    function install_scripts() {
        $option_value = get_option('onvoard_install_scripts', 'no');
        return $option_value == 'yes';
    }

    function strip_explode($list) {
        if ($list instanceof WP_Error) { return []; }
        return array_filter(explode(', ', strip_tags($list)));
    }

    function calculate_discount($regular_price, $sale_price, $on_sale=true) {
        if ($on_sale == false) { return 0; }
        if ($sale_price >= $regular_price) { return 0; }
        return $regular_price - $sale_price;
    }

    function get_page_type() {
        if (is_product()) {
            return 'product';
        }

        if (is_cart()) {
            return 'cart';
        }

        if (is_checkout() && !empty(is_wc_endpoint_url('order-received'))) {
            return 'thank-you';
        }

        if (is_checkout()) {
            return 'checkout';
        }

        return '';
    }

    function get_shop_json() {
		$shop_data = array(
			'currency' => get_woocommerce_currency(),
			'cart_url' => wc_get_cart_url(),
			'checkout_url' => wc_get_checkout_url(),
		);

        return json_encode($shop_data);
    }

    function get_cart_json() {
		$cart = WC()->cart;
		$items_data = array();
		foreach ($cart->get_cart() as $cart_item_key => $item_data) {
            $product = wc_get_product($item_data['product_id']);
            $variation_id = $item_data['variation_id'];

            $item_data['product_name'] = $product->get_name();
            $item_data['sku'] = $product->get_sku();
            $item_data['price'] = $product->get_price();
            $item_data['regular_price'] = $product->get_regular_price();
            $item_data['sale_price'] = $product->get_sale_price();
            $item_data['on_sale'] = $product->is_on_sale();
            $item_data['discount'] = $this->calculate_discount(
                $product->get_regular_price(),
                $product->get_sale_price(),
                $product->is_on_sale()
            );

            $item_data['permalink'] = $product->get_permalink();
		    $item_data['image_url'] = wp_get_attachment_image_url($product->get_image_id());

            if ($product->get_type() === "variable" && $variation_id != 0) {
            	$variation = wc_get_product($variation_id);
		        if (!empty($variation)) {
                    $item_data['variation_id'] = $variation_id;
                    $item_data['variation_name'] = implode(" / ", $variation->get_variation_attributes());
		            $item_data['sku'] = $variation->get_sku();
		            $item_data['price'] = $variation->get_price();
		            $item_data['regular_price'] = $variation->get_regular_price();
		            $item_data['sale_price'] = $variation->get_sale_price();
		            $item_data['on_sale'] = $variation->is_on_sale();
                    $item_data['discount'] = $this->calculate_discount(
                        $variation->get_regular_price(),
                        $variation->get_sale_price(),
                        $variation->is_on_sale()
                    );

            		$item_data['permalink'] = $variation->get_permalink();
		            $item_data['image_url'] = wp_get_attachment_image_url($variation->get_image_id());
		        }
            }

			array_push($items_data, $item_data);
		}

		$cart_data = array(
			'hash' => $cart->get_cart_hash(),
            'currency' => get_woocommerce_currency(),
			'total' => $cart->get_cart_contents_total(),
			'subtotal' => $cart->get_subtotal(),
			'discount_total' => $cart->get_discount_total(),
			'shipping_total' => $cart->get_shipping_total(),
			'items' => $items_data,
		);

        return json_encode($cart_data);
    }

    function get_product_json() {
        if (!is_product()) {
        	return json_encode(array(), JSON_FORCE_OBJECT);
        }

        $product = wc_get_product();
        $product_id = $product->get_id();
        $variation_ids = array();
        $variations_data = array();

        if ($product->get_type() === "variable") {
            $variation_ids = $product->get_children();
        }

        foreach($variation_ids as $variation_id) {
            $variation = wc_get_product($variation_id);
            if (empty($variation) || !$variation) { continue; };

            $variation_data = array(
                'id' => $variation->get_id(),
                'name' => implode(" / ", $variation->get_variation_attributes()), // https://wordpress.stackexchange.com/a/343091
                'sku' => $variation->get_sku(),
                'permalink' => $variation->get_permalink(),
                'status' => $variation->get_status(),
                'on_sale' => $variation->is_on_sale(),
                'manage_stock' => $variation->get_manage_stock(),
                'stock_quantity' => $variation->get_stock_quantity(),
                'stock_status' => $variation->get_stock_status(),
                'image_id'  => $variation->get_image_id(),
                'image_url' => wp_get_attachment_image_url($variation->get_image_id()),

                'price' => $variation->get_price(),
                'regular_price' => $variation->get_regular_price(),
                'sale_price' => $variation->get_sale_price(),
                'discount' => $this->calculate_discount(
                    $variation->get_regular_price(),
                    $variation->get_sale_price(),
                    $variation->is_on_sale()
                ),
            );

            array_push($variations_data, $variation_data);
        }

		$product_data = array(
			'id' => $product->get_id(),
			'name' => $product->get_name(),
			'type' => $product->get_type(),
			'slug' => $product->get_slug(),
			'permalink' => $product->get_permalink(),
			'sku' => $product->get_sku(),
			'status' => $product->get_status(),
			'on_sale' => $product->is_on_sale(),
            'manage_stock' => $product->get_manage_stock(),
            'stock_quantity' => $product->get_stock_quantity(),
			'stock_status' => $product->get_stock_status(),
			'image_id'  => $product->get_image_id(),
			'image_url' => wp_get_attachment_image_url($product->get_image_id()),

			'price' => $product->get_price(),
			'regular_price' => $product->get_regular_price(),
			'sale_price' => $product->get_sale_price(),
            'discount' => $this->calculate_discount(
                $product->get_regular_price(),
                $product->get_sale_price(),
                $product->is_on_sale()
            ),

			'variations' => $variations_data,
			'attributes' => $product->get_attributes(),
			'categories' => $this->strip_explode(wc_get_product_category_list($product_id)),
			'tags' => $this->strip_explode(wc_get_product_tag_list($product_id)),
			'meta_data' => $product->get_meta_data(),
		);

        return json_encode($product_data);
    }

    function get_customer_json() {
        $user = wp_get_current_user();
        $customer = new WC_Customer($user->ID);
        $last_order = $customer->get_last_order();

        $customer_data = array(
            'id' => $user->ID,
            'email' => $user->user_email,
            'first_name' => $user->user_firstname,
            'last_name' => $user->user_lastname,
            'is_paying_customer' => $customer->get_is_paying_customer(),
            'total_spent' => $customer->get_total_spent(),
            'order_count' => $customer->get_order_count(),
        );

        if (!empty($last_order)) {
            $customer_data['last_order_total'] = $last_order->get_total();
            $customer_data['last_order_subtotal'] = $last_order->get_subtotal();
            $customer_data['last_order_created_at'] = $last_order->get_date_created()->getOffsetTimestamp();
        }

        return json_encode($customer_data);
    }

    function get_order_json($order_id) {
		$order = wc_get_order($order_id);
		$items_data = array();

		foreach ($order->get_items() as $item) {
			$product_id = $item->get_product_id();
		    $product = wc_get_product($product_id);

			$item_data = $item->get_data();
		    $variation_id = $item_data['variation_id'];
			$product = $item->get_product();

		    $item_data['sku'] = $product->get_sku();
		    $item_data['price'] = $product->get_price();
		    $item_data['regular_price'] = $product->get_regular_price();
		    $item_data['sale_price'] = $product->get_sale_price();
		    $item_data['on_sale'] = $product->is_on_sale();
            $item_data['discount'] = $this->calculate_discount(
                $product->get_regular_price(),
                $product->get_sale_price(),
                $product->is_on_sale()
            );

		    $item_data['permalink'] = $product->get_permalink();
		    $item_data['image_url'] = wp_get_attachment_image_url($product->get_image_id());

		    if ($product->get_type() === "variable" && $variation_id != 0) {
		    	$variation = wc_get_product($variation_id);
		        if (!empty($variation)) {
		            $item_data['sku'] = $variation->get_sku();
		            $item_data['price'] = $variation->get_price();
		            $item_data['regular_price'] = $variation->get_regular_price();
		            $item_data['sale_price'] = $variation->get_sale_price();
		            $item_data['on_sale'] = $variation->is_on_sale();
		            $item_data['discount'] = $this->calculate_discount(
		                $variation->get_regular_price(),
		                $variation->get_sale_price(),
		                $variation->is_on_sale()
		            );

		    		$item_data['permalink'] = $variation->get_permalink();
		            $item_data['image_url'] = wp_get_attachment_image_url($variation->get_image_id());
		        }
		    }

		    array_push($items_data, $item_data);
		}

		$order_data = array(
		    'id' => $order->get_id(),
		    'customer_id' => $order->get_customer_id(),
		    'total' => $order->get_total(),
		    'subtotal' => $order->get_subtotal(),
		    'discount_total' => $order->get_discount_total(),
		    'shipping_total' => $order->get_shipping_total(),
		    'items' => $items_data,
		);

		return json_encode($order_data);
    }

    function onvoard_wp_head() {
    	if (!$this->install_scripts()) { return; }

        ?>
            <script>
              var ovData = {};
              ovData["ecommerce_platform"] = "woocommerce";
              ovData["page_type"] = "<?php echo esc_attr($this->get_page_type()); ?>";
              ovData["cart"] = <?php echo $this->get_cart_json(); ?>;
              ovData["product"] = <?php echo $this->get_product_json(); ?>;
              ovData["shop"] = <?php echo $this->get_shop_json(); ?>;
              ovData["customer"] = <?php echo $this->get_customer_json(); ?>;
              window.OnVoardData = ovData;
            </script>
        <?php

        $this->render_app_scripts_for_hook('wp_head');
    }

    function onvoard_wp_footer() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('wp_footer');
    }

    function render_app_script($config) {
        if ($config['app'] == 'loader') {
            OnVoardRender::onvoard_loader(array(
                "loader_script_url" => $config['loader_script_url']
            ));
        }

        if ($config['app'] == 'conversion-tracking') {
            OnVoardRender::onvoard_conversion_tracking(array(
                "account_id" => $config['account_id']
            ));
        }

        if ($config['app'] == 'web-tracking') {
            OnVoardRender::onvoard_web_tracking(array(
                "token" => $config['token'],
                "auto_track" => $config['auto_track'],
                "track_checkout_started" => $config['track_checkout_started'],
                "track_product_viewed" => $config['track_product_viewed'],
                "track_cart_item_added" => $config['track_cart_item_added'],
            ));
        }

        if ($config['app'] == 'aov-progress-bar') {
            OnVoardRender::onvoard_aov_progress_bar(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'back-in-stock') {
            OnVoardRender::onvoard_back_in_stock(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'back-in-stock-floating-button') {
            OnVoardRender::onvoard_back_in_stock_floating_button(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'back-in-stock-inline-button') {
            OnVoardRender::onvoard_back_in_stock_inline_button(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'back-in-stock-inline-form') {
            OnVoardRender::onvoard_back_in_stock_inline_form(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'back-in-stock-inline-text') {
            OnVoardRender::onvoard_back_in_stock_inline_text(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'reviews-widget') {
            OnVoardRender::onvoard_reviews_widget(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'star-rating') {
            OnVoardRender::onvoard_star_rating(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'prompt') {
            OnVoardRender::onvoard_prompt(array(
                "account_id" => $config['account_id']
            ));
        }

        if ($config['app'] == 'recommender') {
            OnVoardRender::onvoard_recommender(array(
                "id" => $config['id']
            ));
        }

        if ($config['app'] == 'recommender-tracking') {
            OnVoardRender::onvoard_recommender_tracking(array(
                "account_id" => $config['account_id']
            ));
        }

        return '';
    }

    function render_app_scripts_for_hook($hook) {
        $raw_config = get_option('onvoard_app_scripts_config', '[]');
        $config_rows = json_decode($raw_config, true);

        foreach ($config_rows as $config_row) {
            if ($config_row['hook'] != $hook) { continue; }
            $this->render_app_script($config_row);
        }
    }

    function onvoard_woocommerce_single_product_summary() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_single_product_summary');
    }

    function onvoard_woocommerce_after_single_product_summary() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_after_single_product_summary');
    }

    function onvoard_woocommerce_after_single_product() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_after_single_product');
    }

    function onvoard_woocommerce_product_meta_start() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_product_meta_start');
    }

    function onvoard_woocommerce_after_shop_loop_item_title() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_after_shop_loop_item_title');
    }

    function onvoard_woocommerce_after_shop_loop_item() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_after_shop_loop_item');
    }

    function onvoard_woocommerce_after_shop_loop() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_after_shop_loop');
    }

    function onvoard_woocommerce_before_cart() {
    	if (!$this->install_scripts()) { return; }
       $this->render_app_scripts_for_hook('woocommerce_before_cart');
    }

    function onvoard_woocommerce_after_cart() {
    	if (!$this->install_scripts()) { return; }
        $this->render_app_scripts_for_hook('woocommerce_after_cart');
    }

    function onvoard_woocommerce_thankyou($order_id) {
    	if (!$this->install_scripts()) { return; }
        $output = '
            <script>
              window.OnVoardData["order"] = ' . $this->get_order_json($order_id) . ';
            </script>
        ';

        $this->render_app_scripts_for_hook('woocommerce_thankyou');
    }
}
