define [
  'jquery'
  'pubsub'
], ($, PubSub) ->
  Users =
    # Инициализация
    init: ->

    # Инициализация формы подписки
    initSubscriptionBar: -> # {{{ initSubscriptionBar()
      $('INPUT').on 'keyup', (ev)->
        keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40]
        if !(ev.keyCode in keyCodes)
          $(@).nextAll('.error').css 'opacity', '0'

      $('.subscription-bar__unsubscribe-link').on 'click', (ev)->
        url = $(@).attr 'href'
        email = $(@).attr 'data-email'

        $.ajax
          url: url
          data: 'email=' + email
          type: 'post'
          success: (data)->
            $form = $ '.subscription-bar__form'
            $form.css 'opacity', 1
            $form.css 'visibility', 'visible'
            $form_wrap = $form.parent()
            $form_wrap.find('.subscription-bar__success').css 'opacity', 0

        ev.preventDefault()

      $('.subscription-bar__form').on 'submit', (ev)->
        form_name_prefix = 'subscription-bar-form__'

        $form = $ @
        $form.find('.error').css 'opacity', ''

        $submit = $form.find('button[type="submit"]')
        $submit.attr 'disabled', 'disabled'

        $.ajax
          url: $form.attr('action')
          data: $form.serialize()
          type: 'POST'
          dataType: 'json'
          success: (data)->
            ga 'send', 'event', 'Subscription', 'Subscribed'

            $submit.removeAttr 'disabled'
            $form.css 'opacity', 0
            $form.css 'visibility', 'hidden'
            $form_wrap = $form.parent()
            email = $form.find('#subscription-bar-form__email').val()

            $form_wrap.find('.subscription-bar__email-link')
              .attr('href', 'mailto:' + email)
              .text(email)
            $form_wrap.find('.subscription-bar__unsubscribe-link')
              .attr 'data-email', email
            $form_wrap.find('.subscription-bar__success').css 'opacity', 1

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

    # }}} initSubscriptionBar()

    initAddReviewForm: ()-> # {{{ initAddReviewForm()
      that = @

      $(document).on $.modal.OPEN, (ev, modal)->
        $form = $('#add-review, #add-request')
        $form.find('INPUT, SELECT').filter(':first').focus()

      $(document).on $.modal.BEFORE_OPEN, (ev, modal)->
        $('SELECT').on 'change', (ev)->
          $(@).parent().nextAll('.error').css 'opacity', '0'

        $('INPUT, TEXTAREA').on 'keyup', (ev)->
          keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40]
          if !(ev.keyCode in keyCodes)
            $(@).nextAll('.error').css 'opacity', '0'

        $('#add-review SELECT, #add-request SELECT').uniform
          selectAutoWidth: false
          selectClass: 'selector form__selector_size_big'

        $('#add-review, #add-request').on 'submit', (ev)->
          $form = $(@)
          $form.find('.error').css 'opacity', ''
          $submit = $form.find 'button[type=submit]'
          $submit.attr 'disabled', 'disabled'

          form_name_prefix = 'add-review__'
          data = $form.serialize()

          $.ajax
            type: 'POST'
            url: $form.attr 'action'
            data: data
            dataType: 'json'
            success: ()->
              $.modal.close()

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
    # }}} initAddReviewForm()

    initCabinet: ()-> # {{{
      $('.cabinet__form SELECT').uniform
        selectAutoWidth: false
        selectClass: 'selector form__selector_size_big'

      $('.cabinet__edit-link').parent().find('.hideShowPassword-toggle').addClass 'cabinet__submit_state_hidden'
      $('.cabinet__edit-link').on 'click', (ev)->
        $form = $(@).parent()
        $inputs = $form.find('INPUT, SELECT, TEXTAREA')

        if $inputs.is ':disabled'
          $inputs.removeAttr 'disabled'
          $inputs.parent().removeClass 'cabinet__form-input-wrap_type_disabled'
          $form.find('.form__submit, .hideShowPassword-toggle').removeClass 'cabinet__submit_state_hidden'
          $inputs.first().focus()
          $inputs.closest('.cabinet__form-input-wrap_type_disabled')
            .removeClass('cabinet__form-input-wrap_type_disabled')
            .addClass 'cabinet__form-input-wrap_type_enabled'
        else
          $inputs.attr 'disabled', 'disabled'
          $inputs.parent().addClass 'cabinet__form-input-wrap_type_disabled'
          $form.find('.form__submit, .hideShowPassword-toggle').addClass 'cabinet__submit_state_hidden'
          $inputs.closest('.cabinet__form-input-wrap_type_enabled')
            .removeClass('cabinet__form-input-wrap_type_enabled')
            .addClass 'cabinet__form-input-wrap_type_disabled'

        ev.preventDefault()

      $('.cabinet__form').on 'submit', (ev)->
        form_name_prefix = 'cabinet__'

        $form = $ @
        $inputs = $form.find('INPUT, SELECT, TEXTAREA')
        $form.find('.error').css 'opacity', ''

        $submit = $form.find('button[type="submit"]')
        $submit.attr 'disabled', 'disabled'

        $.ajax
          url: $form.attr('action')
          data: $form.serialize()
          type: 'POST'
          dataType: 'json'
          success: (data)->
            $submit.removeAttr 'disabled'

            $inputs.attr 'disabled', 'disabled'
            $inputs.parent().addClass 'cabinet__form-input-wrap_type_disabled'
            $form.find('.form__submit').addClass 'cabinet__submit_state_hidden'
            $inputs.closest('.cabinet__form-input-wrap_type_enabled')
              .removeClass('cabinet__form-input-wrap_type_enabled')
              .addClass 'cabinet__form-input-wrap_type_disabled'

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

    initAuthPopup: ()-> # {{{
      $(document).on 'click', '.auth-popup__social-link', (ev)->
        w = 640
        h = 480
        left = (screen.width/2)-(w/2)
        top = (screen.height/2)-(h/2)

        popup = window.open $(@).attr('href'), 'Authentication',
          'resizeable=true,' +
          'width=' + w + ', height=' + h + ', top=' + top + ', left=' + left

        popup.focus()

        timer = setInterval ->
          if popup.closed
            clearInterval timer
            location.reload()
        , 100

        ev.preventDefault()

      $('.auth-popup').removeClass 'auth-popup_state_invisible'

      $('.enter-button').on 'click', (ev)->
        $form = $ '.auth-popup'

        if $(@).parent().find('.auth-popup').length == 0
          $form.detach()
          $(@).parent().append $form

        timer = setTimeout ()->
          $form.toggleClass 'auth-popup_state_visible'
          clearTimeout timer
        , 20
        timer2 = setTimeout ()->
          $form.find('[autofocus]').focus()
          clearTimeout timer2
        , 70

        ev.preventDefault()

      $(document).on 'submit', '#user-register', (ev)->
        form_name_prefix = 'user-register__'

        $form = $ @
        $form.find('.error').css 'opacity', ''

        $submit = $form.find('button[type="submit"]')
        $submit.attr 'disabled', 'disabled'

        $.ajax
          url: $form.attr('action')
          data: $form.serialize()
          type: 'POST'
          dataType: 'json'
          success: (data)->
            $submit.removeAttr 'disabled'
            $form.css 'opacity', 0
            $form.css 'visibility', 'hidden'
            ga 'send', 'event', 'UserReg', 'registration'
            redirect = $form.attr('data-redirect') ||  '/cabinet/'
            window.location = redirect

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

      $('BODY').on 'click', (ev)->
        $form = $ '.auth-popup'

        if (
          $form.hasClass('auth-popup_state_visible') and
          !$(ev.target).hasClass("enter-button") and
          $(ev.target).closest(".auth-popup").length == 0
        )
          $form.removeClass 'auth-popup_state_visible'

      $(document).on 'submit', '#auth-form', (ev)->
        form_name_prefix = 'auth-form__'

        $form = $ @
        $form.find('.error').css 'opacity', ''

        $submit = $form.find('button[type="submit"]')
        $submit.attr 'disabled', 'disabled'

        $.ajax
          url: $form.attr('action')
          data: $form.serialize()
          type: 'POST'
          dataType: 'json'
          success: (data)->
            if typeof data.success == 'undefined'
              location.reload()
            else
              $('.auth-popup__lost-text').html data.success

            $submit.removeAttr 'disabled'

          error: (response)->
            $submit.removeAttr 'disabled'
            data = response.responseJSON
            errors = data.errors
            delete data.errors

            for i of data
              $input = $form.find('#' + form_name_prefix + data[i].id)
              $inputWrap = $input.parent()
              $errorEl = $inputWrap.find '.error'

              $errorEl.text data[i].text
              $errorEl.css 'opacity', 1

            $form.find('#' + form_name_prefix + data[0].id).focus() if data
        ev.preventDefault()

      $(document).on 'click','.auth-popup__lost-password-link',(e)->
        $form = $(@).closest 'form'
        $submit = $form.find '.form__submit'
        $el = $form.find(
          '.auth-popup__lost-password .auth-popup__lost-password-link')
        if $el.attr('data-state') == 'lost'
          $el.text $el.attr 'data-login-caption'
          $el.attr 'data-state', 'login'

          $form.find('.auth-popup__input-wrap_type_password')
            .css 'display', 'none'
          $form.find('.auth-popup__lost-text').html(
            $form.find('.auth-popup__lost-text').attr('data-text')
          )
          $form.find('.auth-popup__lost-text').css 'display', 'block'
          $submit.text $submit.attr('data-repair-caption')
          $form.attr 'action', '/users/repair/'
        else
          $el.text $el.attr 'data-lost-caption'
          $el.attr 'data-state', 'lost'

          $form.find('.auth-popup__input-wrap_type_password')
            .css 'display', 'block'
          $form.find('.auth-popup__lost-text').css 'display', 'none'
          $submit.text $submit.attr('data-login-caption')
          $form.attr 'action', '/users/auth/'

        $('#auth-form__email').focus()

        e.preventDefault()
    # }}}
