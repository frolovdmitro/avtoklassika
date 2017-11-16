{{IF details}}
<div id="my_slider_new" class="slider slider_level_hot">
  <h1 class="title">{{lng_hot_deals}}</h1>
  <div class="owl-carousel owl-theme">
    {{BEGIN details}}
      <div class="item">
        <div class="item_group products-list__item">
          {{IF new}}
            <span class="hot-products__new-sticker">NEW</span>
            {{ELSE}} {{IF often_buy}}
              <span class="hot-products__top-sticker">TOP</span>
              {{ELSE}} {{IF discount}}
                <span class="sticker">-{{discount}}%</span>
              {{END IF}}
            {{END IF}}
          {{END IF}}
          <div class="caption_up">
            <a href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/"> {{name}} </a>
          </div>
          <div class="image products-list__image product-image-{{id}}">
            <div class="img">
              <a href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">
                {{IF image}}
                  <img src="{{url_staticServer}}{{image}}" alt="{{name}}">
                {{ELSE}}
                  <img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg">
                {{END IF}}
              </a>
            </div>
          </div>
          <span class="hot-products__old-cost _print-cost" data-cost="{{old_cost_unformat}}" data-usd-cost="{{old_cost_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(old_cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{old_cost}} {{END IF}} </span>
          <div class="caption">{{num_detail}}</div>
          <span class="hot_price" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}">
            {{IF currency_abb == 'грн.'}}
              {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small>
              {{ELSE}} {{IF currency_abb == 'P'}}
                <span class="rur">{{currency_abb}}</span>
                {{ELSE}}{{currency_abb}}
              {{END IF}}{{cost}}
            {{END IF}}
          </span>
          <span class="hot_buy">
              <a class="button-buy hot-products__buy" data-id="{{id}}" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}" href="#buy"> {{lng_buy}} </a>
          </span>
        </div>
      </div>
    {{END details}}
  </div>
</div>
{{END IF}}