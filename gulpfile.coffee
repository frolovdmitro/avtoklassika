'use strict'

gulp = require 'gulp'
$    = require('gulp-load-plugins')()
del  = require 'del'
nib  = require 'nib'
seq  = require 'run-sequence'
path = require 'path'
fs   = require 'fs'
flo  = require 'fb-flo'
source   = require 'vinyl-source-stream'
mozjpeg  = require 'imagemin-mozjpeg'
# browserify = require 'browserify'
watchify = require 'watchify'
sprite   = require('css-sprite').stream

project = require('./package.json').name
domain = 'avtoclassika.com'

static_production_host = 's1.' + domain
static_staging_host    = 's1.' + project + '.uwinart.com'
static_develop_host    = 's1.' + project + '.uwinart.local'
static_host            = static_develop_host
static_server_path     = './static-servers/' + static_production_host + '/'

if process.env.NODE_ENV == 'staging'
  static_host = static_staging_host
else if process.env.NODE_ENV == 'production'
  static_host = 's1av.uwinart.com'

# working with css {{{
stylus_path = './public/stylus/'
stylus_sprites_path = stylus_path + 'assets/sprites/'
css_path    = './public/css/'
cssmin_path = static_server_path + 'css/' # }}}

# working with js {{{
coffee_path = './public/coffee/'
js_path     = './public/js/'
jsmin_path  = static_server_path + 'js/' # }}}

# working with backend css & js {{{
backend_js_path  = './public/backend/js/'
backend_css_path = './public/backend/css/' # }}}

# working with application(tpl, etc) {{{
app_path             = './application/'
app_build_path       = './build/'
app_build_views_path = app_build_path + 'views/' # }}}

# working with images {{{
images_path    = './public/img/'
imagesmin_path = static_server_path + 'img/' # }}}

# working with fonts {{{
fonts_path    = './public/fonts/'
fontsmin_path = static_server_path + 'fonts/' # }}}

# globs for finding files {{{
globs =
  stylus: stylus_path + '**/**.styl'
  css: css_path + '*.css'
  css_backend: css_path + 'backend/main.css'
  css_backend_addition: [
    css_path + 'contenteditable.css'
    css_path + 'fresheditor.css'
  ]

  coffee: coffee_path + '**/**.coffee'
  coffee_app: coffee_path + project + '.coffee'
  js_backend: js_path + 'backend/**/*.js'
  js_app: js_path + project + '.js'
  fonts:  [
    fonts_path + '**/**.svg'
    fonts_path + '**/**.eot'
    fonts_path + '**/**.ttf'
    fonts_path + '**/**.woff'
  ]

  html:
    general: app_path + 'views/**/*.tpl'
    modules: app_path + 'modules/**/*.tpl'
    components: app_path + 'components/**/*.tpl'

  images_src: [
    images_path + 'src/**/*.png'
    images_path + 'src/**/*.jpg'
    images_path + 'src/**/*.jpeg'
    images_path + 'src/**/*.gif'
    '!' + images_path + 'src/sprites-sets/**'
  ]
  images: [
    images_path + '**/*.png'
    images_path + '**/*.jpg'
    images_path + '**/*.jpeg'
    images_path + '**/*.gif'
    images_path + '**/*.svg'
    '!' + images_path + 'src/**'
    '!' + images_path + '_inline/**'
  ]
  sprites: {
    icons: images_path + 'src/sprites-sets/png/icons/*.png'
    social: images_path + 'src/sprites-sets/png/social/*.png'
    panels: images_path + 'src/sprites-sets/png/panels/*.png'
    paysDels: images_path + 'src/sprites-sets/png/payments-deliveries/*.png'
  }
# }}}


gulp.task 'help', $.taskListing

# clean: remove all build files {{{
gulp.task 'clean', (cb) ->
  del([
    css_path, cssmin_path, js_path, jsmin_path, imagesmin_path,
    app_build_path, fontsmin_path,
    images_path + '*.*'
  ], cb)
