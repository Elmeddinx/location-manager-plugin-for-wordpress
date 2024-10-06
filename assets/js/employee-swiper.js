jQuery(document).ready(function ($) {
    // lmEmployeesData değişkeninden çalışan verilerini alalım
    var employees = lmEmployeesData.employees;

    // localStorage'dan selected_state ve selected_city değerlerini alalım
    var selectedState = localStorage.getItem('selected_state');
    var selectedCity = localStorage.getItem('selected_city');

    // Çalışanları filtreleyelim
    var filteredEmployees = employees.filter(function (employee) {
        if (selectedState && employee.state.toLowerCase() !== selectedState.toLowerCase()) {
            return false;
        }
        if (selectedCity && employee.city.toLowerCase() !== selectedCity.toLowerCase()) {
            return false;
        }
        return true;
    });

    // Eğer filtrelenmiş çalışan yoksa bir mesaj gösterelim
    if (filteredEmployees.length === 0) {
        $('.lm-employee-swiper').html('<p>No employees found for your location.</p>');
        return;
    }

    // Swiper wrapper içine slide'ları ekleyelim
    var swiperWrapper = $('.lm-employee-swiper .swiper-wrapper');
    filteredEmployees.forEach(function (employee) {
        var imageUrl = employee.image_url;
        var slideHtml = '<div class="swiper-slide">' +
            '<div class="lm-employee-card">' +
            (imageUrl ? '<img src="' + imageUrl + '" alt="' + employee.name + '" />' : '') +
            '<h3>' + employee.name + '</h3>' +
            '<p>' + employee.title + '</p>' +
            '</div>' +
            '</div>';
        swiperWrapper.append(slideHtml);
    });

    // Swiper'ı başlatalım
    var swiper = new Swiper('.lm-employee-swiper', {
        slidesPerView: 3,
        spaceBetween: 30,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 1,
            },
            1024: {
                slidesPerView: 2,
            },
        }
    });
});
