{{IF details}}
<div class="container">
  <section id="slider_new" class="separator">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
          <div id="my_slider_new" class="slider slider_level_hot">
            <h1 class="title">{{lng_related_products}}</h1>
            <div class="owl-carousel owl-theme">
              {{BEGIN details}}
                <div class="item hot-products__item">
                  <div class="item_group">
                      {{IF new}} <span class="hot-products__new-sticker">NEW</span> {{ELSE}} {{IF often_buy}} <span class="hot-products__top-sticker">TOP</span> {{ELSE}} {{IF discount}} <span class="sticker">-{{discount}}%</span> {{END IF}} {{END IF}} {{END IF}} <a class="hot-products__caption" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/"> {{name}} </a>
                    <div class="image">
                      <div class="img">
                        <a class="products-list__image hot-products__image product-image-{{id}}" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">
                          {{IF image}}
                            <img src="{{url_staticServer}}{{image}}" alt="{{name}}">
                          {{ELSE}}
                            <img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg">
                          {{END IF}}
                        </a>
                      </div>
                    </div>
                    <div class="caption">{{num_detail}}</div>
                    {{IF old_cost}} <span class="hot-products__old-cost" data-cost="{{old_cost_unformat}}" data-usd-cost="{{old_cost_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(old_cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{old_cost}} {{END IF}} </span> {{END IF}} <span class="hot-products__cost _print-cost" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </span><a class="button-buy hot-products__buy" data-id="{{id}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}" href="#buy"> {{lng_buy}} </a>
                  </div>
                </div>
              {{END details}}
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden">
          {{includeBlock('directories', 'directories', 'banner', 'autopart')}}
        </div>
      </div>
    </div>
  </section>
</div>
{{END IF}}
