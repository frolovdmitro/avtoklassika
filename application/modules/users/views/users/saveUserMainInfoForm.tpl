<form id="user-info__form" class="auth__form" method="post" action="/cabinet/save/full/" novalidate>
  {{IF name_email_empty}} 
  <h2 class="auth__caption">{{lng_cabinet_info}}</h2>
  <ul class="auth__fields-list">
    {{END IF}} {{UNLESS name}} 
    <li class="auth__fields-item">
      <label class="auth__label" for="user-info__name">{{lng_name}}:</label> 
      <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="text" name="name" id="user-info__name" value="{{name}}"><span class="form__input-error error"></span> </div>
    </li>
    {{END UNLESS}} {{UNLESS email}} 
    <li class="auth__fields-item">
      <label class="auth__label" for="user-info__email">Email:</label> 
      <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="text" name="email" id="user-info__email" value="{{email}}"><span class="form__input-error error"></span> </div>
    </li>
    {{END UNLESS}} {{IF name_email_empty}} 
  </ul>
  {{END IF}} 
  <h2 class="auth__caption">{{lng_address_delivery}}</h2>
  <ul class="auth__fields-list">
    <li class="auth__fields-item">
      <label class="auth__label" for="user-info__street">{{lng_street}}:</label> 
      <div class="form__input-wrap auth__form-input-wrap auth__form-input-wrap_type_street"> <input class="form__input form__input_type_contrast" type="text" name="street" id="user-info__street" value="{{street}}" autofocus><span class="form__input-error error"></span> </div>
      <label class="auth__label auth__label_type_build" for="user-info__build"> {{lng_build}}: </label> 
      <div class="form__input-wrap auth__form-input-wrap auth__form-input-wrap_type_build"> <input class="form__input form__input_type_contrast" type="text" name="build" id="user-info__build" value="{{build}}"> </div>
      <label class="auth__label auth__label_type_flat" for="user-info__flat"> {{lng_flat_abb}}: </label> 
      <div class="form__input-wrap auth__form-input-wrap auth__form-input-wrap_type_flat"> <input class="form__input form__input_type_contrast" type="text" name="flat" id="user-info__flat" value="{{flat}}"> </div>
    </li>
    <li class="auth__fields-item">
      <label class="auth__label" for="user-info__city">{{lng_city}}:</label> 
      <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="text" name="city" id="user-info__city" value="{{city}}"><span class="form__input-error error"></span> </div>
    </li>
    <li class="auth__fields-item">
      <label class="auth__label" for="user-info__index">{{lng_index}}:</label> 
      <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="text" name="index" id="user-info__index" value="{{index}}"><span class="form__input-error error"></span> </div>
    </li>
    <li class="auth__fields-item">
      <label class="auth__label" for="user-info__country"> {{lng_country}}: </label> 
      <div class="form__input-wrap auth__form-input-wrap">
        <select class="form__select" name="country" id="user-info__country">
          <option value="">{{lng_not_specified}}</option>
          {{BEGIN countries}} <option {{if selected}}selected{{end if}} value="{{id}}"> {{name}} </option> {{END countries}} 
        </select>
        <span class="form__input-error error"></span> 
      </div>
    </li>
    <li class="auth__fields-item">
      <label class="auth__label" for="user-info__phone">{{lng_phone}}:</label> 
      <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="text" name="phone" id="user-info__phone" value="{{phone}}"><span class="form__input-error error"></span> </div>
    </li>
  </ul>
  <hr class="auth__separator">
  <button class="form__submit form__submit_type_big" type="submit"> {{lng_next}} </button> 
</form>
