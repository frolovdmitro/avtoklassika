<!doctype html><!--[if IE 8]><html data-placeholder-focus="false" class="no-js ie8 oldie{{IF mode_teaser}} teaser{{END mode_teaser}}" lang="{{current_language}}-{{upper_current_language}}"><![endif]--><!--[if IE 9]><html data-placeholder-focus="false" class="no-js ie9 oldie{{IF mode_teaser}} teaser{{END mode_teaser}}" lang="{{current_language}}-{{upper_current_language}}"><![endif]--><!--[if gt IE 9]><!--><html data-placeholder-focus="false" class="no-js" lang="{{current_language}}-{{upper_current_language}}"><!--<![endif]--><head><meta charset="UTF-8"><title>{{title}}</title><meta name="description" content="{{description}}"><meta name="keywords" content="{{keywords}}"><meta name="viewport" content="width=1000">
  {{IF module}}
    <meta name="app-route" content="{{module}}">
    {{IF module == 'ads'}}
    <link rel="canonical" href="http://avtoclassika.com/ads/{{type}}/{{ID}}"/>
    {{END IF}}
  {{END IF}}

  {{UNLESS robots}} <meta name="robots" content="index, follow"> {{ELSE}} {{IF robots == 'noindexfollow'}} <meta name="robots" content="noindex, follow"> {{ELSE}} {{IF robots == 'indexnofollow'}} <meta name="robots" content="index, nofollow"> {{ELSE}} {{IF robots == 'noindexnofollow'}} <meta name="robots" content="noindex, nofollow"> {{ELSE}} <meta name="robots" content="index, follow"> {{END IF}} {{END IF}} {{END IF}} {{END UNLESS}} <link rel="stylesheet" href="{{url_css}}{{IF backend}}backend/{{css_backend_main}}{{END backend}}{{UNLESS backend}}{{css_main}}{{END backend}}"> {{IF addition_css}} <link rel="stylesheet" href="{{addition_css}}"> {{END IF}} <meta property="uwin:staticServer" content="{{url_staticServer}}"><meta property="uwin:serverName" content="{{servername}}"><style type="text/css">
    .products-list_type_search .products-list__item:nth-child(3n) {
      margin-right: 7px;
    }
    .products-list_type_search .products-list__item:nth-child(4n) {
      margin-right: 0;
    }

    .basket-sidebar__products {
      text-align: right;
      margin-bottom: 15px;
      overflow: hidden;
    }

    .basket-sidebar__products-caption {
      font-size: 18px;
      color: #6e7c6e;
      padding-left: 20px;
      position: relative;
      margin-top: 5px;
      margin-bottom: 0;
    }

    .basket-products__products-cost {
      background-image: url(//s1.avtoclassika.com/img/_icons-7508c84f.png);
      background-position: -10px -2434px;
      width: 99px;
      height: 29px;
      background-repeat: no-repeat;
      color: #6e7c6e;
      display: inline-block;
      text-align: center;
      font: 700 18px/27px Sensation;
      padding-top: 2px;
      -webkit-font-smoothing: antialiased;
    }
  </style> {{IF current_language == 'en'}} <link rel="canonical" href="http://autoclassics.us{{current_url}}"/> {{END IF}} {{stg_code_head}} </head><div id="fb-root"></div>
