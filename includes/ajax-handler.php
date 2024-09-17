<?php
// Handle AJAX request to get cities based on the selected state
function lm_get_cities_by_state() {
    if (isset($_POST['state']) && !empty($_POST['state'])) {
        $state = sanitize_text_field($_POST['state']);
        
        // Get cities for the selected state from the saved locations
        $locations = lm_get_locations(); // Assuming lm_get_locations() returns states and cities
        
        if (isset($locations[$state])) {
            $cities = explode(',', $locations[$state]); // Convert city list to array
            
            // Output each city as an option in the dropdown
            foreach ($cities as $city) {
                echo '<option value="' . esc_attr(trim($city)) . '">' . ucfirst(trim($city)) . '</option>';
            }
        } else {
            echo '<option value="">No cities available</option>';
        }
    } else {
        echo '<option value="">No state selected</option>';
    }

    wp_die(); // Properly end AJAX requests in WordPress
}

// Prevent redeclaring the function
if (!function_exists('lm_get_locations')) {
    function lm_get_locations() {
        global $wpdb;
        $options = $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->prefix}options WHERE option_name LIKE 'lm_state_%'");
        $locations = [];
        foreach ($options as $option) {
            $state = str_replace('lm_state_', '', $option->option_name);
            $locations[$state] = $option->option_value;
        }
        return $locations;
    }
}

// Hook the AJAX handler for both logged-in and logged-out users
add_action('wp_ajax_get_cities', 'lm_get_cities_by_state'); // For logged-in users
add_action('wp_ajax_nopriv_get_cities', 'lm_get_cities_by_state'); // For logged-out users
?>
