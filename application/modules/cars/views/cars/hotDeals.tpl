{{IF details}} 
<div class="hot-products">
  <h2 class="hot-products__header ribbon ribbon_type_right-outer"> {{lng_hot_deals}} </h2>
  <div id="hot-products__nav" class="hot-products__nav"></div>
  <div  style="clear:both;" id="hot-products__slider" class="hot-products__slider flexslider">
    <ul class="slider__list hot-products__list slides _products-wrap">
      {{BEGIN details}} 
      <li class="slider__item hot-products__item">
        <div class="hot-products__item-inner">
          {{IF new}} <span class="hot-products__new-sticker">NEW</span> {{ELSE}} {{IF often_buy}} <span class="hot-products__top-sticker">TOP</span> {{ELSE}} {{IF discount}} <span class="sticker">-{{discount}}%</span> {{END IF}} {{END IF}} {{END IF}} <a class="hot-products__caption" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/"> {{name}} </a>
          <a class="products-list__image hot-products__image product-image-{{id}}" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">
            {{IF image}} 
            <div class="products-list__image-inner"> <img src="{{url_staticServer}}{{image}}" alt="{{name}}"> </div>
            {{ELSE}} <span class="detail-info__image-link"><img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg"></span> {{END IF}} 
          </a>
          <!-- <span class="hot-products__article">{{num_detail}}</span><span class="hot-products__old-cost _print-cost" data-cost="{{old_cost_unformat}}" data-usd-cost="{{old_cost_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(old_cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{old_cost}} {{END IF}} </span> <span class="hot-products__cost _print-cost" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </span><a class="button-buy hot-products__buy" data-id="{{id}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}" href="#buy"> {{lng_buy}} </a>  -->
        </div>
      </li>
      {{END details}} 
    </ul>
  </div>
  {{includeBlock('directories', 'directories', 'banner', 'index_hot')}} 
</div>
{{END IF}}