# }}}

# backend-css: copy backend css to css {{{
gulp.task 'backend-css', ->
  gulp.src(backend_css_path + '*.css')
    .pipe( gulp.dest css_path + 'backend' )
# }}}
# backend-js: copy backend js to js {{{
gulp.task 'backend-js', ->
  gulp.src(backend_js_path + '**/*.js')
    .pipe( gulp.dest(js_path + 'backend') )
# }}}
# backend: copy backend css and js {{{
gulp.task 'backend', ['backend-css', 'backend-js']
# }}}

# # hello: build hello.app.js {{{
# gulp.task 'hello', ->
#   srcDir = './public/components/external/hello/src/'
#   gulp.src([
#     srcDir + 'hello.js'
#     srcDir + 'hello.then.js'
#     srcDir + 'modules/facebook.js'
#     srcDir + 'modules/google.js'
#     './public/components/local/hello/src/modules/vkontakte.js'
#     srcDir + 'hello.commonjs.js'
#   ]).pipe( $.concat 'hello.app.js' )
#     .pipe( gulp.dest './public/components/external/hello/dist/' )
# # }}}

# svg-general: generate main svg file {{{
gulp.task 'svg-general', ->
  gulp.src([images_path + 'src/sprites-sets/general/*.svg'])
    .pipe($.svgstore
      fileName: '_sprite-general.svg'
      prefix: 'icon-'
      inlinesvg: true
    )
    .pipe( $.svgmin() )
    .pipe(gulp.dest images_path )
# }}}
# svg-build: generate svg files {{{
gulp.task 'svg-build', (cb) ->
  seq 'svg-general', cb
# }}}

# sprites: create sprites {{{
spriteOptions = (name, margin) ->
  margin = margin || 10
  return {
    name: '_' + name
    style: '_' + name + '.styl'
    processor: 'stylus'
    cssPath: '/img/'
    prefix: name
    margin: margin
  }

replaceSpriteClass = (file) ->
  if (file.isBuffer() && path.extname(file.path) == '.styl')
    file.contents = new Buffer(
      file.contents.toString().replace(/^\.([a-zA-Z].*)$/gm, "$1()")
    )

gulp.task 'sprite-icons', ->
  gulp.src(globs.sprites.icons)
    .pipe(sprite spriteOptions('icons') )
    .pipe($.tap replaceSpriteClass)
    .pipe($.if '*.png', gulp.dest(images_path), gulp.dest(stylus_sprites_path))

gulp.task 'sprite-panels', ->
  gulp.src(globs.sprites.panels)
    .pipe(sprite spriteOptions('panels') )
    .pipe($.tap replaceSpriteClass)
    .pipe($.if '*.png', gulp.dest(images_path), gulp.dest(stylus_sprites_path))

gulp.task 'sprite-social', ->
  gulp.src(globs.sprites.social)
    .pipe(sprite spriteOptions('social') )
    .pipe($.tap replaceSpriteClass)
    .pipe($.if '*.png', gulp.dest(images_path), gulp.dest(stylus_sprites_path))

gulp.task 'sprite-payments-deliveries', ->
  gulp.src(globs.sprites.paysDels)
    .pipe(sprite spriteOptions('payments-deliveries') )
    .pipe($.tap replaceSpriteClass)
    .pipe($.if '*.png', gulp.dest(images_path), gulp.dest(stylus_sprites_path))

gulp.task 'sprites', ['sprite-icons', 'sprite-panels', 'sprite-social',
  'sprite-payments-deliveries'
]
# }}}

