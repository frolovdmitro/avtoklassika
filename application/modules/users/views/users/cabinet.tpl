{{includeScript('header.tpl')}}

<div class="container">
  <section id="cabinet" class="">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="group">
            <h1 class="title_3 top_0">{{lng_my_cabinet}}</h1>
            <h2 class="title_discount top_0">{{lng_my_discount}}: {{discount}}%</h2>
          </div>
          <a class="logout cabinet__logout-link" href="/users/logout/"> {{lng_exit_from_cabinet}} </a>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="cabinet clearfix">
            <div class="content">
              <div class="coments tabs">
                <ul id="tabs" class="tabs__nav">
                  <li><a class="tabs__nav-link" title="tab1" href="#tab-info"> {{lng_cabinet_info}} </a></li>
                  <li><a class="tabs__nav-link" title="tab2" href="#tab-info"> {{lng_cabinet_addresses}} </a></li>
                  <li><a class="tabs__nav-link" title="tab3" href="#tab-info"> {{lng_cabinet_history}} </a></li>
                  <li><a class="tabs__nav-link" title="tab4" href="#tab-info"> Объявления </a></li>
                </ul>
                <div id="content">
                  <div id="tab1" class="ololo clearfix">
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 pad_0">
                      <form class="cabinet__form" method="post" action="/cabinet/save/info/" novalidate>
                        <div class="cabinet_name">
                          <input class="no_value readonly" type="text" name="name" id="cabinet__name" disabled placeholder="{{lng_not_specified}}*" value="{{name}}" readonly>
                          <span class="input_error form__input-error error"></span>
                        </div>
                        <div class="cabinet_tel">
                          <input class="no_value readonly" type="tel" name="tel" id="cabinet__tel" disabled placeholder="{{lng_not_specified}}*" value="{{phone}}" readonly>
                          <span class="input_error form__input-error error"></span>
                        </div>
                        <div class="cabinet_password">
                          <input class="no_value readonly" type="password" name="password" id="cabinet__password" disabled data-show-txt="{{lng_show}}" data-hide-txt="{{lng_hide}}" placeholder="{{lng_change_password}}" value="" readonly>
                          <span class="input_error form__input-error error"></span>
                        </div>
                        <div class="cabinet_mail">
                          <input class="no_value readonly" type="email" name="email" id="cabinet__email" disabled {{unless not_email}}readonly{{end unless}} placeholder="Email*" value="{{UNLESS not_email}}{{email}}{{END UNLESS}}" readonly>
                          <span class="input_error form__input-error error"></span>
                        </div>
                        <button class="state_hidden form__submit form__submit_type_main cabinet__submit" type="submit"> {{lng_save_changes}} </button>
                      </form>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pad_0">
                      <a class="cabinet_edit" data-id="tab1" href="">{{lng_edit}}</a>
                    </div>
                  </div>
                  <div id="tab2" style="display: none;" class="ololo clearfix">
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 pad_0">
                      <form class="cabinet__form" method="post" action="/cabinet/save/address/" novalidate>
                        <div id="select_country" class="country form__input-wrap cabinet__form-input-wrap cabinet__form-input-wrap_type_disabled">
                            <label for="country">{{lng_country}}:</label>
                            <select class="form__select cabinet__form-select form__selector_size_big cabinet__form-input_style_large" name="country" id="">
                                <option value="">{{lng_not_specified}}</option>
                                {{BEGIN countries}} <option {{if selected}}selected{{end if}} value="{{id}}"> {{name}} </option> {{END countries}}
                            </select>
                            <span class="form__input-error error"></span>
                        </div>
                        <div class="siti">
                          <label for="siti">{{lng_city}}:</label>
                            <input class="no_value readonly form__input form__input_type_contrast cabinet__form-input cabinet__form-input_style_large" type="text" name="city" id="cabinet__city" disabled placeholder="{{lng_not_specified}}" value="{{city}}">
                            <span class="form__input-error error"></span>
                        </div>
                        <div class="index">
                          <label for="index">{{lng_index}}:</label>
                            <input class="no_value readonly form__input form__input_type_contrast cabinet__form-input" type="text" name="index" id="cabinet__index" disabled placeholder="{{lng_not_specified}}" value="{{index}}">
                            <span class="form__input-error error"></span>
                        </div>
                        <div class="street">
                          <label for="street">{{lng_street}}:</label>
                            <input class="no_value readonly form__input form__input_type_contrast cabinet__form-input" type="text" name="street" id="cabinet__street" disabled placeholder="{{lng_not_specified}}" value="{{street}}">
                            <span class="form__input-error error"></span>
                        </div>
                        <div class="home">
                          <label for="home">{{lng_build}}:</label>
                            <input class="no_value readonly form__input form__input_type_contrast cabinet__form-input" type="text" name="build" id="cabinet__build" disabled placeholder="{{lng_not_specified}}" value="{{build}}">
                            <span class="form__input-error error"></span>
                        </div>
                        <div class="apartment">
                          <label for="apartment">Квартира:</label>
                            <input class="no_value readonly form__input form__input_type_contrast cabinet__form-input" type="text" name="flat" id="cabinet__flat" disabled placeholder="{{lng_not_specified}}" value="{{flat}}">
                            <span class="form__input-error error"></span>
                        </div>
                          <button class="state_hidden form__submit form__submit_type_main cabinet__submit" type="submit"> {{lng_save_changes}} </button>
                      </form>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 pad_0">
                      <a class="cabinet_edit" data-id="tab2" href="">Редактировать</a>
                    </div>
                  </div>
                  <div id="tab3" style="display: none;" class="ololo clearfix">
                      {{includeBlock('users', 'users', 'orders')}}
                  </div>
                  <div id="tab4" style="display: none;" class="ololo clearfix">
                      {{includeBlock('users', 'users', 'adverts')}}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
    $(document).ready(function() {
        $("#tabs li:first").attr("id","current"); // Активируем первую закладку

        $('#tabs a').click(function(e) {
            e.preventDefault();
            $("#content .ololo").hide(); //Скрыть все сожержание
            $("#tabs li").attr("id",""); //Сброс ID
            $(this).parent().attr("id","current"); // Активируем закладку
            $('#' + $(this).attr('title')).show(); // Выводим содержание текущей закладки
        });

    });
</script>

<script>
    $(document).ready(function(){
        $('.cabinet_edit').click(function(e){
            e.preventDefault();
            $('select.form__select').removeAttr('disabled', 'disabled');
            $('button.state_hidden').removeClass('cabinet__submit_state_hidden');
            $('#select_country').toggleClass('cabinet__form-input-wrap_type_disabled');
            var tab = $(this).parent().parent().attr('id');
            var el = $('#' + tab + ' .readonly');
            var button = $('#' + tab + ' button');
            if (el.hasClass('no_value')) {
                el.removeClass('no_value').addClass('value').removeAttr('readonly').removeAttr('disabled').removeClass('disabled');
                button.css('visibility', 'visible');
            } else {
                el.removeClass('value').addClass('no_value').attr('readonly', 'true');
                button.css('visibility', 'hidden');
            }
        });
    });
</script>

{{includeScript('footer.tpl')}}
