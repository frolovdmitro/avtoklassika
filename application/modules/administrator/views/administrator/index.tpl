<div id="overlay" class="overlay invisible transparent"></div>
<div id="wrap">
  <div class="inner-wrap">
    <div class="header">
      <div class="logo">
        <img src="//s1.avtoclassika.com/img/backend/logo-6289fde9.png"> 
        <h1>Панель управления</h1>
      </div>
      <div class="main-header"> </div>
      {{ includeBlock('administrator', 'administrator', 'userbar') }} 
    </div>
    <div class="content">
      <div class="sidebar">
        <div class="home-wrap"> <a class="gohome" href="/">{{ $lng_index_backToSite }}</a> </div>
        {{ includeBlock('administrator', 'administrator', 'modulesmenu') }} 
        <p class="copyright">Copyright &copy; 2009&mdash;2014 <a href="http://uwinart.com/">Uwinart Development</a></p>
      </div>
      <div class="main">
        <div class="mainmenu-wrap"> {{ includeBlock('administrator', 'administrator', 'mainmenu') }} </div>
        <div id="main-content"> </div>
      </div>
    </div>
  </div>
</div>
