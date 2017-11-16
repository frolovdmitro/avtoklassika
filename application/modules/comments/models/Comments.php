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
    \Uwin\Validator,
    \Uwin\Controller\Front,
    \Uwin\TemplaterBlitz as Templater;

/**
 * Модель модуля по умолчанию, который обрабатывает:
 *  - Главную страницу
 *  - Статическую страницу
 *  - Страницы ошибок
 *  - Страницу 404
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Comments extends Abstract_
{
  public function addComment() {
    $subject = null;
    $type = null;

    $parent_id = $this->getRequest()->getParam('id');
    $variables = $this->getRequest()->post();

    if ( null == $parent_id ) {
      $parent_id = '0';

      if ( isset($variables['cmt_dsc_id_fk']) && !empty($variables['cmt_dsc_id_fk']) && 'null' !=$variables['cmt_dsc_id_fk']  ) {
        $subject = $variables['cmt_dsc_id_fk'];
        $type = 'discount';
      } elseif ( isset($variables['cmt_bsn_id_fk']) && !empty($variables['cmt_bsn_id_fk']) && 'null' !=$variables['cmt_bsn_id_fk']) {
        $subject = $variables['cmt_bsn_id_fk'];
        $type = 'partner';
      } else {
        if ('discounts' == $this->getRequest()->getParam('moduleRoute') &&
          'desire_comments' == $this->getRequest()->getParam('type')) {
          $subject = $this->getRequest()->get('id');;
          $type = 'desire';
        } elseif ('discounts' == $this->getRequest()->getParam('moduleRoute') ) {
          $subject = $this->getRequest()->get('id');;
          $type = 'discount';
        } elseif ('business' == $this->getRequest()->getParam('moduleRoute') ) {
          $subject = $this->getRequest()->get('id');;
          $type = 'partner';
        }
      }
    } else {
      $query = $this->db()->query()
        ->addSql('select cmt_subject_id_fk, cmt_type from comments_tbl where cmt_id_pk=$1')
        ->addParam($parent_id)
        ->fetchRow(0, false);

      $subject = $query['cmt_subject_id_fk'];
      $type = $query['cmt_type'];
    }

    if ( !isset($variables['cmt_visible']) ) {
      $variables['cmt_visible'] = 'false';
    }

    $this->db()->query()
      ->addSql('insert into comments_tbl(cmt_usr_id_fk, cmt_subject_id_fk, cmt_parent_id_fk,')
      ->addSql('cmt_text, cmt_type, cmt_visible, cmt_ip)')
      ->addSql('values($1, $2, $3, $4, $5, $6, $7)')
      ->addParam($variables['cmt_usr_id_fk'])
      ->addParam($subject)
      ->addParam($parent_id)
      ->addParam($variables['cmt_text'])
      ->addParam($type)
      ->addParam($variables['cmt_visible'])
      ->addParam($this->getRequest()->getRemoteIp())
      ->execute();

    return $this;
  }

  public function editComment() {
    $id = $this->getRequest()->getParam('id');
    $variables = $this->getRequest()->post();

    if ( !isset($variables['cmt_visible']) ) {
      $variables['cmt_visible'] = 'false';
    }

    $this->db()->query()
      ->addSql('update comments_tbl set cmt_visible=$2')
      ->addSql('where cmt_id_pk=$1')
      ->addParam($id)
      ->addParam($variables['cmt_visible'])
      ->execute();

    return $this;
  }

  public function getComments($vars) {
    $data = $this->db()->query()
      ->addSql('select * from comments_vw cmt')
      ->addSql('where cmt.type = $1 and cmt.subject_id = $2')
      ->addParam($vars['type'])
      ->addParam($vars['id'])
      ->fetchResult(false);

    return [
      'comments' => $data,
      'type' => $vars['type'],
    ];
  }

  public function getAddForm($type = '') {
    return [
      'type' => $type,
    ];
  }

  public function addUserComment() {
    $id = $this->getRequest()->getParam('id');

    $validator = new Validator();
    $data = $this->getRequest()->post();
    $form = 'add_comment';

    $rules = $this->getValidateRules($form);
    $lang_variables = $this->getVariables();

    $errors = $validator->validate($form, $rules, $data, $lang_variables);

    $stopwords = explode(',', mb_strtolower(
      $this->getVariable('config_spam_stopwords'))
    );
    foreach ($stopwords as &$word) {
      $word = preg_quote($word, '/');
    }

    $num_found = preg_match_all('/('.join('|', $stopwords).')/i',
      mb_strtolower($data['text']), $matches);
    if (0 < $num_found) {
      $errors[] = [
        'id' => 'text',
        'text' => $this->getVariable('lng_validate_add_comment_text_spam')
      ];
    }

    if ( !empty($errors) ) {
      $errors['errors'] = true;
    }

    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    $data['text'] = strip_tags($data['text']);
    if ( !isset($data['parent_id']) ) {
      $data['parent_id'] = '';
    }

    $id = $this->db()->query()
      ->addSql('insert into comments_tbl(cmt_name, cmt_email, cmt_text,')
      ->addSql('cmt_type, cmt_subject_id_fk, cmt_parent_id_fk, cmt_ip)values')
      ->addSql('($1, $2, $3, $4, $5, $6, $7)')
      ->addParam($data['name'])
      ->addParam($data['email'])
      ->addParam($data['text'])
      ->addParam($data['type'])
      ->addParam($id)
      ->addParam($data['parent_id'])
      ->addParam($this->getRequest()->getRemoteIp())
      ->execute('cmt_id_pk');

    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
      $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'comment.tpl',
      Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());

    $data['id'] = $id;
    $data['datetime'] = date('d/m/Y H:i');

    $this->setVariables($data);

    return [
      'html' => $templater->parse( $this->getVariables() ),
    ];
  }
}
