(function() {
  define(['jquery', 'pubsub', 'sparky', 'Deals', 'Ads', 'Slider', 'Car', 'Basket', 'News', 'Users', 'Comments', 'UwinGoogleMap', 'uwinTabs', 'json2', 'cookie', 'hashchange', 'mixitup', 'bezier', 'numeral', 'typeahead', 'passwordInput', 'socialshare', 'placeholder', 'powertip', 'modal'], function($, PubSub, sparky, Deals, Ads, Slider, Car, Basket, News, Users, Comments, UwinGoogleMap) {
    var Avtoclassika;
    Avtoclassika = Avtoclassika || (function($, w, d) {
      var App, Public, Routes, cache, cookieDomain, settings;
      Routes = {};
      App = {};
      Public = {};
      cookieDomain = '.' + $('META[property="uwin:serverName"]').attr('content');
      settings = {
        basket: {
          sum: 0,
          sumUsd: 0,
          count: 0,
          products: {},
          promocode: 0
        },
        meta: {},
        debug: false
      };
      cache = {};
      Routes = {
        index: function() {
          var filter;
          Slider.initSlider('#important-panel__slider');
          Deals.initSlider('#hot-products__slider');
          Ads.initBarSlider('#message-bar__slider');
          Ads.initAddForm();
          if (location.hash.substr(1) === '_=_') {
            location.hash = '';
          }
          filter = location.hash.substr(1).split('=')[0] || 'all';
          $('#catalog-autoparts__list-wrap').mixitup({
            transitionSpeed: 500,
            showOnLoad: filter
          });
          return $(window).bind('hashchange', function(e) {
            if (location.hash === '') {
              return $('#catalog-autoparts__list-wrap').mixitup('filter', 'all');
            } else {
              return $('#catalog-autoparts__list-wrap').mixitup('filter', location.hash.substr(1).split('=')[0]);
            }
          });
        },
        basket: function() {
          return Basket.initSteps();
        },
        car: function() {
          Car.init();
          Car.initSlider('#cars-slider__slider');
          Car.initGallery('.detail-info__thumbnail-link, ' + 'A.detail-info__image-link');
          Car.initSchemaSwipebox('.detail-info__schema-link');
          Car._initSchema($('.autoparts__schema'));
          Car.positionMiniSchema('.detail-info__schema');
          Deals.initSlider('#hot-products__slider');
          return Basket.quickBuyForm();
        },
        news: function() {
          return News.init();
        },
        ads: function() {
          Ads.init();
          Ads.initGallery('.ad__thumbnail-link, .ad__image');
          return Ads.initSlider('#other-adverts__slider');
        },
        oAuth: function() {
          return window.close();
        }
      };
      App = {
        init: function() {
          sparky.init(settings);
          sparky.bindEvents();
          sparky.route(Routes);
          numeral.language('ru', {
            delimiters: {
              thousands: ' ',
              decimal: ','
            }
          });
          numeral.language('ru');
          $('.form__input-password').hideShowPassword({
            innerToggle: true,
            states: {
              shown: {
                toggle: {
                  content: $('.form__input-password').data('hide-txt')
                }
              },
              hidden: {
                toggle: {
                  content: $('.form__input-password').data('show-txt')
                }
              }
            }
          });
          $.modal.defaults = {
            overlay: "#fff",
            opacity: 0.75,
            zIndex: 10000,
            escapeClose: true,
            clickClose: true,
            closeText: '',
            closeClass: '',
            showClose: false,
            modalClass: "modal",
            fadeDelay: 0,
            fadeDuration: 100
          };
          $(document).on($.modal.BEFORE_OPEN, function(ev, modal) {
            if ($('#fileupload-single').length === 0) {
              return;
            }
            return $('#fileupload-single').fileupload({
              dataType: 'json',
              send: function(ev, data) {
                var $errorEl, timer;
                if (data.files[0].size > (1024 * 1024 * 1)) {
                  $errorEl = $('.form__fileinput-error');
                  $errorEl.text($(ev.target).attr('data-error-filesize'));
                  $errorEl.css('opacity', '1');
                  timer = setInterval(function() {
                    $errorEl.css('opacity', '0');
                    return clearInterval(timer);
                  }, 5000);
                  return false;
                }
              },
              progressall: function(e, data) {
                var progress;
                progress = parseInt(data.loaded / data.total * 100, 10);
                $('.form__submit').attr('disabled', 'disabled');
                $('#upload-progress').css('width', progress + '%');
                if (progress === 100) {
                  return $('.form__submit').removeAttr('disabled');
                }
              },
              error: function() {
                return $('#upload-progress').css('width', 0);
              },
              done: function(ev, data) {
                var filesNames;
                filesNames = data.files[0].name;
                $('.form__fileinput-files').html(filesNames);
                $('#upload-progress').css('width', 0);
                return $('#add-request #uploaded-file').val(data.result.file);
              }
            });
          });
          $('.share-fb').socialSharePrivacy();
          if ($('#vk_like').length > 0) {
            VK.init({
              apiId: 4241621,
              onlyWidgets: true
            });
            VK.Widgets.Like("vk_like", {
              type: "button",
              height: 20
            });
          }
          PubSub.subscribe('REBIND', function() {
            return sparky.bindEvents();
          });
          PubSub.subscribe('CHANGE_BASKET', function(name, data) {
            return App.logic.changeBasket(data);
          });
          return PubSub.subscribe('DRAW_BASKET', function(name) {
            return App.logic._drawBasket();
          });
        },
        logic: {
          _drawBasket: function() {
            var $countEl, $sidebarCostEl, $sumDecimalEl, $sumIntEl, abb, basket, sum, sumUnformat, sumUsdUnformat;
            basket = settings.basket;
            $countEl = $('.basket-bar__notifier');
            $countEl.addClass('pulse');
            $countEl.text(basket.count);
            if (~~basket.count > 0) {
              $('.basket-bar').removeClass('basket-bar_state_disabled');
            } else {
              $('.basket-bar').addClass('basket-bar_state_disabled');
            }
            $sumIntEl = $('.basket-bar__sum_type_int');
            $sumDecimalEl = $('.basket-bar__sum_type_decimal');
            sumUnformat = basket.sum - (basket.sum / 100 * (basket.promocode || 0));
            sumUsdUnformat = basket.sumUsd - (basket.sumUsd / 100 * (basket.promocode || 0));
            sum = numeral(sumUnformat).format('0,0.0').split(',');
            $sumIntEl.text(sum[0]);
            $sumDecimalEl.text(sum[1]);
            $('.basket-bar__sum').attr('data-cost', sumUnformat);
            $('.basket-bar__sum').attr('data-usd-cost', sumUsdUnformat);
            sum = numeral(sumUnformat).format('0,0.00');
            sum = sum.replace(',00', '');
            $sidebarCostEl = $('.basket-products__total-cost');
            $sidebarCostEl.attr('data-cost', sumUnformat);
            $sidebarCostEl.attr('data-usd-cost', sumUsdUnformat);
            abb = $('#currencies LI A').first().attr('data-short-name');
            if (abb === 'P') {
              abb = '<span class="rur">' + abb + '</span>';
            }
            if (abb === 'грн.') {
              $sidebarCostEl.html(numeral(sumUnformat).format('0,0') + '&thinsp;<small>' + abb + '</small>');
            } else {
              $sidebarCostEl.html(abb + sum);
            }
            $('.basket-bar__currency').html(abb);
            if (abb === 'грн.') {
              $('HEADER .layout__header-info-bar .basket-bar__currency').detach().insertAfter('HEADER .layout__header-info-bar .basket-bar__sum_type_int');
              $('.layout__header-info-bar_type_sticky .basket-bar__currency').detach().insertAfter('.layout__header-info-bar_type_sticky .basket-bar__sum_type_int');
              $('.basket-bar__currency').each(function() {
                return $(this).replaceWith('<small class="basket-bar__currency" style="font-size:65%;"> ' + $(this).text() + '</small>');
              });
            } else {
              $('HEADER .layout__header-info-bar .basket-bar__currency').detach().insertBefore('HEADER .layout__header-info-bar .basket-bar__sum_type_int');
              $('.layout__header-info-bar_type_sticky .basket-bar__currency').detach().insertBefore('.layout__header-info-bar_type_sticky .basket-bar__sum_type_int');
              $('.basket-bar__currency').each(function() {
                return $(this).replaceWith('<span class="basket-bar__currency">' + $(this).text() + '</span>');
              });
            }
            if (sumUnformat <= 0) {
              return $('#basket-submit').attr('disabled', 'disabled');
            } else {
              return $('#basket-submit').removeAttr('disabled');
            }
          },
          changeBasket: function(data) {
            var basket, id;
            basket = settings.basket;
            data.type = data.type || 1;
            basket.sum += (parseFloat(data.cost) * ~~data.count) * data.type;
            basket.sumUsd += (parseFloat(data.costUsd) * ~~data.count) * data.type;
            basket.count = ~~basket.count + (~~data.count * data.type);
            id = data.id.toString().concat('-', data.size, '-', data.color);
            if (data.type === 1) {
              if (basket.products[id]) {
                basket.products[id].count = ~~basket.products[id].count + ~~data.count;
              } else {
                basket.products[id] = {
                  id: data.id,
                  count: data.count,
                  size: data.size,
                  color: data.color
                };
              }
            } else {
              delete basket.products[id];
            }
            if (basket.sum <= 0) {
              $('#basket-submit').attr('disabled', 'disabled');
            } else {
              $('#basket-submit').removeAttr('disabled');
            }
            $.cookie('basket', JSON.stringify(basket), {
              expires: 365,
              path: '/',
              domain: cookieDomain
            });
            return App.logic._drawBasket();
          },
          stickyHeader: function() {
            var sticky_header;
            sticky_header = $('.layout__header-info-bar_type_sticky');
            $(window).on('scroll', function() {
              if ($(document).scrollTop() > 60) {
                return sticky_header.addClass('layout__header-info-bar_type_sticky-show');
              } else {
                return sticky_header.removeClass('layout__header-info-bar_type_sticky-show');
              }
            });
            return $(window).trigger('scroll');
          },
          flyToBasket: function() {
            $('.button-buy').unbind('click');
            return $('.button-buy').on('click', function(ev) {
              var $el, $product, bezier_params, classSticky, clone, color, cost, costUsd, count, diffFlyLeft, diffFlyTop, id, position, size, stopEl, stop_position;
              $el = $(ev.target);
              id = ~~$el.attr('data-id');
              cost = $el.attr('data-cost');
              costUsd = $el.attr('data-usd-cost');
              size = $el.closest('.detail-info').find('#size').val() || 0;
              color = $el.closest('.detail-info').find('#color').val() || 0;
              $('.basket-bar__notifier').removeClass('pulse');
              $product = $el.closest('.products-list__item, .hot-products__item, .detail-info').find('.product-image-' + id);
              count = 1;
              if (0 !== $el.parent().find('#__form__count').length) {
                count = ~~$el.parent().find('#__form__count').val();
              }
              if (0 !== $product.length) {
                diffFlyLeft = -80;
                diffFlyTop = -37;
                if ($product.hasClass('detail-info__image-link')) {
                  diffFlyLeft = -150;
                  diffFlyTop = -77;
                }
                clone = $product.clone();
                position = $product.offset();
                clone.css({
                  'position': 'absolute'
                });
                classSticky = 'layout__header-info-bar_type_sticky-show';
                if ($('.layout__header-info-bar_type_sticky').hasClass(classSticky)) {
                  stopEl = $('.layout__header-info-bar_type_sticky .basket-bar');
                } else {
                  diffFlyTop = $(document).scrollTop() * -1 + 20;
                  stopEl = $('.layout__header-info-bar .basket-bar');
                }
                stop_position = stopEl.offset();
                bezier_params = {
                  start: {
                    x: position.left,
                    y: position.top,
                    angle: -90
                  },
                  end: {
                    x: stop_position.left + diffFlyLeft,
                    y: stop_position.top + diffFlyTop,
                    angle: 180,
                    length: .2
                  }
                };
                clone.appendTo('BODY');
                clone.css('opacity');
                clone.addClass('type_fly');
                clone.animate({
                  path: new $.path.bezier(bezier_params)
                }, 700, function() {
                  $('.basket-bar').removeClass('basket-bar_state_disabled');
                  return PubSub.publish('CHANGE_BASKET', {
                    method: 'add',
                    id: id,
                    count: count,
                    cost: cost,
                    costUsd: costUsd,
                    color: color,
                    size: size,
                    oper: 'add',
                    basket: settings.basket
                  });
                });
              } else {
                $('.basket-bar').removeClass('basket-bar_state_disabled');
                PubSub.publish('CHANGE_BASKET', {
                  method: 'add',
                  id: id,
                  count: count,
                  cost: cost,
                  costUsd: costUsd,
                  color: color,
                  size: size,
                  basket: settings.basket
                });
              }
              return ev.preventDefault();
            });
          },
          virtualFormElements: function() {
            $('.form__virtual-checkbox').on('click', function(ev) {
              var $el, className, data, value;
              $el = $(ev.currentTarget);
              className = 'form__virtual-checkbox_state_ckecked';
              $el.toggleClass(className);
              value = false;
              if ($el.hasClass(className)) {
                value = true;
                if ($el.attr('data-name') === 'all') {
                  $('.form__virtual-checkbox').not($el).attr('data-value', 'false').removeClass('form__virtual-checkbox_state_ckecked');
                } else {
                  $('.form__virtual-checkbox[data-name="all"]').attr('data-value', 'false').removeClass('form__virtual-checkbox_state_ckecked');
                }
              }
              $el.attr('data-value', value);
              data = {
                name: $el.attr('data-name'),
                value: $el.attr('data-value')
              };
              App.logic.buildFilterHash(data);
              return ev.preventDefault();
            });
            return $(window).bind('hashchange', function(ev) {
              return App.logic.buildFilterHash();
            });
          },
          buildFilterHash: function(data) {
            var $el, filter, filterParams, filterParamsArray, hash, index, nameVal;
            if (data == null) {
              data = null;
            }
            if (data && data.name === 'all' && data.value === 'true') {
              location.hash = '';
              return;
            }
            filter = location.hash.substr(1);
            filterParamsArray = [];
            if (filter) {
              filterParamsArray = filter.split('&');
            }
            filterParams = {};
            for (index in filterParamsArray) {
              nameVal = filterParamsArray[index].split('=');
              filterParams[nameVal[0]] = nameVal[1];
              $el = $('[data-name=' + nameVal[0] + ']');
              $el.attr('data-' + nameVal[0], nameVal[1]);
              if (nameVal[1] === 'true') {
                $el.addClass('form__virtual-checkbox_state_ckecked');
              } else {
                $el.removeClass('form__virtual-checkbox_state_ckecked');
              }
            }
            if (data) {
              filterParams[data.name] = data.value;
            }
            if (data) {
              filterParams.page = 1;
            }
            hash = '';
            for (index in filterParams) {
              if (typeof filterParams[index] === 'undefined') {
                filterParams[index] = '=true';
              } else {
                filterParams[index] = '=' + filterParams[index];
              }
              hash += index + filterParams[index] + '&';
            }
            hash = hash.substr(0, hash.length - 1);
            if (hash !== '') {
              return location.hash = hash;
            }
          },
          formElements: function() {
            $(document).on('click', '.form__input-count-btn', function(ev) {
              var $input, color_id, id, result, size_id, step, val;
              ev.preventDefault();
              $input = $(this).parent().find('INPUT');
              step = ~~$(this).attr('data-step');
              val = ~~$input.val();
              result = val + step;
              if (result <= 1) {
                result = 1;
              }
              $input.val(result);
              if ($input.hasClass('basket-products__count')) {
                id = ~~$input.attr('data-id');
                color_id = ~~$input.attr('data-color-id');
                size_id = ~~$input.attr('data-size-id');
                return PubSub.publish('CHANGE_PRODUCT_COUNT', {
                  count: result,
                  id: id,
                  color: color_id,
                  size: size_id
                });
              } else {
                return PubSub.publish('CHANGE_QUICK_COUNT', result);
              }
            });
            $(document).on('change', '.basket-products__count', function(ev) {
              var color_id, id, size_id;
              id = ~~$(this).attr('data-id');
              color_id = ~~$(this).attr('data-color-id');
              size_id = ~~$(this).attr('data-size-id');
              return PubSub.publish('CHANGE_PRODUCT_COUNT', {
                count: ~~$(this).val(),
                id: id,
                color: color_id,
                size: size_id
              });
            });
            $('#quick-buy-form__count').on('change', function(ev) {
              return PubSub.publish('CHANGE_QUICK_COUNT', $(this).val());
            });
            PubSub.subscribe('CHANGE_QUICK_COUNT', function(name, count) {
              var abb, cost, total_cost, total_usd_cost;
              cost = parseFloat($('.detail-info__basket-button').attr('data-cost'));
              total_cost = cost * ~~count;
              total_cost = numeral(total_cost).format('0,0.00');
              total_cost = total_cost.replace(',00', '');
              abb = $('#currencies LI A').first().attr('data-short-name');
              if (abb === 'P') {
                abb = '<span class="rur">' + abb + '</span>';
              }
              $('.quick-buy__total-cost').attr('data-cost', cost * ~~count);
              total_usd_cost = $('.quick-buy__detail-cost').attr('data-usd-cost');
              $('.quick-buy__total-cost').attr('data-usd-cost', total_usd_cost * $('#quick-buy-form__count').val());
              if (abb === 'грн.') {
                return $('.quick-buy__total-cost').html(numeral(cost * ~~count).format('0,0') + '&thinsp;<small>' + abb + '</small>');
              } else {
                return $('.quick-buy__total-cost').html(abb + total_cost);
              }
            });
            return PubSub.subscribe('CHANGE_PRODUCT_COUNT', function(name, data) {
              var $costEl, product_id;
              $costEl = $('#product-cost-' + data.id);
              product_id = data.id.toString().concat('-', data.size, '-', data.color);
              data.count = data.count - settings.basket.products[product_id].count;
              data.cost = $costEl.attr('data-cost');
              data.costUsd = $costEl.attr('data-usd-cost');
              return PubSub.publish('CHANGE_BASKET', {
                method: 'change',
                id: data.id,
                count: data.count,
                cost: data.cost,
                costUsd: data.costUsd,
                color: data.color,
                size: data.size,
                basket: settings.basket
              });
            });
          },
          currenciesSelect: function() {
            var that;
            that = this;
            return $('#currencies .currencies__link').on('click', function(ev) {
              var $current, abb, currency_synonym, ratio;
              ratio = $(this).attr('data-ratio');
              abb = $(this).attr('data-short-name');
              if (abb === 'P') {
                abb = '<span class="rur">' + abb + '</span>';
              }
              $current = $(this).parent().detach();
              $current.prependTo('#currencies');
              settings.basket.sum = settings.basket.sumUsd / ratio;
              $.cookie('basket', JSON.stringify(settings.basket), {
                expires: 365,
                path: '/',
                domain: cookieDomain
              });
              $('[data-cost]').each(function(index, value) {
                var cost;
                cost = $(this).attr('data-usd-cost');
                cost /= ratio;
                $(this).attr('data-cost', cost);
                if ($(this).hasClass('_print-cost')) {
                  if (abb === 'грн.') {
                    cost = numeral(cost).format('0,0');
                    return $(this).html(cost + '&thinsp;<small>' + abb + '</small>');
                  } else {
                    cost = numeral(cost).format('0,0.00');
                    cost = cost.replace(',00', '');
                    return $(this).html(abb + cost);
                  }
                }
              });
              currency_synonym = $('#currencies LI:first A').attr('href').substr(1);
              $('.quick-buy__form INPUT[name=currency]').val(currency_synonym);
              App.logic._drawBasket();
              $.ajax({
                url: '/users/set-currency/' + currency_synonym + '/',
                type: 'POST'
              });
              return ev.preventDefault();
            });
          },
          restoreBasket: function() {
            return $.ajax({
              url: '/json/basket/get/',
              type: 'GET',
              data: {
                products: $.cookie('basket')
              },
              dataType: 'json',
              success: function(data) {
                var basket;
                if (data) {
                  data.count = ~~data.count;
                  data.sum = parseFloat(data.sum);
                  data.sumUsd = parseFloat(data.sumUsd);
                  settings.basket = data;
                  $.cookie('basket', JSON.stringify(data), {
                    expires: 365,
                    path: '/',
                    domain: cookieDomain
                  });
                } else {
                  basket = $.cookie('basket');
                  if (basket) {
                    settings.basket = JSON.parse(basket);
                  }
                }
                App.logic._drawBasket();
                return Basket.sidebar(settings.basket);
              }
            });
          },
          typeaheadDetails: function() {
            var details, notAvailable, statisServer;
            details = new Bloodhound({
              datumTokenizer: function(d) {
                return Bloodhound.tokenizers.whitespace(d.num + ' ' + d.nm + ' ' + d.info);
              },
              queryTokenizer: Bloodhound.tokenizers.whitespace,
              limit: 7,
              remote: '/json/details/search/%QUERY/result.html',
              prefetch: '/json/details/search/presence.html'
            });
            details.initialize();
            statisServer = $('#search-query').attr('data-static-server');
            notAvailable = $('#search-query').attr('data-not-available');
            $('.search-element .typeahead').typeahead(null, {
              autoselect: true,
              displayKey: 'nm',
              source: details.ttAdapter(),
              templates: {
                suggestion: function(data) {
                  var presenceHtml;
                  if (data.im === '') {
                    data.im = '/uploads/images/noimage-sm.jpg';
                  }
                  presenceHtml = '';
                  if (data.pr === '0') {
                    presenceHtml = '<span class="typeahead__not-presence">' + notAvailable + '</span>';
                  }
                  return '<a class="typeahead__item" ' + 'href="/car/' + data.carsy + '/' + data.aid + '/' + data.id + '/">' + '<div class="typeahead__image"><img ' + 'src="' + statisServer + data.im + '">' + presenceHtml + '</div>' + '<strong class="typeahead__caption">#' + data.num + '</strong>' + '<p class="typeahead__text">' + data.nm + '</p>' + '<span class="typeahead__car">' + data.car + '</span>' + '</a>';
                }
              }
            });
            $('.search-element .typeahead').on('typeahead:selected', function(ev, data) {
              return window.location = '/car/' + data.carsy + '/' + data.aid + '/' + data.id + '/';
            });
            return $('.search-form').on('submit', function(ev) {
              if ($(this).closest('FORM').find('#search-query').val() !== '') {
                $(this).trigger('submit');
              }
              return ev.preventDefault();
            });
          }
        }
      };
      return Public = {
        init: function() {
          App.init();
          App.logic.restoreBasket();
          Basket.init(settings);
          App.logic.stickyHeader();
          App.logic.virtualFormElements();
          App.logic.buildFilterHash();
          App.logic.flyToBasket();
          App.logic.formElements();
          App.logic.currenciesSelect();
          App.logic.typeaheadDetails();
          Users.initSubscriptionBar();
          Users.initAddReviewForm();
          Users.initAuthPopup();
          Users.initCabinet();
          Comments.init();
          $('.__tooltip').each(function() {
            return $(this).powerTip({
              placement: $(this).data('placement') || 'n',
              smartPlacement: true
            });
          });
          $('.filter-bar__button').on('click', function(ev) {
            var state;
            state = $(this).data('state');
            $(this).toggleClass('filter-bar__button_state_checked');
            if (state === 'hide') {
              $(this).text($(this).data('show-lng'));
              $(this).data('state', 'show');
              $('.autoparts__schema').removeClass('autoparts__schema_state_visible');
            } else {
              $(this).text($(this).data('hide-lng'));
              $(this).data('state', 'hide');
              $('.autoparts__schema').addClass('autoparts__schema_state_visible');
            }
            return ev.preventDefault();
          });
          $('.layout__header-info-bar_state_invisible').removeClass('layout__header-info-bar_state_invisible');
          PubSub.subscribe('LOAD_PAGE_CONTENT', function(name) {
            return App.logic.flyToBasket();
          });
          $('.tabs').uwinTabs();
          if ($('#map-canvas').length) {
            return UwinGoogleMap($('#map-canvas'), {
              lang: $('html').attr('lang').split('-')[0]
            });
          }
        }
      };
    })(window.jQuery, window, document);
    return Avtoclassika.init();
  });

}).call(this);
