{{includeScript('header.tpl')}} <div class="layout__content"> <div class="layout__content-with-sidebar"> <h1 class="autoparts__h1 ribbon ribbon_type_right-inner">{{name}}</h1> <ul class="breadcrumbs"> <li class="breadcrumbs__item breadcrumbs__item_type_home" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link" href="/news/" itemprop="url"><span itemprop="title">{{lng_news}}</span></a></li> <li class="breadcrumbs__item">{{name}}</li> </ul> {{text}} <div class="tabs tabs_type_news" id="comments=true"> <ul class="tabs__nav"> <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-comments"> {{lng_comments}} ({{count_comments}}) </a></li> </ul> <div id="tab-comments" class="tabs__content {{UNLESS info}}tabs__content_state_current{{END UNLESS}}"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div> </div> <div id="tab-comments" class="tabs__content"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div> </div> <div class="layout__sidebar"> {{includeBlock('news', 'news', 'categories')}} {{includeBlock('directories', 'directories', 'banner', 'car')}}{{includeBlock('seo', 'seo', 'linksBar')}} </div> </div> {{includeScript('footer.tpl')}}