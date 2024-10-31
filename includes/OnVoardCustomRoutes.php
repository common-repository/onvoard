<?php
if (!defined('ABSPATH')) {
    exit;
}


class OnVoardCustomRoutes {

    function __construct() {
        add_action('rest_api_init', function () {
            $this->load_cart_functions();
        });

        add_action('rest_api_init', function () {
            register_rest_route('onvoard', 'add-cart', array(
                'methods' => 'POST',
                'callback' => array($this,'add_cart'),
                'permission_callback' => '__return_true',
            ));
        });

        add_action('rest_api_init', function () {
            register_rest_route('onvoard', 'get-cart', array(
                'methods' => 'GET',
                'callback' => array($this,'get_cart'),
                'permission_callback' => '__return_true',
            ));
        });

        add_action('rest_api_init', function () {
            register_rest_route('onvoard', 'get-option', array(
                'methods' => 'GET',
                'callback' => array($this,'get_option'),
                'permission_callback' => '__return_true',
            ));
        });

        add_action('rest_api_init', function () {
            register_rest_route('onvoard', 'update-option', array(
                'methods' => 'POST',
                'callback' => array($this,'post_update_option'),
                'permission_callback' => '__return_true',
            ));
        });

        add_action('rest_api_init', function () {
            register_rest_route('onvoard', 'update-options', array(
                'methods' => 'POST',
                'callback' => array($this,'post_update_options'),
                'permission_callback' => '__return_true',
            ));
        });
    }

    function validate_request($request) {
        $authorization = $request->get_header('Authorization');
        $authorization = str_replace("Basic ", "", $authorization);

        if (empty($authorization)) {
            throw new Exception( "Missing Authorization header");
        }

        list($consumer_key, $consumer_secret) = explode(":", base64_decode($authorization));

        if (empty($consumer_key) || empty($consumer_secret)) {
            throw new Exception( "Missing consumer_key or consumer_secret");
        }

        global $wpdb;
        $key = hash_hmac('sha256', $consumer_key, 'wc-api');
        $user = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT consumer_key, consumer_secret
                FROM {$wpdb->prefix}woocommerce_api_keys
                WHERE consumer_key = %s
                ", $key
            )
        );

        if ($user->consumer_secret == $consumer_secret) {
            return array(
                'success' => true,
            );
        }

        throw new Exception("Invalid consumer credentials");
    }

    function load_cart_functions() {
        // WooCommerce must be at least v4.5 for this to work
        if (is_null(WC()->cart) && function_exists('wc_load_cart')) {
            WC()->frontend_includes();
            wc_load_cart();
        }
    }

    function add_cart($request) {
    	// this is required in order for add cart to work. For some reasons, if this is omitted,
    	// we can't add simple product to cart.
        $cart = WC()->cart;
        $cart->get_cart();

        $payload = $request->get_json_params();
        $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($payload['id']));
        $product = wc_get_product($product_id);
        $variation_id = $payload['variation_id'];
        $variation_attributes = array();
        $quantity = absint($payload['quantity']);

        if ($variation_id) {
        	$variation_id = absint($variation_id);
        } else {
        	$variation_id = 0;
        }

        $variation = wc_get_product($variation_id);
        if (!empty($variation)) {;
            $variation_attributes = $variation->get_variation_attributes();
        }

        $item_key = $cart->add_to_cart($product_id, $quantity, $variation_id, $variation_attributes);
        if ($item_key !== false) {
            do_action( 'woocommerce_ajax_added_to_cart', $product_id);
        }

        return $this->get_cart($request);
    }

    function get_cart($request) {
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
            $item_data['permalink'] = $product->get_permalink();
            $item_data['image_url'] = wp_get_attachment_image_url($product->get_image_id());

            if ($product->get_type() === "variable" && $variation_id != 0) {
                $variation = wc_get_product($variation_id);
                if (!empty($variation)) {;
                    $item_data['variation_id'] = $variation_id;
                    $item_data['variation_name'] = implode(" / ", $variation->get_variation_attributes());
                    $item_data['sku'] = $variation->get_sku();
                    $item_data['price'] = $variation->get_price();
                    $item_data['regular_price'] = $variation->get_regular_price();
                    $item_data['sale_price'] = $variation->get_sale_price();
                    $item_data['on_sale'] = $variation->is_on_sale();
                    $item_data['permalink'] = $variation->get_permalink();
                    $item_data['image_url'] = wp_get_attachment_image_url($variation->get_image_id());
                }
            }

            array_push($items_data, $item_data);
        }

        $cart_data = array(
            'hash' => $cart->get_cart_hash(),
            'total' => $cart->get_cart_contents_total(),
            'subtotal' => $cart->get_subtotal(),
            'discount_total' => $cart->get_discount_total(),
            'shipping_total' => $cart->get_shipping_total(),
            'items' => $items_data,
        );

        return $cart_data;
    }

    function get_option($request) {
        $this->validate_request($request);

        $option_key = $request->get_param('key');
        $option_value = get_option($option_key, '');
        return $option_value;
    }

    function post_update_option($request) {
        $this->validate_request($request);

        $payload = $request->get_json_params();
        update_option($payload['key'], $payload['value']);
    }

    function post_update_options($request) {
        $this->validate_request($request);

        $payload = $request->get_json_params();
        $options = $payload['options'];

        foreach ($options as $option) {
            update_option($option['key'], $option['value']);
        }
    }
}
