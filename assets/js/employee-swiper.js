jQuery(document).ready(function ($) {
    // lmEmployeesData değişkeninden çalışan verilerini alalım
    var employees = lmEmployeesData.employees;
  
    // localStorage'dan selected_state ve selected_city değerlerini alalım
    var selectedState = localStorage.getItem("selected_state");
    var selectedCity = localStorage.getItem("selected_city");
  
    // 1. Çalışanları eyalet bazında filtreleyelim
    var filteredEmployees = employees.filter(function (employee) {
      return (
        selectedState &&
        employee.state.toLowerCase() === selectedState.toLowerCase()
      );
    });
  
    // 2. Şehir seçilmişse ve o şehir için çalışanlar varsa, çalışanları şehir bazında filtreleyelim
    if (selectedCity) {
      var cityEmployees = filteredEmployees.filter(function (employee) {
        return employee.city.toLowerCase() === selectedCity.toLowerCase();
      });
  
      if (cityEmployees.length > 0) {
        filteredEmployees = cityEmployees;
      }
      // Eğer şehir için çalışan yoksa, filteredEmployees listesi eyaletin tüm çalışanlarını içerir
    }
  
    // Eğer filtrelenmiş çalışan yoksa bir mesaj gösterelim
    if (filteredEmployees.length === 0) {
      $(".lm-employee-swiper").html(
        "<p>No employees found for your location.</p>"
      );
      return;
    }
  
    // Swiper wrapper içine slide'ları ekleyelim
    var swiperWrapper = $(".lm-employee-swiper .swiper-wrapper");
    filteredEmployees.forEach(function (employee) {
      var imageUrl = employee.image_url;
      var slideHtml =
        '<div class="swiper-slide">' +
        '<div class="lm-employee-card">' +
        (imageUrl
          ? '<img src="' + imageUrl + '" alt="' + employee.name + '"  class="lm-employee-img"/>'
          : "") +
        "<h3 class='lm-employee-name'>" +
        employee.name +
        "</h3>" +
        "<p class='lm-employee-title'>" +
        employee.title +
        "</p>" +
        "</div>" +
        "</div>";
      swiperWrapper.append(slideHtml);
    });
  
    // Swiper'ı başlatalım
    var swiper = new Swiper(".lm-employee-swiper", {
      slidesPerView: 1,
      spaceBetween: 30,
  
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        600: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
        1400: {
          slidesPerView: 4,
        },
        1920: {
          slidesPerView: 5,
        },
      },
    });
  });
  