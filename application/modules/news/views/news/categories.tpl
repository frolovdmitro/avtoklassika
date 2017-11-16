
<div class="clearfix rihgt_menu">
  <ul class="all_menu clearfix">
    <h1 class="title">{{lng_categories}}</h1>
    <li class="1 nav_item"><a href="/news/">{{lng_all}}<sup>{{total_count}}</sup></a>
    {{BEGIN categories}} {{IF count}}
    <li class="1 nav_item"><a href="/news/category/{{synonym}}/">{{name}}<sup>{{count}}</sup></a>
      <div class="plus_minus"></div>
      <ul class="sub_menu">
        {{BEGIN cars}}
          <li>
            <a href="/news/category/{{category_synonym}}/{{car_synonym}}/">
                {{if current_language == 'en'}} {{name_en}} {{end if}} {{if current_language == 'de'}} {{name_de}} {{end if}} {{if current_language == 'ru'}} {{name_ru}} {{end if}}
            </a>
          </li>
        {{END cars}}
      </ul>
    </li>
    {{END IF}} {{END categories}}
  </ul>
</div>