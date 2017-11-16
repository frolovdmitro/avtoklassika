<form class="auth__form" method="post" action="/cabinet/save/email/" novalidate>
  <h2 class="auth__caption">{{lng_input_your_email}}</h2>
  <ul class="auth__fields-list">
    <li class="auth__fields-item">
      <label class="auth__label" for="auth__email">Email:</label> 
      <div class="form__input-wrap auth__form-input-wrap"> <input class="form__input form__input_type_contrast" type="email" name="email" id="auth__email" value=""><span class="form__input-error error"></span> </div>
    </li>
  </ul>
  <hr class="auth__separator">
  <button class="form__submit form__submit_type_big" type="submit"> {{lng_next}} </button> 
</form>
