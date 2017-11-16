(function() {
  define(['jquery'], function() {
    var Sparky;
    return Sparky = {
      settings: {},
      init: function(settings) {
        var pathname;
        $('meta[name^="app-"]').each(function() {
          return settings.meta[this.name.replace('app-', '')] = this.content;
        });
        if (typeof settings.meta.route === 'undefined') {
          pathname = window.location.pathname;
          if (pathname[0] === '/') {
            pathname = pathname.substring(1);
          }
          if (pathname[pathname.length - 1] === '/') {
            pathname = pathname.substring(0, pathname.length - 1);
          }
          if (pathname === '') {
            pathname = 'index';
          }
          settings.meta.route = pathname;
        }
        return this.settings = settings;
      },
      log: function(what) {
        if (this.settings.debug) {
          return console.log(what);
        }
      },
      parseRoute: function(input) {
        var check, delimiter, exists, isLast, paths;
        delimiter = input.delimiter || '/';
        paths = input.path.split(delimiter);
        check = input.target[paths.shift()];
        exists = typeof check !== 'undefined';
        isLast = paths.length === 0;
        input.inits = input.inits || [];
        if (exists) {
          if (typeof check.init === 'function') {
            input.inits.push(check.init);
          }
          if (isLast) {
            return input.parsed.call(void 0, {
              exists: true,
              type: typeof check,
              obj: check,
              inits: input.inits
            });
          } else {
            return this.parseRoute({
              path: paths.join(delimiter),
              target: check,
              delimiter: delimiter,
              parsed: input.parsed,
              inits: input.inits
            });
          }
        } else {
          return input.parsed.call(void 0, {
            exists: false
          });
        }
      },
      route: function(Routes) {
        return this.parseRoute({
          path: this.settings.meta.route,
          target: Routes,
          delimiter: '/',
          parsed: function(res) {
            var i, j, len, ref;
            if (res.exists && res.type === 'function') {
              if (res.inits.length !== 0) {
                ref = res.inits;
                for (j = 0, len = ref.length; j < len; j++) {
                  i = ref[j];
                  res.inits[i].call();
                }
              }
              return res.obj.call();
            }
          }
        });
      },
      bindEvents: function(Events) {
        return $('[data-event]').each(function() {
          var _this, bound, dataset, method, name;
          _this = this;
          dataset = $(this).data();
          method = dataset.method || 'click';
          name = dataset.event;
          bound = dataset.bound;
          if (!bound) {
            return Sparky.parseRoute({
              path: name,
              target: Events.endpoints,
              delimiter: '.',
              parsed: function(res) {
                if (res.exists) {
                  dataset.bound = true;
                  return $(_this).on(method, function(e) {
                    return res.obj.call(_this, e);
                  });
                }
              }
            });
          }
        });
      }
    };
  });

}).call(this);
