<?php
/**
 * Uwin CMS
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2012 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\TemplaterBlitz as Templater,
    \Uwin\Model\Abstract_,
    \Uwin\DateTime,
    \Uwin\Cacher\Memcached,
    \Uwin\Controller\Request;

/**
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Utils extends Abstract_
{
  const FIRST_PAGE = 1;

  public static function getPaging($currentPage, $lastPage, $visiblePageNumber = 7) { // {{{
    $paging = [];

    // Если текущая страничка меньше первой, то устанавливаем значение текущей страницы равное 1
    if ($currentPage < self::FIRST_PAGE) {
      $currentPage = self::FIRST_PAGE;

      // Если текущая страничка больше максимальной, то устанавливаем значение текущей страницы равное максимальному
    } elseif ($currentPage > $lastPage) {
      $currentPage = $lastPage;
    }

    if ($visiblePageNumber < $lastPage) {
      $first = $currentPage;
      $last = $currentPage;

      while ($last - $first < $visiblePageNumber - self::FIRST_PAGE) {
        if ($first > self::FIRST_PAGE) {
          $first--;
        }

        if ($last < $lastPage) {
          $last++;
        }
      }

      if ($first > self::FIRST_PAGE) {
        $paging['toFirst'] = true;
      }

      if ($last < $lastPage) {
        $paging['toLast'] = true;
      }
    } elseif (self::FIRST_PAGE == $lastPage) {
      return ['pages' => []];
    } else {
      $first = self::FIRST_PAGE;
      $last = $lastPage;
    }

    if ($currentPage - 1 > 0) {
      $paging['toPrevious'] = $currentPage - 1;
    }

    if ($currentPage + 1 <= $lastPage) {
      $paging['toNext'] = $currentPage + 1;
    }

    for ($i = 0; $first <= $last; $first++, $i++) {
      $paging['pages'][$i]['pageNumber'] = $first;
      if ($currentPage == $first) {
        $paging['pages'][$i]['isCurrentPage'] = true;
      }
    }

    return $paging;
  } // }}}

  public function getLanguagesString($values) { // {{{
    $languages_str = null;

    $languages = $this->db()->query()
      ->addSql('select lng_synonym from languages_tbl order by lng_order')
      ->addTag('languages')
      ->fetchResult(false);

    if ( empty($languages) ) {
      return null;
    }

    foreach ($languages as $language) {
      if ( isset($values['form-lang-' . $language['lng_synonym']])
        && 'true' == $values['form-lang-' . $language['lng_synonym']])
      {
        $languages_str .= $language['lng_synonym'] . '|';
      }
    }

    $languages_str = '|' . trim($languages_str, '|') . '|';

    return $languages_str;
  } // }}}

  public function getCurrentLanguage($values) { // {{{
    $languages = $this->db()->query()
      ->addSql('select lng_synonym from languages_tbl order by lng_order')
      ->addTag('languages')
      ->fetchResult(false);

    $current_language = null;
    if ( !empty($languages) ) {
      foreach ($languages as $language) {
        if ( isset($values['form-lang-' . $language['lng_synonym']]) && 'true' == $values['form-lang-' . $language['lng_synonym']]) {
          $current_language = '_' . $language['lng_synonym'];

          break;
        }
      }
    }

    return $current_language;
  } // }}}

  /**
   * Метод возвращает список языков, а также какой язык является языком
   * по-умолчанию
   *
   * @return array
   */
  public function getLanguages() { // {{{
    $memcached_key = md5('languages');
    $memcached = Memcached::getInstance();
    $result = $memcached->get($memcached_key);

    if (false === $result) {
      // Получаю список языков и узнаю какой по умолчанию
      $languages = $this->db()->query()
        ->addSql('select lng_synonym, lng_default')
        ->addSql('from languages_tbl')
        ->addSql('where lng_enabled = true')
        ->addSql('order by lng_order')
        ->addTag('languages')
        ->fetchResult(false);

      $result = array();
      foreach($languages as $lang) {
        $result['languages'][] = $lang['lng_synonym'];

        if ('t' == $lang['lng_default']) {
          $result['default'] = $lang['lng_synonym'];
        }
      }


      $memcached->set($memcached_key, $result, 86400, array('languages')); // кешируем на сутки
    }

    // Получаем информацию о текущем языке
    $subdomain = Request::getInstance()->subdomain();
    $lang_exists = false;
    if ( !empty($result['languages']) ) {
      foreach ($result['languages'] as $lang) {
        if ($lang == $subdomain) {
          $lang_exists = true;

          break;
        }
      }
    }
    $current_language = $subdomain;
    if (!$lang_exists) {
      $current_language = $result['default'];
    }

    foreach($result['languages'] as $id => $lang) {
      if ($lang == $current_language) {
        unset($result['languages'][$id]);
      }
    }

    array_unshift($result['languages'], $current_language);

    return $result;
  } // }}}

  public function getCurrentCurrency() { // {{{
    //var_dump($_SERVER['GEOIP_COUNTRY_CODE']);
    $currency = Request::getInstance()->cookie('currency');

    if (null === $currency) {
      $countryParams = $this->db()->query()
        ->addSql('select rat_synonym')
        ->addSql('from countries_tbl')
        ->addSql('left join rates_tbl on rat_id_pk = cnt_rat_id_fk')
        ->addSql('where lower(cnt_code) = lower($1)')
        ->addParam($_SERVER['GEOIP_COUNTRY_CODE'])
        ->fetchRow(0, false);

      if (!empty($countryParams)) {
        $inTwoMonths = 60 * 60 * 24 * 60 + time();
        setcookie('currency', $countryParams['rat_synonym'], $inTwoMonths, '/', COOKIE_HOST);
        $_COOKIE['currency'] = $countryParams['rat_synonym'];
        $currency = $countryParams['rat_synonym'];
      }
    }

    $default = null;
    if (null === $currency) {
      $default = 'true';
    }

    $currencyParams = $this->db()->query()
      ->setFields('synonym, value as rate, short_name_#lang# as abbr')
      ->setTable('currencies_vw c')
      ->setWhere('synonym = $1', $currency)
      ->setWhere('c.default = true', $default)
      ->setLimit(1)
      ->addParam($currency, $currency)
      ->addTag('currencies')
//      ->printSql()
      ->fetchRow();

//var_dump($currencyParams);
    if ( empty($currencyParams) ) {
      return [
        'synonym' => 'none',
        'abbr' => '?',
        'rate' => 1,
      ];
    }

    return $currencyParams;
  } // }}}

  /**
   * undocumented function
   *
   * @return void
   */
  public function getPageFeatures() { // {{{
    $request = $this->getRequest();

    $route = $request->getCurrentUrl(true) . '/';
    $route_without_page = $request->getCurrentUrl(true, true) . '/';

    $data = $this->db()->query()
      ->addFields('h1_#lang# as h1, breadcrumb_#lang# as breadcrumb,')
      ->addFields('title_#lang# as title,')
      ->addFields('description_#lang# as description,')
      ->addFields('keywords_#lang# as keywords,')
      ->addFields('text_#lang# as text, metas,')
      ->addFields('seo_header_#lang# as seo_header,')
      ->addFields('seo_text_#lang# as seo_text')
      ->setTable('pages_features_vw')
      ->addWhere('(route = $1 and without_page = false) or')
      ->addWhere('(route = $2 and without_page = true)')
      ->addParam($route)
      ->addParam($route_without_page)
      ->addTag('page_features')
      ->fetchRow();

    if ( empty($data) ) {
      return $data;
    }

    $fields = [
      'h1', 'breadcrumb', 'title', 'description', 'keywords', 'text',
      'meats', 'seo_header', 'seo_text'
    ];

    foreach ($fields as $value) {
      if ( empty($data[$value]) ) {
        unset($data[$value]);
      }
    }

    return $data;
  } // }}}
}
