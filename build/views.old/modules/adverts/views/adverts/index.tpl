{{includeScript('header.tpl')}} <div class="layout__content"> <div class="layout__content-with-sidebar layout__content_type_nomargin"> <h2 class="autoparts__h1 ribbon ribbon_type_right-inner"> {{lng_our_adverts}} </h2> <ul class="breadcrumbs"> <li class="breadcrumbs__item breadcrumbs__item_type_home" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link" href="/ads/" itemprop="url"><span itemprop="title">{{lng_adverts}}</span></a></li> <li class="breadcrumbs__item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link breadcrumbs__link_type_upper" href="/ads/#type-{{type}}=true" itemprop="url"><span itemprop="title"> {{IF type == 'sell'}} {{lng_i_sell}} {{END IF}} {{IF type == 'buy'}} {{lng_i_buy}} {{END IF}} </span></a></li> <li class="breadcrumbs__item"> {{IF category == 'car'}} {{lng_cars}} {{END IF}} {{IF category == 'autopart'}} {{lng_autoparts}} {{END IF}} </li> </ul> <div class="ad"> <div class="ad__image-wrap"> <a class="ad__image" href="{{url_staticServer}}{{image}}" rel="photos" title="{{name}}"><img src="{{url_staticServer}}{{image_medium}}" alt="{{name}}" width="300" height="230"></a> {{IF images}} <ul class="ad__thumbnails"> {{BEGIN images}} <li class="ad__thumbnails-item"><a class="ad__thumbnail-link" rel="photos" title="{{name}}" href="{{url_staticServer}}{{image}}"><img class="ad__thumbnail-image" src="{{url_staticServer}}{{image_small}}" alt="{{name}}"></a></li> {{END images}} </ul> {{END IF}} </div> <div class="ad__info-wrap"> <h1 class="ad__caption">{{name}}</h1> <div class="ad__date"> {{date}} </div> <div class="ad__cost {{IF cost}} _print-cost{{END IF}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF cost}} {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} {{ELSE}} {{lng_contract_price}} {{END cost}} </div> <div class="ad__text"> {{text}} </div> <ul class="ads-list__user-info"> <li class="ads-list__user-item ads-list__user-item_type_location"> {{user_city}} </li> <li class="ads-list__user-item ads-list__user-item_type_phone"><a href="tel:{{user_phone_unformat}}">{{user_phone}}</a></li> <li class="ads-list__user-item ads-list__user-item_type_user"> {{user_name}} </li> <li class="ads-list__user-item ads-list__user-item_type_email"><a href="mailto:{{user_email}}">{{user_email}}</a></li> </ul> <ul class="comments__actions-list comments__actions-list_type_ads"> <li class="comments__actions-item"><a class="comments__actions-share" href="mailto:?subject=Avtoclassika.com{{name_encode}}&body={{share_body}}"> {{lng_share}} </a></li> </ul> </div> </div> <div class="tabs"> <ul class="tabs__nav"> <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-comments"> {{lng_comments}} ({{count_comments}}) </a></li> </ul> <div id="tab-comments" class="tabs__content {{UNLESS info}}tabs__content_state_current{{END UNLESS}}"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div> </div> <div id="tab-comments" class="tabs__content"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div> </div> <div class="layout__sidebar"> <a class="ad__add-button" href="/json/ads/add-form.html" rel="modal:open"> {{lng_add_advert}} </a> {{includeBlock('directories', 'directories', 'banner', 'advert')}}{{includeBlock('seo', 'seo', 'linksBar')}} </div> </div> <div class="layout__separator layout__separator_type_margin-bottom"> </div> {{includeBlock('adverts', 'adverts', 'otherAdverts', params)}} {{includeScript('footer.tpl')}}