(function() {
  var indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  define(['jquery', 'pubsub', 'yepnope', 'iframe_transport', 'fileupload', 'flexslider'], function($, PubSub) {
    var Ads;
    return Ads = {
      uploadedFiles: [],
      init: function() {
        var that;
        that = this;
        $('[data-paging]').uwinPaging({
          gotoTopPage: function() {
            return $.scrollTo('.layout__content.news', 800, {
              offset: -65
            });
          },
          callback: function() {
            return PubSub.publish('LOAD_PAGE_CONTENT', {});
          }
        });
        return this.initAddForm();
      },
      initAddForm: function() {
        var that;
        that = this;
        $(document).on($.modal.OPEN, function(ev, modal) {
          var $form;
          $form = $('#add-ads');
          return $form.find('INPUT, SELECT').filter(':first').focus();
        });
        return $(document).on($.modal.BEFORE_OPEN, function(ev, modal) {
          var uploadedFiles;
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
          $('#add-ads').on('submit', function(ev) {
            var $form, $submit, data, file, form_name_prefix, j, len, ref;
            $form = $(this);
            $form.find('.error').css('opacity', '');
            $submit = $form.find('button[type=submit]');
            $submit.attr('disabled', 'disabled');
            form_name_prefix = 'add-ads__';
            data = $form.serialize();
            ref = that.uploadedFiles;
            for (j = 0, len = ref.length; j < len; j++) {
              file = ref[j];
              data += '&photo[]=' + file;
            }
            return $.ajax({
              type: 'POST',
              url: $form.attr('action'),
              data: data,
              dataType: 'json',
              success: function(data) {
                that.uploadedFiles = [];
                $.modal.close();
                if ($('[data-paging]').length !== 0) {
                  return $('[data-paging]').uwinPaging().data('plugin_uwinPaging').getPage();
                } else {
                  return window.location = '/ads/';
                }
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
          $('#add-ads SELECT').uniform({
            selectAutoWidth: false,
            selectClass: 'selector form__selector_size_big'
          });
          uploadedFiles = 0;
          return $('#fileupload').fileupload({
            dataType: 'json',
            send: function(ev, data) {
              var $errorEl, timer;
              if (data.files[0].size > (1024 * 1024 * 2)) {
                $errorEl = $('.form__fileinput-error');
                $errorEl.text($(ev.target).attr('data-error-filesize'));
                $errorEl.css('opacity', '1');
                timer = setInterval(function() {
                  $errorEl.css('opacity', '0');
                  return clearInterval(timer);
                }, 5000);
                return false;
              }
              if (uploadedFiles >= 10) {
                $errorEl = $('.form__fileinput-error');
                $errorEl.text($(ev.target).attr('data-error-maxfiles'));
                $errorEl.css('opacity', '1');
                timer = setInterval(function() {
                  $errorEl.css('opacity', '0');
                  return clearInterval(timer);
                }, 5000);
                return false;
              }
              return uploadedFiles++;
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
              filesNames = $('.form__fileinput-files').html();
              filesNames = filesNames + ',&nbsp;&nbsp; ' + data.files[0].name;
              if (',&nbsp;&nbsp; ' === filesNames.substr(0, 14)) {
                filesNames = filesNames.substr(14);
              }
              $('.form__fileinput-files').html(filesNames);
              that.uploadedFiles.push(data.result.file);
              return $('#upload-progress').css('width', 0);
            }
          });
        });
      },
      initGallery: function(el) {
        var $el;
        $el = $(el);
        return $el.swipebox({
          hideBarsDelay: 0
        });
      },
      initBarSlider: function(el) {
        var $el;
        $el = $(el);
        return $el.flexslider({
          animation: "slide",
          directionNav: true,
          controlNav: false,
          animationLoop: false,
          itemWidth: 184,
          slideshow: false
        });
      },
      initSlider: function(el) {
        var $el;
        $el = $(el);
        return $el.flexslider({
          animation: "slide",
          directionNav: true,
          controlNav: false,
          animationLoop: false,
          itemWidth: 227,
          itemMargin: 9,
          slideshow: true,
          keyboard: false,
          controlsContainer: '#other-adverts__nav'
        });
      }
    };
  });

}).call(this);
