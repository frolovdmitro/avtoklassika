{{IF adverts}}
<div class="container">
  <section id="slider_new" class="separator">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
          <div id="my_slider_new" class="slider slider_level_hot">
            <h1 class="title"> {{lng_other_adverts}} </h1>
            <div class="owl-carousel owl-theme">
              {{BEGIN adverts}}
                <div class="item before_none">
                  <div class="item_group">
                    <div class="caption_up"><a href="/ads/{{type}}/{{id}}/"> {{name}} </a></div>
                    {{IF image}}
                      <div class="image">
                        <div class="img">
                          <a href="/ads/{{type}}/{{id}}/"><img src="{{url_staticServer}}{{image_mini}}" alt="{{name}}"></a>
                        </div>
                      </div>
                    {{ELSE}}
                      <div class="image">
                        <div class="img">
                          <a href=""><img src="//s1.avtoclassika.com/img/noimage-043a876f.jpg" alt="{{name}}"></a>
                        </div>
                      </div>
                    {{END IF}}
                    <span class="hot_price">
                      {{IF cost}} {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} {{ELSE}} {{lng_contract_price}} {{END cost}}
                    </span>
                  </div>
                </div>
              {{END adverts}}
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden">
            {{includeBlock('directories', 'directories', 'banner', 'advert')}}
        </div>
      </div>
    </div>
  </section>
</div>
{{END IF}}
