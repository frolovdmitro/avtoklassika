{{IF backend}} <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script><script>window.jQuery || document.write('<script src="/js/libs/jquery-1.7.2.js"><\/script>')</script><script src="{{url_js}}backend/libs/shortcut.js"></script><script src="{{url_js}}backend/libs/jquery.contenteditable.js"></script><script src="{{url_js}}backend/libs/uwinTable.js"></script><script src="{{url_js}}backend/libs/jquery.form.js"></script><script src="{{url_js}}backend/libs/spin.min.js"></script><script src="{{url_js}}backend/libs/raphael-min.js"></script><script src="{{url_js}}backend/libs/morris.min.js"></script><script src="{{url_js}}backend/libs/dragndrop.js"></script><script src="{{url_js}}backend/libs/codemirror/lib/codemirror.js"></script><script src="{{url_js}}backend/libs/codemirror/lib/util/formatting.js"></script><script src="{{url_js}}backend/libs/codemirror/mode/css/css.js"></script><script src="{{url_js}}backend/libs/codemirror/mode/htmlmixed/htmlmixed.js"></script><script src="{{url_js}}backend/libs/codemirror/mode/xml/xml.js"></script><script src="{{url_js}}backend/main.js"></script> {{ELSE}} {{UNLESS minify_enabled}} <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script><script data-main="/js/config.js" src="/components/external/requirejs/require.js"></script> {{ELSE}} <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script><script src="{{url_js}}{{js_main}}"></script> {{END minify_enabled}}
<script>
if ($('#basket-page-content').length > 0) {
  $('#basket-page-content').on('submit', '#user-data__form', function(ev) {
    $('.basket-user-info').css('display', 'none');
    $('#form-page-2').css('display', 'block');
    window.scrollTo(0, 0);
  });
}
</script>
<script src="//s1.avtoclassika.com/js/uwin.stats-e69dcda4.js"></script>  {{END backend}}
