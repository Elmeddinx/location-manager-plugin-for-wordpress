<?php
function lm_redirect_to_office_page() {
    if (isset($_POST['lm_state_dropdown']) && isset($_POST['lm_city_dropdown'])) {
        $state = sanitize_text_field($_POST['lm_state_dropdown']);
        $city = sanitize_text_field($_POST['lm_city_dropdown']);

        // Boşlukları "-" ile değiştirme
        $state_slug = str_replace(' ', '-', strtolower($state));
        $city_slug = str_replace(' ', '-', strtolower($city));

        // Dinamik URL'ye yönlendirme (her zaman office sayfasına)
        wp_redirect(home_url("/office/" . $city_slug . '-' . $state_slug));
        exit;
    }
}
add_action('template_redirect', 'lm_redirect_to_office_page');
?>
