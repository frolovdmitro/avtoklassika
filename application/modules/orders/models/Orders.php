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
    \Uwin\Validator,
    \Uwin\Auth,
    \Uwin\Registry,
    \Uwin\Xml,
    \Uwin\Mail,

    PayPal\Rest\ApiContext,
    PayPal\Auth\OAuthTokenCredential,
    PayPal\Api\Amount,
    PayPal\Api\Details,
    PayPal\Api\Payer,
    PayPal\Api\Payment,
    PayPal\Api\RedirectUrls,
    PayPal\Api\Transaction,
    PayPal\Api\Item,
    PayPal\Api\ItemList,
    PayPal\Api\PaymentExecution,

    \Uwin\Controller\Front,
    \Uwin\TemplaterBlitz    as Templater,
    \Uwin\Exception\Route    as RouteException;

/**
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 */
class Orders extends Abstract_
{
  private static $DOMAIN = null;

  private $_currency;

  private $_session_id;

  private $_identity;

  /**
   * Метод возвращает текущую валюту
   */
  private function _getCurrentCurrency() { // {{{
    if ( isset($_COOKIE['currency']) ) {
      return $_COOKIE['currency'];
    }

    $currency = $this->db()->query()->sql(
      "select synonym from currencies_vw crr where crr.default = true")
      ->fetchRow(0, false);

    return $currency['synonym'];
  } // }}}


  private function _getCurrency() { // {{{
    $where = 'crr.default = true';
    if (isset($_COOKIE['currency'])) {
      $where = 'synonym = $1';
    }
    $query = $this->db()->query()->sql(
      "select currency_#lang# as currency, short_name_#lang# as short_name,"
      . "synonym, value, ratio "
      . "from currencies_vw crr "
      . "where " . $where);
    if (isset($_COOKIE['currency'])) {
      $query->addParam($_COOKIE['currency']);
    }
    $currency = $query->fetchRow(0, false);

    return $currency;
  } // }}}


  public function __construct() { // {{{
    self::$DOMAIN = '.' . $this->getRequest()->serverName();

    $storage = Auth::getInstance()->getStorage();
    $this->_session_id = $storage->getId();
    $this->_identity = $storage->identity;

    return $this;
  } // }}}


  /**
    * @return Orders
    */
  public function getIndex() { // {{{
    return $this;
  } // }}}


  /**
  * Возвращает id заказа в который будут добавляться продукты, если заказа нет,
  * создает его
  *
  * @return void
  */
  private function _getOrderId() { // {{{
    $id = $this->db()->query()
      ->addSql('select ord_id_pk from orders_tbl')
      ->addSql('where (')
      ->addSql('ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1)')
      ->addSql('or ord_session_id = $2')
      ->addSql(')')
      ->addSql('and ord_status = $3')
      ->addSql('order by ord_datetime desc limit 1')
      ->addParam($this->_identity)
      ->addParam($this->_session_id)
      ->addParam('not_complete')
      ->fetchField(0, false);

    if ( !empty($id) ) {
      return $id;
    }

    return null;
  } // }}}


  /**
  * Возвращает все данные о заказе
  *
  * @return void
  */
  private function _getOrderData($id) { // {{{
    $ratio = $this->_getCurrency();
    $discount = $this->getUserDiscount();

    $data = $this->db()->query()
      ->addSql('select coalesce(count_details,0) as count, coalesce(ceil((ord_sum*$3) - ord_sum_discount),0) as sum_usd,')
      ->addSql('coalesce(ceil(((ord_sum*$3) - ord_sum_discount)/$2)::FLOAT,0) as sum, coalesce(ord_discount,0) as promocode,')
      ->addSql('replace(replace(trim(to_char(coalesce(ceil(((ord_sum*$3) - ord_sum_discount)/$2)),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as sum_format,')

      ->addSql('coalesce(ceil(ord_sum - ord_sum_discount),0) + coalesce(ceil(ord_sum_delivery),0) as total_sum_usd,')
      ->addSql('coalesce(ceil((ord_sum - ord_sum_discount)/$2)::FLOAT,0) + coalesce(ceil((coalesce(ord_sum_delivery,0))/$2)::FLOAT,0) as total_sum,')
      ->addSql('replace(replace(trim(to_char(coalesce(ceil((ord_sum - ord_sum_discount + coalesce(ord_sum_delivery,0))/$2)),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as total_sum_format')

      ->addSql('from orders_tbl')
      ->addSql('left join (select odd_ord_id_fk, sum(odd_count) as count_details')
      ->addSql('from orders_details_tbl group by odd_ord_id_fk) as odd on ord_id_pk = odd_ord_id_fk')
      ->addSql('where ord_id_pk = $1')
      ->addParam($id)
      ->addParam($ratio['ratio'])
      ->addParam($discount)
      ->fetchRow(0, false);

    return $data;
  } // }}}


  /**
  * Возвращает все товары заказа
  *
  * @return void
  */
  private function _getOrderProducts($id)
  { // {{{
    $ratio = $this->_getCurrency();
    $discount = $this->getUserDiscount();

    $data = $this->db()->query()
      ->addSql('select id, name_#lang# as name, (cost*$3) as cost_usd, advert_id as advert_id,')
      ->addSql("replace(replace(trim(to_char((cost/$2)*$3,'999 999 999.99')), '.', ','), ',00', '') as cost,")
      ->addSql('((cost/$2)*$3)::FLOAT as cost_unformat, ')
      ->addSql('ceil((cost * count)*$3) as sum_usd, ceil(((cost/$2) * count)*$3) as sum_unformat,')
      ->addSql('replace(replace(trim(to_char(((cost/$2)*count)*$3,\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as sum,')
      ->addSql('replace(image, \'-bg\', \'-mini\') as image_mini,')
      ->addSql('replace(image, \'-bg\', \'-sm\') as image_small, count, car_synonym, color_name_#lang# as color_name,')
      ->addSql('color_id, size_id, size_name_#lang# as size_name,')
      ->addSql('status, num')
      ->addSql('from order_products_vw')
      ->addSql('where order_id = $1')
      ->addParam($id)
      ->addParam($ratio['ratio'])
      ->addParam($discount)
      ->fetchResult(false);
    for($i=0;$i<count($data);$i++) {
      if($data[$i]['advert_id']){
        $tmp = $this->db()->query()
          ->addSql('select adv_name, replace(adverts_tbl.adv_image::text, \'-bg\'::text, \'-sm\'::text) AS image_small, adv_image, adv_text')
          ->addSql('from adverts_tbl')
          ->addSql('where adv_id_pk=$1')
          ->addParam($data[$i]['advert_id'])
          ->fetchResult(false);
        $data[$i]['image_small'] = isset($tmp[0]['image_small'])?$tmp[0]['image_small']:'';
        $data[$i]['adv_name'] = isset($tmp[0]['adv_name'])?$tmp[0]['adv_name']:'';
        $data[$i]['adv_text'] = isset($tmp[0]['adv_text'])?mb_substr($tmp[0]['adv_text'],0,200):'';
      }
    }

    return $data;
  } // }}}


  /**
  * undocumented function
  *
  * @return void
  */
  private function _addToBasket($id, $data, $advertID) { // {{{
    if (0 == $data['size']) {
      $data['size'] = null;
    }
    if (0 == $data['color']) {
      $data['color'] = null;
    }

    if ($advertID == null) {
      $exists_order_detail_id = $this->db()->query()
        ->addSql('select odd_id_pk as id')
        ->addSql('from orders_details_tbl')
        ->addSql('where odd_ord_id_fk = $1 and odd_dpt_id_fk = $2')
        ->addSql('and coalesce(odd_dac_id_fk, 0) = coalesce($3, 0)')
        ->addSql('and coalesce(odd_das_id_fk, 0) = coalesce($4, 0)')
        ->addParam($id)
        ->addParam($data['id'])
        ->addParam($data['color'])
        ->addParam($data['size'])
        ->fetchField(0, false);
    }

    if ( empty($exists_order_detail_id) ) {
      $this->db()->query()
        ->addSql('insert into orders_details_tbl(odd_ord_id_fk, odd_dpt_id_fk,')
        ->addSql('odd_count, odd_dac_id_fk, odd_das_id_fk, odd_advert_id)values($1, $2, $3, $4, $5, $6)')
        ->addParam($id)
        ->addParam($data['id'])
        ->addParam($data['count'])
        ->addParam($data['color'])
        ->addParam($data['size'])
        ->addParam($advertID)
        ->execute();
    } else {
      $this->db()->query()
        ->addSql('update orders_details_tbl set odd_count = odd_count + $2')
        ->addSql('where odd_id_pk = $1')
        ->addParam($exists_order_detail_id)
        ->addParam($data['count'])
        ->execute();
    }

    return true;
  } // }}}


