{{includeScript('header.tpl')}}
<div class="container">
  <section id="show_bill_item" class="">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="show_bill_item clearfix">
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
              <h1 class="title_3 top_0"> {{lng_our_adverts}} </h1>
              <div id="breadcrumb">
                <nav class="breadcrumb clearfix">
                  <a class="breadcrumb-item" href="/ads/" itemprop="url"> {{lng_adverts}} </a>
                  <a class="breadcrumb-item" href="/ads/#type-{{type}}=true" itemprop="url">
                    {{IF type == 'sell'}} {{lng_i_sell}} {{END IF}} {{IF type == 'buy'}} {{lng_i_buy}} {{END IF}}
                  </a>
                  <span class="breadcrumb-item active">
                    {{IF category == 'car'}} {{lng_cars}} {{END IF}} {{IF category == 'autopart'}} {{lng_autoparts}} {{END IF}}
                  </span>
                </nav>
              </div>
              <div class="content clearfix">
                <div class="item clearfix">
                  <div class="item_group clearfix">
                    <div class="ad__image-wrap">
                      <a class="ad__image" href="{{url_staticServer}}{{image}}" rel="photos" title="{{name}}"><img src="{{url_staticServer}}{{image_medium}}" alt="{{name}}" width="300" height="230"></a> {{IF images}}
                      <ul class="ad__thumbnails clearfix">
                          {{BEGIN images}}
                        <li class="ad__thumbnails-item"><a class="ad__thumbnail-link" rel="photos" title="{{name}}" href="{{url_staticServer}}{{image}}"><img class="ad__thumbnail-image" src="{{url_staticServer}}{{image_small}}" alt="{{name}}"></a></li>
                          {{END images}}
                      </ul>
                        {{END IF}}
                    </div>
                    <div class="info">
                      <h2> {{name}} </h2>
                      <!--<div class="caption_hot">
                        <span class="rur">P</span>
                        4 061,25-->
                      <div class="caption_hot {{IF cost}} _print-cost{{END IF}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF cost}} {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} {{ELSE}} {{lng_contract_price}} {{END cost}} </div>
                      <p> {{text}} </p>
                      <ul class="clearfix">
                        <li class="location"> {{user_city}} </li>
                        <li class="phone"><a href="tel:{{user_phone_unformat}}">{{user_phone}}</a></li>
                        <li class="user"> {{user_name}} </li>
                        <li class="mail"><a href="mailto:{{user_email}}">{{user_email}}</a></li>
                      </ul>
                      <a class="share" href="mailto:?subject=Avtoclassika.com{{name_encode}}&body={{share_body}}">
                          {{lng_share}}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
                <div class="tabs mob_hidden">
                  <ul class="tabs__nav">
                    <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-comments"> {{lng_comments}} ({{count_comments}}) </a></li>
                  </ul>
                  <div id="tab-comments" class="tabs__content {{UNLESS info}}tabs__content_state_current{{END UNLESS}}"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div>
                </div>
                <div id="tab-comments" class="tabs__content"> {{includeBlock('comments', 'comments', 'comments', comment_data)}} </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0">
              <div class="an_ad">
                <!--<input type="submit" id="my_button" class="my_button" data-toggle="modal" data-target="#to_advertise" value="{{lng_add_advert}}">-->
                <a id="add_adv" class="my_button" href="/json/ads/add-form.html">
                    {{lng_add_advert}}
                </a>
              </div>
              {{includeBlock('directories', 'directories', 'banner', 'advert')}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
{{includeBlock('adverts', 'adverts', 'otherAdverts', params)}}
{{includeScript('footer.tpl')}}

