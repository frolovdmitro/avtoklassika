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
    \Uwin\Controller\Front,
    \Uwin\Registry,
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
class Index extends Abstract_
{
  /**
   * Функция формирует переменные главной странице, которые будут переданы
   * шаблонизатору
   *
   * @return Index
   */
  public function getIndex() {
    return $this;
  }

  /**
   * Метод формирует переменные страницы тизера, которые будут переданы
   * шаблонизатору
   *
   * @return Index
   */
  public function getTeaser() {
    // Отметка о том что страница является тизером
    $this->setVariable('mode_teaser', true);

    return $this;
  }

  public function getSitemap() {
    $rootNodes = $this->db()->query()
      ->addSql('select route as loc, to_char(lastmod, \'YYYY-MM-DD\') || \'T\' || to_char(lastmod, \'HH24:MI:SS\') || \'+00:00\' as lastmod, changefreq, priority, synonym, is_last')
      ->addSql('from sitemap_pages_fn()')
      ->addSql('where enabled = true and synonym != \'ads\'')
      ->addSql('order by order_num')
      ->fetchResult(false);

    $result = [];
    $temp = [];
    if ( empty($rootNodes) ) {
      return [];
    }

    foreach ($rootNodes as $rootNode){
      if ( !empty($rootNode['loc']) ) {
        $rootNode['host'] = $_SERVER['HTTP_HOST'];
        $result[] = $rootNode;
      }

      if ( $rootNode['is_last'] == 'f' ) {
        $nodes = $this->db()->query()
          ->addSql('select route as loc, to_char(lastmod, \'YYYY-MM-DD\') || \'T\' || to_char(lastmod, \'HH24:MI:SS\') || \'+00:00\' as lastmod, changefreq, priority, synonym, is_last')
          ->addSql('from sitemap_pages_fn(\'' . $rootNode['synonym'] . '\')')
          ->addSql('where #languages# and enabled = true')
          ->addSql('order by order_num')
          ->fetchResult(false);

        if ( empty($nodes) ) {
          continue;
        }

        foreach ($nodes as $node){
          if ( empty($node['loc']) ) {
            continue;
          }
          $node['host'] = $_SERVER['HTTP_HOST'];
          $result[] = $node;
        }
      }

    }

    $pathToView = dirname(__DIR__) . DIR_SEP . 'v' . DIR_SEP .
        'index' . DIR_SEP;
    $templater = new Templater($pathToView . 'sitemapXml.tpl', Front::getInstance()->getView());

    $content = $templater->parse([
      'url' => $result,
    ]);

    $settings = Registry::get('path');
    file_put_contents($settings['public'] . 'sitemap.xml', $content);

    $this;
  }
}
