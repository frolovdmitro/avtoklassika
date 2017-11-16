{{IF news}}
{{BEGIN news}}
<div class="item clearfix">
  <div class="item_group clearfix">
    <div class="image">
      <div class="img">
        <a href="/news/{{synonym}}/"><img src="{{url_staticServer}}{{image}}" alt="{{name}}"></a>
      </div>
    </div>
    <div class="info">
      <h2><a href="/news/{{synonym}}/"> {{name}} </a></h2>
      <div class="caption_hot">
          {{date}}
      </div>
      <p> {{description}} </p>
      <a href="/news/{{synonym}}/#comments=true" class="comments"> {{lng_comments}} <sub>({{count_comments}})</sub></a>
    </div>
  </div>
</div>
{{END news}}
{{END IF}}
