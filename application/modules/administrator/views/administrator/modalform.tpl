<div {{IF style}}style="{{style}}"{{END style}} class="modalform {{ action }} {{custom_form_class}}"> 
<h1>{{ caption }}</h1>
{{ IF tabs }} 
<div class="tabs"> {{BEGIN tabs}} <a id="{{id}}"{{IF _first}} class="active"{{END _first}} href="#">{{caption}}</a> {{END tabs}} </div>
{{ END tabs }} {{ UNLESS delete }}{{ UNLESS confirm }}{{ UNLESS custom }} {{ includeBlock('administrator', 'administrator', 'formlanguages', $languagesRow) }} {{ END UNLESS }}{{ END UNLESS }}{{ END UNLESS }} {{ IF custom }} {{ includeBlock('administrator', 'administrator', 'customformlanguages', $langConfig) }} {{ END IF }} 
<div class="content-area"> <form {{ UNLESS forward }}id="actionform"{{ END UNLESS }} {{ IF forward }}id="actionform2"{{ END IF }} method="POST" action="/administrator/modalform-action/{{ module }}/{{ type }}/{{ action }}/{{ id }}/?tableName={{ table }}"> {{ IF delete }} {{ includeBlock('administrator', 'administrator', 'deleteform') }} {{ END delete }} {{ IF edit }} {{ includeBlock('administrator', 'administrator', 'editform') }} {{ END edit }} {{ IF add }} {{ includeBlock('administrator', 'administrator', 'addform') }} {{ END add }} {{ IF add_child }} {{ includeBlock('administrator', 'administrator', 'add-childform') }} {{ END add_child }} {{ IF confirm }} {{ includeBlock('administrator', 'administrator', 'confirmform', confirm) }} {{ END confirm }} {{ IF info }} {{ includeBlock('administrator', 'administrator', 'infoform') }} {{ END info }} {{ IF custom }} {{includeBlock('administrator', 'administrator', 'customform', form_fields)}} {{ END custom }} </form> </div>
<p id="fatal-error" class="error" style="margin:0 0 0 15px;padding:0 0 4px;font-size: 12px;color: #A00;"></p>
<div class="footer">
  <div class="button-strip"> {{UNLESS only_close}} <button class="cancel">{{ lng_forms_cancel }}</button> {{ UNLESS error }}<button type="submit" form="{{ UNLESS forward }}actionform{{ END UNLESS }}{{ IF forward }}actionform2{{ END IF }}" class="submit">{{ action_button_caption }}</button>{{ end error }} {{ END only_close}} {{ IF only_close}} <button class="cancel">{{ lng_forms_close }}</button> {{ END UNLESS }} </div>
</div>
</div>
