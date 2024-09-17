(function ($) {
    $(document).ready(function () {
        // When the state dropdown changes
        $('#lm_state').on('change', function () {
            var state = $(this).val();
            var cityDropdown = $('#lm_city');

            // Reset the city dropdown
            cityDropdown.html('<option value="">Loading...</option>');

            // Send AJAX request to fetch cities
            $.ajax({
                url: ajaxurl,  // This variable is provided by WordPress
                type: 'POST',
                data: {
                    action: 'get_cities',  // This is the action that will trigger the PHP function
                    state: state
                },
                success: function (response) {
                    cityDropdown.html(response);  // Populate the city dropdown with the response
                },
                error: function () {
                    cityDropdown.html('<option value="">Error loading cities</option>');  // Handle errors
                }
            });
        });
    });
    // Ensure Select2 is initialized after the popup is shown

    $(document).on('elementor/popup/show', function () {
        $('.elementor-popup select').select2('destroy');
    });
})(jQuery);
