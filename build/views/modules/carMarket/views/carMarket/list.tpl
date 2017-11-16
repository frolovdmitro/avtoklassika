<!DOCTYPE html>
<html class="no-js" lang="ru-Ru">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta property="og:site_name" content="" />
  <meta property="og:image" content="" />
  <meta property="og:locale" content="" />
  <meta property="og:title" content="" />
  <meta property="og:description" content="" />
  <meta property="og:site" content="" />
  <meta property="og:creator" content="" />
  <meta property="og:title" content="" />
  <meta property="og:description" content="" />
  <meta property="og:image" content="" />
  <meta property="og:image:width" content="" />
  <meta property="og:image:height" content="" />
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="stylesheet" href="//s1.avtoclassika.com/css/car-market/app-1a31367e13a11c128f8c.css">
</head>
<body>

<header class="header">
  <div class="logo">
    <a class="logo__link" href="/car-market/">
      <img class="logo_image" src="//s1.avtoclassika.com/img/car-market/logo.png" alt="Avtoclassika.com">
    </a>
  </div>

  <a href="/" class="header__menu-button">Меню</a>
  <ul class="header__social-buttons social-buttons social-buttons_invert">
    <li class="social-buttons__item">
      <a class="social-buttons__link social-buttons__link_phone"
        href="tel:"
      >
      </a>
    </li>
    <li class="social-buttons__item">
      <a class="social-buttons__link social-buttons__link_facebook"
        href="https://www.facebook.com/avtoclassika" target="_blank"
      >
      </a>
    </li>
    <li class="social-buttons__item">
      <a class="social-buttons__link social-buttons__link_gplus"
        href="" target="_blank"
      >
      </a>
    </li>
  </ul>
</header>

<div class="subheader__wrap">
  <div class="subheader">
    <h1 class="subheader__h1">
      {{lng_autoBuy}}
    </h1>
  </div>
</div>

<div class="breadcrumbs__wrap">
  <ul class="breadcrumbs">
    <li class="breadcrumbs__item">
      <a class="breadcrumbs__link" href="/">{{lng_main}}</a>
    </li>
    <li class="breadcrumbs__item">
      <span class="breadcrumbs__link">{{lng_carsCatalogue}}</span>
    </li>
  </ul>
</div>

<div class="car-market-content__wrap">
  <ul class="car-market-content">
    {{BEGIN cars}}
      <li class="car-market-content__item">
        <div class="car-market-content__item-content">
          <div class="car-market-content__item-content-header">
            {{car_name}}

            <strong>{{year}}</strong>
          </div>
          <div class="car-market-content__item-features">
            <ul>
              {{BEGIN general_features}}
              <li>{{name}}</li>
              {{END general_features}}
            </ul>

            <strong class="car-market-content__item-cost">{{price}}<span>EUR</span></strong>

            <div class="car-market-content__item-details-wrap">
              <a class="car-market-content__item-details" href="/car-market/{{id}}/">
                {{lng_details}}
              </a>
            </div>
          </div>
          <div class="car-market-content__item-text">
            {{small_description}}
          </div>
        </div>

        <div class="car-market-content__item-photo">
          <img class="car-market-content__item-photo-image"
            src="//s1.avtoclassika.com{{image}}" alt="car">
          {{IF is_original == 't'}}
          <div class="car-market-content__item-original">original</div>
          {{END IF}}
        </div>
      </li>
    {{END cars}}
  </ul>
  <br>
  <br>
</div>

<div class="advantages__wrap">
  <div class="advantages">
    <h3 class="advantages__header">{{lng_benefits}}</h3>

    <ul class="advantages__list">
      <li class="advantages__item advantages__item_online-support">
        {{lng_benefit_1}}
      </li>

      <li class="advantages__item advantages__item_in-transit">
        {{lng_benefit_2}}
      </li>

      <li class="advantages__item advantages__item_maintenance">
        {{lng_benefit_3}}
      </li>
    </ul>
  </div>
</div>

<div class="footer__wrap">
  <footer class="footer">
    <div class="footer-contacts">
      <img class="footer-contacts__logo" src="//s1.avtoclassika.com/img/car-market/logo-light.png"
        alt="avtoclassika.com">
      <p class="footer-contacts__text">
        {{lng_contacts}}
      </p>

      <ul class="footer__social-buttons social-buttons social-buttons_invert">
        <li class="social-buttons__item">
          <a class="social-buttons__link social-buttons__link_phone"
            href="tel:"
          >
          </a>
        </li>
        <li class="social-buttons__item">
          <a class="social-buttons__link social-buttons__link_facebook"
            href="https://www.facebook.com/avtoclassika" target="_blank"
          >
          </a>
        </li>
        <li class="social-buttons__item">
          <a class="social-buttons__link social-buttons__link_gplus"
            href="" target="_blank"
          >
          </a>
        </li>
      </ul>
    </div>

    <div class="footer-menu">
      <ul class="footer-menu__list">
        <li class="footer-menu__item">
          <a class="footer-menu__link" href="{{lng_menu_1_link}}">{{lng_menu_1}}</a>
        </li>
        <li class="footer-menu__item">
          <a class="footer-menu__link" href="{{lng_menu_2_link}}">{{lng_menu_2}}</a>
        </li>
        <li class="footer-menu__item">
          <a class="footer-menu__link" href="{{lng_menu_3_link}}">{{lng_menu_3}}</a>
        </li>
        <li class="footer-menu__item">
          <a class="footer-menu__link" href="{{lng_menu_4_link}}">{{lng_menu_4}}</a>
        </li>
      </ul>
    </div>
  </footer>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//s1.avtoclassika.com/js/car-market.js"></script>
</body>
</html>
