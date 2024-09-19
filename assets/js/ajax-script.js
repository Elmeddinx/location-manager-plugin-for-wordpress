(function ($) {
    $(document).ready(function () {
        // Select2'yi devre dışı bırakmadan önce kontrol et
        $(document).on('pumAfterOpen', function () {
            
            // Eğer Select2 zaten aktifse, onu devre dışı bırak
            if ($.fn.select2) {
                if ($('#lm_state_dropdown').data('select2')) {
                    $('#lm_state_dropdown').select2('destroy');
                }
                if ($('#lm_city_dropdown').data('select2')) {
                    $('#lm_city_dropdown').select2('destroy');
                }
            }
        });

        // State dropdown değiştiğinde çalışacak kod
        $('#lm_state_dropdown').on('change', function () {
            var state = $(this).val();
            var cityDropdown = $('#lm_city_dropdown');

            // City dropdown'ı sıfırla
            cityDropdown.html('<option value="">Loading...</option>');

            // AJAX ile şehirleri getir
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_cities',
                    state: state
                },
                success: function (response) {
                    cityDropdown.html(response);
                },
                error: function () {
                    cityDropdown.html('<option value="">Error loading cities</option>');
                }
            });
        });
    });

    $(document).on('pumAfterClose', function () {
        // Pop-up kapanırken Select2'yi devre dışı bırakmadan önce kontrol et
        if ($.fn.select2) {
            if ($('#lm_state_dropdown').data('select2')) {
                $('#lm_state_dropdown').select2('destroy');
            }
            if ($('#lm_city_dropdown').data('select2')) {
                $('#lm_city_dropdown').select2('destroy');
            }
        }
    });
})(jQuery);
