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
    \Uwin\Linguistics,
    \Uwin\Auth,
    \Uwin\Registry,
    \Uwin\Mail,
    \Uwin\Xml,
    \Uwin\Cacher\Memcached,
    \VK\VK,

    \Uwin\TemplaterBlitz    as Templater,
    \Uwin\Controller\Front,

    \Facebook\FacebookSession,
    \Facebook\FacebookRequest,
    \Facebook\GraphUser,
    \Facebook\FacebookRequestException,
    \Facebook\FacebookRedirectLoginHelper,

    \Uwin\Exception\Route    as RouteException;

/**
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 */
class Users extends Abstract_
{

  private function _num2alpha($n) {
    $r = '';
    for ($i = 1; $n >= 0 && $i < 10; $i++) {
      $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
      $n -= pow(26, $i);
    }

    return $r;
  }

  private function _generateSalt() {
    $salt = $this->_num2alpha(crc32(md5(time()+mt_rand(1, 10000))));
    return $salt;
  }

  public function __construct() {
    $auth = Auth::getInstance();
    if ( $auth->hasIdentity() ) {
      $this->setVariable('authed', true);
    }
  }

  private function _sendMailLostPassword($email, $password) {
    // Получаем данные добавленного пользователя
    $data = $this->db()->query()
      ->addSql('select usr_id_pk, usr_email as email,')
      ->addSql('md5(usr_id_pk||md5(usr_email)||usr_date_registration) as id,')
      ->addSql('cast(usr_id_pk as varchar) || cast(EXTRACT(EPOCH FROM usr_date_registration) as int) as code')
      ->addSql('from users_tbl')
      ->addSql('where usr_email=$1')
      ->addParam($email)
      ->fetchRow(0, false);

    // Получаем тему письма
    $subject = $this->getVariable('lng_mails_lost_password_subject');
    $body = $this->getVariable('lng_mails_lost_password_body');

    $data['password'] = $password;

    // И содержимое письма
    $templater = new Templater;
    $templater->load($body);
    $text = $templater->parse($data);

    // Получаем имя и email от которого будут оправляться письма
    if ( false === $mailerName = $this->getVariable('stg_mail_name') ) {
      $mailerName = null;
    }
    $mailerEmail = $this->getVariable('stg_mail_email');

    // Отправляем почту на email зарегестрированного пользователя
    $header = $this->getVariable('stg_email_header');
    $footer = $this->getVariable('stg_email_footer');
    try {
      $settings = Registry::get('stg');
      $settings['mail']['smtp'] = ['host' => 'localhost'];
      $mail = new Mail($settings['mail']['smtp']);
      $mail->setFromEmail($mailerEmail, $mailerName)
        ->addEmail($data['email'])
        ->setSubject($subject)
        ->setText($header . $text . $footer)
        ->send();
    } catch (\Exception $e) {
      var_dump($e);
    }

    $this->createModel('mailer')
      ->saveMail('user', $data['usr_id_pk'], 'lost_password', $data['email'],
        $subject, $text);

    return $this;
  }

  private function _sendMailRegister($user_id) {
    // Получаем данные добавленного пользователя
    $data = $this->db()->query()
      ->addSql('select usr_id_pk, usr_email as email,')
      ->addSql('md5(usr_id_pk||md5(usr_email)||usr_date_registration) as id,')
      ->addSql('cast(usr_id_pk as varchar) || cast(EXTRACT(EPOCH FROM usr_date_registration) as int) as code')
      ->addSql('from users_tbl')
      ->addSql('where usr_id_pk=$1')
      ->addParam($user_id)
      ->fetchRow(0, false);

    // Получаем тему письма
    $subject = $this->getVariable('lng_mails_registration_subject');
    $body = $this->getVariable('lng_mails_registration_body');

    // И содержимое письма
    $templater = new Templater;
    $templater->load($body);
    $text = $templater->parse($data);

    // Получаем имя и email от которого будут оправляться письма
    if ( false === $mailerName = $this->getVariable('stg_mail_name') ) {
      $mailerName = null;
    }
    $mailerEmail = $this->getVariable('stg_mail_email');

    // Отправляем почту на email зарегестрированного пользователя
    $header = $this->getVariable('stg_email_header');
    $footer = $this->getVariable('stg_email_footer');
    try {
      $settings = Registry::get('stg');
      $mail = new Mail($settings['mail']['smtp']);
      $mail->setFromEmail($mailerEmail, $mailerName)
        ->addEmail($data['email'])
        ->setSubject($subject)
        ->setText($header . $text . $footer)
        ->send();
    } catch (\Exception $e) {
    }

    $this->createModel('mailer')
      ->saveMail('user', $data['usr_id_pk'], 'register', $data['email'],
        $subject, $text);

    return $this;
  }

  /**
   * @return Users
   */
  public function getIndex() {
    return $this;
  }

  public function subscribe() {
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'subscribe';

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

    $exists = $this->db()->query()
      ->addSql('select uss_id_pk from users_simple_subscribes_tbl')
      ->addSql('where lower($1) = lower(uss_email)')
      ->addParam($data['email'])
      ->fetchRow(0, false);

    if ( empty($exists) ) {
      $this->db()->query()
        ->addSql('insert into users_simple_subscribes_tbl(uss_email, uss_name,')
        ->addSql('uss_lng_id_fk)values($1, $2,')
        ->addSql('(select lng_id_pk from languages_tbl where lng_synonym = $3))')
        ->addParam($data['email'])
        ->addParam($data['name'])
        ->addParam($this->getVariable('current_language'))
        ->execute();
    } else {
      $this->db()->query()
        ->addSql('update users_simple_subscribes_tbl set uss_name = $1')
        ->addSql('where lower($2) = lower(uss_email)')
        ->addParam($data['name'])
        ->addParam($data['email'])
        ->execute();
    }
    $inTwoMonths = 60 * 60 * 24 * 60 + time();
    setcookie('subscribe', $data['email'], $inTwoMonths, '/', COOKIE_HOST);

    return true;
  }

