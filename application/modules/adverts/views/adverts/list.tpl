{{includeScript('header.tpl')}} 

<div class="container">
  <section id="show_bill_all" class="">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <h1 class="title_3 top_0"> {{lng_our_adverts}} </h1>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="show_bill_all clearfix">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0 mob_visible_768">
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
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden_768">
                {{includeBlock('directories', 'directories', 'banner', 'adverts')}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
{{includeScript('footer.tpl')}}
