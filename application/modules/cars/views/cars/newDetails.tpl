{{IF details}}
<div class="container">
  <section id="slider_new">
    <div class="row">
      <div class="container_center clearfix pad_0">
        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
          <div id="my_slider_new" class="slider">
            <h1 class="title"> {{lng_new_details}} </h1>
            <div class="owl-carousel owl-theme">
              {{BEGIN details}}
                <div class="item hot-products__item">
                  <div class="item_group">
                    <span class="hot"></span>
                    <div class="caption_up">
                      <a href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/"> {{name}} </a>
                    </div>
                    {{IF image}}
                      <div class="image" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">
                        <div class="img">
                          <a class="products-list__image hot-products__image product-image-{{id}}" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">
                            <img src="{{url_staticServer}}{{image}}" alt="{{name}}">
                          </a>
                        </div>
                      </div>
                    {{ELSE}}
                      <span class="detail-info__image-link">
                        <img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg">
                      </span>
                    {{END IF}}
                    <div class="caption">{{num_detail}}</div>
                    <span class="hot_price" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </span><a class="button-buy hot-products__buy" data-id="{{id}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}" href="#buy"> {{lng_buy}} </a>
                
                  </div>
                </div>
              {{END details}}
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden">
          {{includeBlock('directories', 'directories', 'banner', 'index_hot')}}
        </div>
      </div>
    </div>
  </section>
</div>
{{END IF}}