  public function unsubscribe() {
    $validator = new Validator();
    $email = $this->getRequest()->get('email');
    $this->db()->query()
      ->addSql('insert into users_simple_subscribes_tbl (uss_name, uss_email, uss_unsubscribe)values')
      ->addSql('(lower($1), lower($1), true)')
      ->addParam($email)
      ->execute();

    setcookie('subscribe', $email, time() - 3600, '/', COOKIE_HOST);

    return true;
  }

  public function getSubscriptionBar() {
    if ( !isset($_COOKIE['subscribe']) ) {
      return [];
    }

    $subscribe_email = $_COOKIE['subscribe'];

    return [
      'email' => $subscribe_email,
    ];
  }

  public function addReview() {
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'add_review';

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

    $this->db()->query()
      ->addSql('insert into users_reviews_tbl(urv_name, urv_email, urv_text,')
      ->addSql('urv_quality_service, urv_usability_site, urv_quality_goods,')
      ->addSql('urv_shipping)values($1, $2, $3, $4, $5, $6, $7)')
      ->addParam($data['name'])
      ->addParam($data['email'])
      ->addParam($data['text'])
      ->addParam($data['quality_service'])
      ->addParam($data['usability_site'])
      ->addParam($data['quality_goods'])
      ->addParam($data['shipping'])
      ->execute();

    return true;
  }

  public function setCurrency() {
    $currency = $this->getRequest()->getParam('synonym');

    $inTwoMonths = 60 * 60 * 24 * 60 + time();
    setcookie('currency', $currency, $inTwoMonths, '/', COOKIE_HOST);
    // var_dump($_COOKIE);
  }

  public function Auth() {
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'auth';

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();

    $errors = $validator->validate($form, $rules, $data, $lang_variables);

    if (empty($errors)) {
      $auth = Auth::getInstance()->setDb( $this->db() );

      // Установка переменных авторизации
      $auth->setTableName('(select * from users_tbl where usr_enabled=true) usr')
        ->setIdentityColumn('usr_email')
        ->setPasswordColumn('usr_password')
        ->setSaltColumn('usr_salt')
        ->setIdentity(trim($data['email']))
        ->setPassword(trim($data['password']));

      // Аутентификация пользователя
      try {
        $auth->authenticate();

        $storage = Auth::getInstance()->getStorage();
        $session_id = $storage->getId();

        $this->db()->query()
          ->addSql('update orders_tbl set ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1)')
          ->addSql('where ord_session_id = $2')
          ->addSql('and ord_status = $3')
          ->addParam(trim($data['email']))
          ->addParam($session_id)
          ->addParam('not_complete')
          ->execute();
      } catch (\Exception $e) {
        $error_text = $this->getVariable('lng_validate_auth_email_not_found');

        $errors[] = [
          'id' => 'email',
          'text' => $error_text,
        ];
      }
    }

    if ( !empty($errors) ) {
      $errors['errors'] = true;
    }

    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    return true;
  }


