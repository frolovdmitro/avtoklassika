define [
  'jquery'
], () ->
  Sparky =
    settings: {}

    init: (settings) ->
      # Проверяем есть ли meta-теги для приложения: host сайта и маршрут
      # страницы
      $('meta[name^="app-"]').each ->
        settings.meta[@name.replace('app-', '')] = @content

      # Если маршрут не указан - берем его на основе относительного пути
      if (typeof settings.meta.route == 'undefined')
        pathname = window.location.pathname

        if pathname[0] is '/'
          pathname = pathname.substring(1)

        if pathname[pathname.length - 1] is '/'
          pathname = pathname.substring(0, pathname.length - 1)

        # Если главная страница - маршрут index
        if pathname is ''
          pathname = 'index'

        settings.meta.route = pathname

      @settings = settings

    log: (what) ->
      if @settings.debug
        console.log what

    parseRoute: (input) ->
      delimiter = input.delimiter || '/'
      paths = input.path.split(delimiter)
      check = input.target[paths.shift()]
      exists = typeof check isnt 'undefined'
      isLast = paths.length is 0

      input.inits = input.inits || []

      if (exists)
        if (typeof check.init == 'function')
          input.inits.push check.init

        if (isLast)
          input.parsed.call(undefined,
            exists: true
            type: typeof check
            obj: check
            inits: input.inits
          )
        else
          @parseRoute(
            path: paths.join delimiter
            target: check
            delimiter: delimiter
            parsed: input.parsed
            inits: input.inits
          )
      else
        input.parsed.call(undefined,
          exists: false
        )

    route: (Routes) ->
      @parseRoute(
        path: @settings.meta.route,
        target: Routes,
        delimiter: '/',
        parsed: (res) ->
          if (res.exists && res.type == 'function')
            if res.inits.length isnt 0
              for i in res.inits
                res.inits[i].call()

            res.obj.call()
      )

    bindEvents: (Events) ->
      $('[data-event]').each( ->
        _this = @
        dataset = $(@).data()
        method = dataset.method || 'click'
        name = dataset.event
        bound = dataset.bound

        if !bound
          Sparky.parseRoute(
            path: name
            target: Events.endpoints
            delimiter: '.'
            parsed: (res) ->
              if res.exists
                dataset.bound = true

                $(_this).on(method, (e) ->
                  res.obj.call(_this, e)
                )
          )
      )


