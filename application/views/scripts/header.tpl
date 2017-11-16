<div class="container">
  <div class="row">
    <div class="container_center clearfix" id="container_center">
      <header class="desk_top">
        <div class="top_menu clearfix">
          <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mobile_size">
            <div class="brand">
              <div class="logo">
                  {{includeBlock('directories', 'directories', 'laborHours')}}
                <div class="image">
                  <div class="img" itemscope itemtype="http://schema.org/Organization">
                    <a itemprop="url" href="http://{{sitename}}/"><img  alt="{{lng_general_organization}}" itemprop="logo" src="/components/local/swipebox/assets/images/logo.jpg" alt="logo"></a>
                  </div>
                </div>
                <div class="image image_light">
                  <div class="img">
                    <a href="http://{{sitename}}/"><img src="/components/local/swipebox/assets/images/logo_light.png" alt="logo"></a>
                  </div>
                  <span class="logo__organization" itemprop="name">{{lng_general_organization}}</span>
                </div>
                <span class="logo_caption">{{lng_slogan}}</span>
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0 mobile_size">
            <nav class="navbar" role="navigation">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">
                <div class="col-xs-2 pad_0 group_icon_bar">
                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                </div>
                <div class="col-xs-10 pad_0">
                  <div class="header_info header_info_visible clearfix">
                    <form action="/search/" class="clearfix">
                        {{includeBlock('cars', 'cars', 'searchForm')}}
                        {{includeBlock('users', 'users', 'enterBar')}}
                        {{includeBlock('orders', 'orders', 'basketBar')}}
                    </form>
                  </div>
                </div>
              </div>
              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse navbar-ex1-collapse ">
                  {{includeBlock('navigations', 'navigations', 'menu', 'main')}}
                  {{includeBlock('navigations', 'navigations', 'languages')}}
                  {{includeBlock('directories', 'directories', 'currencies')}}
              </div><!-- /.navbar-collapse -->
            </nav>
            <div class="header_info clearfix">
                {{includeBlock('directories', 'directories', 'phones')}}
              <div class="brand_mobile">
                <div class="logo">
                  <div class="image_light_mobile">
                    <div class="img">
                      <a href="http://{{sitename}}/"><img src="/components/local/swipebox/assets/images/logo_light.png" alt="logo"></a>
                    </div>
                  </div>
                </div>
              </div>
              <form action="/search/" class="clearfix">
                    {{includeBlock('cars', 'cars', 'searchForm')}}
                    {{includeBlock('users', 'users', 'enterBar')}}
                    <div class='layout__header-info-bar layout__header-info-bar_type_sticky layout__header-info-bar_state_invisible'>
                      {{includeBlock('orders', 'orders', 'basketBar')}}
                    </div>
              </form>
            </div>
          {{includeBlock('users', 'users', 'authPopup')}}
        </div>
      </header>
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0 group_fb">
        <div class="share_fb">
          <div class="onoffswitch clearfix">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
            <label class="onoffswitch-label" for="myonoffswitch"></label>
          </div>
          <ul class="social-buttons cf">
            <li>
              <a href="http://www.facebook.com/sharer.php?u=http://www.socialitejs.com&t=Socialite.js"
                 class="socialite facebook-like" data-href="http://socialitejs.com" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank">
                <span class="vhidden">Share on Facebook</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="fast_reg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body clearfix">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <form id="user-register" class="modal__form" action="/users/register/" novalidate>

                  <div class="form__input-wrap form__input-wrap_size_divide">
                    <input type="text" name="name" placeholder="Имя*" class="form__input form__input_type_contrast" id="user-register__name">
                    <span class="form__input-error error"></span>
                  </div>

                  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
                    <input class="form__input form__input_type_contrast" id="user-register__email" name="email" type="email" placeholder="E-mail*">
                    <span class="form__input-error error"></span>
                  </div>

                  <div class="form__input-wrap form__input-wrap_size_divide">
                    <input class="form__input form__input_type_contrast" name="password" type="password" id="user-register__password" placeholder="Пароль*">
                    <span class="form__input-error error"></span>
                  </div>

                  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
                    <input class="form__input form__input_type_contrast" name="password_retype" type="password" id="user-register__password_retype" placeholder="Пароль еще раз*">
                    <span class="form__input-error error"></span>
                  </div>

                    <textarea class="w_100" name="" cols="30" rows="10" placeholder="Пользовательское соглашение"></textarea>
                    <input type="submit" class="add_button" value="Регистрация">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
  $(document).ready(function(){
      if ($(window).width() < 530) {
          $('button.search-element__button').click(function(e){
              e.preventDefault();
              $('a.enter_button').toggle(300);
              $('a.basket_bar').toggle(300);
              $('.header_info form div').toggleClass('search-element', 'slow').toggleClass('search-element-phone', 'slow');
              $('.header_info form .search-element input[type=search]').toggle();
              $('.search-element-phone input[type="search"]').css('height', '35px');
              $('.search-element-phone .search-element__button').css('height', '35px');
          });
      }
  });
</script>
<script>
  $(document).ready(function(){
      $(window).scroll(function() {
          if ($(this).scrollTop() >= 10) {
              $('.navbar-collapse').css('top', '70px');
              $('.navbar-header').css('top', '0');
          }
          if ($(this).scrollTop() <= 10) {
              $('.navbar-collapse').css('top', '150px');
              $('.navbar-header').css('top', '70px');
          }
      });
  });

</script>

