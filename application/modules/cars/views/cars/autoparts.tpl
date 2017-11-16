<!--{{IF autoparts}}
<ul class="car-categories__list catalog-autoparts__list">
  {{BEGIN autoparts}}
  <li class="car-categories__item catalog-autoparts__item">
    <a class="catalog-autoparts__link" href="/car/{{car_synonym}}/{{id}}/">
      <img class="catalog-autoparts__image" src="{{url_staticServer}}{{image}}" alt="{{name}}" width="109" height="78"><br>
      <strong class="car-categories__caption catalog-autoparts__caption"> {{name}}{{UNLESS br}}<br> &nbsp;{{END UNLESS}} </strong>
    </a>
  </li>
  {{END autoparts}} 
</ul>
{{END IF}}-->


{{IF autoparts}}
  <ul class="creations_body">
    {{BEGIN autoparts}}
      <li class="nav_item col-lg-3 col-md-3 col-sm-4 col-xs-4 pad_0 widht_50">
        <div class="image">
          <div class="img">
            <a href="/car/{{car_synonym}}/{{id}}/">
              <img class="img img-responsive center-block" src="{{url_staticServer}}{{image}}" alt="{{name}}">
            </a>
          </div>
          <div class="caption">{{name}}{{UNLESS br}}<br> &nbsp;{{END UNLESS}}</div>
        </div>
      </li>
    {{END autoparts}}
  </ul>


{{END IF}}