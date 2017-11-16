define [
  'jquery'
  'pubsub'

  'yepnope'
  'iframe_transport'
  'fileupload'
  'flexslider'
], ($, PubSub) ->
  Ads =
    uploadedFiles: []

    # Инициализация
    init: ->
      that = @

      # Подключаю пейджинг на страницу
      $('[data-paging]').uwinPaging
        gotoTopPage: ->
          $.scrollTo '.layout__content.news', 800, {offset: -65}
        callback: ()->
          PubSub.publish 'LOAD_PAGE_CONTENT', {}
      @initAddForm()

    # Инициализация формы добавления объявления
    initAddForm: ()->
      that = @
      $(document).on $.modal.OPEN, (ev, modal)->
        $form = $('#add-ads')
        $form.find('INPUT, SELECT').filter(':first').focus()

      $(document).on $.modal.BEFORE_OPEN, (ev, modal)->
        $('SELECT').on 'change', (ev)->
          $(@).parent().nextAll('.error').css 'opacity', '0'

        $('INPUT, TEXTAREA').on 'keyup', (ev)->
          keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40]
          if !(ev.keyCode in keyCodes)
            $(@).nextAll('.error').css 'opacity', '0'

        $('#add-ads').on 'submit', (ev)->
          $form = $(@)
          $form.find('.error').css 'opacity', ''
          $submit = $form.find 'button[type=submit]'
          $submit.attr 'disabled', 'disabled'

          form_name_prefix = 'add-ads__'
          data = $form.serialize()

          for file in that.uploadedFiles
            data += '&photo[]=' + file

          $.ajax
            type: 'POST'
            url: $form.attr 'action'
            data: data
            dataType: 'json'
            success: (data)->
              that.uploadedFiles = []
              $.modal.close()
              if $('[data-paging]').length != 0
                $('[data-paging]').uwinPaging().data('plugin_uwinPaging')
                  .getPage()
              else
                window.location = '/ads/'

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

        $('#add-ads SELECT').uniform
          selectAutoWidth: false
          selectClass: 'selector form__selector_size_big'

        uploadedFiles = 0
        $('#fileupload').fileupload
          dataType: 'json'
          send: (ev, data)->
            if data.files[0].size > (1024 *1024 * 2)
              $errorEl = $('.form__fileinput-error')
              $errorEl.text $(ev.target).attr 'data-error-filesize'
              $errorEl.css 'opacity', '1'

              timer = setInterval ->
                $errorEl.css 'opacity', '0'
                clearInterval(timer)
              , 5000

              return false

            if uploadedFiles >= 10
              $errorEl = $('.form__fileinput-error')
              $errorEl.text $(ev.target).attr 'data-error-maxfiles'
              $errorEl.css 'opacity', '1'

              timer = setInterval ->
                $errorEl.css 'opacity', '0'
                clearInterval(timer)
              , 5000

              return false

            uploadedFiles++

          progressall: (e, data)->
            progress = parseInt(data.loaded / data.total * 100, 10)
            $('.form__submit').attr 'disabled', 'disabled'
            $('#upload-progress').css 'width', (progress + '%')
            if progress == 100
              $('.form__submit').removeAttr 'disabled'

          error: ()->
            $('#upload-progress').css 'width', 0

          done: (ev, data)->
            filesNames = $('.form__fileinput-files').html()
            filesNames = filesNames + ',&nbsp;&nbsp; ' + data.files[0].name
            if ',&nbsp;&nbsp; ' == filesNames.substr(0, 14)
              filesNames = filesNames.substr 14
            $('.form__fileinput-files').html filesNames
            that.uploadedFiles.push data.result.file

            $('#upload-progress').css 'width', 0

    # Инициализация галереи
    initGallery: (el)->
      $el = $(el)

      $el.swipebox
        hideBarsDelay: 0

    # Инициализация слайдера с горячими предложениями на главной
    initBarSlider: (el)->
      $el = $(el)

      $el.flexslider
        animation: "slide"
        directionNav: true
        controlNav: false
        animationLoop: false
        itemWidth: 184
        slideshow: false

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
        keyboard: false
        controlsContainer: '#other-adverts__nav'
