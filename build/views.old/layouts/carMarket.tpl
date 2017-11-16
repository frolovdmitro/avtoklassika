<!doctype html>
<html lang="{{current_language}}-{{ucfirst(current_language)}}">
<head>
  <meta charset="UTF-8">
  <title>{{IF data.title}}{{data.title}}{{ELSEIF data}}{{tmpl(title, data, true)}}{{ELSEIF title}}{{title}}{{ELSE}}{{name}}{{END IF}}{{IF data.page}}{{IF data.page != 1}} {{lng_page}} {{data.page}}{{END IF}}{{END IF}}</title>
  <meta name="description" content="{{IF data.description}}{{html_entity_decode(data.description)}}{{ELSEIF data}}{{tmpl(description, data, true)}}{{ELSE}}{{html_entity_decode(description)}}{{END IF}}">
  <meta name="keywords" content="{{IF data.keywords}}{{html_entity_decode(data.keywords)}}{{ELSEIF data}}{{tmpl(keywords, data, true)}}{{ELSE}}{{html_entity_decode(keywords)}}{{END IF}}">
  <meta name="viewport" content="width=device-width">
  <meta property="og:site_name" content="avtoclassika.com">
  <meta property="og:type" content="article">
  {{IF image}}
    <meta property="og:image" content="http://s1.avtoclassika.com{{json(image, 'original.path')}}">
  {{ELSE}}
    <meta property="og:image" content="http://avtoclassika.com/img/logo.png">
  {{END IF}}
  <meta property="og:title" content="{{IF data.title}}{{data.title}}{{ELSEIF data}}{{tmpl(title, data, true)}}{{ELSEIF title}}{{title}}{{ELSE}}{{name}}{{END IF}}{{IF data.page}}{{IF data.page != 1}} {{lng_page}} {{data.page}}{{END IF}}{{END IF}}">
  <meta property="og:description" content="{{IF data.description}}{{html_entity_decode(data.description)}}{{ELSEIF data}}{{tmpl(description, data, true)}}{{ELSE}}{{html_entity_decode(description)}}{{END IF}}">

  <link rel="manifest" href="/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="/img/favicons/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <link rel="stylesheet" href="/css/carmarket.css">
</head>
<body>
111
</body>
</html>
