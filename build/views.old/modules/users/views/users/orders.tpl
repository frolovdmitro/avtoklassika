<table class="table">
  <tr class="table__head">
    <th class="table__field" style="width:30px;text-align:right;"></th>
    <th class="table__field"> {{lng_num}} </th>
    <th class="table__field"> {{lng_date}} </th>
    <th class="table__field"> {{lng_method_delivery}} </th>
    <th class="table__field"> {{lng_method_payment}} </th>
    <th class="table__field"> {{lng_sum}} </th>
    <th class="table__field"> {{lng_status}} </th>
    <th class="table__field"></th>
  </tr>
  {{BEGIN orders}}
  <tr class="table__row">
    <td class="table__field" style="width:30px;text-align:right;"> {{_num}} </td>
    <td class="table__field" style="width:30px;text-align:center;"> #{{num}} </td>
    <td class="table__field" style="width:150px;text-align:center;"> {{datetime}} </td>
    <td class="table__field" style="width:100px;text-align:center;"> {{method_delivery}} {{IF tracking_number}}<br>
      {{IF delivery_id == 1}}<a style="color:#cd4d47" target="_blank" href="https://packageradar.com/courier/ukrposhta/tracking/{{tracking_number}}">{{lng_tracking}}</a>{{END IF}}
      {{IF delivery_id == 2}}<a style="color:#cd4d47" target="_blank" href="https://novaposhta.ua/tracking/?cargo_number={{tracking_number}}">{{lng_tracking}}</a>{{END IF}}
    {{END IF}}</td>
    <td class="table__field" style="width:100px;text-align:center;"> {{method_payment}} </td>
    <td class="table__field" style="width:100px;text-align:center;"> {{IF currency == 'грн.'}} {{sum}}&thinsp;<small>{{currency}}</small> {{ELSE}} {{IF currency == 'P'}}<span class="rur">{{currency}}</span>{{ELSE}}{{currency}}{{END IF}}{{sum}} {{END IF}} </td>
    <td class="table__field" style="text-align:center;"> {{status_text}} </td>
    <td class="table__field" style="text-align:center;"> {{IF status == 'wait_payment'}} <a href="/order-info/{{key}}/continue/">{{lng_continue}}</a> {{ELSE}} {{IF status == 'not_complete'}} <a href="/basket/#step=1">{{lng_ended_order}}</a> {{ELSE}} <a href="/order-info/{{key}}/full/">{{lng_order_info}}</a> {{END IF}} {{END IF}} </td>
  </tr>
  {{END orders}}
</table>
