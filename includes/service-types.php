<?php
function lm_get_dynamic_service_slugs() {
    $services = get_posts(array(
        'post_type' => 'lm_service_type',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ));

    $slugs = array();

    if ($services) {
        foreach ($services as $service) {
            $slugs[] = $service->post_name;
        }
    }

    return $slugs;
}


function lm_localize_service_slugs_script() {
    $service_slugs = lm_get_dynamic_service_slugs();

    wp_localize_script('lm-ajax-script-frontend', 'lm_service_slugs', array(
        'slugs' => $service_slugs,
    ));
}
add_action('wp_enqueue_scripts', 'lm_localize_service_slugs_script');


?>
