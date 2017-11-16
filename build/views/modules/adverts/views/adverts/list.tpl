{{includeScript('header.tpl')}} 
<div class="layout__content news">
  <div class="layout__content-with-sidebar">
    <h1 class="autoparts__h1 ribbon ribbon_type_right-inner"> {{lng_our_adverts}} </h1>
    {{includeBlock('adverts', 'adverts', 'filterBar')}} 
    <nav class="paging" id="nav-top" data-paging data-pages="{{pages}}" data-url="/json/ads/page.html" data-content="#page-content">
      <ul class="paging__list">{{pagestoshow}}</ul>
    </nav>

    <div id="page-content">
      {{advertsList}}
    </div>

    <nav class="paging"  id="nav-foot"  data-paging data-pages="{{pages}}" data-url="/json/ads/page.html" data-content="#page-content">
      <ul class="paging__list">{{pagestoshow}}</ul>
    </nav>
  </div>
  <div class="layout__sidebar">
    <a id="add_adv" class="ad__add-button" href="/json/ads/add-form.html">
      {{lng_add_advert}}
    </a>
    {{includeBlock('directories', 'directories', 'banner', 'adverts')}}
  </div>
</div>
{{includeScript('footer.tpl')}}
