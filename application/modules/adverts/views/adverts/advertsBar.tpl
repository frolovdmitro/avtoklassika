<!--{{IF adverts}}
<div class="message-bar">
  <h3 class="ribbon ribbon_type_inner message-bar__caption">
    <a class="ribbon__link" href="/ads/">{{lng_adverts}}</a>
  </h3>
  <div id="message-bar__slider" class="message-bar__slider flexslider">
    <ul class="message-bar__list slides">
      {{BEGIN adverts}}
      <li class="message-bar__item">
        <h4 class="message-bar__item-caption">{{name}}</h4>
        <a class="message-bar__link" href="/ads/{{type}}/{{id}}/">
          <div class="message-bar__link-table">
            <div class="message-bar__link-table-cell">
              <img class="message-bar__image" src="{{url_staticServer}}{{image}}" alt="{{name}}" width="180">
            </div>
          </div>
        </a>
      </li>
      {{END adverts}}
    </ul>
  </div>
  <a id="add_adv" class="message-bar__button" href="/json/ads/add-form.html">
    {{lng_add_advert}}
  </a>
</div>
{{END IF}}-->


{{IF adverts}}
<div class="slider_announcing">
  <div id="slider_announcing">
    <h1 class="title"><a href="/ads/">{{lng_adverts}}</a></h1>
    <div class="owl-carousel owl-theme">
      {{BEGIN adverts}}
        <div class="item">
          <div class="caption_up">{{name}}</div>
          <div class="item_group">
            <div class="image">
              <a href="/ads/{{type}}/{{id}}/">
                <img src="{{url_staticServer}}{{image}}" alt="{{name}}">
              </a>
            </div>
          </div>
        </div>
      {{END adverts}}
    </div>
    <a id="add_adv" class="my_button message-bar__button" href="/json/ads/add-form.html">{{lng_add_advert}}</a>
  </div>
</div>
{{END IF}}