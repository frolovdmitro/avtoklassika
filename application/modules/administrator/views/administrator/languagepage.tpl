<div class="pageform languageform" style="{{style}}">
  <form id="actionform" method="POST" action="/administrator/modalform-action/{{ module }}/{{ type }}/{{ action }}/">
    <input type="hidden" name="file" id="file" value="{{file}}"> 
    <textarea name="content" id="content">{{value}}</textarea>
    <div class="footer clearfix">
      <button type="submit" form="actionform" class="submit">{{ lng_forms_save }}</button> 
      <p class="success">{{ lng_forms_success }}</p>
    </div>
  </form>
</div>
