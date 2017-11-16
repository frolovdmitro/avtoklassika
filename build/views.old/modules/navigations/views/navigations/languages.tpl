{{IF languages}} <div class="languages-bar"> <span class="languages-bar__link languages-bar__link_state_current"> {{current}} </span>
  <ul class="languages-bar__list"> {{BEGIN languages}}
    {{UNLESS synonym}}
    <li class="languages-bar__item"><a class="languages-bar__link" href="http://{{IF synonym}}{{synonym}}.{{END IF}}{{host}}{{url}}/"> {{name}} </a></li>
    {{ELSE}}
    {{IF synonym == 'en'}}
    <li class="languages-bar__item"><a class="languages-bar__link" href="http://en.avtoclassika.com{{url}}/"> {{name}} </a></li>
    {{ELSE}} <li class="languages-bar__item"><a class="languages-bar__link" href="http://{{IF synonym}}{{synonym}}.{{END IF}}{{host}}{{url}}/"> {{name}} </a></li>
    {{END}}
{{END UNLESS}}
    {{END languages}} </ul> </div> {{END IF}}
