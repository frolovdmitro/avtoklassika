<form id="auth-form" class="auth__form" method="post" action="/users/auth/" novalidate>
  <h2 class="auth__caption">{{lng_authenticate}}</h2>
  <ul class="auth__fields-list">
    <li class="auth__fields-item">
      <label class="auth__label" for="auth-form__email">Email:</label>
      <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="email" name="email" id="auth-form__email" value=""><span class="form__input-error error"></span> </div>
    </li>
    <li class="auth__fields-item" style="position: relative;">
      <div class="auth-popup__input-wrap_type_password">
        <label class="auth__label" for="auth-form__password">{{lng_password}}:</label>
        <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="password" name="password" id="auth-form__password" value=""> </div>
      </div>
      <p class="auth-popup__lost-text auth-popup__lost-text_type_main" data-text="{{lng_repair_text}}"> {{lng_repair_text}} </p>
      <div class="auth-popup__lost-password auth-popup__lost-password_type_main"> <a class="auth-popup__lost-password-link" href="/auth/lost/" data-state="lost" data-lost-caption="{{lng_lost_password}}" data-login-caption="{{lng_logined}}"> {{lng_lost_password}} </a> </div>
    </li>
  </ul>
  <hr class="auth__separator">
    {{includeBlock('users', 'users', 'registerAuthFooter')}}
</form>
