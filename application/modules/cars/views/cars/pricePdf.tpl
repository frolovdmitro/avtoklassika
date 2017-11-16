<!doctype html>
<html class="no-js" lang="{{current_language}}-{{upper_current_language}}">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>{{title}}</title>
    <meta name="description" content="{{description}}">
    <meta name="keywords" content="{{keywords}}">
  </head>
  <body>
    <h1 style="font-family: DejaVu Sans; font-weight: normal;font-size: 18px;"> Avtoclassika.com - Прайс {{name}} </h1>
    <table class="table" style="font-family: DejaVu Sans; font-weight: normal; border: 1px solid #000;border-collapse: collapse; border-spacing: 0; font-size: 14px;">
      <tr class="table__head" style="background: #ccc;">
        <th class="table__field" style="border: 1px solid #000;padding:5px;"> {{lng_num}} </th>
        <th class="table__field" style="border: 1px solid #000;padding:5px;"> {{lng_category}} </th>
        <th class="table__field" style="border: 1px solid #000;padding:5px;"> {{lng_num_by_catalogue}} </th>
        <th class="table__field" style="border: 1px solid #000;padding:5px;"> {{lng_name}} </th>
        <th class="table__field table__field_min_120" style="border: 1px solid #000;padding:5px;"> {{lng_price}} </th>
      </tr>
      {{BEGIN details}} 
      <tr class="table__row">
        <td class="table__field" style="border: 1px solid #000;padding:5px;"> {{_num}} </td>
        <td class="table__field" style="border: 1px solid #000;padding:5px;"> {{autopart}} </td>
        <td class="table__field" style="border: 1px solid #000;padding:5px;"> {{num_detail}} </td>
        <td class="table__field" style="border: 1px solid #000;padding:5px;"> {{name}} </td>
        <td class="table__field" style="border: 1px solid #000;padding:5px;">
          <nobr><span class="_print-cost" data-usd-cost="{{cost_usd}}" data-cost="{{cost_unformat}}"> {{IF currency_abb == 'грн.'}} {{number_format(cost_unformat, 0, ',', '&thinsp;')}}&thinsp;<small>{{currency_abb}}</small> {{ELSE}} {{IF currency_abb == 'P'}}<span class="rur">{{currency_abb}}</span>{{ELSE}}{{currency_abb}}{{END IF}}{{cost}} {{END IF}} </span></nobr>
        </td>
      </tr>
      {{END ditails}} 
    </table>
  </body>
</html>
