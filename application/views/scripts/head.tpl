<!doctype html>
<!--[if IE 8]><html data-placeholder-focus="false" class="no-js ie8 oldie{{IF mode_teaser}} teaser{{END mode_teaser}}" lang="{{current_language}}-{{upper_current_language}}"><![endif]-->
<!--[if IE 9]><html data-placeholder-focus="false" class="no-js ie9 oldie{{IF mode_teaser}} teaser{{END mode_teaser}}" lang="{{current_language}}-{{upper_current_language}}"><![endif]-->
<!--[if gt IE 9]><!--><html data-placeholder-focus="false" class="no-js" lang="{{current_language}}-{{upper_current_language}}"><!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <title>{{title}}</title>
    <meta name="description" content="{{description}}">
    <meta name="keywords" content="{{keywords}}">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    {{IF module}}
    <meta name="app-route" content="{{module}}">
    {{END IF}}
    {{UNLESS robots}}
    <meta name="robots" content="index, follow">
    {{ELSE}}
    {{IF robots == 'noindexfollow'}}
    <meta name="robots" content="noindex, follow">
    {{ELSE}}
    {{IF robots == 'indexnofollow'}}
    <meta name="robots" content="index, nofollow">
    {{ELSE}}
    {{IF robots == 'noindexnofollow'}}
    <meta name="robots" content="noindex, nofollow">
    {{ELSE}}
    <meta name="robots" content="index, follow">
    {{END IF}}
    {{END IF}}
    {{END IF}}
    {{END UNLESS}}
    <link rel="stylesheet" href="{{url_css}}{{IF backend}}backend/{{css_backend_main}}{{END backend}}{{UNLESS backend}}{{css_main}}{{END backend}}">
    {{IF addition_css}}
    <link rel="stylesheet" href="{{addition_css}}">
    {{END IF}}
    <meta property="uwin:staticServer" content="{{url_staticServer}}">
    <meta property="uwin:serverName" content="{{servername}}">
    <style type="text/css">
        .products-list_type_search .products-list__item:nth-child(3n) {
            margin-right: 7px;
        }
        .products-list_type_search .products-list__item:nth-child(4n) {
            margin-right: 0;
        }
    </style>
    <script src="/components/local/swipebox/assets/js/jquery.min.js"></script>
    <script src="/components/local/swipebox/assets/js/bootstrap.js"></script>
    <script src="/components/local/swipebox/assets/js/wow.js"></script>
    <script src="/components/local/swipebox/assets/js/socialite.min.js"></script>
    <script src="/components/local/swipebox/assets/js/owl.carousel.js"></script>
    <script src="/components/local/swipebox/assets/js/isotope2.js"></script>
    <script src="/components/local/swipebox/assets/js/simplebox.js"></script>
    <script src="/components/local/swipebox/assets/js/simplebox_util.js"></script>
    <script src="/components/local/swipebox/assets/js/miniset.js"></script>
    <script src="/components/local/swipebox/assets/js/jquery.formstyler.js"></script>

    <link href="/components/local/swipebox/assets/css/jquery.formstyler.css" rel="stylesheet">
    <link href="/components/local/swipebox/assets/css/miniset.css" rel="stylesheet">
    <link href="/components/local/swipebox/assets/css/simplebox.css" rel="stylesheet">
    <link href="/components/local/swipebox/assets/css/animate.css" rel="stylesheet">
    <link href="/components/local/swipebox/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/components/local/swipebox/assets/css/owl.theme.default.css" rel="stylesheet">
    <link href="/components/local/swipebox/assets/css/owl.carousel.css" rel="stylesheet">
    <link href="/components/local/swipebox/assets/css/style.css" rel="stylesheet">
</head>
{{stg_code_head}}
</head>

