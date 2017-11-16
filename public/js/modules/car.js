(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  define(['jquery', 'pubsub', 'flexslider', 'uwinPaging', 'scrollto', 'uniform', 'swipebox', 'powertip', 'uwinTree'], function($, PubSub) {
    var Car;
    return Car = {
      _filter: {
        replica: false,
        restaurare: false,
        secondhand: false
      },
      init: function() {
        var print_selector, selector, that;
        that = this;
        this.quickBuySend();
        $('SELECT').uniform({
          selectAutoWidth: false
        });
        $('[data-paging]').uwinPaging({
          additionParams: this._filter,
          gotoTopPage: function() {
            return $.scrollTo('.layout__content.autoparts', 800, {
              offset: -65
            });
          },
          callback: function() {
            return PubSub.publish('LOAD_PAGE_CONTENT', {});
          }
        });
        PubSub.subscribe('FILTERED', function(name, data) {
          return that.filteredProducts(data);
        });
        print_selector = '.table__actions-link_type_print,' + '.detail-info__schema-actions-link_type_print';
        $(print_selector).on('click', function(ev) {
          window.print();
          return ev.preventDefault();
        });
        selector = '.car-autoparts-tree__item-plus-minus, ' + '.car-autoparts-tree__subitem-plus-minus';
        $('.car-autoparts-tree__list').on('click', selector, this.openAutopartNodeTree);
        $('.car-autoparts-tree__list').uwinTree({
          selector: '.expanded',
          class_expand_suffix: '_state_expand',
          success: function() {
            return that.openCurrentAutopartNodeTree();
          }
        });
        $('.detail-info__select').on('change', function(ev) {
          var $el, image, imageMedium;
          PubSub.publish('CHANGE_COLOR_SIZE', {
            color_id: ~~$('#color').val(),
            size_id: ~~$('#size').val()
          });
          $el = $(this).find(":selected");
          image = $el.attr('data-image');
          if (image) {
            imageMedium = $el.attr('data-image-medium');
            $('.detail-info__image-link').attr('href', image);
            return $('.detail-info__image-link IMG').attr('src', imageMedium);
          }
        });
        return PubSub.subscribe('CHANGE_COLOR_SIZE', function(name, data) {
          var $el, car_id, id, url;
          $el = $('#detail-name');
          id = ~~$el.attr('data-id');
          car_id = ~~$el.attr('data-car-id');
          url = '/json/car/' + car_id + '/detail-color-size/' + id + '/';
          return $.ajax({
            type: 'GET',
            url: url,
            data: data
          }).done(function(data) {
            var count, notFountText;
            data = $.parseJSON(data);
            $('.detail-info__cost').text(data.currency_abb + data.cost);
            $('.quick-buy__detail-cost').text(data.currency_abb + data.cost);
            count = ~~$('#quick-buy-form__count').val() || 1;
            $('.quick-buy__total-cost').text(data.currency_abb + (data.cost * count));
            $('.detail-info__cost').attr('data-cost', data.cost_unformat);
            $('.detail-info__basket-button').attr('data-cost', data.cost_unformat);
            $('.quick-buy__detail-cost').attr('data-cost', data.cost_unformat);
            $('.detail-info__cost').attr('data-usd-cost', data.cost_usd);
            $('.detail-info__basket-button').attr('data-usd-cost', data.cost_usd);
            $('.quick-buy__detail-cost').attr('data-usd-cost', data.cost_usd);
            if (~~data.count === 0) {
              notFountText = $('.detail-info__cost').attr('data-not-fount-text');
              $('.detail-info__cost').addClass('detail-info__cost_state_disabled');
              $('.detail-info__cost').text(notFountText);
              $('.detail-info__basket-button').addClass('detail-info__basket-button_state_disabled').removeClass('button-buy').css('pointer-events', 'none');
              return $('.detail-info__buy-fast').addClass('detail-info__buy-fast_state_disabled').css('pointer-events', 'none');
            } else {
              $('.detail-info__cost').removeClass('detail-info__cost_state_disabled');
              $('.detail-info__basket-button').removeClass('detail-info__basket-button_state_disabled').addClass('button-buy').css('pointer-events', '');
              return $('.detail-info__buy-fast').removeClass('detail-info__buy-fast_state_disabled').css('pointer-events', '');
            }
          });
        });
      },
      quickBuySend: function() {
        $('INPUT, SELECT, TEXTAREA').on('keyup', function(ev) {
          var keyCodes, ref;
          keyCodes = [13, 9, 16, 17, 18, 91, 37, 38, 39, 40];
          if (!(ref = ev.keyCode, indexOf.call(keyCodes, ref) >= 0)) {
            return $(this).nextAll('.error').css('opacity', '0');
          }
        });
        return $('.quick-buy__form').on('submit', function(ev) {
          var $form, $submit, data, form_name_prefix;
          form_name_prefix = 'quick-buy-form__';
          $form = $(this);
          $form.find('.error').css('opacity', '');
          $submit = $form.find('button[type="submit"]');
          $submit.attr('disabled', 'disabled');
          data = $form.serialize();
          if ($('#color').length !== 0) {
            data += '&color=' + $('#color').val() || '';
          }
          if ($('#size').length !== 0) {
            data += '&size=' + $('#size').val() || '';
          }
          $.ajax({
            url: $form.attr('action'),
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
              $('.quick-buy__success-order-num .success-num-order').text(data.order_num);
              $('.quick-buy__success-text').css('visibility', 'visible');
              return $('.quick-buy__inputs-wrap').css('visibility', 'hidden');
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
      openAutopartNodeTree: function(ev, success) {
        var $el;
        if (success == null) {
          success = null;
        }
        $el = $(ev.target).parent() || ev.parent();
        $el = $el.closest('LI');
        $el.siblings('.__item-' + $el.data('id')).toggleClass('__hidden');
        if (success) {
          return success();
        }
      },
      openCurrentAutopartNodeTree: function() {
        var $el, $pathEl, id, that;
        that = this;
        $pathEl = $('#autopart-path LI');
        if ($pathEl.length === 0) {
          return;
        }
        id = ~~$($pathEl.get(0)).text();
        $el = $('.car-autoparts-tree__item[data-id="' + id + '"]');
        that.openAutopartNodeTree($el, function() {
          var timer;
          id = ~~$($pathEl.get(1)).text();
          return timer = setInterval(function() {
            var subtimer;
            $el = $('.car-autoparts-tree__subitem[data-id="' + id + '"]');
            if ($el.length === 0) {
              return;
            }
            that.openAutopartNodeTree($el);
            $el.find('.expanded').trigger('click');
            subtimer = setInterval(function() {
              var $subel, subid;
              subid = ~~$($pathEl.get(2)).text();
              $subel = $('.car-autoparts-tree__subsubitem[data-id="' + subid + '"]');
              $subel.css('font-style', 'italic');
              if ($subel.length !== 0) {
                return clearInterval(subtimer);
              }
            }, 100);
            if ($el.length !== 0) {
              return clearInterval(timer);
            }
          }, 100);
        });
        return $el.find('.expanded').trigger('click');
      },
      initGallery: function(el) {
        var $el;
        $el = $(el);
        return $el.swipebox({
          hideBarsDelay: 0
        });
      },
      positionMiniSchema: function(el) {
        var $coordWrapEL, $currentEl, $el, $schemaEl, $schemaImgEl, left, schemaHeigth, schemaWidth, top;
        $el = $(el);
        $currentEl = $('.detail-info__schema-coord_state_current');
        $coordWrapEL = $('.detail-info__schema-coord-wrap');
        $coordWrapEL.css('display', 'block');
        $schemaEl = $('.detail-info__schema-link');
        $schemaImgEl = $('.detail-info__schema-image');
        schemaWidth = $schemaEl.outerWidth();
        schemaHeigth = $schemaEl.outerHeight();
        top = left = 0;
        if ($currentEl.length) {
          top = ($currentEl.position().top - (schemaHeigth / 2) + 14) * -1;
          left = ($currentEl.position().left - (schemaWidth / 2) + 14) * -1;
        } else {
          top = ((schemaHeigth / 2) + 14) * -1;
          left = ((schemaWidth / 2) + 14) * -1;
        }
        $coordWrapEL.css('display', 'none');
        $schemaImgEl.css('top', top);
        $schemaImgEl.css('left', left);
        $('.detail-info__schema-coord').each(function() {
          var $newEl;
          $newEl = $(this).clone().appendTo($('.detail-info__schema'));
          $newEl.css('top', '+=' + top + 'px');
          $newEl.css('left', '+=' + left + 'px');
          if (~~$newEl.position().top < 0) {
            $newEl.remove();
          }
          if (~~$newEl.position().left < 0) {
            $newEl.remove();
          }
          if (~~$newEl.position().left > schemaWidth - 24) {
            $newEl.remove();
          }
          if (~~$newEl.position().top > schemaHeigth - 24) {
            return $newEl.remove();
          }
        });
        return $el.removeClass('detail-info__schema_state_invisible');
      },
      _initSchema: function($el) {
        $el.find('.__schema-inner').on('mousedown', function(ev) {
          var X, Y, clientHeight, clientWidth, oldX, oldY, scrollLeft, scrollTop;
          clientWidth = document.body.clientWidth;
          clientHeight = document.body.clientHeight;
          scrollLeft = $(this).scrollLeft();
          scrollTop = $(this).scrollTop();
          oldX = ev.pageX - (clientWidth - scrollLeft);
          oldY = ev.pageY - (clientHeight - scrollTop);
          X = Y = 0;
          $(this).on('mousemove', function(ev) {
            var pX, pY;
            pX = ev.pageX;
            pY = ev.pageY;
            X = pX - oldX;
            Y = pY - oldY;
            console.log(X, Y);
            $(this).scrollTop(clientHeight - Y);
            $(this).scrollLeft(clientWidth - X);
            return false;
          });
          return false;
        });
        $el.find('.__schema-inner').on('mouseup', function() {
          $(this).unbind('mousemove');
          return false;
        });
        return $el.find('.__schema-inner').on('mouseout', function() {
          $(this).unbind('mousemove');
          return false;
        });
      },
      initSchemaSwipebox: function(el) {
        var $el;
        $el = $(el);
        return $el.swipebox({
          hideBarsDelay: 0,
          useInnerWrap: true,
          beforeOpen: function() {
            var timer;
            return timer = setTimeout(function() {
              $el = $('.detail-info__schema-coord-wrap ' + '.detail-info__schema-coord');
              if ($.swipebox.isOpen) {
                $el.clone().appendTo($('#swipebox-slider .slide-inner'));
                $('#swipebox-slider .slide-inner .__tooltip').each(function() {
                  return $(this).powerTip({
                    placement: $(this).data('placement') || 'n',
                    smartPlacement: true
                  });
                });
                clearTimeout(timer);
                $("#swipebox-slider .slide-inner").on('mousedown', function(ev) {
                  var X, Y, clientHeight, clientWidth, oldX, oldY, scrollLeft, scrollTop;
                  clientWidth = document.body.clientWidth;
                  clientHeight = document.body.clientHeight;
                  scrollLeft = $("#swipebox-slider .slide-inner").scrollLeft();
                  scrollTop = $("#swipebox-slider .slide-inner").scrollTop();
                  oldX = ev.pageX - (clientWidth - scrollLeft);
                  oldY = ev.pageY - (clientHeight - scrollTop);
                  X = Y = 0;
                  $("#swipebox-slider .slide-inner").on('mousemove', function(ev) {
                    var pX, pY;
                    pX = ev.pageX;
                    pY = ev.pageY;
                    X = pX - oldX;
                    Y = pY - oldY;
                    $("#swipebox-slider .slide-inner").scrollTop(clientHeight - Y);
                    $("#swipebox-slider .slide-inner").scrollLeft(clientWidth - X);
                    return false;
                  });
                  return false;
                });
                $("#swipebox-slider .slide-inner").on('mouseup', function() {
                  $("#swipebox-slider .slide-inner").unbind('mousemove');
                  return false;
                });
                return $("#swipebox-slider .slide-inner").on('mouseout', function() {
                  $("#swipebox-slider .slide-inner").unbind('mousemove');
                  return false;
                });
              }
            }, 500);
          }
        });
      },
      initSlider: function(el) {
        var $el, flexslider, index, last, length;
        $el = $(el);
        index = $(".cars-slider__list").children(".cars-slider__item_type_current").index();
        length = $(".cars-slider__list .slider__item").length;
        if (index >= 2) {
          index -= 2;
        } else {
          index = 0;
        }
        last = false;
        if ((index + 5) >= length) {
          last = true;
          index = length - 5;
        }
        flexslider = $el.flexslider({
          animation: "slide",
          directionNav: true,
          controlNav: false,
          animationLoop: false,
          itemWidth: 144,
          itemMargin: 35,
          slideshow: false,
          move: 1,
          startAt: index,
          start: function(ev) {
            var $list, diff, matrix, values;
            $list = $('.cars-slider__list');
            matrix = $list.css('-webkit-transform') || $list.css('-moz-transform') || $list.css('-ms-transform') || $list.css('-o-transform') || $list.css('transform');
            values = matrix.split('(')[1].split(')')[0].split(',');
            diff = 0;
            if (!last) {
              diff = 35 * index;
            }
            $('.cars-slider__list').css('-webkit-transform', 'translate3d(' + (~~values[4] - diff) + 'px, 0, 0)');
            return $('.cars-slider__slider').css('opacity', 1);
          }
        });
        if (flexslider.length !== 0) {
          return flexslider.data('flexslider').resize().update();
        }
      },
      filteredProducts: function(data) {
        if (data.type !== 'products') {
          return;
        }
        if (data.value === 'true') {
          data.value = true;
        } else {
          data.value = false;
        }
        return this._filter[data.name] = data.value;
      }
    };
  });

}).call(this);
