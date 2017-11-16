{{includeScript('header.tpl')}} <div class="cars-slider__wrap"> <h1 class="cars-slider__header ribbon ribbon_type_double"> {{lng_header_text}} {{name_nobr}} </h1> {{includeBlock('cars', 'cars', 'slider', id)}} </div> <div class="layout__content"> <ul class="breadcrumbs"> <li class="breadcrumbs__item breadcrumbs__item_type_home" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link" href="/" itemprop="url"><span itemprop="title">{{lng_autoparts}}</span></a></li> <li class="breadcrumbs__item">{{name_nobr}}</li> </ul> {{includeBlock('cars', 'cars', 'autoparts', id)}} <div class="car-autoparts-tree car-autoparts-tree_align_right"> {{includeBlock('cars', 'cars', 'treeAutoparts', id)}} </div> </div> <div class="layout__separator"> </div> <div class="layout__clearfix layout__clearfix_align_center"> <div class="layout__content-with-sidebar"> {{includeBlock('cars', 'cars', 'carHotDeals', id)}} {{IF seo_header}} <div class="car-info-block"> <h2 class="car-info-block__header ribbon ribbon_type_right-inner"> {{seo_header}} </h2> {{IF seo_image}} <img class="car-info-block__image" src="{{url_staticServer}}{{seo_image}}" alt="{{name}}" width="380" height="235"> {{END IF}} <p class="car-info-block__text{{if(seo_image, ' car-info-block__text_type_with-image')}}"> {{seo_text}} </p> <a class="car-info-block__doc-button" href="/car/{{synonym}}/documentations/"> {{lng_docs}} </a><a class="car-info-block__price-button message-bar__button" href="/car/{{synonym}}/price/">{{lng_download_price}}</a> </div> {{END IF}} </div> <div class="layout__sidebar layout__sidebar_type_car" style="position:relative;z-index:9;"> {{includeBlock('directories', 'directories', 'banner', 'car')}}{{includeBlock('seo', 'seo', 'linksBar')}} </div> </div> {{includeScript('footer.tpl')}}
