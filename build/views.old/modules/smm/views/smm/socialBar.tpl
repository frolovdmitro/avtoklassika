{{IF buttons}} <div class="in-social-bar"> <h4 class="in-social-bar__header">{{lng_we_in_sicials}}</h4> <ul class="in-social-bar__list">
  {{BEGIN buttons}} <li class="in-social-bar__item"><a {{IF type == 'youtube'}}style="background: url('/img/youtube.png');width: 44px;height: 40px;"{{END IF}} class="in-social-bar__link in-social-bar__link_type_{{type}}" target="_blank" href="{{url}}"></a></li> {{END buttons}} </ul> </div> {{END IF}}
