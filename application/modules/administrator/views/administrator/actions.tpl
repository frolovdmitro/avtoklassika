{{ IF action }}
<div class="actions">
  {{ BEGIN action}}
    <a class="button table-action" href="{{ module_url }}{{ name }}/">{{ caption }}</a>
  {{ END action }}
  {{IF cars_tbl}}
    <a class="button direct-link" target="_blank" href="/cars/autobazar-excel/">Автобазар Excel</a>
  {{END IF}}
  {{IF users_tbl}}
    <a class="button direct-link" target="_blank" href="/users/generate-users-excel/">Пользователи в Excel</a>
  {{END IF}}
</div>
{{ END action }}
