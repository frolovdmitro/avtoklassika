{{includeScript('header.tpl')}}
{{includeBlock('slider', 'slider', 'slider')}}
{{includeBlock('cars', 'cars', 'carsCatalogue')}}
{{includeBlock('cars', 'cars', 'newDetails')}}
{{includeBlock('default', 'index', 'benefits')}}


<div class="container">
  <section id="announcing">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 pad_0 mob_hidden">
          <div class="announcing">
            <h2 class="title_3"> {{lng_index_seo_header}}</h2>
            <div class="info">{{lng_index_seo_text}}</div>
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 pad_0">
          {{includeBlock('adverts', 'adverts', 'advertsBar')}}
        </div>
      </div>
    </div>
  </section>
</div>

{{includeScript('footer.tpl')}}
