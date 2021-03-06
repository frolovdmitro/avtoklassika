<div class="page-content">
  {{ IF datasources }}
    {{ includeBlock('administrator', 'administrator', 'crumbs') }}
    {{ includeBlock('administrator', 'administrator', 'modulelanguages') }}
    {{ includeBlock('administrator', 'administrator', 'filters') }}
    {{ includeBlock('administrator', 'administrator', 'markers') }}
    {{ includeBlock('administrator', 'administrator', 'actions') }}
    {{ IF table }}
      {{ includeBlock('administrator', 'administrator', 'table', null, false) }}
    {{ END table }}
  {{ END datasources }}
  {{ IF configs }}
    {{ includeBlock('administrator', 'administrator', 'settingslanguages') }}
    {{ includeBlock('administrator', 'administrator', 'formpage') }}
  {{ END configs }}
  {{ IF language }}
    {{ includeBlock('administrator', 'administrator', 'settingslanguages') }}
    {{ includeBlock('administrator', 'administrator', 'languagepage') }}
  {{ END language }}
  {{ IF page }}
    {{ includeBlock(module, model, action) }}
  {{ END page }}
  {{ IF charts }}
    {{ includeBlock('administrator', 'administrator', 'chartspage') }}
  {{ END charts }}
</div>
