{{IF details}}
<div class="filter_body center park_level">
  {{BEGIN details}}
    <div class="item ">
      <div class="item_group products-list__item">
          {{IF discount}}
            <span class="sticker">-{{discount}}%</span>
            {{ELSE}} {{IF new}}
              <span class="hot-products__new-sticker">NEW</span>
              {{ELSE}} {{IF often_buy}}
                <span class="hot-products__top-sticker" style="text-indent: -999px;">TOP</span>
              {{END IF}}
            {{END IF}}
          {{END IF}} {{IF status == 'restaurare'}}
            <span class="products-list__state-label">{{lng_restaurare}}</span>
            {{ELSE}} {{IF status == 'replica'}}
              <span class="products-list__state-label">{{lng_replica}}</span>
              {{ELSE}} {{IF status == 'secondhand'}}
                <span class="products-list__state-label">{{lng_secondhand}}</span>
                {{ELSE}} {{IF status == 'new'}}
                  <span class="products-list__state-label">{{lng_new}}</span>
                {{END IF}}
              {{END IF}}
            {{END IF}}
          {{END IF}}
        <div class="caption_up"><a href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">{{name}}</a></div>
        <a href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/" class="image products-list__image product-image-{{id}}">
          <div class="img">
            {{IF image}}
              <img src="{{url_staticServer}}{{image}}" alt="{{name}}">
            {{ELSE}}
              <img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg">
            {{END IF}}
          </div>
        </a>
            {{IF old_cost}}
          <span class="products-list__old-cost _print-cost" data-cost="{{old_cost_unformat}}" data-usd-cost="{{old_cost_usd}}" href="#buy">
                  {{IF currency_abb == 'грн.'}} {{number_format(old_cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small>
              {{ELSE}} {{IF currency_abb == 'P'}}
            <span class="rur">{{currency_abb}}</span>
              {{ELSE}}{{currency_abb}}
              {{END IF}}{{old_cost}}
              {{END IF}}
              </span>
            {{END IF}}
        <div class="caption">{{num_detail}}</div>
        <span class="products-list__cost _print-cost" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </span>
        <span class="hot_buy">
            <a class="button-buy" data-id="{{id}}" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}" href="#buy"> {{lng_buy}} </a>
        </span>
      </div>
    </div>
  {{END details}}
</div>
{{END IF}}