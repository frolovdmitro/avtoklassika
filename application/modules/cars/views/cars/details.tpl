{{includeScript('header.tpl')}}
<div class="container">
  <section id="car_park" class="car_park">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="catalog catalog_slider">
            <h1 class="title_catalog"> {{lng_header_text}} {{car_name_nobr}} </h1>
            {{includeBlock('cars', 'cars', 'slider', car_id)}}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="container">
  <section id="car_park_level" class="car_park level_2">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 pad_0">
          <h1 class="title_3 top_0">{{name}}</h1>
          <div id="breadcrumb">
            <nav class="breadcrumb">
              <a class="breadcrumb-item" href="/" itemprop="url">{{lng_autoparts}}</a>
              <a class="breadcrumb-item" itemprop="url" href="/car/{{car_synonym}}/">{{car_name_nobr}}</a>
              <span class="breadcrumb-item active">{{name}}</span>
            </nav>
          </div>
            <div class="filter-bar">
              <ul class="filter-bar__block filter-bar__block_align_left">
                <li class="filter-bar__item filter-bar__item_align_left">
                  <a data-type="products" data-name="restaurare" class="form__virtual-checkbox" href="#type=restaurare"> {{lng_restaurare}} </a>
                </li>
                <li class="filter-bar__item filter-bar__item_align_left">
                  <a data-type="products" data-name="replica" class="form__virtual-checkbox" href="#type=replica"> {{lng_replica}} </a>
                </li>
                <li class="filter-bar__item filter-bar__item_align_left">
                  <a data-type="products" data-name="secondhand" class="form__virtual-checkbox" href="#type=secondhand"> {{lng_secondhand}} </a>
                </li>
                <li class="filter-bar__item filter-bar__item_align_left">
                  <a data-type="products" data-name="new" class="form__virtual-checkbox" href="#new=true"> {{lng_new}} </a>
                </li>
              </ul>
              <ul class="filter-bar__block filter-bar__block_align_right">
                {{IF schema}}
                  <li class="filter-bar__item filter-bar__item_align_right">
                    <a class="filter-bar__button filter-bar__button_state_checked" href="#" data-state="hide" data-hide-lng="{{lng_hide_schema}}" data-show-lng="{{lng_show_schema}}"> {{lng_hide_schema}} </a>
                  </li>
                {{END IF}}
              </ul>
            </div>
          <nav class="paging" data-paging data-pages="{{pages}}" data-url="/json/car/{{id}}/page.html" data-content="#page-content"> </nav>
          <div id="page-content">
            {{includeBlock('cars', 'cars', 'pageContent')}}
          </div>
          <nav class="paging" data-paging data-pages="{{pages}}" data-url="/json/car/{{id}}/page.html" data-content="#page-content"> </nav>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 pad_0 mob_hidden" id="auto_section">
          <div class="car-autoparts-tree car-autoparts-tree_align_right">
            {{includeBlock('cars', 'cars', 'treeAutoparts', car_id)}}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

{{IF deals_exists}}
  <div class="container">
    <section id="slider_new" class="separator">
      <div class="row">
        <div class="container_center clearfix">
          <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 pad_0">
            {{includeBlock('cars', 'cars', 'carHotDeals', car_id)}}
          </div>
          <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 pad_0 mob_hidden">
              {{includeBlock('directories', 'directories', 'banner', 'car')}}
          </div>
        </div>
      </div>
    </section>
  </div>
  {{IF seo_text}}
    <div class="container mob_hidden">
      <section id="product_info">
        <div class="row">
          <div class="container_center clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
              <div class="product_info">
                {{IF seo_header}}
                  <h1 class="title left_10"> {{seo_header}} </h1>
                {{END IF}}
                <div class="group clearfix">
                  <div class="content">
                    <p>{{seo_text}}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  {{END IF}}
{{END IF}}



{{includeScript('footer.tpl')}}
