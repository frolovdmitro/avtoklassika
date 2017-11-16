{{includeScript('header.tpl')}} 


<div class="container">
  <section id="car_park" class="car_park">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="catalog catalog_slider">
            <h1 class="title_catalog"> {{lng_header_text}} {{name_nobr}} </h1>
            {{includeBlock('cars', 'cars', 'slider', id)}}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>



<div class="container">
  <section id="car_park_level" class="car_park">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 pad_0">
          <div id="breadcrumb">
            <nav class="breadcrumb">
              <a class="breadcrumb-item" href="/" itemprop="url">{{lng_autoparts}}</a>
              <span class="breadcrumb-item active">{{name_nobr}}</span>
            </nav>
          </div>
          <div class="filter_body center park_level">
            {{includeBlock('cars', 'cars', 'autoparts', id)}}
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 pad_0 mob_hidden" id="auto_section">
            <div class="car-autoparts-tree car-autoparts-tree_align_right">
                {{includeBlock('cars', 'cars', 'treeAutoparts', id)}}
            </div>
        </div>
      </div>
    </div>
  </section>
</div>


<div class="container">
    <section id="slider_new" class="separator">
        <div class="row">
            <div class="container_center clearfix pad_0">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
                    {{includeBlock('cars', 'cars', 'carHotDeals', id)}}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden">
                    {{includeBlock('directories', 'directories', 'banner', 'index_hot')}}
                </div>
            </div>
        </div>
    </section>
</div>


{{IF seo_header}}
<div class="container mob_hidden">
    <section id="product_info">
        <div class="row">
            <div class="container_center clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
                    <div class="product_info">
                        <h1 class="title">{{seo_header}}</h1>
                        <div class="group clearfix">
                            <div class="image">
                                <div class="img">
                                    {{IF seo_image}}
                                    <img src="{{url_staticServer}}{{seo_image}}" alt="{{name}}">
                                    {{END IF}}
                                </div>
                            </div>
                            <div class="content">
                                {{seo_text}}
                            </div>
                            <span class="documentation">
                                <a href="/car/{{synonym}}/documentations/"> {{lng_docs}} </a>
                            </span>
                            <span class="price_list">
                                <a href="/car/{{synonym}}/price/"> {{lng_download_price}} </a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
{{END IF}}


{{includeScript('footer.tpl')}}