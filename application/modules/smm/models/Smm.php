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
use \Uwin\Model\Abstract_    as Abstract_;

/**
 * Модель модуля управления социальными сетями, которые связаны с сайтом
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Smm extends Abstract_
{
  public function getIndex() {
    return null;
  }

  public function getSocialBar() {
    $buttons = $this->db()->query()
      ->addSql('select smm_name_#lang# as name, smm_url as url,')
      ->addSql('  smm_type as type')
      ->addSql('from smm_tbl')
      ->addSql('where smm_enabled = true order by smm_order')
      ->addTag('smm')
      ->fetchResult();

    $this->setVariable('buttons', $buttons);

    return $this;
  }
}
