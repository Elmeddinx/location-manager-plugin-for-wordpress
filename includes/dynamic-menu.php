<?php
function lm_get_service_for_page($post_id) {
    $service_type = get_post_meta($post_id, '_lm_service_type', true);
    return $service_type ? $service_type : null;
}

function lm_update_menu_links($items, $args) {
    if (!is_admin()) {
        $user_city = isset($_COOKIE['user_city']) ? sanitize_text_field($_COOKIE['user_city']) : null;
        $user_state = isset($_COOKIE['user_state']) ? sanitize_text_field($_COOKIE['user_state']) : null;

        if ($user_city && $user_state) {
            $city_slug = str_replace(' ', '-', strtolower($user_city));
            $state_slug = str_replace(' ', '-', strtolower($user_state));

            foreach ($items as &$item) {
                $service_slug = lm_get_service_for_page($item->object_id);

                if ($service_slug) {
                    $item->url = "/{$service_slug}/{$city_slug}-{$state_slug}/";
                }
            }
        }
    }
    return $items;
}

add_filter('wp_nav_menu_objects', 'lm_update_menu_links', 10, 2);

?>