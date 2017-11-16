{{IF page == 0}} <div class="layout__content-with-sidebar layout__content_type_basket" id="basket-page" data-num="0"> <div class="tabs"> <ul class="tabs__nav"> <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-register"> {{lng_i_new_client}} </a></li> <li class="tabs__nav-item"><a class="tabs__nav-link" href="#tab-auth"> {{lng_i_old_client}} </a></li> </ul> <div id="tab-register" class="tabs__content tabs__content_state_current"> {{includeBlock('users', 'users', 'mainRegisterForm')}} </div> <div id="tab-auth" class="tabs__content"> {{includeBlock('users', 'users', 'mainAuthForm')}} </div> </div> </div> <div class="layout__sidebar layout__sidebar_type_basket"> {{includeBlock('orders', 'orders', 'basketSidebar')}} </div> {{END IF}} {{IF page == 1}} <div class="layout__content" id="basket-page" data-num="1"> {{includeBlock('orders', 'orders', 'basketProducts')}} </div> {{END IF}} {{IF page == -1}} <div class="layout__content-with-sidebar layout__content_type_basket" id="basket-page" data-num="0.5"> <div class="tabs"> <ul class="tabs__nav"> <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-user-info"> {{lng_user_data}} </a></li> </ul>

  <div id="tab-user-info" class="tabs__content tabs__content_state_current"> {{includeBlock('users', 'users', 'saveUserMainInfoForm')}} </div>
</div> </div> <div class="layout__sidebar layout__sidebar_type_basket"> {{includeBlock('orders', 'orders', 'basketSidebar')}} </div> {{END IF}}

{{IF page == 2}} <div class="layout__content-with-sidebar layout__content_type_basket" id="basket-page" data-num="2">
  <form style="display:none;" id="form-page-2" action="/json/order/create/" method="post">
    <div class="payments-deliveries__wrap"> <div id="payments-deliveries-wrap">
      {{includeBlock('directories', 'directories', 'paymentMethods', userParams)}}
      {{includeBlock('directories', 'directories', 'deliveryMethods', userParams)}}
    </div>
    <div class="basket-steps__comment-delivery-wrap"> <a href="#add-comment" class="basket-steps__add-comment"> {{lng_add_comment}} </a> <div class="payments-deliveries__delivery-cost"> {{lng_delivery_cost}} <span class="payments-deliveries__delivery-cost-value _print-cost" data-cost="{{cost_delivery_unformat}}" data-usd-cost="{{cost_delivery_usd}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_delivery_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{currency_abb}}{{cost_delivery}}0 {{END IF}} </span> </div> <textarea id="basket-comment" class="form__input form__input_type_contrast basket-steps__comment-textarea" name="comment" rows="5" placeholder="{{lng_comment_text}}"></textarea> </div> <button class="form__submit form__submit_type_main basket-steps__create-btn" type="submit" disabled> {{lng_created_order}} </button> </div>
  </form>
  {{includeBlock('users', 'users', 'basketUserInfo')}}

</div> <div class="layout__sidebar layout__sidebar_type_basket"> {{includeBlock('orders', 'orders', 'basketSidebar')}} </div> {{END IF}}
