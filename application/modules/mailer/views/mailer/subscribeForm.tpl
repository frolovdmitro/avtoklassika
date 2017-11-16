<div class="subscribe-form">
  <h4>{{lng_mailer_caption}}</h4>
  <div class="subscribe-forms-wrap">
    <form id="subscribe-form" class="clearfix" novalidate method="post" action="/json/subscribe">
      <p>{{lng_mailer_description}}</p>
      <div class="input-wrap"> <label class="icons" for="email">{{lng_mailer_placeholder}}</label><input type="email" name="email" id="email"> </div>
      <button type="submit">{{lng_mailer_subscribe}}</button> 
      <p class="error">{{lng_mailer_email_error}}</p>
    </form>
    <form id="unsubscribe-form" class="hide clearfix" novalidate method="post" action="/json/unsubscribe">
      <p>{{lng_mailer_unsubscribe_text}}</p>
      <div class="input-wrap"> <a class="current-email" href="mailto:{{email}}">{{email}}</a><input type="hidden" name="key" value="{{key}}"> </div>
      <button type="submit">{{lng_mailer_unsubscribe}}</button> 
    </form>
  </div>
</div>
