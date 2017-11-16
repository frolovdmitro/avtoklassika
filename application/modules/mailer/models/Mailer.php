<?php
/**
 * Uwin CMS
 *
 * Файл содержит модель модуля управления социальными сетями, которые связаны с
 * сайтом
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_;
use \Uwin\Validator;
use \Uwin\Registry;
use \Uwin\TemplaterBlitz     as Templater;

/**
 * Модель модуля управления социальными сетями, которые связаны с сайтом
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Mailer extends Abstract_
{
  public function saveMail($type_parent, $parent_id, $type_mail, $email, $subject, $text) {
    $this->db()->query()
      ->addSql('insert into mails_tbl(mls_parent_type, mls_parent_id_fk,')
      ->addSql('mls_type, mls_email, mls_subject, mls_body, mls_status)')
      ->addSql('values($1, $2, $3, $4, $5, $6, $7)')
      ->addParam($type_parent)
      ->addParam($parent_id)
      ->addParam($type_mail)
      ->addParam($email)
      ->addParam($subject)
      ->addParam($text)
      ->addParam('send')
      ->execute();

    return $this;
  }

  public function saveMailer($mailerType, $mailType, $languages, $values, $users) {
    $query = $this->db()->query()
      ->addSql('insert into mailers_tbl(mlr_type, mlr_languages,')
      ->addParam($mailerType)
      ->addParam('|' . implode('|', $languages) . '|');
    $sql = '';
    foreach ($languages as $lang) {
      $sql .= 'mlr_subject_' . $lang . ', ';
      $query->addParam($values['subject_' . $lang]);
    }
    $sql = trim($sql, ', ');
    $query->addSql($sql . ')values($1, $2,');
    $sql = '';
    $i = 3;
    foreach ($languages as $lang) {
      $sql .= '$' . $i . ', ';
      $i++;
    }
    $sql = trim($sql, ', ');

    $mailer_id = $query->addSql($sql . ')')
      ->execute('mlr_id_pk');

    //    $query = $this->db()->query()
    //      ->addSql('START TRANSACTION;');

    $query = $this->db()->query();
    foreach ($users as $user) {
      $templater = new Templater;
      $templater->setGlobals(array('mailerId' => $mailer_id));
      $templater->load($values['text_' . $user['language']]);

      $unsubscribeLink = 'http://' . SERVER_NAME . '/' .'unsubscribe/?id=' . $user['finish'];
      $templater->setGlobals(array('user_md5' => md5($user['email']), 'user_email' => $user['email']));

      $query
        ->clearSql()
        ->clearParams()
        ->addSql('insert into mails_tbl(mls_mlr_id_fk, mls_email, mls_status, mls_parent_type,')
        ->addSql('mls_subject,')
        ->addSql('mls_parent_id_fk, mls_body, mls_unsubscribe)')
        ->addSql('values($1, $2, $3, $4, $5, $6, $7, $8)')
        ->addParam($mailer_id)
        ->addParam($user['email'])
        ->addParam('wait')
        ->addParam($mailType)
        ->addParam($values['subject_' . $user['language']])
        ->addParam($user['id'])
        ->addParam( $templater->parse($user) )
        ->addParam($unsubscribeLink)
        ->execute();
    }
    //    $query->addSql('COMMIT;')
    //      ->addParam($mailer_id)
    //      ->addParam('wait')
    //      ->addParam($mailType)
    //      ->execute();

    return $this;
  }

  public function subscribe() {
    $email = $this->getRequest()->post('email');
    $result = null;

    $validator = new Validator();

    if ( $validator->isEmpty($email) ) {
      $this->getRequest()->sendHeaderError();

      return false;
    }

    if ( !$validator->isEmail($email) ) {
      $this->getRequest()->sendHeaderError();

      return false;
    }

    if ( $validator->isExistsInDb($email, $this->db()->query(),
      '(select * from users_tbl where usr_notsubscribed = false)s', 'usr_id_pk' ) )
    {
      $this->getRequest()->sendHeaderError();

      return false;
    }

    $subscribe_dt = $this->db()->query()
      ->addSql('update users_tbl set usr_datetime_subscribe = now(), usr_notsubscribed = false')
      ->addSql('where usr_email = $1')
      ->addParam($email)
      ->execute('usr_datetime_subscribe');

    if ( empty($subscribe_dt) ) {
      $lang = $this->db()->query()
        ->addSql('select lng_id_pk as id')
        ->addSql('from languages_tbl')
        ->addSql('where lng_synonym = $1')
        ->addParam( Registry::get('current_language') )
        ->fetchRow(0, false);

      $subscribe_dt = $this->db()->query()
        ->addSql('insert into users_tbl (usr_email, usr_datetime_subscribe, usr_lng_id_fk)values($1, now(), $2)')
        ->addParam($email)
        ->addParam($lang['id'])
        ->execute('usr_datetime_subscribe');
    }

    $result = array(
      'key'   => md5(md5($subscribe_dt) . $email),
      'email' => $email,
    );

    return $result;
  }

  public function unsubscribe() {
    $email = $this->getRequest()->post('email');
    $key = $this->getRequest()->post('key');

    $this->db()->query()
      ->addSql('update users_tbl set usr_notsubscribed = true')
      ->addSql('where usr_email = $1 and md5(md5(usr_datetime_subscribe::VARCHAR)||usr_email) = $2')
      ->addParam($email)
      ->addParam($key)
      ->execute();

    return $this;
  }

  public function mailOpenStat() {
    $mailerId = $this->getRequest()->getParam('idMailer');
    $mdEmail  = $this->getRequest()->getParam('md5Email');

    $this->db()->query()
      ->addSql('update mails_tbl set mls_opened=true where')
      ->addSql('mls_mlr_id_fk=$1 and (md5(mls_email) = $2 or md5(mls_email || \'\r\') = $2)')
      ->addParam($mailerId)
      ->addParam($mdEmail)
      ->execute();

    echo file_get_contents($this->getVariable('path_public') . '/img/src/mail-bg.jpeg');

    return $this;
  }

  public function getSubscribeForm() {
    $this->setTags( __FUNCTION__, array() )
      ->saveCache(__FUNCTION__, null);

    return null;
  }
}
