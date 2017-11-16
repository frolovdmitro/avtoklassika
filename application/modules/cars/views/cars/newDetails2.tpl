{{includeScript('header.tpl')}}
<div class="layout__content autoparts">
  <h1 class="autoparts__h1 ribbon ribbon_type_right-inner">{{lng_new_details}}</h1>
  <ul class="breadcrumbs">
    <li class="breadcrumbs__item breadcrumbs__item_type_home"
     itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
      <a class="breadcrumbs__link" href="/" itemprop="url">
        <span itemprop="title">{{lng_autoparts}}</span>
      </a>
    </li>
    <li class="breadcrumbs__item">{{lng_new_details}}</li>
  </ul>

  <div class="layout__content-with-sidebar" style="width:calc(100% + 10px)">
    <div id="page-content">
      {{IF details}}
      <ul class="products-list _products-wrap">
        {{BEGIN details}}
        <li class="products-list__item">
          {{IF discount}}
            <span class="sticker">-{{discount}}%</span>
          {{ELSE}}
            {{IF new}}
              <span class="hot-products__new-sticker">NEW</span>
            {{ELSE}}
              {{IF often_buy}}
                <span class="hot-products__top-sticker" style="text-indent: -999px;">TOP</span>
              {{END IF}}
            {{END IF}}
          {{END IF}}
          {{IF status == 'restaurare'}}
            <span class="products-list__state-label">{{lng_restaurare}}</span>
          {{ELSE}}
            {{IF status == 'replica'}}
              <span class="products-list__state-label">{{lng_replica}}</span>
            {{ELSE}}
              {{IF status == 'secondhand'}}
                <span class="products-list__state-label">{{lng_secondhand}}</span>
              {{ELSE}}
                {{IF status == 'new'}}
                  <span class="products-list__state-label">{{lng_new}}</span>
                {{END IF}}
              {{END IF}}
            {{END IF}}
          {{END IF}}
          <a class="products-list__caption" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">{{name}}</a><!--
          --><a class="products-list__image product-image-{{id}}" href="/car/{{car_synonym}}/{{autopart_id}}/{{id}}/">
            <div class="products-list__image-inner">
              {{IF image}}
                <img class="products-list__image-element" src="{{url_staticServer}}{{image}}" alt="{{name}}">
              {{ELSE}}
                <img src="/img/noimage.jpg">
              {{END IF}}
            </div>
          </a><span class="products-list__article">{{num_detail}}</span>
          {{IF old_cost}}
            <span class="products-list__old-cost _print-cost" data-cost="{{old_cost_unformat}}" data-usd-cost="{{old_cost_usd}}" href="#buy">
              {{IF currency_abb == 'грн.'}}
                {{number_format(old_cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small>
              {{ELSE}}
                {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{old_cost}}
              {{END IF}}
            </span>
          {{END IF}}
          <span class="products-list__cost _print-cost" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}">
            {{IF currency_abb == 'грн.'}}
              {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small>
            {{ELSE}}
              {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}}
            {{END IF}}
          </span><a class="button-buy products-list__buy products-list__buy_type_hovered" data-id="{{id}}" data-cost="{{cost_unformat}}" data-usd-cost="{{cost_usd}}" href="#buy">
            {{lng_buy}}
          </a></li>
        {{END details}}
      </ul>
      {{END IF}}
    </div>
  </div>
</div>

{{includeScript('footer.tpl')}}
