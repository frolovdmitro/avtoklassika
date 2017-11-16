define [
  'jquery'
  'pubsub'

  'uwinTree'
], ($, PubSub) ->
  News =
    # Инициализация
    init: ->
      # Подключаю пейджинг на страницу
      $('[data-paging]').uwinPaging
        gotoTopPage: ->
          $.scrollTo '.layout__content.news', 800, {offset: -65}
        callback: ()->
          PubSub.publish 'LOAD_PAGE_CONTENT', {}

      $('.car-autoparts-tree__list').uwinTree
        selector: '.expanded'
        class_expand_suffix: '_state_expand'
