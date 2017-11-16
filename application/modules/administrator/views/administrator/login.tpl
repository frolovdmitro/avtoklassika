<div id="container">
  <div id="lg-main">
    <div class="lg-header">
      <img src="//s1.avtoclassika.com/img/backend/logo-6289fde9.png"> 
      <h1>{{ $lng_login_caption }}</h1>
    </div>
    <form method="POST" id="sign-in-form" action="/administrator/authenticate/">
      <div class="field-wrap"> <label for="name">{{ $lng_login_email }}</label> <input id="name" name="name" type="text" spellcheck="false" autocomplete="off" maxlength="64" value=""> </div>
      <div class="field-wrap"> <label for="password">{{ $lng_login_password }}</label> <input id="password" name="password" type="password" spellcheck="false" maxlength="32" value=""> </div>
      <img class="admin-login-loader" width="16" height="16" src="//s1.avtoclassika.com/img/backend/loader-951887e6.gif"> <input id="sign-in" type="submit" value="{{ $lng_login_enter }}"/> 
    </form>
    <div class="tooltip"><span></span></div>
  </div>
</div>
