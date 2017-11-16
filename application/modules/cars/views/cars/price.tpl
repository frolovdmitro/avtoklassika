{{includeScript('header.tpl')}} 
<div class="container">
  <section id="car_park" class="car_park">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="catalog catalog_slider">
            <h1 class="title_catalog"> {{lng_pricelist}} {{name}} </h1>
              {{includeBlock('cars', 'cars', 'slider', id)}}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<div class="container">
  <section id="car_park_level" class="car_park">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 pad_0">
          <div id="breadcrumb">
            <nav class="breadcrumb">
              <a class="breadcrumb-item" href="/" itemprop="url">{{lng_autoparts}}</a>
              <a class="breadcrumb-item" href="/car/{{synonym}}/" itemprop="url">{{name}}</a>
              <span class="breadcrumb-item active">{{lng_pricelist}}</span>
            </nav>
          </div>
          <div class="filter_body center park_level">
            <ul class="table__actions">
              <li class="table__actions-item"><a class="table__actions-link" href="/car/{{synonym}}/price/price.pdf" target="_blank"><strong>PDF</strong> {{lng_download}} </a></li>
              <li class="table__actions-item"><a class="table__actions-link table__actions-link_type_print" href="#print">{{lng_print}}</a></li>
            </ul>
            <div class="table-responsive">
              <table class="table">
                <tr class="table__head">
                  <th class="table__field"> {{lng_num}} </th>
                  <th class="table__field"> {{lng_category}} </th>
                  <th class="table__field"> {{lng_num_by_catalogue}} </th>
                  <th class="table__field"> {{lng_name}} </th>
                  <th class="table__field table__field_min_120"> {{lng_price}} </th>
                </tr>
                  {{BEGIN details}}
                <tr class="table__row">
                  <td class="table__field"> {{_num}} </td>
                  <td class="table__field"> {{autopart}} </td>
                  <td class="table__field"> {{num_detail}} </td>
                  <td class="table__field"> {{name}} </td>
                  <td class="table__field"> <span class="_print-cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </span><a class="button-buy table__buy-link" data-id="{{id}}" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}" href="#buy"> {{lng_to_basket}} </a> </td>
                </tr>
                  {{END ditails}}
              </table>
            </div>
            <ul class="table__actions">
              <li class="table__actions-item"><a class="table__actions-link" href="/car/{{synonym}}/price/price.pdf" target="_blank"><strong>PDF</strong> {{lng_download}} </a></li>
              <li class="table__actions-item"><a class="table__actions-link table__actions-link_type_print" href="#print">{{lng_print}}</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 pad_0 mob_hidden" id="auto_section">
            {{includeBlock('directories', 'directories', 'banner', 'car')}}
          <div class="car-autoparts-tree car-autoparts-tree_align_right">
              {{includeBlock('cars', 'cars', 'treeAutoparts', id)}}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


{{includeScript('footer.tpl')}}
