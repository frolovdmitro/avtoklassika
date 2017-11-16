<div class="basket-user-info">
  <a class="cabinet__edit-link" href="#edit">{{lng_edit}}</a>
  <div class="basket-user-info__block">
    <h4 class="basket-user-info__caption basket-user-info__caption_type_user"> {{lng_recipient}} </h4>
    <ul class="basket-user-info__list">
      <li class="basket-user-info__item basket-user-info__item_type_user"><span id="basket-user-info__surname">{{surname}}</span><br>
      <em id="basket-user-info__secondname">{{secondname}}</em></li>
      <li class="basket-user-info__item basket-user-info__item_type_phone"> {{phone}} </li>
      <li class="basket-user-info__item basket-user-info__item_type_email"> {{email}} </li>
    </ul>
  </div>
  <div class="basket-user-info__block">
    <h4 class="basket-user-info__caption basket-user-info__caption_type_address">{{lng_address}}</h4>
    <ul class="basket-user-info__list basket-user-info__list_type_address">
      <li class="basket-user-info__item"><strong>{{lng_street}}:</strong><span id="basket-user-info__street">{{street}}</span>,
        <span id="basket-user-info__build">{{build}}</span>,
        {{lng_flat_abb}} <span id="basket-user-info__flat">{{flat}}</span>
      </li>
      <li class="basket-user-info__item"><strong>{{lng_city}}:</strong><span id="basket-user-info__city">{{city}}</span></li>
      <li class="basket-user-info__item"><strong>{{lng_index}}:</strong><span id="basket-user-info__index">{{index}}</span></li>
      <li class="basket-user-info__item"><strong>{{lng_country}}:</strong><span id="basket-user-info__country">{{country}}</span></li>
    </ul>
  </div>
  <form id="user-data__form" class="auth__form form_state_hidden"
    method="post" action="/order-user-info/save/" novalidate style="clear: both;padding-top: 10px;">
    <ul class="auth__fields-list">
      <li class="auth__fields-item"><label class="auth__label" for="user-info__name">{{lng_name}}:</label>
      <div class="form__input-wrap auth__form-input-wrap">
        <input class="form__input form__input_type_contrast" type="text" name="name" id="user-info__name"
          value="{{name}}"><span class="form__input-error error"></span>
      </div>
      </li>
    </ul>
    <ul class="auth__fields-list">
      <li class="auth__fields-item"><label class="auth__label"
        for="user-info__street">{{lng_street}}:</label>
        <div class="form__input-wrap auth__form-input-wrap">
          <input class="form__input form__input_type_contrast" type="text" name="street" id="user-info__street" value="{{street}}"><!--
          --><span class="form__input-error error"></span>
        </div>
        <!--
        <label class="auth__label auth__label_type_build" for="user-info__build">
          {{lng_build}}:
        </label>
        <div class="form__input-wrap auth__form-input-wrap auth__form-input-wrap_type_build">
        </div>
        <label class="auth__label auth__label_type_flat" for="user-info__flat"> {{lng_flat_abb}}: </label>
        <div class="form__input-wrap auth__form-input-wrap auth__form-input-wrap_type_flat">
        </div>
        -->
        <input class="form__input form__input_type_contrast" type="hidden" name="build" id="user-info__build" value="{{build}}">
        <input class="form__input form__input_type_contrast" type="hidden" name="flat" id="user-info__flat" value="{{flat}}">
      </li>
      <li class="auth__fields-item"><label class="auth__label" for="user-info__city">{{lng_city}}:</label>
        <div class="form__input-wrap auth__form-input-wrap">
          <input class="form__input form__input_type_contrast" type="text" name="city" id="user-info__city" value="{{city}}"><!--
          --><span class="form__input-error error"></span>
        </div>
      </li>
      <li class="auth__fields-item"><label class="auth__label" for="user-info__index">{{lng_index}}:</label>
        <div class="form__input-wrap auth__form-input-wrap">
          <input class="form__input form__input_type_contrast" type="text" name="index" id="user-info__index"
            value="{{index}}"><span class="form__input-error error"></span>
        </div>
      </li>
      <li class="auth__fields-item"><!--
        --><label class="auth__label" for="user-info__country">
          {{lng_country}}:
        </label>
        <div class="form__input-wrap auth__form-input-wrap">
          <select class="form__select" name="country" id="user-info__country">
            <option value="">{{lng_not_specified}}</option>
            {{BEGIN countries}}
              <option {{if selected}}selected{{end if}} value="{{id}}">{{name}}</option>
            {{END countries}}
          </select>
          <span class="form__input-error error"></span>
        </div>
      </li>
      <li class="auth__fields-item"><label class="auth__label" for="user-info__phone">{{lng_phone}}:</label>
        <div class="form__input-wrap auth__form-input-wrap">
          <input class="form__input form__input_type_contrast" type="text" name="phone"
            id="user-info__phone" value="{{phone}}"><span class="form__input-error error"></span>
        </div>
      </li>
    </ul>
    <hr class="auth__separator">
    <button class="form__submit form__submit_type_main" type="submit" style="float:right;">
      {{lng_save_changes2}}
    </button>
  </form>
</div>
