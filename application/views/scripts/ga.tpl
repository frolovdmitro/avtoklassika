{{IF stg_google_analytics_enabled}} <script>
    var _gaq=[['_setAccount','{{stg_google_analytics_code}}'],['_trackPageview']];
    (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
    g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
    s.parentNode.insertBefore(g,s)}(document,'script'));
</script> {{END stg_google_analytics_enabled}}
