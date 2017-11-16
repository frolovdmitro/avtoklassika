define [
  'jquery'
  'pubsub'

  'scrollto'
], ($, PubSub) ->
  Basket =
    # Инициализация
    init: (settings)->
      that = @
      @promocode(settings)
      @continueOrder()

      $(document).on 'click', '.cabinet__edit-link', (ev)->
        $('#user-data__form').toggleClass 'form_state_hidden'
        $('.basket-user-info__block').toggleClass 'form_state_hidden'

        ev.preventDefault()

      PubSub.subscribe 'CHANGE_BASKET', (name, data)->
        dataBasket =
          method: data.method
          id: data.id
          count: data.count
          cost: data.cost
          costUsd: data.costUsd
          color: data.color
          size: data.size
          products: data.basket.products

        total_count = $('#basket-products__count-' + data.id).val()
        # console.log total_count
        $('#product-sum-' + data.id).attr('data-usd-cost',
          ~~data.costUsd * total_count)
        $('#product-sum-' + data.id).attr('data-cost', ~~data.cost * total_count)
        abb = $('#currencies LI A').first().attr 'data-short-name'
        if abb == 'P'
          abb = '<span class="rur">' + abb + '</span>'
        $('.basket-bar__currency').html abb

        if abb == 'грн.'
          $('#product-sum-' + data.id).html((~~data.cost * total_count) + '&thinsp;<small>' + abb + '</small>')
        else
          $('#product-sum-' + data.id).text abb + (~~data.cost * total_count)

        $.ajax
          url: '/json/basket/change/'
          data: dataBasket
          type: 'POST'
          dataType: 'json'
          success: ()->
            if data.oper && data.oper == 'add'
              if $('.basket-products__list').length != 0
                that._loadBasketPage()

    _loadBasketPage: (page)->
      page = page || 1
      $.ajax
        url: '/json/basket/step/' + page + '/'
        type: 'GET'
        success: (content)->
          $contentEl = $ '#basket-page-content'

          prevPage = $contentEl.find('#basket-page').attr 'data-num'
          if typeof prevPage != 'undefined'
            prevPage = prevPage
          originalPage = $(content).attr('data-num') || 1
          location.hash = 'step=' + originalPage

          $('.basket-steps__item').removeClass 'basket-steps__item_state_active'
          $('.basket-steps__link_step_' + page).parent()
            .addClass('basket-steps__item_state_active')

          $contentEl.html content

          $('.tabs').uwinTabs()
          $('.auth__form SELECT').uniform
            selectAutoWidth: false
            selectClass: 'selector form__selector_size_big'
          PubSub.publish 'LOAD_PAGE_CONTENT', {}

          $('SELECT').on 'change', (ev)->
            $(@).parent().nextAll('.error').css 'opacity', '0'

          $('INPUT, TEXTAREA').on 'keyup', (ev)->
            keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40]
            if !(ev.keyCode in keyCodes)
              $(@).nextAll('.error').css 'opacity', '0'

          $('.basket-steps__add-comment').on 'click', (ev)->
            $('.basket-steps__comment-textarea')
              .toggleClass 'basket-steps__comment-textarea_type_visible'

            ev.preventDefault()

          $(document).on 'change',
            '.payments-deliveries__radio', (ev)->
              $form = $('#form-page-2')
              $submit = $form.find('.form__submit')
              payment = $form.find('INPUT[name="payment"]:checked').length
              delivery = $form.find('INPUT[name="delivery"]:checked').length

              if payment == 1 && delivery == 1
                $submit.removeAttr 'disabled'
              else
                $submit.attr 'disabled', 'disabled'

              cost = $(@).attr 'data-cost'

              if typeof cost == 'undefined'
                return

              $el = $('.payments-deliveries__delivery-cost-value')

              $el.attr 'data-cost', cost
              $el.attr 'data-usd-cost', $(@).attr 'data-usd-cost'
              if ~~cost == 0
                sum = 0
              else
                sum = numeral(cost).format('0,0.00')
                sum = sum.replace ',00', ''
              abb = $('#currencies LI A').first().attr 'data-short-name'
              if abb == 'P'
                abb = '<span class="rur">' + abb + '</span>'
              if abb == 'грн.'
                $el.html(sum + '&thinsp;<small>' + abb + '</small>')
              else
                $el.html abb + sum

          $('#user-data__form').on 'submit', (ev)->
            $form = $(@)
            $form.find('.error').css 'opacity', ''
            $submit = $form.find 'button[type=submit]'
            $submit.attr 'disabled', 'disabled'

            form_name_prefix = 'user-info__'
            data = $form.serialize()

            $.ajax
              type: 'POST'
              url: $form.attr 'action'
              data: data
              dataType: 'json'
              success: (data)->
                $('#basket-user-info__surname').text data.surname
                $('#basket-user-info__secondname').text data.secondname
                $('.basket-user-info__item_type_phone').text data.phone
                $('#basket-user-info__street').text data.street
                $('#basket-user-info__build').text data.build
                $('#basket-user-info__flat').text data.flat
                $('#basket-user-info__city').text data.city
                $('#basket-user-info__index').text data.index
                country = $('#user-info__country OPTION:selected').text()
                $('#basket-user-info__country').text country
                $('#user-data__form').toggleClass 'form_state_hidden'
                $('.basket-user-info__block').toggleClass 'form_state_hidden'
                $('#payments-deliveries-wrap').html data.html
                $('.payments-deliveries__delivery-cost-value')
                  .attr('data-cost', 0)
                $('.payments-deliveries__delivery-cost-value')
                  .attr('data-usd-cost', 0)
                abb = $('#currencies LI A').first().attr 'data-short-name'
                if abb == 'P'
                  abb = '<span class="rur">' + abb + '</span>'
                $('.payments-deliveries__delivery-cost-value').html abb + '0'
                $submit.removeAttr 'disabled'

              error: (response)->
                $submit.removeAttr 'disabled'
                data = response.responseJSON
                errors = data.errors
                delete data.errors

                for i of data
                  $input = $('#' + form_name_prefix + data[i].id)
                  $inputWrap = $input.parent()
                  if $inputWrap.hasClass 'selector'
                    $inputWrap = $inputWrap.parent()
                  $errorEl = $inputWrap.find '.error'

                  $errorEl.text data[i].text
                  $errorEl.css 'opacity', 1
                  $submit.removeAttr 'disabled'

                $('#' + form_name_prefix + data[0].id).focus() if data

              ev.preventDefault()

          $('#user-info__form').on 'submit', (ev)->
            $form = $(@)
            $form.find('.error').css 'opacity', ''
            $submit = $form.find 'button[type=submit]'
            $submit.attr 'disabled', 'disabled'

            form_name_prefix = 'user-info__'
            data = $form.serialize()

            $.ajax
              type: 'POST'
              url: $form.attr 'action'
              data: data
              dataType: 'json'
              success: (data)->
                location.hash = 'step=' + 1

              error: (response)->
                $submit.removeAttr 'disabled'
                data = response.responseJSON
                errors = data.errors
                delete data.errors

                for i of data
                  $input = $('#' + form_name_prefix + data[i].id)
                  $inputWrap = $input.parent()
                  if $inputWrap.hasClass 'selector'
                    $inputWrap = $inputWrap.parent()
                  $errorEl = $inputWrap.find '.error'

                  $errorEl.text data[i].text
                  $errorEl.css 'opacity', 1
                  $submit.removeAttr 'disabled'

                $('#' + form_name_prefix + data[0].id).focus() if data

              ev.preventDefault()

          $('#form-page-2').on 'submit', (ev)->
            $form = $(@)
            $submit = $form.find 'button[type=submit]'
            $submit.attr 'disabled', 'disabled'

            data = $form.serialize()

            $.ajax
              type: 'POST'
              url: $form.attr 'action'
              data: data
              dataType: 'json'
              success: (data)->
                ga('require', 'ecommerce', 'ecommerce.js')
                lang = $('HTML').attr('lang').split('-')[0]

                ga('ecommerce:addTransaction', {
                  id: data.num
                  currency: 'USD'
                  affiliation: lang + '.avtoclassika.com'
                  revenue: ~~$('.basket-products__total-cost')
                    .attr('data-usd-cost') +
                    ~~$('.payments-deliveries__delivery-cost-value')
                    .attr('data-usd-cost')
                  shipping: $('.payments-deliveries__delivery-cost-value')
                    .attr('data-usd-cost')
                })

                $('#basket-sidebar__list LI').each ->
                  ga('ecommerce:addItem', {
                    id: data.num
                    name: $(@).find('.basket-sidebar__item-name').text()
                    category: $(@).attr 'data-car'
                    price: $(@).find('.basket-sidebar__item-cost')
                      .attr 'data-usd-cost'
                    quantity: $(@).find('.basket-sidebar__item-count').text()
                      .split(' ')[0]
                  })

                ga('ecommerce:send')
                # localStorage.removeItem 'basket'
                $.removeCookie('basket', {
                  path: '/'
                  domain: '.' + $('META[property="uwin:serverName"]')
                    .attr('content')
                })

                if data.state == 'finish'
                  window.location = data.redirect
                if data.state == 'liqpay' or data.state == 'portmone'
                  $('<div>' + data.form + '</div>').find('FORM').submit()

              error: (response)->
                $submit.removeAttr 'disabled'

              ev.preventDefault()

    sidebar: (basket)->
      $(document).on 'click',
        '.basket-sidebar__item-delete, .basket-products__delete',
        (ev)->
          $el = $(@)
          $el.addClass '__disabled'
          id = $el.attr('data-id')
          $item = $el.closest '.basket-sidebar__item, .basket-products__item'
          $.ajax
            url: '/json/basket/delete-item/' + id + '/'
            type: 'POST'
            dataType: 'json'
            success: (data)->
              $el.removeClass '__disabled'
              $item.remove()

              delete basket.products[id]
              PubSub.publish 'CHANGE_BASKET',
                method: 'delete'
                type: -1
                id: data.id
                count: data.count
                cost: data.cost
                costUsd: data.costUsd
                color: data.color
                size: data.size
                basket: basket
              # localStorage.setItem 'basket', JSON.stringify basket

          ev.preventDefault()


    # Показываем форму быстрой покупки
    quickBuyForm: ->
      $('.detail-info__buy-fast').on 'click', (ev)->
        $form = $ '#quick-buy-form'
        $form.css 'top', $(@).position().top + $(@).height() + 15
        if !$form.hasClass 'quick-buy_state_visible'
          $.scrollTo '.detail-info__buy-fast', 500, {offset: -100}

        $form.toggleClass 'quick-buy_state_visible'
        timer = setTimeout ()->
          $form.find('[autofocus]').focus()

          clearTimeout timer
        , 50

        ev.preventDefault()

      $('BODY').on 'click', (ev)->
        $form = $('#quick-buy-form')

        if (
          $form.hasClass('quick-buy_state_visible') and
          !$(ev.target).hasClass("detail-info__buy-fast") and
          $(ev.target).closest("#quick-buy-form").length == 0
        )
          $form.removeClass 'quick-buy_state_visible'

    initSteps: ->
      that = @
      $(window).bind 'hashchange', (ev)->
        hash = location.hash.substr 1
        page = hash.substr hash.indexOf('=')+1
        that._loadBasketPage page

      $(window).trigger 'hashchange'

    promocode: (settings)->
      $(document).on 'submit', '#basket-promocode-wrap', (ev)->
        $form = $(@)
        $form.find('.error').css 'opacity', ''
        $submit = $form.find 'button[type=submit]'
        $submit.attr 'disabled', 'disabled'
        data = $form.serialize()

        $.ajax
          url: $form.attr 'action'
          data: data
          dataType: 'json'
          type: 'POST'
          success: (data)->
            $form.find('.form__input, .form__button_type_withinput, ' +
              '.form__input-error').css 'display', 'none'

            settings.basket.promocode = data.value
            PubSub.publish 'DRAW_BASKET',

          error: (response)->
            $submit.removeAttr 'disabled'
            data = response.responseJSON
            errors = data.errors
            delete data.errors

            $input = $('#promocode')
            $inputWrap = $input.parent()
            if $inputWrap.hasClass 'selector'
              $inputWrap = $inputWrap.parent()
            $errorEl = $inputWrap.find '.error'

            $errorEl.text data[0].text
            $errorEl.css 'opacity', 1
            $submit.removeAttr 'disabled'


        ev.preventDefault()

    continueOrder: ()->
      $('.payments-deliveries__radio').on 'change', (ev)->
        $form = $('#form-order-continue')
        $submit = $form.find('.form__submit')
        payment = $form.find('INPUT[name="payment"]:checked').length

        if payment == 1
          $submit.removeAttr 'disabled'
        else
          $submit.attr 'disabled', 'disabled'

      $('#form-order-continue').on 'submit', (ev)->
        $form = $(@)
        $submit = $form.find 'button[type=submit]'
        $submit.attr 'disabled', 'disabled'

        data = $form.serialize()

        $.ajax
          type: 'POST'
          url: $form.attr 'action'
          data: data
          dataType: 'json'
          success: (data)->
            if data.state == 'finish'
              window.location = data.redirect
            if data.state == 'liqpay' or data.state == 'portmone'
              $('<div>' + data.form + '</div>').find('FORM').submit()

          error: (response)->
            $submit.removeAttr 'disabled'

          ev.preventDefault()
