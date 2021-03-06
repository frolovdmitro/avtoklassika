<div id="my_modal" class="add-ads-modal">
  <div style="display: block;" class="modal" id="to_advertise" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <button id="close_button" type="button" class="close" data-dismiss="my_modal" aria-hidden="true">&times;</button>
        <div class="modal-body clearfix">

          <form id="add-ads" class="modal__form" action="/json/ads/addadvert.html">
            <div class="form__input-wrap form__input-wrap_size_divide">
              <input type="text" name="name" id="add-ads__name" placeholder="{{lng_name}}*">
              <span class="form__input-error error"></span>
            </div>
            <div class="form__input-wrap form__input-wrap_size_divide">
              <input type="text" name="city" id="add-ads__city" placeholder="{{lng_city_country}}*">
              <span class="form__input-error error"></span>
            </div>
            <div class="form__input-wrap form__input-wrap_size_divide">
              <input type="email" name="email" id="add-ads__email" placeholder="E-mail*">
              <span class="form__input-error error"></span>
            </div>
            <div class="form__input-wrap form__input-wrap_size_divide">
              <input type="text" name="phone" id="add-ads__phone" placeholder="{{lng_phone}}*">
              <span class="form__input-error error"></span>
            </div>

            <div class="form__input-wrap form__input-wrap_size_divide">
              <select class="form__select" name="type" id="add-ads__type">
                  {{IF need_pay == 0}}
                <option value="">{{lng_select_type}}</option>
                <option value="buy">{{lng_i_buy}}</option>
                  {{END}}
                <option value="sell">{{lng_i_sell}}</option>
              </select>
              <span class="form__input-error error"></span>
            </div>

            <div class="form__input-wrap form__input-wrap_size_divide">
              <select class="form__select" name="category" id="add-ads__category">
                <option value="">{{lng_select_category}}</option>
                <option value="car">{{lng_car}}</option>
                <option value="autopart">{{lng_autopart}}</option>
              </select>
              <span class="form__input-error error"></span>
            </div>

            <div class="form__input-wrap form__input-wrap_size_divide">
              <input type="text" name="price" id="add-ads__price" placeholder="{{lng_input_price}}">
              <span class="form__input-error error"></span>
            </div>

            <div class="form__input-wrap form__input-wrap_size_divide">
              <select class="form__select" name="currency" id="add-ads__currency">
                <option value>Выберите валюту</option>
                  {{BEGIN currency}}
                <option value="{{id}}">{{name}}</option>
                  {{END currency}}
              </select>
              <span class="form__input-error error"></span>
            </div>

            <div class="form__input-wrap form__input-wrap_size_divide">
              <input class="w_100" type="text" name="caption" id="add-ads__caption" placeholder="{{lng_title}}*">
              <span class="form__input-error error"></span>
            </div>

            <div class="form__input-wrap form__input-wrap_size_divide">
              <textarea class="w_100" name="text" id="add-ads__text" cols="30" rows="10" placeholder="{{lng_advert_text}}*"></textarea>
              <span class="form__input-error error"></span>
            </div>

            <div class="form__input-wrap form__input-wrap_size_divide">
              <input class="w_100" type="text" name="video" id="add-ads__videourl" placeholder="ссылка на видео">
              <span class="form__input-error error"></span>
            </div>

            <div id="upload-progress" class="form__progress modal__progress"></div>
            <div class="form__fileinput-input-error-wrap form__fileinput-input-error-wrap_type_ads">
              <div class="form__fileinput-wrap">
                <span class="form__fileinput-caption">{{lng_upload_photo}}</span>
                <input id="fileupload" type="file" name="files[]" accept="image/jpeg" class="form__fileinput" data-url="/json/ads/add-images/" multiple data-error-filesize="{{lng_validate_add_files_maxfilesize}}" data-error-maxfiles="{{lng_validate_add_files_maxfiles}}">
              </div>
              <div class="form__fileinput-error">
              </div>
            </div>
            <div class="form__fileinput-files form__fileinput-files_type_ads"></div>
            <button class="add_button form__submit form__submit_type_main ad__submit" type="submit"> {{lng_add_advert}} </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){
      $('#close_button').click(function(e){
          e.preventDefault();
          $('.modal').remove();
          $('.jquery-modal').remove();
      });
  });
</script>
