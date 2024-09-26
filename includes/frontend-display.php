<?php
function lm_display_location_dropdown_in_popup() {
    ?>
    <div id="location-popup">
        <div class="popup-content">
            <h2>Select Your Location</h2>
            <form method="POST" action="">
                <label for="lm_state_dropdown">Select State:</label>
                <select id="lm_state_dropdown" name="lm_state_dropdown" >
                    <option value="">Select State</option>
                    <?php
                    $locations = lm_get_locations();
                    foreach ($locations as $state => $cities) {
                        echo '<option value="' . esc_attr($state) . '">' . esc_html(ucfirst($state)) . '</option>';
                    }
                    ?>
                </select>
                
                <br/><br/>
                <label for="lm_city_dropdown">Select City:</label>
                <select id="lm_city_dropdown" name="lm_city_dropdown">
                    <option value="">Select City</option>
                </select>
                
                <br/><br/>
                <button type="submit">Select as my location</button>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#lm_state_dropdown').on('change', function() {
                var selectedState = $(this).val();

                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url("admin-ajax.php"); ?>',
                    data: {
                        action: 'get_cities',
                        state: selectedState
                    },
                    success: function(response) {
                        $('#lm_city_dropdown').html(response);
                    }
                });
            });
            
        });
    </script>
    <?php
}
add_shortcode('lm_location_popup', 'lm_display_location_dropdown_in_popup');
?>
