{{ IF languages }} 
  <ul id="form-languages" class="languages-switch">
    {{ BEGIN languages }}
      <li {{ IF active }}class="active"{{ END IF }} data-lang="{{ synonym }}"><input form="actionform" type="checkbox" {{ IF use }}checked {{ END IF }}name="form-lang-{{ synonym }}" id="form-lang-{{ synonym }}" value="true"><a href="#">{{ name }}</a></li>
    {{ END languages }}
  </ul>
{{ END IF }}
