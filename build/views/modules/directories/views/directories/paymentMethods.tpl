<div class="payments-deliveries {{IF basket}}payments-deliveries_type_mini{{END IF}} payments-deliveries_align_left">
  <h4 class="payments-deliveries__header">{{lng_payment}}</h4>
  <ul class="payments-deliveries__list">
    {{BEGIN methods}}
    {{IF ord_dl != 1}}
    <li class="payments-deliveries__item"> {{IF basket}} <input type="radio" name="payment" id="payment-{{id}}" class="payments-deliveries__radio" value="{{id}}"> {{END IF}} <label for="payment-{{id}}" class="payments-deliveries__link {{if(_last, ' payments-deliveries__link_position_last')}}"><img class="payments-deliveries__image" src="/img/payments/{{type}}.png" alt="{{name}}" width="71" height="57"> {{name}} {{IF description}} <span data-hint="{{description}}" class="hint--top{{if(multiline, ' hint--multiline')}} icons__help"></span> {{END IF}} </label></li>
    {{END IF}}
    {{END methods}}
  </ul>
</div>
