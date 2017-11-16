<div class="pageform" style="{{style}}">
  <form id="actionform" method="POST" action="/administrator/modalform-action/{{ module }}/{{ type }}/{{ action }}/">
    <dl class="clearfix">
      {{ BEGIN field }} {{ IF group }} <h{{level}}>{{group}}</h{{level}}> {{ END group }} {{ IF row }} <dt{{ IF label_style }} style="{{ label_style }}"{{ END label_style }}><label for="{{ id }}">{{ caption }}{{ IF required }}&nbsp;<span>*</span>{{ END required }}</label></dt> <dd {{ IF readonly }}class="readonly"{{ end readonly }}{{ IF style }} style="{{ style }}"{{ END style }}> {{ IF input }} <input id="{{ id }}" name="{{ id }}" type="text" {{ IF focused }}autofocus {{ end focused }}value="{{ value }}" {{ IF readonly }}readonly {{ end readonly }}placeholder="{{ description }}"> {{ END input }} {{ IF password }} <input id="{{ id }}" name="{{ id }}" type="password" {{ IF focused }}autofocus {{ end focused }}value="{{ value }}" {{ IF readonly }}readonly {{ end readonly }}placeholder="{{ description }}"> {{ END password }} {{ IF textarea }} <textarea id="{{ id }}" name="{{ id }}" rows="{{ rows }}" {{IF richNoStyleFile}}richnostyle="true"{{END IF}} richclass="{{richContainerClass}}" richclassdynamic="{{richContainerClassDinamyc}}" class="{{IF richedit}}richedit{{END IF}} {{ IF code }}codepress {{ code }}{{ END code }}" {{ IF focused }}autofocus {{ end focused }} {{ IF readonly }}readonly {{ end readonly }}placeholder="{{ description }}">{{value}}</textarea> {{ END textarea }} {{ IF file }} <input id="{{ id }}" name="{{ id }}" type="file" {{ IF focused }}autofocus {{ end focused }}> {{ END input }} {{ IF list }} <select id="{{ id }}" name="{{ id }}" {{ IF focused }}autofocus {{ end focused }} {{ IF readonly }}disabled{{ end readonly }}> {{ BEGIN item}} <option {{ default }} value="{{ id }}">{{ caption }}</option> {{ END item }} </select> {{ END list }} 
      <p id="error-{{ id }}" class="error"></p>
      </dd> {{ END row}} {{ UNLESS row }} <dd class="row"{{ IF style }} style="{{ style }}"{{ END style }}> 
      <div class="input-block"> {{ IF bool }} <input id="{{ id }}" name="{{ id }}" type="checkbox" value="true" {{ IF value }}checked {{ end value }}> <label for="{{ id }}">{{ caption }}</label> {{ END bool }} </div>
      </dd> {{ END row }} {{ END field }} 
    </dl>
    <div class="footer clearfix">
      <button type="submit" form="actionform" class="submit">{{ lng_forms_save }}</button> 
      <p class="success">{{ lng_forms_success }}</p>
    </div>
  </form>
</div>
