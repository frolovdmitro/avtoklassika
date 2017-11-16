<?php
/**
 * Uwin CMS
 *
 * Файл содержащий модель модуля статических страниц
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_,
    \Uwin\Controller\Front,
    \Uwin\Registry,
    \Uwin\Linguistics,
    \Uwin\TemplaterBlitz     as Templater,
    \Uwin\Exception\Route    as RouteException;

/**
 * Модель модуля статических страниц
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Pages extends Abstract_
{
  /**
   * Метод возвращает текст указанной страницы
   *
   * @param $synonym - Синоним страницы
     *
   * @return string
   * @throws RouteException
   */
  public function _getPageText($synonym) {
    $page = $this->db()->query()
      ->sql('
        select pg_id_pk as id, pg_synonym as synonym, pg_robots as robots,
          coalesce(pg_title_#lang#, pg_caption_#lang#) as title,
          pg_caption_#lang# as caption, pg_css_class as css_class,
          pg_keywords_#lang# as keywords, pg_description_#lang# as description,
          pg_text_#lang# as text
        from pages_tbl
        where pg_synonym=$1 and pg_enabled=true and #pg_languages#')
      ->addParam($synonym)
      ->addTag('page-' . $synonym)
      ->fetchRow(0, false);

    if ( empty($page) ) {
      throw new RouteException;
    }

    $page[str_replace('-', '_', $page['synonym'])] = true;

    return $page;
  }

  /**
   * Метод формирует переменные статической страницы, которые будут переданы
   * шаблонизатору
   *
   * @return Pages
   */
  public function getIndex() {
    $name = $this->getRequest()->getParam('page');
    $variables = $this->_getPageText($name);

    $registry = Registry::getInstance();
    $path = $registry->path;

    $filename = str_replace('{{row_id}}', $variables['id'], 'images/pages/{{row_id}}/css/addition.css');
    $filepath = $path['uploadDir'] . $filename;

    if (file_exists($filepath)) {
      $static_server = $this->getVariable('url_staticServer');
      $this->setVariable('addition_css', $static_server . '/uploads/' . $filename . '?' . md5_file($path['uploadDir'] . $filename));
    }

    $this->setVariable('page', $variables)
      ->setVariables($variables);

    return $this;
  }

  public function getSynonym($value, $params = array()) {
      if ( !empty($value) ) {
          return $value;
      }

    $ling = new Linguistics;
    $synonym = $ling->getWebTranslit($params['caption']);

    if ( mb_strlen($synonym) >= 128 ) {
          $synonym = mb_substr($synonym, 0, 127);
    }

    // Делаем синоним уникальным
    $selectResult = $this->db()->query()
      ->addSql('select pg_id_pk from pages_tbl where lower(pg_synonym)=lower($1)')
      ->addParam($synonym)
      ->fetchRow(0, false);

    if ( !empty($selectResult) ) {
          $synonym .= date('-d-m-Y');
    }

    return $synonym;
  }

  public function getCanonical($value, $params = array()) {
    if (!empty($value)) {
      return $value;
    }

    return $this->getRequest()->getHost(true) . '/'
      . $this->getSynonym($params['synonym'], array('name' => $params['caption']))
      . '/';
  }
}
