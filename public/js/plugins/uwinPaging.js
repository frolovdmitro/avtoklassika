(function() {
  (function($, window, document) {
    var Plugin, defaults, pluginName;
    pluginName = 'uwinPaging';
    defaults = {
      url: 'page.html',
      pages: 1,
      useFirstLastArrows: true,
      contentSelector: '#content',
      countLeftRightPages: 6
    };
    Plugin = (function() {
      function Plugin(element, options) {
        this.element = element;
        this.$element = $(this.element);
        this.settings = $.extend({}, defaults, options);
        if (this.$element.attr('data-content')) {
          this.settings.contentSelector = this.$element.attr('data-content');
        }
        this._defaults = defaults;
        this._name = pluginName;
        this._contentEl = $(this.settings.contentSelector);
        if (this.$element.attr('data-url')) {
          this.settings.url = this.$element.attr('data-url');
        }
        if (this.$element.attr('data-pages')) {
          this.settings.pages = ~~this.$element.attr('data-pages');
        }
        this.init();
      }

      Plugin.prototype.getParamsFromHash = function() {
        var filter, index, nameVal, params, paramsArray;
        filter = location.hash.substr(1);
        paramsArray = [];
        if (filter) {
          paramsArray = filter.split('&');
        }
        params = {};
        for (index in paramsArray) {
          nameVal = paramsArray[index].split('=');
          params[nameVal[0]] = nameVal[1];
        }
        return params;
      };

      Plugin.prototype.convertParamsToHash = function(params) {
        var hash, index;
        hash = '#';
        for (index in params) {
          hash += index + '=' + params[index] + '&';
        }
        hash = hash.substr(0, hash.length - 1);
        return hash;
      };

      Plugin.prototype.getPageHash = function(page) {
        var hash, params;
        params = this.getParamsFromHash();
        if (page) {
          params.page = page;
        }
        hash = this.convertParamsToHash(params);
        return hash;
      };

      Plugin.prototype.getCurrentPage = function() {
        return ~~(this.getParamsFromHash().page || 1);
      };

      Plugin.prototype.render = function() {
        var active, currentPage, diffPages, firstPageLink, i, j, lastPageLink, ref, ref1, ul;
        if (this.settings.pages <= 1) {
          this.$element.html('');
          return;
        }
        currentPage = this.getCurrentPage();
        firstPageLink = currentPage - (this.settings.countLeftRightPages / 2);
        diffPages = 0;
        if (firstPageLink < 1) {
          diffPages = firstPageLink * -1;
          firstPageLink = 1;
          diffPages++;
        }
        lastPageLink = ~~currentPage + ~~(this.settings.countLeftRightPages / 2) + ~~diffPages;
        if (lastPageLink > this.settings.pages) {
          lastPageLink = this.settings.pages;
        }
        this.$element.html('<ul class="paging__list"></ul>');
        ul = this.$element.find('UL');
        if (currentPage !== 1) {
          ul.append('<li class="paging__item paging__item_type_prev">' + '<a class="paging__link paging__link_type_prev" href="' + this.getPageHash(currentPage - 1) + '">Предыдущая</a></li>');
        } else {
          ul.append('<li style="visibility: hidden;"' + 'class="paging__item paging__item_type_prev">' + '<a class="paging__link paging__link_type_prev" ' + 'href="' + this.getPageHash(currentPage - 1) + '">Предыдущая</a></li>');
        }
        for (i = j = ref = firstPageLink, ref1 = lastPageLink; ref <= ref1 ? j <= ref1 : j >= ref1; i = ref <= ref1 ? ++j : --j) {
          active = ' class="paging__item"';
          if (i === currentPage) {
            active = ' class="paging__item paging__item_type_current"';
          }
          ul.append('<li' + active + ' data-page="' + i + '">' + '<a class="paging__link" href="' + this.getPageHash(i) + '">' + i + '</a></li>');
        }
        if (currentPage !== this.settings.pages) {
          return ul.append('<li class="paging__item paging__item_type_next">' + '<a class="paging__link paging__link_type_next"' + ' href="' + this.getPageHash(currentPage + 1) + '">Следующая</a>' + '</li>');
        } else {
          return ul.append('<li style="visibility: hidden;" ' + 'class="paging__item paging__item_type_next">' + '<a class="paging__link paging__link_type_next" href="' + this.getPageHash(currentPage + 1) + '">Следующая</a></li>');
        }
      };

      Plugin.prototype.getPage = function() {
        var params, that;
        that = this;
        params = this.getParamsFromHash();
        params.page = this.getCurrentPage();
        return $.ajax({
          type: 'GET',
          url: that.settings.url,
          data: params
        }).done(function(data) {
          var prevPage, response;
          response = $.parseJSON(data);
          that._contentEl.html(response.html);
          that.settings.pages = ~~response.pages;
          prevPage = that.$element.find('.paging__item_type_current').attr('data-page');
          if (~~prevPage === 0) {
            prevPage = 1;
          }
          if (prevPage !== ~~that.getCurrentPage()) {
            if (!that.settings.gotoTopPage) {
              $('html, body').animate({
                scrollTop: 0
              }, 300);
            } else {
              if ($(window).scrollTop() > that._contentEl.offset().top) {
                that.settings.gotoTopPage();
              }
            }
          }
          that.render();
          if (that.settings.callback instanceof Function) {
            return that.settings.callback.call(this);
          }
        });
      };

      Plugin.prototype.init = function() {
        var that;
        that = this;
        this.render();
        return $(window).hashchange(function(ev) {
          return that.getPage();
        }).trigger('hashchange');
      };

      return Plugin;

    })();
    return $.fn[pluginName] = function(options) {
      return this.each(function() {
        if (!$.data(this, "plugin_" + pluginName)) {
          return $.data(this, "plugin_" + pluginName, new Plugin(this, options));
        }
      });
    };
  })(jQuery, window, document);

}).call(this);
