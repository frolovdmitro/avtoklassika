{{includeScript('header.tpl')}} 
<div class="layout__content">
  {{UNLESS type}} 
  <ul class="basket-steps">
    <li class="basket-steps__item basket-steps__item_type_caption ribbon ribbon_type_right-outer">{{lng_create_order}}</li>
    <li class="basket-steps__item"><span class="basket-steps__link basket-steps__link_step_1"> <span class="basket-steps__num ">1</span>{{lng_step_1}}</span></li>
    <li class="basket-steps__item"><span class="basket-steps__link basket-steps__link_step_2"> <span class="basket-steps__num ">2</span>{{lng_step_2}}</span></li>
    <li class="basket-steps__item basket-steps__item_state_active basket-steps__item_pos_last"><span class="basket-steps__link basket-steps__link_step_3"> <span class="basket-steps__num ">3</span>{{lng_step_3}}</span></li>
  </ul>
  <div class="layout__content-with-sidebar layout__content_type_basket" id="basket-page" data-num="3">
    <div class="order-thanks">
      <h2 class="order-thanks__caption"> {{lng_order}} #{{num}} {{lng_decorated}} </h2>
      <p class="order-thanks__text">{{lng_mrms}} {{user_name}}. {{lng_you_order}} #{{num}} {{lng_decorated}}. {{lng_order_thanks_text}} </p>
    </div>
    <h3 class="order-thanks__congratulations">{{lng_congratulations}}</h3>
    <div class="order-thanks" style="margin-bottom:340px;text-align:center;">
      <p style="text-align: left;margin-top: 0;">We are constantly striving to improve the value we deliver in our customer service and product lines.To help us achieve this goal, we are conducting a brief online web survey to assess our performance.
      <p> 
      <p style="text-align: left;">Below is a link to a brief web survey with questions around our performance and the future needs and trends you foresee in restoration projects.</p>
      <a href="http://www.survio.com/survey/d/C9U8B4F6J1L8Q9N3R" target="_blank" style="display: inline-block; background: #ff8800; color: #fff; font-size: 26px; padding: 5px 40px; border: 1px solid #c26700;">{{lng_survey}}</a> 
      <p style="text-align: left;margin-top: 20px;">To show our appreciation, all participants will get 3% discount for next order.
      <p> 
      <p style="text-align: left;">For your participation, we thank you in advance. It's our commitment to learn from your feedback and work to improve our services even more based on your suggestions and ideas.</p>
      <p style="text-align: left;margin-bottom:0;">Thank you again for your cooperation.</p>
    </div>
  </div>
  <div class="order-thanks__share">
    {{lng_share_friends}}<br><br> 
    <div class="order-thanks__share-buttons" style="display:inline-block;">
      <div class="fb-like" style="float:left;margin-right: 20px;font-size:1px;" data-href="http://avtoclassika.com/" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
      <div class="g-plusone" data-size="medium" data-annotation="inline" style="float:left;margin-right: 20px;" data-width="120" data-href="http://avtoclassika.com/car/{{car_synonym}}/{{autopart_id}}/{{id}}/"></div>
    </div>
  </div>
  {{ELSE}} 
  <div class="layout__content-with-sidebar layout__content_type_basket">
    <div class="order-thanks" style="margin-bottom: 20px;">
      <h2 class="order-thanks__caption"> {{lng_order}} #{{num}} {{lng_has_state}} {{IF status == 'proccess'}}"{{lng_state_process}}"{{END IF}} {{IF status == 'wait_payment'}}"{{lng_state_wait_payment}}"{{END IF}} {{IF status == 'pending'}}"{{lng_state_pending}}"{{END IF}} {{IF status == 'paid'}}"{{lng_state_paid}}"{{END IF}} {{IF status == 'verify_payment'}}"{{lng_state_verify_payment}}"{{END IF}} {{IF status == 'cancel'}}"{{lng_state_cancel}}"{{END IF}} {{IF status == 'success'}}"{{lng_state_success}}"{{END IF}} </h2>
      <p class="order-thanks__text">{{lng_mrms}} {{user_name}}. {{lng_you_order}} #{{num}} {{lng_created}} {{date}}. </p>
    </div>
    <ul class="basket-products__list">
      {{BEGIN products}} 
      <li class="basket-products__item" style="padding:10px;">
        <img class="basket-products__image" src="{{url_staticServer}}{{image_small}}"> 
        <div class="basket-products__info" style="width:300px;">
          <h2 class="basket-products__name">{{name}}</h2>
          {{IF status == 'restaurare'}} <span class="basket-products__state-label">{{lng_restaurare}}</span> {{ELSE}} {{IF status == 'replica'}} <span class="basket-products__state-label">{{lng_replica}}</span> {{ELSE}} {{IF status == 'secondhand'}} <span class="basket-products__state-label">{{lng_secondhand}}</span> {{ELSE}} {{IF status == 'new'}} <span class="basket-products__state-label">{{lng_new}}</span> {{END IF}} {{END IF}} {{END IF}} {{END IF}}<br> <strong class="quick-buy__num-detail"># {{num}}</strong> 
        </div>
        <div class="basket-products__actions" style="margin-top:10px;width:120px;">
          <div class="form__input-count-wrap basket-products__count-wrap" style="font:bold 18px/27px 'Sensation';"> {{count}} {{lng_unit}} </div>
          <div id="product-cost-{{id}}" class="products-list__cost basket-products__detail-cost"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </div>
        </div>
      </li>
      {{END products}} 
    </ul>
    {{IF type == 'continue'}} {{IF status == 'wait_payment'}} 
    <form id="form-order-continue" action="/json/order/continue/{{num}}/" method="post"> {{includeBlock('directories', 'directories', 'paymentMethods', userParams)}} <button class="form__submit form__submit_type_main basket-steps__create-btn" type="submit" disabled> {{lng_created_order}} </button> </form>
    {{ELSE}} 
    <h3 class="order-thanks__congratulations">{{lng_congratulations}}</h3>
    {{END IF}} {{ELSE}} 
    <h3 class="order-thanks__congratulations">{{lng_congratulations}}</h3>
    {{END IF}} 
  </div>
  {{END UNLESS}} 
  <div class="layout__sidebar layout__sidebar_type_basket">
    <div class="basket-sidebar">
      <div class="basket-sidebar__panel" style="overflow: hidden;">
        <ul class="basket-user-info__list basket-user-info__list_type_finish">
          <li class="basket-user-info__item basket-user-info__item_type_userbig"> {{user_surname}}<br><em>{{user_secondname}}</em> </li>
          <li class="basket-user-info__item basket-user-info__item_type_phonebig"> {{user_phones}} </li>
          <li class="basket-user-info__item basket-user-info__item_type_emailbig"> {{user_email}} </li>
        </ul>
        <div class="basket-sidebar__panel-item">
          {{IF ord_num!=10009009001}}
          <h4 class="basket-sidebar__panel-caption basket-sidebar__panel-caption_type_delivery"> {{lng_delivery}} </h4>
          <p class="basket-sidebar__value">{{delivery_name}}</p>
          {{END IF}}
          {{IF sum_delivery}}
          <p class="basket-sidebar__value basket-sidebar__value_align_right"> {{IF currency_abb == 'грн.'}} {{sum_delivery}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{sum_delivery}}</span> {{END IF}} </p>
          {{END IF}} 
        </div>
        <div class="basket-sidebar__panel-item">
          <h4 class="basket-sidebar__panel-caption basket-sidebar__panel-caption_type_address"> {{lng_address}} </h4>
          <ul class="basket-user-info__list basket-user-info__list_type_address">
            <li class="basket-user-info__item basket-user-info__item_type_sidebar"> <strong>{{lng_street}}:</strong> {{user_street}}, {{user_build}}, {{lng_flat_abb}} {{user_flat}} </li>
            <li class="basket-user-info__item basket-user-info__item_type_sidebar"> <strong>{{lng_city}}:</strong> {{user_city}} </li>
            <li class="basket-user-info__item basket-user-info__item_type_sidebar"> <strong>{{lng_index}}:</strong> {{user_index}} </li>
            <li class="basket-user-info__item basket-user-info__item_type_sidebar"> <strong>{{lng_country}}:</strong> {{user_country}} </li>
          </ul>
        </div>
        <div class="basket-sidebar__panel-item basket-sidebar__panel-item_type_last">
          <h4 class="basket-sidebar__panel-caption basket-sidebar__panel-caption_type_payment"> {{lng_payment}} </h4>
          <p class="basket-sidebar__value">{{payment_name}}</p>
        </div>
      </div>
      <div class="basket-sidebar__total">
        <h4 class="basket-sidebar__total-caption">{{lng_total_cost}}:</h4>
        <div class="quick-buy__total-cost"> {{IF currency_abb == 'грн.'}} <span id="basket-sidebar-cost">{{total_sum}}</span>&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}<span id="basket-sidebar-cost">{{total_sum}}</span> {{END IF}} </div>
      </div>
    </div>
  </div>
</div>
{{includeScript('footer.tpl')}}
