<form id="add-request" class="modal__form" action="/car/add-request/" novalidate>
  <div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="text" name="name" id="add-review__name" placeholder="{{lng_name}}*"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="email" name="email" id="add-review__email" placeholder="E-mail*"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide">
    <select class="form__select" name="car" id="add-review__car">
      <option value="">{{lng_select_car}}*</option>
      {{BEGIN cars}} 
      <option value="{{id}}">{{name}}</option>
      {{END cars}} 
    </select>
    <span class="form__input-error error"></span> 
  </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="text" name="model" id="add-review__model" placeholder="{{lng_model}}*"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="text" name="year" id="add-review__year" placeholder="{{lng_year}}"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="text" name="volume" id="add-review__volume" placeholder="{{lng_volume}}"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="text" name="body_type" id="add-review__body_type" placeholder="{{lng_body_type}}"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="text" name="fuel_type" id="add-review__fuel_type" placeholder="{{lng_fuel_type}}"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="text" name="detail_name" id="add-review__detail_name" placeholder="{{lng_detail_name}}*"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last"> <input class="form__input form__input_type_contrast" type="text" name="cost" id="add-request__cost" placeholder="{{lng_approximet_cost}}"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide"> <input class="form__input form__input_type_contrast" type="text" name="detail_num" id="add-review__detail_num" placeholder="{{lng_num_by_catalogue}}"> <span class="form__input-error error"></span> </div>
  <div class="form__input-wrap form__input-wrap_size_divide form__input-wrap_pos_last">
    <select class="form__select" name="state" id="add-request__state">
      <option value="">{{lng_state}}</option>
      <option value="secondhand">{{lng_secondhand}}</option>
      <option value="replica">{{lng_replica}}</option>
      <option value="new">{{lng_new}}</option>
      <option value="restaurare">{{lng_restaurare}}</option>
    </select>
    <span class="form__input-error error"></span> 
  </div>
  <div class="form__input-wrap form__input-wrap_size_100"> <textarea class="form__input form__input_type_contrast" name="text" id="add-request__text" rows="4" placeholder="{{lng_description}}"></textarea> <span class="form__input-error error"></span> </div>
  <input type="hidden" name="uploaded-file" id="uploaded-file"> 
  <div id="upload-progress" class="form__progress modal__progress"></div>
  <div class="form__fileinput-input-error-wrap form__fileinput-input-error-wrap_type_ads">
    <div class="form__fileinput-wrap"> <span class="form__fileinput-caption">{{lng_upload_photo_detail}}</span> <input id="fileupload-single" type="file" name="file" accept="image/jpeg" class="form__fileinput" data-url="/request/add-image/" data-error-filesize="{{lng_validate_add_files_maxfilesize}} Max file size 1Mb"> </div>
    <div class="form__fileinput-error"></div>
  </div>
  <div class="form__fileinput-files form__fileinput-files_type_ads"></div>
  <button class="form__submit form__submit_type_main ad__submit" type="submit"> {{lng_send_request}} </button> 
</form>
