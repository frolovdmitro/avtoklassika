{{includeScript('header.tpl')}}
<div class="container">
  <section id="car_park" class="car_park">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="catalog catalog_slider">
            <h1 class="title_catalog"> {{lng_documentation}} {{name}} </h1>
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
              <a class="breadcrumb-item" href="/car/{{synonym}}/" itemprop="url">{{name}}</a>
              <span class="breadcrumb-item active">{{lng_documentation}}</span>
            </nav>
          </div>
          <div class="filter_body center park_level">
            <ul class="docs-list">
                {{BEGIN docs}}
              <li class="docs-list__item"><a class="docs-list__link" href="/car/{{car_synonym}}/documentations/{{synonym}}/">{{name}}</a></li>
                {{END docs}}
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 pad_0 mob_hidden" id="auto_section">
            {{includeBlock('directories', 'directories', 'banner', 'car')}}
          <div class="car-autoparts-tree car-autoparts-tree_align_right">
              {{includeBlock('cars', 'cars', 'treeAutoparts', id)}}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

{{includeScript('footer.tpl')}}
