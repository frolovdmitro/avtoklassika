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
    \Uwin\Linguistics,
    \Uwin\Registry,
    \Uwin\Controller\Front,
    \Uwin\TemplaterBlitz as Templater,
    \Uwin\Exception\Route as RouteException;

/**
 * Модель модуля статических страниц
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class News extends Abstract_
{
  const NEWS_ON_PAGE = 10;

  public function __construct() {
    $this->ling = new Linguistics();
    $this->setVariable('module', 'news');

    $params = $this->getRequest()->getParams();

    $title = $this->getVariable('lng_index_title');
    $description = $this->getVariable('lng_index_description');
    $keywords = $this->getVariable('lng_index_keywords');

    $meta_tags = [
      'title' => $title,
      'description' => $description,
      'keywords' => $keywords,
    ];

    if ( isset($params['category']) ) {
      $meta_tags = $this->db()->query()
        ->addSql('select title_#lang# as title,')
        ->addSql('description_#lang# as description, robots,')
        ->addSql('keywords_#lang# as keywords from news_categories_tree_vw')
        ->addSql('where lower(synonym)=lower($1)')
        ->addParam($params['category'])
        ->addTag('news')
        ->fetchRow(0);
    }

    $this->setVariables($meta_tags);

  }

  public function getSynonym($value, $params = array()) {
    if (!empty($value)) {
      return $value;
    }

    $synonym = $this->ling->getWebTranslit($params['caption']);

    if (mb_strlen($synonym) >= 128) {
      $synonym = mb_substr($synonym, 0, 127);
    }

    // Делаем синоним уникальным
    $selectResult = $this->db()->query()
      ->addSql('select nw_id_pk from news_tbl where lower(nw_synonym)=lower($1)')
      ->addParam($synonym)
      ->addTag('news')
      ->fetchRow(0);

    if (!empty($selectResult)) {
      $synonym .= date('-d-m-Y');
    }

    return $synonym;
  }

  public function getCanonical($value, $params = array()) {
    if (!empty($value)) {
      return $value;
    }

    return $this->getRequest()->getHost(true) . '/news/'
      . $this->getSynonym($params['synonym'], array('name' => $params['name']))
      . '/';
  }


  public function getIndex() {
    $synonym = $this->getRequest()->getParam('synonym');

    $query = $this->db()->query()
      ->addSql('select id, synonym, name_#lang# as name,')
      ->addSql('title_#lang# as title, text_#lang# as text,')
      ->addSql('description_#lang# as description, count_comments,')
      ->addSql('keywords_#lang# as keywords, date, robots')
      ->addSql('from news_info_vw')
      ->addSql('where lower(synonym)=lower($1) and #languages#')
      ->addParam($synonym);

    $news = $query->addTag('news')
      ->fetchRow(0);

    if (empty($news)) {
      throw new RouteException;
    }

    $registry = Registry::getInstance();
    $path = $registry->path;

    $filename = str_replace('{{row_id}}', $news['id'], 'images/news/{{row_id}}/css/addition.css');
    $filepath = $path['uploadDir'] . $filename;

    if (file_exists($filepath)) {
      $static_server = $this->getVariable('url_staticServer');
      $this->setVariable('addition_css', $static_server . '/uploads/' . $filename . '?' . md5_file($path['uploadDir'] . $filename));
    // var_dump($this->getVariable('addition_css'));
    }

    $news['comment_data'] = [
      'id' => $news['id'],
      'type' => 'news',
    ];

    return $news;
  }

  public function getList() {
    $result = [];

    $category = $this->getRequest()->getParam('category');
    if ( empty($category) ) {
      $category = 'all';
    }

    $car = $this->getRequest()->getParam('car');
    if ( empty($car) ) {
      $car = 'all';
    }

    return [
      'category_synonym' => $category,
      'car_synonym' => $car,
    ];
  }

  public function getPageAjax() {
    $count_on_page = 10;
    $page = ~~$this->getRequest()->get('page');
    if ($page == 0) {
      $page = 1;
    }

    $params = $this->getRequest()->getParams();
    $category = $params['category'];
    $car = $params['car'];

    $i = 3;

    $query = $this->db()->query()
      ->sql('select name_#lang# as name,
        coalesce(description_#lang#, text_#lang#) as description,
        image, date, synonym, count_comments,
        ceil((count(*) OVER())::FLOAT / $2) AS full_count
        from news_list_vw where #languages#');
    if ('all' !== $category) {
      $query->addSql('and category_synonym = $3');
      $i++;
    }
    if ('all' !== $car) {
      $query->addSql('and car_synonym = $' . $i);
    }
    $query->addSql('limit $2 offset $1')
      ->addTag('news')
      ->addParam(($page-1) * $count_on_page)
      ->addParam($count_on_page);
    if ('all' !== $category) {
      $query->addParam($category);
    }
    if ('all' !== $car) {
      $query->addParam($car);
    }
    $data = $query->fetchResult();

    foreach($data as &$news) {
      $news['description'] = strip_tags($news['description']);
      $news['description'] = $this->ling->shortedText($news['description'],
        170, false);
    }

    $pathToView = dirname(__DIR__) . DIR_SEP . 'views' . DIR_SEP .
        $this->getRequest()->getModuleName() . DIR_SEP;
    $templater = new Templater($pathToView . 'pageContent.tpl', Front::getInstance()->getView());
    $templater->setGlobals($this->getVariables());
    $this->setVariable('news', $data);

    return [
      'html' => $templater->parse( $this->getVariables() ),
      'pages' => $data[0]['full_count']
    ];
  }

  public function getNewsBar() {
    $news = $this->db()->query()
      ->addSql('select name_#lang# as name, synonym, image, date')
      ->addSql('from  last_news_vw')
      ->addSql('where #languages#')
      ->addSql('limit 4')
      ->addTag('news')
      ->fetchResult();

    return [
      'news' => $news,
    ];
  }

  public function getCategories() {
    $params = $this->getRequest()->getParams();

    $total_count = $this->db()->query()
      ->addSql('select coalesce(sum(count), 0)')
      ->addSql('from news_categories_tree_vw')
      ->addSql('where #languages#')
      ->addTag('news')
      ->fetchField(0);

    $this->setVariable('total_count', $total_count);

    if ( !isset($params['category']) ) {
      $params['category'] = 'all';
    }
    $this->setVariable('current_category', $params['category']);

    if ( !isset($params['car']) ) {
      $params['car'] = 'all';
    }
    $this->setVariable('current_car', $params['car']);

    $categories = $this->db()->query()
      ->addSql('select name_#lang# as name, synonym, cars, count')
      ->addSql('from news_categories_tree_vw')
      ->addSql('where #languages#')
      ->addTag('news')
      ->fetchResult();

    if ( empty($categories) ) {
      return [];
    }

    foreach ($categories as &$category){
      if ($category['synonym'] == $params['category']) {
        $category['current'] = true;
      }

      $category['cars'] = json_decode($category['cars'], true);
      if ( !empty($category['cars']) ) {
        foreach ($category['cars'] as &$car) {
          if ($car['car_synonym'] == $params['car']) {
            $car['current'] = true;
          }
        }
      }
    }

    return [
      'categories' => $categories
    ];
  }
}
