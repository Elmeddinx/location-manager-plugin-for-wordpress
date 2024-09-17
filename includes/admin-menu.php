<?php
// Add a custom admin menu for locations
function lm_add_admin_menu() {
    add_menu_page(
        'Locations',
        'Locations',
        'manage_options',
        'lm_location_menu',
        'lm_display_locations',
        'dashicons-location-alt',
        6
    );
}
add_action('admin_menu', 'lm_add_admin_menu');

// Display the form to add, edit and manage states and cities
function lm_display_locations() {
    ?>
    <div class="wrap">
        <h1>Location Management</h1>

        <!-- Add New Location Form -->
        <form method="post" action="">
            <h2>Add New Location</h2>
            <label for="state_field">State:</label>
            <input type="text" id="state_field" name="state_field" value="" required />
            <br/><br/>
            <label for="city_field">Cities (separate by commas):</label>
            <input type="text" id="city_field" name="city_field" value="" required />
            <br/><br/>
            <input type="submit" name="lm_save_location" value="Save Location" class="button button-primary" />
        </form>

        <?php
        if (isset($_POST['lm_save_location'])) {
            $state = sanitize_text_field($_POST['state_field']);
            $cities = sanitize_text_field($_POST['city_field']);
            update_option('lm_state_' . strtolower($state), $cities); // Save or update the state and cities
            echo '<div class="updated"><p>Location saved!</p></div>';
        }

        if (isset($_POST['lm_update_location'])) {
            $state = sanitize_text_field($_POST['edit_state_field']);
            $cities = sanitize_text_field($_POST['edit_city_field']);
            update_option('lm_state_' . strtolower($state), $cities); // Update the state and cities
            echo '<div class="updated"><p>Location updated!</p></div>';
        }

        if (isset($_POST['lm_delete_location'])) {
            $state_to_delete = sanitize_text_field($_POST['delete_state']);
            delete_option('lm_state_' . strtolower($state_to_delete)); // Delete the selected state and its cities
            echo '<div class="updated"><p>Location deleted!</p></div>';
        }

        // Display Saved Locations
        $locations = lm_get_locations();
        if (!empty($locations)) {
            echo '<h2>Saved Locations</h2>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>State</th><th>Cities</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            foreach ($locations as $state => $cities) {
                echo '<tr>';
                echo '<td>' . ucfirst($state) . '</td>';
                echo '<td>' . esc_html($cities) . '</td>';
                echo '<td>';
                echo '<form method="post" style="display:inline;">';
                echo '<input type="hidden" name="edit_state" value="' . esc_attr($state) . '" />';
                echo '<input type="submit" name="lm_edit_location" value="Edit" class="button button-secondary" />';
                echo '</form> ';
                echo '<form method="post" style="display:inline;">';
                echo '<input type="hidden" name="delete_state" value="' . esc_attr($state) . '" />';
                echo '<input type="submit" name="lm_delete_location" value="Delete" class="button button-danger" onclick="return confirm(\'Are you sure you want to delete this location?\');" />';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }

        // Handle Edit Form Display
        if (isset($_POST['lm_edit_location'])) {
            $state_to_edit = sanitize_text_field($_POST['edit_state']);
            $cities_to_edit = get_option('lm_state_' . strtolower($state_to_edit));
            ?>
            <h2>Edit Location</h2>
            <form method="post" action="">
                <label for="edit_state_field">State:</label>
                <input type="text" id="edit_state_field" name="edit_state_field" value="<?php echo esc_attr($state_to_edit); ?>" readonly />
                <br/><br/>
                <label for="edit_city_field">Cities (separate by commas):</label>
                <input type="text" id="edit_city_field" name="edit_city_field" value="<?php echo esc_attr($cities_to_edit); ?>" required />
                <br/><br/>
                <input type="submit" name="lm_update_location" value="Update Location" class="button button-primary" />
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}

// Retrieve the saved locations
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
?>
