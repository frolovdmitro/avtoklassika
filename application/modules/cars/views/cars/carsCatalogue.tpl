{{IF cars}}
<div class="container" id="my_catalog">
  <section id="car_park">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="catalog">
            <h1 class="title_catalog">Каталог запчастей</h1>
            <div class="group_filter">
              <div class="nav_filter">
                <ul class="creations_filter">
                  <li class="catalog-autoparts__nav-item_state_current">
                    <a class="catalog-autoparts__nav-link filter all" data-filter="all" href="#all=true">{{lng_all}}</a>
                  </li>
                  {{BEGIN types}}
                    <li>
                      <a class="catalog-autoparts__nav-link filter {{synonym}}" style="padding: 0 5px;" data-filter="{{synonym}}" href="#{{synonym}}=true">{{name}}</a>
                    </li>
                  {{END types}}
                </ul>
              </div>
              <div class="filter_body center">
                <div id="catalog-autoparts__list-wrap">
                  <ul class="catalog-autoparts__list">
                      {{BEGIN cars}}
                    <li class="catalog-autoparts__item mix mix_all {{type}}"><a class="catalog-autoparts__link" href="/car/{{synonym}}/"><img class="catalog-autoparts__image" src="{{url_staticServer}}{{image}}" alt="{{name}}" width="109" height="78"><br> <strong class="catalog-autoparts__caption">{{name}}{{UNLESS nobr}}<br> &nbsp;{{END ENLESS}}</strong></a></li>
                      {{END cars}}
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


<!--<div id="my_catalog">
  <div class="catalog-autoparts">
    <nav class="catalog-autoparts__nav">
      <ul class="catalog-autoparts__nav-list">
        <li class="catalog-autoparts__nav-item catalog-autoparts__nav-item_state_current">
          <div class="catalog-autoparts__nav-link-wrap">
            <a class="catalog-autoparts__nav-link filter all" data-filter="all" href="#all=true">{{lng_all}}</a>
          </div>
        </li>
        {{BEGIN types}}
          <li class="catalog-autoparts__nav-item">
            <div class="catalog-autoparts__nav-link-wrap">
              <a class="catalog-autoparts__nav-link filter {{synonym}}" data-filter="{{synonym}}" href="#{{synonym}}=true">{{name}}</a>
            </div>
          </li>
        {{END types}}
      </ul>
    </nav>
    <div id="catalog-autoparts__list-wrap">
      <ul class="catalog-autoparts__list">
        {{BEGIN cars}}
        <li class="catalog-autoparts__item mix mix_all {{type}}"><a class="catalog-autoparts__link" href="/car/{{synonym}}/"><img class="catalog-autoparts__image" src="{{url_staticServer}}{{image}}" alt="{{name}}" width="109" height="78"><br> <strong class="catalog-autoparts__caption">{{name}}{{UNLESS nobr}}<br> &nbsp;{{END ENLESS}}</strong></a></li>
        {{END cars}}
      </ul>
    </div>
  </div>
</div>-->
{{END IF}}
