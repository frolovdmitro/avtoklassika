<form id="add-review" class="modal__form" action="/users/add-review/" novalidate>
  <div class="form__input-wrap form__input-wrap_size_divide">
    <input class="form__input form__input_type_contrast" type="text" name="name" id="add-review__name" placeholder="{{lng_name}}*">
    <span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
    <input class="form__input form__input_type_contrast" type="email" name="email" id="add-review__email" placeholder="E-mail*">
    <span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_divide">
    <select class="form__select" name="quality_service" id="add-review__quality_service">
      <option value="">{{lng_quality_service}}</option>
      <option value="excellent">{{lng_excellent}}</option>
      <option value="well">{{lng_well}}</option>
      <option value="normal">{{lng_normal}}</option>
      <option value="bad">{{lng_bad}}</option>
      <option value="awful">{{lng_awful}}</option>
    </select>
    <span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
    <select class="form__select" name="usability_site" id="add-review__usability_site">
      <option value="">{{lng_usability_site}}</option>
      <option value="excellent">{{lng_excellent}}</option>
      <option value="well">{{lng_well}}</option>
      <option value="normal">{{lng_normal}}</option>
      <option value="bad">{{lng_bad}}</option>
      <option value="awful">{{lng_awful}}</option>
    </select>
    <span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_divide">
    <select class="form__select" name="quality_goods" id="add-review__quality_goods">
      <option value="">{{lng_quality_goods}}</option>
      <option value="excellent">{{lng_excellent}}</option>
      <option value="well">{{lng_well}}</option>
      <option value="normal">{{lng_normal}}</option>
      <option value="bad">{{lng_bad}}</option>
      <option value="awful">{{lng_awful}}</option>
    </select>
    <span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
    <select class="form__select" name="shipping" id="add-review__shipping">
      <option value="">{{lng_shipping}}</option>
      <option value="excellent">{{lng_excellent}}</option>
      <option value="well">{{lng_well}}</option>
      <option value="normal">{{lng_normal}}</option>
      <option value="bad">{{lng_bad}}</option>
      <option value="awful">{{lng_awful}}</option>
    </select>
    <span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_100">
    <textarea class="form__input form__input_type_contrast" name="text" id="add-review__text" rows="8" placeholder="{{lng_review_text}}*"></textarea>
      <span class="form__input-error error"></span>
  </div>
  <button class="form__submit form__submit_type_main ad__submit" type="submit"> {{lng_send_review}} </button>
</form>
