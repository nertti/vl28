jQuery(document).ready(function($) {
  // Image.svg to svg code
  jQuery('.svg').each(function () {
    var $img = jQuery(this);
    var imgID = $img.attr('id');
    var imgClass = $img.attr('class');
    var imgURL = $img.attr('src');

    jQuery.get(imgURL, function (data) {
      // Get the SVG tag, ignore the rest
      var $svg = jQuery(data).find('svg');

      // Add replaced image's ID to the new SVG
      if (typeof imgID !== 'undefined') {
        $svg = $svg.attr('id', imgID);
      }
      // Add replaced image's classes to the new SVG
      if (typeof imgClass !== 'undefined') {
        $svg = $svg.attr('class', imgClass + ' replaced-svg');
      }

      // Remove any invalid XML tags as per http://validator.w3.org
      $svg = $svg.removeAttr('xmlns:a');

      // Replace image with new SVG
      $img.replaceWith($svg);

    }, 'xml');
  });

  $(".phone-input").mask("+7 (999) 999-99-99");

   // Активация модальных окон
   const allModal = new HystModal({
    linkAttributeName: "data-hystmodal",
    beforeOpen: function(modal){
      if(modal.openedWindow.id == 'cartModal') {
        $('.header').addClass('header_index');
      }

      if(modal.openedWindow.id == 'menuModal') {
        $('.header').addClass('header_index');
        $('.menu-btn').addClass('active');
        $('.menu-btn').attr('data-hystclose', 'true');
        $('.menu-btn').removeAttr('data-hystmodal');
      }
    },
    afterClose: function(modal){
      if(modal.openedWindow.id == 'cartModal') {
        $('.header').removeClass('header_index');
      }

      if(modal.openedWindow.id == 'menuModal') {
        $('.header').removeClass('header_index');
        $('.menu-btn').removeClass('active');
        $('.menu-btn').removeAttr('data-hystclose');
        $('.menu-btn').attr('data-hystmodal', '#menuModal');
      }
    },
  });

   const productSwiper = new Swiper('.product-swiper', {
    slidesPerView: 1,
    loop: true,
    observer: true,
    pagination: {
      el: '.product-swiper .swiper-pagination',
      clickable: true,
    },
  });

  $('.tovar__color-item').on('click', function(){
    var colorValue = $(this).find('input').val();
    $('.tovar__color-current').text(colorValue);
  });

   // Табы в продукте
   $('.client__tab:not(.active)').hide();
   $(document).on("click", ".client__nav-item:not(.active)", function () {
     $(this)
         .addClass("active")
         .siblings()
         .removeClass("active")
         .closest(".client")
         .find(".client__tab")
         .removeClass('active')
         .hide()
         .eq($(this).index())
         .addClass('active')
         .fadeIn();
   });

    // Изменение кол-ва продукта
    /*
  $(document).on('click', '.plus, .minus', function () {
		var qty = $(this).parent();
		var val = parseFloat(qty.find('input').val());
		var max = parseFloat(qty.find('input').attr('max'));
		var min = parseFloat(qty.find('input').attr('min'));
		var step = 1;

      if ($(this).is('.plus')) {
        if (max && max <= val) {
          qty.find('input').val(max);
        } else {
          qty.find('input').val(val + step);
        }
      } 
      
      if ($(this).is('.minus')) {
        if (min && min >= val) {
          qty.find('input').val(min);
        } else if (val > min) {
          qty.find('input').val(val - step);
        }
      }

      qty.find('input').trigger('change');
	});

  $('.promo__btn').on('click', function(){
    $(this).toggleClass('active');
    $('.promo__show').toggle(400);
  });
*/

   $('.loyal__tab:not(.active)').hide();
   $(document).on("click", ".loyal__nav-item:not(.active)", function () {
     $(this)
         .addClass("active")
         .siblings()
         .removeClass("active")
         .closest(".loyal")
         .find(".loyal__tab")
         .removeClass('active')
         .hide()
         .eq($(this).index())
         .addClass('active')
         .fadeIn();
   });


  var gallerySwipers = [];
  $('.product__swiper').each(function(i) {
    var this_ID = $(this).attr('id');

    gallerySwipers[i] = new Swiper("#"+this_ID, {
      spaceBetween: 0,
      slidesPerView: 1,
      navigation: {
        nextEl: "#"+this_ID+" .swiper-button-next",
        prevEl: "#"+this_ID+" .swiper-button-prev",
      },
      scrollbar: {
        el: "#"+this_ID+" .swiper-scrollbar",
      },
    pagination: {
        el: '#'+this_ID+' .swiper-pagination',
        clickable: true,
    },
      breakpoints: {
        991: {
          loop: true,
        },
      }
    }); 
  });

  $(window).scroll(function(){
    var sticky = $('.header_product'),
        scroll = $(window).scrollTop();
  
    if (scroll >= 150) sticky.addClass('header_animate');
    else sticky.removeClass('header_animate');
  });

    $(document).on("click", ".filter__item_color", function() {
        // Удаляем активный класс со всех элементов checkmark
        $('.checkmark').removeClass('active');

        // Добавляем активный класс к вложенному элементу checkmark
        $(this).find('.checkmark').addClass('active');
    });
});
