define [ # {{{
  # Библиотеки
  'jquery'
  'pubsub'

  # Вспомогательные библиотеки
  'sparky'

  # Модули
  'Deals'
  'Ads'
  'Slider'
  'Car'
  'Basket'
  'News'
  'Users'
  'Comments'

  'UwinGoogleMap'
  'uwinTabs'

  'json2'
  'cookie'
  'hashchange'
  'mixitup'
  'bezier'
  'numeral'
  'typeahead'
  'passwordInput'
  'socialshare'
  'placeholder'
  'powertip'
  'modal' # }}}
], ($, PubSub, sparky, Deals, Ads, Slider, Car, Basket, News, Users, Comments,
UwinGoogleMap) ->
  Avtoclassika = Avtoclassika || (($, w, d) ->
    Routes = {} # Логика для отдельных страниц
    App    = {} # Глобальная логика и инициализация
    Public = {} # Публичные функции

    cookieDomain = '.' + $('META[property="uwin:serverName"]').attr('content')

    settings =
      basket:
        sum: 0
        sumUsd: 0
        count: 0
        products: {}
        promocode: 0
      meta: {}
      debug: false

    cache = {}

    Routes =
      # Главная страница
      index: -> # {{{ Routes.index()
        Slider.initSlider '#important-panel__slider'
        Deals.initSlider '#hot-products__slider'
        Ads.initBarSlider '#message-bar__slider'
        Ads.initAddForm()

        if location.hash.substr(1) == '_=_'
          location.hash = ''

        filter = location.hash.substr(1).split('=')[0] || 'all'
        $('#catalog-autoparts__list-wrap').mixitup
          transitionSpeed: 500
          showOnLoad: filter

        $(window).bind 'hashchange', (e)->
          if location.hash == ''
            $('#catalog-autoparts__list-wrap').mixitup('filter', 'all')
          else
            $('#catalog-autoparts__list-wrap').mixitup('filter',
              location.hash.substr(1).split('=')[0])
      # }}} Routes.index()

      basket: -> # {{{ Routes.basket()
        Basket.initSteps()
      # }}} Routes.basket()

      car: -> # {{{ Routes.car()
        Car.init()
        Car.initSlider '#cars-slider__slider'
        Car.initGallery '.detail-info__thumbnail-link, ' +
          'A.detail-info__image-link'
        Car.initSchemaSwipebox '.detail-info__schema-link'
        Car._initSchema $('.autoparts__schema')
        Car.positionMiniSchema '.detail-info__schema'

        Deals.initSlider '#hot-products__slider'
        Basket.quickBuyForm()
      # }}} Routes.car()

      news: -> # {{{ Routes.news()
        News.init()
      # }}} Routes.news()

      ads: -> # {{{ Routes.ads()
        Ads.init()
        Ads.initGallery '.ad__thumbnail-link, .ad__image'
        Ads.initSlider '#other-adverts__slider'
      # }}} Routes.ads()

      oAuth: -> # {{{ Routes.oAuth()
        window.close()
      # }}} Routes.oAuth()

    App =
      # Инициализация приложения
      init: -> # {{{ App.init()
        sparky.init settings
        sparky.bindEvents()
        sparky.route Routes

        numeral.language 'ru',
          delimiters:
            thousands: ' '
            decimal: ','
        numeral.language 'ru'

        $('.form__input-password').hideShowPassword
          innerToggle: true
          states:
            shown:
              toggle:
                content: $('.form__input-password').data 'hide-txt'

            hidden:
              toggle:
                content: $('.form__input-password').data 'show-txt'

        $.modal.defaults =
          overlay: "#fff"
          opacity: 0.75
          zIndex: 10000
          escapeClose: true
          clickClose: true
          closeText: ''
          closeClass: ''
          showClose: false
          modalClass: "modal"
          fadeDelay: 0
          fadeDuration: 100

        $(document).on $.modal.BEFORE_OPEN, (ev, modal)->
          if $('#fileupload-single').length == 0
            return

          $('#fileupload-single').fileupload
            dataType: 'json'
            send: (ev, data)->
              if data.files[0].size > (1024 *1024 * 1)
                $errorEl = $('.form__fileinput-error')
                $errorEl.text $(ev.target).attr 'data-error-filesize'
                $errorEl.css 'opacity', '1'

                timer = setInterval ->
                  $errorEl.css 'opacity', '0'
                  clearInterval(timer)
                , 5000

                return false

            progressall: (e, data)->
              progress = parseInt(data.loaded / data.total * 100, 10)
              $('.form__submit').attr 'disabled', 'disabled'
              $('#upload-progress').css 'width', (progress + '%')
              if progress == 100
                $('.form__submit').removeAttr 'disabled'

            error: ()->
              $('#upload-progress').css 'width', 0

            done: (ev, data)->
              filesNames = data.files[0].name
              $('.form__fileinput-files').html filesNames
              $('#upload-progress').css 'width', 0
              $('#add-request #uploaded-file').val data.result.file


        $('.share-fb').socialSharePrivacy()
        if $('#vk_like').length > 0
          VK.init
            apiId: 4241621
            onlyWidgets: true
          VK.Widgets.Like "vk_like", {type: "button", height: 20}

        PubSub.subscribe 'REBIND', ->
          sparky.bindEvents()
        PubSub.subscribe 'CHANGE_BASKET', (name, data)->
          App.logic.changeBasket(data)
        PubSub.subscribe 'DRAW_BASKET', (name)->
          App.logic._drawBasket()
      # }}} App.init()

      logic:
        _drawBasket: ()->
          basket = settings.basket
          $countEl = $ '.basket-bar__notifier'
          $countEl.addClass 'pulse'
          $countEl.text basket.count
          if ~~basket.count > 0
            $('.basket-bar').removeClass 'basket-bar_state_disabled'
          else
            $('.basket-bar').addClass 'basket-bar_state_disabled'

          $sumIntEl = $ '.basket-bar__sum_type_int'
          $sumDecimalEl = $ '.basket-bar__sum_type_decimal'

          sumUnformat = basket.sum - (basket.sum/100 * (basket.promocode||0))
          sumUsdUnformat = basket.sumUsd -
            (basket.sumUsd/100 * (basket.promocode||0))

          sum = numeral(sumUnformat).format('0,0.0').split ','
          $sumIntEl.text sum[0]
          $sumDecimalEl.text sum[1]
          $('.basket-bar__sum').attr 'data-cost', sumUnformat
          $('.basket-bar__sum').attr 'data-usd-cost', sumUsdUnformat

          sum = numeral(sumUnformat).format('0,0.00')
          sum = sum.replace ',00', ''
          $sidebarCostEl = $('.basket-products__total-cost')
          $sidebarCostEl.attr 'data-cost', sumUnformat
          $sidebarCostEl.attr 'data-usd-cost', sumUsdUnformat
          abb = $('#currencies LI A').first().attr 'data-short-name'
          if abb == 'P'
            abb = '<span class="rur">' + abb + '</span>'
          if abb == 'грн.'
            $sidebarCostEl.html numeral(sumUnformat).format('0,0') + '&thinsp;<small>' + abb + '</small>'
          else
            $sidebarCostEl.html abb + sum

          $('.basket-bar__currency').html abb

          if abb == 'грн.'
            $('HEADER .layout__header-info-bar .basket-bar__currency').detach().insertAfter('HEADER .layout__header-info-bar .basket-bar__sum_type_int')
            $('.layout__header-info-bar_type_sticky .basket-bar__currency').detach().insertAfter('.layout__header-info-bar_type_sticky .basket-bar__sum_type_int')
            $('.basket-bar__currency').each ->
              $(@).replaceWith('<small class="basket-bar__currency" style="font-size:65%;"> ' + $(@).text() + '</small>')
          else
            $('HEADER .layout__header-info-bar .basket-bar__currency').detach().insertBefore('HEADER .layout__header-info-bar .basket-bar__sum_type_int')
            $('.layout__header-info-bar_type_sticky .basket-bar__currency').detach().insertBefore('.layout__header-info-bar_type_sticky .basket-bar__sum_type_int')
            $('.basket-bar__currency').each ->
              $(@).replaceWith('<span class="basket-bar__currency">' + $(@).text() + '</span>')

          if sumUnformat <= 0
            $('#basket-submit').attr 'disabled', 'disabled'
          else
            $('#basket-submit').removeAttr 'disabled'

        changeBasket: (data)-> # {{{ App.logic.changeBasket(data)
          basket = settings.basket
          data.type = data.type || 1

          basket.sum += (parseFloat(data.cost) * ~~data.count) * data.type
          basket.sumUsd += (parseFloat(data.costUsd) * ~~data.count) * data.type
          basket.count = ~~basket.count + (~~data.count * data.type)

          id = data.id.toString().concat('-', data.size, '-', data.color)
          if data.type == 1
            if basket.products[id]
              basket.products[id]
                .count = ~~basket.products[id].count + ~~data.count
            else
              basket.products[id] =
                id: data.id
                count: data.count
                size: data.size
                color: data.color
          else
            delete basket.products[id]

          if basket.sum <= 0
            $('#basket-submit').attr 'disabled', 'disabled'
          else
            $('#basket-submit').removeAttr 'disabled'

          # localStorage.setItem 'basket', JSON.stringify basket
          $.cookie('basket', JSON.stringify(basket), {
            expires: 365
            path: '/'
            domain: cookieDomain
          })

          App.logic._drawBasket()

        # }}} App.logic.changeBasket(data)

        stickyHeader: ()-> # {{{ App.logic.stickyHeader()
          sticky_header = $('.layout__header-info-bar_type_sticky')
          $(window).on 'scroll', ()->
            if $(document).scrollTop() > 60
              sticky_header.addClass 'layout__header-info-bar_type_sticky-show'
            else
              sticky_header
                .removeClass 'layout__header-info-bar_type_sticky-show'
          $(window).trigger 'scroll'
        # }}} App.logic.stickyHeader()

        flyToBasket: ()-> # {{{ App.logic.flyToBasket()
          $('.button-buy').unbind 'click'
          $('.button-buy').on 'click', (ev)->
            $el = $ ev.target
            id = ~~$el.attr 'data-id'
            cost = $el.attr 'data-cost'
            costUsd = $el.attr 'data-usd-cost'
            size = $el.closest('.detail-info').find('#size').val() || 0
            color = $el.closest('.detail-info').find('#color').val() || 0
            $('.basket-bar__notifier').removeClass 'pulse'

            $product = $el.closest('.products-list__item, .hot-products__item, .detail-info').find('.product-image-' + id)

            count = 1
            if 0 != $el.parent().find('#__form__count').length
              count = ~~$el.parent().find('#__form__count').val()

            if 0 != $product.length
              diffFlyLeft = -80
              diffFlyTop = -37
              if $product.hasClass 'detail-info__image-link'
                diffFlyLeft = -150
                diffFlyTop = -77

              clone = $product.clone()
              position = $product.offset()
              clone.css
                'position': 'absolute'

              classSticky = 'layout__header-info-bar_type_sticky-show'
              if $('.layout__header-info-bar_type_sticky').hasClass(classSticky)
                stopEl = $ '.layout__header-info-bar_type_sticky .basket-bar'
              else
                diffFlyTop = $(document).scrollTop() * -1 + 20
                stopEl = $ '.layout__header-info-bar .basket-bar'
              stop_position = stopEl.offset()

              bezier_params =
                start:
                  x: position.left
                  y: position.top
                  angle: -90
                end:
                  x: stop_position.left + diffFlyLeft
                  y: stop_position.top + diffFlyTop
                  angle: 180
                  length: .2

              clone.appendTo('BODY')
              clone.css('opacity')

              clone.addClass 'type_fly'
              clone.animate {path : new $.path.bezier(bezier_params)}, 700, ()->
                $('.basket-bar').removeClass 'basket-bar_state_disabled'
                PubSub.publish 'CHANGE_BASKET',
                  method: 'add'
                  id: id
                  count: count
                  cost: cost
                  costUsd: costUsd
                  color: color
                  size: size
                  oper: 'add'
                  basket: settings.basket
            else
              $('.basket-bar').removeClass 'basket-bar_state_disabled'
              PubSub.publish 'CHANGE_BASKET',
                method: 'add'
                id: id
                count: count
                cost: cost
                costUsd: costUsd
                color: color
                size: size
                basket: settings.basket

            ev.preventDefault()
        # }}} App.logic.flyToBasket()

        virtualFormElements: ()-> # {{{ App.logic.virtualFormElements()
          $('.form__virtual-checkbox').on 'click', (ev)->
            $el = $ ev.currentTarget
            className = 'form__virtual-checkbox_state_ckecked'
            $el.toggleClass className
            value = false
            if $el.hasClass className
              value = true
              if $el.attr('data-name') == 'all'
                $('.form__virtual-checkbox').not($el)
                  .attr('data-value', 'false')
                  .removeClass 'form__virtual-checkbox_state_ckecked'
              else
                $('.form__virtual-checkbox[data-name="all"]')
                  .attr('data-value', 'false')
                  .removeClass 'form__virtual-checkbox_state_ckecked'
            $el.attr 'data-value', value

            data = {
              name: $el.attr 'data-name'
              value: $el.attr 'data-value'
            }

            App.logic.buildFilterHash(data)

            ev.preventDefault()

          $(window).bind 'hashchange', (ev)->
            App.logic.buildFilterHash()
        # }}} App.logic.virtualFormElements()

        buildFilterHash: (data = null)-> # {{{ App.logic.buildFilterHash()
          if data && data.name == 'all' && data.value == 'true'
            location.hash = ''

            return

          filter = location.hash.substr(1)
          filterParamsArray = []
          filterParamsArray = filter.split '&' if filter
          filterParams = {}

          for index of filterParamsArray
            nameVal = filterParamsArray[index].split('=')
            filterParams[nameVal[0]] = nameVal[1]
            $el = $('[data-name=' + nameVal[0] + ']')
            $el.attr 'data-' + nameVal[0], nameVal[1]
            if nameVal[1] == 'true'
              $el.addClass 'form__virtual-checkbox_state_ckecked'
            else
              $el.removeClass 'form__virtual-checkbox_state_ckecked'

          filterParams[data.name] = data.value if data
          filterParams.page = 1 if data

          hash = ''
          for index of filterParams
            if typeof filterParams[index] == 'undefined'
              filterParams[index] = '=true'
            else
              filterParams[index] = '=' + filterParams[index]

            hash += index + filterParams[index] + '&'
          hash = hash.substr 0, (hash.length - 1)

          if hash != ''
            location.hash = hash

        # }}} App.logic.buildFilterHash()

        formElements: ()-> # {{{ App.logic.formElements()
          $(document).on 'click', '.form__input-count-btn', (ev)->
            ev.preventDefault()

            $input = $(@).parent().find 'INPUT'
            step = ~~$(@).attr 'data-step'
            val = ~~$input.val()

            result = val + step
            if result <= 1
              result = 1

            $input.val(result)
            if $input.hasClass 'basket-products__count'
              id = ~~$input.attr 'data-id'
              color_id = ~~$input.attr 'data-color-id'
              size_id = ~~$input.attr 'data-size-id'
              PubSub.publish 'CHANGE_PRODUCT_COUNT',
                count: result
                id: id
                color: color_id
                size: size_id
            else
              PubSub.publish 'CHANGE_QUICK_COUNT', result

          $(document).on 'change', '.basket-products__count', (ev)->
            id = ~~$(@).attr 'data-id'
            color_id = ~~$(@).attr 'data-color-id'
            size_id = ~~$(@).attr 'data-size-id'
            PubSub.publish 'CHANGE_PRODUCT_COUNT',
              count: ~~$(@).val()
              id: id
              color: color_id
              size: size_id

          $('#quick-buy-form__count').on 'change', (ev)->
            PubSub.publish 'CHANGE_QUICK_COUNT', $(@).val()

          PubSub.subscribe 'CHANGE_QUICK_COUNT', (name, count)->
            cost = parseFloat $('.detail-info__basket-button').attr('data-cost')
            total_cost = cost * ~~count
            total_cost = numeral(total_cost).format('0,0.00')
            total_cost = total_cost.replace ',00', ''
            abb = $('#currencies LI A').first().attr 'data-short-name'
            if abb == 'P'
              abb = '<span class="rur">' + abb + '</span>'
            $('.quick-buy__total-cost').attr 'data-cost', (cost * ~~count)
            total_usd_cost = $('.quick-buy__detail-cost').attr 'data-usd-cost'
            $('.quick-buy__total-cost').attr('data-usd-cost',
              total_usd_cost * $('#quick-buy-form__count').val()
            )

            if abb == 'грн.'
              $('.quick-buy__total-cost').html(numeral(cost * ~~count).format('0,0') + '&thinsp;<small>' + abb + '</small>')
            else
              $('.quick-buy__total-cost').html(abb + total_cost)

          PubSub.subscribe 'CHANGE_PRODUCT_COUNT', (name, data)->
            $costEl = $('#product-cost-' + data.id)
            product_id = data.id.toString().concat('-', data.size, '-',
              data.color)
            # console.log settings.basket.products
            data.count = data.count - settings.basket.products[product_id].count
            data.cost = $costEl.attr 'data-cost'
            data.costUsd = $costEl.attr 'data-usd-cost'

            PubSub.publish 'CHANGE_BASKET',
              method: 'change'
              id: data.id
              count: data.count
              cost: data.cost
              costUsd: data.costUsd
              color: data.color
              size: data.size
              basket: settings.basket
        # }}} App.logic.formElements()

        currenciesSelect: ()-> # {{{ App.logic.currenciesSelect()
          that = @
          $('#currencies .currencies__link').on 'click', (ev)->
            ratio = $(@).attr 'data-ratio'
            abb = $(@).attr 'data-short-name'
            if abb == 'P'
              abb = '<span class="rur">' + abb + '</span>'

            $current = $(@).parent().detach()
            $current.prependTo '#currencies'

            settings.basket.sum = settings.basket.sumUsd / ratio
            # localStorage.setItem 'basket', JSON.stringify settings.basket
            $.cookie('basket', JSON.stringify(settings.basket), {
              expires: 365
              path: '/'
              domain: cookieDomain
            })

            $('[data-cost]').each (index, value)->
              cost = $(@).attr 'data-usd-cost'
              cost /= ratio
              $(@).attr 'data-cost', cost
              if $(@).hasClass '_print-cost'
                if abb == 'грн.'
                  cost = numeral(cost).format('0,0')
                  $(@).html(cost + '&thinsp;<small>' + abb + '</small>')
                else
                  cost = numeral(cost).format('0,0.00')
                  cost = cost.replace ',00', ''
                  $(@).html(abb + cost)

            currency_synonym = $('#currencies LI:first A').attr('href').substr 1
            $('.quick-buy__form INPUT[name=currency]').val currency_synonym

            App.logic._drawBasket()

            $.ajax
              url: '/users/set-currency/' + currency_synonym + '/'
              type: 'POST'

            ev.preventDefault()
        # }}} App.logic.currenciesSelect()

        restoreBasket: ()->
          $.ajax
            url: '/json/basket/get/'
            type: 'GET'
            # data: {products: localStorage.getItem('basket')}
            data: {products: $.cookie('basket')}
            dataType: 'json'
            success: (data)->
              if data
                data.count = ~~data.count
                data.sum = parseFloat(data.sum)
                data.sumUsd = parseFloat(data.sumUsd)

                settings.basket = data
                # localStorage.setItem 'basket', JSON.stringify data
                $.cookie('basket', JSON.stringify(data), {
                  expires: 365
                  path: '/'
                  domain: cookieDomain
                })
              else
                # basket = localStorage.getItem('basket')
                basket = $.cookie('basket')
                settings.basket = JSON.parse(basket) if basket

              App.logic._drawBasket()
              Basket.sidebar(settings.basket)

        typeaheadDetails: ()->
          details = new Bloodhound({
            datumTokenizer: (d)->
              return Bloodhound.tokenizers.whitespace(d.num + ' ' + d.nm + ' ' + d.info)
            queryTokenizer: Bloodhound.tokenizers.whitespace
            limit: 7
            remote: '/json/details/search/%QUERY/result.html'
            prefetch: '/json/details/search/presence.html'
          })

          details.initialize()
          statisServer = $('#search-query').attr 'data-static-server'
          notAvailable = $('#search-query').attr 'data-not-available'

          $('.search-element .typeahead').typeahead(null,
            autoselect: true
            displayKey: 'nm'
            source: details.ttAdapter()
            templates: {
              suggestion: (data)->
                if data.im == ''
                  data.im = '/uploads/images/noimage-sm.jpg'

                presenceHtml = ''
                if data.pr == '0'
                  presenceHtml = '<span class="typeahead__not-presence">' +
                    notAvailable + '</span>'

                return '<a class="typeahead__item" '+
                  'href="/car/' + data.carsy + '/' +
                  data.aid + '/' + data.id + '/">' +
                  '<div class="typeahead__image"><img '+
                  'src="' + statisServer +
                  data.im + '">' + presenceHtml + '</div>' +
                  '<strong class="typeahead__caption">#' +
                  data.num + '</strong>' +
                  '<p class="typeahead__text">' + data.nm + '</p>' +
                  '<span class="typeahead__car">' + data.car + '</span>' +
                  '</a>'
            }
          )
          $('.search-element .typeahead').on 'typeahead:selected', (ev, data)->
            window.location = '/car/' + data.carsy + '/' + data.aid + '/' +
              data.id + '/'

          $('.search-form').on 'submit', (ev) ->
            # elems = $('.tt-dropdown-menu .typeahead__item')
            # if elems.length != 0
            #   href= $(elems.get(0)).attr 'href'
            #   window.location = href
            if $(@).closest('FORM').find('#search-query').val() != ''
              $(@).trigger 'submit'

            ev.preventDefault()

    Public =
      # Глобальная инициализация
      init: ->
        App.init()
        App.logic.restoreBasket()
        Basket.init(settings)
        App.logic.stickyHeader()
        App.logic.virtualFormElements()
        App.logic.buildFilterHash()
        App.logic.flyToBasket()
        App.logic.formElements()
        App.logic.currenciesSelect()
        App.logic.typeaheadDetails()
        Users.initSubscriptionBar()
        Users.initAddReviewForm()
        Users.initAuthPopup()
        Users.initCabinet()
        Comments.init()

        $('.__tooltip').each ->
          $(@).powerTip
            # followMouse: true
            placement: $(@).data('placement') || 'n'
            smartPlacement: true

        $('.filter-bar__button').on 'click', (ev) ->
          state = $(@).data 'state'
          $(@).toggleClass 'filter-bar__button_state_checked'
          if state == 'hide'
            $(@).text $(@).data('show-lng')
            $(@).data 'state', 'show'
            $('.autoparts__schema').removeClass 'autoparts__schema_state_visible'
          else
            $(@).text $(@).data('hide-lng')
            $(@).data 'state', 'hide'
            $('.autoparts__schema').addClass 'autoparts__schema_state_visible'

          ev.preventDefault()

        $('.layout__header-info-bar_state_invisible')
          .removeClass 'layout__header-info-bar_state_invisible'

        PubSub.subscribe 'LOAD_PAGE_CONTENT', (name)->
          App.logic.flyToBasket()

        $('.tabs').uwinTabs()
        if $('#map-canvas').length
          UwinGoogleMap $('#map-canvas'),
            lang: $('html').attr('lang').split('-')[0]

  )(window.jQuery, window, document)

  Avtoclassika.init()
