<?php
/**
 * Uwin CMS
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_,
    \Uwin\Linguistics,
    \Uwin\Controller\Front,
    \Uwin\Auth,
    \Uwin\Validator,
    \Uwin\TemplaterBlitz as Templater,
    \Uwin\Exception\Route as RouteException;

/**
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 */
class Adverts extends Abstract_
{
  public $currency = null;
  public $metaTags;

  public function __construct() {
    $this->ling = new Linguistics();
    $this->setVariable('module', 'ads');

    $advertid = $this->getRequest()->getParam('id');
    $adverttype = $this->getRequest()->getParam('type');
    $this->setVariable('ID', $advertid);
    $this->setVariable('type', $adverttype);

    $title = $this->getVariable('lng_index_title');
    $description = $this->getVariable('lng_index_description');
    $keywords = $this->getVariable('lng_index_keywords');

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
    $this->currency = $query
      ->addTag('currencies')
      ->fetchRow(0, false);
    $this->setVariable('currency_abb', $this->currency['short_name']);

    if ( null !== $this->getRequest()->getParam('id') ) {
      $meta_tags_variables = $this->db()->query()
        ->addSql('select name, cost, date, city_#lang# as city,')
        ->addSql('country_#lang# as country, title, keywords, description,')
        ->addSql('case when type = \'sell\' then \'#1\'')
        ->addSql('when type = \'buy\' then \'#2\' end as type,')
        ->addSql('case when category = \'autopart\' then \'#3\'')
        ->addSql('when type = \'car\' then \'#4\' end as category,')
        ->addSql('case when (cost::VARCHAR is null or cost::VARCHAR = \'\') then \'#5\' else cost::VARCHAR end as cost,')
        ->addSql('short_name_#lang# as rate_name')
        ->addSql('from adverts2_vw')
        ->addSql('where id=$1')
        ->addParam( (int)$this->getRequest()->getParam('id') )
        ->addReplacement( $this->getVariable('lng_i_sell') )
        ->addReplacement( $this->getVariable('lng_i_buy') )
        ->addReplacement( $this->getVariable('lng_autopart') )
        ->addReplacement( $this->getVariable('lng_car') )
        ->addReplacement( $this->getVariable('lng_contract_price') )
        ->addTag('adverts')
        ->fetchRow(0, false);

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
    }

    $meta_tags = [
      'title' => trim($title),
      'description' => trim($description),
      'keywords' => trim($keywords),
    ];

    $this->metaTags = $meta_tags;
    $this->setVariables($meta_tags);
  }

  private function _uploadImage($id, $name, $photos = [], $file_input = null, $i = 1) {
    $ling = new Linguistics;
    $dir = $this->getVariable('path_upload_images') . 'ads/' . $id . '/';
    if ( !file_exists($dir) ) {
      mkdir($dir, 0777, true);
    }
    $filename = $ling->getWebTranslit($name);

    if ($file_input !== null) {
      $ext = strtolower(pathinfo($file_input['name'])['extension']);
      $new_file = $dir . $filename . '-' . $i . '-bg.' . $ext;
      move_uploaded_file($file_input['tmp_name'], $new_file);
      $photos[] = $new_file;
    }

    foreach ($photos as $file){
      $ext = strtolower(pathinfo($file)['extension']);
      if ($file_input === null) {
        $new_file = $dir . $filename . '-' . $i . '-bg.' . $ext;
        if (file_exists($file)) {
          rename($file, $new_file);
        }
      }

      $medium_file = $dir . $filename . '-' . $i . '-md.' . $ext;
      $im = new imagick($new_file);
      $im->cropThumbnailImage(300, 230);
      $im->writeImage($medium_file);

      $small_file = $dir . $filename . '-' . $i . '-sm.' . $ext;
      $im = new imagick($new_file);
      $im->cropThumbnailImage(222, 152);
      $im->writeImage($small_file);

      $mini_file = $dir . $filename . '-' . $i . '-mini.' . $ext;
      $im = new imagick($new_file);
      $im->cropThumbnailImage(193, 132);
      $im->writeImage($mini_file);

      $micro_file = $dir . $filename . '-' . $i . '-micro.' . $ext;
      $im = new imagick($new_file);
      $im->cropThumbnailImage(180, 123);
      $im->writeImage($micro_file);

      $thm_file = $dir . $filename . '-' . $i . '-thm.' . $ext;
      $im = new imagick($new_file);
      $im->thumbnailImage(60, 60, true);
      $im->writeImage($thm_file);

      $thm_adm_file = $dir . '.thm-' . $filename . '-' . $i . '-bg.' . $ext;
      $im = new imagick($new_file);
      $im->thumbnailImage(75, 75, true);
      $im->writeImage($thm_adm_file);

      $this->db()->query()
        ->addSql('insert into adverts_attachments_tbl(ada_adv_id_fk, ada_image)')
        ->addSql('values')
        ->addSql('($1, $2)')
        ->addParam($id)
        ->addParam('/uploads/images/ads/' . $id . '/'
          . $filename . '-' . $i . '-bg.' . $ext)
        ->execute();

      $i++;
    }

    return true;
  }

