(function() {
  require.config({
    deps: ['avtoclassika'],
    paths: {
      jquery: '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min',
      sparky: 'assets/sparky',
      Deals: 'modules/deals',
      Ads: 'modules/ads',
      Slider: 'modules/slider',
      Car: 'modules/car',
      Basket: 'modules/basket',
      News: 'modules/news',
      Users: 'modules/users',
      Comments: 'modules/comments',
      yepnope: '../components/external/yepnope/yepnope',
      json2: '../components/external/json2/json2',
      cookie: '../components/external/jquery.cookie/jquery.cookie',
      sticky: '../components/external/sticky/jquery.sticky',
      pubsub: '../components/external/pubsub-js/src/pubsub',
      flexslider: '../components/external/flexslider/jquery.flexslider',
      numeral: '../components/external/numeral/min/numeral.min',
      mixitup: '../components/external/mixitup/jquery.mixitup.min',
      scrollto: '../components/external/jquery.scrollTo/jquery.scrollTo.min',
      uniform: '../components/external/jquery.uniform/jquery.uniform.min',
      swipebox: '../components/local/swipebox/source/jquery.swipebox',
      passwordInput: '../components/external/hideShowPassword/hideShowPassword.min',
      modal: '../components/external/jquery-modal/jquery.modal',
      powertip: '../components/external/jquery-powertip/dist/jquery.powertip',
      typeahead: '../components/external/typeahead.js/dist/typeahead.bundle.min',
      'jquery.ui.widget': '../components/external/blueimp-file-upload/js' + '/vendor/jquery.ui.widget',
      iframe_transport: '../components/external/blueimp-file-upload/js' + '/jquery.iframe-transport',
      fileupload: '../components/external/blueimp-file-upload/js/' + 'jquery.fileupload',
      placeholder: '../components/external/Placeholders.js/build/' + 'placeholders.jquery',
      bezier: '../components/local/jquery.path/jquery.path',
      socialshare: '../components/local/SocialSharePrivacy/javascripts/' + 'jquery.socialshareprivacy.min',
      socialshare_de: '../components/local/SocialSharePrivacy/' + 'javascripts/jquery.socialshareprivacy.min.de',
      socialshare_ru: '../components/local/SocialSharePrivacy/' + 'javascripts/jquery.socialshareprivacy.min.ru',
      hashchange: '../components/local/jquery-hashchange/jquery.ba-hashchange',
      uwinTree: 'plugins/uwinTree',
      uwinPaging: 'plugins/uwinPaging',
      uwinTabs: 'plugins/uwinTabs',
      UwinGoogleMap: 'plugins/uwinGoogleMap'
    },
    shim: {
      hashchange: ['jquery'],
      flexslider: ['jquery'],
      sticky: ['jquery'],
      cookie: ['jquery'],
      mixitup: ['jquery'],
      scrollto: ['jquery'],
      uniform: ['jquery'],
      swipebox: ['jquery'],
      bezier: ['jquery'],
      modal: ['jquery'],
      placeholder: ['jquery'],
      typeahead: ['jquery'],
      passwordInput: ['jquery'],
      powertip: ['jquery'],
      socialshare: ['jquery'],
      socialshare_ru: ['jquery', 'socialshare'],
      socialshare_de: ['jquery', 'socialshare'],
      uwinTree: ['jquery'],
      uwinPaging: ['jquery'],
      uwinTabs: ['jquery'],
      UwinGoogleMap: ['jquery', 'yepnope']
    }
  });

}).call(this);
