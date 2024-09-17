<?php
function lm_redirect_to_service_page() {
    if (isset($_POST['lm_state_dropdown']) && isset($_POST['lm_city_dropdown'])) {
        $state = sanitize_text_field($_POST['lm_state_dropdown']);
        $city = sanitize_text_field($_POST['lm_city_dropdown']);
        $service_slug = 'blinds'; // Örnek ürün/hizmet slug

        // Dinamik URL'ye yönlendirme
        wp_redirect(home_url("/$service_slug/" . strtolower($city) . '-' . strtolower($state)));
        exit;
    }
}
add_action('template_redirect', 'lm_redirect_to_service_page');
?>
