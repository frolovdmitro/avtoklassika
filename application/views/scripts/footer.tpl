<footer>
  <div class="container">
    <div class="row">
      <div class="container_center clearfix">
          {{includeBlock('navigations', 'navigations', 'menu', 'info')}}

          {{includeBlock('navigations', 'navigations', 'menu', 'services')}}

        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 pad_0 widht_100">
          <h4 class="title">{{lng_contacts}}</h4>
          <ul class="info_contacts clearfix">
            <li class="contact"><a href="/contacts/">{{lng_general_address}}</a></li>
              {{includeBlock('directories', 'directories', 'phonesSimple')}}
            <li class="mail"><a href="mailto:{{lng_general_email}}">{{lng_general_email}}</a></li>
          </ul>
            {{includeBlock('smm', 'smm', 'socialBar')}}
        </div>


          {{includeBlock('users', 'users', 'subscriptionBar')}}

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0 clearfix">
          <div class="method_payments_bar"></div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0 clearfix">
          <ul class="copyright clearfix">
            <li>{{lng_copyright}}</li>
            <li>
              <a class="design" href="http://felix-art.com/" target="_blank">{{lng_design}}</a>
              <!--<a class="copyright_uwinart" target="_blank" href="http://softwest.net"> Разработка SoftWest Group </a>-->
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</footer>