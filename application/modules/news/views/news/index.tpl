{{includeScript('header.tpl')}}
<!--<div class="layout__content">
  <div class="layout__content-with-sidebar">
    <h1 class="autoparts__h1 ribbon ribbon_type_right-inner">{{name}}</h1>
    <ul class="breadcrumbs">
      <li class="breadcrumbs__item breadcrumbs__item_type_home" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
        <a class="breadcrumbs__link" href="/news/" itemprop="url">
          <span itemprop="title">{{lng_news}}</span>
        </a>
      </li>
      <li class="breadcrumbs__item">{{name}}</li>
    </ul>
    {{text}}
    <div class="tabs tabs_type_news" id="comments=true">
      <ul class="tabs__nav">
        <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-comments"> {{lng_comments}} ({{count_comments}}) </a></li>
      </ul>
      <div id="tab-comments" class="tabs__content {{UNLESS info}}tabs__content_state_current{{END UNLESS}}"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div>
    </div>
    <div id="tab-comments" class="tabs__content"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div>
  </div>
  <div class="layout__sidebar"> {{includeBlock('news', 'news', 'categories')}} {{includeBlock('directories', 'directories', 'banner', 'car')}}{{includeBlock('seo', 'seo', 'linksBar')}} </div>
</div>-->

<div class="container">
  <section id="news" class="">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="news clearfix">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
              <h1 class="title_3 top_0">{{name}}</h1>
              <div id="breadcrumb">
                <nav class="breadcrumb clearfix">
                  <a class="breadcrumb-item" href="/news/">{{lng_news}}</a>
                  <span class="breadcrumb-item active">{{name}}</span>
                </nav>
              </div>
              <div class="news_item">
                  {{text}}
              </div>
              <div class="coments mob_hidden">
                <ul id="tabs">
                  <li><a href="#" title="tab1">{{lng_comments}} <sup>({{count_comments}})</sup></a></li>
                </ul>
                <div id="content">
                  <div id="tab1"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden">
              {{includeBlock('news', 'news', 'categories')}}
              {{includeBlock('directories', 'directories', 'banner', 'car')}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
{{includeScript('footer.tpl')}}
