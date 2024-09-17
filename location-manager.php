<?php
/*
Plugin Name: Location Manager
Description: A plugin that allows managing states and cities and dynamic URL redirection based on location.
Version: 1.2
Author: Code Genie Studio
*/

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/admin-api-settings.php';  // Check if this file exists
include_once plugin_dir_path(__FILE__) . 'includes/frontend-display.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';  // Check if this file exists
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';  // Check if this file exists
include_once plugin_dir_path(__FILE__) . 'includes/redirect.php';  // Check if this file exists
include_once plugin_dir_path(__FILE__) . 'includes/geoip.php';  // Check if this file exists
include_once plugin_dir_path(__FILE__) . 'includes/ajax-handler.php';  // Check if this file exists


// Enqueue JavaScript for the plugin (for both admin panel and frontend usage)
function lm_enqueue_custom_js() {
    // Only load in admin panel
    if (is_admin()) {
        wp_enqueue_script('lm-ajax-script-admin', plugin_dir_url(__FILE__) . 'assets/js/ajax-script.js', array('jquery'), null, true);
        wp_localize_script('lm-ajax-script-admin', 'ajaxurl', admin_url('admin-ajax.php'));
    } else {
        // Load for frontend
        wp_enqueue_script('lm-ajax-script-frontend', plugin_dir_url(__FILE__) . 'assets/js/ajax-script.js', array('jquery'), null, true);
        wp_localize_script('lm-ajax-script-frontend', 'ajaxurl', admin_url('admin-ajax.php'));
    }
}
add_action('wp_enqueue_scripts', 'lm_enqueue_custom_js');
add_action('admin_enqueue_scripts', 'lm_enqueue_custom_js');


// Plugin activation hook
function lm_activate() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'lm_activate');

// Plugin deactivation hook
function lm_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'lm_deactivate');

?>
