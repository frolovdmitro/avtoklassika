<li class="">
  {{IF basket}}
    <input type="radio" name="delivery" id="delivery-{{id}}" data-usd-cost="{{cost_usd_delivery}}" data-cost="{{cost_delivery_unformat}}" class="payments-deliveries__radio" value="{{id}}">
  {{END IF}}
  <label for="delivery-{{id}}">
    <img class="payments-deliveries__image" src="/img/deliveries/{{type}}.png" alt="" width="71" height="57">
    {{name}}
    {{IF description}}
      <a href="" class="htooltip">
        <span>{{description}}</span>
      </a>
    {{END IF}}
  </label>
</li>
