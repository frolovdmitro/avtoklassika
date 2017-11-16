

{{includeScript('header.tpl')}}
<div class="container">
  <section id="payment" class="">
    <div class="row">
      <div class="container_center clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <h1 class="title_3 top_0">{{caption}}</h1>
          <div id="breadcrumb">
            <nav class="breadcrumb clearfix">
              <a class="breadcrumb-item" itemprop="url" href="/">{{lng_autoparts}}</a>
              <span class="breadcrumb-item active">{{caption}}</span>
            </nav>
          </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pad_0">
          <div class="posluga clearfix">
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 pad_0 mob_visible_768">
              <div class="content payment">
                  {{text}}
              </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 pad_0 mob_hidden_768">
                {{includeBlock('directories', 'directories', 'banner', 'car')}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
{{includeScript('footer.tpl')}}