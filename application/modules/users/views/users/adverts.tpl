

<style>
  .table__field__link{
    text-align: center;
  }
  .adv__edit-link{
    display: inline-block;
    color: #6e7c6e;
    font-style: italic;
    position: relative;
    padding-left: 28px;
    line-height: 24px;
  }
  .adv__edit-link:before{
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    background-image: url(//s1.avtoclassika.com/img/_icons-7508c84f.png);
    background-position: -10px -1683px;
    width: 24px;
    height: 24px;
  }
</style>



</table>

<div class="table-responsive">
  <table class="table advert">
    <tr class="table__head">
      <td class="table__field"> {{lng_num}} </td>
      <td class="table__field"> {{lng_date}} </td>
      <td class="table__field"> {{lng_date}} </td>
      <td class="table__field"> {{lng_status}} </td>
      <td class="table__field"> Действия </td>
    </tr>

  {{BEGIN adverts}}
    <tr class="table__row">
      <td class="table__field"> {{_num}} </td>
      <td class="table__field"> {{date}} </td>
      <td class="table__field"> {{title}} </td>
      <td class="table__field">
        {{IF status == 't'}}
          опубликовано
        {{END IF}}
        {{IF status == 'f'}}
          не опубликовано
        {{END IF}}
      </td>
      <td class="table__field table__field__link">
        {{IF status == 't'}}
          <a class="adv__edit-link" href="/cabinet/editadvert/?id={{id}}">Редактировать</a>
        {{END IF}}
      </td>
    </tr>
  {{END adverts}}
  </table>

</div>
