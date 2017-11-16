{{BEGIN tabs}}
  <dl id="{{id}}-tab" class="clearfix" {{UNLESS _first}}style="display:none"{{END _first}}>
    {{ BEGIN field }}
      {{ IF row }}
        <dt {{ IF language }}data-lang="{{language}}"{{ END IF }} style="{{ IF language_hidden}}display: none;{{ END IF }}{{ label_style }}">
          <label for="{{ id }}">{{ caption }}{{ IF required }}&nbsp;<span>*</span>{{ END required }}</label>
        </dt>
        <dd {{ IF language }}data-lang="{{language}}"{{ END IF }} {{ IF readonly }}class="readonly"{{ end readonly }} style="{{ IF language_hidden}}display: none;{{ END IF }}{{ style }}">
          {{ IF input }} <input id="{{ id }}" name="{{ id }}" type="text" {{ IF focused }}autofocus {{ end focused }}value="{{ value }}" {{ IF readonly }}readonly {{ end readonly }}placeholder="{{ description }}"> {{ END input }} {{ IF password }} <input id="{{ id }}" name="{{ id }}" type="password" {{ IF focused }}autofocus {{ end focused }}value="{{ value }}" {{ IF readonly }}readonly {{ end readonly }}placeholder="{{ description }}"> {{ END password }} {{ IF textarea }} <textarea id="{{ id }}" name="{{ id }}" rows="{{ rows }}" {{IF richNoStyleFile}}richnostyle="true"{{END IF}} richclass="{{richContainerClass}}" richclassdynamic="{{richContainerClassDinamyc}}" class="{{IF richedit}}richedit{{END IF}} {{ IF code }}codepress {{ code }}{{ END code }}" {{IF code}}data-code="{{code}}"{{END code}} {{ IF focused }}autofocus {{ end focused }} {{ IF readonly }}readonly {{ end readonly }}placeholder="{{ description }}">{{value}}</textarea> {{ END textarea }} {{ IF file }} <input id="{{ id }}" name="{{ id }}" type="file" {{ IF focused }}autofocus {{ end focused }}> {{ END input }} {{ IF list }} <select id="{{ id }}" name="{{ id }}" {{ IF focused }}autofocus {{ end focused }} {{ IF readonly }}disabled{{ end readonly }}> {{ BEGIN item}} <option {{ default }} value="{{ id }}">{{ caption }}</option> {{ END item }} </select> {{ END list }}
          {{ IF html }} 
            <div id="{{id}}">{{value}}</div>
          {{ END html }} 
          <p id="error-{{ id }}" class="error"></p>
        </dd>
      {{ END row}}
      {{ UNLESS row }}
        <dd class="row"{{ IF style }} style="{{ style }}"{{ END style }}> 
          <div class="input-block"> {{ IF bool }} <input id="{{ id }}" name="{{ id }}" type="checkbox" value="true" {{ IF value }}checked {{ end value }}> <label for="{{ id }}">{{ caption }}</label> {{ END bool }} </div>
        </dd>
      {{ END row }}
    {{ END field }}
  </dl>
{{END tabs}}
