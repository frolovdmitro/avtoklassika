<?php
/**
 * Uwin CMS
 *
 * Файл содержащий модель модуля по умолчанию, который обрабатывает:
 *  - Главную страницу
 *  - Статическую страницу
 *  - Страницы ошибок
 *  - Страницу 404
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_,
    \Uwin\Registry,
    \Uwin\Controller\Request;

/**
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Directories extends Abstract_
{
  /**
   * @return Directories
   */
  public function getLanguages() {
    $request = Request::getInstance();
    $languages = Registry::get('languages');

    if ( empty($languages) ) {
      return $this;
    }

    $result = array();
    $i = 1;
    foreach ($languages as $lang) {
      $_lang = array();
      $_lang['synonym'] = $lang;
      $_lang['www'] = $request->withWww();
      $_lang['host'] = $this->getRequest()->serverName();

      if ( $lang == Registry::get('current_language') ) {
        $_lang['current'] = true;
      }

      if ($i <= 2) {
        $result['items'][] = $_lang;
      } else {
        $result['more_items'][] = $_lang;
      }

      $i++;
    }

    $this->setTags( __FUNCTION__, array('languages') )
      ->saveCache(__FUNCTION__, $result);

    return $result;
  }

  public function getPhones() {
    $phones = $this->db()->query()->sql(
      "select replace(phn_phones, E'\n', '||') as phones "
      . "from phones_tbl "
      . "left join languages_tbl on phn_lng_id_fk = lng_id_pk "
      . "where phn_enabled = true and lng_synonym = $1 "
      . "limit 1")
      ->addParam( Registry::get('current_language') )
      ->addTag('phones')
      ->fetchField();

    if ( empty($phones) ) {
      return [];
    }

    $result = [];
    $replacement = [' ', '(', ')', '-'];
    foreach (explode('||', $phones) as $phone){
      $result[] = [
        'phone' => $phone,
        'phone_unformat' => str_replace($replacement, '', $phone),
      ];
    }

    return ['phones' => $result];
  }

  public function getPhonesSimple() {
    $phones = $this->db()->query()->sql(
      "select replace(phn_phones, E'\n', '||') as phones "
      . "from phones_tbl "
      . "left join languages_tbl on phn_lng_id_fk = lng_id_pk "
      . "where phn_enabled = true and lng_synonym = $1 "
      . "limit 1")
      ->addParam( Registry::get('current_language') )
      ->addTag('phones')
      ->fetchField();

    if ( empty($phones) ) {
      return [];
    }

    $result = [];
    $replacement = [' ', '(', ')', '-'];
    foreach (explode('||', $phones) as $phone){
      $result[] = [
        'phone' => $phone,
        'phone_unformat' => str_replace($replacement, '', $phone),
      ];
    }

    return ['phones' => $result];
  }

  public function getLaborHours() {
    $labor_hours = $this->db()->query()->sql(
      "select replace(replace(phn_labor_hours, '((', "
      . "'<strong class=\"labor-hours-bar__hours\">'), '))', '</strong>') "
      . "as labor_hours "
      . "from phones_tbl "
      . "left join languages_tbl on phn_lng_id_fk = lng_id_pk "
      . "where phn_enabled = true and lng_synonym = $1 "
      . "limit 1")
      ->addParam( Registry::get('current_language') )
      ->addTag('phones')
      ->fetchField();

    return ['labor_hours' => $labor_hours];
  }

  public function getBanner($type) {
    $limit = 1;
    if ('adverts' == $type) {
      $limit = 4;
    }

    if ('advert' == $type) {
      $limit = 1;
      $type = 'adverts';
    }

    if ('car' == $type) {
      $limit = 2;
    }

    if ('autopart' == $type) {
      $limit = 1;
      $type = 'car';
    }

    $banner = $this->db()->query()
      ->addSql('select file_#lang# as file, url, target_blank, autopart_request')
      ->addSql('from banners_vw')
      ->addSql('where type=$1 and #languages#')
      ->addSql('ORDER BY RANDOM()')
      ->addSql('LIMIT ' . $limit)
      ->addParam($type)
      ->addTag('banners')
      ->fetchResult();

    return [$type => $banner];
  }

  private function _getCurrency() {
    $where = 'cr.default = true';
    if (isset($_COOKIE['currency'])) {
      $where = 'synonym = $1';
    }
    $query = $this->db()->query()->sql(
      "select currency_#lang# as currency, short_name_#lang# as short_name,"
      . "synonym, value, ratio "
      . "from currencies_vw cr "
      . "where " . $where)
      ->addTag('currencies');
    if (isset($_COOKIE['currency'])) {
      $query->addParam($_COOKIE['currency']);
    }
    $currency = $query->fetchRow(0);

    return $currency;
  }

  public function getDeliveryMethods($params = null) {
    if ( !isset($params['type']) ) {
      $params['type'] = '';
    }
    if ( !isset($params['country']) ) {
      $params['country'] = false;
    }
    if ( !isset($params['weight']) ) {
      $params['weight'] =false;
    }

    $currency = $this->_getCurrency();
    $query = $this->db()->query()
      ->addSql('select *,')
      ->addSql('replace(replace(trim(to_char((cost_usd_delivery/$3),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as cost_delivery,')
      ->addSql('coalesce(cost_usd_delivery/$3,0) as cost_delivery_unformat')
      ->addSql('from (')
      ->addSql('select id, type, name_#lang# as name,')
      ->addSql('description_#lang# as description,')
      ->addSql("'" . $params['type'] . '\' as basket,')
      ->addSql('case when char_length(description_#lang#) > 50 then true else null end as multiline,')

      ->addSql('case when type = \'ukrposhta\' then ')
      ->addSql('(select coalesce(cnt_onetime_tariff,0)')
      ->addSql('+ coalesce(cnt_kg_tariff,0)*$2 from countries_tbl')
      ->addSql('where cnt_synonym = $1)')
      ->addSql('when type = \'conductor\' then ')
      ->addSql('(select coalesce(cnt_onetime_tariff,0)')
      ->addSql('+ coalesce(cnt_kg_tariff,0)*$2 from countries_tbl')
      ->addSql('where cnt_synonym = $1)')
      ->addSql('when type = \'ups\' then ')
      ->addSql('(select (coalesce(cnt_onetime_tariff,0)')
      ->addSql('+ coalesce(cnt_kg_tariff,0)*$2)*5 from countries_tbl')
      ->addSql('where cnt_synonym = $1)')
      ->addSql('else cost end as cost_usd_delivery')

      ->addSql('from delivery_methods_vw')
      ->addSql('where #languages#')
      ->addParam($params['country'])
      ->addParam($params['weight'])
      ->addParam($currency['ratio'])
      ->addTag('deliveries');
    if ( isset($params['city']) ) {
      $query->addSql('and (select filtered_payments_delivery(filter, $1, $4, $2)) > 0')
        ->addParam($params['city']);
    }
    $query->addSql(') sq');
    $methods = $query->fetchResult();

    return [
      'methods' => $methods,
      'basket' => $params['type'],
    ];
  }

  public function getPaymentMethods($params = null) {
    if ( !isset($params['type']) ) {
      $params['type'] = '';
    }

    $query = $this->db()->query()
      ->addSql('select id, type, name_#lang# as name,')
      ->addSql('description_#lang# as description,')
      ->addSql("'" . $params['type'] . '\' as basket,')
      ->addSql('case when char_length(description_#lang#) > 50 then true else null end as multiline')
      ->addSql('from payment_methods_vw')
      ->addSql('where #languages#')
      ->addTag('payments');
    if ( isset($params['city']) ) {
      $query->addSql('and (select filtered_payments_delivery(filter, $1, $2, $3)) > 0')
        ->addParam($params['country'])
        ->addParam($params['city'])
        ->addParam($params['weight']);
    }
    $methods = $query->fetchResult();
    $length = count($methods);
    for($i=0;$i<$length;$i++){
        $methods[$i]['ord_dl'] = 0;
        if(isset($params['ord_num'])){
            $methods[$i]['ord_dl'] = ($params['ord_num']==10009009001 && ($methods[$i]['type'] == "cash" || $methods[$i]['type'] == "cod" || $methods[$i]['type'] == "courier"))?1:0;
        }
    }

    return [
      'methods' => $methods,
      'basket' => $params['type'],
    ];
  }

  public function saveNode() {
    $id = $this->getRequest()->getParam('id');
    $variables = $this->getRequest()->post();

    // var_dump($variables);
    $exists = $this->db()->query()
      ->addSql('select smp_id_pk from sitemap_tbl where smp_synonym = $1')
      ->addParam($id)
      ->fetchField(0);

    $enabled = 'true';
    if ( !isset($variables['enabled']) ) {
      $enabled = 'false';
    }
    if ( empty($exists) ) {
      $this->db()->query()
        ->addSql('insert into sitemap_tbl')
        ->addSql('(smp_synonym, smp_lastmod, smp_changefreq, smp_priority, smp_enabled)')
        ->addSql('values')
        ->addSql('($1, $2, $3, $4, $5);')
        ->addParam($id)
        ->addParam($variables['lastmod'])
        ->addParam($variables['changefreq'])
        ->addParam($variables['priority'])
        ->addParam($enabled)
        ->execute();
    } else {
      $this->db()->query()
        ->addSql('update sitemap_tbl')
        ->addSql('set smp_lastmod=$2, smp_changefreq=$3, smp_priority=$4, smp_enabled=$5')
        ->addSql('where smp_synonym = $1')
        ->addParam($id)
        ->addParam($variables['lastmod'])
        ->addParam($variables['changefreq'])
        ->addParam($variables['priority'])
        ->addParam($enabled)
        ->execute();
    }

    return $this;
  }

  public function getCurrencies() {
    $currencies = $this->db()->query()->sql(
      "select currency_#lang# as currency, short_name_#lang# as short_name,"
      . "synonym, value, ratio, id "
      . "from currencies_vw")
      ->addTag('currencies')
      ->fetchResult();

    $current = [];
    if ( isset($_COOKIE['currency']) ) {
      foreach ($currencies as $key => &$currency){
        if ($currency['synonym'] === $_COOKIE['currency']) {
          $current = $currencies[$key];
          unset($currencies[$key]);
        }
      }
      array_unshift($currencies, $current);
    }

    return [
      'items' => $currencies,
    ];
  }
}
