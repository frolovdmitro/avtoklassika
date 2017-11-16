<div class="recommend-bar__wrap">
  <div class="recommend-bar">
    <strong class="recommend-bar__percent">{{stg_general_rating}}%</strong> 
    <div class="recommend-bar__stars"></div>
    <p class="recommend-bar__text"> {{lng_reccomend_shop}}<br> <strong>AVTOCLASSIKA.COM</strong> </p>
  </div>
  <a class="recommend-bar__add-feedback" data-toggle="modal" data-target="#review">{{lng_add_feedback}}</a>
</div>

<div class="modal fade" id="review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog footer_review">
    <div class="modal-content">
      <div class="modal-body clearfix">
        <form id="add-review" class="modal__form" action="/users/add-review/" novalidate>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <input type="text" name="name" id="add-review__name" placeholder="{{lng_name}}*">
            <span class="errorname erorrs"></span>
            <span class="form__input-error error"></span>
            <select id="my_select_review" name="quality_service">
              <option value="">{{lng_quality_service}}</option>
              <option value="excellent">{{lng_excellent}}</option>
              <option value="well">{{lng_well}}</option>
              <option value="normal">{{lng_normal}}</option>
              <option value="bad">{{lng_bad}}</option>
              <option value="awful">{{lng_awful}}</option>
            </select>
            <select id="my_select_review_1" name="quality_goods">
              <option value="">{{lng_quality_goods}}</option>
              <option value="excellent">{{lng_excellent}}</option>
              <option value="well">{{lng_well}}</option>
              <option value="normal">{{lng_normal}}</option>
              <option value="bad">{{lng_bad}}</option>
              <option value="awful">{{lng_awful}}</option>
            </select>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
            <input type="email" name="email" id="add-review__email" placeholder="E-mail*">
            <span class="erroremail erorrs"></span>
            <span class="form__input-error error"></span>
            <select id="my_select_review_2" name="usability_site">
              <option value="">{{lng_usability_site}}</option>
              <option value="excellent">{{lng_excellent}}</option>
              <option value="well">{{lng_well}}</option>
              <option value="normal">{{lng_normal}}</option>
              <option value="bad">{{lng_bad}}</option>
              <option value="awful">{{lng_awful}}</option>
            </select>
            <select id="my_select_review_3" name="shipping">
              <option value="">{{lng_shipping}}</option>
              <option value="excellent">{{lng_excellent}}</option>
              <option value="well">{{lng_well}}</option>
              <option value="normal">{{lng_normal}}</option>
              <option value="bad">{{lng_bad}}</option>
              <option value="awful">{{lng_awful}}</option>
            </select>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <textarea class="w_100" name="text" id="add-review__text" cols="30" rows="10" placeholder="{{lng_review_text}}*"></textarea>
            <span class="errortext erorrs"></span>
          </div>
          <button type="submit" class="form__submit form__submit_type_main ad__submit add_button">{{lng_send_review}}</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#add-review').submit(function(e) {
    e.preventDefault();
    $('.erorrs').text('');
    var data ={
      name : $('#add-review__name').val(),
      email : $('#add-review__email').val(),
      quality_service : $('#my_select_review option:selected').val(),
      usability_site  : $('#my_select_review_1 option:selected').val(),
      quality_goods : $('#my_select_review_2 option:selected').val(),
      shipping  : $('#my_select_review_3 option:selected').val(),
      text  : $('#add-review__text').val(),
    }

    $.ajax({
      url: '/users/add-review/',
      type: 'POST',
      data: data,
    })
    .done(function() {
      location.reload();
    })
    .fail(function(resp) {
        var response = $.parseJSON( resp.responseText );
        if(response.errors){
          $.each(response, function(index, val) {
            if(typeof val ==    "object"){
              console.info(val);
              $('.error'+val.id).text(val.text);
            }
          });
        }
    })

  });
});
      // $("#add-review").on("submit", function(t) {
      //     var str = $(this).serialize();
      //     ajax({
      //         type: "POST",
      //         url: "/users/add-review/",
      //         data: str,
      //         dataType: "json",
      //         success: function() {
      //             $(#review).modal('hide');
      //         },
      //         error: function(t) {
      //             alert("Error");
      //             var i, s, o, l, u;
      //             n.removeAttr("disabled"), a = t.responseJSON, l = a.errors, delete a.errors;
      //             for (u in a) s = e("#" + r + a[u].id), o = s.parent(), o.hasClass("selector") && (o = o.parent()), i = o.find(".error"), i.text(a[u].text), i.css("opacity", 1), n.removeAttr("disabled");
      //             if (a) return e("#" + r + a[0].id).focus()
      //         }
      //     }, t.preventDefault())
      // })
</script>