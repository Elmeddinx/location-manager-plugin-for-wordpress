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
    add_meta_box(
        'service_type_info',
        'Service Type',
        'lm_display_service_meta_box',
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
    echo '<select id="lm_state" name="lm_state" style="width: 100%;">';
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
            $city = trim($city);
            $selected_city = ($saved_city == $city) ? 'selected' : '';
            echo '<option value="' . esc_attr($city) . '" ' . $selected_city . '>' . ucfirst($city) . '</option>';
        }
    }
    echo '</select>';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            function loadCities(state) {
                var cityDropdown = $('#lm_city');
                cityDropdown.html('<option value="">Loading...</option>');

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'get_cities',
                        state: state
                    },
                    success: function (response) {
                        cityDropdown.html(response);
                        var savedCity = '<?php echo esc_js($saved_city); ?>';
                        if (savedCity) {
                            cityDropdown.val(savedCity);
                        }
                    }
                });
            }

            $('#lm_state').on('change', function () {
                var state = $(this).val();
                if (state) {
                    loadCities(state);
                } else {
                    $('#lm_city').html('<option value="">Select City</option>');
                }
            });

            var initialState = $('#lm_state').val();
            if (initialState) {
                loadCities(initialState);
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

function lm_display_service_meta_box($post) {
    // Nonce alanı ekleyelim
    wp_nonce_field('lm_save_meta_box_data', 'lm_meta_box_nonce');

    $current_service = get_post_meta($post->ID, '_lm_service_type', true);

    $services = get_posts(array(
        'post_type' => 'lm_service_type',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    ?>
    <label for="lm_service_type">Select Service Type:</label>
    <select name="lm_service_type" id="lm_service_type" style="width: 100%;">
        <option value="">--Select Service--</option>
        <?php if ($services) : ?>
            <?php foreach ($services as $service) : ?>
                <option value="<?php echo esc_attr($service->post_name); ?>" <?php selected($current_service, $service->post_name); ?>>
                    <?php echo esc_html($service->post_title); ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <?php
}
function lm_save_meta_box_data($post_id) {
    // Nonce kontrolü
    if (!isset($_POST['lm_meta_box_nonce']) || !wp_verify_nonce($_POST['lm_meta_box_nonce'], 'lm_save_meta_box_data')) {
        return;
    }

    // Otosave kontrolü
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Kullanıcı yetkisi kontrolü
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // State kaydetme
    if (isset($_POST['lm_state'])) {
        $state = sanitize_text_field($_POST['lm_state']);
        update_post_meta($post_id, '_lm_state', $state);
    }

    // City kaydetme
    if (isset($_POST['lm_city'])) {
        $city = sanitize_text_field($_POST['lm_city']);
        update_post_meta($post_id, '_lm_city', $city);
    }

    // Service Type kaydetme
    if (isset($_POST['lm_service_type'])) {
        $service_type = sanitize_text_field($_POST['lm_service_type']);
        update_post_meta($post_id, '_lm_service_type', $service_type);
    }
}
add_action('save_post', 'lm_save_meta_box_data');
add_action('save_post', 'lm_save_location_info');
?>
