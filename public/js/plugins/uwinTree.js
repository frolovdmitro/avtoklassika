(function() {
  (function($, window, document) {
    var Plugin, defaults, pluginName;
    pluginName = 'uwinTree';
    defaults = {};
    Plugin = (function() {
      function Plugin(element, options) {
        var that;
        this.element = element;
        this.$element = $(this.element);
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        that = this;
        this.$element.on('click', this.settings.selector, function(ev) {
          var $el;
          $el = $(ev.currentTarget).parent();
          if ($el.hasClass('car-autoparts-tree__item')) {
            $el.toggleClass('car-autoparts-tree__item' + that.settings.class_expand_suffix);
          }
          if ($el.hasClass('car-autoparts-tree__subitem')) {
            $el.toggleClass('car-autoparts-tree__subitem' + that.settings.class_expand_suffix);
          }
          return ev.preventDefault();
        });
        if (this.settings.success) {
          this.settings.success();
        }
      }

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
