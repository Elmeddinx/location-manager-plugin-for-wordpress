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
                    console.log('Cities loaded: ', response);  // For debugging purposes
                    cityDropdown.html(response);  // Populate the city dropdown with the response
                },
                error: function () {
                    cityDropdown.html('<option value="">Error loading cities</option>');  // Handle errors
                }
            });
        });

        // Popup opened event, without Select2 initialization
        $(document).on('pumAfterOpen', function () {
            console.log('Popup opened');
        });
    });
})(jQuery);
