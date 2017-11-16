{{includeScript('header.tpl')}}
<div class="container">
  <section id="basket" class="">
    <div class="row">
      <div class="container_center clearfix ">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="basket clearfix">
            <div class="content">
              <!--<ul class="basket-steps" id="tabs">
                <li class="title">
                    {{lng_create_order}}
                </li>
                <li class="ohter basket-steps__item">
                  <a class="" href="#step=1">
                    <span class="step">1</span>
                      {{lng_step_1}}
                  </a>
                </li>
                <li class="ohter basket-steps__item basket-steps__link_step_2">
                  <a class="" href="#step=2">
                    <span class="step">2</span>
                      {{lng_step_2}}
                  </a>
                </li>
                <li class="ohter basket-steps__item basket-steps__link_step_3">
                   <span class=" basket-steps__link_step_3">
                      <span class="step">3</span>{{lng_step_3}}
                   </span>
                </li>
              </ul>-->

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
                <li class="ohter basket-steps__item">
                  <span class="verification basket-steps__link_step_4">
                    <span class="step">4</span>{{lng_step_3}}
                  </span>
                </li>
              </ul>
              <div id="content">
                <div id="basket-page-content"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


<script>
//    $(document).ready(function() {
//        $('#tabs a').click(function() {
//            $("#tabs li").removeClass("basket-steps__item_state_active"); //Сброс ID
//            $(this).parent().addClass("basket-steps__item_state_active"); // Активируем закладку
//        });
//    });
</script>
<!--<div class="layout__content">
  <ul class="basket-steps">
    <li class="basket-steps__item basket-steps__item_type_caption ribbon ribbon_type_right-outer">
        {{lng_create_order}}
    </li>
    <li class="basket-steps__item basket-steps__item_state_active">
      <a class="basket-steps__link basket-steps__link_step_1" href="#step=1">
        <span class="basket-steps__num ">1</span>
        {{lng_step_1}}
      </a>
    </li>
    <li class="basket-steps__item">
      <a class="basket-steps__link basket-steps__link_step_2" href="#step=2">
        <span class="basket-steps__num ">2</span>
        {{lng_step_2}}
      </a>
    </li>
    <li class="basket-steps__item basket-steps__item_pos_last">
      <span class="basket-steps__link basket-steps__link_step_3">
        <span class="basket-steps__num ">3</span>{{lng_step_3}}
      </span>
    </li>
  </ul>
  <div id="basket-page-content"></div>
</div>-->
{{includeScript('footer.tpl')}}
