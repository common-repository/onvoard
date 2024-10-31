<?php
if (!defined('ABSPATH')) {
    exit;
}


class OnVoardAdmin {
    function __construct() {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'onvoard_woocommerce_menu'));
            add_action('admin_enqueue_scripts', array($this, 'onvoard_admin_scripts'));
        }
    }

    function onvoard_woocommerce_menu() {
        $page_title = 'OnVoard';
        $menu_title = 'OnVoard';
        $capability = 'manage_options';
        $menu_slug = 'onvoard';
        $function = array($this, 'settings');
        $icon_url = ONVOARD_PLUGIN_URL . "/assets/img/icon.svg";

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url);
    }

    function onvoard_head_html() {
        ?>
            <div class="onvoard-head">
               <div class="onvoard-logo">
                <a href="<?php echo esc_url(ONVOARD_CONSOLE_URL); ?>" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" id="onvoard-logo" viewBox="0 0 476 125"><path d="M46.5,241.9m0.7,17.5,0,17.5M88,295.7" transform="translate(-16.5 -183.2)"/><path d="M191.2,296.4V260.9q0-7.6-2.2-11t-7.4-3.4a11.7,11.7,0,0,0-7.8,2.7,14.9,14.9,0,0,0-4.6,6.7v40.5H152.9V233.9h12.9l1.9,8.3h0.5a26.9,26.9,0,0,1,7.6-7q4.7-3,12.3-3a25.3,25.3,0,0,1,8.3,1.3,14.1,14.1,0,0,1,6.1,4.1,18.8,18.8,0,0,1,3.8,7.8,50.6,50.6,0,0,1,1.3,12.2v38.9H191.2Z" transform="translate(-16.5 -183.2)"/><path d="M245.8,260.3l2.3,14.9h0.6l2.5-15,16.7-51.2h18L253.4,297H241.2l-32.7-88.1h19.9Z" transform="translate(-16.5 -183.2)"/><path d="M275.3,265.2q0-15.9,7.8-24.4t21.8-8.6a32.6,32.6,0,0,1,13,2.4,24.3,24.3,0,0,1,9.1,6.7,28.6,28.6,0,0,1,5.4,10.4,47.5,47.5,0,0,1,1.8,13.5q0,15.9-7.7,24.4t-21.7,8.6a32.6,32.6,0,0,1-13-2.4,24.8,24.8,0,0,1-9.2-6.7,28.1,28.1,0,0,1-5.5-10.4A47.4,47.4,0,0,1,275.3,265.2Zm16.8,0a36.4,36.4,0,0,0,.8,7.6,21.2,21.2,0,0,0,2.3,6.1A10.8,10.8,0,0,0,299,283a11.2,11.2,0,0,0,5.8,1.4q6.4,0,9.5-4.6t3.1-14.6q0-8.6-2.9-13.9t-9.7-5.3a11.1,11.1,0,0,0-9.4,4.5Q292,254.9,292,265.2Z" transform="translate(-16.5 -183.2)"/><path d="M340.7,237.5a53.4,53.4,0,0,1,11.9-3.6,76.5,76.5,0,0,1,14.4-1.3,32.7,32.7,0,0,1,10.9,1.6,15.7,15.7,0,0,1,10.6,11.3,35.2,35.2,0,0,1,1.1,9q0,5.5-.4,11.1t-0.4,10.9q-0.1,5.4.3,10.4a39.3,39.3,0,0,0,1.9,9.6H377.5l-2.6-8.6h-0.6a22.3,22.3,0,0,1-6.9,6.7q-4.4,2.8-11.4,2.8a22.3,22.3,0,0,1-7.9-1.3,16.8,16.8,0,0,1-9.9-9.5,19.2,19.2,0,0,1-1.4-7.4,17.4,17.4,0,0,1,2.6-9.7,18.5,18.5,0,0,1,7.4-6.4,36.6,36.6,0,0,1,11.5-3.4,81.5,81.5,0,0,1,14.9-.6q0.9-7-1-10.1t-8.4-3.1a57.2,57.2,0,0,0-10.3,1,37.8,37.8,0,0,0-8.9,2.6ZM361.3,284a12.5,12.5,0,0,0,7.8-2.2,15.3,15.3,0,0,0,4.3-4.7V269a44.8,44.8,0,0,0-7.4-.1,27.3,27.3,0,0,0-6.3,1.1,10.6,10.6,0,0,0-4.4,2.5,5.6,5.6,0,0,0-1.6,4.1,7.1,7.1,0,0,0,2.1,5.4A8,8,0,0,0,361.3,284Z" transform="translate(-16.5 -183.2)"/><path d="M433.2,249.2a21.2,21.2,0,0,0-7-1.4,11.5,11.5,0,0,0-7.3,2.3,11.1,11.1,0,0,0-3.9,5.9v40.4H398.7V233.9h12.6l1.9,8.3h0.5a17,17,0,0,1,5.8-7.1,14.6,14.6,0,0,1,8.5-2.5,23.7,23.7,0,0,1,7.4,1.4Z" transform="translate(-16.5 -183.2)"/><path d="M491.3,274.4q0,5.3.1,10.5a88.8,88.8,0,0,0,1.1,11.6H480.9l-2.2-8.1h-0.5a18.9,18.9,0,0,1-7.2,7.1,21.9,21.9,0,0,1-11.1,2.7q-11.8,0-18.2-7.7t-6.5-24.2q0-16,7.3-24.9t21.3-8.9a38.2,38.2,0,0,1,6.4.4,25.3,25.3,0,0,1,5,1.4V208.9h16.3v65.5Zm-27.6,10a11.3,11.3,0,0,0,7.5-2.3,12.5,12.5,0,0,0,3.9-6.7V248.9a12.5,12.5,0,0,0-3.9-2.1,18.3,18.3,0,0,0-5.5-.7q-6.9,0-10.3,4.6t-3.4,15.9q0,8,2.8,12.9T463.7,284.4Z" transform="translate(-16.5 -183.2)"/><path d="M112.7,193.1L82.6,269.2h-9L54.9,222.6H66.2l12.1,32.6,25.5-66.9A62.5,62.5,0,1,0,112.7,193.1Z" transform="translate(-16.5 -183.2)"/></svg>
                </a>
               </div>
            </div>
        <?php
    }

    function onvoard_connected_settings_html() {
        $onvoard_account_id = get_option('onvoard_account_id', null);
        $guide_url = 'https://help.onvoard.com/hc/en-us/articles/4407463483161';

        ?>
            <div class="onvoard-settings">
                <div class="ov-settings-setup">
                    <h4>You are <style="font-weight: bold; color: green;">connected</style> to OnVoard account <code><?php echo esc_html($onvoard_account_id); ?></code>.</h4>
                    <p>To configure settings for OnVoard apps, go to <a href="<?php echo esc_url(ONVOARD_CONSOLE_URL); ?>" target="_blank">console</a>.</p>
                    <p>For more details, check our <a href="<?php echo esc_url($guide_url); ?>" target="_blank">setup guide</a> for WooCommerce.</p>
                </div>
            </div>
        <?php
    }


    function onvoard_unconnected_settings_html() {
        $onvoard_account_id = get_option('onvoard_account_id', null);
        $schemes = array("https://", "http://");
        $store_domain = str_replace($schemes, "", home_url());

        $external_connect_url = ONVOARD_CONSOLE_URL . 'sources/woocommerce/marketingplatform/external-connect?store_domain=' . $store_domain;
        $connection_link = ONVOARD_CONSOLE_URL . 'detect-account?next=' . urlencode($external_connect_url);

        $guide_url = 'https://help.onvoard.com/hc/en-us/articles/4407463483161';
        $register_url = ONVOARD_CONSOLE_URL . 'register/?platform=woocommerce';
        $settings_url = admin_url('admin.php?page=onvoard');

        ?>
          <div class="onvoard-page">
            <div class="onvoard-settings">
                <div class="onvoard-settings-setup">
                    <div class="onvoard-step">
                        <div class="onvoard-step-body">
                            <div class="onvoard-step-title">1) Register OnVoard account</div>
                            <div class="onvoard-step-content">Register for an OnVoard account and select <code>WooCommerce</code> for your ecommerce platform.</div>
                        </div>

                        <div class="onvoard-step-link">
                            <a href="<?php echo esc_url($register_url); ?>" target="_blank">
                            <button type="button" class="onvoard-step-button">
                                <span class="onvoard-step-button-text">Register OnVoard</span>
                            </button>
                            </a>
                        </div>
                    </div>

                    <div class="onvoard-step">
                        <div class="onvoard-step-body">
                            <div class="onvoard-step-title">2) Login to OnVoard</div>
                            <div class="onvoard-step-content">After registration, ensure that you are logged in to your OnVoard account.</div>
                        </div>

                        <div class="onvoard-step-link">
                            <a href="<?php echo esc_url(ONVOARD_CONSOLE_URL); ?>" target="_blank">
                            <button type="button" class="onvoard-step-button">
                                <span class="onvoard-step-button-text">View Console</span>
                            </button>
                            </a>
                        </div>
                    </div>

                    <div class="onvoard-step">
                        <div class="onvoard-step-body">
                            <div class="onvoard-step-title">3) Connect WooCommerce</div>
                            <div class="onvoard-step-content">Connect WooCommerce store to your OnVoard account.</div>
                        </div>

                        <div class="onvoard-step-link">
                            <a href="<?php echo esc_url($connection_link); ?>" target="_blank">
                            <button type="button" class="onvoard-step-button">
                                <span class="onvoard-step-button-text">Connect Store</span>
                            </button>
                            </a>
                        </div>
                    </div>

                    <div class="onvoard-step">
                        <div class="onvoard-step-body">
                            <div class="onvoard-step-title">4) Reload this Page</div>
                            <div class="onvoard-step-content">Once connected, reload this page to view updated settings.</div>
                        </div>

                        <div class="onvoard-step-link">
                            <a href="<?php echo esc_url($settings_url); ?>">
                            <button type="button" class="onvoard-step-button">
                                <span class="onvoard-step-button-text">Reload Page</span>
                            </button>
                            </a>
                        </div>
                    </div>

                    <p>For more details, check our <a href="<?php echo esc_url($guide_url); ?>" target="_blank">setup guide</a> for WooCommerce.</p>
                </div>
            </div>
          </div>
        <?php
    }

    function settings() {
        $onvoard_account_id = get_option('onvoard_account_id', null);

        if (empty($onvoard_account_id)) {
	        ?>
	          <div class="onvoard-page">
	            <?php echo esc_html($this->onvoard_head_html()); ?>
	            <?php echo esc_html($this->onvoard_unconnected_settings_html()); ?>
	          </div>
	        <?php
        } else {
	        ?>
	          <div class="onvoard-page">
	            <?php echo esc_html($this->onvoard_head_html()); ?>
	            <?php echo esc_html($this->onvoard_connected_settings_html()); ?>
	          </div>
	        <?php
        }
    }

    function onvoard_admin_scripts() {
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'onvoard') {
                wp_enqueue_style('onvoard-admin-style.css', ONVOARD_PLUGIN_URL . '/assets/css/onvoard-admin-style.css?' . time());
            }
        }
    }
}
