<?php
/**
  Plugin Name: OnVoard
  Plugin URI: https://onvoard.com
  Description: This plugin connects your WooCommerce store to OnVoard. OnVoard is all-in-one ecommerce marketing platform for modern merchants.

  Version: 1.0.0
  WC requires at least: 4.5
  WC tested up to: 5.7.1

  Author: OnVoard
  Author URI: https://www.onvoard.com
  License: GPLv2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('ONVOARD_CONSOLE_URL')) {
    define("ONVOARD_CONSOLE_URL", "https://console.onvoard.com/");
}

define('ONVOARD_PLUGIN_URL', plugin_dir_url( __FILE__));

include_once 'admin/OnVoardAdmin.php';
include_once 'includes/OnVoardCustomRoutes.php';
include_once 'includes/OnVoardHooks.php';
include_once 'includes/OnVoardRender.php';
include_once 'includes/OnVoardShortcodes.php';

global $onvoard_admin;
global $onvoard_custom_routes;
global $onvoard_hooks;

$onvoard_admin = new OnVoardAdmin();
$onvoard_custom_routes = new OnVoardCustomRoutes();
$onvoard_hooks = new OnVoardHooks();
$onvoard_shortcodes = new OnVoardShortcodes();


// Required for Dev
// If this is not used, we get "A valid URL was not provided" error when callback_url is on same host/server as wordpress
// Note: 10, 2 priority is required to get this working.
add_filter('http_request_args', function($args, $url) {
    if (strpos($url, ONVOARD_CONSOLE_URL) !== false) {
        $args['reject_unsafe_urls'] = false;
    }

    return $args;
}, 10, 2);


register_uninstall_hook(__FILE__, 'onvoard_uninstall');
function onvoard_uninstall() {
    global $wpdb;

    $account_id = get_option('onvoard_account_id', '') . '';
    $key_id = get_option('onvoard_woocommerce_key_id', '') . '';
    $consumer_key = get_option('onvoard_woocommerce_consumer_key', '') . '';
    $consumer_secret = get_option('onvoard_woocommerce_consumer_secret', '') . '';
    $callback_body = array(
        'account_id' => $account_id,
        'key_id' => $key_id,
        'consumer_key' => $consumer_key,
    );

    // delete OnVoard api keys
    if (!empty($consumer_key)) {
        $table = "{$wpdb->prefix}woocommerce_api_keys";
        $sql = $wpdb->prepare("DELETE FROM $table WHERE consumer_key = %s", $consumer_key);
        $wpdb->query($sql);
    }

    $table = "{$wpdb->prefix}woocommerce_api_keys";
    $sql = $wpdb->prepare("DELETE FROM $table WHERE description LIKE 'OnVoard - API%'");
    $wpdb->query($sql);

    // delete OnVoard webhooks
    $table = "{$wpdb->prefix}wc_webhooks";
    $sql = $wpdb->prepare("DELETE FROM $table WHERE delivery_url LIKE '%onvoard.com/sources%'");
    $wpdb->query($sql);

    //remove options
    $plugin_options = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'onvoard_%'");
    foreach ($plugin_options as $option) {
        delete_option($option->option_name);
    }

    // uninstall callback
    $uninstall_callback_url = ONVOARD_CONSOLE_URL . "sources/woocommerce/marketingplatform/callback/uninstall";
    wp_remote_post($uninstall_callback_url, array(
        'method'        => 'POST',
        'timeout'       => 5,
        'redirection'   => 5,
        'httpversion' => '1.0',
        'blocking' => false,
        'headers' => array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode($callback_body),
        'cookies' => array()
    ));
}