# stylus-components: compile stylus components to css {{{
gulp.task 'stylus-components', ->
  gulp.src(app_path + 'components/*/*.styl')
    .pipe($.changed css_path, {extension: '.css'})
    .pipe($.cached 'stylusComponents')
    .pipe(
      $.if process.platform == 'darwin',
        $.plumber
          errorHandler: $.notify.onError
            title: 'Error compile STYLUS'
            message: '<%= error.message.split("\\n")[0].split("/stylus/")[1] %>'
        $.plumber()
    )
    .pipe( $.stylus
      use: [nib()]
      import: ['nib', '../../../public/stylus/_config.styl']
      url:
        name: 'embedurl'
    )
    .pipe($.remember 'stylusComponents')
    .pipe( gulp.dest css_path + 'components/' )
# }}}
# stylus-assets: compile stylus assets to css {{{
gulp.task 'stylus-assets', ->
  gulp.src([stylus_path + '+(blocks|assets|media|plugins)/*.+(styl|css)'])
    .pipe($.changed css_path, {extension: '.css'})
    .pipe($.cached 'stylusAssets')
    .pipe(
      $.if process.platform == 'darwin',
        $.plumber
          errorHandler: $.notify.onError
            title: 'Error compile STYLUS'
            message: '<%= error.message.split("\\n")[0].split("/stylus/")[1] %>'
        $.plumber()
    )
    .pipe($.stylus
      paths: [
        './public/img/_inline'
      ]
      use: [nib()]
      import: ['nib', '../_config.styl', '../core/*.styl',
        '../assets/sprites/*.styl']
      url:
        name: 'embedurl'
    )
    .pipe($.remember 'stylusAssets')
    .pipe(gulp.dest css_path)
# }}}
# stylus-main: compile main.styl to css {{{
gulp.task 'stylus-main', ->
  gulp.src(stylus_path + project + '.styl')
    .pipe($.cached 'stylusMain')
    .pipe(
      $.if process.platform == 'darwin',
        $.plumber
          errorHandler: $.notify.onError
            title: 'Error compile STYLUS',
            message: '<%= error.message.split("\\n")[0].split("/stylus/")[1] %>'
        $.plumber()
    )
    .pipe($.stylus
      define:
        import_tree_css: require 'stylus-import-tree-css'
    )
    .pipe($.remember 'stylusMain')
    .pipe(gulp.dest css_path)
# }}}
# stylus: compile stylus to css {{{
gulp.task 'stylus', (cb) ->
  seq ['stylus-assets', 'stylus-components'], 'stylus-main', cb
# }}}

# cssmin: minify css files and save to static server {{{
gulp.task 'cssmin', ->
  images_manifest = require(imagesmin_path + 'rev-manifest.json')
  fonts_manifest = false
  if fs.existsSync(fontsmin_path + 'rev-manifest.json')
    fonts_manifest = require(fontsmin_path + 'rev-manifest.json')

  gulp.src(globs.css_backend)
    # .pipe($.minifyCss {noAdvanced: true})
    .pipe($.fingerprint images_manifest,
      base: '/img/'
      prefix: '//' + static_host + '/img/'
    )
    .pipe($.rev())
    .pipe(gulp.dest(cssmin_path + 'backend'))
    .pipe($.gzip())
    .pipe(gulp.dest(cssmin_path + 'backend'))

  gulp.src(globs.css_backend_addition)
    .pipe($.minifyCss {noAdvanced: true})
    .pipe(gulp.dest(cssmin_path + 'backend/additional'))
    .pipe($.gzip())
    .pipe(gulp.dest(cssmin_path + 'backend/additional'))

  gulp.src(globs.css)
    .pipe($.minifyCss {noAdvanced: true})
    .pipe($.fingerprint images_manifest,
      base: '/img/'
      prefix: '//' + static_host + '/img/'
    )
    .pipe($.fingerprint fonts_manifest,
      base: '/fonts/'
      mode: 'replace'
      prefix: '//' + static_host + '/fonts/'
    )
    .pipe($.rev())
    .pipe(gulp.dest cssmin_path)
    .pipe($.size {title: 'Size minify css'})
    .pipe($.gzip())
    .pipe(gulp.dest cssmin_path)
    .pipe($.size {title: 'Size gzip css'})
