{{UNLESS authed}} <div class="auth-popup auth-popup_state_invisible">
   <form id="auth-form" class="auth-popup__form" method="post" action="/users/auth/" novalidate> <div class="form__input-wrap auth-popup__input-wrap">
     <input class="form__input auth-popup__input" type="email" name="email" id="auth-form__email" placeholder="Email*" autofocus><span class="form__input-error error"></span>
   </div> <div class="form__input-wrap auth-popup__input-wrap auth-popup__input-wrap_type_password">
     <input class="form__input auth-popup__input" type="password" name="password" id="auth-form__password" placeholder="{{lng_password}}*"> </div>
     <p class="auth-popup__lost-text" data-text="{{lng_repair_text}}"> {{lng_repair_text}} </p>
     <button class="form__submit form__submit_type_main auth-popup__submit" type="submit" data-login-caption="{{lng_enter}}" data-repair-caption="{{lng_repair_password}}"> {{lng_enter}} </button>
      <div class="auth-popup__lost-password"> <a class="auth-popup__lost-password-link" href="/auth/lost/" data-state="lost" data-lost-caption="{{lng_lost_password}}" data-login-caption="{{lng_logined}}"> {{lng_lost_password}} </a> </div>
     </form> <div class="auth-popup__social"> <h4 class="auth-popup__social-caption">{{lng_enter_with}}</h4> <ul class="auth-popup__social-list">
       {{includeBlock('users', 'users', 'oAuthLinks')}} </ul> 
     </div> <div class="auth-popup__register"> <a class="auth-popup__register-link" href="/json/users/register-form.html" rel="modal:open"> {{lng_quick_register}} </a> </div>
  </div> {{END UNLESS}}
