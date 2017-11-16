<table class="table">
  <tr class="table__head">
    <th class="table__field">
      {{lng_num}}
    </th>
    <th class="table__field">
      {{lng_date}}
    </th>
    <th class="table__field">
      ЗАГОЛОВОК
    </th>

    <th class="table__field">
      {{lng_status}}
    </th>
    <th class="table__field">
      Действия
    </th>

  </tr>


  {{BEGIN adverts}}



  <tr class="table__row">
    <td class="table__field" style="width:30px;text-align:right;">
      {{_num}}
    </td>

    <td class="table__field" style="width:150px;text-align:center;">
      {{date}}
    </td>
    <td class="table__field" style="width:100px;text-align:center;">
      {{title}}
    </td>
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

<!-- <script type="text/javascript">
  onload = function(e) {
    links = document.getElementsByClassName('cabinet__edit-link');
    for(i=0; i<links.length; i++) {
      links[i].addEventListener('click', function(e){e.stopPropagation();}, false);
    }
  }
</script> -->



</table>