# }}}

# coffeelint: lint coffeescript files {{{
gulp.task 'coffeelint', ->
  gulp.src(globs.coffee)
    .pipe( $.coffeelint() )
    .pipe(
      $.if process.platform == 'darwin', $.notify
        title: 'Error coffee linting'
        message: (file) ->
          return false if file.coffeelint.success

          errors = file.coffeelint.results.map( (data) ->
            if data.error
              return "(" + data.error.line + ':' + data.error.character +
              ') ' + data.error.reason
          ).join "\n"

          return file.relative + " (" + file.coffeelint.results.length +
          " errors)\n" + errors
    )
    .pipe( $.coffeelint.reporter() )
# }}}

# coffee: compile coffeescript to javascript {{{
gulp.task 'coffee', ->
  gulp.src(globs.coffee)
    .pipe($.cached 'coffee')
    .pipe($.plumber
      errorHandler: $.notify.onError
        title: 'Error compile coffeescript'
        message: '<%= error.message.split("\\n")[0].split("/coffee/")[1] %>'
    )
    .pipe($.coffee())
    .pipe($.remember 'coffee')
    .pipe(gulp.dest js_path)
# }}}

# watchify: watch and compile browserify {{{
gulp.task 'watchify', ->
  bundler = watchify(browserify
    entries: globs.coffee_app
    extensions: ['.coffee']
    debug: true
    # Required for watchify
    cache: {}
    packageCache: {}
    fullPaths: true
  )

  bundler.on 'update', ->
    $.util.log('Build browserify...')
    rebundle()

  rebundle = ->
    bundler
      .bundle()
      .on('error', (e) ->
        $.util.log 'Browserify error', e
        @emit 'end'
      )
      .pipe(source(project + '.js'))
      .pipe(gulp.dest js_path)

  rebundle()
# }}}

# jsmin: minify javascript files and save to static server {{{
gulp.task 'jsmin', ->
  gulp.src(globs.js_backend)
    .pipe($.plumber())
    .pipe($.uglify())
    .pipe(gulp.dest(jsmin_path + 'backend'))
    .pipe($.gzip())
    .pipe(gulp.dest(jsmin_path + 'backend'))

  $.requirejs(
    {
      baseUrl: './public/js/'
      name: '../components/external/almond/almond'
      mainConfigFile: './public/js/config.js'
      include: ['avtoclassika']
      insertRequire: ['avtoclassika']
      out: 'avtoclassika.js'
      paths: {
        jquery: 'plugins/jquery.terminator'
      }
    })
    .pipe($.size {title: 'Size source js'})
    .pipe($.uglify() )
    .pipe($.size {title: 'Size minimize js'})
    .pipe($.rev())
    .pipe(gulp.dest jsmin_path)
    .pipe($.gzip())
    .pipe(gulp.dest jsmin_path)
    .pipe($.size {title: 'Size gzip js'})
# }}}

# imgcopy: copy images from src {{{
gulp.task 'imgcopy', ->
  gulp.src(globs.images_src)
    .pipe(gulp.dest images_path)
# }}}

# imgmin: minify images and save to static server {{{
gulp.task 'imgmin', ->
  gulp.src(globs.images)
    .pipe($.size {title: 'Size source images'})
    .pipe($.if( (file) ->
      return false if path.extname(file.path) == '.jpg'
      return true
    $.imagemin
        pngquant: true
        progressive: true
        svgoPlugins: [{removeViewBox: false}]
    mozjpeg()()
    ))
    .pipe($.size {title: 'Size minimize images'})
    .pipe($.rev())
    .pipe(gulp.dest imagesmin_path)
    .pipe($.rev.manifest())
    .pipe(gulp.dest imagesmin_path)
# }}}

