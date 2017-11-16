{{ IF languages }} 
  <ul id="module-languages" class="languages-switch">
    {{ BEGIN languages }}
      <li {{ IF active }}class="active"{{ END IF }} data-lang="{{ synonym }}"><a href="#">{{ name }}</a></li>
    {{ END languages }}
  </ul>
{{ END IF }}
