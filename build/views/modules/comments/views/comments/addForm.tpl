{{IF cars}} <div id="cars-slider__slider" class="cars-slider__slider flexslider"> <ul class="slider__list cars-slider__list slides"> {{BEGIN cars}} <li class="slider__item cars-slider__item catalog-autoparts__item {{if(current, ' cars-slider__item_type_current')}}" "><a class="catalog-autoparts__link {{if(current, ' catalog-autoparts__link_type_current')}}" href="/car/{{synonym}}/"><img class="catalog-autoparts__image" src="{{url_staticServer}}{{image}}" alt="{{name}}" width="109" height="78"><br> <strong class="catalog-autoparts__caption">{{name}}<br>&nbsp;</strong></a></li> {{END cars}} </ul> </div> {{END IF}}