### html-minifier dont work correct
# tpl-general-min: minify general tpl files {{{
gulp.task 'tpl-general-min', ->
  images_manifest = require(imagesmin_path + 'rev-manifest.json')

  gulp.src(globs.html.general)
    .pipe($.size {title: 'Size general templates'})
    .pipe($.changed app_build_views_path)
    .pipe($.htmlmin
      removeComments: true
      collapseWhitespace: true
      conservativeCollapse: true
      removeAttributeQuotes: true
      removeScriptTypeAttributes: true
      removeStyleLinkTypeAttributes: true
      customAttrSurround: [
        [/{{IF.+}}/, /{{END IF}}/]
      ]
    )
    .pipe($.fingerprint images_manifest,
      base: '/img/',
      prefix: '//' + static_host + '/img/'
    )
    .pipe(gulp.dest app_build_views_path)
    .pipe($.size {title: 'Size mininized general templates'})
# }}}
# tpl-components-min: minify components tpl files {{{
gulp.task 'tpl-components-min', ->
  images_manifest = require(imagesmin_path + 'rev-manifest.json')

  gulp.src(globs.html.components)
    .pipe($.size {title: 'Size components templates'})
    .pipe($.changed app_build_views_path)
    .pipe($.htmlmin
      removeComments: true
      collapseWhitespace: true
      conservativeCollapse: true
      removeAttributeQuotes: true
      removeScriptTypeAttributes: true
      removeStyleLinkTypeAttributes: true
      customAttrSurround: [
        [/{{IF.+}}/, /{{END IF}}/]
      ]
    )
    .pipe($.fingerprint images_manifest,
      base: '/img/'
      prefix: '//' + static_host + '/img/'
    )
    .pipe(gulp.dest(app_build_views_path + 'components/'))
    .pipe($.size {title: 'Size mininized components templates'})
