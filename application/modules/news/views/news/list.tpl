{{includeScript('header.tpl')}}

<div class="container">
  <section id="news" class="">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="news clearfix">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
              <h1 class="title_3 top_0">{{lng_our_news}}</h1>
              <nav class="paging" data-paging data-pages="{{pages}}" data-url="/json/news/{{IF category_synonym}}{{category_synonym}}/{{END IF}}{{IF car_synonym}}{{car_synonym}}/{{END IF}}page.html" data-content="#page-content"></nav>
              <div id="page-content"></div>
              <nav class="paging" data-paging data-pages="{{pages}}" data-url="/json/news/{{IF category_synonym}}{{category_synonym}}/{{END IF}}{{IF car_synonym}}{{car_synonym}}/{{END IF}}page.html" data-content="#page-content"></nav>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden">
                {{includeBlock('news', 'news', 'categories')}}
                {{includeBlock('directories', 'directories', 'banner', 'car')}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

{{includeScript('footer.tpl')}}
