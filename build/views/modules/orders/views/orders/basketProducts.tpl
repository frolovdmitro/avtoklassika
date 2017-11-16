<div class="basket-products">
  <ul class="basket-products__list">
    {{BEGIN products}} 
    <li class="basket-products__item">
      <img class="basket-products__image" src="{{url_staticServer}}{{image_small}}"> 
      <div class="basket-products__info">
        {{IF advert_id}}
        <h2 class="basket-products__name data-advert-{{advert_id}}">{{name}} #{{advert_id}}</h2>
        <a class="adv__edit-link" href="/cabinet/editadvert/?id={{advert_id}}">Редактировать</a>
        <h3 class="basket-products__name_adv"><span  style="font-size: 18px; color: #0c555d">{{adv_name}}</span> :<span  style="font-size: 16px">{{adv_text}}</span></h2>
        {{ELSE}}
        <h2 class="basket-products__name">{{name}}</h2>
        {{END IF}}

        {{IF num!= 10009009001}}
        {{IF status == 'restaurare'}} <span class="basket-products__state-label">{{lng_restaurare}}</span> {{ELSE}} {{IF status == 'replica'}} <span class="basket-products__state-label">{{lng_replica}}</span> {{ELSE}} {{IF status == 'secondhand'}} <span class="basket-products__state-label">{{lng_secondhand}}</span> {{ELSE}} {{IF status == 'new'}} <span class="basket-products__state-label">{{lng_new}}</span> {{END IF}} {{END IF}} {{END IF}} {{END IF}} 
        <div id="product-cost-{{id}}" style="display: inline-block; margin-left: 20px; font-size: 17px; line-height: 1; color: #0c555d; }" class="_print-cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </div>
        <br>
        <strong class="quick-buy__num-detail"># {{num}}</strong> 
        {{END IF}}
      </div>
     {{IF advert_id}}
      <div class="basket-products__actions">
        <div class="form__input-count-wrap basket-products__count-wrap"> </div>
        <div id="product-sum-{{id}}"
             data-usd-cost="{{sum_usd}}" data-cost="{{sum_unformat}}"
             class="products-list__cost basket-products__detail-cost _print-cost">
          {{IF currency_abb == 'грн.'}}
          {{number_format(sum_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small>
          {{ELSE}}
          {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{sum}}
          {{END IF}}
        </div><a class="basket-products__delete" {{IF advert_id}} data-advert-id="{{advert_id}}" {{END IF}}
          data-id="{{id}}-{{size}}-{{color}}" href="#delete"></a>
      </div>
      {{ELSE}}
      <div class="basket-products__actions">
        <div class="form__input-count-wrap basket-products__count-wrap"> <a class="form__input-count-btn form__input-count-btn_type_minus" href="#delete" data-step="-1">-</a> <input class="form__input_type_count basket-products__count" type="text" name="count" data-id="{{id}}" data-color-id="{{color_id}}" data-size-id="{{size_id}}" id="basket-products__count-{{id}}" value="{{count}}" maxlength="2"> <a class="form__input-count-btn form__input-count-btn_type_plus" href="#add" data-step="1">+</a> </div>
        <div id="product-sum-{{id}}" data-usd-cost="{{sum_usd}}" data-cost="{{sum_unformat}}" class="products-list__cost basket-products__detail-cost _print-cost"> {{IF currency_abb == 'грн.'}} {{number_format(sum_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{sum}} {{END IF}} </div>
        <a class="basket-products__delete" data-id="{{id}}-{{size_id}}-{{color_id}}" href="#delete"></a> 
      </div>
      {{END IF}}
    </li>
    {{END products}} 
  </ul>
  <div class="basket-products__footer">
    {{IF promocode}} 
    <form class="basket-products__promocode-wrap" method="post" action="/json/basket/promocode/" id="basket-promocode-wrap">
      <label class="basket-products__promocode-label" for="promocode"> {{lng_input_promocode}} <span data-hint="{{lng_promocode_hint}}" class="basket-products__promocode-hint hint--top icons__help"></span> </label> 
      <div class="form__input-wrap"> <span class="basket-products__promocode-value"> {{stg_discount_value}}% </span> {{UNLESS discount}} <input class="form__input form__input_type_contrast form__input_type_withbutton" type="text" name="promocode" id="promocode" data-discount-value="{{stg_discount_value}}"> <button class="form__button_type_withinput" type="submit"> {{lng_apply}} </button> <span class="form__input-error error"></span> {{END UNLESS}} </div>
    </form>
    {{END IF}} <a id="basket-submit" class="form__submit quick-buy__submit quick-buy__submit_type_a" href="#step=2"> {{lng_buy}} </a> 
    <div class="basket-products__total-cost _print-cost" data-usd-cost="{{total_sum_usd}}" data-cost="{{total_sum_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(total_sum_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{total_sum}} {{END IF}} </div>
  </div>
  {{IF products}} {{includeBlock('cars', 'cars', 'relatedDetails', relatedData)}} {{END IF}} 
</div>
