(function() {
  define(['jquery', 'pubsub', 'uwinTree'], function($, PubSub) {
    var News;
    return News = {
      init: function() {
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
        return $('.car-autoparts-tree__list').uwinTree({
          selector: '.expanded',
          class_expand_suffix: '_state_expand'
        });
      }
    };
  });

}).call(this);
