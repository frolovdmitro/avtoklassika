(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  define(['jquery', 'pubsub'], function($, PubSub) {
    var Users;
    return Users = {
      init: function() {},
      initSubscriptionBar: function() {
        $('INPUT').on('keyup', function(ev) {
          var keyCodes, ref;
          keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40];
          if (!(ref = ev.keyCode, indexOf.call(keyCodes, ref) >= 0)) {
            return $(this).nextAll('.error').css('opacity', '0');
          }
        });
        $('.subscription-bar__unsubscribe-link').on('click', function(ev) {
          var email, url;
          url = $(this).attr('href');
          email = $(this).attr('data-email');
          $.ajax({
            url: url,
            data: 'email=' + email,
            type: 'post',
            success: function(data) {
              var $form, $form_wrap;
              $form = $('.subscription-bar__form');
              $form.css('opacity', 1);
              $form.css('visibility', 'visible');
              $form_wrap = $form.parent();
              return $form_wrap.find('.subscription-bar__success').css('opacity', 0);
            }
          });
          return ev.preventDefault();
        });
        return $('.subscription-bar__form').on('submit', function(ev) {
          var $form, $submit, form_name_prefix;
          form_name_prefix = 'subscription-bar-form__';
          $form = $(this);
          $form.find('.error').css('opacity', '');
          $submit = $form.find('button[type="submit"]');
          $submit.attr('disabled', 'disabled');
          $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
              var $form_wrap, email;
              ga('send', 'event', 'Subscription', 'Subscribed');
              $submit.removeAttr('disabled');
              $form.css('opacity', 0);
              $form.css('visibility', 'hidden');
              $form_wrap = $form.parent();
              email = $form.find('#subscription-bar-form__email').val();
              $form_wrap.find('.subscription-bar__email-link').attr('href', 'mailto:' + email).text(email);
              $form_wrap.find('.subscription-bar__unsubscribe-link').attr('data-email', email);
              return $form_wrap.find('.subscription-bar__success').css('opacity', 1);
            },
            error: function(response) {
              var $errorEl, $input, $inputWrap, data, errors, i;
              $submit.removeAttr('disabled');
              data = response.responseJSON;
              errors = data.errors;
              delete data.errors;
              for (i in data) {
                $input = $('#' + form_name_prefix + data[i].id);
                $inputWrap = $input.parent();
                $errorEl = $inputWrap.find('.error');
                $errorEl.text(data[i].text);
                $errorEl.css('opacity', 1);
              }
              if (data) {
                return $('#' + form_name_prefix + data[0].id).focus();
              }
            }
          });
          return ev.preventDefault();
        });
      },
      initAddReviewForm: function() {
        var that;
        that = this;
        $(document).on($.modal.OPEN, function(ev, modal) {
          var $form;
          $form = $('#add-review, #add-request');
          return $form.find('INPUT, SELECT').filter(':first').focus();
        });
        return $(document).on($.modal.BEFORE_OPEN, function(ev, modal) {
          $('SELECT').on('change', function(ev) {
            return $(this).parent().nextAll('.error').css('opacity', '0');
          });
          $('INPUT, TEXTAREA').on('keyup', function(ev) {
            var keyCodes, ref;
            keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40];
            if (!(ref = ev.keyCode, indexOf.call(keyCodes, ref) >= 0)) {
              return $(this).nextAll('.error').css('opacity', '0');
            }
          });
          $('#add-review SELECT, #add-request SELECT').uniform({
            selectAutoWidth: false,
            selectClass: 'selector form__selector_size_big'
          });
          return $('#add-review, #add-request').on('submit', function(ev) {
            var $form, $submit, data, form_name_prefix;
            $form = $(this);
            $form.find('.error').css('opacity', '');
            $submit = $form.find('button[type=submit]');
            $submit.attr('disabled', 'disabled');
            form_name_prefix = 'add-review__';
            data = $form.serialize();
            return $.ajax({
              type: 'POST',
              url: $form.attr('action'),
              data: data,
              dataType: 'json',
              success: function() {
                return $.modal.close();
              },
              error: function(response) {
                var $errorEl, $input, $inputWrap, errors, i;
                $submit.removeAttr('disabled');
                data = response.responseJSON;
                errors = data.errors;
                delete data.errors;
                for (i in data) {
                  $input = $('#' + form_name_prefix + data[i].id);
                  $inputWrap = $input.parent();
                  if ($inputWrap.hasClass('selector')) {
                    $inputWrap = $inputWrap.parent();
                  }
                  $errorEl = $inputWrap.find('.error');
                  $errorEl.text(data[i].text);
                  $errorEl.css('opacity', 1);
                  $submit.removeAttr('disabled');
                }
                if (data) {
                  return $('#' + form_name_prefix + data[0].id).focus();
                }
              }
            }, ev.preventDefault());
          });
        });
      },
      initCabinet: function() {
        $('.cabinet__form SELECT').uniform({
          selectAutoWidth: false,
          selectClass: 'selector form__selector_size_big'
        });
        // $('.cabinet__edit-link').parent().find('.hideShowPassword-toggle').addClass('cabinet__submit_state_hidden');
        $('.cabinet__edit-link').on('click', function(ev) {
          var $form, $inputs;
          $form = $(this).parent();
          $inputs = $form.find('INPUT, SELECT, TEXTAREA');
          if ($inputs.is(':disabled')) {
            $inputs.removeAttr('disabled');
            $inputs.parent().removeClass('cabinet__form-input-wrap_type_disabled');
            $form.find('.form__submit, .hideShowPassword-toggle').removeClass('cabinet__submit_state_hidden');
            $inputs.first().focus();
            $inputs.closest('.cabinet__form-input-wrap_type_disabled').removeClass('cabinet__form-input-wrap_type_disabled').addClass('cabinet__form-input-wrap_type_enabled');
          } else {
            $inputs.attr('disabled', 'disabled');
            $inputs.parent().addClass('cabinet__form-input-wrap_type_disabled');
            $form.find('.form__submit, .hideShowPassword-toggle').addClass('cabinet__submit_state_hidden');
            $inputs.closest('.cabinet__form-input-wrap_type_enabled').removeClass('cabinet__form-input-wrap_type_enabled').addClass('cabinet__form-input-wrap_type_disabled');
          }
          return ev.preventDefault();
        });
        return $('.cabinet__form').on('submit', function(ev) {
          var $form, $inputs, $submit, form_name_prefix;
          form_name_prefix = 'cabinet__';
          $form = $(this);
          $inputs = $form.find('INPUT, SELECT, TEXTAREA');
          $form.find('.error').css('opacity', '');
          $submit = $form.find('button[type="submit"]');
          $submit.attr('disabled', 'disabled');
          $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
              $submit.removeAttr('disabled');
              $inputs.attr('disabled', 'disabled');
              $inputs.parent().addClass('cabinet__form-input-wrap_type_disabled');
              $form.find('.form__submit').addClass('cabinet__submit_state_hidden');
              return $inputs.closest('.cabinet__form-input-wrap_type_enabled').removeClass('cabinet__form-input-wrap_type_enabled').addClass('cabinet__form-input-wrap_type_disabled');
            },
            error: function(response) {
              var $errorEl, $input, $inputWrap, data, errors, i;
              $submit.removeAttr('disabled');
              data = response.responseJSON;
              errors = data.errors;
              delete data.errors;
              for (i in data) {
                $input = $('#' + form_name_prefix + data[i].id);
                $inputWrap = $input.parent();
                $errorEl = $inputWrap.find('.error');
                $errorEl.text(data[i].text);
                $errorEl.css('opacity', 1);
              }
              if (data) {
                return $('#' + form_name_prefix + data[0].id).focus();
              }
            }
          });
          return ev.preventDefault();
        });
      },
      initAuthPopup: function() {
        $(document).on('click', '.auth-popup__social-link', function(ev) {
          var h, left, popup, timer, top, w;
          w = 640;
          h = 480;
          left = (screen.width / 2) - (w / 2);
          top = (screen.height / 2) - (h / 2);
          popup = window.open($(this).attr('href'), 'Authentication', 'resizeable=true,' + 'width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
          popup.focus();
          timer = setInterval(function() {
            if (popup.closed) {
              clearInterval(timer);
              return location.reload();
            }
          }, 100);
          return ev.preventDefault();
        });
        $('.auth-popup').removeClass('auth-popup_state_invisible');
        $('.enter-button').on('click', function(ev) {
          var $form, timer, timer2;
          $form = $('.auth-popup');
          if ($(this).parent().find('.auth-popup').length === 0) {
            $form.detach();
            $(this).parent().append($form);
          }
          timer = setTimeout(function() {
            $form.toggleClass('auth-popup_state_visible');
            return clearTimeout(timer);
          }, 20);
          timer2 = setTimeout(function() {
            $form.find('[autofocus]').focus();
            return clearTimeout(timer2);
          }, 70);
          return ev.preventDefault();
        });
        $(document).on('submit', '#user-register', function(ev) {
          var $form, $submit, form_name_prefix;
          form_name_prefix = 'user-register__';
          $form = $(this);
          $form.find('.error').css('opacity', '');
          $submit = $form.find('button[type="submit"]');
          $submit.attr('disabled', 'disabled');
          $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
              var redirect;
              $submit.removeAttr('disabled');
              $form.css('opacity', 0);
              $form.css('visibility', 'hidden');
              ga('send', 'event', 'UserReg', 'registration');
              redirect = $form.attr('data-redirect') || '/cabinet/';
              return window.location = redirect;
            },
            error: function(response) {
              var $errorEl, $input, $inputWrap, data, errors, i;
              $submit.removeAttr('disabled');
              data = response.responseJSON;
              errors = data.errors;
              delete data.errors;
              for (i in data) {
                $input = $('#' + form_name_prefix + data[i].id);
                $inputWrap = $input.parent();
                $errorEl = $inputWrap.find('.error');
                $errorEl.text(data[i].text);
                $errorEl.css('opacity', 1);
              }
              if (data) {
                return $('#' + form_name_prefix + data[0].id).focus();
              }
            }
          });
          return ev.preventDefault();
        });
        $('BODY').on('click', function(ev) {
          var $form;
          $form = $('.auth-popup');
          if ($form.hasClass('auth-popup_state_visible') && !$(ev.target).hasClass("enter-button") && $(ev.target).closest(".auth-popup").length === 0) {
            return $form.removeClass('auth-popup_state_visible');
          }
        });
        $(document).on('submit', '#auth-form', function(ev) {
          var $form, $submit, form_name_prefix;
          form_name_prefix = 'auth-form__';
          $form = $(this);
          $form.find('.error').css('opacity', '');
          $submit = $form.find('button[type="submit"]');
          $submit.attr('disabled', 'disabled');
          $.ajax({
            url: $form.attr('action'),
            data: $form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(data) {
              if (typeof data.success === 'undefined') {
                location.reload();
              } else {
                $('.auth-popup__lost-text').html(data.success);
              }
              return $submit.removeAttr('disabled');
            },
            error: function(response) {
              var $errorEl, $input, $inputWrap, data, errors, i;
              $submit.removeAttr('disabled');
              data = response.responseJSON;
              errors = data.errors;
              delete data.errors;
              for (i in data) {
                $input = $form.find('#' + form_name_prefix + data[i].id);
                $inputWrap = $input.parent();
                $errorEl = $inputWrap.find('.error');
                $errorEl.text(data[i].text);
                $errorEl.css('opacity', 1);
              }
              if (data) {
                return $form.find('#' + form_name_prefix + data[0].id).focus();
              }
            }
          });
          return ev.preventDefault();
        });
        return $(document).on('click', '.auth-popup__lost-password-link', function(e) {
          var $el, $form, $submit;
          $form = $(this).closest('form');
          $submit = $form.find('.form__submit');
          $el = $form.find('.auth-popup__lost-password .auth-popup__lost-password-link');
          if ($el.attr('data-state') === 'lost') {
            $el.text($el.attr('data-login-caption'));
            $el.attr('data-state', 'login');
            $form.find('.auth-popup__input-wrap_type_password').css('display', 'none');
            $form.find('.auth-popup__lost-text').html($form.find('.auth-popup__lost-text').attr('data-text'));
            $form.find('.auth-popup__lost-text').css('display', 'block');
            $submit.text($submit.attr('data-repair-caption'));
            $form.attr('action', '/users/repair/');
          } else {
            $el.text($el.attr('data-lost-caption'));
            $el.attr('data-state', 'lost');
            $form.find('.auth-popup__input-wrap_type_password').css('display', 'block');
            $form.find('.auth-popup__lost-text').css('display', 'none');
            $submit.text($submit.attr('data-login-caption'));
            $form.attr('action', '/users/auth/');
          }
          $('#auth-form__email').focus();
          return e.preventDefault();
        });
      }
    };
  });

}).call(this);
