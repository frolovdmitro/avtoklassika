define [
  'jquery'
  'yepnope'
], () ->
  UwinGoogleMap = (element, options) ->
    options = options || {}
    that = @

    yepnope
      load: '//www.google.com/jsapi?'
      complete: ->
        google.load('maps', '3', {
          callback: ->
            _init(that, options)
          other_params: 'sensor=false&language=' + options.lang
        })

    @defaults =
      centerCoord: '50.486081,30.49324'
      zoom: 10 # Масштаб
      draggable: true
      disableDefaultUI: false
      disableDoubleClickZoom: false
      scrollwheel: false
      mapTypeControl: true
      streetViewControl: true
      zoomControl: true
      panControl: true
      pin: '/img/pins/me.png'

    @markers = []

    @settings = $.extend {}, @defaults, options

    getLatLng = (coord, is_center = false) ->
      latlng = coord.split(',')
      diff = 0
      if is_center
        diff = -0.0005
      coord =
        lat: parseFloat(latlng[0]) + diff,
        lng: parseFloat(latlng[1])

      new google.maps.LatLng(coord.lat, coord.lng)

    _init = (that, options) ->
      that.settings.centerCoord = (options.centerCoord ||
      element.attr('data-center') ||
      that.settings.centerCoord)

      that.settings.zoom = ~~(options.zoom ||
        element.attr('data-zoom') ||
        that.settings.zoom)

      that.settings.center = getLatLng that.settings.centerCoord, true
      if !options.markers
        that.markers.push element.attr('data-marker')

      # Рисуем карту
      that.map = new google.maps.Map element.get(0), that.settings

      # Добавляемна на карту  маркер
      for marker in that.markers
        new google.maps.Marker(
          icon: that.settings.pin
          map: that.map
          draggable: false
          position: getLatLng(marker)
        )

    return @

  # prototype = UwinGoogleMap.prototype

  # prototype.setCenter = (latlng) ->

  # prototype.setZoom = (zoom) ->

  # prototype.addMarker = (marker) ->

  # prototype.deleteMarker = (marker) ->

  # prototype.clearMarkers = ->

  # prototype.centeringMarker = ->

  (element, options) ->
    new UwinGoogleMap(element, options)