  public function repairPassword() {
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'auth';

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();

    $errors = $validator->validate($form, $rules, $data, $lang_variables);

    $user = $this->db()->query()
      ->addSql('select usr_id_pk from users_tbl where usr_enabled = true')
      ->addSql('and lower($1) = lower(usr_email)')
      ->addParam($data['email'])
      ->fetchRow(0, false);

    if (empty($errors) && empty($user)) {
      $error_text = $this->getVariable('lng_validate_repair_email_not_found');

      $errors[] = [
        'id' => 'email',
        'text' => $error_text,
      ];
    }

    if ( !empty($errors) ) {
      $errors['errors'] = true;
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    if ( !empty($errors) ) {
    }

    // Регистрируем пользователя, и ложим на счет указанное кол-во бонусов
    $password = $this->_generateSalt();
    $this->db()->query()
      ->addSql('update users_tbl set usr_password = $1')
      ->addSql('where usr_email=$2')
      ->addParam($password)
      ->addParam($data['email'])
      ->execute();

    // Отправляем письмо зарегенному пользователю
    $this->_sendMailLostPassword($data['email'], $password);

    // Получаем текст сообщения о успешной регистрации пользователя
    return [
      'success' => $this->getVariable('lng_validate_repair_success')
    ];
  }

  public function register() {
    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'register';

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();

    $errors = $validator->validate($form, $rules, $data, $lang_variables);

    if ( $validator->isExistsInDb($data['email'], $this->db()->query(),
      'users_tbl', 'usr_email') ) {
      $errors[] = [
        'id' => 'email',
        'text' => $lang_variables['lng_validate_register_email_exists'],
      ];
    }

    if ( empty($errors) && $data['password'] !== $data['password_retype']) {
      $errors[] = [
        'id' => 'password',
        'text' => $lang_variables['lng_validate_register_password_retype_retype'],
      ];
    }

    if ( !empty($errors) ) {
      $errors['errors'] = true;
    }

    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    $user_id = $this->db()->query()
      ->addSql('insert into users_tbl(usr_email, usr_name, usr_password,')
      ->addSql('usr_lng_id_fk)')
      ->addSql('values($1, $2, $3,')
      ->addSql('(select lng_id_pk from languages_tbl where lng_synonym = $4))')
      ->addParam($data['email'])
      ->addParam($data['name'])
      ->addParam($data['password'])
      ->addParam($this->getVariable('current_language'))
      ->execute('usr_id_pk');

    // Отправляем письмо зарегенному пользователю
    $this->_sendMailRegister($user_id);

    $auth = Auth::getInstance()->setDb( $this->db() );

    // Установка переменных авторизации
    $auth->setTableName('(select coalesce(usr_social_id, usr_email) as usr_email, usr_password, usr_salt from users_tbl where usr_enabled=true) usr')
      ->setIdentityColumn('usr_email')
      ->setPasswordColumn('usr_password')
      ->setSaltColumn('usr_salt')
      ->setIdentity(trim($data['email']))
      ->setPassword(trim($data['password']));

    // Аутентификация пользователя
    $auth->authenticate();

    $storage = Auth::getInstance()->getStorage();
    $session_id = $storage->getId();

    $this->db()->query()
      ->addSql('update orders_tbl set ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1)')
      ->addSql('where ord_session_id = $2')
      ->addSql('and ord_status = $3')
      ->addParam(trim($data['email']))
      ->addParam($session_id)
      ->addParam('not_complete')
      ->execute();

    return true;
  }

  public function getCabinet() {
    $auth = Auth::getInstance();
    $email = $auth->getStorage()->identity;

    $data = $this->db()->query()
      ->addSql('select usr_name as name, usr_email as email, usr_phones as phone,')
      ->addSql('usr_city as city, usr_index as index, usr_street as street,')
      ->addSql('usr_build as build, usr_flat as flat, cnt_name_#lang# as country,')
      ->addSql('cnt_id_pk')
      ->addSql('from users_tbl')
      ->addSql('left join countries_tbl on cnt_id_pk = usr_cnt_id_fk')
      ->addSql('where usr_email=$1 or usr_social_id = $1')
      ->addParam($email)
      ->fetchRow(0, false);

    $countries = $this->db()->query()
      ->addSql('select cnt_name_#lang# as name, cnt_id_pk as id,')
      ->addSql('case when cnt_id_pk = $1 then 1 else null end selected')
      ->addSql('from countries_tbl')
      ->addSql('where cnt_enabled = true order by coalesce(cnt_prior, 9999), cnt_name_#lang#')
      ->addParam($data['cnt_id_pk'])
      ->fetchResult(false);

    $data['countries'] = $countries;
    $data['discount'] = $this->getUserDiscountValue();
    if (strpos($data['email'], '@') === false) {
      $data['not_email'] = true;
    }

    return $data;
  }

  public function saveDataCabinet() {
    $auth = Auth::getInstance();
    $email = $auth->getStorage()->identity;

    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = $this->getRequest()->getParam('type');

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();

    if (strpos($email, '@') !== false) {
      unset($rules['email']);
    }

    if ($form == 'full' and !isset($data['name']) ) {
      unset($rules['name']);
    }
    $errors = $validator->validate($form, $rules, $data, $lang_variables);

    if ( !empty($errors) ) {
      $errors['errors'] = true;
    }

    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    if ('info' == $form) {
      $i = 4;
      $q = $this->db()->query()
        ->addSql('update users_tbl set usr_name = $1, usr_phones = $2');
      if (isset($data['email'])) {
        $q->addSql(', usr_email = $' . $i);
        $i++;
      }
      if (!empty($data['password'])) {
        $q->addSql(', usr_password = $' . $i);
        $i++;
      }

      $q->addSql('where lower($3) = lower(usr_email)')
        ->addParam($data['name'])
        ->addParam($data['tel'])
        ->addParam($email);
      if (isset($data['email'])) {
        $q->addParam($data['email']);
        $auth->getStorage()->identity = $data['email'];
      }
      if (!empty($data['password'])) {
        $q->addParam($data['password']);
      }

      $q->execute();
    }

    if ('address' == $form) {
      $this->db()->query()
        ->addSql('update users_tbl set usr_city = $2, usr_index = $3,')
        ->addSql('usr_street = $4, usr_build = $5, usr_flat = $6, usr_cnt_id_fk = $7')
        ->addSql('where lower($1) = lower(usr_email)')
        ->addParam($email)
        ->addParam($data['city'])
        ->addParam($data['index'])
        ->addParam($data['street'])
        ->addParam($data['build'])
        ->addParam($data['flat'])
        ->addParam($data['country'])
        ->execute();
    }

    if ('full' == $form) {
      $i = 9;
      $query = $this->db()->query()
        ->addSql('update users_tbl set usr_city = $2, usr_index = $3,')
        ->addSql('usr_street = $4, usr_build = $5, usr_flat = $6, usr_cnt_id_fk = $7, usr_phones=$8');
      if ( isset($data['name']) ) {
        $query->addSql(',usr_name = $' . $i);
        $i++;
      } else {
        $data['name'] = null;
      }
      if ( isset($data['email']) ) {
        $query->addSql(',usr_email = $' . $i);
        $i++;
      } else {
        $data['email'] = null;
      }
      $query->addSql('where lower($1) = lower(usr_email)')
        ->addParam($email)
        ->addParam($data['city'])
        ->addParam($data['index'])
        ->addParam($data['street'])
        ->addParam($data['build'])
        ->addParam($data['flat'])
        ->addParam($data['country'])
        ->addParam($data['phone'])
        ->addParam($data['name'], $data['name'])
        ->addParam($data['email'], $data['email'])
        ->execute();
    }
    return true;
  }

  public function getOrders() {
    $auth = Auth::getInstance();
    $email = $auth->getStorage()->identity;

    $orders = $this->db()->query()
      ->addSql('select user_orders2_vw.num, user_orders2_vw.status, user_orders2_vw.method_delivery_#lang# as method_delivery,')
      ->addSql('method_payment_#lang# as method_payment, user_orders2_vw.key,')
      ->addSql('$2*user_orders2_vw.sum_unformat, user_orders2_vw.currency_#lang# as currency, user_orders2_vw.rate, to_number(user_orders2_vw.sum, \'999999\')*$2 as sum, user_orders2_vw.datetime,')
      ->addSql('delivery_id, tracking_number')
      ->addSql('from user_orders2_vw')
      ->addSql('left join orders_details_vw on orders_details_vw.order_id = user_orders2_vw.num')
      ->addSql('where lower($1) = lower(user_email) or')
      ->addSql('lower($1) = lower(social_id)')
      ->addSql("and orders_details_vw.num != '10009009001'")
      // ->addSql('where user_email is not null')
      ->addParam($email)
      ->addParam($this->getUserDiscount())
      ->fetchResult(false);

    foreach ($orders as &$order){
      $order['status_text'] = $this->getVariable('lng_statuses_' . $order['status']);
    }

    return [
      'orders' => $orders,
    ];
  }

  public function getOAuthLinks() {
    $memcached = Memcached::getInstance();

    $googleAuthUrl = $this->googleAuthUrl()['authUrl'];
    $facebookAuthUrl = $this->facebookAuthUrl()['authUrl'];
    // $twitterAuthUrl = $this->twitterAuthUrl()['authUrl'];
    $vkAuthUrl = $this->vkAuthUrl()['authUrl'];
    $odnoklassnikiAuthUrl = $this->odnoklassnikiAuthUrl()['authUrl'];


    $data = [
      'google_auth_url' => $googleAuthUrl,
      'facebook_auth_url' => $facebookAuthUrl,
      // 'twitter_auth_url' => $twitterAuthUrl,
      'vk_auth_url' => $vkAuthUrl,
      'odnoklassniki_auth_url' => $odnoklassnikiAuthUrl,
    ];

    return $data;
  }

  public function registerAndLogin($type, $userinfo) {
    // создаю пользователя в бд если его нет
    // пароль генерирую любой
    $exists = $this->db()->query()
      ->addSql('select usr_id_pk, usr_password, usr_email from users_tbl')
      ->addSql('where lower($1) = lower(usr_email) or')
      ->addSql('lower($1) = lower(usr_social_id)')
      ->addParam($userinfo['email'])
      ->fetchRow(0, false);

    if ( empty($exists) ) {
      $email = $userinfo['email'];
      $password = $this->db()->query()
        ->addSql('insert into users_tbl(usr_email, usr_name, usr_password,')
        ->addSql('usr_auth_type, usr_lng_id_fk, usr_social_id)')
        ->addSql('values($1, $2, $3, $4, ')
        ->addSql('(select lng_id_pk from languages_tbl where lng_synonym = $5), $1)')
        ->addParam($userinfo['email'])
        ->addParam($userinfo['given_name'] . ' ' . $userinfo['family_name'])
        ->addParam( $this->_generateSalt() )
        ->addParam($type)
        ->addParam($this->getVariable('current_language'))
        ->execute('usr_password');
    } else {
      $password = $exists['usr_password'];
      $email = $exists['usr_email'];
    }

    $auth = Auth::getInstance()->setDb( $this->db() );

    // Установка переменных авторизации
    $auth->setTableName('(select coalesce(usr_social_id, usr_email) as usr_email, usr_password, usr_salt from users_tbl where usr_enabled=true) usr')
      ->setIdentityColumn('usr_email')
      ->setPasswordColumn('usr_password')
      ->setSaltColumn('usr_salt')
      ->setIdentity(trim($userinfo['email']))
      ->useCryptPassword()
      ->setPassword($password);

    // Аутентификация пользователя
    $auth->authenticate();
    $storage = Auth::getInstance()->getStorage();
    $session_id = $storage->getId();

    $this->db()->query()
      ->addSql('update orders_tbl set ord_usr_id_fk = (select usr_id_pk from users_tbl where usr_email = $1 or usr_social_id = $1 limit 1)')
      ->addSql('where ord_session_id = $2')
      ->addSql('and ord_status = $3')
      ->addParam(trim($userinfo['email']))
      ->addParam($session_id)
      ->addParam('not_complete')
      ->execute();

    return null;
  }

  public function googleAuthUrl() {
    $client_id = $this->getVariable('stg_auth_api_google_client_id');
    $client_secret = $this->getVariable('stg_auth_api_google_secret');

    $current_lang = $this->getVariable('current_language');
    $default_lang = $this->getVariable('default_language');

    $lang_domain = '';
    if ($current_lang != $default_lang) {
      $lang_domain = $current_lang . '.';
    }

    $redirect_uri = 'http://' . SERVER_NAME . '/callback/googleplus/';

    $client = new Google_Client();
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);

    $service = new Google_Service_Urlshortener($client);
    $client->addScope(Google_Service_Urlshortener::URLSHORTENER);
    $client->addScope('https://www.googleapis.com/auth/userinfo.email');

    $authUrl = $client->createAuthUrl();

    return [
      'authUrl' => $authUrl,
    ];
  }

