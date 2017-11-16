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
    \Uwin\Linguistics,
    \Uwin\Controller\Front,
    \Uwin\Validator,
    \Uwin\TemplaterBlitz as Templater,
    \Uwin\Exception\Route as RouteException;

/**
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 */
class CarMarket extends Abstract_
{
  public function getIndex() {
    $id = $this->getRequest()->getParam('id');

    $data = $this->db()->query()
      ->addSql('select id, background, h1_#lang# as h1, title_#lang# as title,')
      ->addSql('description_#lang# as description, keywords_#lang# as keywords,')
      ->addSql('image as image, price, year,')
      ->addSql('car_name_#lang# as car_name, seria as seria,')
      ->addSql('is_original as is_original')
      ->addSql('from car_market_info_vw where id = $1')
      ->addParam($id)
      ->addTag('car-market')
      ->fetchRow();

    if (empty($data)) {
      throw new RouteException;
    }

    $data['photos'] = $this->db()->query()
      ->addSql('select name_#lang# as name, image as image')
      ->addSql('from car_market_photos_vw where car_market_id = $1 order by ord')
      ->addParam($id)
      ->addTag('car-market')
      ->fetchResult();

    $data['features'] = $this->db()->query()
      ->addSql('select header_#lang# as header, text_#lang# as text, icon')
      ->addSql('from car_market_features_vw where car_market_id = $1 order by ord')
      ->addParam($id)
      ->addTag('car-market')
      ->fetchResult();

    $data['descriptions'] = $this->db()->query()
      ->addSql('select header_#lang# as header, text_#lang# as text, image, youtube_id')
      ->addSql('from car_market_descriptions_vw where car_market_id = $1 order by ord')
      ->addParam($id)
      ->addTag('car-market')
      ->fetchResult();

    return [
      'data' => $data,
    ];
  }

  public function getList() {
    $result = $this->db()->query()
      ->addSql('select id, general_features_#lang# as general_features,')
      ->addSql('small_description_#lang# as small_description,')
      ->addSql('image, price, year, car_name_#lang# as car_name, is_original')
      ->addSql('from car_market_vw')
      ->addTag('car-market')
      ->fetchResult(false);

    if (!empty($result)) {
      foreach ($result as $index => &$item) {
        $item['car_name'] = strip_tags($item['car_name']);
        $_general_features = explode("\n", $item['general_features']);
        if (!empty($_general_features)) {
          $item['general_features'] = [];
          foreach ($_general_features as $value) {
            $item['general_features'][] = ['name' => $value];
          }
        }
      }
    }
    return ['cars' => $result];
  }
}
