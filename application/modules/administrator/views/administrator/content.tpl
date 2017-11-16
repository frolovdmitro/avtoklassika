<div class="module-header">
  <div class="module-icon"></div>
  <div class="module-caption-block">
    <h2 class="caption">{{ $caption }}</h2>
    <p>{{ $description }}</p>
  </div>
  {{ IF tabs }} 
  <ul class="module-tabs">
    {{ BEGIN tabs }} 
    <li><a class="button {{ $small }} {{ $active }} {{ $class }}" href="{{ $address }}">{{ $caption }}</a></li>
    {{ END }} 
  </ul>
  {{ END IF }} 
</div>
<div class="module-wrap">
  <div id="module-main-area"> {{ includeBlock('administrator', 'administrator', 'subpage') }} </div>
</div>
