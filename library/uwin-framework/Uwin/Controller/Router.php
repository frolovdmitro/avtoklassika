<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Controller\Router, который является
 * маршрутизатором и выполняет все действия по определению марщрута к
 * контроллеру на основе преданного ему URL
 *
 * @category   Uwin
 * @package    Uwin\Controller
 * @subpackage Router
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Controller, к которому относится класс Router
 */
namespace Uwin\Controller;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Router\Route\Abstract_   as RouteAbstract,
    \Uwin\Exception\Route                     as Exception,
    \Uwin\Cacher\Interface_                   as Cacher,
    \Uwin\Controller\Router\Route\StaticRoute,
    \Uwin\Controller\Router\Route\DynamicRoute;

/**
 * Класс, который является маршрутизатором и выполняет все действия по
 * определению марщрута к контроллеру на основе преданного ему URL
 *
 * @category   Uwin
 * @package    Uwin\Controller
 * @subpackage Router
 * @author     Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Router
{
  /**
   * Ссылка на объект класса кешировщика
   * @var \Uwin\Cacher\Interface_
   */
  private $_cacher = null;

  /**
   * Переменные маршрута, переданный маршрутизтору, на основе которых он
   * строит маршрут к контроллеру
   * @var array
   */
  private $_routeVariable = array();

  /**
   * Имя маршрута, определенное с помощью $_routeVariable
   * @var string
   */
  private $_routeName = null;

  /**
   * Имя маршрута по-умолчанию (если маршрутизатору не был передан маршрут)
   * @var string
   */
  private $_routeNameIndex = 'index';

  /**
   * Имя маршрута, которое используется, если переданное имя маршрута не
   * найдено в правилах маршрутизации
   * @var string
   */
  private $_routeNameDefault = null;

  /**
   * Массив правил маршрутизации, определенных в маршрутизаторе
   * @var array
   */
  private $_routeRules = array();

  /**
   * Массив тегов для главной страницы
   * @var array
   */
  private $_indexTags = array();

  /**
   * Передача переменных, полученых маршрутизатором, объекту класса
   * Uwin\Controller\Request
   *
   * @param array $params - Массив переменных полученных маршрутизатором
   *
   * @return bool
   */
  private function _setRequestParams($params)
  {
    $request = Request::getInstance();

    foreach ($params as $param => $value) {
      $request->setParam($param, $value);

      if ('module' === $param) {
        $request->setModuleName($value);
      } else if ('controller' === $param) {
        $request->setControllerName($value);
      } else if ('action' === $param) {
        $request->setActionName($value);
      }
    }

    return true;
  }

  /**
   * Метод возвращает правило маршрутизации
   *
   * @return array
   */
  private function _getRouteRule() {
    $keyCache = null;

    if ( $this->useCacher() ) {
      $keyCache = 'route_' . md5( serialize( $this->getRouteVariable() ) );
      $value = $this->_cacher->get($keyCache);

      if (false !== $value) {
        return $value;
      }
    }

    $value = false;
    foreach ($this->_routeRules[$this->getRouteName()] as $route) {
      /**
       * @var \Uwin\Controller\Router\Route\Abstract_ $route
       */
      $rule = $route->match( $this->getRouteVariable() );
      if (false !== $rule) {
        $value = array(
          'rule' => $rule,
          'tags' => $route->getCacheTags()
        );
        break;
      }
    }

    if ( $this->useCacher() ) {
      $this->_cacher->set($keyCache, $value, 86400, 'tg_route');
    }

    return $value;
  }

  /**
   * Конструктор класса. Добавляет правила маршрутизации по умолчанию
   */
  public function __construct()
  {
    $this->createDefaultRouteRules();
  }

  /**
   * Метод устаналивает объект класса, который будет отвечать за кеширование
   *
   * @param \Uwin\Cacher\Interface_ $cacher - Объект класса кешировщика
   *
   * @return Router
   */
  public function setCacher(Cacher $cacher)
  {
    $this->_cacher = $cacher;

    return $this;
  }

  /**
   * Метод возвращает признак того, используется кеширование или нет
   *
   * @return bool
   */
  public function useCacher()
  {
    if ( empty($this->_cacher) ) {
      return false;
    }

    return true;
  }

  /**
   * Метод добавляет правила маршрутизации по умолчанию
   *
   * @return Router
   */
  public function createDefaultRouteRules()
  {
    // Правило, для маршрута по-умолчанию
    $this
      ->removeRouteRules($this->_routeNameIndex)
      ->addRouteRules(
        $this->_routeNameIndex,
        new StaticRoute('',
        array('module'     => 'default',
        'controller' => 'index',
        'action'     => 'index'
      ),
      $this->getTagsRouteIndex()
    )
  );

    return $this;
  }

  /**
   * Метод добавляет правило маршрутизации переданное аргументами методу
   *
   * @param string        $name  - Имя маршрута
   * @param RouteAbstract $route - Объект который содержит данные маршрутизации
   *
   * @return Router
   */
  public function addRouteRules($name, RouteAbstract $route)
  {
    $this->_routeRules[$name][] = $route;

    return $this;
  }

  /**
   * Удаляем правила маршрутизации для указанного маршрута
   *
   * @param string $name - имя правила
   *
   * @return Router
   */
  public function removeRouteRules($name) {
    unset($this->_routeRules[$name]);

    return $this;
  }

  public function addRoutesFiles(array $files) {
    foreach ($files as $file) {
      $routeRules = json_decode(file_get_contents($file), true);
      $module = basename(pathinfo($file)['dirname']);

      foreach ($routeRules as $route => $rule){
        $name = explode('/', $route)[0];

        if ( !isset($rule['module']) ) {
          $rule['module'] = $module;
        }

        if ( !isset($rule['controller']) ) {
          $rule['controller'] = $rule['module'];
        }

        if (false === strpos($route, ':') ) {
          $this->addRouteRules($name, new StaticRoute($route, $rule));
        } else {
          $this->addRouteRules($name, new DynamicRoute($route, $rule));
        }
      }
    }

    return $this;
  }

  /**
   * Метод устанавливает переменные маршрута переданные маршрутизатору
   *
   * @param string $route - Переменные маршрута
   *
   * @return Router
   */
  public function setRouteVariable($route)
  {
    $route = urldecode($route);

    $route = trim($route, '/\\');
    $route = explode('/', $route);

    $this->_routeVariable = $route;

    // Получение имени правила маршрутизации
    $this->setRouteName();

    return $this;
  }

  /**
   * Метод возвращает массив переменных маршрута, переданных маршрутизатору
   *
   * @return array
   */
  public function getRouteVariable()
  {
    return $this->_routeVariable;
  }

  /**
   * Метод устанавливает имя маршрута, который используется если маршрут не
   * указан
   *
   * @param string $name - Имя маршрута
   *
   * @return Router
   */
  public function setRouteNameIndex($name)
  {
    $this->_routeNameIndex = $name;

    return $this;
  }

  /**
   * Метод возвращает имя маршрута, который используется если маршрут не
   * указан
   *
   * @return string
   */
  public function getRouteNameIndex()
  {
    return $this->_routeNameIndex;
  }

  /**
   * Метод устанавливает массив тегов для главной страницы
   *
   * @param array $tags - массив тегов
   *
   * @return Router
   */
  public function setTagsRouteIndex(array $tags) {
    $this->_indexTags = $tags;

    return $this;
  }

  /**
   * Метод очищает массив тегов для главной страницы
   *
   * @return Router
   */
  public function clearTagsRouteIndex() {
    $this->_indexTags = array();

    return $this;
  }

  /**
   * Метод возвращает массив тегов для главной страницы
   *
   * @return array
   */
  public function getTagsRouteIndex() {
    return $this->_indexTags;
  }
  /**
   * Метод устанавливает имя маршрута, которое используется, если переданное
   * имя маршрута не найдено в правилах маршрутизации
   *
   * @param string $name - Имя маршрута
   *
   * @return Router
   */
  public function setRouteNameDefault($name)
  {
    $this->_routeNameDefault = $name;

    return $this;
  }

  /**
   * Метод возвращает имя маршрута, которое используется, если переданное
   * имя маршрута не найдено в правилах маршрутизации
   *
   * @return string
   */
  public function getRouteNameDefault()
  {
    return $this->_routeNameDefault;
  }

  /**
   * Метод, который устанавливает имя маршрута который будет использовать
   * маршрутизатор
   *
   * @param string $name Имя маршрута. Если не указано, то оно попределяется с переменных маршрута
   *
   * @return bool
   */
  public function setRouteName($name = null)
  {
    if (null === $name) {
      $route = $this->getRouteVariable();
      $this->_routeName = $route[0];

      return $this->_routeName;
    }

    $this->_routeName = $name;

    return true;
  }

  /**
   * Метод, который возвращает имя маршрута который будет использовать
   * маршрутизатор
   *
   * @return string
   * @throws Uwin\Controller\Router\Exception Ошибка работы маршрутизатора
   */
  public function getRouteName()
  {
    // Если имя маршрута не указано используется главный маршрут
    if ( empty($this->_routeName) ) {
      return $this->_routeNameIndex;
    }

    // Если имя маршрута не найдено в правилах маршрутизации, то
    if ( !array_key_exists($this->_routeName, $this->_routeRules) ) {
      // Если не указано имя маршрута которое используется, когда не найден
      // маршрут
      if ( null === $this->_routeNameDefault ) {
        throw new Exception('Router error: route name not found in route rules', 701);
      }

      // Если не найден маршрут, который используется, когда не найден
      // маршрут :), то используется имя маршрута ошибки
      if ( !array_key_exists($this->_routeNameDefault, $this->_routeRules) ) {
        throw new Exception('Router error: route name "' . $this->_routeNameDefault . '" not found in route rules', 701);
      }

      return $this->_routeNameDefault;
    }

    return $this->_routeName;
  }

  /**
   * Передача в объект класса Uwin\Controller\Request всех переменных,
   * которые определил маршрутизатор
   *
   * @return bool
   * @throws \Uwin\Controller\Router\Exception
   */
  public function route()
  {
    $params = $this->_getRouteRule();

    if (false === $params) {
      throw new Exception('Router error: route not found in route rules', 702);
    }
    if ( $this->useCacher() && isset($params['tags']) && !empty($params['tags'])) {
      $page = $this->_cacher->get( md5(serialize($params)) );

      if (false !== $page) {
        echo $page;

        return false;
      }
    }

    $this->_setRequestParams($params['rule']);

    return true;
  }

  /**
   * Сохранение в кеш текста страницы
   *
   * @param $body - текст страницы
   *
   * @return Router
   */
  public function cachePage($body) {
    $params = $this->_getRouteRule();

    if ( $this->useCacher() && isset($params['tags']) && !empty($params['tags']) ) {
      $this->_cacher->set(md5(serialize($params)), $body, 7200, $params['tags']);
    }

    return $this;
  }
}
