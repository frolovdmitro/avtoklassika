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

/**
 * Модель модуля управления социальными сетями, которые связаны с сайтом
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Seo extends Abstract_
{
  public function multiadd() {
    $vars = $this->getRequest()->post();

    $links = explode("\n", $vars['text']);
    foreach ($links as $link){
      if ( empty($link) ) {
        continue;
      }

      $link = rtrim($link, ';');
      $data = explode(';', $link);

      if ( !isset($data[3]) ) {
        continue;
      }

      $categories = explode(',', $data[0]);

      $id = $this->db()->query()
        ->addSql('insert into seo_links_tbl(sol_link, sol_anchor, sol_count)')
        ->addSql('values($1, $2, $3)')
        ->addParam($data[1])
        ->addParam($data[2])
        ->addParam((int)$data[3])
        ->execute('sol_id_pk');

      if ( is_array($categories) ) {
        foreach ($categories as $cat_id) {
          if (0 != $cat_id) {
            $this->db()->query()
              ->addSql('insert into seo_links_categories_page_tbl(slcp_scp_id_fk, slcp_sol_id_fk)')
              ->addSql('values($1, $2)')
              ->addParam($cat_id)
              ->addParam($id)
              ->execute();
          }
        }
      }
    }

    return $this;
  }

  /**
    * undocumented function
    *
    * @return void
    */
  public function getLinksBar() {
    $url = $this->getRequest()->getCurrentUrl(false);
    $url = rtrim($url, '/') . '/';
    $links = $this->db()->query()
      ->addSql('select link, anchor')
      ->addSql('from links_by_page_vw')
      ->addSql('where page_url = $1')
      ->addParam($url)
      ->fetchResult(false);

    return [
      'links' => $links
    ];
  }
}
