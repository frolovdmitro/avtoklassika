{{includeScript('head.tpl')}}
<body>
<div id="fb-root"></div>
<div class="wrapper">
  <div class="body-wrap">
      {{content()}}
  </div>
</div>

<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/{{current_language}}_{{fullupper_current_language}}/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<!-- <script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//vk.com/js/api/openapi.js?111";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'vk-jssdk'));</script> -->

<script type="text/javascript">
    window.___gcfg = {lang: '{{current_language}}'};

    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/platform.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
</script>

<script>
</script>

<script>
    (function($) {
        $(function() {
            $('#add-ads__type, #add-ads__category, #add-ads__currency, #my_select,#my_select_2,#my_select_3,#my_select_review,#my_select_review_1,#my_select_review_2,#my_select_review_3').styler({
                idSuffix: '-styler',
                filePlaceholder: 'Файл не выбран',
                fileBrowse: 'Обзор...',
                fileNumber: 'Выбрано файлов: %s',
                selectSearch: false,
                selectSearchLimit: 10,
                selectSearchNotFound: 'Совпадений не найдено',
                selectSearchPlaceholder: 'Поиск...',
                selectVisibleOptions: 0,
                singleSelectzIndex: '100',
                selectSmartPositioning: true,
                locale: 'ru',
                locales: {
                    'en': {
                        filePlaceholder: 'No file selected',
                        fileBrowse: 'Browse...',
                        fileNumber: 'Selected files: %s',
                        selectPlaceholder: 'Select...',
                        selectSearchNotFound: 'No matches found',
                        selectSearchPlaceholder: 'Search...'
                    }
                }
            });
        });
    })(jQuery);
</script>

<script>
    var close =true;
    $('.enter_button').click(
        function() {
            if($(this).attr('href') != '#'){
                return true;
            }
            if(close){
                jQuery('#myModal_bill_item').slideDown(90);
                close = false;
                return false
            }else{
                jQuery('#myModal_bill_item').slideUp(90);
                close = true;
                return false;
            }
        });

    $(document).click( function(event){
        if( $(event.target).closest("#myModal_bill_item").length )
            return;
        $("#myModal_bill_item").fadeOut("slow");
        close = true;
        event.stopPropagation();
    });
</script>

<script>
    $('#slider_level .owl-carousel').owlCarousel({
        loop:false,
        navText: ['',''],
        margin: 0,
        dots: false,
        nav:true,
        responsive:{
            0:{
                items:2
            },
            350:{
                items:2
            },
            530:{
                items:3
            },
            700:{
                items:4
            },
            840:{
                items:4
            },
            1000:{
                items:5
            }
        }
    });
</script>
<script>
    Socialite.setup({
        facebook: {
            lang     : 'en_GB',
            appId    : 123456789,
            onlike   : function(url) { /* ... */ },
            onunlike : function(url) { /* ... */ },
            onsend   : function(url) { /* ... */ }
        }
    });
</script>

<script>
    $('#slider_announcing .owl-carousel').owlCarousel({
        loop:true,
        navText: ['',''],
        margin:10,
        nav:true,
        dots: false,
        responsive:{
            0:{
                items:1
            },
            320:{
                items:1
            },
            380:{
                items:2
            },
            600:{
                items:3
            },
            767:{
                items:3
            },
            768:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });

    $('#my_slider_top .owl-carousel').owlCarousel(
        {
            lazyLoad:true,
            navText: ['',''],
            items:1,
            autoplay: true,
            smartSpeed: 1500,
            nav:true,
            dots: true,
            loop : true
        }
    );
    $('#my_slider_new .owl-carousel').owlCarousel({
        loop: true,
        navText: ['', ''],
        margin: 0,
        nav: true,
        dots: false,
        responsive: {
            0: {
                items: 1
            },
            450: {
                items: 2
            },
            767: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });

</script>
<script>
    $(document).ready(function(){
        $('#language').hover(function() {
            $(this).css('min-height','85px');
        }, function() {
            $(this).removeAttr('style');
        });


        $('#currencies li').click(function(e){
            var parent =$(this).parent();
            var liHeight = $(this).height();
            var cnt = $(this).parent().find('li').length;
            if(parent.attr('data-open') == 'close') {
                console.info('open');
                parent.attr('data-open', 'open');
                parent.css('min-height',(cnt*liHeight+10)+'px');
            }
            else{
                console.info('close');
                location.replace($(this).find('a').attr('href'));
                parent.attr('data-open', 'close');
                parent.removeAttr('style');
                parent.prepend($(this));
            }
            if(location.hash)
                location.hash='';
            return false;
        });

        if($('body').width() >=768) {
            window.onscroll = function () {
                var scrolled = window.pageYOffset || document.documentElement.scrollTop;
                if (scrolled > 40)
                    $('header').addClass('mobile');
                else
                    $('header').removeClass('mobile');
            };
        }
        // $('.top_menu ul li a').click(function(){
        //     $('li a').removeClass("current");
        //     $(this).addClass("current");
        // });
    });
</script>
<script>
    $(".plus_minus").click(function() {
        if($(this).hasClass('minus') == false) {
            $(this).siblings().show();
            $(this).addClass("minus");
        }else{
            $(this).removeClass("minus");
            $(this).siblings('ul').hide();
        }
    });
</script>
{{includeScript('js.tpl')}}
</body>
</html>
