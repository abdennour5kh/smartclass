$(document).ready(function () {
  var body = $('body');
    var contentWrapper = $('.content-wrapper');
    var scroller = $('.container-scroller');
    var footer = $('.footer');
    var sidebar = $('.sidebar');

    function addActiveClass(element) {
    const href = element.attr('href'); 
    const currentPath = window.location.pathname; 

    
    const url = new URL(href, window.location.origin); 
    const linkPath = url.pathname;

    if (currentPath === linkPath || currentPath.startsWith(linkPath + '/')) {
      element.parents('.nav-item').addClass('active');

      if (element.parents('.sub-menu').length) {
        element.closest('.collapse').addClass('show');
        element.addClass('active');
      }

      if (element.parents('.submenu-item').length) {
        element.addClass('active');
      }
    }
  }

  // Run the function on each sidebar link
  $(document).ready(function () {
    $('.sidebar .nav-link').each(function () {
      addActiveClass($(this));
    });
  });



    //Close other submenu in sidebar on opening any

    sidebar.on('show.bs.collapse', '.collapse', function() {
      sidebar.find('.collapse.show').collapse('hide');
    });


    //Change sidebar

    $('[data-toggle="minimize"]').on("click", function() {
      body.toggleClass('sidebar-icon-only');
    });
    $('[data-toggle="offcanvas"]').on("click", function() {
      $('.sidebar-offcanvas').toggleClass('active')
    });

    //checkbox and radios
    $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');
  
});
document.addEventListener("DOMContentLoaded", function () {
  new Swiper(".announcement", {
      slidesPerView: 1,
      spaceBetween: 20,
      loop: true,
      // Pagination dots
      pagination: {
          el: ".swiper-pagination",
          clickable: true,
      },
      autoplay: {
          delay: 2000, // 2 seconds
          disableOnInteraction: false,
      },
  //   breakpoints: {
  //     768: {
  //       slidesPerView: 2
  //     },
  //     992: {
  //       slidesPerView: 3
  //     }
  //   }
  });

  new Swiper('#classes', {
    slidesPerView: 1, // or auto
    spaceBetween: 15,
    navigation: {
      nextEl: '#swiper-next',
      prevEl: '#swiper-prev',
    },
    loop: true,
    breakpoints: {
      768: { slidesPerView: 2 },
      992: { slidesPerView: 3 }
    },
    autoplay: {
      delay: 4000, // 4 seconds
      disableOnInteraction: false,
    },
  });
});
window.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
      document.querySelectorAll('#classesProgressBar').forEach(bar => {
          const percentage = parseInt(bar.getAttribute('aria-valuenow'), 10);

          // Remove any existing color
          bar.classList.remove('bg-success', 'bg-warning', 'bg-danger');

          bar.style.width = percentage + "%";

          // Add appropriate Bootstrap color
          if (percentage >= 75) {
              bar.classList.add('bg-success');
          } else if (percentage >= 50) {
              bar.classList.add('bg-warning');
          } else {
              bar.classList.add('bg-danger');
          }

          
      });
  }, 100); // delay to trigger transition
});
$(document).ready(function () {
  window.table = $('#smartClassTable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthChange: false,
      ordering: true,
      dom: 'tip',
      order: [[0, 'asc']],
  });

  $('#smartClassTableSearch').on('keyup', function () {
      table.search(this.value).draw();
  });

  $('#downloadReport').on('click', function () {
      alert('Download will be implemented soon.');
  });

});