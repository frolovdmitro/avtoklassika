<!--{{IF news}}
<div class="news-bar">
  <a class="news-bar__header ribbon ribbon_type_right-inner" href="/news/"> {{lng_news}} </a>
  <ul class="news-bar__list">
    {{BEGIN news}} 
    <li class="news-bar__item">
      <a class="news-bar__item-image-link" href="/news/{{synonym}}/">
        <img class="news-bar__item-image" src="{{url_staticServer}}{{image}}" alt="{{name}}" width="58" height="50">
      </a>
      <p class="news-bar__item-name">
        <strong class="news-bar__item-date">{{date}}</strong><br>
        <a class="news-bar__item-link" href="/news/{{synonym}}/">{{name}}</a>
      </p>
    </li>
    {{END news}} 
  </ul>
</div>
{{END IF}}-->


{{IF news}}
<div class="news">
  <h1 class="title">
    <a href="/news/"> {{lng_news}} </a>
  </h1>
  <ul class="news-bar_list clearfix">
    {{BEGIN news}}
      <li class="news-bar_item clearfix">
        <a class="news-bar_item-image-link" href="/news/{{synonym}}/">
          <img class="news-bar_item-image" src="{{url_staticServer}}{{image}}" alt="{{name}}" width="58" height="50">
        </a>
        <p class="news-bar_item-name">
          <strong class="news-bar_item-date">{{date}}</strong><br>
          <a class="news-bar_item-link" href="/news/{{synonym}}/">{{name}}</a>
        </p>
      </li>
    {{END news}}
  </ul>
</div>
{{END IF}}
