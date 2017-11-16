<?php
/**
 * Uwin CMS
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2013 UwinArt Development (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_,
    \Uwin\Registry,
    \Uwin\Controller\Front,
    \Uwin\Exception\Route as RouteException;

/**
 *
 * @author    Alex Kolomiets (a.kolomiets@uwinart.com)
 * @copyright Copyright (c) 2012-2013 UwinArt Development (http://uwinart.com)
 */
class Slider extends Abstract_
{
  /**
   * @return Slider
   */
  public function getIndex() {
    return $this;
  }

  public function getSlider() {
    $slides = $this->db()->query()
      ->addSql('select name_#lang# as name,')
      ->addSql('image_#lang# as image, link, target_blank')
      ->addSql('from slider_vw')
      ->addSql('where #languages#')
      ->addTag('slider')
      ->fetchResult();

    if ('true' === $this->getVariable('config_index_use_autoslide')) {
      shuffle($slides);
    }


    return [
      'config_index_delay' => $this->getVariable('stg_index_delay'),
      'config_index_use_autoslide' => $this->getVariable('stg_index_use_autoslide'),
      'slides' => $slides
    ];
  }
}
