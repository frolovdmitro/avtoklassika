{{IF adverts}} <div class="layout__clearfix layout__clearfix_align_center"> <div class="layout__content-with-sidebar"> <div class="hot-products"> <h2 class="hot-products__header ribbon ribbon_type_right-outer"> {{lng_other_adverts}} </h2> <div id="other-adverts__nav" class="hot-products__nav"> </div> <div id="other-adverts__slider" class="hot-products__slider flexslider"> <ul class="slider__list hot-products__list slides _products-wrap"> {{BEGIN adverts}} <li class="slider__item hot-products__item"> <div class="hot-products__item-inner hot-products__item-inner_type_advert"> <a class="hot-products__caption hot-products__caption_type_advert" href="/ads/{{type}}/{{id}}/"> {{name}} </a><a class="products-list__image hot-products__image product-image-{{id}}" href="/ads/{{type}}/{{id}}/"> {{IF image}} <div class="products-list__image-inner"> <img class="ad__other-image" src="{{url_staticServer}}{{image_mini}}" alt="{{name}}" width="193" height="132"> </div> {{ELSE}} <div class="products-list__image-inner"> <img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg"> </div> {{END IF}} </a> </div> <div class="ad__other-cost {{IF cost}} _print-cost{{END IF}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}">{{IF cost}} {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} {{ELSE}} {{lng_contract_price}} {{END cost}} </div> </li> {{END adverts}} </ul> </div> </div> </div> <div class="layout__sidebar layout__sidebar_type_car"> {{includeBlock('directories', 'directories', 'banner', 'advert')}} </div> </div> {{END IF}}