(function() {
  define(['jquery', 'yepnope'], function() {
    var UwinGoogleMap;
    UwinGoogleMap = function(element, options) {
      var _init, getLatLng, that;
      options = options || {};
      that = this;
      yepnope({
        load: '//www.google.com/jsapi?',
        complete: function() {
          return google.load('maps', '3', {
            callback: function() {
              return _init(that, options);
            },
            other_params: 'sensor=false&language=' + options.lang
          });
        }
      });
      this.defaults = {
        centerCoord: '50.486081,30.49324',
        zoom: 10,
        draggable: true,
        disableDefaultUI: false,
        disableDoubleClickZoom: false,
        scrollwheel: false,
        mapTypeControl: true,
        streetViewControl: true,
        zoomControl: true,
        panControl: true,
        pin: '/img/pins/me.png'
      };
      this.markers = [];
      this.settings = $.extend({}, this.defaults, options);
      getLatLng = function(coord, is_center) {
        var diff, latlng;
        if (is_center == null) {
          is_center = false;
        }
        latlng = coord.split(',');
        diff = 0;
        if (is_center) {
          diff = -0.0005;
        }
        coord = {
          lat: parseFloat(latlng[0]) + diff,
          lng: parseFloat(latlng[1])
        };
        return new google.maps.LatLng(coord.lat, coord.lng);
      };
      _init = function(that, options) {
        var i, len, marker, ref, results;
        that.settings.centerCoord = options.centerCoord || element.attr('data-center') || that.settings.centerCoord;
        that.settings.zoom = ~~(options.zoom || element.attr('data-zoom') || that.settings.zoom);
        that.settings.center = getLatLng(that.settings.centerCoord, true);
        if (!options.markers) {
          that.markers.push(element.attr('data-marker'));
        }
        that.map = new google.maps.Map(element.get(0), that.settings);
        ref = that.markers;
        results = [];
        for (i = 0, len = ref.length; i < len; i++) {
          marker = ref[i];
          results.push(new google.maps.Marker({
            icon: that.settings.pin,
            map: that.map,
            draggable: false,
            position: getLatLng(marker)
          }));
        }
        return results;
      };
      return this;
    };
    return function(element, options) {
      return new UwinGoogleMap(element, options);
    };
  });

}).call(this);
