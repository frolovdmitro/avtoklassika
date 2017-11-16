{{includeScript('head.tpl')}} <body><div class="body-wrap"> {{content()}} </div><script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/{{current_language}}_{{fullupper_current_language}}/all.js#xfbml=1";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script><script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//vk.com/js/api/openapi.js?111";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'vk-jssdk'));</script><script type="text/javascript">
    window.___gcfg = {lang: '{{current_language}}'};

    (function() {
      var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
      po.src = 'https://apis.google.com/js/platform.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
  </script> {{includeScript('js.tpl')}} </body></html>
