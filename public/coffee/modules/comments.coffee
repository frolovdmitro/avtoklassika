define [
  'jquery'
], ($) ->
  Comments =
    form_selector: '#add-comment-wrap'
    reply_selector: '.comments__actions-reply'
    reply_form_selector: '.comments__reply-wrap'

    $form: null

    # Инициализация
    init: ->
      that = @

      form_html = $(@form_selector).html()

      $('.comments').on 'click', @reply_selector, (ev)->
        $comment_item = $(@).closest('.comments__item')
        id = ~~$comment_item.attr('data-id')
        level = ~~$comment_item.attr('data-level') + 1
        $reply_wrap =  $(@).closest('.comments__item')
          .find(that.reply_form_selector)

        if $reply_wrap.html() == ''
          $reply_wrap.html form_html
          $reply_wrap.find('#add-comment').attr 'id', ('add-comment' + id)
          $reply_wrap.find('#add-comment__name').attr 'id',
            ('add-comment__name' +id)
          $reply_wrap.find('#add-comment__email').attr 'id',
            ('add-comment__email' + id)
          $reply_wrap.find('#add-comment__text').attr 'id',
            ('add-comment__text' + id)
          $reply_wrap.find('[name=parent_id]').val id
          $reply_wrap.find('[name=level]').val level

          $reply_wrap.find('FORM').on 'submit', (ev)->
            that.add(id, ev)
            ev.preventDefault()

        $reply_wrap.toggleClass 'comments__reply-wrap_state_show'
        if $reply_wrap.hasClass 'comments__reply-wrap_state_show'
          $reply_wrap.find('INPUT, SELECT, TEXTAREA').first().focus()

        ev.preventDefault()

      $(@form_selector).find('FORM').on 'submit', (ev)->
        that.add('', ev)
        ev.preventDefault()

    add: (id, ev)->
      $form = $(ev.target)
      $form.find('.error').css 'opacity', ''
      $submit = $form.find 'button[type=submit]'
      $submit.attr 'disabled', 'disabled'

      form_name_prefix = 'add-comment__'
      data = $form.serialize()

      $.ajax
        type: 'POST'
        url: $form.attr 'action'
        data: data
        dataType: 'json'
        success: (data)->
          if $form.parent().attr('id') == 'add-comment-wrap'
            $('.comments').append data.html
          else
            # console.log $form.closest('.comments__item ')
            # console.log $form.closest('.comments__item ').attr 'class'
            $(data.html).insertAfter $form.closest('.comments__item ')
            $form.closest('.comments__reply-wrap_state_show')
              .removeClass 'comments__reply-wrap_state_show'

          $form.find('INPUT, TEXTAREA').val ''
          $submit.removeAttr 'disabled'

        error: (response)->
          $submit.removeAttr 'disabled'
          data = response.responseJSON
          errors = data.errors
          delete data.errors

          for i of data
            $input = $('#' + form_name_prefix + data[i].id + id)
            $inputWrap = $input.parent()
            if $inputWrap.hasClass 'selector'
              $inputWrap = $inputWrap.parent()
            $errorEl = $inputWrap.find '.error'

            $errorEl.text data[i].text
            $errorEl.css 'opacity', 1
            $submit.removeAttr 'disabled'

          $('#' + form_name_prefix + data[0].id + id).focus() if data
