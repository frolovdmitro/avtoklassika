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
    \Uwin\Linguistics,
    \Uwin\Cacher\Memcached,
    \Uwin\Controller\Front,
    \Uwin\Validator,
    \Uwin\Registry,
    \Uwin\Mail,
    \Uwin\Xml,
    \Uwin\Auth,
    \Uwin\Exception\Route as RouteException,
    \Uwin\TemplaterBlitz as Templater;

/**
 * Модель
 *  - Главную страницу
 *  - Статическую страницу
 *  - Страницы ошибок
 *  - Страницу 404
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Cars extends Abstract_
{
  public $currency = null;

  public function __construct() {
    $this->ling = new Linguistics();
    $this->setVariable('module', 'car');

    $where = 'crr.default = true';
    if (isset($_COOKIE['currency'])) {
      $where = 'synonym = $1';
    }
    $query = $this->db()->query()->sql(
      "select currency_#lang# as currency, short_name_#lang# as short_name,"
      . "synonym, value, ratio "
      . "from currencies_vw as crr "
      . "where " . $where);
    if (isset($_COOKIE['currency'])) {
      $query->addParam($_COOKIE['currency']);
    }
    $this->currency = $query->fetchRow(0, 10);

    $this->setVariable('currency_abb', $this->currency['short_name']);

    if ( 'details' == $this->getRequest()->getParam('action') &&
         'cars' == $this->getRequest()->getParam('module')) {
      $meta_tags_variables = $this->db()->query()
        ->addSql('select id, car_name_#lang# as car_name, name_#lang# as name,')
        ->addSql('title_#lang# as title, car_synonym, is_last,')
        ->addSql('keywords_#lang# as keywords, description_#lang# as description')
        ->addSql('from autopart_info_vw')
        ->addSql('where id=$1')
        ->addParam( (int)$this->getrequest()->getparam('autopart') )
        ->fetchRow(0, false);

      if ( empty($meta_tags_variables) ) {
        throw new RouteException;
      }

      $last = '';
      if ($meta_tags_variables['is_last'] == 't') {
        $last = '_last';
      }
      $templater = new templater(null, front::getinstance()->getview());
      $templater->setglobals($this->getvariables());
      $templater->load( $this->getvariable('lng_templates' . $last . '_category_title') );
      $title = $templater->parse($meta_tags_variables);

      $templater = new templater(null, front::getinstance()->getview());
      $templater->setglobals($this->getvariables());
      $templater->load( $this->getvariable('lng_templates' . $last . '_category_description') );
      $description = $templater->parse($meta_tags_variables);

      $templater = new templater(null, front::getinstance()->getview());
      $templater->setglobals($this->getvariables());
      $templater->load( $this->getvariable('lng_templates' . $last . '_category_keywords') );
      $keywords = $templater->parse($meta_tags_variables);

      $meta_tags = [
        'title' => strip_tags(str_replace('<', ' <', $title)),
        'description' => strip_tags(str_replace('<', ' <', $description)),
        'keywords' => strip_tags(str_replace('<', ' <', $keywords)),
      ];

      $this->setvariables($meta_tags);
    } else
    if ( 'index' == $this->getrequest()->getparam('action') &&
         'cars' == $this->getRequest()->getParam('module')) {
      $meta_tags_variables = $this->db()->query()
        ->addsql('select name_#lang# as name, synonym, title_#lang# as title,')
        ->addsql('keywords_#lang# as keywords, description_#lang# as description')
        ->addsql('from car_info_vw')
        ->addsql('where synonym=$1')
        ->addparam( $this->getrequest()->getparam('car') )
        ->fetchrow(0, 60);
      if ( empty($meta_tags_variables) ) {
        throw new RouteException;
      }

      $templater = new templater(null, front::getinstance()->getview());
      $templater->setglobals($this->getvariables());
      $templater->load( $this->getvariable('lng_templates_car_title') );
      $title = $templater->parse($meta_tags_variables);

      $templater = new templater(null, front::getinstance()->getview());
      $templater->setglobals($this->getvariables());
      $templater->load( $this->getvariable('lng_templates_car_description') );
      $description = $templater->parse($meta_tags_variables);

      $templater = new templater(null, front::getinstance()->getview());
      $templater->setglobals($this->getvariables());
      $templater->load( $this->getvariable('lng_templates_car_keywords') );
      $keywords = $templater->parse($meta_tags_variables);

      $meta_tags = [
        'title' => strip_tags(str_replace('<', ' <', $title)),
        'description' => strip_tags(str_replace('<', ' <', $description)),
        'keywords' => strip_tags(str_replace('<', ' <', $keywords)),
      ];

      $this->setvariables($meta_tags);
    } else
    if ( null !== $this->getRequest()->getParam('detail') ) {
      $meta_tags_variables = $this->db()->query()
        ->addSql('select name_#lang# as name, detail_num as num, car_name_#lang# as car,')
        ->addSql('title_#lang# as title, keywords_#lang# as keywords,')
        ->addSql('description_#lang# as description, info_#lang# as info,')
        ->addSql('case when status = \'new\' then \'#1\'')
        ->addSql('when status = \'secondhand\' then \'#2\'')
        ->addSql('when status = \'replica\' then \'#3\'')
        ->addSql('when status = \'restaurare\' then \'#4\' end as status,')
        ->addSql('autopart_name_#lang# as autopart')
        ->addSql('from detail_info_vw')
        ->addSql('where id=$1')
        ->addParam( (int)$this->getRequest()->getParam('detail') )
        ->addReplacement( $this->getVariable('lng_new') )
        ->addReplacement( $this->getVariable('lng_secondhand') )
        ->addReplacement( $this->getVariable('lng_replica') )
        ->addReplacement( $this->getVariable('lng_restaurare') )
        ->fetchRow(0, 60);
      if ( empty($meta_tags_variables) ) {
        throw new RouteException;
      }

      $templater = new Templater(null, Front::getInstance()->getView());
      $templater->setGlobals($this->getVariables());
      $templater->load( $this->getVariable('lng_templates_title') );
      $title = $templater->parse($meta_tags_variables);

      $templater = new Templater(null, Front::getInstance()->getView());
      $templater->setGlobals($this->getVariables());
      $templater->load( $this->getVariable('lng_templates_description') );
      $description = $templater->parse($meta_tags_variables);

      $templater = new Templater(null, Front::getInstance()->getView());
      $templater->setGlobals($this->getVariables());
      $templater->load( $this->getVariable('lng_templates_keywords') );
      $keywords = $templater->parse($meta_tags_variables);

      $meta_tags = [
        'title' => strip_tags(str_replace('<', ' <', $title)),
        'description' => strip_tags(str_replace('<', ' <', $description)),
        'keywords' => strip_tags(str_replace('<', ' <', $keywords)),
      ];

      $this->setVariables($meta_tags);
    }
  }

  private function _getBreadcrumbs($id, $remove = true) {
    $breadcrumbs = $this->db()->query()
      ->addSql('WITH RECURSIVE autoparts(apt_id_pk, apt_parent_id_fk, apt_car_id_fk, name) AS (')
      ->addSql('SELECT apt_id_pk, apt_parent_id_fk, apt_car_id_fk, apt_name_#lang# FROM autoparts_tbl WHERE apt_id_pk = $1')
      ->addSql('UNION')
      ->addSql('SELECT t.apt_id_pk, t.apt_parent_id_fk, t.apt_car_id_fk, t.apt_name_#lang# as name')
      ->addSql('FROM autoparts_tbl t')
      ->addSql('JOIN autoparts rt ON rt.apt_parent_id_fk = t.apt_id_pk')
      ->addSql(')')
      ->addSql('SELECT apt_id_pk as id, replace(name, \'<br>\', \' \') as name, car_synonym FROM autoparts')
      ->addSql('join cars_tbl on car_id_pk = apt_car_id_fk')
      ->addParam($id)
      ->addTag('autoparts')
      ->fetchResult();

    $breadcrumbs = array_reverse($breadcrumbs);
    $full_breadcrumbs = $breadcrumbs;

    if ( isset($breadcrumbs[1]) && $remove ) {
      if ( count($breadcrumbs) === 3) {
        unset($breadcrumbs[1]);
      }
    }

    return [
      'shorted' => $breadcrumbs,
      'full' => $full_breadcrumbs,
    ];
  }

  public function getDocSynonym($value, $params = array()) {
    if (!empty($value)) {
      return $value;
    }

    $synonym = $this->ling->getWebTranslit($params['caption']);

    if (mb_strlen($synonym) >= 128) {
      $synonym = mb_substr($synonym, 0, 127);
    }

    // Делаем синоним уникальным
    $selectResult = $this->db()->query()
      ->addSql('select crd_id_pk from car_docs_tbl where lower(crd_synonym)=lower($1)')
      ->addParam($synonym)
      ->fetchRow(0, false);

    if (!empty($selectResult)) {
      $synonym .= date('-d-m-Y');
    }

    return $synonym;
  }
  /**
   * Функция формирует переменные главной странице, которые будут переданы
   * шаблонизатору
   *
   * @return Index
   */
  public function getIndex() {
    $synonym = $this->getRequest()->getParam('car');

    $data = $this->db()->query()
      ->addSql('select id, name_#lang# as name, synonym,')
      ->addSql('replace(name_#lang#, \'<br>\', \' \') as name_nobr,')
      ->addSql('seo_header_#lang# as seo_header, seo_text_#lang# as seo_text,')
      ->addSql('seo_image, robots')
      ->addSql('from car_info_vw')
      ->addSql('where synonym = $1 and #languages#')
      ->addParam($synonym)
      ->addTag('cars')
      ->fetchRow(0);

    $this->setVariables($data);

    return $this;
  }

  public function getPageAjax() {
    $full = $this->getRequest()->getParam('full');
    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
        $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'pageContent.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());

    $limit = 18;
    $page = (int)$this->getRequest()->get('page');
    $autopart = (int)$this->getRequest()->getParam('autopart');
    $offset = ($page-1) * $limit;
    // var_dump($this->getRequest()->getParams());

    $status_restaurare = $this->getRequest()->get('restaurare');
    $status_replica = $this->getRequest()->get('replica');
    $status_secondhand = $this->getRequest()->get('secondhand');
    $status_new = $this->getRequest()->get('new');
    $use_status = null;
    $restaurare= '!';
    $replica = '!';
    $secondhand = '!';
    $new = '!';
    if ('true' == $status_restaurare) {
      $use_status = true;
      $restaurare = 'restaurare';
    }
    if ('true' == $status_replica) {
      $use_status = true;
      $replica = 'replica';
    }
    if ('true' == $status_secondhand) {
      $use_status = true;
      $secondhand = 'secondhand';
    }
    if ('true' == $status_new) {
      $use_status = true;
      $new = 'new';
    }

    $query = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('($5*cost_unformat/$4)::FLOAT as cost_unformat,')
      ->addSql('($5*old_cost_unformat/$4)::FLOAT as old_cost_unformat,')
      ->addSql('$5*cost_unformat as cost_usd, $5*old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char(($5*cost_unformat/$4),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char(($5*old_cost_unformat/$4),'999 999 999.99')), '.', ','), ',00', '') as old_cost,")
      ->addSql('discount, new, car_synonym, autopart_id,')
      ->addSql('often_buy,')
      ->addSql('ceil((count(*) OVER())::FLOAT / $1) AS full_count, status')
      ->addSql('from details_autoparts_vw')
      ->addSql('where #languages#')
      ->addSql('and status = ANY(ARRAY[\'' . $restaurare . '\', \'' . $replica . '\', \'' . $secondhand . '\', \'' . $new . '\'])',
        $use_status);
    if (null === $full) {
      $query->addSql('and autopart_id in (')
        ->addSql('  WITH RECURSIVE subautoparts AS(')
        ->addSql('    SELECT * FROM autoparts_tbl WHERE apt_id_pk= $3')
        ->addSql('    UNION ALL')
        ->addSql('    SELECT apt.* FROM autoparts_tbl AS apt JOIN subautoparts AS sapt ON (apt.apt_parent_id_fk = sapt.apt_id_pk')
        ->addSql('  )')
        ->addSql(') SELECT apt_id_pk FROM subautoparts where apt_is_last = true)');
    } else {
      $query->addSql('and car_id = $3');
    }
    $query->addSql('order by autopart_name_#lang#, name_#lang#')
      ->addSql('limit $1 offset $2')
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->addParam($limit)
      ->addParam($offset)
      ->addParam($autopart)
      ->addParam($this->currency['ratio'])
      ->addParam($this->getUserDiscount());
    $details = $query->fetchResult();
    // var_dump($query->getSql());

    $this->setVariable('details', $details);
    return ['html' => $templater->parse($this->getVariables()), 'pages' => $details[0]['full_count']];
  }

  public function getPageContent($is_car = false) {
    $autopart = (int)$this->getRequest()->getParam('autopart');
    $car = $this->getRequest()->getParam('car');

    $query = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('($3*cost_unformat/$2)::FLOAT as cost_unformat,')
      ->addSql('(old_cost_unformat/$2)::FLOAT as old_cost_unformat,')
      ->addSql('$3*cost_unformat as cost_usd, old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char(($3*cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char((old_cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as old_cost,")
      ->addSql('discount, new, car_synonym, autopart_id,')
      ->addSql('often_buy, status')
      ->addSql('from details_autoparts_vw')
      ->addSql('where #languages#');
    if (!$is_car) {
      $query->addSql('and autopart_id in (')
        ->addSql('  WITH RECURSIVE subautoparts AS(')
        ->addSql('    SELECT * FROM autoparts_tbl WHERE apt_id_pk= $1')
        ->addSql('    UNION ALL')
        ->addSql('    SELECT apt.* FROM autoparts_tbl AS apt JOIN subautoparts AS sapt ON (apt.apt_parent_id_fk = sapt.apt_id_pk')
        ->addSql('  )')
        ->addSql(') SELECT apt_id_pk FROM subautoparts where apt_is_last = true)')
        ->addTag('cars')
        ->addTag('autoparts')
        ->addTag('details')
        ->addParam($autopart);
    } else {
      $query->addSql('and car_synonym = $1')
        ->addParam($car);
    }
    $query
      ->addSql('order by autopart_name_#lang#, name_#lang# limit 18')
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->addParam($this->currency['ratio'])
      ->addParam($this->getUserDiscount());
    $details = $query->fetchResult();

    return ['details' => $details];
  }

  public function getCarsCatalogue() {
    $cars = $this->db()->query()
      ->addSql('select name_#lang# as name, synonym, image, image_active,')
      ->addSql('case when position(\'<br>\' in name_#lang#) > 0 then true else null end nobr,')
      ->addSql('type')
      ->addSql('from cars_vw')
      ->addSql('where #languages#')
      ->addTag('cars')
      ->fetchResult();

    $types = $this->db()->query()
      ->addSql('select name_#lang# as name, synonym')
      ->addSql('from car_types_vw')
      ->addSql('where #languages#')
      ->addTag('car_types')
      ->fetchResult();

    return [
      'cars' => $cars,
      'types' => $types
    ];
  }

  public function getSlider($id) {
    $cars = $this->db()->query()
      ->addSql('select name_#lang# as name, synonym,')
      ->addSql('case when id = $1 then image_active else image end as image,')
      ->addSql('type, case when id = $1 then true else null end as current')
      ->addSql('from cars_vw')
      ->addSql('where #languages#')
      ->addParam($id)
      ->addTag('cars')
      ->fetchResult();

    return [
      'cars' => $cars
    ];
  }

  public function getAutoparts($id) {
    $autoparts = $this->db()->query()
      ->addSql('select id, name_#lang# as name, image, car_synonym,')
      ->addSql('case when position(\'<br>\' in name_#lang#) > 0 then true else null end as br')
      ->addSql('from main_autoparts_vw')
      ->addSql('where car_id = $1 and #languages#')
      ->addParam($id)
      ->addTag('cars')
      ->addTag('autoparts')
      ->fetchResult();

    return [
      'autoparts' => $autoparts
    ];
  }

  public function getPricePdf() {
    $car = $this->getRequest()->getParam('car');

    $data = $this->db()->query()
      ->addSql('select id, name_#lang# as name, synonym, price_title_#lang# as title,')
      ->addSql('price_keywords_#lang# as keywords,')
      ->addSql('price_description_#lang# as description, price_robots as robots')
      ->addSql('from car_info_vw')
      ->addSql('where synonym = $1 and #languages#')
      ->addParam($car)
      ->addTag('cars')
      ->fetchRow(0);

    $details = $this->db()->query()
      ->addSql('select id, num_detail, name_#lang# as name,')
      ->addSql('(cost/$2) as cost_unformat,')
      ->addSql('cost as cost_usd,')
      ->addSql('replace(replace(trim(to_char((cost/$2),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as cost,')
      ->addSql('autopart_#lang# as autopart')
      ->addSql('from details_pricelist_vw')
      ->addSql('where car_synonym = $1 and #languages# limit 600')
      ->addParam($car)
      ->addParam($this->currency['ratio'])
      ->fetchResult(false);

    $cache = Memcached::getInstance()->get(md5(serialize($details)));

    if (false !== $cache) {
      header('Content-type: application/pdf');
      header('Content-Disposition: inline; filename="price-' . $car . '.pdf"');
      header('Content-Transfer-Encoding: binary');
      echo $cache;

      return $this;
    }

    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('memory_limit', '256M');
    define("DOMPDF_UNICODE_ENABLED", true);
    define('DOMPDF_ENABLE_AUTOLOAD', false);
    $vendor_path = Registry::get('path')['vendor'];

    require_once $vendor_path . '/dompdf/dompdf/dompdf_config.inc.php';

    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
        $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'pricePdf.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $data['details'] = $details;
    $body = $templater->parse($data);

    $dompdf = new DOMPDF();
    $dompdf->load_html($body);
    $dompdf->render();

    $pdf = $dompdf->output();
    Memcached::getInstance()->set(md5(serialize($details)), $pdf, 864000);
    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="price-' . $car . '.pdf"');
    header('Content-Transfer-Encoding: binary');
    echo $pdf;

    return $this;
  }

  public function getPrice() {
    $car = $this->getRequest()->getParam('car');

    $data = $this->db()->query()
      ->addSql('select id, name_#lang# as name, synonym, price_title_#lang# as title,')
      ->addSql('price_keywords_#lang# as keywords,')
      ->addSql('price_description_#lang# as description, price_robots as robots')
      ->addSql('from car_info_vw')
      ->addSql('where synonym = $1 and #languages#')
      ->addParam($car)
      ->addTag('cars')
      ->fetchRow(0);

    $this->setVariables($data);

    $details = $this->db()->query()
      ->addSql('select id, num_detail, name_#lang# as name,')
      ->addSql('(cost/$2) as cost_unformat,')
      ->addSql('cost as cost_usd,')
      ->addSql('replace(replace(trim(to_char((cost/$2),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as cost,')
      ->addSql('autopart_#lang# as autopart')
      ->addSql('from details_pricelist_vw')
      ->addSql('where car_synonym = $1 and #languages#')
      ->addParam($car)
      ->addParam($this->currency['ratio'])
      ->fetchResult(false);

    $this->setVariable('details', $details);

    return $this;
  }

  public function getDocsList() {
    $car = $this->getRequest()->getParam('car');

    $data = $this->db()->query()
      ->addSql('select id, name_#lang# as name, synonym, doc_title_#lang# as title,')
      ->addSql('doc_keywords_#lang# as keywords, doc_description_#lang# as description,')
      ->addSql('doc_robots as robots')
      ->addSql('from car_info_vw')
      ->addSql('where synonym = $1 and #languages#')
      ->addParam($car)
      ->addTag('cars')
      ->fetchRow(0);

    $this->setVariables($data);

    $docs = $this->db()->query()
      ->addSql('select synonym, name_#lang# as name, car_synonym')
      ->addSql('from car_docs_vw')
      ->addSql('where car_id = $1 and #languages#')
      ->addSql('order by name_#lang#')
      ->addParam($data['id'])
      ->fetchResult(false);

    $this->setVariable('docs', $docs);

    return $this;
  }

  public function getDoc() {
    $doc = $this->getRequest()->getParam('doc');

    $data = $this->db()->query()
      ->addSql('select name_#lang# as name, title_#lang# as title,')
      ->addSql('description_#lang# as description, keywords_#lang# as keywords,')
      ->addSql('text_#lang# as text, car_id, car_name_#lang# as car_name,')
      ->addSql('car_synonym, robots')
      ->addSql('from car_doc_info_vw')
      ->addSql('join cars_tbl on car_id_pk = car_id')
      ->addSql('where synonym = $1 and #languages#')
      ->addParam($doc)
      ->fetchRow(false);

    if ( empty($data) ) {
      throw new RouteException;
    }

    $this->setVariables($data);

    return $this;
  }

  public function getAllDetails() {
    $car = $this->getRequest()->getParam('car');

    $data = $this->db()->query()
      ->addSql('select id as car_id, name_#lang# as car_name, synonym as car_synonym,')
      ->addSql('replace(name_#lang#, \'<br>\', \' \') as car_name_nobr,')
      ->addSql('seo_header_#lang# as seo_header, seo_text_#lang# as seo_text,')
      ->addSql('seo_image, robots')
      ->addSql('from car_info_vw')
      ->addSql('where synonym = $1 and #languages#')
      ->addParam($car)
      ->addTag('cars')
      ->fetchRow(0);

    if ( empty($data) ) {
      throw new RouteException;
    }

    $this->setVariables($data);

    $deals = $this->db()->query()
      ->addSql('select count(id) as cnt')
      ->addSql('from details_autoparts_vw')
      ->addSql('where car_synonym = $1 and coalesce(old_cost_unformat,0) > 0 and #languages#')
      ->addParam($car)
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->fetchRow(0);

    if ($deals['cnt'] > 0 || !empty($data['seo_text'])) {
      $this->setVariable('deals_exists', true);
    }
    if ($deals['cnt'] == 0 && !empty($data['seo_text'])) {
      $this->setVariable('only_seo', true);
    }

    return $this;
  }

  public function getNewDetails2() {
    $data = $this->db()->query()
      ->addSql('select id as car_id, name_#lang# as car_name, synonym as car_synonym,')
      ->addSql('replace(name_#lang#, \'<br>\', \' \') as car_name_nobr,')
      ->addSql('seo_header_#lang# as seo_header, seo_text_#lang# as seo_text,')
      ->addSql('seo_image, robots')
      ->addSql('from car_info_vw')
      ->addSql('where #languages#')
      ->addTag('cars')
      ->fetchRow(0);

    if ( empty($data) ) {
      throw new RouteException;
    }

    $details = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('(cost_unformat/$1)::FLOAT as cost_unformat,')
      ->addSql('(old_cost_unformat/$1)::FLOAT as old_cost_unformat,')
      ->addSql('cost_unformat as cost_usd, old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char((cost_unformat/$1),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char((old_cost_unformat/$1),'999 999 999.99')), '.', ','), ',00', '') as old_cost,")
      ->addSql('discount, new, car_synonym, autopart_id,')
      ->addSql('often_buy, status')
      ->addSql('from details_autoparts_vw')
      ->addSql('where #languages#')

      ->addSql('and id in (')
      ->addSql("SELECT dpt_id_pk")
      ->addSql("   FROM details_autoparts_tbl")
      ->addSql("   JOIN autoparts_tbl ON autoparts_tbl.apt_id_pk::integer = details_autoparts_tbl.dpt_apt_id_fk::integer")
      ->addSql("   JOIN cars_tbl ON cars_tbl.car_id_pk::integer = autoparts_tbl.apt_car_id_fk::integer")
      ->addSql("  WHERE details_autoparts_tbl.dpt_last_update::timestamp without time zone > (( SELECT max(mailers_tbl.mlr_datetime::timestamp without time zone) AS max")
      ->addSql("   FROM (select * from mailers_tbl where mlr_type = 'new_autoparts' order by mlr_datetime desc offset 1) as mailers_tbl")
      ->addSql("  WHERE mailers_tbl.mlr_type::text = 'new_autoparts'::text)) AND COALESCE(details_autoparts_tbl.dpt_presence::integer, 0) > 0 and coalesce(dpt_discount, 0) = 0")
      ->addSql(')')

      ->addSql('order by autopart_name_#lang#, name_#lang#')
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->addParam($this->currency['ratio'])
      ->fetchResult(false);

    $this->setVariable('details', $details);

    return $this;
  }


  public function getDetails() {
    $car = $this->getRequest()->getParam('car');
    $autopart = $this->getRequest()->getParam('autopart');

    $data = $this->db()->query()
      ->addSql('select id, car_id, car_name_#lang# as car_name, car_synonym,')
      ->addSql('robots, name_#lang# as name, case when is_main_category then null else schema end as schema,')
      ->addSql('replace(car_name_#lang#, \'<br>\', \' \') as car_name_nobr,')
      ->addSql('seo_header_#lang# as seo_header, seo_text_#lang# as seo_text')
      ->addSql('from autopart_info_vw')
      ->addSql('where id = $1 and #languages#')
      ->addParam($autopart)
      ->addTag('cars')
      ->addTag('autoparts')
      ->fetchRow(0);

    if ( empty($data) ) {
      throw new RouteException;
    }
    $path  = $this->getVariable('path_static_server') . $data['schema'];

    if ( !empty($data['schema']) && file_exists($path) ) {
      $size = getimagesize($path);
      $data['schema_width'] = $size[0];
      $data['schema_height'] = $size[1];

      $data['coordinates'] = $this->db()->query()
        ->addSql('select car_synonym, autopart_id, detail_id, num_detail, image, status,')
        ->addSql('name_#lang# as name, num, _top as _toppos, _left, presence,')
        ->addSql('($3*cost_unformat/$2)::FLOAT as cost_unformat,')
        ->addSql('$3*cost_unformat as cost_usd,')
        ->addSql("replace(replace(trim(to_char(($3*cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as cost")
        ->addSql('from coordinates_details_autopart_vw')
        ->addSql('where autopart_id = $1')
        ->addParam($data['id'])
        ->addParam($this->currency['ratio'])
        ->addParam($this->getUserDiscount())
        ->fetchResult(false);
    }
    $this->setVariables($data);

    $autoparts_crumbs = $this->_getBreadcrumbs($data['id']);
    $autoparts_full_crumbs = $autoparts_crumbs['full'];
    $autoparts_crumbs = $autoparts_crumbs['shorted'];

    $this->setVariable('autoparts_crumbs', $autoparts_crumbs);
    $this->setVariable('autoparts_full_crumbs', $autoparts_full_crumbs);

    $deals = $this->db()->query()
      ->addSql('select count(id) as cnt')
      ->addSql('from details_autoparts_vw')
      ->addSql('where car_synonym = $1 and coalesce($2*old_cost_unformat,0) > 0 and #languages#')
      ->addParam($car)
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->addParam($this->getUserDiscount())
      ->fetchRow(0);

    if ($deals['cnt'] > 0 || !empty($data['seo_text'])) {
      $this->setVariable('deals_exists', true);
    }
    if ($deals['cnt'] == 0 && !empty($data['seo_text'])) {
      $this->setVariable('only_seo', true);
    }

    return $this;
  }

  public function getDetail() {
    $detail = $this->getRequest()->getParam('detail');

    $mailerId = $this->getRequest()->get('mailer');
    if ( !empty($mailerId) ) {
      $this->db()->query()
        ->addSql('update mails_tbl set mls_link=$3 where')
        ->addSql('mls_mlr_id_fk=$1 and (md5(mls_email) = $2 or md5(mls_email || \'\r\') = $2)')
        ->addParam($mailerId)
        ->addParam( $this->getRequest()->get('user') )
        ->addParam( $this->getRequest()->getCurrentUrl() )
        ->execute();
    }

    $data = $this->db()->query()
      ->addSql('select id, name_#lang# as name, detail_num, image, sale,')
      ->addSql('cost_unformat*$3 as cost_usd,')
      ->addSql("replace(replace(trim(to_char(($3*cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("($3*cost_unformat/$2)::FLOAT as cost_unformat,")
      ->addSql('discount, new, car_synonym, autopart_id, case when apt_parent_id_fk is null then null else schema end as schema,')
      ->addSql('often_buy, status, image_medium, count_comments,')
      ->addSql('replace(car_name_#lang#, \'<br>\', \' \') as car_name_nobr,')
      ->addSql('info_#lang# as info, car_name_#lang# as car_name, car_id,')
      ->addSql('image_small, image_mini, robots, presence, dpt_youtube as youtube')
      ->addSql('from detail_info_vw')
      ->addSql('left join autoparts_tbl on apt_id_pk = autopart_id')
      ->addSql('join details_autoparts_tbl on dpt_id_pk = id')
      ->addSql('where id = $1 and #languages#')
      ->addParam($detail)
      ->addParam($this->currency['ratio'])
      ->addParam($this->getUserDiscount())
      ->fetchRow(0, false);

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_share') );
    $share_body = $templater->parse($data);
    $share_body = rawurlencode($share_body);
    $data['share_body'] = $share_body;
    $data['name_encode'] = rawurlencode('#' . $data['detail_num'] . ' - ' . $data['name']);

    $data['currency'] = $this->currency['synonym'];
    if ( empty($data) ) {
      throw new RouteException;
    }

    $data['comment_data'] = [
      'id' => $data['id'],
      'type' => 'detail',
    ];

    $this->setVariables($data);

    $images = $this->db()->query()
      ->addSql('select name_#lang# as name, image, image_small')
      ->addSql('from details_autoparts_photos_vw')
      ->addSql('where detail_id = $1 and #languages#')
      ->addParam($data['id'])
      ->fetchResult(false);
    $this->setVariable('images', $images);

    $colors = $this->db()->query()
      ->addSql('select id, name_#lang# as name, image, image_medium, diff_cost')
      ->addSql('from details_autoparts_colors_vw')
      ->addSql('where detail_id = $1 and #languages#')
      ->addParam($data['id'])
      ->fetchResult(false);
    $this->setVariable('colors', $colors);

    $sizes = $this->db()->query()
      ->addSql('select id, name_#lang# as name, image, image_medium, diff_cost')
      ->addSql('from details_autoparts_sizes_vw')
      ->addSql('where detail_id = $1 and #languages#')
      ->addParam($data['id'])
      ->fetchResult(false);
    $this->setVariable('sizes', $sizes);

    $coordinates = $this->db()->query()
      ->addSql('select car_synonym, autopart_id, detail_id, num_detail, image, status,')
      ->addSql('name_#lang# as name, num, _top as _toppos, _left, presence,')

      ->addSql('($4*cost_unformat/$3)::FLOAT as cost_unformat,')
      ->addSql('$4*cost_unformat as cost_usd,')
      ->addSql("replace(replace(trim(to_char(($4*cost_unformat/$3),'999 999 999.99')), '.', ','), ',00', '') as cost,")

      ->addSql('case when _left > 320 then true else null end right_hint,')
      ->addSql('case when _top < 30 then true else null end bottom_hint,')
      ->addSql('case when detail_id = $2 then true else null end as current')
      ->addSql('from coordinates_details_autopart_vw')
      ->addSql('where autopart_id = $1')
      ->addParam($data['autopart_id'])
      ->addParam($data['id'])
      ->addParam($this->currency['ratio'])
      ->addParam($this->getUserDiscount())
      ->fetchResult(false);

    $this->setVariable('coordinates', $coordinates);

    $autoparts_crumbs = $this->_getBreadcrumbs($data['autopart_id']);
    $this->setVariable('autoparts_crumbs', $autoparts_crumbs['shorted']);
    $this->setVariable('autoparts_full_crumbs', $autoparts_crumbs['full']);
    $this->setVariable('data', $data);

    return $this;
  }

  public function getTreeAutoparts($car_id) {
    $parent_id = $this->getRequest()->getParam('id');
    $type = $this->getRequest()->getParam('type');

    if ( empty($type) ) {
      $type = 'root';
    }

    $nodes = $this->db()->query()
      ->addSql('select depth, path, apt_id_pk as id, apt_name_#lang# as name,')
      ->addSql('  apt_car_id_fk as car_id, car_synonym, apt_parent_id_fk,')
      ->addSql('  case when apt_is_last = true then true else null end as is_last,')
      ->addSql('  count')
      ->addSql('from (')
      ->addSql('select sq.*, count_details_in_autopart_fn(apt_id_pk, apt_car_id_fk, \'#lang#\') as count')
      ->addSql('from (')
      ->addSql('WITH RECURSIVE tree AS (')
      ->addSql('  (select 1 as depth, array[apt_id_pk::int] as path,')
      ->addSql('    apt_id_pk, apt_name_#lang#, apt_car_id_fk, apt_is_last,')
      ->addSql('    apt_order, apt_parent_id_fk, car_synonym')
      ->addSql('  from autoparts_tbl apt')
      ->addSql('  join cars_tbl on car_id_pk = apt_car_id_fk')
      ->addSql('  where apt_car_id_fk = $1 and apt_parent_id_fk is null')
      ->addSql('  )')
      ->addSql('  union all  ')
      ->addSql('  select tree.depth + 1, tree.path || apt.apt_order::int,')
      ->addSql('    apt.apt_id_pk, apt.apt_name_#lang#, apt.apt_car_id_fk, apt.apt_is_last,')
      ->addSql('    apt.apt_order, apt.apt_parent_id_fk, car.car_synonym')
      ->addSql('  from tree')
      ->addSql('  join autoparts_tbl apt on apt.apt_parent_id_fk = tree.apt_id_pk')
      ->addSql('  join cars_tbl car on car_id_pk = apt.apt_car_id_fk')
      ->addSql('  where tree.depth < 3 ')
      ->addSql(')')
      ->addSql('select * from tree')
      ->addSql('order by path, apt_order')
      ->addSql(') sq')
      ->addSql(') sqq where count > 0')
      ->addParam($car_id)
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->fetchResult(true);

    return [
      $type => true,
      'car_synonym' => $nodes[0]['car_synonym'],
      'nodes' => $nodes,
    ];
  }

  public function getHotDeals() {
    $limit = 15;

    // var_dump($this->currency);
    $details = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('($3*cost_unformat/$2)::FLOAT as cost_unformat, discount,')
      ->addSql('new, car_synonym, autopart_id, status,')
      ->addSql('(old_cost_unformat/$2)::FLOAT as old_cost_unformat,')
      ->addSql('$3*cost_unformat as cost_usd, old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char(($3*cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char((old_cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as old_cost")
      ->addSql('from details_autoparts_vw')
      ->addSql('where coalesce(old_cost_unformat,0) > 0 and #languages#')
      ->addSql('order by random()')
      ->addSql('limit $1')
      ->addParam($limit)
      ->addParam($this->currency['ratio'])
      ->addParam($this->getUserDiscount())
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->fetchResult(true, 360);

    return [
      'details' => $details
    ];
  }

  public function getCarHotDeals($car_id) {
    $limit = 15;

    $details = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('($4*cost_unformat/$3)::FLOAT as cost_unformat, discount,')
      ->addSql('new, car_synonym, autopart_id, status,')
      ->addSql('(old_cost_unformat/$3)::FLOAT as old_cost_unformat,')
      ->addSql('cost_unformat as cost_usd, old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char(($4*cost_unformat/$3),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char((old_cost_unformat/$3),'999 999 999.99')), '.', ','), ',00', '') as old_cost")
      ->addSql('from details_autoparts_vw')
      ->addSql('where car_id = $1 and coalesce(old_cost_unformat,0) > 0 and #languages#')
      ->addSql('order by random()')
      ->addSql('limit $2')
      ->addParam($car_id)
      ->addParam($limit)
      ->addParam($this->currency['ratio'])
      ->addParam($this->getUserDiscount())
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->fetchResult(true, 360);

    return [
      'details' => $details
    ];
  }

  public function getRelatedDetails($detail_id) {
    $limit = 15;
    if ( is_array($detail_id) ) {
      $detail_id = $detail_id['id'];
      $this->setVariable('basket', true);
    }

    $details = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('discount, new, car_synonym, autopart_id,')
      ->addSql('($4*cost_unformat/$3)::FLOAT as cost_unformat,')
      ->addSql('(old_cost_unformat/$3)::FLOAT as old_cost_unformat,')
      ->addSql('$4*cost_unformat as cost_usd, old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char(($4*cost_unformat/$3),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char((old_cost_unformat/$3),'999 999 999.99')), '.', ','), ',00', '') as old_cost,")
      ->addSql('status')
      ->addSql('from related_details_vw')
      ->addSql('where')
      ->addSql('order_id in (select distinct odd_ord_id_fk from')
      ->addSql('orders_details_tbl where odd_dpt_id_fk = $1)')
      ->addSql('and id != $1 and #languages#')
      ->addSql('order by random()')
      ->addSql('limit $2')
      ->addParam($detail_id)
      ->addParam($limit)
      ->addParam($this->currency['ratio'])
      ->addParam($this->getUserDiscount())
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->fetchResult();

    return [
      'details' => $details
    ];
  }

  public function getColorSizeInfo($detail_id) {
    $color_id = (int)$this->getRequest()->get('color_id');
    $size_id = (int)$this->getRequest()->get('size_id');

    $data = $this->db()->query()
      ->addSql('select count,')
      ->addSql('(cost_unformat/$4)::FLOAT as cost_unformat,')
      ->addSql('cost_unformat as cost_usd,')
      ->addSql("replace(replace(trim(to_char((cost_unformat/$4),'999 999 999.99')), '.', ','), ',00', '') as cost")
      ->addSql('from color_size_cost_count_fn($1, $2, $3)')
      ->addParam($detail_id)
      ->addParam($color_id)
      ->addParam($size_id)
      ->addParam($this->currency['ratio'])
      ->fetchRow(0, false);
    $data['currency_abb'] = $this->currency['short_name'];

    return $data;
  }

  public function getSearchForm() {
    return [
      'query' => $this->getRequest()->get('query'),
    ];
  }

  public function getSearchPresenceDetail() {
    $data = $this->db()->query()
      ->addSql('select id, name_#lang# as nm, num, carsy,')
      ->addSql('car_name_#lang# as car,')
      ->addSql('aid, pr, im')
      ->addSql('from search_details_prefetch_vw')
      ->fetchResult(120);

    return $data;
  }

  public function getSearchPage() {
    $q = $this->getRequest()->get('query');
    $sp = new SphinxClient();

    $sp->setServer($this->getVariable('stg_sphinx_host'),
      $this->getVariable('stg_sphinx_port')
    );
    $sp->setLimits(0, 150, 150);
    $sp->SetArrayResult(true);
    $sp->setSortMode(SPH_SORT_EXTENDED, 'pr DESC, @relevance DESC');
    $results = $sp->Query($q, 'avtoclassika_' .
      $this->getVariable('current_language') . '_idx');

    $this->setVariable('query', $q);
    $this->setVariable('title', $this->getVariable('lng_search_placeholder'));
    if ( empty($results['matches']) ) {
      return $this;
    }

    $ids = '';
    foreach ($results['matches'] as $match){
      $ids .= $match['id'] . ',';
    }
    $ids = rtrim($ids, ',');


    $query = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('(cost_unformat/$1)::FLOAT as cost_unformat,')
      ->addSql('(old_cost_unformat/$1)::FLOAT as old_cost_unformat,')
      ->addSql('cost_unformat as cost_usd, old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char((cost_unformat/$1),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char((old_cost_unformat/$1),'999 999 999.99')), '.', ','), ',00', '') as old_cost,")
      ->addSql('discount, new, car_synonym, min(autopart_id) as autopart_id,')
      ->addSql('often_buy, status')
      ->addSql('from details_autoparts_vw')
      ->addSql('where #languages#')
      ->addSql('and id = ANY(\'{#1}\'::int[])')
      ->addReplacement($ids)
      ->addSql('group by id, name_#lang#, num_detail, image, sale,')
      ->addSql('cost_unformat,')
      ->addSql('old_cost_unformat,')
      ->addSql('discount, new, car_synonym,')
      ->addSql('often_buy, status')
      ->addSql('order by car_synonym, name_#lang#')
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->addParam($this->currency['ratio']);
    $details = $query->fetchResult();

    $this->setVariable('details', $details);

    return $this;
  }

  public function getSearchDetail($query) {
    $sp = new SphinxClient();
//    $sp->setServer($this->getVariable('stg_sphinx_host'),
//      $this->getVariable('stg_sphinx_port')
//    );
//var_dump($this->getVariable('stg_sphinx_host'));
    $sp->setServer('127.0.0.1', 9332 );

    $sp->SetArrayResult(true);
    $sp->setSortMode(SPH_SORT_EXTENDED, 'pr DESC, @relevance DESC');
    $results = $sp->Query($query, 'avtoclassika_' .
      $this->getVariable('current_language') . '_idx');

    if ( empty($results['matches']) ) {
      return [];
    }

    $data = [];
    foreach ($results['matches'] as $match){
      $match['attrs']['id'] = $match['id'];
      $data[] = $match['attrs'];
    }

    return $data;
  }

  public function sendNewDetails() {
    $_tmp_languages = $this->db()->query()
      ->addSql('select lng_synonym as synonym')
      ->addSql('from languages_tbl')
      ->addSql('where lng_enabled=true')
      ->fetchResult(false);

    $languages = array();
    foreach($_tmp_languages as $lang) {
      $languages[] = $lang['synonym'];
    }

    $users = $this->db()->query()
      ->addSql('select usr_id_pk as id, usr_name as user_name, ueml.usr_email as email, coalesce(lng_synonym, \'ru\') as language,')
      ->addSql('md5(\'avuwin\'||md5(ueml.usr_email)) as finish from')
      ->addSql('(select distinct usr_email from users_tbl  ')
      ->addSql('where usr_email is not null and position(\'@\' in usr_email) > 0')
      ->addSql('union')
      ->addSql('select distinct ord_user_email from orders_tbl')
      ->addSql('where ord_user_email is not null) ueml')
      ->addSql('left join users_tbl usr on usr.usr_email = ueml.usr_email')
      ->addSql('left join languages_tbl on lng_id_pk=usr_lng_id_fk')
      ->addSql('where ueml.usr_email != \'\'')
      ->fetchResult(false);

    $values = [];
    $new_users = [];
    foreach ($users as &$user) {
      $cars = $this->db()->query()
        ->addSql('select distinct car_synonym as car from orders_tbl')
        ->addSql('left join orders_details_tbl on ord_id_pk = odd_ord_id_fk')
        ->addSql('left join details_autoparts_tbl on dpt_id_pk = odd_dpt_id_fk')
        ->addSql('left join autoparts_tbl on apt_id_pk = dpt_apt_id_fk')
        ->addSql('left join cars_tbl on car_id_pk = apt_car_id_fk')
        ->addSql('where ord_user_email = $1 and apt_car_id_fk is not null')
        ->addParam($user['email'])
        ->fetchResult(false);

      if (count($cars) > 0) {
        $cars_str = "";
        foreach($cars as $car) {
          $cars_str .= "'" . $car['car'] . "',";
        }
        $cars_str = trim($cars_str, ',');

        $details = $this->db()->query()
          ->addSql('select *, \'#1\' as url_staticServer, replace(detail_image, \'-bg\', \'-sm\') as image from new_autoparts_mailer_vw')
          ->addSql('where car_synonym = any(array[#2])')
          ->addReplacement( $this->getVariable('url_staticServer') )
          ->addReplacement($cars_str)
          ->fetchResult(false);

        $user['details'] = $details;
        $new_users[] = $user;
      }
    }

    $path = Registry::get('path');
    $path = $path['userSettings'] . 'modules/cars';

    $settingsXml = new Xml;
    $settingsXml->setFileSettings($path . '/languages/ru.xml');
    $settingsXml->setPathNode('/mails/mailer');
    $settings_ru = $settingsXml->getValues();
    $values['subject_ru'] = $settings_ru['subject'];
    $values['text_ru'] = $settings_ru['body'];
    $values['text_ru'] = str_replace('&amp;', '&', $settings_ru['body']);

    $settingsXml->setFileSettings($path . '/languages/en.xml');
    $settingsXml->setPathNode('/mails/mailer');
    $settings_en = $settingsXml->getValues();
    $values['subject_en'] = $settings_en['subject'];
    $values['text_en'] = $settings_en['body'];
    $values['text_en'] = str_replace('&amp;', '&', $settings_en['body']);

    $settingsXml->setFileSettings($path . '/languages/de.xml');
    $settingsXml->setPathNode('/mails/mailer');
    $settings_de = $settingsXml->getValues();
    $values['subject_de'] = $settings_de['subject'];
    $values['text_de'] = $settings_de['body'];
    $values['text_de'] = str_replace('&amp;', '&', $settings_de['body']);

    $this->createModel('mailer')
       ->saveMailer('new_autoparts', 'user', $languages, $values, $new_users);

    return $this;
  }

  public function sendSaleDetails() {
    $_tmp_languages = $this->db()->query()
      ->addSql('select lng_synonym as synonym')
      ->addSql('from languages_tbl')
      ->addSql('where lng_enabled=true')
      ->fetchResult(false);

    $languages = array();
    foreach($_tmp_languages as $lang) {
      $languages[] = $lang['synonym'];
    }

    $users = $this->db()->query()
      ->addSql('select usr_id_pk as id, usr_name as user_name, ueml.usr_email as email, coalesce(lng_synonym, \'ru\') as language,')
      ->addSql('md5(\'avuwin\'||md5(ueml.usr_email)) as finish from')
      ->addSql('(select distinct usr_email from users_tbl  ')
      ->addSql('where usr_email is not null and position(\'@\' in usr_email) > 0')
      ->addSql('union')
      ->addSql('select distinct ord_user_email from orders_tbl')
      ->addSql('where ord_user_email is not null) ueml')
      ->addSql('left join users_tbl usr on usr.usr_email = ueml.usr_email')
      ->addSql('left join languages_tbl on lng_id_pk=usr_lng_id_fk')
      ->addSql('where ueml.usr_email != \'\'')
      ->fetchResult(false);

    $details = $this->db()->query()
      ->addSql('select *, \'#1\' as url_staticServer, replace(detail_image, \'-bg\', \'-sm\') as image from sale_autoparts_mailer_vw limit 20')
      ->addReplacement( $this->getVariable('url_staticServer') )
      ->fetchResult(false);

    $this->db()->query()
      ->addSql('update details_autoparts_tbl set dpt_last_update = now() + \'5 minutes\' where dpt_id_pk in (select detail_id from sale_autoparts_mailer_vw offset 20)')
      ->execute();

    $values = [];
    foreach ($users as &$user) {
      $user['details'] = $details;
    }

    $path = Registry::get('path');
    $path = $path['userSettings'] . 'modules/cars';

    $settingsXml = new Xml;
    $settingsXml->setFileSettings($path . '/languages/ru.xml');
    $settingsXml->setPathNode('/mails/sale_mailer');
    $settings_ru = $settingsXml->getValues();
    $values['subject_ru'] = $settings_ru['subject'];
    $values['text_ru'] = $settings_ru['body'];
    $values['text_ru'] = str_replace('&amp;', '&', $settings_ru['body']);

    $settingsXml->setFileSettings($path . '/languages/en.xml');
    $settingsXml->setPathNode('/mails/sale_mailer');
    $settings_en = $settingsXml->getValues();
    $values['subject_en'] = $settings_en['subject'];
    $values['text_en'] = $settings_en['body'];
    $values['text_en'] = str_replace('&amp;', '&', $settings_en['body']);

    $settingsXml->setFileSettings($path . '/languages/de.xml');
    $settingsXml->setPathNode('/mails/sale_mailer');
    $settings_de = $settingsXml->getValues();
    $values['subject_de'] = $settings_de['subject'];
    $values['text_de'] = $settings_de['body'];
    $values['text_de'] = str_replace('&amp;', '&', $settings_de['body']);

    $this->createModel('mailer')
       ->saveMailer('sale_autoparts', 'user', $languages, $values, $users);

    return $this;
  }

  public function notInMailer() {
    $id = $this->getRequest()->getParam('id');
    $data = $this->db()->query()
      ->addSql('update details_autoparts_tbl set dpt_last_update=')
      ->addSql('(select max(mlr_datetime) + \'-1 minutes\' from mailers_tbl where mlr_type = \'new_autoparts\')')
      ->addSql('where dpt_id_pk = $1')
      ->addParam($id)
      ->execute();

    return true;
  }

  public function mailOpenStat() {
    header('Content-Length: ' . filesize($this->getVariable('path_public') . '/img/logo.jpg'));
    header('Accept-Ranges: bytes');
    header_remove("X-Powered-By");
    header('Last-Modified: Fri, 11 Apr 2014 09:12:13 GMT');
    header('ETag:"5347b1ed-359e"');
    header_remove("Expires");


    $mailerId = $this->getRequest()->getParam('idMailer');
    $mdEmail  = $this->getRequest()->getParam('md5Email');

    $this->db()->query()
      ->addSql('update mails_tbl set mls_opened=true where')
      ->addSql('mls_mlr_id_fk=$1 and (md5(mls_email) = $2 or md5(mls_email || \'\r\') = $2)')
      ->addParam((int)$mailerId)
      ->addParam($mdEmail)
      ->execute();

    echo file_get_contents($this->getVariable('path_public') . '/img/logo.jpg');

    return $this;
  }

  public function getNewDetails() {
    $limit = 15;

    // var_dump($this->currency);
    $details = $this->db()->query()
      ->addSql('select id, name_#lang# as name, num_detail, image, sale,')
      ->addSql('(cost_unformat/$2)::FLOAT as cost_unformat, discount,')
      ->addSql('new, car_synonym, autopart_id, status,')
      ->addSql('(old_cost_unformat/$2)::FLOAT as old_cost_unformat,')
      ->addSql('cost_unformat as cost_usd, old_cost_unformat as old_cost_usd,')
      ->addSql("replace(replace(trim(to_char((cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql("replace(replace(trim(to_char((old_cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as old_cost")
      ->addSql('from details_autoparts_vw')
      ->addSql('where new= true and #languages#')
      ->addSql('order by random()')
      ->addSql('limit $1')
      ->addParam($limit)
      ->addParam($this->currency['ratio'])
      ->addTag('cars')
      ->addTag('autoparts')
      ->addTag('details')
      ->fetchResult(true, 360);

    return [
      'details' => $details
    ];
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function addRequestAutopart() {
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'add_request';

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();

    $errors = $validator->validate($form, $rules, $data, $lang_variables);

    if ( !empty($errors) ) {
      $errors['errors'] = true;
    }

    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    $id = $this->db()->query()
      ->addSql('insert into details_requests_tbl(drq_name, drq_email, drq_car_id_fk, ')
      ->addSql('drq_detail_name, drq_detail_num, drq_detail_state, drq_approximet_cost,')
      ->addSql('drq_text, drq_model, drq_year, drq_volume, drq_body_type, drq_fuel_type)values($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)')
      ->addParam($data['name'])
      ->addParam($data['email'])
      ->addParam($data['car'])
      ->addParam($data['detail_name'])
      ->addParam($data['detail_num'])
      ->addParam($data['state'])
      ->addParam($data['cost'])
      ->addParam($data['text'])
      ->addParam($data['model'])
      ->addParam((int)$data['year'])
      ->addParam($data['volume'])
      ->addParam($data['body_type'])
      ->addParam($data['fuel_type'])
      ->execute('drq_id_pk');

    // Получаем имя и email от которого будут оправляться письма
    if ( false === $mailerName = $this->getVariable('stg_mail_name') ) {
      $mailerName = null;
    }

    $car = $this->db()->query()
      ->addSql('select cfr_name_ru as name')
      ->addSql('from cars_for_requests_tbl where cfr_id_pk = $1')
      ->addParam($data['car'])
      ->fetchRow(0, false);

    $subject = 'Запрос на деталь';
    $email_text = "Отправлен запрос на запчасть.<br>";
    $email_text .= "Имя: " . $data['name'] . "<br>";
    $email_text .= "Email: " . $data['email'] . "<br>";
    $email_text .= "Автомобиль: " . $car['name'] . "<br>";
    $email_text .= "Модель: " . $data['model'] . "<br>";
    $email_text .= "Запчасть: " . $data['detail_name'] . "<br>";
    $email_text .= "Допустимая стоимость: " . $data['cost'] . "<br>";
    $email_text .= "<br>Более детальная информация в  <a href='http://" . SERVER_NAME . "/administrator/cars/requests/'>панели управления</a>";
    $mailerEmail = $this->getVariable('stg_mail_email');
    $settings = Registry::get('stg');
    $mail = new Mail($settings['mail']['smtp']);
    $mail->setFromEmail($mailerEmail, $mailerName)
      ->addEmail($this->getVariable('config_requests_email'))
      ->setSubject($subject)
      ->setText($email_text)
      ->send();

    if ( empty($data['uploaded-file']) ) {
      return true;
    }

    $dir = $this->getVariable('path_upload_images') . 'requests/' . $id . '/';
    if ( !file_exists($dir) ) {
      mkdir($dir, 0777, true);
    }
    $filename = time();

    $file = $data['uploaded-file'];
    $ext = strtolower(pathinfo($file)['extension']);
    $new_file = $dir . $filename . '.' . $ext;
    if (file_exists($file)) {
      rename($file, $new_file);
      $this->db()->query()
        ->addSql('update details_requests_tbl set drq_image = $2 where drq_id_pk = $1')
        ->addParam($id)
        ->addParam('/uploads/images/requests/' . $id . '/' . $filename . '.' . $ext)
        ->execute();

      $thm_adm_file = $dir . '.thm-' . $filename . '.' . $ext;
      $im = new imagick($new_file);
      $im->thumbnailImage(75, 75, true);
      $im->writeImage($thm_adm_file);
    }

    return true;
  }

  public function getRequestForm() {
    $cars = $this->db()->query()
      ->addSql('select cfr_id_pk as id, cfr_name_#lang# as name')
      ->addSql('from cars_for_requests_tbl where cfr_enabled = true')
      ->addSql('order by cfr_name_#lang#')
      ->fetchResult(false);

    return [
      'cars' => $cars,
    ];
  }

  public function uploadRequestImage() {
    $files = $this->getRequest()->files()['file'];
    if ( (1024 * 1024 * 1) < $files['size'] ) {
      return false;
    }

    $basename = pathinfo($files['tmp_name'])['basename'];
    $ext = strtolower(pathinfo($files['name'])['extension']);
    $dest = $this->getVariable('path_uploadDir') . 'tmp/' . $basename . '.'
      . $ext;
    move_uploaded_file($files['tmp_name'], $dest);

    return [
      'file' => $dest,
    ];
  }

  public function autobazarExcel() {
    $autoparts = $this->db()->query()
      ->addSql("select num_detail, crt_name_ru as car_type, replace(car_name_ru, '<br>', ' ') as car, cost_unformat as cost, name_ru as name, path,")
      ->addSql("case status when 'secondhand' then 'б/у'when 'replica' then 'новодел'when 'new' then 'оригинал'when 'restaurare' then 'реставрация' end || '|в наличии|доставка по Украине' as options")
      ->addSql("from details_autoparts_vw dap")
      ->addSql("join cars_tbl cr on cr.car_synonym = dap.car_synonym")
      ->addSql("join car_types_tbl on crt_id_pk = car_crt_id_fk")
      ->addSql("left join (")
      ->addSql("WITH RECURSIVE autoparts(id, parent_id, depth, path) AS (")
      ->addSql("SELECT apt_id_pk, apt_parent_id_fk, 1::INT AS depth, apt_name_ru::TEXT AS path FROM autoparts_tbl AS tn WHERE apt_parent_id_fk is null")
      ->addSql("UNION ALL")
      ->addSql("SELECT apt_id_pk, apt_parent_id_fk, p.depth + 1 AS depth, (p.path || '->' || c.apt_name_ru::TEXT) FROM autoparts AS p, autoparts_tbl AS c WHERE c.apt_parent_id_fk = p.id")
      ->addSql(")")
      ->addSql("SELECT id, trim(path) as path FROM autoparts AS n")
      ->addSql(") pth on pth.id = autopart_id")
      ->addSql("where cr.car_synonym != 'Motorcycles' and cr.car_synonym != 'Trucks' and cr.car_synonym != 'literature' and cr.car_synonym != 'Accessories'")
      ->fetchResult(false);

    $path_library = $this->getVariable('path_library');
    $path_root = $this->getVariable('path_root');

    $excelFile = $path_root . 'scripts/autobazar-template.xlsx';

    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($excelFile);
    $worksheet = $objPHPExcel->setActiveSheetIndex(0);

    // $countDetails = count($orderDetails);
    // if ( 1 < $countDetails ) {
    //   $worksheet->insertNewRowBefore(23, $countDetails - 1);
    // }
    //
    // $worksheet->setCellValue('I21', $orderInfo['currency']);
    // $worksheet->setCellValue('J21', $orderInfo['currency']);
    // $worksheet->setCellValue('J' . (22 + $countDetails), $orderInfo['sum_subtotal']);
    // $worksheet->setCellValue('J' . (25 + $countDetails), $orderInfo['discount']);
    // $worksheet->setCellValue('J' . (28 + $countDetails), $orderInfo['sum_delivery']);
    // $worksheet->setCellValue('J' . (31 + $countDetails), $orderInfo['sum_total']);
    // $worksheet->setCellValue('C' . (26 + $countDetails), $orderInfo['payment_name_ru']);
    // $worksheet->setCellValue('B' . (26 + $countDetails), $orderInfo['delivery_name_ru']);
    //
    $i = 2;
    foreach ($autoparts as $row) {
      $root = '';
      $subroot = '';
      if ( !empty($row['path']) ) {
        $path = explode('->', $row['path']);
        if (isset($path[0])) {
          $root = $path[0];
        }
        if (isset($path[1])) {
          $subroot = $path[1];
        }
      }
      $worksheet->setCellValue('A' . $i, $row['car_type']);
      $worksheet->setCellValue('B' . $i, $row['car']);
      $worksheet->setCellValue('E' . $i, $row['cost']);
      $worksheet->setCellValue('F' . $i, '$');
      $worksheet->setCellValue('G' . $i, 'Легковые');
      $worksheet->setCellValue('H' . $i, $root);
      $worksheet->setCellValue('I' . $i, $subroot);
      $worksheet->setCellValue('J' . $i, $row['options']);
      $worksheet->setCellValue('K' . $i, $row['name']);

      $i++;
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="autobazar.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter->save('php://output');

    return true;
  }


    /**
     * undocumented function
     *
     * @return void
     */
    public function priceChange() {
      $data = $this->getRequest()->post();

      $data = $this->db()->query()
        ->addSql('update details_autoparts_tbl set dpt_cost=dpt_cost + (dpt_cost*$1)')
        ->addSql('where dpt_status = $2')
        ->addParam(str_replace(',', '.', (float)((float)($data['value']) / 100)))
        ->addParam($data['status'])
        ->execute();

      return $this;
    }

    /**
     * undocumented function
     *
     * @return void
     */
    public function addByNumber() {
      $autopart_id = $this->getRequest()->get('parent_id');
      $num = $this->getRequest()->post('value');
      $status = $this->getRequest()->post('status');
      $cost = $this->getRequest()->post('cost');
      $presence = $this->getRequest()->post('presence');

      $id = $this->db()->query()
        ->addSql('select dpt_id_pk')
        ->addSql('from details_autoparts_tbl')
        ->addSql('where trim(lower(dpt_num_detail))=trim(lower($1))')
        ->addSql('limit 1')
        ->addParam($num)
        ->fetchRow();

      if (empty($id)) {
        return $this;
      }
      $id = $id['dpt_id_pk'];
      $new_id = $this->db()->query()
        ->addSql('insert into details_autoparts_tbl')
        ->addSql('(dpt_apt_id_fk,')
        ->addSql('dpt_num_detail, dpt_name_ru, dpt_name_en, dpt_name_de,')
        ->addSql('dpt_title_ru, dpt_title_en, dpt_title_de,')
        ->addSql('dpt_description_ru, dpt_description_en, dpt_description_de,')
        ->addSql('dpt_keywords_ru, dpt_keywords_en, dpt_keywords_de,')
        ->addSql('dpt_image, dpt_presence, dpt_cost, dpt_weight, dpt_status,')
        ->addSql('dpt_often_buy, dpt_sale, dpt_languages, dpt_num_order, dpt_image_txt,')
        ->addSql('dpt_top, dpt_discount, dpt_info_ru, dpt_info_en, dpt_info_de, dpt_robots)')
        ->addSql('(select $2, dpt_num_detail, dpt_name_ru, dpt_name_en, dpt_name_de,')
        ->addSql('dpt_title_ru, dpt_title_en, dpt_title_de,')
        ->addSql('dpt_description_ru, dpt_description_en, dpt_description_de,')
        ->addSql('dpt_keywords_ru, dpt_keywords_en, dpt_keywords_de,')
        ->addSql('dpt_image, coalesce($5, dpt_presence), coalesce($4, dpt_cost), dpt_weight, coalesce($3, dpt_status),')
        ->addSql('dpt_often_buy, dpt_sale, dpt_languages, dpt_num_order, dpt_image_txt,')
        ->addSql('dpt_top, dpt_discount, dpt_info_ru, dpt_info_en, dpt_info_de, dpt_robots')
        ->addSql('from details_autoparts_tbl')
        ->addSql('where dpt_id_pk = $1)')
        ->addParam($id)
        ->addParam($autopart_id)
        ->addParam($status)
        ->addParam($cost)
        ->addParam($presence)
        ->execute('dpt_id_pk');

        $this->db()->query()
          ->addSql('insert into details_autoparts_photos_tbl')
          ->addSql('(dap_dpt_id_fk, dap_name_ru, dap_name_en, dap_name_de, dap_image, dap_languages, dap_order, dap_enabled)')
          ->addSql('(select $2, dap_name_ru, dap_name_en, dap_name_de, dap_image, dap_languages, dap_order, dap_enabled')
          ->addSql('from details_autoparts_photos_tbl')
          ->addSql('where dap_dpt_id_fk = $1)')
          ->addParam($id)
          ->addParam($new_id)
          ->execute();

      return $this;
    }

    public function updateAllAutopartNum($value, $params = array()) {
      if (empty($value)) {
        return $value;
      }

      $langs = [];

      if ($this->getRequest()->post('form-lang-ru') == true) {
        $langs[] = 'ru';
      }
      if ($this->getRequest()->post('form-lang-en') == true) {
        $langs[] = 'en';
      }
      if ($this->getRequest()->post('form-lang-de') == true) {
        $langs[] = 'de';
      }

      $lang = '|' . implode($langs, '|') . '|';

      $this->db()->query()
        ->addSql('update details_autoparts_tbl set')
        ->addSql('dpt_name_ru = $3, dpt_name_en = $4, dpt_name_de = $5,')
        ->addSql('dpt_title_ru = $6, dpt_title_en = $7, dpt_title_de = $8,')
        ->addSql('dpt_description_ru = $9, dpt_description_en = $10, dpt_description_de = $11,')
        ->addSql('dpt_keywords_ru = $12, dpt_keywords_en = $13, dpt_keywords_de = $14,')
        ->addSql('dpt_image = $15, dpt_languages = $16, dpt_image_txt = $17,')
        ->addSql('dpt_info_ru = $18, dpt_info_en = $19, dpt_info_de = $20,')
        ->addSql('dpt_robots = $21')
        ->addSql('where trim(lower(dpt_num_detail))=trim(lower($1)) and dpt_status = $2')
        ->addParam($value)
        ->addParam($params['dpt_status'])
        ->addParam($params['dpt_name_ru'])
        ->addParam($params['dpt_name_en'])
        ->addParam($params['dpt_name_de'])
        ->addParam($params['dpt_title_ru'])
        ->addParam($params['dpt_title_en'])
        ->addParam($params['dpt_title_de'])
        ->addParam($params['dpt_description_ru'])
        ->addParam($params['dpt_description_en'])
        ->addParam($params['dpt_description_de'])
        ->addParam($params['dpt_keywords_ru'])
        ->addParam($params['dpt_keywords_en'])
        ->addParam($params['dpt_keywords_de'])
        ->addParam($params['dpt_image'])
        ->addParam($lang)
        ->addParam($params['dpt_image_txt'])
        ->addParam($params['dpt_info_ru'])
        ->addParam($params['dpt_info_en'])
        ->addParam($params['dpt_info_de'])
        ->addParam($params['dpt_robots'])
        ->execute();

      return $value;
    }

    /*
     * Get Autorized User discount from users tbl
     * */
    public function getUserDiscount() {
      $auth = Auth::getInstance();
      if ( $auth->hasIdentity() ) {
        $email = $auth->getStorage()->identity;
        $dis = $this->db()->query()
          ->addSql('select usr_discount as discount')
          ->addSql('from users_tbl')
          ->addSql('where usr_email=$1')
          ->addParam($email)
          ->fetchRow(0, false);
        $discount = abs(1 - $dis['discount']/100);

        return $discount;
      }


      return 1;
    }
}
