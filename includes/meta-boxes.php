<?php
// Add meta boxes to posts/pages for state and city input
function lm_add_meta_boxes() {
    add_meta_box(
        'location_info',           // Unique ID for meta box
        'Location Info',           // Meta box title
        'lm_display_location_meta_box', // Callback function to display content
        'page',                    // Post type (page, post, etc.)
        'side',                    // Context (side, normal, advanced)
        'high'                     // Priority (high, low)
    );
}
add_action('add_meta_boxes', 'lm_add_meta_boxes');

// Display the meta box fields
function lm_display_location_meta_box($post) {
    // Get saved values from post meta
    $saved_state = get_post_meta($post->ID, '_lm_state', true);
    $saved_city = get_post_meta($post->ID, '_lm_city', true);
    $locations = lm_get_locations(); // Get saved locations (states and cities)

    // Dropdown for State selection
    echo '<label for="lm_state">State:</label>';
    echo '<select id="lm_state" name="lm_state" onchange="loadCities(this.value)" style="width: 100%;">';
    echo '<option value="">Select State</option>';

    // Loop through the saved locations to populate the state dropdown
    foreach ($locations as $state => $cities) {
        $selected = ($saved_state == $state) ? 'selected' : '';
        echo '<option value="' . esc_attr($state) . '" ' . $selected . '>' . ucfirst($state) . '</option>';
    }
    echo '</select>';

    // Dropdown for City selection
    echo '<br/><br/><label for="lm_city">City:</label>';
    echo '<select id="lm_city" name="lm_city" style="width: 100%;">';
    echo '<option value="">Select City</option>';

    // Pre-load saved cities if a state is already saved
    if ($saved_state && isset($locations[$saved_state])) {
        $saved_cities = explode(',', $locations[$saved_state]);
        foreach ($saved_cities as $city) {
            $selected_city = ($saved_city == $city) ? 'selected' : '';
            echo '<option value="' . esc_attr(trim($city)) . '" ' . $selected_city . '>' . ucfirst(trim($city)) . '</option>';
        }
    }
    echo '</select>';
    ?>

    <!-- Inline JavaScript to handle dynamic city loading based on selected state -->
    <script type="text/javascript">
        function loadCities(state) {
            var cityDropdown = document.getElementById('lm_city');
            cityDropdown.innerHTML = '<option value="">Loading...</option>';
            
            var data = {
                'action': 'get_cities',
                'state': state
            };

            jQuery.post(ajaxurl, data, function(response) {
                cityDropdown.innerHTML = response;
            });
        }
    </script>

    <?php
}

// Save the meta box data (State and City)
function lm_save_location_meta($post_id) {
    // Save the state value if it's set
    if (isset($_POST['lm_state'])) {
        update_post_meta($post_id, '_lm_state', sanitize_text_field($_POST['lm_state']));
    }

    // Save the city value if it's set
    if (isset($_POST['lm_city'])) {
        update_post_meta($post_id, '_lm_city', sanitize_text_field($_POST['lm_city']));
    }
}
add_action('save_post', 'lm_save_location_meta');

// Retrieve saved locations (states and their cities) from the database
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
?>
