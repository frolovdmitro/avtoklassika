{{IF backend}}
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="/js/libs/jquery-1.7.2.js"><\/script>')</script>
<script src="{{url_js}}backend/libs/shortcut.js"></script>
<script src="{{url_js}}backend/libs/jquery.contenteditable.js"></script>
<script src="{{url_js}}backend/libs/uwinTable.js"></script>
<script src="{{url_js}}backend/libs/jquery.form.js"></script>
<script src="{{url_js}}backend/libs/spin.min.js"></script>
<script src="{{url_js}}backend/libs/raphael-min.js"></script>
<script src="{{url_js}}backend/libs/morris.min.js"></script>
<script src="{{url_js}}backend/libs/dragndrop.js"></script>
<script src="{{url_js}}backend/libs/codemirror/lib/codemirror.js"></script>
<script src="{{url_js}}backend/libs/codemirror/lib/util/formatting.js"></script>
<script src="{{url_js}}backend/libs/codemirror/mode/css/css.js"></script>
<script src="{{url_js}}backend/libs/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="{{url_js}}backend/libs/codemirror/mode/xml/xml.js"></script>
<script src="{{url_js}}backend/main.js"></script>
{{ELSE}}
{{UNLESS minify_enabled}}
<script data-main="/js/config.js" src="/components/external/requirejs/require.js"></script>
{{ELSE}}
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<!-- <script src="{{url_js}}{{js_main}}"></script> -->
<script src="/components/local/swipebox/assets/js/autoclassica.js"></script>
{{END minify_enabled}}
{{stg_code_js}}
{{END backend}}
