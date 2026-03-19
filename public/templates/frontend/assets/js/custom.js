
//WOW Scroll Spy
// var wow = new WOW({
//     //disabled for mobile
//     mobile: false
// });
// wow.init();

$('.animate').jAnimateSequence(['bounce']);
$('.animate2').jAnimateSequence(['bounce', 'tada', 'wobble']);
/* Slicknav Mobile Menu
========================================================*/
$(document).ready(function(){
  $('.hmobile-menu').slicknav({
    prependTo: '.navbar-header',
    parentTag: 'liner',
    allowParentLinks: true,
    duplicate: true,
    label: '',
    closedSymbol: '<i class="fa fa-angle-right"></i>',
    openedSymbol: '<i class="fa fa-angle-down"></i>',
  });
});

// Sticky Nav
$(window).on('scroll', function() {
    if ($(window).scrollTop() > 100) {
        $('.scrolling-navbar').addClass('top-nav-collapse');
    } else {
        $('.scrolling-navbar').removeClass('top-nav-collapse');
    }
});

// Slider Carousel
$("#carousel-image-slider").owlCarousel({
  navigation : false, // Show next and prev buttons
  slideSpeed : 300,
  paginationSpeed : 400,
  singleItem:true,
  pagination: false,
  autoPlay: 3000,
});

 // Back Top button
  var offset = 200;
  var duration = 500;
  $(window).scroll(function() {
    if ($(this).scrollTop() > offset) {
      $('.back-to-top').fadeIn(400);
    } else {
      $('.back-to-top').fadeOut(400);
    }
  });
  $('.back-to-top').click(function(event) {
    event.preventDefault();
    $('html, body').animate({
      scrollTop: 0
    }, 600);
    return false;
  })

// Magnific popup calls
$('.popup-gallery').magnificPopup({
    delegate: 'a',
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    mainClass: 'mfp-img-mobile',
    gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1]
    },
    image: {
        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
    }
});