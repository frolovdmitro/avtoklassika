

<div class="table-responsive">
  <table class="table history">
    <tr class="table__head">
      <td class="table__field"></td>
      <td class="table__field"> {{lng_num}} </td>
      <td class="table__field"> {{lng_date}} </td>
      <td class="table__field"> {{lng_method_delivery}} </td>
      <td class="table__field"> {{lng_method_payment}} </td>
      <td class="table__field"> {{lng_sum}} </td>
      <td class="table__field"> {{lng_status}} </td>
      <td class="table__field"></td>
    </tr>
    {{BEGIN orders}}
      <tr class="table__row">
        <td class="table__field"> {{_num}} </td>
        <td class="table__field"> #{{num}} </td>
        <td class="table__field"> {{datetime}} </td>
        <td class="table__field">
            {{method_delivery}} {{IF tracking_number}}<br>
            {{IF delivery_id == 1}}<a style="color:#cd4d47" target="_blank" href="https://packageradar.com/courier/ukrposhta/tracking/{{tracking_number}}">{{lng_tracking}}</a>{{END IF}}
            {{IF delivery_id == 2}}<a style="color:#cd4d47" target="_blank" href="https://novaposhta.ua/tracking/?cargo_number={{tracking_number}}">{{lng_tracking}}</a>{{END IF}}
            {{END IF}}
        </td>
        <td class="table__field"> {{method_payment}} </td>
        <td class="table__field">
            {{IF currency == 'грн.'}} {{sum}}&thinsp;<small>{{currency}}</small> {{ELSE}} {{IF currency == 'P'}}
            <span class="rur">{{currency}}</span>{{ELSE}}{{currency}}{{END IF}}{{sum}} {{END IF}}
        </td>
        <td class="table__field"> {{status_text}} </td>
        <td class="table__field">
            {{IF status == 'wait_payment'}}
              <a href="/order-info/{{key}}/continue/">{{lng_continue}}</a>
              {{ELSE}} {{IF status == 'not_complete'}}
                <a href="/basket/#step=1">{{lng_ended_order}}</a>
              {{ELSE}}
                <a href="/order-info/{{key}}/full/">{{lng_order_info}}</a>
              {{END IF}}
            {{END IF}}
        </td>
      </tr>
    {{END orders}}
  </table>
</div>
