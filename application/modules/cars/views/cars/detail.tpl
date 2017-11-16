{{includeScript('header.tpl')}}
<div class="container">
  <section id="car_park_level" class="car_park level_2">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <h1 id="detail-name" data-id="{{id}}" data-car-id="{{car_id}}" class="title_3 top_0">{{name}}</h1>
          <div id="breadcrumb">
            <nav class="breadcrumb clearfix">
              <a class="breadcrumb-item" href="/" itemprop="url">{{lng_autoparts}}</a>
              <a class="breadcrumb-item" itemprop="url" href="/car/{{car_synonym}}/">{{car_name_nobr}}</a>
              {{BEGIN autoparts_crumbs}}
                <a class="breadcrumb-item" itemprop="url" href="/car/{{car_synonym}}/{{id}}/">{{name}}</a>
              {{END autoparts_crumbs}}
              <span class="breadcrumb-item active">{{name}}</span>
            </nav>
          </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="filter_body center park_level tovar clearfix">
            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 pad_0">
              <div class="item">
                <div class="detail-info__image-block {{unless(image, ' detail-info__image-block_state_no-image')}}">
                  <span class="detail-info__article"># {{detail_num}}</span> {{IF discount}} <span class="sticker">-{{discount}}%</span> {{ELSE}} {{IF new}} <span class="hot-products__new-sticker">NEW</span> {{ELSE}} {{IF often_buy}} <span class="hot-products__top-sticker">TOP</span> {{END IF}} {{END IF}} {{END IF}} {{IF status == 'restaurare'}} <span class="products-list__state-label">{{lng_restaurare}}</span> {{ELSE}} {{IF status == 'replica'}} <span class="products-list__state-label">{{lng_replica}}</span> {{ELSE}} {{IF status == 'secondhand'}} <span class="products-list__state-label">{{lng_secondhand}}</span> {{ELSE}} {{IF status == 'new'}} <span class="products-list__state-label">{{lng_new}}</span> {{END IF}} {{END IF}} {{END IF}} {{END IF}}
                  <div class="detail-info__image-wrap _products-wrap"> {{IF image}} <a class="detail-info__image-link product-image-{{id}}" rel="photos" title="{{name}}" href="{{url_staticServer}}{{image}}"><img src="{{url_staticServer}}{{image_medium}}" alt="{{name}}"></a> {{ELSE}} <span class="detail-info__image-link"><img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg"></span> {{END IF}} </div>
                    {{IF images}}
                  <ul class="detail-info__thumbnails">
                      {{BEGIN images}}
                    <li class="detail-info__thumbnails-item"><a class="detail-info__thumbnail-link" rel="photos" title="{{name}}" href="{{url_staticServer}}{{image}}"><img class="detail-info__thumbnail-image" src="{{url_staticServer}}{{image_small}}" alt="{{name}}"></a></li>
                      {{END images}}
                  </ul>
                    {{END IF}}
                </div>
              </div>

              <div class="shema">
                {{IF schema}}
                  <div class="detail-info__schema detail-info__schema_state_invisible">
                    <a class="detail-info__schema-link" href="{{url_staticServer}}{{schema}}" title="{{car_name}} \ Двигатель \ Система питания \ {{name}}"><img class="detail-info__schema-image" src="{{url_staticServer}}{{schema}}" alt="{{autopart_name}}"></a>
                    <div class="detail-info__schema-coord-wrap">
                        {{BEGIN coordinates}} {{IF current}} <span class="__tooltip detail-info__schema-coord detail-info__schema-coord_state_current" style="top:{{_toppos}}px; left:{{_left}}px;">{{num}}</span> {{ELSE}} <a href="/car/{{car_synonym}}/{{autopart_id}}/{{detail_id}}/" class="__tooltip detail-info__schema-coord{{UNLESS presence}} detail-info__schema-coord_state_no{{END UNLESS}}" data-powertiptarget="tooltip-content-{{_num}}" style="top:{{_toppos}}px; left:{{_left}}px;">{{num}}</a>
                      <div id="tooltip-content-{{_num}}" class="tooltip__wrap">
                          {{IF image}} <img class="tooltip__image" src="{{url_staticServer}}{{image}}"> {{ELSE}} <img class="tooltip__image" src="//s1.avtoclassika.com/img/noimage-sm-1bd30ac1.jpg"> {{END IF}}
                        <div class="tooltip__info">
                          <strong>#{{num_detail}}</strong><br> {{name}} {{IF presence}}<br>
                          <div class="tooltip__cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </div>
                            {{END IF}}
                        </div>
                      </div>
                        {{END IF}} {{END coordinates}}
                    </div>
                    <ul class="detail-info__schema-actions">
                      <li class="detail-info__schema-actions-item"><a class="detail-info__schema-actions-link detail-info__schema-actions-link_type_send" href="mailto:?subject={{name_encode}}&body={{share_body}}"> {{lng_send}} </a></li>
                      <li class="detail-info__schema-actions-item"><a class="detail-info__schema-actions-link detail-info__schema-actions-link_type_print" href="#print"> {{lng_print}} </a></li>
                    </ul>
                  </div>
                {{ELSE}}
                  <div class="detail-info__schema detail-info__schema_state_no-image"> Схема отсутствует </div>
                {{END IF}}
                </div>
              </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 pad_0" id="my_basket">
              <div class="detail-info__buy-block {{UNLESS sizes}}{{UNLESS colors}} detail-info__buy-block_valign_middle {{END colors}}{{END UNLESS}}">
                <div class="detail-info__buy-block-inner">
                  <div class="detail-info__buy-block-inner-cell">
                      {{IF sizes}}
                    <div class="detail-info__input-wrap"> <select id="size" class="detail-info__select" name="size"> <option value="" {{if image}}data-image="{{url_staticServer}}{{image}}" {{end if}} {{if image_medium}} data-image-medium="{{url_staticServer}}{{image_medium}}" {{end if}}>{{lng_select_size}}</option> {{BEGIN sizes}} <option value="{{id}}" {{if image}}data-image="{{url_staticServer}}{{image}}" {{end if}} {{if image_medium}} data-image-medium="{{url_staticServer}}{{image_medium}}" {{end if}}> {{name}} </option> {{END sizes}} </select> </div>
                      {{END IF}} {{IF colors}}
                    <div class="detail-info__input-wrap"> <select id="color" class="detail-info__select" name="color"> <option value="" {{if image}}data-image="{{url_staticServer}}{{image}}" {{end if}} {{if image_medium}} data-image-medium="{{url_staticServer}}{{image_medium}}" {{end if}}>{{lng_select_color}}</option> {{BEGIN colors}} <option value="{{id}}" {{if image}}data-image="{{url_staticServer}}{{image}}" {{end if}} {{if image_medium}} data-image-medium="{{url_staticServer}}{{image_medium}}" {{end if}}> {{name}} </option> {{END colors}} </select> </div>
                      {{END IF}} {{IF presence}}
                    <div class="detail-info__cost _print-cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}" data-not-fount-text="{{lng_not_in_store}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </div>
                    <div class="form__input-count-wrap" style="margin: 0 auto 13px;"> <a class="form__input-count-btn form__input-count-btn_type_minus" href="#delete" data-step="-1">-</a> <input class="form__input_type_count" type="text" name="_count" id="__form__count" value="1" maxlength="2"> <a class="form__input-count-btn form__input-count-btn_type_plus" href="#add" data-step="1">+</a> </div>
                    <a class="button-buy detail-info__basket-button" data-id="{{id}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}" href="#buy"> {{lng_buy}} </a><a class="detail-info__buy-fast" href="#fast-buy">{{lng_fast_buy}}</a> {{ELSE}}
                    <div class="detail-info__cost detail-info__cost_state_disabled"> {{lng_not_in_store}} </div>
                    <span class="detail-info__basket-button detail-info__basket-button_state_disabled"> {{lng_buy}} </span> {{END IF}}
                  </div>
                </div>
                  {{includeBlock('orders', 'orders', 'quickBuy', data)}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="container">
  <section id="payment">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 pad_0">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
            <div class="coments mob_hidden">
              <div class="tabs">
                <ul class="tabs__nav">
                    {{IF info}}
                  <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-info"> {{lng_description}} </a></li>
                    {{END IF}}
                  <li class="tabs__nav-item {{UNLESS info}}tabs__nav-item_pos_first tabs__nav-item_state_current{{END UNLESS}}"><a class="tabs__nav-link" href="#tab-comments"> {{lng_comments}} ({{count_comments}}) </a></li>
                </ul>
                  {{IF info}}
                <div id="tab-info" class="tabs__content tabs__content_state_current">
                    {{info}}
                    {{IF youtube}}
                  <div style="    clear: both; text-align: center; padding: 20px 0 0;">
                    <iframe width="665" height="370" style="max-width:100%;"
                            src="//www.youtube.com/embed/{{youtube}}" frameborder="0"
                            allowfullscreen></iframe>
                  </div>
                    {{END IF}}
                </div>
                  {{END IF}}
                <div id="tab-comments" class="tabs__content {{UNLESS info}}tabs__content_state_current{{END UNLESS}}">
                    {{includeBlock('comments', 'comments', 'comments', comment_data)}}
                </div>
              </div>
              <div class="fb-like" style="float:left;margin-right: 20px;" data-href="http://avtoclassika.com/car/{{car_synonym}}/{{autopart_id}}/{{id}}/" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"> </div>
              <div class="g-plusone" data-size="medium" data-annotation="inline" style="float:left;margin-right: 20px;" data-width="120" data-href="http://avtoclassika.com/car/{{car_synonym}}/{{autopart_id}}/{{id}}/"> </div>
            </div>
          </div>

        </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 pad_0 mob_hidden" id="auto_section">
          <div class="car-autoparts-tree car-autoparts-tree_type_detail">
            {{includeBlock('cars', 'cars', 'treeAutoparts', car_id)}}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
{{includeBlock('cars', 'cars', 'relatedDetails', id)}}
{{includeScript('footer.tpl')}}
