<?php
// AJAX isteğini işleme al
function lm_get_cities_by_state() {
    if (isset($_POST['state']) && !empty($_POST['state'])) {
        $state = sanitize_text_field($_POST['state']);
        error_log('State received: ' . $state);  // Gelen state'i logla

        // Kayıtlı lokasyonlardan şehirleri getir
        $locations = lm_get_locations(); // Mevcut fonksiyondan lokasyonları al

        if (isset($locations[$state])) {
            $cities = explode(',', $locations[$state]); // Şehirleri diziye çevir
            foreach ($cities as $city) {
                echo '<option value="' . esc_attr(trim($city)) . '">' . ucfirst(trim($city)) . '</option>';
            }
        } else {
            echo '<option value="">No cities available</option>';
        }
    } else {
        echo '<option value="">No state selected</option>';
    }

    wp_die(); // AJAX isteğini sonlandır
}




// AJAX handler'ı ekle
add_action('wp_ajax_get_cities', 'lm_get_cities_by_state'); // Giriş yapmış kullanıcılar için
add_action('wp_ajax_nopriv_get_cities', 'lm_get_cities_by_state'); // Giriş yapmamış kullanıcılar için
?>
