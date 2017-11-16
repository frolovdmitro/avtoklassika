{{UNLESS authed}} 
<div id="myModal_bill_item" class="">
    <div class="modaldialog">
      <div class="modalcontent">
        <div class="modalbody clearfix">
          <form id="auth-form" class="auth-popup__form" action="/users/auth/" method="POST">
            <div class="form__input-wrap auth-popup__input-wrap">
              <input class="form__input auth-popup__input" type="email" name="email" placeholder="E-mail*" id="auth-form__email">
              <span class="form__input-error error"></span>
            </div>
            <input type="password" name="password" placeholder="Пароль*" class="form__input-wrap auth-popup__input-wrap auth-popup__input-wrap_type_password">
            <input class="my_button" type="submit" value="Вход">
            <div class="auth-popup__lost-password"><a class="lost_password auth-popup__lost-password-link" href="/auth/lost/" data-state="lost" data-lost-caption="Забыли пароль?" data-login-caption="Войти?" href="">Забыли пароль?</a></div>
          </form>
          <div class="enter_via clearfix">
            <h4>{{lng_enter_with}}</h4>
            <ul class="clearfix">
              {{includeBlock('users', 'users', 'oAuthLinks')}}
            </ul>
          </div>
          <a class="fast_reg" data-toggle="modal" data-target="#fast_reg">Быстрая регистрация</a>
        </div>
      </div>
    </div>
  </div>
</div>
{{END UNLESS}}
