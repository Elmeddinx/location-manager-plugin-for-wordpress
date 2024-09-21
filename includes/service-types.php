<?php
// Service slug'ları al ve localize_script ile JS'e aktar
function lm_get_dynamic_service_slugs() {
    // Fetch all service types from the custom post type 'lm_service_type'
    $services = get_posts(array(
        'post_type' => 'lm_service_type',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));

    $slugs = array();

    if ($services) {
        foreach ($services as $service) {
            $slugs[] = $service->post_name; // Get the post slug (post_name)
        }
    }

    return $slugs; // Return an array of service slugs
}


function lm_localize_service_slugs_script() {
    // Enqueue your JS file
    //wp_enqueue_script('lm-ajax-script-frontend', plugin_dir_url(__FILE__) . 'assets/js/ajax-script.js', array('jquery'), '1.80', true);

    // Fetch dynamic service slugs
    $service_slugs = lm_get_dynamic_service_slugs();

    // Localize slugs for JavaScript
    wp_localize_script('lm-ajax-script-frontend', 'lm_service_slugs', array(
        'slugs' => $service_slugs, // JS will have access to lm_service_slugs.slugs
    ));
}
add_action('wp_enqueue_scripts', 'lm_localize_service_slugs_script');


?>
