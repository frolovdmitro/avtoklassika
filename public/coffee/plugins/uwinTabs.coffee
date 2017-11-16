do ($ = jQuery, window, document) ->
  pluginName = 'uwinTabs'

  class Plugin
    constructor: (@element) ->
      @$element  = $(@element)

      # Приватные переменные
      @_name      = pluginName

      that = @
      @$element.find('.tabs__nav-link').on 'click', (ev)->
        that.$element.find('.tabs__nav-item')
          .removeClass 'tabs__nav-item_state_current'
        $(@).parent().addClass 'tabs__nav-item_state_current'

        panel_id = $(@).attr 'href'

        that.$element.find('.tabs__content')
          .removeClass 'tabs__content_state_current'
        $(panel_id).addClass 'tabs__content_state_current'

        ev.preventDefault()

  $.fn[pluginName] = () ->
    @each ->
      if !$.data(@, "plugin_#{pluginName}")
        $.data(@, "plugin_#{pluginName}", new Plugin(@))
