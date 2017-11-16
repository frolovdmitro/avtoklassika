
{{includeScript('header.tpl')}}
<div class="container">
  <section id="basket" class="">
    <div class="row">
      <div class="container_center clearfix ">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="basket clearfix">
            <div class="content">
              <ul id="tabs">
                <li class="title">
                    {{lng_create_order}}
                </li>
                <li class="ohter one basket-steps__item">
                  <a class="basket-steps__link_step_1" href="#step=1">
                    <span class="step">1</span>
                      {{lng_step_1}}
                  </a>
                </li>
                <li class="ohter basket-steps__item">
                  <a class="basket-steps__link_step_2" href="#step=2">
                    <span class="step">2</span>
                    <!-- {{lng_step_2}} -->
                    Доставка
                  </a>
                </li>
                <li class="ohter basket-steps__item">
                  <a class="basket-steps__link_step_2" href="#step=3">
                    <span class="step">3</span>
                    <!-- {{lng_step_2}} -->
                    Оплата
                  </a>
                </li>
                <li class="ohter basket-steps__item basket-steps__item_state_active">
                  <span class="verification basket-steps__link_step_4">
                    <span class="step">4</span>{{lng_step_3}}
                  </span>
                </li>
              </ul>
              <div id="content">
                  {{UNLESS type}}
                <div id="tab4" id="basket-page" class="ololo clearfix">
                  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 pad_0">
                    <div class="confirmation">
                      <div class="order_processed">
                        <h2> {{lng_order}} #{{num}} {{lng_decorated}} </h2>
                        <p>{{lng_mrms}} {{user_name}}. {{lng_you_order}} #{{num}} {{lng_decorated}}.
                            {{lng_order_thanks_text}}
                        </p>
                      </div>
                      <h3> {{lng_congratulations}} </h3>

                      <div class="order_processed or_1">
                        <p>
                          We are constantly striving to improve the value we deliver in our
                          customer service and product lines.To help us achieve this goal,
                          we are conducting a brief online web survey to assess our performance.
                        </p>
                        <p>
                          Below is a link to a brief web survey with questions around our
                          performance and the future needs and trends you foresee in
                          restoration projects.
                        </p>
                        <a href="http://www.survio.com/survey/d/C9U8B4F6J1L8Q9N3R" target="_blank">Submit survey</a>
                        <p>
                          To show our appreciation, all participants will get 3% discount for next order.
                        </p>
                        <p>
                          For your participation, we thank you in advance. It's our commitment to
                          learn from your feedback and work to improve our services even more
                          based on your suggestions and ideas.
                        </p>
                        <p>
                          Thank you again for your cooperation.
                        </p>
                      </div>
                    </div>
                    <div class="share mob_hidden_768">
                        {{lng_share_friends}}<br><br><br>
                      <div class="order-thanks__share-buttons" style="display:inline-block;">
                        <div class="fb-like" style="float:left;margin-right: 20px;font-size:1px;" data-href="http://avtoclassika.com/" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                        <div class="g-plusone" data-size="medium" data-annotation="inline" style="float:left;margin-right: 20px;" data-width="120" data-href="http://avtoclassika.com/car/{{car_synonym}}/{{autopart_id}}/{{id}}/"></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pad_0">
                    <div class="panel">
                      <ul class="first clearfix">
                        <li class="name">{{user_surname}}<br><em>{{user_secondname}}</em></li>
                        <li class="phone">{{user_phones}}</li>
                        <li class="email"> {{user_email}} </li>
                      </ul>
                      <ul class="middle clearfix">
                        <li>
                        <span class="basket_bar_cost">
                             <span class="rur">P</span>
                            142,50
                        </span>
                        </li>
                      </ul>
                      <ul class="last clearfix">
                        <li class="title adress">{{lng_address}}</li>
                        <li class="street">
                          <span>{{lng_street}}:</span>
                          <span>{{user_street}}, {{user_build}}, {{lng_flat_abb}} {{user_flat}} </span>
                        </li>
                        <li class="siti">
                          <span>{{lng_city}}:</span>
                          <span>{{user_city}}</span>
                        </li>
                        <li class="index">
                          <span>{{lng_index}}:</span>
                          <span>{{user_index}}</span>
                        </li>
                        <li class="country">
                          <span>{{lng_country}}:</span>
                          <span>{{user_country}}</span>
                        </li>
                      </ul>
                      <div class="pay clearfix">
                        <h3>{{lng_payment}}</h3>
                        <p class="payment">{{payment_name}}</p>
                      </div>
                    </div>
                    <div class="total_summ clearfix">
                      <h4>{{lng_total_cost}}:</h4>
                      <span class="basket_bar_cost">
                          {{IF currency_abb == 'грн.'}} <span id="basket-sidebar-cost">{{total_sum}}</span>&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}<span id="basket-sidebar-cost">{{total_sum}}</span> {{END IF}}
                      </span>
                    </div>
                  </div>
                </div>
                  {{END UNLESS}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
{{includeScript('footer.tpl')}}

