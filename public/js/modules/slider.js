(function() {
  define(['jquery', 'flexslider'], function($) {
    var Slider;
    return Slider = {
      init: function() {
        var that;
        return that = this;
      },
      initSlider: function(el) {
        var $el, slideshow, slideshowSpeed;
        $el = $(el);
        slideshow = false;
        slideshowSpeed = 0;
        if (~~($el.attr('data-autoslide')) > 0) {
          slideshow = true;
          slideshowSpeed = ~~($el.attr('data-autoslide'));
        }
        return $el.flexslider({
          animation: "slide",
          directionNav: true,
          controlNav: true,
          animationLoop: true,
          slideshow: slideshow,
          slideshowSpeed: slideshowSpeed,
          itemWidth: 700
        });
      }
    };
  });

}).call(this);