  public function facebookAuthUrl() {
    $app_id = $this->getVariable('stg_auth_api_facebook_app_id');
    $app_secret = $this->getVariable('stg_auth_api_facebook_secret');

    $current_lang = $this->getVariable('current_language');
    $default_lang = $this->getVariable('default_language');
    // var_dump($current_lang);

    $lang_domain = '';
    if ($current_lang != $default_lang) {
      $lang_domain = $current_lang . '.';
    }

    $redirect_uri = 'http://' . SERVER_NAME . '/callback/facebook/';


    FacebookSession::setDefaultApplication($app_id, $app_secret);
    $helper = new FacebookRedirectLoginHelper($redirect_uri);
    // $facebook = new Facebook([
    //   'appId'  => $app_id,
    //   'secret' => $app_secret,
    // ]);

    $params = array(
      'scope' => 'email',
      'redirect_uri' => $redirect_uri,
    );
    $authUrl = $helper->getLoginUrl(['scope' =>'email']);//$facebook->getLoginUrl($params);

    return [
      'authUrl' => $authUrl,
    ];
    return $this->_facebookUrl;
  }

  public function twitterAuthUrl() {
    $key = $this->getVariable('stg_auth_api_twitter_key');
    $secret = $this->getVariable('stg_auth_api_twitter_secret');

    $current_lang = $this->getVariable('current_language');
    $default_lang = $this->getVariable('default_language');

    $lang_domain = '';
    if ($current_lang != $default_lang) {
      $lang_domain = $current_lang . '.';
    }

    $redirect_uri = 'http://' . SERVER_NAME . '/callback/twitter/';

    $twitter = new Abraham\TwitterOAuth\TwitterOAuth($key, $secret);
    @$request_token = $twitter->getRequestToken($redirect_uri);
    @$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
    @$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
    $authUrl = $twitter->getAuthorizeURL($token);
    // var_dump($authUrl);

    return [
      'authUrl' => $authUrl,
    ];
  }

