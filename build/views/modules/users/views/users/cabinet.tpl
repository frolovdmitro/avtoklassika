{{includeScript('header.tpl')}} 
<div class="layout__content">
  <div class="layout__content">
    <h1 class="autoparts__h1 cabinet__h1 ribbon ribbon_type_right-inner"> {{lng_my_cabinet}} </h1>

    <h2 class="hot-products__header ribbon ribbon_type_right-outer">
      {{lng_my_discount}}: {{discount}}%
    </h2>

    <a class="cabinet__logout-link" href="/users/logout/"> {{lng_exit_from_cabinet}} </a> 
    <div class="tabs">
      <ul class="tabs__nav">
        <li class="tabs__nav-item tabs__nav-item_pos_first tabs__nav-item_state_current"><a class="tabs__nav-link" href="#tab-info"> {{lng_cabinet_info}} </a></li>
        <li class="tabs__nav-item"><a class="tabs__nav-link" href="#tab-address"> {{lng_cabinet_addresses}} </a></li>
        <li class="tabs__nav-item"><a class="tabs__nav-link" href="#tab-history"> {{lng_cabinet_history}} </a></li>
        <li class="tabs__nav-item"><a class="tabs__nav-link" href="#tab-adverts">
            Объявления
          </a></li>
      </ul>
      <div id="tab-info" class="tabs__content tabs__content_state_current">
        <a class="cabinet__edit-link" href="#edit">{{lng_edit}}</a> 
        <form class="cabinet__form" method="post" action="/cabinet/save/info/" novalidate>
          <ul class="cabinet__info">
            <li class="cabinet__info-item">
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_name cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input cabinet__form-input_style_large" type="text" name="name" id="cabinet__name" disabled placeholder="{{lng_not_specified}}*" value="{{name}}"><span class="form__input-error error"></span> </div>
            </li>
            <li class="cabinet__info-item">
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_tel cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input cabinet__form-input_style_large" type="tel" name="tel" id="cabinet__tel" disabled placeholder="{{lng_not_specified}}*" value="{{phone}}"><span class="form__input-error error"></span> </div>
            </li>
            <li class="cabinet__info-item">
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_password cabinet__form-input-wrap_type_disabled"> <span style="position: absolute; top: 10px; left: 0; font-size: 36px; line-height: 1;">*</span><input class="form__input form__input-password form__input_type_contrast cabinet__form-input cabinet__form-input_style_large" type="password" name="password" id="cabinet__password" disabled data-show-txt="{{lng_show}}" data-hide-txt="{{lng_hide}}" placeholder="{{lng_change_password}}" value=""><span class="form__input-error error"></span> </div>
            </li>
            <li class="cabinet__info-item">
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_email cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input" type="text" name="email" id="cabinet__email" disabled {{unless not_email}}readonly{{end unless}} placeholder="Email*" value="{{UNLESS not_email}}{{email}}{{END UNLESS}}"><span class="form__input-error error"></span> </div>
            </li>
          </ul>
          <button class="form__submit form__submit_type_main cabinet__submit cabinet__submit_state_hidden" type="submit"> {{lng_save_changes}} </button> 
        </form>
      </div>
      <div id="tab-history" class="tabs__content"> {{includeBlock('users', 'users', 'orders')}} </div>

      <div id="tab-adverts" class="tabs__content">
        {{includeBlock('users', 'users', 'adverts')}}
      </div>

      <div id="tab-address" class="tabs__content">
        <a class="cabinet__edit-link" href="#edit">{{lng_edit}}</a> 
        <form class="cabinet__form cabinet__form_with_label" method="post" action="/cabinet/save/address/" novalidate>
          <ul class="cabinet__info">
            <li class="cabinet__info-item">
              <label class="cabinet__label" for="cabinet_country"> {{lng_country}}: </label> 
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_disabled">
                <select class="form__select cabinet__form-select form__selector_size_big cabinet__form-input_style_large" name="country" id="cabinet_country" disabled>
                  <option value="">{{lng_not_specified}}</option>
                  {{BEGIN countries}} <option {{if selected}}selected{{end if}} value="{{id}}"> {{name}} </option> {{END countries}} 
                </select>
                <span class="form__input-error error"></span> 
              </div>
            </li>
            <li class="cabinet__info-item">
              <label class="cabinet__label" for="cabinet_city"> {{lng_city}}: </label> 
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input cabinet__form-input_style_large" type="text" name="city" id="cabinet__city" disabled placeholder="{{lng_not_specified}}" value="{{city}}"><span class="form__input-error error"></span> </div>
            </li>
            <li class="cabinet__info-item">
              <label class="cabinet__label" for="cabinet_index"> {{lng_index}}: </label> 
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input" type="text" name="index" id="cabinet__index" disabled placeholder="{{lng_not_specified}}" value="{{index}}"><span class="form__input-error error"></span> </div>
            </li>
            <li class="cabinet__info-item">
              <label class="cabinet__label" for="cabinet_street"> {{lng_street}}: </label> 
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input" type="text" name="street" id="cabinet__street" disabled placeholder="{{lng_not_specified}}" value="{{street}}"><span class="form__input-error error"></span> </div>
            </li>
            <li class="cabinet__info-item">
              <label class="cabinet__label" for="cabinet_build"> {{lng_build}}: </label> 
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input" type="text" name="build" id="cabinet__build" disabled placeholder="{{lng_not_specified}}" value="{{build}}"><span class="form__input-error error"></span> </div>
            </li>
            <li class="cabinet__info-item">
              <label class="cabinet__label" for="cabinet_flat">Квартира:</label> 
              <div class="form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_disabled"> <input class="form__input form__input_type_contrast cabinet__form-input" type="text" name="flat" id="cabinet__flat" disabled placeholder="{{lng_not_specified}}" value="{{flat}}"><span class="form__input-error error"></span> </div>
            </li>
          </ul>
          <button class="form__submit form__submit_type_main cabinet__submit cabinet__submit_state_hidden" type="submit"> {{lng_save_changes}} </button> 
        </form>
      </div>
    </div>
  </div>
</div>
{{includeScript('footer.tpl')}}
