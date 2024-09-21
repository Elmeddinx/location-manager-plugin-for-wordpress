<?php
// Function to get dynamic service types for the current page
function lm_get_service_for_page($post_id) {
    $service_type = get_post_meta($post_id, '_lm_service_type', true);
    return $service_type ? $service_type : null;
}

// Function to update menu links dynamically
function lm_update_menu_links($items, $args) {
    if (!is_admin()) {
        // Kullanıcının city ve state bilgilerini al
        $user_city = isset($_COOKIE['user_city']) ? sanitize_text_field($_COOKIE['user_city']) : null;
        $user_state = isset($_COOKIE['user_state']) ? sanitize_text_field($_COOKIE['user_state']) : null;

        // Eğer city/state bilgileri varsa sadece linkleri görsel olarak güncelle, yönlendirme yapma
        if ($user_city && $user_state) {
            $city_slug = str_replace(' ', '-', strtolower($user_city));
            $state_slug = str_replace(' ', '-', strtolower($user_state));

            // Menü linklerini sadece görsel olarak değiştir, yönlendirme etkilenmez
            foreach ($items as &$item) {
                $service_slug = lm_get_service_for_page($item->object_id);

                // Eğer service sayfasıysa dinamik olarak city-state ekle
                if ($service_slug) {
                    // Burada home_url() yerine doğrudan statik bir yapı oluşturalım
                    $item->url = "/{$service_slug}/{$city_slug}-{$state_slug}/"; // Yönlendirme olmadan link değişikliği
                }
            }
        }
    }
    return $items;
}

add_filter('wp_nav_menu_objects', 'lm_update_menu_links', 10, 2);

?>