  public function vkAuthUrl() {
    $app_id = $this->getVariable('stg_auth_api_vk_app_id');
    $secret = $this->getVariable('stg_auth_api_vk_secret');

    $current_lang = $this->getVariable('current_language');
    $default_lang = $this->getVariable('default_language');

    $lang_domain = '';
    if ($current_lang != $default_lang) {
      $lang_domain = $current_lang . '.';
    }

    $redirect_uri = 'http://' . SERVER_NAME . '/callback/vk/';

    $vk = new VK($app_id, $secret);
    $authUrl = $vk->getAuthorizeURL('', $redirect_uri);

    return [
      'authUrl' => $authUrl,
    ];
  }

  public function odnoklassnikiAuthUrl() {
    $app_id = $this->getVariable('stg_auth_api_odnoklassniki_app_id');
    $secret = $this->getVariable('stg_auth_api_odnoklassniki_secret');
    $token = $this->getVariable('stg_auth_api_odnoklassniki_token');

    $current_lang = $this->getVariable('current_language');
    $default_lang = $this->getVariable('default_language');

    $lang_domain = '';
    if ($current_lang != $default_lang) {
      $lang_domain = $current_lang . '.';
    }

    $redirect_uri = 'http://' . SERVER_NAME . '/callback/odnoklassniki/';

    $authUrl = 'http://www.odnoklassniki.ru/oauth/authorize?client_id=' .
      $app_id . '&scope=VALUABLE&response_type=code&redirect_uri=' .
      urlencode($redirect_uri);

    return [
      'authUrl' => $authUrl,
    ];
  }

  public function oAuthenticate() {
    $type = $this->getRequest()->getParam('type');

    if ($type == 'googleplus') {
      if (!isset($_GET['code'])) {
        return false;
      }
      $client_id = $this->getVariable('stg_auth_api_google_client_id');
      $client_secret = $this->getVariable('stg_auth_api_google_secret');
      $redirect_uri = 'http://' . SERVER_NAME . '/callback/googleplus/';

      $client = new Google_Client();
      $client->setClientId($client_id);
      $client->setClientSecret($client_secret);
      $client->setRedirectUri($redirect_uri);

      $client->authenticate($_GET['code']);

      if (!$client->getAccessToken()) {
        return false;
      }

      $oauth2 = new Google_Service_Oauth2($client);
      $userinfo = $oauth2->userinfo->get();

      $this->registerAndLogin('google', $userinfo);
    }

    if ($type == 'facebook') {
      $app_id = $this->getVariable('stg_auth_api_facebook_app_id');
      $app_secret = $this->getVariable('stg_auth_api_facebook_secret');
      // var_dump($app_secret);

      FacebookSession::setDefaultApplication($app_id, $app_secret);
    $current_lang = $this->getVariable('current_language');
    $default_lang = $this->getVariable('default_language');

    $lang_domain = '';
    if ($current_lang != $default_lang) {
      $lang_domain = $current_lang . '.';
    }

    $redirect_uri = 'http://' . SERVER_NAME . '/callback/facebook/';
    $user = null;
      try {
        $helper = new FacebookRedirectLoginHelper($redirect_uri);
        $session = $helper->getSessionFromRedirect();
      } catch(FacebookRequestException $ex) {
        // var_dump('123');
        // When Facebook returns an error
      } catch(\Exception $ex) {
        // var_dump('234');
        // When validation fails or other local issues
      }
      if ($session) {
    // var_dump($redirect_uri);
        $user = (new FacebookRequest(
          $session, 'GET', '/me')
        )->execute()->getGraphObject();
      }
    // var_dump($user);
    // throw \Exception;


    // throw \Exception;

      // throw \Exception;
      // $facebook = new Facebook([
      //   'appId'  => $app_id,
      //   'secret' => $app_secret,
      //   'cookie' => true
      // ]);
      //
      // $user = $facebook->getUser();
      $userinfo = [];
      if ($user) {
        // $userinfo = $facebook->api('/me');
        $userinfo['email'] = $user->getProperty('email');
        $userinfo['given_name'] = $user->getProperty('first_name');
        $userinfo['family_name'] = $user->getProperty('last_name');
        $this->registerAndLogin('facebook', $userinfo);
      }
//var_dump($_REQUEST);
//var_dump([
//        'appId'  => $app_id,
//        'secret' => $app_secret,
//      ]);
//var_dump($facebook);
//var_dump($user);
//throw Exception;
    }

    if ($type == 'twitter') {
      $key = $this->getVariable('stg_auth_api_twitter_key');
      $secret = $this->getVariable('stg_auth_api_twitter_secret');
      $token = $this->getVariable('stg_auth_api_twitter_token');
      $tokensecret = $this->getVariable('stg_auth_api_twitter_tokensecret');

      $twitter = new TwitterOAuth($key, $secret, $token, $tokensecret);

      $user = $twitter->get('account/verify_credentials');
      $userinfo = [];

      $userinfo['given_name'] = $user->name;
      $userinfo['family_name'] = '';
      $userinfo['email'] = 'http://twitter.com/' . $user->screen_name;
      $this->registerAndLogin('twitter', $userinfo);
    }

    if ($type == 'vk') {
      if (!isset($_REQUEST['code'])) {
        return false;
      }
      $app_id = $this->getVariable('stg_auth_api_vk_app_id');
      $secret = $this->getVariable('stg_auth_api_vk_secret');

      $current_lang = $this->getVariable('current_language');
      $default_lang = $this->getVariable('default_language');

      $lang_domain = '';
      if ($current_lang != $default_lang) {
        $lang_domain = $current_lang . '.';
      }

      $redirect_uri = 'http://' . SERVER_NAME . '/callback/vk/';

      $vk = new VK($app_id, $secret);
      $access_token = $vk->getAccessToken($_REQUEST['code'], $redirect_uri);

      $user = $vk->api('users.get', [
        'uids' => $access_token['user_id'],
        'fields' => 'first_name,last_name,sex,email'
      ]);
      $userinfo = [];
      $user = $user['response'][0];

      $userinfo['given_name'] = $user['first_name'];
      $userinfo['family_name'] = $user['last_name'];
      $userinfo['email'] = 'http://vk.com/ - ' . $access_token['user_id'];
      $this->registerAndLogin('vk', $userinfo);
    }

    if ($type == 'odnoklassniki') {
      if (!isset($_REQUEST['code'])) {
        return false;
      }
      $app_id = $this->getVariable('stg_auth_api_odnoklassniki_app_id');
      $secret = $this->getVariable('stg_auth_api_odnoklassniki_secret');
      $token = $this->getVariable('stg_auth_api_odnoklassniki_token');

      $current_lang = $this->getVariable('current_language');
      $default_lang = $this->getVariable('default_language');

      $lang_domain = '';
      if ($current_lang != $default_lang) {
        $lang_domain = $current_lang . '.';
      }

      $redirect_uri = 'http://' . SERVER_NAME . '/callback/odnoklassniki/';

      $curl = curl_init('http://api.odnoklassniki.ru/oauth/token.do');
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, 'code=' . $_GET['code'] .
        '&redirect_uri=' . urlencode($redirect_uri) .
        '&grant_type=authorization_code&client_id=' . $app_id .
        '&client_secret=' . $token);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $s = curl_exec($curl);
      curl_close($curl);
      $auth = json_decode($s, true);

      $curl = curl_init('http://api.odnoklassniki.ru/fb.do?access_token=' .
        $auth['access_token'] . '&application_key=' . $secret .
        '&method=users.getCurrentUser&sig=' .
        md5('application_key=' . $secret . 'method=users.getCurrentUser' .
        md5($auth['access_token'] . $token)));

      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $s = curl_exec($curl);
      curl_close($curl);
      $user = json_decode($s, true);

      $userinfo = [];
      $userinfo['given_name'] = $user['first_name'];
      $userinfo['family_name'] = $user['last_name'];
      $userinfo['email'] = 'http://odnoklassniki.ru/ - ' . $user['uid'];
      $this->registerAndLogin($type, $userinfo);
    }

