(function() {
  (function($, window, document) {
    var Plugin, pluginName;
    pluginName = 'uwinTabs';
    Plugin = (function() {
      function Plugin(element) {
        var that;
        this.element = element;
        this.$element = $(this.element);
        this._name = pluginName;
        that = this;
        this.$element.find('.tabs__nav-link').on('click', function(ev) {
          var panel_id;
          that.$element.find('.tabs__nav-item').removeClass('tabs__nav-item_state_current');
          $(this).parent().addClass('tabs__nav-item_state_current');
          panel_id = $(this).attr('href');
          that.$element.find('.tabs__content').removeClass('tabs__content_state_current');
          $(panel_id).addClass('tabs__content_state_current');
          return ev.preventDefault();
        });
      }

      return Plugin;

    })();
    return $.fn[pluginName] = function() {
      return this.each(function() {
        if (!$.data(this, "plugin_" + pluginName)) {
          return $.data(this, "plugin_" + pluginName, new Plugin(this));
        }
      });
    };
  })(jQuery, window, document);

}).call(this);