  private function _changeProductInBasket($id, $data) { // {{{
    $this->db()->query()
      ->addSql('update orders_details_tbl set odd_count = $5')
      ->addSql('where odd_ord_id_fk = $1')
      ->addSql('and odd_dpt_id_fk = $2')
      ->addSql('and coalesce(odd_das_id_fk, 0) = coalesce($3, 0)')
      ->addSql('and coalesce(odd_dac_id_fk, 0) = coalesce($4, 0)')
      ->addParam($id)
      ->addParam($data['id'])
      ->addParam($data['color'])
      ->addParam($data['size'])
      ->addParam($data['count'])
      ->execute();

    return true;
  } // }}}


  /**
  * Изменение корзины
  *
  * @return void
  */
  public function changeBasket() { // {{{
    $id = $this->_getOrderId();
    if ( empty($id) ) {
      $id = $this->db()->query()
        ->addSql('insert into orders_tbl(ord_usr_id_fk, ord_session_id, ord_status, ord_rat_id_fk,')
        ->addSql('ord_lng_id_fk)values((select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1),')
        ->addSql('$2, $3,')
        ->addSql('(select rat_id_pk from rates_tbl where rat_synonym = $4),')
        ->addSql('(select lng_id_pk from languages_tbl where lng_synonym = $5))')
        ->addParam($this->_identity)
        ->addParam($this->_session_id)
        ->addParam('not_complete')
        ->addParam($this->_getCurrentCurrency())
        ->addParam($this->getVariable('current_language'))
        ->execute('ord_id_pk');
    }

    $product = $this->getRequest()->post();

    if ('add' == $product['method']) {
      if (isset($product['adverts_id'])) { $advertID = $product['adverts_id']; } else {$advertID = null; }
      $this->_addToBasket($id, $product, $advertID);
    }

    if ('change' == $product['method']) {
      $this->_changeProductInBasket($id, $product);
    }

    $order_data = $this->_getOrderData($id);

    return $order_data;
  } // }}}


  public function deleteBasketItem() { // {{{
    $order_id = $this->_getOrderId();
    $id_array = explode('-', $this->getRequest()->getParam('id'));

    $data = $this->getRequest()->post();
    if (isset($data['advert_id']) && $data['advert_id'] > 0) {
      $this->db()->query()
        ->addSql('delete from orders_details_tbl where odd_ord_id_fk = $2')
        ->addSql('and odd_advert_id = $1')
        ->addParam($data['advert_id'])
        ->addParam($order_id)
        ->execute();
    } else  {
      $this->db()->query()
        ->addSql('delete from orders_details_tbl where odd_ord_id_fk = $1')
        ->addSql('and odd_dpt_id_fk = $2')
        ->addSql('and coalesce(odd_das_id_fk, 0) = coalesce($3, 0)')
        ->addSql('and coalesce(odd_dac_id_fk, 0) = coalesce($4, 0)')
        ->addParam($order_id)
        ->addParam($id_array[0])
        ->addParam($id_array[1])
        ->addParam($id_array[2])
        ->execute();
    }

    $order_data = $this->_getOrderData($order_id);

    return $order_data;
  } // }}}


  /**
  * Очищаем промокод если он устарел
  *
  * @return void
  */
  private function _clearPromocode() { // {{{
    $discount_enabled = $this->getVariable('stg_discount_enabled');
    $start_date = strtotime($this->getVariable('stg_discount_start_date'));
    if ( empty($start_date) ) {
      $start_date = time() - 60;
    }
    $stop_date = strtotime($this->getVariable('stg_discount_stop_date'));
    if ( empty($stop_date) ) {
      $stop_date = time() + 60;
    } else {
      $stop_date = strtotime($this->getVariable('stg_discount_stop_date'))
        + (60 * 60 * 24);
    }

    if('true' == $discount_enabled and time() > $start_date and time() < $stop_date) {
      return false;
    }

    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      return null;
    }

    $this->db()->query()
      ->addSql('update orders_tbl set ord_discount = null')
      ->addSql('where ord_status=$1 and')
      ->addSql('ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $2 or usr_social_id = $2 limit 1)')
      ->addParam('not_complete')
      ->addParam($auth->getStorage()->identity)
      ->execute();

