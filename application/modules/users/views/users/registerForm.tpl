<form id="user-register" class="modal__form" action="/users/register/" novalidate>
  <div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="text" name="name" id="user-register__name" placeholder="{{lng_name}}*"><span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="email" name="email" id="user-register__email" placeholder="E-mail*"><span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="password" name="password" id="user-register__password" placeholder="{{lng_password}}*"><span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="password" name="password_retype" id="user-register__password_retype" placeholder="{{lng_password_retype}}*"><span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_100">
    <div class="form__offer-text"> {{lng_mails_public_offer_text}} </div>
  </div>
  <button class="form__submit form__submit_type_main ad__submit" type="submit"> {{lng_register}} </button> 
</form>
