    

$(function () {


    $('[href^="#"]').click(function () {
        var adjust = 50;
        var speed = 400;
        var href = $(this).attr("href");
        var target = $(href == "#" || href == "" ? 'html' : href);
        var position = target.offset().top - adjust;
        $('body,html').animate({ scrollTop: position }, speed, 'swing');
        return false;
    });

    //sp-menu
    $(function () {
        $('.toggle').click(function () {
            $(this).toggleClass('active');

            if ($(this).hasClass('active')) {
                $('.nav-menu').addClass('active');
                $('.nav-menu').fadeIn(500);
            } else {
                $('.nav-menu').removeClass('active');
                $('.nav-menu').fadeOut(500);
            }
        });

        $('.navmenu-a').click(function () {
            $('.nav-menu').removeClass('active');
            $('.nav-menu').fadeOut(1000);
            $('.toggle').removeClass('active');
        });
    });
    $('.g-menu a[href]').on('click', function (event) {
        $('.toggle').trigger('click');
    });


	
jQuery(document).ready(function($){
   $('.wpcf7 p').contents().unwrap();
});

$(function(){
  $(".fadeIn").on("inview", function (event, isInView) {
    if (isInView) {
      $(this).stop().addClass("is-show");
    }
  });
});
 
	


	$(function () {
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 100) { // スクロールされたら
            $('.js-header').addClass('change-color');
        } else {
            $('.js-header').removeClass('change-color');
        }
    });
});


});
