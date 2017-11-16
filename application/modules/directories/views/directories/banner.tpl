{{BEGIN index_slider}}
<div class="important-panel__ad ad-panel">
    <a class="ad-panel__link" {{IF autopart_request}}href="/json/request-autopart.html" rel="modal:open"{{ELSE}}href="{{url}}"{{if(target_blank, ' target="_blank"')}}{{END IF}}>
        <img class="ad-panel__image" src="{{url_staticServer}}{{file}}">
    </a>
</div>
{{END index_slider}}

{{BEGIN index_hot}}
<div class="banner">
    <div class="img">
        <a {{IF autopart_request}}href="/json/request-autopart.html" rel="modal:open"{{ELSE}}href="{{url}}"{{if(target_blank, ' target="_blank"')}}{{END IF}}>
            <img alt="banner" src="{{url_staticServer}}{{file}}">
        </a>
    </div>
</div>
{{END index_hot}}

{{BEGIN car}}
<div class="banner">
    <div class="img">
        <a {{IF autopart_request}}href="/json/request-autopart.html" rel="modal:open"{{ELSE}}href="{{url}}"{{if(target_blank, ' target="_blank"')}}{{END IF}}>
            <img alt="banner" src="{{url_staticServer}}{{file}}">
        </a>
    </div>
</div>
{{END car}}

{{BEGIN adverts}}
<div class="banner mob_hidden">
    <div class="img">
        <a {{IF autopart_request}}href="/json/request-autopart.html" rel="modal:open"{{ELSE}}href="{{url}}"{{if(target_blank, ' target="_blank"')}}{{END IF}}>
            <img alt="banner" src="{{url_staticServer}}{{file}}">
        </a>
    </div>
</div>
{{END adverts}}
