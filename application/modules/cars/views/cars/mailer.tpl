<table style="border:1px solid #DFEEDD;" width="100%" cellpadding="5" cellspacing="1" border="0">
  <tr>
    <td bgcolor="#CDDAD0" height="35"> фото / photo </td>
    <td bgcolor="#CDDAD0" height="35"> наименование / name </td>
    <td bgcolor="#CDDAD0" height="35"> стоимость / cost (USD) </td>
  </tr>
  <tr{{if _odd}} bgcolor="#F3F8F2" {{end if}}> 
  <td align="center"> {{IF image}} <a target="_blank" href="http://avtoclassika.com/car/{{car_synonym}}/{{autopart_id}}/{{detail_id}}/"><img src="{{url_staticServer}}{{image}}"></a> {{ELSE}} <a target="_blank" href="http://avtoclassika.com/car/{{car_synonym}}/{{autopart_id}}/{{detail_id}}/"><img src="//s1.avtoclassika.com/img/noimage-sm-1bd30ac1.jpg"></a> {{END IF}} </td>
  <td>
    {{IF num}} <font color="#6E7A71" style="font-size:18px;padding-bottom:5px;"> {{num}} </font> {{END IF}} {{IF name}} <br> <a target="_blank" style="font-size:14px;padding-bottom:5px;" href="http://avtoclassika.com/car/{{car_synonym}}/{{autopart_id}}/{{detail_id}}/"><font color="#134936">{{name}}</font></a> {{END IF}} {{IF description}} 
    <p style="margin:4px 0;color:#898989;font-size:14px;"> {{description}} </p>
    {{END IF}} {{IF status}} 
    <div style="border:1px solid #ffffff;padding:3px 10px; background-color:#DADED9;text-transform:uppercase;color:#134936; text-align:center;width:200px;margin:5px 0;"> {{status}} </div>
    {{END IF}} 
  </td>
  <td style="font-size:18px" align="center"> <font color="#1B543D"><strong>{{cost}}</strong></font> </td>
  </tr> 
</table>
