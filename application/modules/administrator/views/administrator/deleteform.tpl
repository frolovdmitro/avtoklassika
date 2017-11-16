{{ UNLESS error }} 
<p>{{ text }}</p>
<br> {{ end error }} {{ IF error }} 
<p class="error">{{ error }}:</p>
<ul class="blocks">
  {{ BEGIN block_table }} 
  <li>Таблица <b>«{{ table }}»</b> ({{ count_rows }} записей)</li>
  {{ END block_table }} 
</ul>
{{ END error }} {{ IF relative }} 
<p class="info">{{ relative }}:</p>
<ul class="relatives">
  {{ BEGIN relative_table }} 
  <li>Таблица <b>«{{ table }}»</b> ({{ count_rows }} записей)</li>
  {{ END relative_table }} 
</ul>
{{ END relative }}
