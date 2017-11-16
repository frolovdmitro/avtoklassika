<div class="basket_bar clearfix">
   <div class="basket_item">
      <h4><a href="/basket">{{lng_basket}}</a></h4>
      <ul id="basket-sidebar__list" class="basket-sidebar__list">
          {{BEGIN products}}
         <li class="basket-sidebar__item" data-car="{{car_synonym}}">
             {{IF image_mini}}
            <img class="basket-sidebar__image" width="60" src="{{url_staticServer}}{{image_mini}}">
             {{ELSE}}
            <img class="basket-sidebar__image" width="60" src="//s1.avtoclassika.com/img/noimage-043a876f.jpg">
             {{END IF}}
            <div class="basket-sidebar__item-info">
               <strong class="basket-sidebar__item-name">{{name}}</strong><br>
                {{IF color_name}}{{color_name}}<br>{{END IF}} {{IF size_name}}{{size_name}}<br>{{END IF}} <span class="basket-sidebar__item-count">{{count}} {{lng_unit}} {{lng_for2}}</span> <span class="basket-sidebar__item-cost _print-cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </span>
               <a style="visibility: hidden;" class="basket-sidebar__item-delete"  {{IF advert_id}} data-advert-id="{{advert_id}}" {{END IF}}
                  data-id="{{id}}-{{size}}-{{color}}" href="#delete"></a>
            </div>
         </li>
          {{END products}}
      </ul>
   </div>

   <div class="sum">
      <h4>{{lng_products_cost}}:</h4>
      <span class="basket_bar_cost">
         <div data-usd-cost="{{products_sum_usd}}" data-cost="{{products_sum_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(products_sum_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{products_sum}} {{END IF}} </div>
      </span>
   </div>
   <div class="total_sum clearfix">
      <h4>{{lng_total_cost}}::</h4>
      <span class="basket_bar_cost">
         <div data-usd-cost="{{total_sum_usd}}" data-cost="{{total_sum_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(total_sum_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{total_sum}} {{END IF}} </div>
      </span>
   </div>
</div>