# }}}
# tpl-modules-min: minify modules tpl files {{{
gulp.task 'tpl-modules-min', ->
  images_manifest = require(imagesmin_path + 'rev-manifest.json')

  gulp.src(globs.html.modules)
    .pipe($.size {title: 'Size modules templates'})
    .pipe($.changed app_build_views_path)
    .pipe($.htmlmin
      removeComments: true
      collapseWhitespace: true
      conservativeCollapse: true
      removeAttributeQuotes: true
      removeScriptTypeAttributes: true
      removeStyleLinkTypeAttributes: true
      customAttrSurround: [
        [/\{\{\w+\([\w,\s'0-9]+/, /\)\}\}/]
        [/{{IF\s+\w+}}/, /{{END IF}}/]
      ]
    )
    .pipe($.fingerprint images_manifest,
      base: '/img/'
      prefix: '//' + static_host + '/img/'
    )
    .pipe(gulp.dest(app_build_views_path + 'modules/'))
    .pipe($.size {title: 'Size mininized modules templates'})
# }}}
###
# tpl-general-min: minify general tpl files {{{
gulp.task 'tpl-general-min', ->
  images_manifest = require(imagesmin_path + 'rev-manifest.json')

  gulp.src(globs.html.general)
    .pipe($.size {title: 'Size general templates'})
    .pipe($.changed app_build_views_path)
    .pipe($.compressor {'remove-intertag-spaces': true})
    .pipe($.fingerprint images_manifest,
      base: '/img/'
      prefix: '//' + static_host + '/img/'
    )
    .pipe(gulp.dest app_build_views_path)
    .pipe($.size {title: 'Size mininized general templates'})
# }}}
# tpl-components-min: minify components tpl files {{{
gulp.task 'tpl-components-min', ->
  images_manifest = require(imagesmin_path + 'rev-manifest.json')

  gulp.src(globs.html.components)
    .pipe($.size {title: 'Size components templates'})
    .pipe($.changed app_build_views_path)
    .pipe($.compressor {'remove-intertag-spaces': true})
    .pipe($.fingerprint images_manifest,
      base: '/img/'
      prefix: '//' + static_host + '/img/'
    )
    .pipe(gulp.dest(app_build_views_path + 'components/'))
    .pipe($.size {title: 'Size mininized components templates'})
# }}}
# tpl-modules-min: minify modules tpl files {{{
gulp.task 'tpl-modules-min', ->
  images_manifest = require(imagesmin_path + 'rev-manifest.json')

  gulp.src(globs.html.modules)
    .pipe($.size {title: 'Size modules templates'})
    .pipe($.changed app_build_views_path)
    .pipe($.compressor {'preserve-intertag-spaces': true})
    .pipe($.fingerprint images_manifest,
      base: '/img/'
      prefix: '//' + static_host + '/img/'
    )
    .pipe(gulp.dest(app_build_views_path + 'modules/'))
    .pipe($.size {title: 'Size mininized modules templates'})
# }}}

# tplmin: minify all tpl files {{{
gulp.task 'tplmin', (cb) ->
  seq 'tpl-general-min', 'tpl-components-min', 'tpl-modules-min', cb
# }}}

# fontsmin: fonts files save to static server {{{
gulp.task 'fontsmin', ->
  gulp.src(globs.fonts)
    .pipe( $.size {title: 'Size fonts'} )
    .pipe( $.rev() )
    .pipe( gulp.dest fontsmin_path )
    .pipe( $.rev.manifest() )
    .pipe( gulp.dest fontsmin_path )

  gulp.src(globs.fonts)
    .pipe( $.rev() )
    .pipe( $.gzip() )
    .pipe( gulp.dest fontsmin_path )
    .pipe( $.size {title: 'Size gzip fonts'} )
# }}}

# flo: livereload with fb-flo {{{
gulp.task 'flo', (done) ->
  flo('public/',
    port: 8888
    host: 'localhost'
    verbose: false
    glob: [
      'css/**/*.css'
      'js/**/*.js'
      '../application/**/*.tpl'
    ], (filepath, cb) ->
      $.util.log('Reloading "', filepath, '" with flo...')

      cb
        resourceURL: '/' + filepath
        contents: fs.readFileSync('./public/' + filepath).toString()
        update: (_window, _resourceURL) ->
          console.log('Resource ' + _resourceURL
            + ' has just been updated with new content')
        reload: filepath.match(/\.(js|tpl|php)$/)
  ).once 'ready', done
# }}}

# dev: watching changed file and reload browser {{{
gulp.task 'dev', ['stylus', 'coffee'], (done) ->
  assetsPath = stylus_path + '+(blocks|assets|media|plugins)/*.+(styl|css)'
  watcherStylusAssets = gulp.watch assetsPath, ['stylus-assets']
  watcherStylusAssets.on 'change', (ev) ->
    if ev.type == 'deleted'
      delete $.cached.caches.stylusAssets[ev.path]
      $.remember.forget 'stylusAssets', ev.path

  componentsPath = app_path + 'components/*/*.styl'
  watcherStylusComp = gulp.watch componentsPath, ['stylus-components']
  watcherStylusComp.on 'change', (ev) ->
    if ev.type == 'deleted'
      delete $.cached.caches.stylusComponents[ev.path]
      $.remember.forget 'stylusComponents', ev.path

  coffeePath = coffee_path + '**/*.coffee'
  watcherCoffee = gulp.watch coffeePath, ['coffee']
  watcherCoffee.on 'change', (ev) ->
    if ev.type == 'deleted'
      delete $.cached.caches.coffee[ev.path]
      $.remember.forget 'coffee', ev.path

  # gulp.start 'flo'

  done()
# }}}

gulp.task 'rebuild', (cb) ->
  seq 'svg-build', 'sprites', 'stylus', 'coffee', 'backend',
  'imgcopy', 'imgmin', 'tplmin', 'fontsmin', 'cssmin', 'jsmin', cb

gulp.task 'build', (cb) ->
  seq 'clean', 'rebuild', cb

gulp.task 'default', ['help']
