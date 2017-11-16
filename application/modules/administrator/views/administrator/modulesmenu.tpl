{{ IF $modulesMenu }} 
<ul class="module-menu">
  {{ BEGIN modulesMenu }} 
  <li>
    <a href="{{ $address }}" class="caption {{ $active }}">{{ $caption }}</a> {{ IF modulesSubmenu }} <a id="submodule-exp-{{ $class }}" href="#" class="visible-module-submenu">{{ $hide }}</a> {{ END modulesSubmenu }} {{ IF modulesSubmenu }} 
    <ul class="module-submenu">
      {{ BEGIN modulesSubmenu }} 
      <li> <a {{ IF active }}class="active" {{ END active }} href="{{ $address }}">{{ $caption }}</a> </li>
      {{ END}} 
    </ul>
    {{ END IF }} 
  </li>
  {{ END }} 
</ul>
{{ END IF }}
