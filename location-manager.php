<?php
/*
Plugin Name: Location Manager
Description: A plugin that allows managing states and cities and dynamic URL redirection based on location.
Version: 1.2.2
Author: Code Genie Studio
*/


include_once plugin_dir_path(__FILE__) . 'includes/admin-api-settings.php'; 
include_once plugin_dir_path(__FILE__) . 'includes/frontend-display.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php'; 
include_once plugin_dir_path(__FILE__) . 'includes/meta-boxes.php';
include_once plugin_dir_path(__FILE__) . 'includes/redirect.php';
include_once plugin_dir_path(__FILE__) . 'includes/geoip.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax-handler.php';
include_once plugin_dir_path(__FILE__) . '/includes/admin-services.php';
include_once plugin_dir_path(__FILE__) . '/includes/dynamic-menu.php';
include_once plugin_dir_path(__FILE__) . '/includes/service-types.php';
include_once plugin_dir_path(__FILE__) . '/includes/cache-helper.php';

function lm_enqueue_custom_js() {
    if (!wp_script_is('lm-ajax-script-frontend', 'enqueued')) {
        wp_enqueue_script('lm-ajax-script-frontend', plugin_dir_url(__FILE__) . 'assets/js/ajax-script.js', array('jquery'), '1.2.6', true);
        
        $service_slugs = lm_get_dynamic_service_slugs();
        wp_localize_script('lm-ajax-script-frontend', 'lm_service_slugs', array(
            'slugs' => $service_slugs,
        ));
    }
}
add_action('wp_enqueue_scripts', 'lm_enqueue_custom_js');

add_action('wp', 'lm_invoke_geoip');

function lm_invoke_geoip() {
    $location = get_user_location();
}


function lm_activate() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'lm_activate');

function lm_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'lm_deactivate');


?>
