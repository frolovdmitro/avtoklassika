{{IF items}}
  <ul id="currencies" data-open="close" class="currencies" class="currencies navbar-right">
    {{BEGIN items}}
      <li class="currencies__item">
        <a class="currencies__link" data-ratio="{{ratio}}" data-ratio-id="{{id}}" data-short-name="{{short_name}}" href="#{{synonym}}">
        <!-- <a class="currencies__link" data-ratio="{{ratio}}" data-ratio-id="{{id}}" data-short-name="{{short_name}}" > -->
          {{currency}}
            <strong class="currencies__short-name"> {{IF short_name == 'P'}}
              <span class="rur">{{short_name}}</span>
              {{ELSE}}
                {{short_name}}
              {{END IF}}
            </strong>
        </a>
      </li>
    {{END items}}
  </ul>
{{END items}}



