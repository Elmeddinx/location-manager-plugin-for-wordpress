jQuery(document).ready(function ($) {
  var employees = lmEmployeesData.employees;

  var selectedState = localStorage.getItem("selected_state");
  var selectedCity = localStorage.getItem("selected_city");
  var swiperTitleSpan = document.getElementById("employee-swiper-title-span");
  var filteredEmployees = employees.filter(function (employee) {
    return (
      selectedState &&
      employee.state.toLowerCase() === selectedState.toLowerCase()
    );
  });

  if (selectedCity) {
    var cityEmployees = filteredEmployees.filter(function (employee) {
      return employee.city.toLowerCase() === selectedCity.toLowerCase();
    });

    if (cityEmployees.length > 0) {
      filteredEmployees = cityEmployees;
      swiperTitleSpan.textContent = selectedCity;
    } else {
      swiperTitleSpan.textContent = selectedState;
    }
  } else {
    swiperTitleSpan.textContent = selectedState;
  }

  if (filteredEmployees.length === 0) {
    $(".employee-swiper-container").html("");
    return;
  }

  var swiperWrapper = $(".lm-employee-swiper .swiper-wrapper");
  filteredEmployees.forEach(function (employee) {
    var imageUrl = employee.image_url;
    var slideHtml =
      '<div class="swiper-slide">' +
      '<div class="lm-employee-card">' +
      (imageUrl
        ? '<img src="' +
          imageUrl +
          '" alt="' +
          employee.name +
          '"  class="lm-employee-img"/>'
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

  var swiper = new Swiper(".lm-employee-swiper", {
    slidesPerView: 1,
    spaceBetween: 30,

    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      768: {
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
