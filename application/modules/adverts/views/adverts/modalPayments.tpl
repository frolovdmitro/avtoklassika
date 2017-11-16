<form id="proceed_payment" class="modal__payment" action="/ads/">
    <div class="payments-deliveries__wrap">
      <div id="payments-deliveries-wrap">
        <div class="payments-deliveries" style="margin:0 auto; width: auto!important;text-align: center;">
          <h4 class="payments-deliveries__header">К оплате</h4>
            <p style="font-size: 18px;margin: 0 0 20px;">Ваше объявление сохранено. Для публикации — оплатите: <strong>10 UAH</strong></p>
        </div>

      </div>

    </div>

    <button class="form__submit form__submit_type_main basket-steps__create-btn" type="submit" id="addToCart">
      Перейти в корзину
    </button>

</form>
<script type="text/javascript">
  $('#addToCart').on('click', function(e) {
    e.preventDefault();
    var data = {
      method: 'add',
      id: 170136,
      count: 1,
      color: 0,
      size: 0
    };
    $.ajax({
      type: 'POST',
      url: '/json/basket/change/',
      data: data,
      success: function(data) {
        console.info('Succesfull service add to cart');
        window.location = window.location.origin+'/basket'
      },
      error: function(data) {
        alert('Ошибка регистрации платной услуги: '+data);
      }
    });
  })
</script>
