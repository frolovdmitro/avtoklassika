<?php
/**
 * Uwin CMS
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2014 UwinArt Development (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_,
    \Uwin\Registry,
    \Uwin\Linguistics,
    \Uwin\Xml;

/**
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2014 UwinArt Developmeny (http://uwinart.com)
 */
class SeoScripts extends Abstract_
{
  private $_cronMethods = [];

  /**
   * undocumented function
   *
   * @return void
   */
  public function postingLinks() {
    // Получаем неразмещенные ссылки
    $unplaced_links = $this->db()->query()
      ->addSql('select id, link, anchor, count_unplaced, languages, types')
      ->addSql('from unplaced_links_vw')
      ->fetchResult(false);

    if ( empty($unplaced_links) ) {
      return null;
    }

    foreach ($unplaced_links as $link){
      $free_pages = $this->db()->query()
        ->addSql('select * from free_pages_vw')
        ->addSql("where type = any('#1')")
        ->addSql('and url != $1 and \'{#2}\' not in (links)')
        ->addSql("order by random() limit $2")
        ->addReplacement($link['types'])
        ->addReplacement($link['link'])
        ->addParam($link['link'])
        ->addParam($link['count_unplaced'])
        ->fetchResult(false);

      if ( empty($free_pages) ) {
        continue;
      }

      foreach ($free_pages as $page){
        $this->db()->query()
          ->addSql('insert into seo_links_on_pages_tbl(slp_url, slp_type_page, slp_sol_id_fk)')
          ->addSql('values($1, $2, $3)')
          ->addParam($page['url'])
          ->addParam($page['type'])
          ->addParam($link['id'])
          ->execute();
      }
    }

    return true;
  }
}
