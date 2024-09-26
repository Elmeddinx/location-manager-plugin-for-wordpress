<?php
function lm_redirect_to_office_page() {
    if (isset($_POST['lm_state_dropdown']) && isset($_POST['lm_city_dropdown'])) {
        $state = sanitize_text_field($_POST['lm_state_dropdown']);
        $city = sanitize_text_field($_POST['lm_city_dropdown']);

        $state_slug = str_replace(' ', '-', strtolower($state));
        $city_slug = str_replace(' ', '-', strtolower($city));

        $current_url = home_url("/office/" . $city_slug . '-' . $state_slug);

        if ($_SERVER['REQUEST_URI'] !== "/office/" . $city_slug . '-' . $state_slug) {
            wp_redirect($current_url);
            exit;
        }
    }
}
add_action('template_redirect', 'lm_redirect_to_office_page');
?>
