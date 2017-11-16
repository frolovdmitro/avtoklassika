<div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 pad_0 my_form widht_100 mob_hidden_768">
  <h4 class="title">{{lng_subscription}}</h4>
  <form action="/users/subscribe/" method="post"
          {{if email}} style="opacity:0;visibility:hidden;" {{end if}} class="subscription-bar__form">

    <div class="mail">
      <input type="email" name="email" id="subscription-bar-form__email" placeholder="E-mail">
      <span class="form__input-error error subscription-bar__error">Вы не указали email</span>
    </div>
    <div class="name">
      <input type="text" name="name" id="subscription-bar-form__name" placeholder="{{lng_name}}">
      <span class="form__input-error error subscription-bar__error">Вы не указали email</span>
    </div>
    <div class="submit">
      <input type="submit" value="{{lng_subscribe}}">
    </div>
  </form>
  <p class="subscription-bar__success"
          {{if email}} style="opacity:1;visibility:visible; font-size: 14px;" {{end if}}>
      {{lng_subscribe_success}}
    <a class="subscription-bar__email-link" href="mailto:{{email}}">
        {{email}}
    </a><br>
    <a class="subscription-bar__unsubscribe-link" href="/users/unsubscribe/" data-email="{{email}}">{{lng_unsubscribe}}</a>
  </p>
    {{includeBlock('users', 'users', 'recommendBar')}}
</div>