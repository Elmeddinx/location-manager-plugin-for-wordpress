<?php
// Display location dropdown inside a popup on the frontend with AJAX
function lm_display_location_dropdown_in_popup() {
    // Output the popup HTML structure
    ?>
    <div id="location-popup">
        <div class="popup-content">
            <h2>Select Your Location</h2>
            <form method="POST" action="">
                <!-- State Dropdown -->
                <label for="lm_state_dropdown">Select State:</label>
                <select id="lm_state_dropdown" name="lm_state_dropdown" >
                    <option value="">Select State</option>
                    <?php
                    $locations = lm_get_locations(); // Fetch all locations (states and cities)
                    foreach ($locations as $state => $cities) {
                        echo '<option value="' . esc_attr($state) . '">' . esc_html(ucfirst($state)) . '</option>';
                    }
                    ?>
                </select>
                
                <!-- City Dropdown -->
                <br/><br/>
                <label for="lm_city_dropdown">Select City:</label>
                <select id="lm_city_dropdown" name="lm_city_dropdown">
                    <option value="">Select City</option>
                </select>
                
                <!-- Submit Button -->
                <br/><br/>
                <button type="submit">Select as my location</button>
            </form>
        </div>
    </div>

    <!-- JavaScript for showing the popup and handling dynamic city loading -->
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // When state is selected, trigger AJAX to load cities
            $('#lm_state_dropdown').on('change', function() {
                var selectedState = $(this).val(); // Get selected state

                // Make AJAX call to get cities for the selected state
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    data: {
                        action: 'get_cities', // The function in AJAX handler
                        state: selectedState
                    },
                    success: function(response) {
                        // Populate city dropdown with the returned cities
                        $('#lm_city_dropdown').html(response);
                    }
                });
            });
            
        });
    </script>
    <?php
}

// Register shortcode for frontend popup
add_shortcode('lm_location_popup', 'lm_display_location_dropdown_in_popup');
?>
