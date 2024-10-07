<?php
function lm_get_cities_by_state() {
    if (isset($_POST['state']) && !empty($_POST['state'])) {
        $state = sanitize_text_field($_POST['state']);

        $locations = lm_get_locations(); 
        echo '<option value="">Select City</option>';
        if (isset($locations[$state])) {
            $cities = explode(',', $locations[$state]);
            foreach ($cities as $city) {
                echo '<option value="' . esc_attr(trim($city)) . '">' . ucfirst(trim($city)) . '</option>';
            }
        } else {
            echo '<option value="">No cities available</option>';
        }
    } else {
        echo '<option value="">No state selected</option>';
    }

    wp_die();
}

add_action('wp_ajax_get_cities', 'lm_get_cities_by_state');
add_action('wp_ajax_nopriv_get_cities', 'lm_get_cities_by_state');

?>
