(function ($) {
    $(document).ready(function () {
        // Popup open event handler
        $(document).on('pumAfterOpen', function () {
            if ($('#lm_state_dropdown').data('select2')) {
                $('#lm_state_dropdown').select2('destroy');
            }
            if ($('#lm_city_dropdown').data('select2')) {
                $('#lm_city_dropdown').select2('destroy');
            }

            // If values exist in local storage, pre-fill them
            const storedState = localStorage.getItem('selected_state');
            const storedCity = localStorage.getItem('selected_city');

            if (storedState) {
                $('#lm_state_dropdown').val(storedState).trigger('change');
            }

            if (storedCity) {
                $('#lm_city_dropdown').val(storedCity);
            }
        });

        // State dropdown change event
        $('#lm_state_dropdown').on('change', function () {
            const state = $(this).val();
            const cityDropdown = $('#lm_city_dropdown');

            // Store the selected state in local storage
            localStorage.setItem('selected_state', state);

            // Reset city dropdown
            cityDropdown.html('<option value="">Loading...</option>');

            // Fetch cities based on selected state
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_cities',
                    state: state
                },
                success: function (response) {
                    cityDropdown.html(response);

                    // Store the selected city in local storage if it's already selected
                    const storedCity = localStorage.getItem('selected_city');
                    if (storedCity) {
                        cityDropdown.val(storedCity);
                    }
                },
                error: function () {
                    cityDropdown.html('<option value="">Error loading cities</option>');
                }
            });
        });

        // City dropdown change event
        $('#lm_city_dropdown').on('change', function () {
            const city = $(this).val();

            // Store the selected city in local storage
            localStorage.setItem('selected_city', city);
        });

        // After closing the popup, destroy Select2 if it exists
        $(document).on('pumAfterClose', function () {
            if ($('#lm_state_dropdown').data('select2')) {
                $('#lm_state_dropdown').select2('destroy');
            }
            if ($('#lm_city_dropdown').data('select2')) {
                $('#lm_city_dropdown').select2('destroy');
            }
        });
        // "Select as My Location" butonuna tıklandığında yönlendirme
        $('#select-location-btn').on('click', function () {
            const selectedState = localStorage.getItem('selected_state');
            const selectedCity = localStorage.getItem('selected_city');

            // Eğer kullanıcı şehir ve eyalet seçtiyse yönlendirme yap
            if (selectedState && selectedCity) {
                window.location.href = '/office/' + selectedCity.toLowerCase() + '-' + selectedState.toLowerCase();
            } else {
                // Eğer şehir ve eyalet seçilmediyse bir uyarı ver
                alert('Please select your state and city.');
            }
        });

        // Dynamically update service links based on stored city and state
        const userCity = localStorage.getItem('selected_city').toLowerCase().replace(/\s+/g, '-');
        const userState = localStorage.getItem('selected_state').toLowerCase().replace(/\s+/g, '-');

        // Make sure that the service slugs exist
        if (typeof lm_service_slugs !== 'undefined' && Array.isArray(lm_service_slugs.slugs)) {
            // Iterate through the menu items and update links
            $('.header-menu a').each(function () {
                const linkText = $(this).text().toLowerCase().trim();
                console.log("Link Text:" + linkText);
                // Check if the link text matches any service slug
                const matchingSlug = lm_service_slugs.slugs.find(slug => linkText.includes(slug));

                // Only update the link if both city and state are available
                if (matchingSlug && userCity && userState) {
                    $(this).attr('href', '/' + matchingSlug + '/' + userCity + '-' + userState);
                    console.log('Updated link for:', matchingSlug);
                } else if (matchingSlug && (!userCity || !userState)) {
                    // If either city or state is missing, keep the original link (no change)
                    $(this).attr('href', '/' + matchingSlug);
                    console.log('Kept original link for:', matchingSlug);
                } else {
                    console.log('No matching slug found for:', linkText);
                }
            });
        } else {
            console.error('Service slugs are not defined or are not an array.');
        }

    });

})(jQuery);

document.addEventListener('DOMContentLoaded', function () {
    var userCity = localStorage.getItem('selected_city');
    let userState = localStorage.getItem('selected_state');

    if (userCity && userState) {
        let formattedCity = userCity.toLowerCase().replace(/\s+/g, '-');
        let formattedState = userState.toLowerCase().replace(/\s+/g, '-');

        let locationTextElement = document.getElementById('header-location-text');
        let linkHref = document.createElement("a");
        linkHref.href = `/office/${formattedCity}-${formattedState}`;
        linkHref.textContent = userCity;

        if (locationTextElement) {
            locationTextElement.innerHTML = "";
            locationTextElement.appendChild(linkHref);
        }
    }
});