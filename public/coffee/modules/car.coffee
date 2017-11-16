define [
  'jquery'
  'pubsub'

  'flexslider'
  'uwinPaging'
  'scrollto'
  'uniform'
  'swipebox'
  'powertip'

  'uwinTree'
], ($, PubSub) ->
  Car =
    _filter:
      replica: false
      restaurare: false
      secondhand: false

    # Инициализация
    init: -> # {{{
      that = @

      @quickBuySend()

      $('SELECT').uniform
        selectAutoWidth: false

      # Подключаю пейджинг на страницу
      $('[data-paging]').uwinPaging
        additionParams: @_filter
        gotoTopPage: ->
          $.scrollTo '.layout__content.autoparts', 800, {offset: -65}
        callback: ()->
          PubSub.publish 'LOAD_PAGE_CONTENT', {}

      PubSub.subscribe 'FILTERED', (name, data)->
        that.filteredProducts data

      # Кнопка печати
      print_selector = '.table__actions-link_type_print,' +
        '.detail-info__schema-actions-link_type_print'
      $(print_selector).on 'click', (ev)->
        window.print()
        ev.preventDefault()


      selector = '.car-autoparts-tree__item-plus-minus, ' +
        '.car-autoparts-tree__subitem-plus-minus'

      $('.car-autoparts-tree__list').on 'click', selector, @openAutopartNodeTree

      $('.car-autoparts-tree__list').uwinTree
        selector: '.expanded'
        class_expand_suffix: '_state_expand'
        success: ()->
          that.openCurrentAutopartNodeTree()

      $('.detail-info__select').on 'change', (ev)->
        PubSub.publish 'CHANGE_COLOR_SIZE',
          color_id: ~~$('#color').val()
          size_id: ~~$('#size').val()

        $el = $(@).find(":selected")
        image = $el.attr 'data-image'
        if image
          imageMedium = $el.attr 'data-image-medium'
          $('.detail-info__image-link').attr 'href', image
          $('.detail-info__image-link IMG').attr 'src', imageMedium

      PubSub.subscribe 'CHANGE_COLOR_SIZE', (name, data)->
        $el = $ '#detail-name'
        id = ~~$el.attr 'data-id'
        car_id = ~~$el.attr 'data-car-id'
        url = '/json/car/' + car_id + '/detail-color-size/' + id + '/'

        $.ajax
          type: 'GET'
          url: url
          data: data
        .done (data)->
          data = $.parseJSON data
          $('.detail-info__cost').text data.currency_abb + data.cost
          $('.quick-buy__detail-cost').text data.currency_abb + data.cost
          count = ~~$('#quick-buy-form__count').val() || 1
          $('.quick-buy__total-cost').text(
            data.currency_abb + (data.cost * count)
          )
          $('.detail-info__cost').attr 'data-cost', data.cost_unformat
          $('.detail-info__basket-button').attr 'data-cost', data.cost_unformat
          $('.quick-buy__detail-cost').attr 'data-cost', data.cost_unformat

          $('.detail-info__cost').attr 'data-usd-cost', data.cost_usd
          $('.detail-info__basket-button').attr 'data-usd-cost', data.cost_usd
          $('.quick-buy__detail-cost').attr 'data-usd-cost', data.cost_usd

          if ~~data.count == 0
            notFountText = $('.detail-info__cost').attr 'data-not-fount-text'
            $('.detail-info__cost').addClass 'detail-info__cost_state_disabled'
            $('.detail-info__cost').text notFountText
            $('.detail-info__basket-button').addClass(
              'detail-info__basket-button_state_disabled')
                .removeClass('button-buy')
                .css 'pointer-events', 'none'
            $('.detail-info__buy-fast').addClass(
              'detail-info__buy-fast_state_disabled')
                .css 'pointer-events', 'none'
          else
            $('.detail-info__cost').removeClass(
              'detail-info__cost_state_disabled')
            $('.detail-info__basket-button').removeClass(
              'detail-info__basket-button_state_disabled')
                .addClass('button-buy')
                .css 'pointer-events', ''
            $('.detail-info__buy-fast').removeClass(
              'detail-info__buy-fast_state_disabled')
                .css 'pointer-events', ''
    # }}}

    # Отправляем форму быстрого заказа
    quickBuySend: () -> # {{{
      $('INPUT, SELECT, TEXTAREA').on 'keyup', (ev)->
        keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40]
        if !(ev.keyCode in keyCodes)
          $(@).nextAll('.error').css 'opacity', '0'

      $('.quick-buy__form').on 'submit', (ev)->
        form_name_prefix = 'quick-buy-form__'

        $form = $ @
        $form.find('.error').css 'opacity', ''

        $submit = $form.find('button[type="submit"]')
        $submit.attr 'disabled', 'disabled'

        data = $form.serialize()
        if $('#color').length != 0
          data += '&color='+ $('#color').val() || ''
        if $('#size').length != 0
          data += '&size='+ $('#size').val() || ''

        $.ajax
          url: $form.attr('action')
          data: data
          type: 'POST'
          dataType: 'json'
          success: (data)->
            $('.quick-buy__success-order-num .success-num-order')
              .text data.order_num
            $('.quick-buy__success-text').css 'visibility', 'visible'
            $('.quick-buy__inputs-wrap').css 'visibility', 'hidden'

          error: (response)->
            $submit.removeAttr 'disabled'
            data = response.responseJSON
            errors = data.errors
            delete data.errors

            for i of data
              $input = $('#' + form_name_prefix + data[i].id)
              $inputWrap = $input.parent()
              $errorEl = $inputWrap.find '.error'

              $errorEl.text data[i].text
              $errorEl.css 'opacity', 1

            $('#' + form_name_prefix + data[0].id).focus() if data
        ev.preventDefault()
    # }}}

    # Открывать группу автозапчастей
    openAutopartNodeTree: (ev, success = null) -> # {{{
      $el = $(ev.target).parent() || ev.parent()
      $el = $el.closest 'LI'
      # console.log $el

      $el.siblings('.__item-' + $el.data('id')).toggleClass '__hidden'
      if success
        success()
    # }}}

    # Открываем текущую группу автозапчастей
    openCurrentAutopartNodeTree: () -> # {{{
      that = @
      $pathEl = $('#autopart-path LI')
      if $pathEl.length == 0
        return

      id = ~~$($pathEl.get(0)).text()
      $el = $('.car-autoparts-tree__item[data-id="' + id + '"]')

      that.openAutopartNodeTree $el, ->
        id = ~~$($pathEl.get(1)).text()
        timer = setInterval ->
          $el = $('.car-autoparts-tree__subitem[data-id="' + id + '"]')
          if $el.length == 0
            return
          that.openAutopartNodeTree $el
          $el.find('.expanded').trigger 'click'

          subtimer = setInterval ->
            subid = ~~$($pathEl.get(2)).text()
            $subel = $('.car-autoparts-tree__subsubitem[data-id="' +
              subid + '"]')
            $subel.css 'font-style', 'italic'
            clearInterval(subtimer) if $subel.length != 0
          , 100

          clearInterval(timer) if $el.length != 0
        , 100

      $el.find('.expanded').trigger 'click'
    # }}}

    # Инициализация галереи
    initGallery: (el) -> # {{{
      $el = $(el)

      $el.swipebox
        hideBarsDelay: 0
    # }}}

    positionMiniSchema: (el) -> # {{{
      $el = $(el)
      $currentEl = $ '.detail-info__schema-coord_state_current'
      $coordWrapEL = $ '.detail-info__schema-coord-wrap'
      $coordWrapEL.css 'display', 'block'
      $schemaEl = $ '.detail-info__schema-link'
      $schemaImgEl = $ '.detail-info__schema-image'
      schemaWidth = $schemaEl.outerWidth()
      schemaHeigth = $schemaEl.outerHeight()

      top = left = 0

      if $currentEl.length
        top = ($currentEl.position().top - (schemaHeigth/2) + 14) * -1
        left = ($currentEl.position().left - (schemaWidth/2) + 14) * -1
      else
        top = ((schemaHeigth/2) + 14) * -1
        left = ((schemaWidth/2) + 14) * -1

      $coordWrapEL.css 'display', 'none'

      $schemaImgEl.css 'top', top
      $schemaImgEl.css 'left', left

      $('.detail-info__schema-coord').each ()->
        $newEl = $(@).clone().appendTo $ '.detail-info__schema'
        $newEl.css 'top', '+=' + top + 'px'
        $newEl.css 'left', '+=' + left + 'px'

        $newEl.remove() if ~~$newEl.position().top < 0
        $newEl.remove() if ~~$newEl.position().left < 0
        $newEl.remove() if ~~$newEl.position().left > schemaWidth - 24
        $newEl.remove() if ~~$newEl.position().top > schemaHeigth - 24

      $el.removeClass 'detail-info__schema_state_invisible'
    # }}}

    _initSchema: ($el) -> # {{{
      $el.find('.__schema-inner').on 'mousedown', (ev) ->
        clientWidth = document.body.clientWidth
        clientHeight = document.body.clientHeight
        scrollLeft = $(@).scrollLeft()
        scrollTop = $(@).scrollTop()
        oldX = ev.pageX - (clientWidth - scrollLeft)
        oldY = ev.pageY - (clientHeight - scrollTop)

        X = Y = 0
        $(@).on 'mousemove', (ev)->
          pX = ev.pageX
          pY = ev.pageY

          X = pX - oldX
          Y = pY - oldY
          console.log X, Y

          $(@).scrollTop(
            clientHeight - Y
          )
          $(@).scrollLeft(
            clientWidth - X
          )

          return false

        return false

      $el.find('.__schema-inner').on 'mouseup', ->
        $(@).unbind 'mousemove'
        return false

      $el.find('.__schema-inner').on 'mouseout', ()->
        $(@).unbind 'mousemove'
        return false
    # }}}

    # Инициализация галереи
    initSchemaSwipebox: (el) -> # {{{
      $el = $(el)

      $el.swipebox
        hideBarsDelay: 0
        useInnerWrap: true
        beforeOpen: ->
          timer = setTimeout(
            ()->
              $el = $('.detail-info__schema-coord-wrap ' +
                '.detail-info__schema-coord')

              if $.swipebox.isOpen
                $el.clone().appendTo $('#swipebox-slider .slide-inner')
                $('#swipebox-slider .slide-inner .__tooltip').each ->
                  $(@).powerTip
                    # followMouse: true
                    placement: $(@).data('placement') || 'n'
                    smartPlacement: true
                clearTimeout timer

                $("#swipebox-slider .slide-inner").on 'mousedown', (ev)->
                  clientWidth = document.body.clientWidth
                  clientHeight = document.body.clientHeight
                  scrollLeft = $("#swipebox-slider .slide-inner").scrollLeft()
                  scrollTop = $("#swipebox-slider .slide-inner").scrollTop()
                  oldX = ev.pageX - (clientWidth - scrollLeft)
                  oldY = ev.pageY - (clientHeight - scrollTop)

                  X = Y = 0
                  $("#swipebox-slider .slide-inner").on 'mousemove', (ev)->
                    pX = ev.pageX
                    pY = ev.pageY

                    X = pX - oldX
                    Y = pY - oldY

                    $("#swipebox-slider .slide-inner").scrollTop(
                      clientHeight - Y
                    )
                    $("#swipebox-slider .slide-inner").scrollLeft(
                      clientWidth - X
                    )

                    return false

                  return false

                $("#swipebox-slider .slide-inner").on 'mouseup', ->
                  $("#swipebox-slider .slide-inner").unbind 'mousemove'
                  return false

                $("#swipebox-slider .slide-inner").on 'mouseout', ()->
                  $("#swipebox-slider .slide-inner").unbind 'mousemove'
                  return false
            , 500)
    # }}}

    # Инициализация слайдера с горячими предложениями на главной
    initSlider: (el) -> # {{{
      $el = $(el)

      index = $(".cars-slider__list")
        .children(".cars-slider__item_type_current").index()
      length = $(".cars-slider__list .slider__item").length

      if index >= 2
        index -= 2
      else
        index = 0

      last = false
      if (index + 5) >= length
        last = true
        index = length - 5

      flexslider = $el.flexslider
        animation: "slide"
        directionNav: true
        controlNav: false
        animationLoop: false
        itemWidth: 144
        itemMargin: 35
        slideshow: false
        move: 1
        startAt: index
        start: (ev)->
          $list = $('.cars-slider__list')
          matrix = $list.css('-webkit-transform') ||
            $list.css('-moz-transform') ||
            $list.css('-ms-transform') ||
            $list.css('-o-transform') ||
            $list.css('transform')

          values = matrix.split('(')[1].split(')')[0].split ','

          diff = 0
          diff = (35 * index) if !last
          $('.cars-slider__list').css '-webkit-transform',
            'translate3d(' + (~~values[4] - diff) + 'px, 0, 0)'

          $('.cars-slider__slider').css 'opacity', 1

      if flexslider.length != 0
        flexslider.data('flexslider').resize().update()
    # }}}

    filteredProducts: (data) -> # {{{
      if data.type != 'products'
        return

      if data.value == 'true'
        data.value = true
      else
        data.value = false

      @_filter[data.name] = data.value
    # }}}
