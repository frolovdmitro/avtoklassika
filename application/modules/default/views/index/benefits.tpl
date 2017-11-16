<!--<div class="benefits">
  <div class="benefits__wrap">
    <h2 class="benefits__header ribbon ribbon_type_right">{{lng_our_benefits}}</h2>
    <ul class="benefits__list">
      <li class="benefits__item benefits__item_type_{{config_benefits_one_type}}">
        <div class="benefits__item-icon benefits__item-icon_type_refund"></div>
        <strong class="benefits__item-caption">{{lng_benefits_one_name}}</strong>
        <div class="benefits__hint">
          <strong>{{lng_benefits_one_name}}</strong>
          <p>{{lng_benefits_one_description}}</p>
        </div>
      </li>
      &nbsp;
      <li class="benefits__item benefits__item_type_{{config_benefits_two_type}}">
        <div class="benefits__item-icon benefits__item-icon_type_payments"></div>
        <strong class="benefits__item-caption">{{lng_benefits_two_name}}</strong>
        <div class="benefits__hint">
          <strong>{{lng_benefits_two_name}}</strong>
          <p>{{lng_benefits_two_description}}</p>
        </div>
      </li>
      &nbsp;
      <li class="benefits__item benefits__item_type_{{config_benefits_three_type}}">
        <div class="benefits__item-icon benefits__item-icon_type_delivery"></div>
        <strong class="benefits__item-caption">{{lng_benefits_three_name}}</strong>
        <div class="benefits__hint">
          <strong>{{lng_benefits_three_name}}</strong>
          <p>{{lng_benefits_three_description}}</p>
        </div>
      </li>
    </ul>

  </div>
</div>-->

<div class="container">
  <section id="advantage">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 pad_0 mob_hidden_900">
          <div class="advantage clearfix">
            <h1 class="title">{{lng_our_benefits}}</h1>
            <ul class="benefits_list">
              <li class="benefits_item benefits_item_type_">
                <div class="benefits_item-icon benefits_item-icon_type_refund"></div>
                <strong class="benefits_item-caption">{{lng_benefits_one_name}}</strong>
                <div class="benefits_hint">
                  <strong>{{lng_benefits_one_name}}</strong>
                  <p>{{lng_benefits_one_description}}</p>
                </div>
              </li>
              <li class="benefits_item benefits_item_type_">
                <div class="benefits_item-icon benefits_item-icon_type_payments"></div>
                <strong class="benefits_item-caption">{{lng_benefits_two_name}}</strong>
                <div class="benefits_hint">
                  <strong>{{lng_benefits_two_name}}</strong>
                  <p>{{lng_benefits_two_description}}</p>
                </div>
              </li>
              <li class="benefits_item benefits_item_type_">
                <div class="benefits_item-icon benefits_item-icon_type_delivery"></div>
                <strong class="benefits_item-caption">{{lng_benefits_three_name}}</strong>
                <div class="benefits_hint">
                  <strong>{{lng_benefits_three_name}}</strong>
                  <p>{{lng_benefits_three_description}}</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 pad_0 mob_visible_900">
          {{includeBlock('news', 'news', 'newsBar')}}
        </div>
      </div>
    </div>
  </section>
</div>
