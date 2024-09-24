<?php
/*
Plugin Name: Location Manager
Description: A plugin that allows managing states and cities and dynamic URL redirection based on location.
Version: 1.5.1
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
include_once plugin_dir_path(__FILE__) . '/includes/admin-services.php';
include_once plugin_dir_path(__FILE__) . '/includes/dynamic-menu.php';
include_once plugin_dir_path(__FILE__) . '/includes/service-types.php';

// Enqueue JavaScript for the plugin (for both admin panel and frontend usage)
// JavaScript dosyasını bir kez çağıracak şekilde düzenleyelim
function lm_enqueue_custom_js() {
    // Önceki script'in zaten yüklü olup olmadığını kontrol edelim
    if (!wp_script_is('lm-ajax-script-frontend', 'enqueued')) {
        wp_enqueue_script('lm-ajax-script-frontend', plugin_dir_url(__FILE__) . 'assets/js/ajax-script.js', array('jquery'), '1.90', true);
        
        // Service slugs'ları dinamik olarak alıp JS'e aktaralım
        $service_slugs = lm_get_dynamic_service_slugs();
        wp_localize_script('lm-ajax-script-frontend', 'lm_service_slugs', array(
            'slugs' => $service_slugs,
        ));
    }
}
add_action('wp_enqueue_scripts', 'lm_enqueue_custom_js');



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
