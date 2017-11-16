{{IF items}} 
<ul id="currencies" class="currencies">
  {{BEGIN items}} 
  <li class="currencies__item"><a class="currencies__link" data-ratio="{{ratio}}" data-ratio-id="{{id}}" data-short-name="{{short_name}}" href="#{{synonym}}">
    {{currency}}
    <strong class="currencies__short-name"> {{IF short_name == 'P'}} <span class="rur">{{short_name}}</span> {{ELSE}} {{short_name}} {{END IF}} </strong></a>
  </li>
  {{END items}} 
</ul>
{{END items}}
