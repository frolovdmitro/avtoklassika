{{BEGIN editForm}}
<div class="" id="adv_preview">
    <h2 class="autoparts__h1 ribbon ribbon_type_right-inner"> Обьявление #{{id}}</h2>
    <a class="adv__edit-link" href="#edit" id="goToEditView">Редактировать</a>
    <ul class="breadcrumbs">
        <li class="breadcrumbs__item breadcrumbs__item_type_home" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link" href="/ads/" itemprop="url"><span itemprop="title">ОБЪЯВЛЕНИЯ</span></a></li>
      <li class="breadcrumbs__item" itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a class="breadcrumbs__link breadcrumbs__link_type_upper" href="/ads/#type-{{type}}=true" itemprop="url"><!--
      --><span itemprop="title">
        {{IF type == 'sell'}}
            {{type}}
            {{END IF}}
            {{IF type == 'buy'}}
            {{type}}
            {{END IF}}
        </span><!--
      --></a></li>
      <li class="breadcrumbs__item">
        {{IF category == 'car'}}
        {{category}}
        {{END IF}}
        {{IF category == 'autopart'}}
        {{category}}
        {{END IF}}
      </li>
    </ul>
    <div class="ad">
      <div class="ad__image-wrap">
        <a class="ad__image" href="{{url_staticServer}}{{image}}" rel="photos" title="{{name}}"><img src="{{url_staticServer}}{{image_medium}}" alt="{{name}}" width="300" height="230"></a>
        {{IF images}}
        <ul class="ad__thumbnails">
          {{BEGIN images}}
          <li class="ad__thumbnails-item"><a class="ad__thumbnail-link" rel="photos" title="{{name}}" href="{{url_staticServer}}{{image}}">
              <img class="ad__thumbnail-image" src="{{url_staticServer}}{{image_small}}" alt="{{name}}"></a></li>
          {{END images}}
        </ul>
        {{END IF}}
    </div>
        <div class="ad__info-wrap">
            <h1 class="ad__caption">{{title}}</h1>
            <div class="ad__date">{{date}}</div>
            <div class="ad__cost {{IF cost}} _print-cost{{END IF}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}">
              {{IF cost}}{{cost}}{{ELSE}}Договорная{{END IF}}
               <small>грн.</small>
              <!-- -->
              <!-- -->
            </div>
            <div class="ad__text"> {{text}}  </div>
            <ul class="ads-list__user-info">
                <li class="ads-list__user-item ads-list__user-item_type_location">{{user_city}}</li>
                <li class="ads-list__user-item ads-list__user-item_type_phone">{{user_phone}}</li>
                <li class="ads-list__user-item ads-list__user-item_type_user"> {{user_name}}  </li>
                <li class="ads-list__user-item ads-list__user-item_type_user"> {{url}}  </li>
                <li class="ads-list__user-item ads-list__user-item_type_email"><a href="mailto:{{user_email}}">{{user_email}}</a></li>
            </ul>
        </div>
    </div>
</div>




