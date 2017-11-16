define [
  'jquery'
  'flexslider'
], ($) ->
  Slider =
    # Инициализация
    init: ->
      that = @

    # Инициализация слайдера с горячими предложениями на главной
    initSlider: (el)->
      $el = $(el)

      slideshow = false
      slideshowSpeed = 0
      if ~~($el.attr 'data-autoslide') > 0
        slideshow = true
        slideshowSpeed = ~~($el.attr 'data-autoslide')

      $el.flexslider
        animation: "slide"
        directionNav: true
        controlNav: true
        animationLoop: true
        slideshow: slideshow
        slideshowSpeed: slideshowSpeed
        itemWidth: 700
        # itemMargin: 5
