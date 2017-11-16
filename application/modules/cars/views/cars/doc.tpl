{{includeScript('header.tpl')}} 
<div class="cars-slider__wrap">
  <h1 class="cars-slider__header ribbon ribbon_type_double">{{name}}</h1>
  {{includeBlock('cars', 'cars', 'slider', car_id)}}
</div>
<div class="layout__content">
  <ul class="breadcrumbs">
    <li class="breadcrumbs__item breadcrumbs__item_type_home" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link" href="/" itemprop="url"><span itemprop="title">{{lng_autoparts}}</span></a></li>
    <li class="breadcrumbs__item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link" href="/car/{{car_synonym}}/" itemprop="url"><span itemprop="title">{{car_name}}</span></a></li>
    <li class="breadcrumbs__item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link" href="/car/{{car_synonym}}/documentations/" itemprop="url"><span itemprop="title">{{lng_documentation}}</span></a></li>
    <li class="breadcrumbs__item">{{name}}</li>
  </ul>
  <div class="layout__content-with-sidebar layout__content-with-sidebar_margin_top"> {{text}} </div>
  <div class="layout__sidebar layout__sidebar_type_doc">
    <div class="car-autoparts-tree"> {{includeBlock('cars', 'cars', 'treeAutoparts', car_id)}} </div>
    {{includeBlock('directories', 'directories', 'banner', 'car')}}
  </div>
</div>
{{includeScript('footer.tpl')}}