<div class="edit-form" id="adv_edit">
  <form class="modal__form" action="/cabinet/editadvert/?id=202123" method="post">
    <input hidden name="id" value="{{id}}">

    <div class="form__input-wrap form__input-wrap_size_half">
      <input class="form__input form__input_type_contrast" type="text" name="name" id="add-ads__name" placeholder="Ваше имя*" value="{{user_name}}">
      <span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_half form__input-wrap_pos_last">
      <input class="form__input form__input_type_contrast" type="text" name="city" id="add-ads__city" placeholder="Страна, город*" value="{{city}}"><span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_half form__input-wrap_pos_last">
      <input class="form__input form__input_type_contrast" type="text" name="phone" id="add-ads__phone" placeholder="{{user_phone}}*" value="{{user_phone}}"><span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_half">
      <select class="form__select" name="type" id="add-ads__type">
        <option value="{{type}}">{{type}}</option>
        <option value="buy">Купить</option>
        <option value="sell">Продать</option>
      </select>
      <span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_half form__input-wrap_pos_last">
      <select class="form__select" name="category" id="add-ads__category">
        <option value="{{category}}">{{category}}</option>
        <option value="car">Автомобиль</option>
        <option value="autopart">Автозапчасть</option>
      </select>
      <span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_half">
      <input class="form__input form__input_type_contrast" type="text" name="price" id="add-ads__price" placeholder="Укажите стоимость" value="{{IF cost}}{{cost_unformat}}{{ELSE}}0{{END IF}}"><span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_half form__input-wrap_pos_last">
      <select class="form__select" name="currency" id="add-ads__currency">
        <option value="1">{{rate_name}}</option>
        {{BEGIN currency}}
        <option value="{{id}}">{{name}}</option>
        {{END currency}}
      </select>
      <span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_half">
      <input class="form__input form__input_type_contrast" type="email" name="user_email" placeholder="" readonly value="{{user_email}}"><span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_100">
     <input class="form__input form__input_type_contrast" type="text" name="caption" id="add-ads__caption" placeholder="Заголовок" value="{{IF title}} {{title}}{{ELSE}}Заголовок{{END IF}}">
        <span class="form__input-error error"></span>
    </div>

    <div class="form__input-wrap form__input-wrap_size_100">
      <textarea class="form__input form__input_type_contrast" name="text" id="add-ads__text" rows="5" placeholder="Текст вашего объявления"  value="{{text}}">{{text}}</textarea><span class="form__input-error error"></span>
    </div>

    <!-- Add video -->
    <div class="form__input-wrap form__input-wrap_size_100">
      <input class="form__input form__input_type_contrast" type="text" name="url" id="add-ads__videourl" placeholder="{{IF url}}{{url}}{{ELSE}}ссылка на видео{{END IF}}"  value="{{url}}"><span class="form__input-error error"></span>
    </div>
    <!-- END Add video -->

    <!--
    <div id="upload-progress" class="form__progress modal__progress">
    </div>
    <div class="form__fileinput-input-error-wrap form__fileinput-input-error-wrap_type_ads">
      <div class="form__fileinput-wrap">
        <span class="form__fileinput-caption">{{lng_upload_photo}}</span><input id="fileupload" type="file" name="files[]" accept="image/jpeg" class="form__fileinput" data-url="/ads/add-images/" multiple data-error-filesize="{{lng_validate_add_files_maxfilesize}}" data-error-maxfiles="{{lng_validate_add_files_maxfiles}}">
      </div>
      <div class="form__fileinput-error">
      </div>
    </div>  -->
    <!--
    --><div class="form__fileinput-files form__fileinput-files_type_ads">
    </div>

    <button class="form__submit form__submit_type_main ad_cancel"> Отмена </button>

    <button class="form__submit form__submit_type_main ad__submit-edit" type="submit"> Сохранить </button>
  </form>  
</div>


<style>
  .edit-form{
    display: none;
    margin: 0 auto 20px;
    background: #ebf6eb;
    width: 700px;
    padding: 25px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
  }
  .edit-form form{
    text-align: justify;
  }
  .edit-form .form__select{
    width: 100%;
    height: 37px;
    text-indent: 13px;
    color: #6e7c6e;
    border: 1px solid #9faf9f;
    border-top: 2px solid #9faf9f;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
  }
  .edit-form input[readonly]{
    color: rgba(110, 124, 110, .55);
  }
  .ad__date{
    top: auto;
  }
  .form__input-wrap_size_half{
    width: 48%;
  }
  .adv__edit-link{
    float: right;
    margin-top: 10px;
    color: #6e7c6e;
    font-style: italic;
    position: relative;
    padding-left: 28px;
    line-height: 24px;
  }
  .adv__edit-link:before{
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    background-image: url(//s1.avtoclassika.com/img/_icons-7508c84f.png);
    background-position: -10px -1683px;
    width: 24px;
    height: 24px;
  }
  .ad_cancel{
    clear: both;
    margin-top: 10px;
    float: left;
  }
  .ad__submit-edit{
    float: right;
    margin-top: 10px;
  }
</style>
<script type="text/javascript">
  onload = function(e) {
    $('#goToEditView').on('click', function (e) {
      e.preventDefault();
      $('#adv_preview').hide();
      $('#adv_edit').show();
    })
    $('.ad_cancel').on('click', function (e) {
      e.preventDefault();
      $('#adv_edit').hide();
      $('#adv_preview').show();
    })
  }
</script>


{{END editForm}}
