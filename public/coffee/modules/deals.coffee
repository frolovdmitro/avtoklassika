define [
  'jquery'
  'flexslider'
], ($) ->
  Deals =
    # Инициализация
    init: ->
      that = @

    # Инициализация слайдера с горячими предложениями на главной
    initSlider: (el)->
      $el = $(el)

      $el.flexslider
        animation: "slide"
        directionNav: true
        controlNav: false
        animationLoop: false
        itemWidth: 227
        itemMargin: 9
        slideshow: true
        controlsContainer: '#hot-products__nav'
