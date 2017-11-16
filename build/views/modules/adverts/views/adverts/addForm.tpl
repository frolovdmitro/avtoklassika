<!-- <form id="add-ads" class="modal__form" action="/ads/add/"> -->
<form id="add-ads" class="modal__form" action="/json/ads/addadvert.html">
  <div class="form__input-wrap form__input-wrap_size_divide"><input class="form__input form__input_type_contrast" type="text" name="name" id="add-ads__name" placeholder="{{lng_name}}*"><span class="form__input-error error"></span></div><!--
  --><div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"><input class="form__input form__input_type_contrast" type="text" name="city" id="add-ads__city" placeholder="{{lng_city_country}}*"><span class="form__input-error error"></span></div><!--
  --><div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="email" name="email" id="add-ads__email" placeholder="E-mail*"><span class="form__input-error error"></span> </div><!--
  --><div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="text" name="phone" id="add-ads__phone" placeholder="{{lng_phone}}*"><span class="form__input-error error"></span> </div><!--
  --><div class="form__input-wrap form__input-wrap_size_divide">
    <select class="form__select" name="type" id="add-ads__type">
      {{IF need_pay == 0}}
        <option value="">{{lng_select_type}}</option>
        <option value="buy">{{lng_i_buy}}</option>
      {{END}}
        <option value="sell">{{lng_i_sell}}</option>
    </select>
    <span class="form__input-error error"></span>
  </div><!--
  --><div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
    <select class="form__select" name="category" id="add-ads__category">
      <option value="">{{lng_select_category}}</option>
      <option value="car">{{lng_car}}</option>
      <option value="autopart">{{lng_autopart}}</option>
    </select>
    <span class="form__input-error error"></span>
  </div><!--
  --><div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="text" name="price" id="add-ads__price" placeholder="{{lng_input_price}}"><span class="form__input-error error"></span> </div><!--
  --><div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
    <select class="form__select" name="currency" id="add-ads__currency">
      <option value="">{{lng_select_currency}}</option>
      {{BEGIN currency}}
      <option value="{{id}}">{{name}}</option>
      {{END currency}}
    </select>
    <span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_100">
    <input class="form__input form__input_type_contrast" type="text" name="caption" id="add-ads__caption" placeholder="{{lng_title}}*"><span class="form__input-error error"></span>
  </div>
  <div class="form__input-wrap form__input-wrap_size_100">
    <textarea class="form__input form__input_type_contrast" name="text" id="add-ads__text" rows="5" placeholder="{{lng_advert_text}}*"></textarea><span class="form__input-error error"></span>
  </div>

  <!-- Add video -->
  <div class="form__input-wrap form__input-wrap_size_100">
    <input class="form__input form__input_type_contrast" type="text" name="video" id="add-ads__videourl" placeholder="ссылка на видео"><span class="form__input-error error"></span>
  </div>
  <!-- END Add video -->

  <div id="upload-progress" class="form__progress modal__progress">
  </div>
  <div class="form__fileinput-input-error-wrap form__fileinput-input-error-wrap_type_ads">
    <div class="form__fileinput-wrap">
      <span class="form__fileinput-caption">{{lng_upload_photo}}</span>


      <!--   <input id="fileupload" type="file" name="files[]" accept="image/jpeg" class="form__fileinput" data-url="/ads/add-images/" multiple data-error-filesize="{{lng_validate_add_files_maxfilesize}}" data-error-maxfiles="{{lng_validate_add_files_maxfiles}}">
      -->
      <input id="fileupload" type="file" name="files[]" accept="image/jpeg" class="form__fileinput" data-url="/json/ads/add-images/" multiple data-error-filesize="{{lng_validate_add_files_maxfilesize}}" data-error-maxfiles="{{lng_validate_add_files_maxfiles}}">
    </div>
    <div class="form__fileinput-error">
    </div>
  </div>
  <!--
  --><div class="form__fileinput-files form__fileinput-files_type_ads"> </div>
  <button class="form__submit form__submit_type_main ad__submit" type="submit"> {{lng_add_advert}} </button>
</form>


<style>
  .form__fileinput-files .remove{
    display: inline-block;
    vertical-align: middle;
    width: 13px;
    height: 13px;
    position: absolute;
    right: 0;
    margin-left: 5px;
  }
  .form__fileinput-files .remove:before,
  .form__fileinput-files .remove:after{
    content: '';
    display: block;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 2px;
    height: 11px;
    background-color: #9faf9f;
  }
  .form__fileinput-files .remove:hover:before,
  .form__fileinput-files .remove:hover:after{
    background-color: #0c555d;
  }
  .form__fileinput-files .remove:before{
    transform: translate(-50%, -50%) rotate(-45deg);
  }
  .form__fileinput-files .remove:after{
    transform: translate(-50%, -50%) rotate(45deg);
  }
  #add-ads .ad__thumbnails {
    margin: 0;
  }
  #add-ads .ad__thumbnails-item {
    width: 50px;
    height: auto;
    margin-bottom: 0;
    border-radius: 0;
    position: relative;
  }
  .ad__thumbnail-image {
    vertical-align: bottom;
  }
</style>
