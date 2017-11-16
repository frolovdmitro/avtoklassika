(function() {
  define(['jquery', 'flexslider'], function($) {
    var Deals;
    return Deals = {
      init: function() {
        var that;
        return that = this;
      },
      initSlider: function(el) {
        var $el;
        $el = $(el);
        return $el.flexslider({
          animation: "slide",
          directionNav: true,
          controlNav: false,
          animationLoop: false,
          itemWidth: 227,
          itemMargin: 9,
          slideshow: true,
          controlsContainer: '#hot-products__nav'
        });
      }
    };
  });

}).call(this);