  public function getAdvertsBar() {
    $adverts = $this->db()->query()
      ->addSql('select * from last_adverts_vw')
      ->addSql('limit 6')
      ->addTag('adverts')
      ->fetchResult(false);

    return [
      'adverts' => $adverts,
    ];
  }

  public function getIndex() {
    $id = (int)$this->getRequest()->getParam('id');

    $query = $this->db()->query()
      ->addSql('select id, title, description, keywords, name, category, type, date, image, image_medium,')
      ->addSql('text, user_name, user_city_#lang# as user_city,')
      ->addSql("(cost_unformat/$2)::FLOAT as cost_unformat, cost_unformat as cost_usd,")
      ->addSql("replace(replace(trim(to_char((cost_unformat/$2),'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql('user_email, user_phone, user_phone_unformat,')
      ->addSql('short_name_#lang# as rate_name, count_comments')
      ->addSql('from adverts2_vw')
      ->addSql('where id = $1')
      ->addParam($id)
      ->addParam($this->currency['ratio']);

    $advert = $query->addTag('advert-' . $id)
      ->fetchRow(0, false);

    if (empty($advert)) {
      throw new RouteException;
    }

    $advert['text'] = nl2br(strip_tags($advert['text']));
    $advert['params'] = [
      'id' => $id,
      'type' => $advert['type'],
      'category' => $advert['category'],
    ];

    $images = $this->db()->query()
      ->addSql('select name, image, image_small')
      ->addSql('from adverts_attachments_vw')
      ->addSql('where advert_id = $1 offset 1')
      ->addParam($id)
      ->addTag('advert-images')
      ->fetchResult();
    $advert['images'] = $images;

    $advert['comment_data'] = [
      'id' => $id,
      'type' => 'advert',
    ];

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_share') );
    $share_body = $templater->parse($advert);
    $share_body = rawurlencode($share_body);
    $advert['share_body'] = $share_body;
    $advert['name_encode'] = rawurlencode(' | ' . $advert['name']);

    $meta_tags_variables = $this->db()->query()
      ->addSql('select name, cost, date, city_#lang# as city,')
      ->addSql('country_#lang# as country, title, keywords, description,')
      ->addSql('case when type = \'sell\' then \'#1\'')
      ->addSql('when type = \'buy\' then \'#2\' end as type,')
      ->addSql('case when category = \'autopart\' then \'#3\'')
      ->addSql('when type = \'car\' then \'#4\' end as category,')
      ->addSql('case when (cost::VARCHAR is null or cost::VARCHAR = \'\') then \'#5\' else cost::VARCHAR end as cost,')
      ->addSql('short_name_#lang# as rate_name')
      ->addSql('from adverts2_vw')
      ->addSql('where id=$1')
      ->addParam( (int)$this->getRequest()->getParam('id') )
      ->addReplacement( $this->getVariable('lng_i_sell') )
      ->addReplacement( $this->getVariable('lng_i_buy') )
      ->addReplacement( $this->getVariable('lng_autopart') )
      ->addReplacement( $this->getVariable('lng_car') )
      ->addReplacement( $this->getVariable('lng_contract_price') )
      ->addTag('adverts')
      ->fetchRow(0, false);

    $advert_tmp = $this->db()->query()
      ->addSql('select adv_video_url as url')
      ->addSql('from adverts_tbl where adv_id_pk = $1')
      ->addParam($id)
      ->addTag('advert-url' . $id)
      ->fetchRow(0, false);
    $advert['url'] =  $advert_tmp['url'];

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_title') );
    $advert['title'] = trim($templater->parse($meta_tags_variables));

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_description') );
    $advert['description'] = trim($templater->parse($meta_tags_variables));

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_keywords') );
    $advert['keywords'] = trim($templater->parse($meta_tags_variables));

    return $advert;
  }

  public function getVars() {
    $abstract =  $this->getVariables();
    $adverts =  $this->getIndex();

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobal($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_title') );
    $title = $templater->parse($this->metaTags);
    // $title = $templater->parse($adverts);
    $abstract['title']  = $title;

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobal($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_keywords') );
    $title = $templater->parse($this->metaTags);
    $abstract['keywords']  = $title;

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobal($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_description') );
    $title = $templater->parse($this->metaTags);
    $abstract['description']  = $title;

    return  array_merge($adverts, $abstract);
  }

  public function getList() {
    $result = [];
  }

  public function getPageAjax() {
    $count_on_page = 10;
    $page = ~~$this->getRequest()->get('page');
    if ($page == 0) {
      $page = 1;
    }
    $type_sell = $this->getRequest()->get('type-sell');
    $type_buy = $this->getRequest()->get('type-buy');
    $category_car = $this->getRequest()->get('category-car');
    $category_autopart = $this->getRequest()->get('category-autopart');

    $type_sell_buy = null;
    $sell = '!';
    $buy = '!';
    if ('true' == $type_sell) {
      $type_sell_buy = true;
      $sell = 'sell';
    }
    if ('true' == $type_buy) {
      $type_sell_buy = true;
      $buy = 'buy';
    }

    $category_autopart_car = null;
    $autopart = '!';
    $car = '!';
    if ('true' == $category_autopart) {
      $category_autopart_car = true;
      $autopart = 'autopart';
    }
    if ('true' == $category_car) {
      $category_autopart_car = true;
      $car = 'car';
    }

    $params = $this->getRequest()->getParams();

    $i = 3;

    $query = $this->db()->query()
      ->sql("select id, name, text, category, type, image_small as image,
        date, user_name, user_city_#lang# as user_city,
        (cost_unformat/$3)::FLOAT as cost_unformat, cost_unformat as cost_usd,
        replace(replace(trim(to_char((cost_unformat/$3),'999 999 999.99')), '.', ','), ',00', '') as cost,
        user_email, user_phone, user_phone_unformat,
        short_name_#lang# as rate_name
        from adverts_vw
        where 1 = 1")
      ->addSql('and type = ANY(ARRAY[\'' . $sell . '\', \'' . $buy . '\'])',
        $type_sell_buy)
      ->addSql('and category = ANY(ARRAY[\'' . $autopart . '\', \'' . $car . '\'])',
        $category_autopart_car)
      ->addSql('limit $2 offset $1')
      ->addParam(($page-1) * $count_on_page)
      ->addParam($count_on_page)
      ->addParam($this->currency['ratio'])
      ->addTag('adverts');
    $data = $query->fetchResult(false);

    foreach($data as &$news) {
      $news['text'] = strip_tags($news['text']);
      $news['text'] = $this->ling->shortedText($news['text'],
        200, false);
    }

    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
        $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'pageContent.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $this->setVariable('adverts', $data);

    $count = $this->db()->query()
      ->addSql('select ceil(count(adv_id_pk)::FLOAT / $1) as full_count')
      ->addSql('from adverts_tbl where adv_enabled = true')
      ->addSql('and adv_type = ANY(ARRAY[\'' . $sell . '\', \'' . $buy . '\'])',
        $type_sell_buy)
      ->addSql('and adv_category = ANY(ARRAY[\'' . $autopart . '\', \'' . $car . '\'])',
        $category_autopart_car)
      ->addParam($count_on_page)
      ->addTag('adverts')
      ->fetchField(0);

    return [
      'html' => $templater->parse( $this->getVariables() ),
      'pages' => $count
    ];
  }

  public function getOtherAdverts($params) {
    $adverts = $this->db()->query()
      ->addSql('select id, name, category, type, image, image_mini,')
      ->addSql('short_name_#lang# as rate_name,')
      ->addSql("(cost_unformat/$4)::FLOAT as cost_unformat, cost_unformat as cost_usd,")
      ->addSql("replace(replace(trim(to_char((cost_unformat/$4),'999 999 999.99')), '.', ','), ',00', '') as cost")
      ->addSql('from adverts_vw')
      ->addSql('where id != $1 and category = $2 and type = $3 limit 12')
      ->addParam($params['id'])
      ->addParam($params['category'])
      ->addParam($params['type'])
      ->addParam($this->currency['ratio'])
      ->addTag('adverts')
      ->fetchResult(false);

    return [
      'adverts' => $adverts
    ];
  }

  public function addImage() {
    $advert_id = $this->getRequest()->get('id');
    $advert = $this->db()->query()
      ->addSql('select name, count(ada_id_pk)+1 as max')
      ->addSql('from adverts_vw')
      ->addSql('left join adverts_attachments_tbl on ada_adv_id_fk = id')
      ->addSql('where id = $1')
      ->addSql('group by name')
      ->addParam($advert_id)
      ->fetchRow(0, false);

    $files = $this->getRequest()->files()['ada_image'];
    $this->_uploadImage($advert_id, $advert['name'], [], $files, $advert['max']);

    return $this;
  }

  public function uploadFiles() {
    $files = $this->getRequest()->files()['files'];
    if ( (1024 * 1024 * 2) < $files['size'][0] ) {
      return false;
    }

    $basename = pathinfo($files['tmp_name'][0])['basename'];
    $ext = strtolower(pathinfo($files['name'][0])['extension']);
    $dest = $this->getVariable('path_uploadDir') . 'tmp/' . $basename . '.'
      . $ext;
    move_uploaded_file($files['tmp_name'][0], $dest);

    return [
      'file' => $dest,
    ];
  }


  public function getStatus() {
    $user_ip = $this->get_client_ip();

    $if_posted_today = $this->db()->query()
      ->addSql('select adv_date_create as date')
      ->addSql('from adverts_tbl where adv_user_ip = $1')
      ->addParam($user_ip)
      ->addSql('ORDER BY adv_id_pk DESC')
      ->fetchField(0, false);

    $curent_date = date('Y-m-d');
    if (!empty($if_posted_today)) {
      if (substr($if_posted_today, 0, 10) == $curent_date) {
        return 1;
      }
    }

    return 0;
  }

  public function getAddForm() {
    $currencies = $this->db()->query()->sql(
      "select currency_#lang# as name, id "
      . "from currencies_vw as crr ")
      ->addTag('currencies')
      ->fetchResult(false);

    return ['currency' => $currencies];
  }

  public function getPage() {
    $page = $this->getRequest()->getParams();
    return $page;
  }

  public function getPageCount() {
    $count_on_page = 10;
    $count = $this->db()->query()
      ->addSql('select ceil(count(adv_id_pk)::FLOAT / $1) as full_count')
      ->addSql('from adverts_tbl where adv_enabled = true')
      ->addParam($count_on_page)
      ->addTag('adverts')
      ->fetchField(0);

    return [
      'pages' => $count
    ];
  }

  public function getPageAdverts() {
    $count_on_page = 10;
    $getpage = $this->getRequest()->getParams();
    if (isset($getpage['page'])) {
      $page = $getpage['page'];
    } else {
      $page = 1;
    }


    if ($page == 0) {
      $page = 1;
    }
    $type_sell = '';
    $type_buy= '';
    $category_car= '';
    $category_autopart= '';

    $type_sell_buy = null;
    $sell = '!';
    $buy = '!';
    if ('true' == $type_sell) {
      $type_sell_buy = true;
      $sell = 'sell';
    }
    if ('true' == $type_buy) {
      $type_sell_buy = true;
      $buy = 'buy';
    }

    $category_autopart_car = null;
    $autopart = '!';
    $car = '!';
    if ('true' == $category_autopart) {
      $category_autopart_car = true;
      $autopart = 'autopart';
    }
    if ('true' == $category_car) {
      $category_autopart_car = true;
      $car = 'car';
    }

    $params = $this->getRequest()->getParams();

    $i = 3;

    $query = $this->db()->query()
      ->sql("select id, name, text, category, type, image_small as image,
        date, user_name, user_city_#lang# as user_city,
        (cost_unformat/$3)::FLOAT as cost_unformat, cost_unformat as cost_usd,
        replace(replace(trim(to_char((cost_unformat/$3),'999 999 999.99')), '.', ','), ',00', '') as cost,
        user_email, user_phone, user_phone_unformat,
        short_name_#lang# as rate_name
        from adverts_vw
        where 1 = 1")
        ->addSql('and type = ANY(ARRAY[\'' . $sell . '\', \'' . $buy . '\'])',
          $type_sell_buy)
          ->addSql('and category = ANY(ARRAY[\'' . $autopart . '\', \'' . $car . '\'])',
            $category_autopart_car)
            ->addSql('limit $2 offset $1')
            ->addParam(($page-1) * $count_on_page)
            ->addParam($count_on_page)
            ->addParam($this->currency['ratio'])
            ->addTag('adverts');
    $data = $query->fetchResult(false);

    foreach($data as &$news) {
      $news['text'] = strip_tags($news['text']);
      $news['text'] = $this->ling->shortedText($news['text'],
        200, false);
    }

    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
      $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'pageContent.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $this->setVariable('adverts', $data);

    $count = $this->db()->query()
      ->addSql('select ceil(count(adv_id_pk)::FLOAT / $1) as full_count')
      ->addSql('from adverts_tbl where adv_enabled = true')
      ->addSql('and adv_type = ANY(ARRAY[\'' . $sell . '\', \'' . $buy . '\'])',
        $type_sell_buy)
        ->addSql('and adv_category = ANY(ARRAY[\'' . $autopart . '\', \'' . $car . '\'])',
          $category_autopart_car)
          ->addParam($count_on_page)
          ->addTag('adverts')
          ->fetchField(0);

    return ['html' => $templater->parse( $this->getVariables() )];
  }
  public function needPay() {
    $user_ip = $this->get_client_ip();

    $if_posted_today = $this->db()->query()
      ->addSql('select adv_date_create as date')
      ->addSql('from adverts_tbl where adv_user_ip = $1')
      ->addParam($user_ip)
      ->addSql('ORDER BY adv_id_pk DESC')
      ->fetchField(0, false);

    $curent_date = date('Y-m-d');
    $need_pay = intval(0);
    $enabled = 'TRUE';
    if (!empty($if_posted_today)) {
      if (substr($if_posted_today, 0, 10) == $curent_date) {
        $need_pay = 1;
        $enabled = 'FALSE';   // не публикуем
        //   $enabled = 'TRUE';    // = публикуем
      }
    }
    return ['need_pay'=>$need_pay];
  }
  public function addPremiumAdvert() {
    $validator = new Validator();
    $data = $this->getRequest()->post();
    // if ($_SERVER['REQUEST_METHOD'] == 'POST') {  $data = $this->getRequest()->post();}
    // if ($_SERVER['REQUEST_METHOD'] == 'GET') {  $data = $this->getRequest()->get();}

    $form = 'add';
    $payUrl = "/json/ads/payments-form.html";
    if (!isset($data['photo'])) {  $data['photo'] = [];  }
    $auth = Auth::getInstance();
    $email = $auth->getStorage()->identity;

    $user_ip = $this->get_client_ip();

    $if_posted_today = $this->db()->query()
      ->addSql('select adv_date_create as date')
      ->addSql('from adverts_tbl where adv_user_ip = $1')
      ->addParam($user_ip)
      ->addSql('ORDER BY adv_id_pk DESC')
      ->fetchField(0, false);

    $curent_date = date('Y-m-d');
    $need_pay = intval(0);
    $enabled = 'TRUE';
    if (!empty($if_posted_today)) {
      if (substr($if_posted_today, 0, 10) == $curent_date) {
        $need_pay = 1;
        $enabled = 'FALSE';   // не публикуем
        //   $enabled = 'TRUE';    // = публикуем
      }
    }
    if (!empty($data['video'])) {
      $need_pay = 1;
      $enabled = 'FALSE';
      $data['video'] = $this->linkifyYouTubeURLs($data['video']);
    }
    if (count($data['photo']) > 5 ) {
      $need_pay = 1;
      $enabled = 'FALSE';

    }

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();
    $errors = $validator->validate($form, $rules, $data, $lang_variables);

    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    if (empty($data['caption'])) {
      $data['caption'] = "null";
    }
    if (empty($data['video'])) {
      $data['video'] = "null";
    }
    $id = $this->db()->query()
      ->addSql('insert into adverts_tbl(adv_type, adv_category, adv_cost,')
      ->addSql('adv_rat_id_fk, adv_name, adv_user_name, adv_user_city,')
      ->addSql('adv_user_email, adv_user_phone, adv_lng_id_fk, adv_text, adv_video_url, adv_user_ip, adv_enabled, adv_type_payable)')
      ->addSql('values')
      ->addSql('($1, $2, $3, $4, $5, $6, $7, $8, $9,')
      ->addSql('(select lng_id_pk from languages_tbl where lng_synonym = $10)')
      ->addSql(', $11, $12, $13, $14, $15)')
      ->addParam($data['type'])
      ->addParam($data['category'])
      ->addParam(intval($data['price']))
      ->addParam(2)
      ->addParam($data['caption'])
      ->addParam($data['name'])
      ->addParam($data['city'])
      ->addParam($email)
      ->addParam($data['phone'])
      ->addParam($this->getVariable('current_language'))
      ->addParam($data['text'])
      ->addParam($data['video'])
      ->addParam($user_ip)
      ->addParam($enabled)
      ->addParam($need_pay)
      ->execute('adv_id_pk');

    if ( empty($data['photo']) ) {
      return [
        "errors" => 0,
        "status" => 1,
        "need_pay" => $need_pay,
        "pay_form_url" => $payUrl,
        "advert_id" => $id
      ];
    }

    $this->_uploadImage($id, $data['caption'], $data['photo']);

    return [
      "errors" => 0,
      "status" => 1,
      "need_pay" => $need_pay,
      "pay_form_url" => $payUrl,
      "advert_id" => $id
    ];
  }

  public function rmImage() {
    $data = $this->getRequest()->post();

    $url = isset($data['image'])?"/uploads".preg_replace("/^(.*)\/uploads/iu", "", $data['image']):false;

    //return ['status' => $url];

    if($url) {
      $img = $this->db()->query()
        ->addSql('select ada_id_pk id, ada_adv_id_fk adv_id')
        ->addSql('from adverts_attachments_tbl')
        ->addSql("where ada_image = $1")
        ->addParam($url)
        ->fetchRow(0, false);
      //return $img;
      if ($img) {
        $result =  $this->db()->query()
          ->sql('DELETE FROM adverts_attachments_tbl where ada_id_pk='.$img['id']);
        //->addParam($img['id']);
        //$result->execute();
        try {
          $result->execute();
          $this->db()->query()
            ->sql("update adverts_tbl set adv_image=NULL")
            ->addSql("where adv_id_pk = $1")
            ->addParam($img['adv_id'])
            ->execute();
          $images = $this->db()->query()
            ->addSql('select name, image, image_small')
            ->addSql('from adverts_attachments_vw')
            ->addSql('where advert_id = $1')
            ->addParam($img['adv_id'])
            ->addTag('advert-images')
            ->fetchResult(false);
          if(count($images)>0){
            $this->db()->query()
              ->sql("update adverts_tbl set adv_image='". stripcslashes($images[0]['image'])."'")
              ->addSql("where adv_id_pk = $1")
              ->addParam($img['adv_id'])
              ->execute();
          }

          //unlink($url);
          return ["status" => 0];
        } catch (Exception $e){
          return $e->getMessage();
        }
        //unlink($url);

      } else return ["status" => 'no image'];
    } else {
      return ["status" => 'error 1'];
    }

  }

  public  function get_client_ip() {
    if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
      $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
    else
      $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }


  //  http://stackoverflow.com/questions/5830387/how-to-find-all-youtube-video-ids-in-a-string-using-a-regex
  public function linkifyYouTubeURLs($text) {
    $result = strpos ($text, 'http');
    if ($result === FALSE) {
      $result2 = strpos ($text, 'yout');
      if ($result2 === FALSE) {
        $text = 'youtube.com/'. $text;
      } else {
        $text = 'http://'. $text;
      }
      $text = 'http://'. $text;
    }

    // case we have http but no yout
    $result3 = strpos ($text, 'yout');
    if ($result3 === FALSE) {
      $text = '';
    }

    $text = preg_replace('~(?#!js YouTubeId Rev:20160125_1800)
      # Match non-linked youtube URL in the wild. (Rev:20130823)
      https?://          # Required scheme. Either http or https.
      (?:[0-9A-Z-]+\.)?  # Optional subdomain.
      (?:                # Group host alternatives.
      youtu\.be/       # Either youtu.be,
      | youtube          # or youtube.com or
      (?:-nocookie)?   # youtube-nocookie.com
      \.com            # followed by
      \S*?             # Allow anything up to VIDEO_ID,
      [^\w\s-]         # but char before ID is non-ID char.
    )                  # End host alternatives.
    ([\w-]{11})        # $1: VIDEO_ID is exactly 11 chars.
    (?=[^\w-]|$)       # Assert next char is non-ID or EOS.
    (?!                # Assert URL is not pre-linked.
    [?=&+%\w.-]*     # Allow URL (query) remainder.
    (?:              # Group pre-linked alternatives.
    [\'"][^<>]*>   # Either inside a start tag,
    | </a>           # or inside <a> element text contents.
  )                # End recognized pre-linked alts.
)                  # End negative lookahead assertion.
[?=&+%\w.-]*       # Consume any URL (query) remainder.
~ix', 'http://www.youtube.com/embed/$1',
$text);
return $text;
  }
}