    return true;
  }

  public function getBasketUserInfo() {
    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      return null;
    }
    $email = $auth->getStorage()->identity;

    $userinfo = $this->db()->query()
      ->addSql('SELECT id, coalesce(ord_user_name, name) as name,')
      ->addSql('coalesce(ord_user_email, email) as email,')
      ->addSql('coalesce(ord_user_phones, phone) as phone,')
      ->addSql('coalesce(ord_user_street, street) as street,')
      ->addSql('coalesce(ord_user_build, build) as build,')
      ->addSql('coalesce(ord_user_flat, flat) as flat,')
      ->addSql('coalesce(ord_user_city, city) as city,')
      ->addSql('coalesce(cnt_name_#lang#, country_#lang#) as country,')
      ->addSql('coalesce(ord_user_index, index) as index,')
      ->addSql('coalesce(ord_user_cnt_id_fk, country_id) as country_id')
      ->addSql('from user_info_vw')
      ->addSql('left join orders_tbl on ord_usr_id_fk = id and ord_status = $2')
      ->addSql('left join countries_tbl as cnt on cnt_id_pk = ord_user_cnt_id_fk')
      ->addSql('where email = $1 or social_id = $1')
      ->addParam($email)
      ->addParam('not_complete')
      ->fetchRow(0, false);

    $name_arr = explode(' ', $userinfo['name']);
    $userinfo['surname'] = $name_arr[0];
    $userinfo['secondname'] = implode(' ', array_slice($name_arr, 1));

    $countries = $this->db()->query()
      ->addSql('select cnt_name_#lang# as name, cnt_id_pk as id,')
      ->addSql('case when cnt_id_pk = $1 then 1 else null end selected')
      ->addSql('from countries_tbl')
      ->addSql('where cnt_enabled = true order by coalesce(cnt_prior, 9999), cnt_name_#lang#')
      ->addParam($userinfo['country_id'])
      ->fetchResult(false);

    $userinfo['countries'] = $countries;

    return $userinfo;
  }

  public function mailer() {
    $variables = $this->getRequest()->post();

    $_tmp_languages = $this->db()->query()
      ->addSql('select lng_synonym as synonym')
      ->addSql('from languages_tbl')
      ->addSql('where lng_enabled=true')
      ->fetchResult(false);

    $languages = array();
    foreach($_tmp_languages as $lang) {
      $languages[] = $lang['synonym'];
    }

    try {
      $users = $this->db()->query()
        ->addSql('select usr_id_pk, usr_name as user_name, ueml.usr_email as usr_email, coalesce(lng_synonym, \'ru\') as lng_synonym,')
        ->addSql('md5(\'avuwin\'||md5(ueml.usr_email)) as finish from')
        ->addSql('(select distinct usr_email from users_tbl  ')
        ->addSql('where usr_email is not null and position(\'@\' in usr_email) > 0')
        ->addSql('union')
        ->addSql('select distinct ord_user_email from orders_tbl')
        ->addSql('where ord_user_email is not null) ueml')
        ->addSql('left join users_tbl usr on usr.usr_email = ueml.usr_email')
        ->addSql('left join languages_tbl on lng_id_pk=usr_lng_id_fk')
        ->addSql('left join last_date_order_by_user_vw on usr_id_pk = ord_usr_id_fk')
        ->addSql('where ueml.usr_email != \'\' and last_date_order is not null')
        ->fetchResult(false);

      $mails_str = null;
      if ( !empty($users) ) {
        $users_param = array();
        foreach($users as $user) {
          if ( isset($variables['form-lang-' . $user['lng_synonym']]) ) {
            $users_param[] = array('id' => $user['usr_id_pk'], 'email' => $user['usr_email']
              , 'finish' => $user['finish'], 'language' => $user['lng_synonym']);
          }
        }

        $this->createModel('mailer')
          ->saveMailer('users', 'user', $languages, $variables, $users_param);
      }
    } catch(\Exception $e) {
      var_dump($e);
    }
    return $this;
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function sendDiscountEmail($params) {
    $user = $this->db()->query()
      ->addSql('select usr_id_pk as id, usr_discount as discount, usr_email as email, lng_synonym as lang')
      ->addSql('from users_tbl')
      ->addSql('left join languages_tbl on lng_id_pk = usr_lng_id_fk')
      ->addSql('where usr_id_pk = $1')
      ->addParam($params['id'])
      ->fetchRow(0, false);

    if ($user['discount'] == $params['usr_discount']) {
      return $this;
    }

    // Получаем тему письма
    $registry = Registry::getInstance();
    $path_root = $registry['path']['userSettings'] . 'modules' . DIR_SEP;
    $dirLanguage = $path_root . 'users' . DIR_SEP . 'languages' . DIR_SEP;
    $fileLanguage = $dirLanguage . $user['lang'] . '.xml';
    $configLoader = new Xml;
    $configLoader->setFileSettings($fileLanguage);
    $config_values = $configLoader->getValues();

    $subject = $config_values['mails']['change_discount']['subject'];
    $body = $config_values['mails']['change_discount']['body'];

    $user['discount'] = $params['usr_discount'];

    // И содержимое письма
    $templater = new Templater;
    $templater->load($body);
    $text = $templater->parse($user);

    // Получаем имя и email от которого будут оправляться письма
    if ( false === $mailerName = $this->getVariable('stg_mail_name') ) {
      $mailerName = null;
    }
    $mailerEmail = $this->getVariable('stg_mail_email');

    // Отправляем почту на email зарегестрированного пользователя
    $header = $this->getVariable('stg_email_header');
    $footer = $this->getVariable('stg_email_footer');
    try {
      $settings = Registry::get('stg');
      $mail = new Mail($settings['mail']['smtp']);
      $mail->setFromEmail($mailerEmail, $mailerName)
        ->addEmail($user['email'])
        ->setSubject($subject)
        ->setText($header . $text . $footer)
        ->send();
    } catch (\Exception $e) {
    }

    $this->createModel('mailer')
      ->saveMail('user', $user['id'], 'change_discount', $user['email'],
        $subject, $text);

    return $this;
  }

  public function newUsersExcel($from = null, $to = null) {
    $request = $this->getRequest();
    if ($from === null) {
      $from = $request->post('from');
    }
    if ($from === null) {
      $to = $request->post('to');
    }

    $users = $this->db()->query()
      ->addSql("select to_char(usr_date_registration, 'DD.MM.YYYY') as dt, usr_email, usr_name, usr_index, cnt_name_ru, usr_city, usr_street, usr_build, usr_flat, concat(' ', usr_phones) as usr_phones")
      ->addSql("from users_tbl")
      ->addSql("left join countries_tbl on cnt_id_pk = usr_cnt_id_fk")
      ->addSql("where usr_street is not null and trim(usr_street) != ''")
      ->addSql("order by usr_date_registration desc")
      ->fetchResult(false);

    $path_library = $this->getVariable('path_library');
    $path_root = $this->getVariable('path_root');

    $excelFile = $path_root . 'scripts/users.xlsx';

    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel = $objReader->load($excelFile);
    $worksheet = $objPHPExcel->setActiveSheetIndex(0);

    $i = 2;
    foreach ($users as $row) {
      $worksheet->setCellValue('A' . $i, $row['usr_email']);
      $worksheet->setCellValue('B' . $i, $row['usr_name']);
      $worksheet->setCellValue('C' . $i, $row['usr_index']);
      $worksheet->setCellValue('D' . $i, $row['usr_city']);
      $worksheet->setCellValue('E' . $i, $row['cnt_name_ru']);
      $worksheet->setCellValue('F' . $i, $row['usr_street']);
      $worksheet->setCellValue('G' . $i, $row['usr_build']);
      $worksheet->setCellValue('H' . $i, $row['usr_flat']);
      $worksheet->setCellValue('I' . $i, $row['dt']);
      $worksheet->setCellValue('J' . $i, $row['usr_phones']);

      $i++;
    }
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="users-' . date('d-m-Y') . '.xlsx"');
    header('Cache-Control: max-age=0');
    $objWriter->save('php://output');

    return true;
  }


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

  public function getAdverts() {

    $auth = Auth::getInstance();
    $email = $auth->getStorage()->identity;


    $user_adverts = $this->db()->query()
      ->addSql('select adv_date_create as date, adv_id_pk as id,')
      ->addSql('adv_name as title,')
      ->addSql('adv_enabled as status')
      ->addSql('from adverts_tbl where adv_user_email = $1')
      ->addParam($email)
      ->fetchResult(false);

    return [
      'adverts' =>  $user_adverts,
    ];
  }

  private function _uploadImage($id, $name, $photos = [], $file_input = null, $i = 1) {
    $number = $this->db()->query()
      ->addSql('select count(ada_id_pk) as count')
      ->addSql('from adverts_attachments_tbl')
      ->addSql('where ada_adv_id_fk = $1')
      ->addParam($id)
      ->fetchField(0, false);

    $i = $number;
    $i++;
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
        $new_file = $dir . $filename. '-' . $i . '-bg.' . $ext;
        if (file_exists($file)) {
          if (file_exists($new_file)) {
            unlink($new_file);
          }
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

      $tmp = $this->db()->query()
        ->addSql('insert into adverts_attachments_tbl(ada_adv_id_fk, ada_image)')
        ->addSql('values')
        ->addSql('($1, $2)')
        ->addParam($id)
        ->addParam('/uploads/images/ads/' . $id . '/'
        . $filename . '-' . $i . '-bg.' . $ext);
      $tmp->execute();
               /*try {
                   $tmp->execute();
               } catch (Exception $e){
                   return $e->getMessage();
               }*/


      $i++;
    }

    return true;
  }

  public function getCurrency() {
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
    $currency = $query
      ->addTag('currencies')
      ->fetchRow(0);
    $this->setVariable('currency_abb', $currency['short_name']);

    return $currency;

  }


  public function geteditForm() {
    $auth = Auth::getInstance();
    $email = $auth->getStorage()->identity;

    $currency = $this->getCurrency();

    $all_currencies = $this->db()->query()->sql(
      "select currency_#lang# as name, id "
      . "from currencies_vw as crr ")
      ->addTag('currencies')
      ->fetchResult(false);

    if ($this->getRequest()->isPost()) {
      $data = $this->getRequest()->post();
      $enabled = 'TRUE';
      $idstr  = $this->getRequest()->get("id");
      $advert_p = $this->db()->query()
        ->addSql('select adv_type_payable as payable')
        ->addSql('from adverts_tbl where adv_id_pk = $1')
        ->addParam($idstr)
        ->fetchRow(0, false);
      $payableFlag = $advert_p['payable'];

      if(!empty($data['need_unpublish']) && $data['need_unpublish'] == 1) {
        $enabled = 'FALSE';
        $payableFlag = 1;
      }

      if (!empty($data['files_urls'])) {
        $this->_uploadImage($data['id'], $data['caption'], $data['files_urls']);
      }

      if (!empty($data['url'])) {
        $data['url'] = $this->linkifyYouTubeURLs($data['url']);
      }

      $tmp = $this->db()->query()
        ->addSql('update adverts_tbl set')
        ->addSql('adv_user_name = $2,')
        ->addSql('adv_user_city = $3,')
        ->addSql('adv_user_phone = $4,')
        ->addSql('adv_type = $5,')
        ->addSql('adv_category = $6,')
        ->addSql('adv_cost = $7,')
        ->addSql('adv_rat_id_fk	= $8,')
        ->addSql('adv_video_url = $9,')
        ->addSql('adv_name = $10,')
        ->addSql('adv_text = $11,')
        ->addSql('adv_type_payable = $12,')
        ->addSql('adv_enabled = $13')
        ->addSql('where adv_id_pk = $1')
        ->addParam($data['id'])                 // 1
        ->addParam($data['name'])               // 2
        ->addParam($data['city'])               // 3
        ->addParam($data['phone'])              //4
        ->addParam($data['type'])               //5
        ->addParam($data['category'])           //6
        ->addParam($data['price'])              // 7
        ->addParam($data['currency'])            //8
        ->addParam($data['url'])                 //9
        ->addParam($data['caption'])            //10
        ->addParam($data['text'])               //11
        ->addParam($payableFlag)                //12
        ->addParam($enabled);                   //13
      try {
        $tmp->execute();
      }
      catch (Exception $e){
        return $e->getMessage();
      }

      //  return ['editForm' => 'false'];
    }

    $idstr  = $this->getRequest()->get("id");

    $advert_tmp = $this->db()->query()
      ->addSql('select adv_name as title, adv_text as text, adv_user_phone as phone, adv_user_name as name,')
      ->addSql('adv_video_url as url, adv_enabled as status, adv_user_city as city, adv_image as img, adv_user_email as email,')
      ->addSql('adv_type as type, adv_category as category, adv_cost as cost, adv_date_create as date, adv_type_payable as payable')
      ->addSql('from adverts_tbl where adv_id_pk = $1')
      ->addParam($idstr)
      ->fetchRow(0, false);
    $advert['id'] = $idstr;

    $advert = $this->db()->query()
      ->addSql('select id, title, description, keywords, name, category, type, date, image, image_medium, adv_enabled,')
      ->addSql('text, user_name, user_city_#lang# as user_city, rat_value,')
      ->addSql("(cost_unformat/$2)::FLOAT as cost_unformat, cost_unformat as cost_usd,")
      ->addSql("replace(replace(trim(to_char((cost_unformat/$2),'999999999.99')), '.', '.'), ',00', '') as cost,")
      ->addSql('user_email, user_phone, user_phone_unformat,')
      ->addSql('short_name_#lang# as rate_name, count_comments')
      ->addSql('from adverts_vw_1')
      ->addSql('where id = $1')
      ->addParam($idstr)
      ->addParam($currency['ratio'])
      ->addTag('advert-' . $idstr)
      ->fetchRow(0, false);
    $advert['id'] = $idstr;
    $advert['url'] =  $advert_tmp['url'];
    $advert['city'] =  $advert_tmp['city'];
    $advert['currency'] = $all_currencies;
    $advert['payable'] = $advert_tmp['payable'];

    $images = $this->db()->query()
      ->addSql('select name, image, image_small')
      ->addSql('from adverts_attachments_vw')
      ->addSql('where advert_id = $1  offset 1')
      ->addParam($idstr)
      ->addTag('advert-images')
      ->fetchResult(false);

    if (!empty($images)) {  $advert['images'] = $images;  }

    // проверяем что мы открыли свое объявление
    if ( !isset($advert['user_email']) || $advert['user_email'] !==  $email) {
      // return false;  }
      return ['editForm' => 'false']; }

    $advert['params'] = [
      'id' => $idstr,
      'type' => $advert['type'],
      'category' => $advert['category'],
    ];

    $templater = new Templater(null, Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $templater->load( $this->getVariable('lng_templates_share') );
    $share_body = $templater->parse($advert);
    $share_body = rawurlencode($share_body);
    $advert['share_body'] = $share_body;
    $advert['name_encode'] = rawurlencode(' | ' . $advert['name']);

    return [
      'editForm' => $advert,
    ];
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
