{{includeScript('header.tpl')}}

<div class="layout__content autoparts">
  <h1 class="autoparts__h1 ribbon ribbon_type_right-inner">{{lng_all_autoparts}} {{name}}</h1>
  <ul class="breadcrumbs">
    <li class="breadcrumbs__item breadcrumbs__item_type_home" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"> <a class="breadcrumbs__link" href="/" itemprop="url"> <span itemprop="title">{{lng_autoparts}}</span> </a> </li>
    <li class="breadcrumbs__item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"> <a class="breadcrumbs__link" itemprop="url" href="/car/{{car_synonym}}/"> <span itemprop="title">{{car_name_nobr}}</span> </a> </li>
    <li class="breadcrumbs__item">{{lng_all_autoparts}}</li>
  </ul>
  <div class="layout__content-with-sidebar">
    <div class="filter-bar">
      <ul class="filter-bar__block filter-bar__block_align_left">
        <li class="filter-bar__item filter-bar__item_align_left"> <a data-type="products" data-name="restaurare" class="form__virtual-checkbox" href="#type=restaurare"> {{lng_restaurare}} </a> </li>
        <li class="filter-bar__item filter-bar__item_align_left"> <a data-type="products" data-name="replica" class="form__virtual-checkbox" href="#type=replica"> {{lng_replica}} </a> </li>
        <li class="filter-bar__item filter-bar__item_align_left"> <a data-type="products" data-name="secondhand" class="form__virtual-checkbox" href="#type=secondhand"> {{lng_secondhand}} </a> </li>
        <li class="filter-bar__item filter-bar__item_align_left"> <a data-type="products" data-name="new" class="form__virtual-checkbox" href="#new=true"> {{lng_new}} </a> </li>
      </ul>
      <ul class="filter-bar__block filter-bar__block_align_right">
        <li class="filter-bar__item filter-bar__item_align_right"> <span class="form__icons form__icons_type_tiles"> {{lng_tiles}} </span> </li>
      </ul>
    </div>
    <nav class="paging" data-paging data-pages="{{pages}}" data-url="/json/car-all/{{car_id}}/page.html" data-content="#page-content"> </nav>
    <div id="page-content"> {{includeBlock('cars', 'cars', 'pageContent', true)}} </div>
    <nav class="paging" data-paging data-pages="{{pages}}" data-url="/json/car-all/{{car_id}}/page.html" data-content="#page-content"> </nav>
  </div>
  <div style="float:right;">
    <div class="car-autoparts-tree car-autoparts-tree_type_autoparts" style="float: none;"> {{includeBlock('cars', 'cars', 'treeAutoparts', car_id)}} </div>
    {{UNLESS deals_exists}} {{includeBlock('directories', 'directories', 'banner', 'car')}} {{END UNLESS}}
  </div>
</div>
{{includeScript('footer.tpl')}}
