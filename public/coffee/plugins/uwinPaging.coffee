do ($ = jQuery, window, document) ->
  pluginName = 'uwinPaging'

  # Опции плагина
  defaults =
    url: 'page.html' # URL куда будет отправлен запрос на получение страницы
    pages: 1 # Кол-во страниц
    useFirstLastArrows: true # Отображать или нет стрелки первая/последняя стр
    contentSelector: '#content' # Селектор контентной части
    # сколько выводить ссылок на страницы слева и справа от активной страницы
    countLeftRightPages: 6

  class Plugin
    constructor: (@element, options) ->
      @$element  = $(@element)
      @settings  = $.extend {}, defaults, options

      if @$element.attr('data-content')
        @settings.contentSelector = @$element.attr('data-content')

      # Приватные переменные
      @_defaults  = defaults
      @_name      = pluginName
      @_contentEl = $(@settings.contentSelector)

      # URL куда делать запрос на контент страницы
      if @$element.attr('data-url')
        @settings.url = @$element.attr('data-url')

      # Кол-во страниц
      if @$element.attr('data-pages')
        @settings.pages = ~~@$element.attr('data-pages')

      @init()

    getParamsFromHash: ()->
      filter = location.hash.substr(1)
      paramsArray = []
      paramsArray = filter.split '&' if filter
      params = {}

      for index of paramsArray
        nameVal = paramsArray[index].split('=')
        params[nameVal[0]] = nameVal[1]

      return params

    convertParamsToHash: (params)->
      hash = '#'
      for index of params
        hash += index + '=' + params[index] + '&'
      hash = hash.substr 0, (hash.length - 1)

      return hash

    # Метод возвращает hash для указанной страницы
    getPageHash: (page)->
      params = @getParamsFromHash()
      params.page = page if page

      hash = @convertParamsToHash(params)

      return hash

    # Метод возвращает номер текущей страницы
    getCurrentPage: ->
      return ~~(@getParamsFromHash().page || 1)

    # Рендеринг пейджинга
    render: ->
      if @settings.pages <= 1
        @$element.html ''
        return

      currentPage = @getCurrentPage()
      firstPageLink = currentPage - (@settings.countLeftRightPages / 2)

      diffPages = 0
      if firstPageLink < 1
        diffPages = firstPageLink * -1
        firstPageLink = 1
        diffPages++

      lastPageLink = ~~currentPage + ~~(@settings.countLeftRightPages / 2) +
        ~~diffPages
      if lastPageLink > @settings.pages
        lastPageLink = @settings.pages

      # Создаем пустой список страниц
      @$element.html '<ul class="paging__list"></ul>'
      ul = @$element.find('UL')

      # Добавляем стрелочку "пред. страниц" и "первая страница", если нужно
      if currentPage != 1
        ul.append '<li class="paging__item paging__item_type_prev">' +
          '<a class="paging__link paging__link_type_prev" href="' +
          @getPageHash(currentPage - 1) + '">Предыдущая</a></li>'
      else
        ul.append '<li style="visibility: hidden;"' +
          'class="paging__item paging__item_type_prev">' +
          '<a class="paging__link paging__link_type_prev" ' +
          'href="' + @getPageHash(currentPage - 1) + '">Предыдущая</a></li>'

      # Добавляем ссылки на ближайшие страницы
      for i in [firstPageLink .. lastPageLink]
        active = ' class="paging__item"'
        if i == currentPage
          active = ' class="paging__item paging__item_type_current"'

        ul.append '<li' + active + ' data-page="' + i + '">' +
          '<a class="paging__link" href="' + @getPageHash(i) +
          '">' + i + '</a></li>'

      # Добавляем стрелочку "след. страниц" и "последняя страница", если нужно
      if currentPage != @settings.pages
        ul.append '<li class="paging__item paging__item_type_next">' +
          '<a class="paging__link paging__link_type_next"' +
          ' href="' + @getPageHash(currentPage + 1) + '">Следующая</a>' +
          '</li>'
      else
        ul.append '<li style="visibility: hidden;" ' +
          'class="paging__item paging__item_type_next">' +
          '<a class="paging__link paging__link_type_next" href="' +
          @getPageHash(currentPage + 1) + '">Следующая</a></li>'

    # Метод возвращает и устанавливает контент текущей страницы
    getPage: ->
      that = @

      params = @getParamsFromHash()
      params.page = @getCurrentPage()

      $.ajax
        type: 'GET'
        url: that.settings.url
        data: params
      .done (data)->
        response = $.parseJSON data
        that._contentEl.html response.html
        that.settings.pages = ~~response.pages

        # Если изменилась страница, делаем скролл наверх родной или переданной
        # функцией
        prevPage = that.$element.find('.paging__item_type_current')
          .attr 'data-page'
        if ~~prevPage == 0
          prevPage = 1

        if prevPage != ~~that.getCurrentPage()
          if !that.settings.gotoTopPage
            $('html, body').animate
              scrollTop: 0
            , 300
          else
            if $(window).scrollTop() > that._contentEl.offset().top
              that.settings.gotoTopPage()

        # Перерисовываем пейджинг
        that.render()

        if that.settings.callback instanceof Function
          that.settings.callback.call @

    init: ->
      that = @
      @render()

      # Навешиваем на событие hashchange получение страницы
      $(window).hashchange (ev)->
        that.getPage()
      .trigger 'hashchange'

  $.fn[pluginName] = (options) ->
    @each ->
      if !$.data(@, "plugin_#{pluginName}")
        $.data(@, "plugin_#{pluginName}", new Plugin(@, options))
