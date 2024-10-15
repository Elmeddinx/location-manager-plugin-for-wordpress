(function ($) {
    $(document).ready(function () {
        $(document).on('pumAfterOpen', function () {
            if ($('#lm_state_dropdown').data('select2')) {
                $('#lm_state_dropdown').select2('destroy');
            }
            if ($('#lm_city_dropdown').data('select2')) {
                $('#lm_city_dropdown').select2('destroy');
            }

            const storedState = localStorage.getItem('selected_state');
            const storedCity = localStorage.getItem('selected_city');

            if (storedState) {
                $('#lm_state_dropdown').val(storedState).trigger('change');
            }

            if (storedCity) {
                $('#lm_city_dropdown').val(storedCity);
            }
        });

        $('#lm_state_dropdown').on('change', function () {
            const state = $(this).val();
            const cityDropdown = $('#lm_city_dropdown');

            localStorage.setItem('selected_state', state);

            cityDropdown.html('<option value="">Loading...</option>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_cities',
                    state: state
                },
                success: function (response) {
                    cityDropdown.html(response);

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

        $('#lm_city_dropdown').on('change', function () {
            const city = $(this).val();

            localStorage.setItem('selected_city', city);
        });

        $(document).on('pumAfterClose', function () {
            if ($('#lm_state_dropdown').data('select2')) {
                $('#lm_state_dropdown').select2('destroy');
            }
            if ($('#lm_city_dropdown').data('select2')) {
                $('#lm_city_dropdown').select2('destroy');
            }
        });
        $('#select-location-btn').on('click', function () {
            const selectedState = localStorage.getItem('selected_state');
            const selectedCity = localStorage.getItem('selected_city');

            if (selectedState && selectedCity) {
                window.location.href = '/office/' + selectedCity.toLowerCase() + '-' + selectedState.toLowerCase();
            } else {
                alert('Please select your state and city.');
            }
        });

        let userCity;
        let userState;
        if (localStorage.getItem('selected_city')) {
            userCity = localStorage.getItem('selected_city').toLowerCase().replace(/\s+/g, '-');
        }
        if (localStorage.getItem('selected_city')) {
            userState = localStorage.getItem('selected_state').toLowerCase().replace(/\s+/g, '-');
        }

        if (userCity && userState && typeof lm_service_slugs !== 'undefined' && Array.isArray(lm_service_slugs.slugs)) {
            $('.header-menu a').each(function () {
                const linkText = $(this).text().toLowerCase().trim().replace(/\s+/g, '-');
                const matchingSlug = lm_service_slugs.slugs.find(slug => {
                    const regex = new RegExp(`^${slug}$`, 'i');
                    return regex.test(linkText);
                });
                if (matchingSlug && userCity && userState) {
                    $(this).attr('href', '/' + matchingSlug + '/' + userCity + '-' + userState);
                } else if (matchingSlug && (!userCity || !userState)) {
                    $(this).attr('href', '/' + matchingSlug);
                }
            });
            $('.service-card-link a').each(function () {
                const linkText = $(this).text().toLowerCase().trim().replace(/\s+/g, '-');
                const matchingSlug = lm_service_slugs.slugs.find(slug => {
                    const regex = new RegExp(`^${slug}$`, 'i');
                    return regex.test(linkText);
                });

                if (matchingSlug && userCity && userState) {
                    $(this).attr('href', '/' + matchingSlug + '/' + userCity + '-' + userState);
                } else if (matchingSlug && (!userCity || !userState)) {
                    $(this).attr('href', '/' + matchingSlug);
                }
            });
            $('.service-card-btn .wdt-button').each(function () {
                let linkText = $(this).text().toLowerCase().trim().replace(/\s+/g, '-');

                if (linkText.startsWith('view-')) {
                    linkText = linkText.replace('view-', '');
                }

                const matchingSlug = lm_service_slugs.slugs.find(slug => {
                    const regex = new RegExp(`^${slug}$`, 'i');
                    return regex.test(linkText);
                });

                if (matchingSlug && userCity && userState) {
                    $(this).attr('href', '/' + matchingSlug + '/' + userCity + '-' + userState);
                } else if (matchingSlug && (!userCity || !userState)) {
                    $(this).attr('href', '/' + matchingSlug);
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