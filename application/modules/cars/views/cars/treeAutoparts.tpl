<h2 class="car-autoparts-tree__header ribbon ribbon_type_right-inner"> {{lng_sections}} </h2>
<ul class="car-autoparts-tree__list">
  <li class="car-autoparts-tree__item">
    <div class="car-autoparts-tree__item-plus-minus expanded" style="visibility:hidden;"></div>
    <a class="car-autoparts-tree__item-link" style="text-transform:uppercase;" href="/car/{{car_synonym}}/all/">{{lng_all_autoparts}}</sup> </a>
  </li>
  {{BEGIN nodes}}
    <li class=" __item-{{apt_parent_id_fk}} {{IF depth== '1'}} car-autoparts-tree__item{{END IF}} {{IF depth== '2'}} car-autoparts-tree__subitem __hidden{{END IF}} {{IF depth== '3'}} car-autoparts-tree__subsubitem __hidden{{END IF}} " data-id="{{id}}" data-car-id="{{car_id}}">
      {{IF depth != '3'}}
      <div class=" {{IF depth== '1'}} car-autoparts-tree__item-plus-minus{{END IF}}
        {{IF depth== '2'}} car-autoparts-tree__subitem-plus-minus{{END IF}} expanded"></div>
      {{END IF}}
      <a class=" {{IF depth== '1'}} car-autoparts-tree__item-link{{END IF}}
        {{IF depth== '2'}} car-autoparts-tree__subitem-link{{END IF}}
        {{IF depth== '3'}} car-autoparts-tree__subsubitem-link{{END IF}} "
         href="/car/{{car_synonym}}/{{id}}/"> {{name}}&nbsp;<sup>{{count}}</sup> </a>
    </li>
  {{END nodes}}
</ul>