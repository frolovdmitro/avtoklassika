<form id="user-register" class="auth__form" method="post" action="/users/register/" novalidate data-redirect="/basket/"> <h2 class="auth__caption">{{lng_register}}</h2> <ul class="auth__fields-list"> <li class="auth__fields-item"><label class="auth__label" for="user-register__name">{{lng_fio}}:</label> <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="text" name="name" id="user-register__name" autofocus><span class="form__input-error error"></span> </div> </li> <li class="auth__fields-item"><label class="auth__label" for="user-register__email">Email:</label> <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="email" name="email" id="user-register__email" value=""><span class="form__input-error error"></span> </div> </li> <li class="auth__fields-item"><label class="auth__label" for="user-register__password">{{lng_password}}:</label> <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="password" name="password" id="user-register__password" value=""><span class="form__input-error error"></span> </div> </li> <li class="auth__fields-item"><label class="auth__label" for="user-register__password_retype"> {{lng_password_retype}}: </label> <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="password" name="password_retype" id="user-register__password_retype"><span class="form__input-error error"></span> </div> </li> </ul> <hr class="auth__separator"> {{includeBlock('users', 'users', 'registerAuthFooter')}} </form>