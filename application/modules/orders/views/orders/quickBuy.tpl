<div id="quick-buy-form" class="quick-buy">
  <form class="quick-buy__form" method="post" action="/json/order/quick-buy/{{id}}/" novalidate>
    <p class="quick-buy__text">{{lng_quick_buy_text}}</p>
    <div class="quick-buy__inputs-success-wrap">
      <div class="quick-buy__success-text">
        <p>{{lng_quick_buy_success}}</p>
        <p class="quick-buy__success-order-num"> {{lng_order_num}} #<strong class="success-num-order"></strong> </p>
      </div>
      <div class="quick-buy__inputs-wrap">
        <div class="form__input-wrap"> <input class="form__input quick-buy__input" type="text" name="name" id="quick-buy-form__name" placeholder="{{lng_name}}*" autofocus><span class="form__input-error error"></span> </div>
        <div class="form__input-wrap"> <input class="form__input quick-buy__input" type="tel" name="phone" id="quick-buy-form__phone" placeholder="{{lng_phone}}*"><span class="form__input-error error"></span> </div>
        <div class="form__input-wrap"> <input class="form__input quick-buy__input" type="email" name="email" id="quick-buy-form__email" placeholder="E-mail*"><span class="form__input-error error"></span> </div>
        <input type="hidden" name="currency" value="{{currency}}"><input type="hidden" name="lang" value="{{current_language}}"> 
      </div>
    </div>
    <strong class="quick-buy__total-caption">{{lng_our_order}}:</strong> 
    <div class="quick-buy__detail-info-wrap">
      <div class="quick-buy__image-wrap">
        <div class="quick-buy__image-table">
          <div class="quick-buy__image-table-cell"> <img src="{{url_staticServer}}{{image_mini}}" alt="{{name}}"> </div>
        </div>
      </div>
      <div class="quick-buy__detail-info">
        <h4 class="quick-buy__detail-name">{{name}}</h4>
        {{IF status == 'restaurare'}} <span class="products-list__state-label quick-buy__state-label"> {{lng_restaurare}} </span> {{ELSE}} {{IF status == 'replica'}} <span class="products-list__state-label quick-buy__state-label"> {{lng_replica}} </span> {{ELSE}} {{IF status == 'secondhand'}} <span class="products-list__state-label quick-buy__state-label"> {{lng_secondhand}} </span> {{ELSE}} {{IF status == 'new'}} <span class="products-list__state-label quick-buy__state-label"> {{lng_new}} </span> {{END IF}} {{END IF}} {{END IF}} {{END IF}} <strong class="quick-buy__num-detail"># {{detail_num}}</strong> 
      </div>
      <div class="quick-buy__count-cost-info">
        <div class="form__input-count-wrap"> <a class="form__input-count-btn form__input-count-btn_type_minus" href="#delete" data-step="-1">-</a><input class="form__input_type_count" type="text" name="count" id="quick-buy-form__count" value="1" maxlength="2"><a class="form__input-count-btn form__input-count-btn_type_plus" href="#add" data-step="1">+</a> </div>
        <div class="products-list__cost quick-buy__detail-cost _print-cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </div>
      </div>
    </div>
    <div class="quick-buy__total-info">
      <button class="form__submit quick-buy__submit" type="submit"> {{lng_buy}} </button> 
      <div class="quick-buy__total-cost _print-cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </div>
    </div>
  </form>
</div>
