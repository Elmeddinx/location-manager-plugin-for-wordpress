<?php
function lm_add_meta_boxes() {
    add_meta_box(
        'location_info',           
        'Location Info',           
        'lm_display_location_meta_box', 
        'page',                    
        'side',                    
        'high'                     
    );
}
add_action('add_meta_boxes', 'lm_add_meta_boxes');

function lm_display_location_meta_box($post) {
    $saved_state = get_post_meta($post->ID, '_lm_state', true);
    $saved_city = get_post_meta($post->ID, '_lm_city', true);
    $locations = lm_get_locations(); 

    echo '<label for="lm_state">State:</label>';
    echo '<select id="lm_state" name="lm_state" onchange="loadCities(this.value)" style="width: 100%;">';
    echo '<option value="">Select State</option>';

    foreach ($locations as $state => $cities) {
        $selected = ($saved_state == $state) ? 'selected' : '';
        echo '<option value="' . esc_attr($state) . '" ' . $selected . '>' . ucfirst($state) . '</option>';
    }
    echo '</select>';

    echo '<br/><br/><label for="lm_city">City:</label>';
    echo '<select id="lm_city" name="lm_city" style="width: 100%;">';
    echo '<option value="">Select City</option>';

    if ($saved_state && isset($locations[$saved_state])) {
        $saved_cities = explode(',', $locations[$saved_state]);
        foreach ($saved_cities as $city) {
            $selected_city = ($saved_city == $city) ? 'selected' : '';
            echo '<option value="' . esc_attr(trim($city)) . '" ' . $selected_city . '>' . ucfirst(trim($city)) . '</option>';
        }
    }
    echo '</select>';
    ?>

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

                var savedCity = '<?php echo esc_js($saved_city); ?>';
                if (savedCity) {
                    console.log("Saved city: " + savedCity);
                    jQuery(cityDropdown).val(savedCity);
                }
            });
        }

        jQuery(document).ready(function () {
            var state = jQuery('#lm_state').val();
            if (state) {
                loadCities(state);
            }
        });
    </script>

    <?php
}

function lm_save_location_info($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (isset($_POST['lm_state'])) {
        $state = sanitize_text_field($_POST['lm_state']);
        update_post_meta($post_id, '_lm_state', $state);
    }

    if (isset($_POST['lm_city'])) {
        $city = sanitize_text_field($_POST['lm_city']);
        update_post_meta($post_id, '_lm_city', $city);
    }
}
add_action('save_post', 'lm_save_location_info');




function lm_add_service_meta_box() {
    add_meta_box(
        'lm_service_type_meta', 
        'Service Type', 
        'lm_display_service_meta_box',
        'page',
        'side', 
        'high'
    );
}
add_action('add_meta_boxes', 'lm_add_service_meta_box');

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
