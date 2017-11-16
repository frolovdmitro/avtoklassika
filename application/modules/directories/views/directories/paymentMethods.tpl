<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pad_0">
    <div class="payment_delivery">
      <h4 class="delivery_buy_title">{{lng_payment}}</h4>
      <ul class="clearfix">
          {{BEGIN methods}}
            {{IF ord_dl != 1}}
              <li class="">
                  {{IF basket}}
                <input type="radio" name="payment" id="payment-{{id}}" class="payments-deliveries__radio" value="{{id}}">
                  {{END IF}}
                <label for="payment-{{id}}">
                  <img class="payments-deliveries__image" src="/img/payments/{{type}}.png" alt="{{name}}" width="71" height="57">
                    {{name}}
                    {{IF description}}
                      <a href="" class="htooltip">
                        <span>
                          {{description}}
                        </span>
                      </a>
                    {{END IF}}
                </label>
              </li>
            {{END IF}}
          {{END methods}}
      </ul>
    </div>
</div>