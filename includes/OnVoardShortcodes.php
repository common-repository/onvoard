<?php
if (!defined('ABSPATH')) {
    exit;
}

class OnVoardShortcodes {
    function __construct() {
        $this->init_shortcodes();
    }

    function init_shortcodes() {
        add_shortcode('onvoard_aov_progress_bar', array($this, 'onvoard_aov_progress_bar'));
        add_shortcode('onvoard_back_in_stock_inline_button', array($this, 'onvoard_back_in_stock_inline_button'));
        add_shortcode('onvoard_back_in_stock_inline_form', array($this, 'onvoard_back_in_stock_inline_form'));
        add_shortcode('onvoard_back_in_stock_inline_text', array($this, 'onvoard_back_in_stock_inline_text'));

		add_shortcode('onvoard_reviews_widget', array($this, 'onvoard_reviews_widget'));
        add_shortcode('onvoard_star_rating', array($this, 'onvoard_star_rating'));
        add_shortcode('onvoard_recommender', array($this, 'onvoard_recommender'));
    }

    public function onvoard_aov_progress_bar($args) {
        ob_start();
        OnVoardRender::onvoard_aov_progress_bar($args);
        $output = ob_get_clean();
        return $output;
    }

    public function onvoard_back_in_stock_inline_button($args) {
        ob_start();
        OnVoardRender::onvoard_back_in_stock_inline_button($args);
        $output = ob_get_clean();
        return $output;
    }

    public function onvoard_back_in_stock_inline_form($args) {
        ob_start();
        OnVoardRender::onvoard_back_in_stock_inline_form($args);
        $output = ob_get_clean();
        return $output;
    }

    public function onvoard_back_in_stock_inline_text($args) {
        ob_start();
        OnVoardRender::onvoard_back_in_stock_inline_text($args);
        $output = ob_get_clean();
        return $output;
    }

    public function onvoard_reviews_widget($args) {
        ob_start();
        OnVoardRender::onvoard_reviews_widget($args);
        $output = ob_get_clean();
        return $output;
    }

    public function onvoard_star_rating($args) {
        ob_start();
        OnVoardRender::onvoard_star_rating($args);
        $output = ob_get_clean();
        return $output;
    }

    public function onvoard_recommender($args) {
        ob_start();
        OnVoardRender::onvoard_recommender($args);
        $output = ob_get_clean();
        return $output;
    }
}


