do ($ = jQuery, window, document) ->
  pluginName = 'uwinTree'

  # Опции плагина
  defaults = {}

  class Plugin
    constructor: (@element, options) ->
      @$element  = $(@element)
      @settings  = $.extend {}, defaults, options

      # Приватные переменные
      @_defaults  = defaults
      @_name      = pluginName

      that = @
      @$element.on 'click', @settings.selector, (ev)->
        $el = $(ev.currentTarget).parent()
        if $el.hasClass 'car-autoparts-tree__item'
          $el.toggleClass 'car-autoparts-tree__item' +
            that.settings.class_expand_suffix

        if $el.hasClass 'car-autoparts-tree__subitem'
          $el.toggleClass 'car-autoparts-tree__subitem' +
            that.settings.class_expand_suffix

        ev.preventDefault()
      if @settings.success
        @settings.success()

  $.fn[pluginName] = (options) ->
    @each ->
      if !$.data(@, "plugin_#{pluginName}")
        $.data(@, "plugin_#{pluginName}", new Plugin(@, options))
