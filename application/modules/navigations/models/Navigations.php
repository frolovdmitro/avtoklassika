<?php
/**
 * Uwin CMS
 *
 * Файл содержит модель модуля управления навигационнными меню на сайте
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_,
    \Uwin\Registry,
    \Uwin\Cacher\Memcached;

/**
 * Модель модуля управления навигационнными меню на сайте
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Navigations extends Abstract_
{
  /**
   * @param string $name
   * @param int    $parent
   *
   * @return array
   */
  private function _getMenuData($name, $parent) {
    $query = $this->db()->query()->sql(
      'select nvi_id_pk as id, nvg_css_class as menu_class, '
      . '   nvi_name as name, nvi_address as address, nvi_css_class as item_class, '
      . '   childs, nvg_name_#lang# as caption, nvi_rel as rel,'
      . '   case nvi_target_blank when true then true else null end as target_blank '
      . 'from navitems_tbl nvi '
      . '   left join navigations_tbl on nvi_nvg_id_fk = nvg_id_pk'
      . '   left join (select count(nvi_id_pk) as childs, nvi_parent_id_fk, '
      . '           nvi_lng_id_fk '
      . '           from navitems_tbl '
      . '           group by nvi_parent_id_fk, nvi_lng_id_fk) chld '
      . '       on chld.nvi_parent_id_fk = nvi_id_pk '
      . '           and chld.nvi_lng_id_fk = nvi.nvi_lng_id_fk '
      . '   left join languages_tbl on nvi.nvi_lng_id_fk = lng_id_pk '
      . 'where nvg_type =$1 and nvg_enabled = true and nvi_visible = true '
      . '   and lng_synonym=$2');

    $result = $query->addSql('and coalesce(nvi.nvi_parent_id_fk,0) = $3', $parent)
      ->addSql('order by nvi_order')
      ->addParam($name)
      ->addParam( Registry::get('current_language') )
      ->addParam($parent, $parent)
      ->addTag('navigations')
      ->addTag('menu_' . $name)
      ->fetchResult(true, 86400);

    return $result;
  }

  /**
   * @param string $name
   * @param int $parent = null
   *
   * @return array
   */
  private function _getMenu($name, $parent = null) {
    $items = $this->_getMenuData($name, (int)$parent);

    foreach ($items as $id => $item) {
      if ( $this->getRequest()->getCurrentUrl() == rtrim($item['address'], '/') ) {
        $items[$id]['active'] = true;
      }

      if (0 < (int)$item['childs']) {
        $items[$id]['subitems'] = $this->_getMenu($name, $item['id']);
      }
    }

    return $items;
  }

  /**
   * @param string $type = null
   *
   * @return array|null
   */
  public function getMenu($type = 'main') {
    $items = $this->_getMenu($type);

    if ( empty($items) ) {
      return null;
    }

    $result[$type] = array(
      'class' => $items[0]['menu_class'],
      'caption' => $items[0]['caption'],
      'items' => $items,
    );

    return $result;
  }

  public function getLanguages() {
    $current_lang = Registry::get('current_language');

    $languages = $this->db()->query()
      ->addSql('select lng_short_name as name, lng_synonym as syn,')
      ->addSql('case when lng_default = true then null else lng_synonym end as synonym')
      ->addSql('from languages_tbl')
      ->addSql('where lng_enabled = true')
      ->addSql('order by lng_order')
      ->addTag('languages')
      ->fetchResult();

    $current_name = '';
    $host = $this->getRequest()->serverName();
    foreach ($languages as $id => &$lang) {
      if ($lang['syn'] === $current_lang) {
        $current_name = $lang['name'];
        unset($languages[$id]);
        continue;
      }

      $lang['host'] = rtrim($host, '/');
      $lang['url'] = $this->getRequest()->getCurrentUrl();
    }

    return [
      'current' => $current_name,
      'languages' => $languages,
    ];
  }
}
