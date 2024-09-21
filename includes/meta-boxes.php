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

            jQuery.post(ajaxurl, data, function (response) {
                cityDropdown.innerHTML = response;

                // Kaydedilen şehir verisini kullanarak, dropdown'da doğru seçimi yap
                var savedCity = '<?php echo esc_js($saved_city); ?>';
                if (savedCity) {
                    console.log("Saved city: " + savedCity); // Konsolda kaydedilen şehir bilgisi
                    // Select'i güncelle ve seçilen şehri ayarla
                    jQuery(cityDropdown).val(savedCity);
                }
            });
        }

        // Sayfa yüklendiğinde şehir dropdown'ı otomatik olarak doldur
        jQuery(document).ready(function () {
            var state = jQuery('#lm_state').val();
            if (state) {
                loadCities(state); // Eğer kaydedilmiş bir state varsa, şehirleri yükle
            }
        });
    </script>

    <?php
}

// Function to save the location meta box data
function lm_save_location_info($post_id) {
    // Check if it's an autosave or the user doesn't have permission to save the data
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verify the nonce (if you added a nonce for security, add the nonce check here)
    error_log("save_post triggered for post ID: " . $post_id);
    // Save the state information
    if (isset($_POST['lm_state'])) {
        $state = sanitize_text_field($_POST['lm_state']);
        error_log("State being saved: " . $state);
        update_post_meta($post_id, '_lm_state', $state);
    } else {
        error_log("State not found in POST data.");
    }

    // Save the city information
    if (isset($_POST['lm_city'])) {
        $city = sanitize_text_field($_POST['lm_city']);
        error_log("City being saved: " . $city);
        update_post_meta($post_id, '_lm_city', $city);
    } else {
        error_log("City not found in POST data.");
    }
}
add_action('save_post', 'lm_save_location_info');


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

// Add service type meta box to pages
function lm_add_service_meta_box() {
    add_meta_box(
        'lm_service_type_meta', 
        'Service Type', 
        'lm_display_service_meta_box', // This function is in admin-services.php
        'page', // Adding to pages
        'side', 
        'high'
    );
}
add_action('add_meta_boxes', 'lm_add_service_meta_box');

// Save the selected service type when the page is saved
function lm_save_service_meta_box_data($post_id) {
    if (array_key_exists('lm_service_type', $_POST)) {
        update_post_meta(
            $post_id,
            '_lm_service_type',
            sanitize_text_field($_POST['lm_service_type'])
        );
    }
}
add_action('save_post', 'lm_save_service_meta_box_data');

?>
