<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pad_0">
  <div class="payment_delivery">
    <h4 class="delivery_title"> {{lng_delivery}} </h4>
    <ul class="clearfix">
      {{BEGIN methods}}
        {{includeBlock('directories', 'directories', 'deliveryItem')}}
      {{END methods}}
    </ul>
  </div>
</div>