{{IF adverts}}
<div class="content clearfix">
  {{BEGIN adverts}}
    <div class="item clearfix">
      <h2><a class="ads-list__caption-link" href="/ads/{{type}}/{{id}}/"> {{name}} </a></h2>
      <div class="item_group">
        {{IF image}}
        <div class="image" href="/ads/{{type}}/{{id}}/">
          <div class="img">
            <a href="/ads/{{type}}/{{id}}/">
              <img src="{{url_staticServer}}{{image}}" alt="{{name}}">
            </a>
          </div>
          <div>
            <span class="ads-list__cost{{IF cost}} _print-cost{{END IF}}" data-usd-cost="{{cost_usd}}"
                data-cost="{{cost_unformat}}"> {{IF cost}} {{IF currency_abb == 'грн.'}}
                {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;
                <small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}
            <span class="rur">{{currency_abb}}</span>
                {{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} {{ELSE}}
                {{lng_contract_price}} {{END cost}} </span>
          </div>
        </div>
        {{END IF}}
        <div class="info">
          <p> {{text}} </p>
          <ul class="clearfix">
            <li class="location"> {{user_city}} </li>
            <li class="phone"><a href="tel:{{user_phone_unformat}}">{{user_phone}}</a></li>
            <li class="user"> {{user_name}} </li>
            <li class="mail"><a href="mailto:{{user_email}}">{{user_email}}</a></li>
          </ul>
        </div>
      </div>
    </div>
  {{END adverts}}
</div>
{{END IF}}