    return true;
  } // }}}


  public function getBasketBar() { // {{{
    $this->_clearPromocode();

    $id = $this->_getOrderId();
    $data = $this->_getOrderData($id);

    $where = 'crr.default = true';
    if (isset($_COOKIE['currency'])) {
      $where = 'synonym = $1';
    }
    $query = $this->db()->query()->sql(
      "select short_name_#lang# as short_name "
      . "from currencies_vw crr "
      . "where " . $where);
    if (isset($_COOKIE['currency'])) {
      $query->addParam($_COOKIE['currency']);
    }
    $currency = $query->fetchField(0, 200);

    $data['short_name'] = $currency;

    return $data;
  } // }}}


  /**
   * undocumented function
   *
   * @return void
   */
  public function getBasketData() { // {{{
    $id = $this->_getOrderId();
    $total = $this->_getOrderData($id);
    // var_dump($total);
    if ( empty($total) ) {
      $this->getRequest()->redirect('/');
    }
    $products = $this->_getOrderProducts($id);
    $discount = $total['promocode'];

    $currency = $this->_getCurrency();
    $this->setVariable('currency_abb', $currency['short_name']);

    $discount_enabled = $this->getVariable('stg_discount_enabled');
    $start_date = strtotime($this->getVariable('stg_discount_start_date'));
    if ( empty($start_date) ) {
      $start_date = time() - 60;
    }
    $stop_date = strtotime($this->getVariable('stg_discount_stop_date'));
    if ( empty($stop_date) ) {
      $stop_date = time() + 60;
    } else {
      $stop_date = strtotime($this->getVariable('stg_discount_stop_date'))
        + (60 * 60 * 24);
    }

    if('true' == $discount_enabled and time() > $start_date and time() < $stop_date) {
      $this->setVariable('promocode', $this->getVariable('stg_discount_value'));
    }

    $first_id = null;
    if ( !empty($products) ) {
      $first_id = $products[0]['id'];
    }

    return [
      'relatedData' => [
        'type' => 'basket',
        'id'   => $first_id,
      ],
      'total_sum' => $total['total_sum_format'],
      'total_sum_unformat' => $total['total_sum'],
      'total_sum_usd' => $total['total_sum_usd'],

      'products_sum' => $total['sum_format'],
      'products_sum_unformat' => $total['sum'],
      'products_sum_usd' => $total['sum_usd'],

      'products' => $products,
      'discount'   => $discount,
    ];
  } // }}}


  public function getBasketSidebar() { // {{{
    return $this->getBasketData();
  } // }}}


  public function getBasketProducts() { // {{{
    return $this->getBasketData();
  } // }}}

















  private function _sendCreateForAdminMail($quick = false, $data) { // {{{
    $quick_txt = '';
    if ($quick) {
      $quick_txt = '_quick';
    }
    $subject = $this->getVariable('lng_mails' . $quick_txt . '_order_created_subject');
    $body = $this->getVariable('lng_mails' . $quick_txt . '_order_created_body');

    $data['url_staticServer'] = $this->getVariable('url_staticServer');

    $templater = new Templater;
    $templater->load($subject);
    $subject = $templater->parse($data);

    $templater = new Templater;
    $templater->load($body);
    $body = $templater->parse($data);

    // var_dump($body);
    // Получаем имя и email от которого будут оправляться письма
    if ( false === $mailerName = $this->getVariable('stg_mail_name') ) {
      $mailerName = null;
    }
    $mailerEmail = $this->getVariable('stg_mail_email');

    $managers = $this->getVariable('stg_general_administrator_email');
    // Отправляем почту на email админу
    $settings = Registry::get('stg');
    $mail = new Mail($settings['mail']['smtp']);
    $mail->setFromEmail($mailerEmail, $mailerName)
      ->addEmail($data['user_email'])
      ->addEmail( explode(',', $managers) )
      ->setSubject($subject)
      ->setText($body)
      ->send();

    $user = $this->db()->query()
      ->addSql('select usr_id_pk from users_tbl where lower(usr_email) = lower($1)')
      ->addParam($data['user_email'])
      ->fetchRow(0, false);

    if (!empty($user)) {
      $this->createModel('mailer')
        ->saveMail('user', $user['usr_id_pk'], 'create_order', $data['user_email'],
          $subject, $body);
    }

    return $this;
  } // }}}

  public function getOrderReport() { // {{{
    $id = $this->getRequest()->getParam('id');

    $orderInfo = $this->db()->query()
      ->addSql('select num, date, user_name, user_street, user_build,')
      ->addSql('user_flat, user_city, user_index, user_country_ru,')
      ->addSql('currency, discount,')
      ->addSql('sum_delivery_usd_unformat*rate as sum_delivery,')
      ->addSql('sum_discount_usd_unformat*rate as sum_discount,')
      ->addSql('(sum_discount_usd_unformat+sum_usd_unformat)*rate as sum_subtotal,')
      ->addSql('(sum_delivery_usd_unformat+sum_usd_unformat)*rate as sum_total,')
      ->addSql('payment_name_ru, delivery_name_ru')
      ->addSql('from order_info_vw')
      ->addSql('where id = $1')
      ->addParam($id)
      ->fetchRow(0, false);

    if ( empty($orderInfo) ) {
      return  false;
    }

    $path_library = $this->getVariable('path_library');
    $path_root = $this->getVariable('path_root');

    $excelFile = $path_root . 'scripts/order-template.xlsx';

    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($excelFile);
    $worksheet = $objPHPExcel->setActiveSheetIndex(0);

    $worksheet->setCellValue('I4', $orderInfo['num']);
    $worksheet->setCellValue('I7', $orderInfo['date']);
    $worksheet->setCellValue('B12', $orderInfo['user_name']);
    $worksheet->setCellValue('B13', $orderInfo['user_street'] . ', ' . $orderInfo['user_build'] . ', кв. ' . $orderInfo['user_flat']);
    $worksheet->setCellValue('B14','г. ' . $orderInfo['user_city'] . ', ' . $orderInfo['user_index'] . ', ' . $orderInfo['user_country_ru']);


    $orderDetails = $this->db()->query()
      ->addSql('select num, name_ru, count, cost*rate as cost, count*cost*rate as sum from orders_details_vw')
      ->addSql('where order_id = $1')
      ->addSql('order by odd_id_pk')
      ->addParam($id)
      ->fetchResult(false);


    if ( !empty($orderDetails) ) {
      $countDetails = count($orderDetails);
      if ( 1 < $countDetails ) {
        $worksheet->insertNewRowBefore(23, $countDetails - 1);
      }

      $worksheet->setCellValue('I21', $orderInfo['currency']);
      $worksheet->setCellValue('J21', $orderInfo['currency']);
      $worksheet->setCellValue('J' . (22 + $countDetails), $orderInfo['sum_subtotal']);
      $worksheet->setCellValue('J' . (25 + $countDetails), $orderInfo['discount']);
      $worksheet->setCellValue('J' . (28 + $countDetails), $orderInfo['sum_delivery']);
      $worksheet->setCellValue('J' . (31 + $countDetails), $orderInfo['sum_total']);
      $worksheet->setCellValue('C' . (26 + $countDetails), $orderInfo['payment_name_ru']);
      $worksheet->setCellValue('B' . (26 + $countDetails), $orderInfo['delivery_name_ru']);

      $i = 22;
      foreach ($orderDetails as $row) {
        $worksheet->mergeCells('C'. $i .':F'. $i);
        $worksheet->mergeCells('G'. $i .':H'. $i);
        $worksheet->setCellValue('B' . $i, $row['num']);
        $worksheet->setCellValue('C' . $i, $row['name_ru']);
        $worksheet->setCellValue('G' . $i, $row['count']);
        $worksheet->setCellValue('I' . $i, $row['cost']);
        $worksheet->setCellValue('J' . $i, $row['sum']);

        $i++;
      }
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="order-' . $id . '.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter->save('php://output');

    return true;
  } // }}}

  public function quickBuy($id) { // {{{
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'quick_buy';

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

    if ( !isset($data['color']) ) {
      $data['color'] = '';
    }
    if ( !isset($data['size']) ) {
      $data['size'] = '';
    }

    $order_num = $this->db()->query()
      ->addSql('insert into orders_tbl(ord_usr_id_fk, ord_user_name,')
      ->addSql('ord_user_email, ord_user_phones, ord_lng_id_fk, ord_currency, ord_rat_id_fk)')
      ->addSql('values')
      ->addSql('($1, $2, $3, $4,')
      ->addSql('(select lng_id_pk from languages_tbl')
      ->addSql('  where lower(lng_synonym) = lower($5)), $6,')
      ->addSql('(select rat_id_pk from rates_tbl where lower(rat_synonym)=lower($6)))')
      ->addParam(null)
      ->addParam($data['name'])
      ->addParam($data['email'])
      ->addParam($data['phone'])
      ->addParam($data['lang'])
      ->addParam($data['currency'])
      ->execute('ord_id_pk');

    $this->db()->query()
      ->addSql('insert into orders_details_tbl')
      ->addSql('(odd_ord_id_fk, odd_dpt_id_fk, odd_count, odd_dac_id_fk,')
      ->addSql('odd_das_id_fk)')
      ->addSql('values')
      ->addSql('($1, $2, $3, $4, $5)')
      ->addParam($order_num)
      ->addParam($id)
      ->addParam($data['count'])
      ->addParam($data['color'])
      ->addParam($data['size'])
      ->execute();

    $data = $this->db()->query()
      ->addSql('select num, date, user_name, user_email, language, currency,')
      ->addSql('user_phones, detail_count, detail_cost, detail_sum,')
      ->addSql('detail_name_#lang# as detail_name, detail_id, car_name_#lang# as car_name,')
      ->addSql('detail_image, detail_num, autopart_id, car_synonym')
      ->addSql('from quick_order_info_vw')
      ->addSql('where num = $1')
      ->addParam($order_num)
      ->fetchRow(0, false);

    if ($data['language'] != 'ru') {
      $data['lng_domain'] = $data['language'] . '.';
    }

    $this->_sendCreateForAdminMail(true, $data);

    return [
      'order_num' => $order_num
    ];
  } // }}}



  // private function _savePreorder($email, $data) { // {{{
  //   // throw new RouteException;
  //   if ( !isset($data['id']) ) {
  //     $data['id'] = '';
  //   }
  //   if ( !isset($data['method']) ) {
  //     $data['method'] = '';
  //   }
  //   if ( !isset($data['color']) ) {
  //     $data['color'] = null;
  //   }
  //   if ( !isset($data['size']) ) {
  //     $data['size'] = null;
  //   }
  //
  //   if ( '0' == $data['color'] ) {
  //     $data['color'] = null;
  //   }
  //   if ( '0' == $data['size'] ) {
  //     $data['size'] = null;
  //   }
  //
  //
  //   $exists = $this->db()->query()
  //     ->addSql('select ord_id_pk, odd_id_pk, products_in_db_fn(ord_id_pk::int) as products_in_db from orders_tbl')
  //     ->addSql('left join orders_details_tbl on odd_ord_id_fk = ord_id_pk and odd_dpt_id_fk = $3 and coalesce(odd_dac_id_fk,0) = coalesce($4,0) and coalesce(odd_das_id_fk,0) = $5')
  //     ->addSql('where ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1)')
  //     ->addSql('and ord_status = $2')
  //     ->addParam($email)
  //     ->addParam('not_complete')
  //     ->addParam($data['id'])
  //     ->addParam((int)$data['color'])
  //     ->addParam((int)$data['size'])
  //     ->fetchRow(0, false);
  //
  //   if ( empty($exists) ) {
  //     $id = $this->db()->query()
  //       ->addSql('insert into orders_tbl(ord_usr_id_fk, ord_status, ord_rat_id_fk,')
  //       ->addSql('ord_lng_id_fk)values((select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1),')
  //       ->addSql('$2,')
  //       ->addSql('(select rat_id_pk from rates_tbl where rat_synonym = $3),')
  //       ->addSql('(select lng_id_pk from languages_tbl where lng_synonym = $4))')
  //       ->addParam($email)
  //       ->addParam('not_complete')
  //       ->addParam($this->_getCurrentCurrency())
  //       ->addParam($this->getVariable('current_language'))
  //       ->execute('ord_id_pk');
  //
  //     if ( !empty($data) ) {
  //       if ( $data['method'] != 'add' && $data['method'] != 'change' && $data['method'] != 'delete') {
  //         foreach ($data['products'] as $product) {
  //           if (0 == $product['size']) {
  //             $product['size'] = null;
  //           }
  //           if (0 == $product['color']) {
  //             $product['color'] = null;
  //           }
  //           $this->db()->query()
  //             ->addSql('insert into orders_details_tbl(odd_ord_id_fk, odd_dpt_id_fk,')
  //             ->addSql('odd_count, odd_dac_id_fk, odd_das_id_fk)values($1, $2, $3, $4, $5)')
  //             ->addParam($id)
  //             ->addParam($product['id'])
  //             ->addParam($product['count'])
  //             ->addParam($product['color'])
  //             ->addParam($product['size'])
  //             ->execute();
  //         }
  //       }
  //     }
  //   } else {
  //     $id = $exists['ord_id_pk'];
  //   }
  //
  //   // $this->db()->query()
  //   //   ->addSql('delete from orders_details_tbl')
  //   //   ->addSql('where odd_ord_id_fk = $1')
  //   //   ->addParam($id)
  //   //   ->execute();
  //
  //   // if ( empty($data) ) {
  //   //   return $this;
  //   // }
  //
  //   if ('add' == $data['method']) {
  //     if ( empty($exists) || empty($exists['odd_id_pk']) ) {
  //   // var_dump($email, $data);
  //       $this->db()->query()
  //         ->addSql('insert into orders_details_tbl(odd_ord_id_fk, odd_dpt_id_fk,')
  //         ->addSql('odd_count, odd_dac_id_fk, odd_das_id_fk)values($1, $2, $3, $4, $5)')
  //         ->addParam($id)
  //         ->addParam($data['id'])
  //         ->addParam((int)$data['count'])
  //         ->addParam($data['color'])
  //         ->addParam($data['size'])
  //         ->execute();
  //     } else {
  //       $this->db()->query()
  //         ->addSql('update orders_details_tbl set odd_count = odd_count + $2 where odd_id_pk = $1')
  //         ->addParam($exists['odd_id_pk'])
  //         ->addParam((int)$data['count'])
  //         ->execute();
  //     }
  //   } else
  //   if ('delete' == $data['method']) {
  //     $this->db()->query()
  //       ->addSql('delete from orders_details_tbl where odd_ord_id_fk = $1 and odd_dpt_id_fk = $2')
  //       ->addSql('and coalesce(odd_dac_id_fk,0) = coalesce($3,0) and coalesce(odd_das_id_fk,0) = coalesce($4,0)')
  //       ->addParam($id)
  //       ->addParam($data['id'])
  //       ->addParam($data['color'])
  //       ->addParam($data['size'])
  //       ->execute();
  //   } else
  //   if ('change' == $data['method']) {
  //     $this->db()->query()
  //       ->addSql('update orders_details_tbl set odd_count = odd_count + $5 where odd_ord_id_fk = $1 and odd_dpt_id_fk = $2')
  //       ->addSql('and coalesce(odd_dac_id_fk,0) = coalesce($3,0) and coalesce(odd_das_id_fk,0) = coalesce($4,0)')
  //       ->addParam($id)
  //       ->addParam($data['id'])
  //       ->addParam($data['color'])
  //       ->addParam($data['size'])
  //       ->addParam((int)$data['count'])
  //       ->execute();
  //   }
  //
  //
  //   setcookie('bsk-products', json_encode($data['products']), 2147483647, '/', self::$DOMAIN);
  //   return $id;
  // } // }}}


  public function changeStatus($params = []) { // {{{
    $data = $this->db()->query()
      ->addSql('select ord_status as status, coalesce(ord_user_email, usr_email) as user_email,')
      ->addSql('ord_num as num, coalesce(ord_user_name, usr_name) as user_name, ord_status as status, ord_status, ord_tracking_number as tracking_number,')
      ->addSql('lng_synonym as lang, ord_dvm_id_fk')
      ->addSql('from orders_tbl')
      ->addSql('left join languages_tbl on ord_lng_id_fk = lng_id_pk')
      ->addSql('left join users_tbl on usr_id_pk = ord_usr_id_fk')
      ->addSql('where ord_id_pk = $1')
      ->addParam($params['id'])
      ->fetchRow(0, false);


    if ($data['status'] == $params['ord_status']) {
      return $this;
    }

    $fileLanguage = dirname($this->getLanguageFile()) . '/' . $data['lang'] . '.xml';
    $configLoader = new Xml;
    $configLoader->setFileSettings($fileLanguage);
    $config_values = $configLoader->getValues();

    $subject = $config_values['mails']['order_change_status']['subject'];
    $body = $config_values['mails']['order_change_status']['body'];
    $status = $config_values['state'][ $params['ord_status'] ];

    $data['ord_status'] = $params['ord_status'];
    $data['tracking_number'] = $params['ord_tracking_number'];
    $templater = new Templater;
    $templater->load($subject);
    $subject = $templater->parse($data);

    $data['status'] = $status;
    $templater = new Templater;
    $templater->load($body);
    $body = $templater->parse($data);

    // Получаем имя и email от которого будут оправляться письма
    if ( false === $mailerName = $this->getVariable('stg_mail_name') ) {
      $mailerName = null;
    }
    $mailerEmail = $this->getVariable('stg_mail_email');

    $managers = $this->getVariable('stg_general_administrator_email');
    // Отправляем почту на email админу
    $settings = Registry::get('stg');
    $mail = new Mail($settings['mail']['smtp']);
    $mail->setFromEmail($mailerEmail, $mailerName)
      ->addEmail($data['user_email'])
      ->addEmail( explode(',', $managers) )
      ->setSubject($subject)
      ->setText($body)
      ->send();

    $user = $this->db()->query()
      ->addSql('select usr_id_pk from users_tbl where lower(usr_email) = lower($1)')
      ->addParam($data['user_email'])
      ->fetchRow(0, false);

    if (!empty($user)) {
      $this->createModel('mailer')
        ->saveMail('user', $user['usr_id_pk'], 'create_order', $data['user_email'],
          $subject, $body);
    }

    return $this;
  } // }}}


  // public function getBasket() { // {{{
    // $client_order = json_decode(
    //   html_entity_decode($this->getRequest()->get('products')), true);
    //
    // setcookie('bsk-products', json_encode($client_order['products']), 2147483647, '/', self::$DOMAIN);
    // $auth = Auth::getInstance();
    // if ( !$auth->hasIdentity() ) {
    //   return null;
    // }
    //
    // $client_products = $client_order['products'];
    //
    // if ( empty($client_products) ) {
    //   return null;
    // }
    //
    // $order = [
    //   'sum' => $client_order['sum'],
    //   'sumUsd' => $client_order['sumUsd'],
    //   'promocode' => $client_order['promocode'],
    //   'count' => $client_order['count'],
    // ];
    //
    // $email = $auth->getStorage()->identity;
    //
    // $details = [];
    // $db_details = $this->db()->query()
    //   ->addSql('select odd_dpt_id_fk as id, odd_count as count,')
    //   ->addSql('coalesce(odd_dac_id_fk,0) as color, coalesce(odd_das_id_fk,0) as size')
    //   ->addSql('from orders_details_tbl')
    //   ->addSql('where odd_ord_id_fk = (select ord_id_pk from orders_tbl')
    //   ->addSql('where ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1)')
    //   ->addSql('and ord_status = $2) limit 1')
    //   ->addParam($email)
    //   ->addParam('not_complete')
    //   ->fetchResult(false);
    //
    // if ( empty($db_details) ) {
    //   $order['products'] = $client_products;
    //   $this->_savePreorder($email, $client_products);
    //
    //   return $order;
    // }
    //
    // foreach ($db_details as $detail) {
    //   $details[$detail['id'] . '-' . $detail['size'] . '-' .
    //     $detail['color']] = $detail;
    // }
    //
    // if (md5(json_encode($details)) == md5(json_encode($client_products))) {
    //   $order['products'] = $details;
    //
    //   return $order;
    // }
    //
    // // Если товары не совпадают - совмещаем их и пишем в заказ
    // foreach ($client_products as $client_product_id => $client_product) {
    //   if ( isset($details[$client_product_id]) ) {
    //     $details[$client_product_id]['count'] = max(
    //       $details[$client_product_id]['count'], $client_product['count']
    //     );
    //
    //     continue;
    //   }
    //
    //   $details[$client_product_id] = $client_product;
    // }
    //
    // $order_id = $this->_savePreorder($email, $details);
    // $order = $this->_getOrderData($order_id);
    // $order['products'] = $details;
    //
    // return $order;
  // } // }}}



  public function getBasketPage() { // {{{
    $page = (int)$this->getRequest()->getParam('page');

    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      return ['page' => 0];
    }

    $order_id = $this->_getOrderId();
    $email = $auth->getStorage()->identity;
    $data = $this->db()->query()
      ->addSql('select coalesce(ord_user_name, usr_name) as name,')
      ->addSql('coalesce(ord_user_email, usr_email) as email,')
      ->addSql('coalesce(ord_user_phones, usr_phones) as phone,')
      ->addSql('coalesce(ord_user_city, usr_city) as city,')
      ->addSql('coalesce(ord_user_index, usr_index) as index,')
      ->addSql('coalesce(ord_user_street, usr_street) as street,')
      ->addSql('coalesce(ord_user_build, usr_build) as build,')
      ->addSql('coalesce(ord_user_flat, usr_flat) as flat,')
      ->addSql('coalesce(ocnt.cnt_name_#lang#, ucnt.cnt_name_#lang#) as country,')
      ->addSql('coalesce(ord_user_cnt_id_fk, ucnt.cnt_id_pk) as cnt_id_pk,')
      ->addSql('coalesce(ocnt.cnt_synonym, ucnt.cnt_synonym) as cnt_synonym')
      ->addSql('from users_tbl')
      ->addSql('left join countries_tbl ucnt on ucnt.cnt_id_pk = usr_cnt_id_fk')
      ->addSql('left join orders_tbl on ord_usr_id_fk = usr_id_pk and ord_status = $2 and ord_id_pk = $3')
      ->addSql('left join countries_tbl as ocnt on ocnt.cnt_id_pk = ord_user_cnt_id_fk')
      ->addSql('where usr_email=$1 or usr_social_id = $1 limit 1')
      ->addParam($email)
      ->addParam('not_complete')
      ->addParam($order_id)
      ->fetchRow(0, false);

    $weight = $this->db()->query()
      ->addSql('SELECT sum(count*coalesce(weight,0)) as total_weight')
      ->addSql('from orders_details_vw')
      ->addSql('where order_id=(select ord_id_pk from orders_tbl where')
      ->addSql('ord_status=$2 and')
      ->addSql('ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1) and ord_id_pk = $3)')
      ->addParam($email)
      ->addParam('not_complete')
      ->addParam($order_id)
      ->fetchRow(0, false);

    $weight = $weight['total_weight'];

//     if ( empty($data['cnt_id_pk'])) {
//       $data['cnt_id_pk'] = 0;
//     }
//
//     if ( empty($data['email'])) {
//       $data['email'] = $email;
//     }
//
//     if ( empty($data['name'])) {
//       $data['name'] = '';
//     }
//     if ( empty($data['city'])) {
//       $data['city'] = '';
//     }
//     if ( empty($data['index'])) {
//       $data['index'] = '';
//     }
//     if ( empty($data['street'])) {
//       $data['street'] = '';
//     }
//     if ( empty($data['build'])) {
//       $data['build'] = '';
//     }
//     if ( empty($data['flat'])) {
//       $data['flat'] = '';
//     }
//     if ( empty($data['phone'])) {
//       $data['phone'] = '';
//     }
    $countries = $this->db()->query()
      ->addSql('select cnt_name_#lang# as name, cnt_id_pk as id,')
      ->addSql('case when cnt_id_pk = $1 then 1 else null end selected')
      ->addSql('from countries_tbl')
      ->addSql('where cnt_enabled = true order by coalesce(cnt_prior, 9999), cnt_name_#lang#')
      ->addParam($data['cnt_id_pk'])
      ->fetchResult(false);

    $data['countries'] = $countries;

    if (strpos($data['email'], '@') === false) {
      $data['email'] = null;
    }

    $name_email_empty = false;
    if ( empty($data['name']) or empty($data['email']) ) {
      $name_email_empty = true;
    }

    $currency = $this->_getCurrency();

    if (
      empty($data['email']) or
      empty($data['phone']) or
      empty($data['name']) or
      empty($data['country']) or
      empty($data['city']) or
      empty($data['index']) or
      empty($data['street']) or
      empty($data['build']) or
      empty($data['flat'])
    ) {
      return [
        'page' => -1,
        'name' => $data['name'],
        'email' => $data['email'],
        'countries' => $data['countries'],
        'city' => $data['city'],
        'index' => $data['index'],
        'street' => $data['street'],
        'build' => $data['build'],
        'flat' => $data['flat'],
        'phone' => $data['phone'],
        'name_email_empty' => $name_email_empty,
      ];
    }

    if ($page <= 0) {
      $page = 1;
    }

    if ($page == 3 && !empty($weight)) {
      $page = 2;
    }

    // var_dump($data['city']);
    return [
      'page' => $page,
      'currency_abb' => $currency['short_name'],
      'userParams' => [
        'type' => 'edit',
        'country' => $data['cnt_synonym'],
        'city' => $data['city'],
        'weight' => $weight,
      ],
    ];
  } // }}}


  public function createOrder() { // {{{
    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      return null;
    }
    $email = $auth->getStorage()->identity;

    $data = $this->getRequest()->post();

    if(isset($data['with_advets']) && intval($data['with_advets']) == 1) {
      $withAdverts = 1;
    } else { $withAdverts =  0; }

    //$withAdverts = 1;
    //return $data;
    $sql_query = "update orders_tbl set ord_status = $3, ord_note = $4,ord_dvm_id_fk = $5, ord_advert_in_order = $9,ord_discount = $8,";

    $num = $this->db()->query()
      ->addSql('update orders_tbl set ord_datetime = now(), ord_status = $3, ord_note = $4, ')
      ->addSql('ord_dvm_id_fk = $5, ord_advert_in_order = $9,')
      ->addSql('ord_discount = $8,')
      ->addSql('ord_sum_delivery = coalesce(')
      ->addSql('  (select case')
      ->addSql('    when type = \'ukrposhta\' then ')
      ->addSql('    (select coalesce(cnt_onetime_tariff,0) + coalesce(cnt_kg_tariff,0) *')
      ->addSql('      (select sum(odd_count*(select dpt_weight from details_autoparts_tbl where dpt_id_pk = odd_dpt_id_fk)) from orders_details_tbl where odd_ord_id_fk = ord_id_pk)')
      ->addSql('      from countries_tbl')
      ->addSql('      where cnt_id_pk = coalesce(ord_user_cnt_id_fk, (select usr_cnt_id_fk from users_tbl where usr_id_pk = ord_usr_id_fk))')
      ->addSql('    )')
      ->addSql('    when type = \'conductor\' then ')
      ->addSql('    (select coalesce(cnt_onetime_tariff,0) + coalesce(cnt_kg_tariff,0) *')
      ->addSql('      (select sum(odd_count*(select dpt_weight from details_autoparts_tbl where dpt_id_pk = odd_dpt_id_fk)) from orders_details_tbl where odd_ord_id_fk = ord_id_pk)')
      ->addSql('      from countries_tbl')
      ->addSql('      where cnt_id_pk = coalesce(ord_user_cnt_id_fk, (select usr_cnt_id_fk from users_tbl where usr_id_pk = ord_usr_id_fk))')
      ->addSql('    )')
      ->addSql('    when type = \'ups\' then ')
      ->addSql('    (select (coalesce(cnt_onetime_tariff,0) + coalesce(cnt_kg_tariff,0) *')
      ->addSql('      (select sum(odd_count*(select dpt_weight from details_autoparts_tbl where dpt_id_pk = odd_dpt_id_fk)) from orders_details_tbl where odd_ord_id_fk = ord_id_pk))*5')
      ->addSql('      from countries_tbl')
      ->addSql('      where cnt_id_pk = coalesce(ord_user_cnt_id_fk, (select usr_cnt_id_fk from users_tbl where usr_id_pk = ord_usr_id_fk))')
      ->addSql('    )')
      ->addSql('  else cost end')
      ->addSql('  from delivery_methods_vw where id = $5),0),')
      ->addSql('ord_pym_id_fk = $6,')
      ->addSql('ord_rat_id_fk = (select rat_id_pk from rates_tbl where rat_synonym = $7)')
      ->addSql('where ord_status=$1 and')
      ->addSql('ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $2 or usr_social_id = $2 limit 1)')
      ->addParam('not_complete')
      ->addParam($email)
      ->addParam('wait_payment')
      ->addParam(($data['comment'])?$data['comment']:NULL)
      ->addParam($data['delivery'])
      ->addParam($data['payment'])
      ->addParam($this->_getCurrentCurrency())
      ->addParam($this->getUserDiscountValue())
      ->addParam($withAdverts)
      ->execute('ord_num');

    //return $num;
    // remove delivery method if pay for adv
    $adv_tmp = $this->db()->query()
      ->sql('select num from orders_details_vw')
      ->addSql('where order_id = $1')
      ->addParam($num)
      ->fetchRow();

    if($adv_tmp && $adv_tmp['num'] == 10009009001){
      $tmp = $this->db()->query()
        ->sql('update orders_tbl set ord_dvm_id_fk = NULL')
        ->addSql("where ord_num = $1")
        ->addParam($num)
        ->execute();
    }

    // Отправляю письмо
    $data = $this->db()->query()
      ->addSql('select num, date, user_name, user_email, user_phones,')
      ->addSql('user_country_#lang# as user_country, user_city, user_index,')
      ->addSql('user_street, user_build, user_flat, status, note,')
      ->addSql('sum_usd, sum_usd_unformat, sum, discount, sum_discount_usd, sum_discount,')
      ->addSql('sum_delivery_usd, sum_delivery_usd_unformat, sum_delivery, rate, currency,')
      ->addSql('payment_name_#lang# as payment_name, payment_type,')
      ->addSql('delivery_name_#lang# as delivery_name, language, total_sum,')
      ->addSql('total_sum_unformat+sum_delivery_unformat as total_sum_unformat,')
      ->addSql('total_sum_uah+sum_delivery_uah as total_sum_uah')
      ->addSql('from order_info_vw')
      ->addSql('where num = $1')
      ->addParam($num)
      ->fetchRow(0, false);

    // var_dump($data['payment_type']);
    if ($data['language'] != 'ru') {
      $data['lng_domain'] = $data['language'] . '.';
    }

    $details = $this->db()->query()
      ->addSql('SELECT id, name_#lang# as name, num, status, image_mini as image,')
      ->addSql('count, color_#lang# as color, size_#lang# as size, autopart_id,')
      ->addSql('(cost*ord_rate) as cost_unformat, car_name_#lang# as car_name,')
      ->addSql('car_synonym, cost as cost_usd, color_id, size_id,\'' . $data['currency'] . '\' as currency,')
      ->addSql('replace(replace(trim(to_char((cost*ord_rate),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as cost,')
      ->addSql('replace(replace(trim(to_char((cost*count*ord_rate),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as sum,')
      ->addSql('\'' . $this->getVariable('url_staticServer') . '\' as url_staticServer')
      ->addSql('from orders_details_vw')
      ->addSql('left join orders_tbl on order_id = ord_id_pk')
      ->addSql('left join users_tbl on usr_id_pk = ord_usr_id_fk')
      ->addSql('left join autoparts_tbl on apt_id_pk = autopart_id')
      ->addSql('left join cars_tbl on car_id_pk = apt_car_id_fk')
      ->addSql('where order_id = $1')
      ->addParam($num)
      ->fetchResult(false);

    $data['details'] = $details;

    if ( !empty($details) && isset($data['num']) && !empty($data['num']) ) {
      $this->_sendCreateForAdminMail(false, $data);
    }

    $state = 'finish';
    $form = null;
    // Возвращаю нужные данные для онлайн оплат
    // Visa
    $result_url = '/order-info/' . md5(md5($num) . $email) . '/';
    if ($data['payment_type'] == 'visa') {
      // $state = 'portmone';
      // $cancel_url = '/order-info/' . md5(md5($num) . $email) . '/continue/';
      //
      // $form = $this->getPortmone($num, $data['total_sum_uah'],
      //   $result_url, $cancel_url);

      $state = 'liqpay';
      $host = 'http://' . $_SERVER['HTTP_HOST'];
      $server_url = '/order-set-status/visa/';

      if (strtolower($data['currency']) == 'rur') {
        $data['currency'] = 'RUR';
      }

      $this->db()->query()
        ->addSql('update orders_tbl set ord_platon_sum_verify = $2')
        ->addSql('where ord_num = $1')
        ->addParam($num)
        ->addParam($data['total_sum_uah'])
        ->execute();

      $form = $this->getPlaton($num, $data['total_sum_uah'], $data['total_sum_unformat'],
        $data['currency'], $host . $result_url, $host . $server_url);
    }

    // Privat24
    if ($data['payment_type'] == 'private24') {
      $state = 'private24';
      $host = 'http://' . $_SERVER['HTTP_HOST'];
      $server_url = '/order-set-status/private24/';

      $form = $this->getPrivate24($num, $data['total_sum_uah'],
        $host . $result_url, $host . $server_url);
    }

    // PayPal
    if ($data['payment_type'] == 'paypal') {
      $success_url = '/order-info/' . md5(md5($num) . $email) . '/';
      $cancel_url = '/order-info/' . md5(md5($num) . $email) . '/continue/';
      $result_url = $this->getPayPal($num,
        $data['sum_delivery_usd_unformat'],
        $data['sum_usd_unformat'],
        $success_url, $cancel_url);
    }

    setcookie('bsk-products', null, time()-3600, '/', self::$DOMAIN);

    return [
      'state' => $state,
      'redirect' => $result_url,
      'num' => $num,
      'form' => $form,
      'test' => $this->getUserDiscountValue(),
    ];
  } // }}}

  public function getBasketCreatedOrderInfo() { // {{{
    return [];
  } // }}}

  public function promocode() { // {{{
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'promocode';

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();

    $errors = $validator->validate($form, $rules, $data, $lang_variables);
    if ($data['promocode'] != $this->getVariable('stg_discount_code')) {
      $errors[] = [
        'id' => 'promocode',
        'text' => $lang_variables['lng_validate_promocode_promocode_not']
      ];
    }

    if ( !empty($errors) ) {
      $errors['errors'] = true;
    }


    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      return null;
    }
    $email = $auth->getStorage()->identity;

    $this->db()->query()
      ->addSql('update orders_tbl set ord_discount = $3')
      ->addSql('where ord_status=$1 and')
      ->addSql('ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $2 or usr_social_id = $2 limit 1)')
      ->addParam('not_complete')
      ->addParam($email)
      ->addParam( $this->getVariable('stg_discount_value') )
      ->execute();

    return ['value' => $this->getVariable('stg_discount_value')];
  } // }}}

  public function getOrderInfo() { // {{{
    $num = $this->getRequest()->getParam('num');
    $type = $this->getRequest()->getParam('type');

    $data = $this->db()->query()
      ->addSql('select id, num, date, user_name, user_email, user_phones,')
      ->addSql('user_country_#lang# as user_country, user_city, user_index,')
      ->addSql('user_street, user_build, user_flat, status, note, paypal_id,')
      ->addSql('sum_usd, sum, discount, sum_discount_usd, sum_discount,')
      ->addSql('sum_delivery_usd, sum_delivery, rate, currency_abb,')
      ->addSql('payment_name_#lang# as payment_name, user_country_synonym,')
      ->addSql('delivery_name_#lang# as delivery_name, language, total_sum,')
      ->addSql('coalesce(sum_usd_unformat,0)+coalesce(sum_delivery_usd_unformat,0) as total_sum_usd')
      ->addSql('from order_info_vw')
      ->addSql('where md5(md5(num::VARCHAR)||user_email) = $1')
      ->addParam($num)
      ->fetchRow(0, false);

    $name_arr = explode(' ', $data['user_name']);
    $data['user_surname'] = $name_arr[0];
    $data['user_secondname'] = implode(' ', array_slice($name_arr, 1));
    $data['type'] = $type;

    if ( !empty($data['paypal_id']) && $data['status'] != 'paid' && $data['status'] != 'success' && isset( $_GET['PayerID'] ) ) {
      $apiContext = new ApiContext(
        new OAuthTokenCredential(
          $this->getVariable('stg_paypal_username'),
          $this->getVariable('stg_paypal_password')
        )
      );
      $payerId = $_GET['PayerID'];
      $apiContext->setConfig([ 'mode' => 'live']);
      $payment = Payment::get($data['paypal_id'], $apiContext);
      $paymentExecution= new PaymentExecution();
      $paymentExecution->setPayerId($payerId);
      $payment->execute($paymentExecution, $apiContext);
      $paypal_sum = (int)$payment->getTransactions()[0]->getAmount()->getTotal();
      $db_sum = (int)$data['total_sum_usd'];

      ob_start();
      var_dump($paypal_sum);
      var_dump($db_sum);
      var_dump($payment->getState());
      $result = ob_get_clean();
      file_put_contents('/tmp/ppay2.txt', $result);

      if ($paypal_sum == $db_sum && $payment->getState() == 'approved') {
        $this->db()->query()
          ->addSql('update orders_tbl set ord_status = $2 where ord_id_pk = $1')
          ->addParam($data['id'])
          ->addParam('paid')
          ->execute();

        $this->changeStatus([
          'ord_status' => 'paid',
          'id' => $data['id']
        ]);
      }
    }

    $products = $this->db()->query()
      ->addSql('SELECT id, name_#lang# as name, num, status, image_small,')
      ->addSql('count, color_#lang# as color, size_#lang# as size,')
      ->addSql('(cost*ord_rate*$2) as cost_unformat, rat_short_name_#lang# as currency_abb,')
      ->addSql('discount as discount,')
      ->addSql('(cost*$2) as cost_usd, color_id, size_id,')
      ->addSql('replace(replace(trim(to_char((cost*ord_rate*$2),\'999 999 999.99\')), \'.\', \',\'), \',00\', \'\') as cost')
      ->addSql('from orders_details_vw')
      ->addSql('left join orders_tbl on order_id = ord_id_pk')
      ->addSql('left join users_tbl on usr_id_pk = ord_usr_id_fk')
      ->addSql('left join rates_tbl on rat_id_pk = ord_rat_id_fk')
      ->addSql('where md5(md5(order_id::VARCHAR)||coalesce(ord_user_email, usr_email)) = $1')
      ->addParam($num)
      ->addParam($this->getUserDiscount())
      ->fetchResult(false);

    $data['products'] = $products;

    $weight = $this->db()->query()
      ->addSql('SELECT sum(count*coalesce(weight,0)) as total_weight')
      ->addSql('from orders_details_vw')
      ->addSql('left join orders_tbl on order_id = ord_id_pk')
      ->addSql('left join users_tbl on usr_id_pk = ord_usr_id_fk')
      ->addSql('where md5(md5(order_id::VARCHAR)||coalesce(ord_user_email, usr_email))=$1')
      ->addParam($num)
      ->fetchRow(0, false);

    $weight = $weight['total_weight'];
    $data['userParams'] = [
      'type' => 'edit',
      'country' => $data['user_country_synonym'],
      'city' => $data['user_city'],
      'weight' => $weight,
      'ord_num' => isset($products[0]['num'])?$products[0]['num']:false
    ];
    // var_dump($data['userParams']);
      // var_dump($data);

    return $data;
  } // }}}

  public function getPaypal($order_id, $shipping_sum, $details_sum, $result_url, $cancel_url) { // {{{
    $apiContext = new ApiContext(
      new OAuthTokenCredential(
        $this->getVariable('stg_paypal_username'),
        $this->getVariable('stg_paypal_password')
      )
    );
    $apiContext->setConfig(
      array(
        'mode' => 'live',
        // 'mode' => 'sandbox',
        // 'http.ConnectionTimeOut' => 30,
      )
    );

    // var_dump($this->getVariable('stg_paypal_username'));
    $payer = new Payer();
    $payer->setPaymentMethod("paypal");

    $item = new Item();
    $item->setName($this->getVariable('lng_order_description') . $order_id)
      ->setCurrency('EUR')
      ->setQuantity(1)
      ->setPrice($details_sum);

    $itemList = new ItemList();
    $itemList->setItems([$item]);

    $details = new Details();
    $details->setShipping($shipping_sum)
      ->setSubtotal($details_sum);

    $amount = new Amount();
    $amount->setCurrency("EUR")
      ->setTotal($shipping_sum + $details_sum)
      ->setDetails($details);

    // var_dump($items);
    // var_dump($shipping_sum, $details_sum);
    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($itemList)
      ->setDescription( $this->getVariable('lng_order_description')
            . $order_id);

    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($baseUrl . $result_url)
      ->setCancelUrl($baseUrl . $cancel_url);

    $payment = new Payment();
    $payment->setIntent("sale")
      ->setPayer($payer)
      ->setRedirectUrls($redirectUrls)
      ->setTransactions([$transaction]);

    $payment->create($apiContext);

    // var_dump($payment->getLinks());
    foreach($payment->getLinks() as $link) {
      if($link->getRel() == 'approval_url') {
        $redirectUrl = $link->getHref();
        break;
      }
    }

    $this->db()->query()
      ->addSql('update orders_tbl set ord_paypal_id = $2 where ord_num = $1')
      ->addParam($order_id)
      ->addParam( $payment->getId() )
      ->execute();

    return $redirectUrl;
  } // }}}

  public function getLiqpay($order_id, $amount, $currency, $result_url, $server_url) { // {{{
    $public_key = $this->getVariable('stg_liqpay_id');
    $private_key = $this->getVariable('stg_liqpay_password');

    if ($currency == 'rur') {
      $currency = 'rub';
    }

    $support_languages = ['ru' => true, 'en' => true];
    $language = $this->getVariable('current_language');

    if ( !isset($support_languages[$language]) ) {
      $language = 'en';
    }

    $liqpay = new LiqPay($public_key, $private_key);
    $form = $liqpay->getForm([
      'order_id' => $order_id,
      'amount' => $amount,
      'currency' => strtoupper($currency),
      'language' => $language,
      'result_url' => $result_url,
      'server_url' => $server_url,
      'description' => $this->getVariable('lng_order_description')
        . $order_id,
    ]);

    return $form;
  } // }}}

  public function getPlaton($order_id, $amount, $amount_original, $currency, $result_url, $server_url) { // {{{
    $url = $this->getVariable('stg_platon_url');
    $merchant_id = $this->getVariable('stg_platon_id');
    $merchant_password = $this->getVariable('stg_platon_password');

    $support_languages = ['ru' => true, 'en' => true];
    $language = $this->getVariable('current_language');

    if ( !isset($support_languages[$language]) ) {
      $language = 'en';
    }

    $platon = new Platon($url, $merchant_id, $merchant_password);
    $form = $platon->getForm([
      'order_id' => $order_id,
      'amount' => $amount,
      'amount_original' => $amount_original,
      'currency' => strtoupper($currency),
      'language' => $language,
      'result_url' => $result_url,
      'server_url' => $server_url,
      'description' => $this->getVariable('lng_order_description')
        . $order_id,
    ]);

    return $form;
  } // }}}

  public function equeringSetStatus() { // {{{
    file_put_contents('/tmp/qqq.txt', $_GET);
    file_put_contents('/tmp/qqq1.txt', $_POST);
  } // }}}

  public function continueOrder() { // {{{
    $id = $this->getRequest()->getParam('id');
    $params = $this->getRequest()->post();

    $this->db()->query()
      ->addSql('update orders_tbl set ord_pym_id_fk = $2')
      ->addSql('where ord_id_pk = $1')
      ->addParam($id)
      ->addParam($params['payment'])
      ->execute();

    $data = $this->db()->query()
      ->addSql('select user_email, currency,')
      ->addSql('total_sum_unformat+sum_delivery_unformat as total_sum_unformat,')
      ->addSql('sum_usd_unformat, sum_delivery_usd_unformat,')
      ->addSql('total_sum_uah+sum_delivery_uah as total_sum_uah')
      ->addSql('from order_info_vw')
      ->addSql('where num = $1')
      ->addParam($id)
      ->fetchRow(0, false);

    $details = $this->db()->query()
      ->addSql('SELECT id, name_#lang# as name, count,')
      ->addSql('round(cost) as cost_usd')
      ->addSql('from orders_details_vw')
      ->addSql('where order_id = $1')
      ->addParam($id)
      ->fetchResult(false);

    $state = 'finish';
    $form = null;
    // Возвращаю нужные данные для онлайн оплат
    // Visa
    $result_url = '/order-info/' . md5(md5($id) . $data['user_email']) . '/';
    if ($params['payment'] == 1) {
      // $state = 'portmone';
      // $cancel_url = $result_url . 'continue/';
      //
      // $form = $this->getPortmone($id, $data['total_sum_uah'],
      //   $result_url, $cancel_url);

      $state = 'liqpay';
      $host = 'http://' . $_SERVER['HTTP_HOST'];
      $server_url = '/order-set-status/visa/';

      $this->db()->query()
        ->addSql('update orders_tbl set ord_platon_sum_verify = $2')
        ->addSql('where ord_num = $1')
        ->addParam($id)
        ->addParam($data['total_sum_uah'])
        ->execute();

      $form = $this->getPlaton($id . '-' . time(), $data['total_sum_uah'], $data['total_sum_unformat'],
        $data['currency'], $host . $result_url, $host . $server_url);
    }

    // Privat24
    if ($params['payment'] == 9) {
      $state = 'privat24';
      $host = 'http://' . $_SERVER['HTTP_HOST'];
      $server_url = '/order-set-status/private24/';

      $form = $this->getPrivate24($id, $data['total_sum_uah'],
        $host . $result_url, $host . $server_url);
    }
    // PayPal
    if ($params['payment'] == 2) {
      $success_url = '/order-info/' . md5(md5($id) . $data['user_email']) . '/';
      $cancel_url = '/order-info/' . md5(md5($id) . $data['user_email']) . '/continue/';
      $result_url = $this->getPayPal($id,
        $data['sum_delivery_usd_unformat'],
        $data['sum_usd_unformat'],
        $success_url, $cancel_url);
    }

    return [
      'state' => $state,
      'redirect' => $result_url,
      'num' => $id,
      'form' => $form,
    ];
  } // }}}

  public function saveOrderUserInfo() { // {{{
    $auth = Auth::getInstance();
    $email = $auth->getStorage()->identity;
    $form = 'order_user_info';

    $validator = new Validator();
    $data = $this->getRequest()->post();

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

    if(isset($data['with_advets']) && intval($data['with_advets']) == 1) {
      $withAdverts = 1;
    } else { $withAdverts =  0; }

    // var_dump($email);
    $num = $this->db()->query()
      ->addSql('update orders_tbl set ord_user_name = $3, ord_user_phones = $4,')
      ->addSql('ord_user_city = $5, ord_user_index = $6, ord_user_street = $7,')
      ->addSql('ord_user_build = $8, ord_user_flat = $9, ord_user_cnt_id_fk = $10, ord_advert_in_order = $11')
      ->addSql('where ord_status=$1 and')
      ->addSql('ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $2 or usr_social_id = $2 limit 1)')
      ->addParam('not_complete')
      ->addParam($email)
      ->addParam($data['name'])
      ->addParam($data['phone'])
      ->addParam($data['city'])
      ->addParam($data['index'])
      ->addParam($data['street'])
      ->addParam($data['build'])
      ->addParam($data['flat'])
      ->addParam($data['country'])
      ->addParam($withAdverts)
      ->execute('ord_num');

    $name_arr = explode(' ', $data['name']);
    $data['surname'] = $name_arr[0];
    $data['secondname'] = implode(' ', array_slice($name_arr, 1));

    $country = $this->db()->query()
      ->addSql('SELECT cnt_synonym')
      ->addSql('from countries_tbl')
      ->addSql('where cnt_id_pk=$1')
      ->addParam($data['country'])
      ->fetchRow(0, false);

    $weight = $this->db()->query()
      ->addSql('SELECT sum(count*coalesce(weight,0)) as total_weight')
      ->addSql('from orders_details_vw')
      ->addSql('left join orders_tbl on order_id = ord_id_pk')
      ->addSql('left join users_tbl on usr_id_pk = ord_usr_id_fk')
      ->addSql('where order_id=$1')
      ->addParam($num)
      ->fetchRow(0, false);

    $weight = $weight['total_weight'];
    $userParams = [
      'type' => 'edit',
      'country' => $country['cnt_synonym'],
      'city' => $data['city'],
      'weight' => $weight,
    ];

    // var_dump($userParams);
    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
        $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'paymentsDeliveries.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $data['html'] = $templater->parse(['userParams' => $userParams]);

    return $data;
  } // }}}

  public function getPortmone($order_id, $sum, $result_url, $cancel_url) { // {{{
    // $this->getVariable('stg_portmone_payee_id');
    // $this->getVariable('stg_portmone_login');
    // $this->getVariable('stg_portmone_password');
    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];

    $support_languages = ['ru' => true, 'en' => true];
    $language = $this->getVariable('current_language');

    if ( !isset($support_languages[$language]) ) {
      $language = 'en';
    }

    $data = [
      'num' => $order_id,
      'sum' => $sum,
      'lang' => $language,
      'success_url' => $baseUrl . $result_url,
      'cancel_url' => $baseUrl . $cancel_url,
    ];

    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
        $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'portmone.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());

    $result = $templater->parse($data);

    return $result;
  } // }}}

  public function getPrivate24($order_id, $amount, $result_url, $server_url) { // {{{
    $merchant_id = $this->getVariable('stg_private24_id');

    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
        $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'private24.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $form = $templater->parse([
      'merchant_id' => $merchant_id,
      'order_id' => $order_id,
      'amount' => $amount,
      //'sandbox' => true,
      'result_url' => $result_url,
      'server_url' => $server_url,
      'description' => 'Заказ на ' . PROJECT_NAME . ' #' . $order_id,
    ]);

    return $form;
  } // }}}

  /**
   * undocumented function
   *
   * @return void
   */
  public function changeStatusPlatonOrder() { // {{{
    $data = $this->getRequest()->post();
    ob_start();
    var_dump($data);
    $result = ob_get_clean();
    file_put_contents('/tmp/platon.txt', $result);

    if ('SALE' == $data['status']) {
      $this->db()->query()
        ->addSql('update orders_tbl set ord_status = $3')
        ->addSql('where ord_num = $1 and ord_platon_sum_verify = $2')
        ->addParam($data['order'])
        ->addParam($data['amount'])
        ->addParam('paid')
        ->printSql()
        ->execute();

      // check if we have Adverts in Order
      $adv_bool = $this->db()->query()
        ->addSql('select ord_advert_in_order as advert_bool from orders_tbl')
        ->addSql('where ord_num = $1')
        ->addParam($data['order'])
        ->fetchRow(0);
      if ($adv_bool == 't') {
        // public adverts:
        $adverts_id_arr = $this->db()->query()
          ->addSql('select odd_advert_id as advert_id')
          ->addSql('from orders_details_tbl')
          ->addSql('where odd_ord_id_fk = $1')
          ->addParam($data['order'])
          ->fetchResult();
        foreach ($adverts_id_arr as $item)  {
          if ($item['advert_id'] > 0) {
            $this->db()->query()
              ->addSql('update adverts_tbl set adv_enabled = TRUE')
              ->addSql('where adv_id_pk = $1')
              ->addParam($item['advert_id'])
              ->execute();
          }
        }
      }
    }

    return null;
  } // }}}


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

  public function getUserDiscountValue() {
    $auth = Auth::getInstance();
    if ( $auth->hasIdentity() ) {
      $email = $auth->getStorage()->identity;
      $dis = $this->db()->query()
        ->addSql('select usr_discount as discount')
        ->addSql('from users_tbl')
        ->addSql('where usr_email=$1')
        ->addParam($email)
        ->fetchRow(0, false);

      return $dis['discount'];
    }


    return null;
  }
}
