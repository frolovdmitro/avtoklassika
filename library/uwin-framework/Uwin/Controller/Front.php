<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Controller\Front, который является главным
 * контроллером, и решает кому передавать управление (так сказать дерижер для всех
 * остальных классов)
 *
 * @category  Uwin
 * @package   Uwin\Controller
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Controller, к которому относится класс Front
 */
namespace Uwin\Controller;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Registry                    as Registry;
use \Uwin\Layout                      as Layout;
use \Uwin\View                        as View;
use \Uwin\Auth                        as Auth;
use \Uwin\Exception\Route             as RouteException;
use \Uwin\Controller\Exception        as ControllerException;
use \Uwin\Cacher\Memcached            as Memcached;

/**
 * Класс, который является главным контроллером и решает кому передавать
 * управление (так сказать дирижер для всех остальных классов)
 *
 * @category  Uwin
 * @package   Uwin\Controller
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Front
{
  private $_forwarded = false;

  /**
   * Ссылка на объект данного класса
   * @var Front
   */
  private static $_instance = null;

  /**
   * Ссылка на объект класса маршрутизатора
   * @var Router
   */
  private $_router = null;

  /**
   * Ссылка на объект класса вида
   * @var Uwin\View
   */
  private $_view = null;

  /**
   * Ссылка на объект класса вида
   * @var Request
   */
  private $_request = null;

  /**
   * Имя модуля, куда передается управление
   * @var string
   */
  private $_moduleClass = null;

  /**
   * Имя класса контроллера, куда передается управление
   * @var string
   */
  private $_controllerClass = null;

  /**
   * Имя действия в классе, куда передается управление
   * @var string
   */
  private $_actionClass = null;

  /**
   * Полное имя файла контроллера, куда передается управление
   * @var string
   */
  private $_fileController = null;

  private $_pageFeatures = [];

  /**
   * @var string
   */
  private $_mode = 'index';

  /**
   * Приватный констркутор, используется для того, чтобы запретить создавать
   * объект класса напрямую
   *
   * @return Front
   */
  private function __construct() {}

  /**
   * Приватный метод __clone, используется для того, чтобы запретить
   * клонировать объект класса
   *
   * @return void
   */
  private function __clone() {}

  /**
   * Метод, который возвращает полный путь к файлу контроллера
   *
   * @return string
   */
  private function _buildPathByFileController()
  {
    $registry = Registry::getInstance();

    $file = $registry['path']['modules'] .
             $this->_moduleClass . DIR_SEP . 'controllers' . DIR_SEP .
             $this->_controllerClass . '.php';

    return $file;
  }

  /**
   * Метод, если не указаны имя модели, контроллера, вида, получает их у
   * класса Uwin\Controller\Request, выполняет преобразования, чтобы получить
   * имя класса контроллера и имя действия контроллера и передает их по ссылке
   * через параметры метода
   *
   * @param string &$module Имя модуля
   * @param string &$controller Имя контроллера
   * @param string &$action Имя действия
   * @return bool
   */
  private function _setPartsControllerClass(&$module = null, &$controller = null, &$action = null)
  {
    $request = $this->getRequest();

    if (null === $module) {
      $module = $request->getModuleName();
    }

    if (null === $controller) {
      $controller = $request->getControllerName();
    }

    if (null === $action) {
      $action = $request->getActionName();
    }

    $mode = $this->getMode();

    // Если включен режим отличный от INDEX(работающий сайт) и загружаемый
    // модуль не модуль администратора(administrator) и в сессии пользователя
    // нет информации о том что он администратор и метод HTTP запроса не POST,
    // то формируем имя модуля, котроллера и действия соответствующего режима сайта
    if ( 'index' != $mode && 'administrator' != $module &&
      'errorsTexts' != $action &&
      !Auth::getInstance()->setStorageNamespace('UwinAuthAdmin')->hasIdentity()
      && !$request->isPost() && !$this->_forwarded
    ) {
      $settings = Registry::get('stg');
      $url = str_replace( '/', '_', trim($request->getCurrentUrl(), '/') );

      if ( array_key_exists($mode, $settings) &&
         array_key_exists($url, $settings[$mode])
      ) {
        $module = $settings[$mode][$url]['module'];
        $controller = $settings[$mode][$url]['controller'];
        $action = $settings[$mode][$url]['action'];
      } else {
        $module = 'default';
        $controller = 'Index';
        $action = $this->getMode();
      }
      $request->setActionName($action);
    }
    Auth::getInstance()->setStorageNamespace('UwinAuth')->hasIdentity();
    $this->_moduleClass = $module;
    $this->_controllerClass = $controller = ucfirst($controller) . 'Controller';
    $this->_actionClass = $action = $action . 'Action';

    return true;
  }

  /**
   * Метод, который на основе запроса формирует полный путь к файлу
   * контроллера
   *
   * @param string &$module Имя модуля
   * @param string &$controller Имя контроллера
   * @param string &$action Имя действия
     *
   * @return string
   */
  private function _getControllerFile(&$module = null, &$controller = null, &$action = null)
  {
    $this->_setPartsControllerClass($module, $controller, $action);
    $file = $this->_buildPathByFileController();

    $this->_fileController = $file;

    return $file;
  }

  /**
   * Метод возвращает ссылку на объект класса Uwin\Controller\Front
   *
   * @return Front
   */
  public static function getInstance()
  {
    if ( null === self::$_instance ) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  /**
   * Возвращает путь к файлу, где расположен класс модели, с которой вид
   * получает данные
   *
   * @param string $module Имя модуля
   * @param string $controller Имя контроллера
     *
   * @return string
   */
  public function getModelFile($module, $controller)
  {
    $controller = ucfirst($controller);

    $reginstry = Registry::getInstance();

    $file = $reginstry['path']['modules'] . $module . DIR_SEP .
      'models' . DIR_SEP . $controller. '.php';

    return $file;
  }

    /**
     *
     * @param \Uwin\Model\Abstract_ $model
     * @param string $action
     *
     * @return string
     * @throws Exception
     */
  public function getModelAction($model, $action)
  {
    $method = 'get' . $action;
    if ( !method_exists($model, $method) ) {
      throw new ControllerException('Controller error: method "' . $method . '" in model "' . get_class($model) . '" not found', 1002);
    }

    return $method;
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function setPageFeatures($data)
  {
    $this->_pageFeatures = $data;

    return $this;
  }

  /**
   * Метод, устанавливающий объект класса Uwin\View
   *
   * @param \Uwin\View $view - Объект вида
   *
   * @return Front
   */
  public function setView(View $view)
  {
    $this->_view = $view;

    return $this;
  }

  /**
   * Метод, возвращающий объект класса Uwin\View
   *
   * @return \Uwin\View
   */
  public function getView()
  {
    return $this->_view;
  }


  /**
   * Метод, устанавливающий объект класса Uwin\Controller\Router
   *
   * @param Router $router Объект класса маршрутизатора
     *
   * @return Front
   */
  public function setRouter(Router $router)
  {
    $this->_router = $router;

    return $this;
  }

  /**
   * Метод, возвращающий объект класса Uwin\Controller\Router
   *
   * @return Router
   */
  public function getRouter()
  {
    return $this->_router;
  }

  /**
   * Метод, возвращающий объект класса Uwin\Controller\Request
   *
   * @return Request
   */
  public function getRequest()
  {
    if (null === $this->_request) {
      $this->_request = Request::getInstance();
    }

    return $this->_request;
  }

  /**
   * Метод, который создает объект класса нужного контроллера и возвращает его,
   * а также, в своих аргументах возвращает имя модуля, имя класса контроллера
   * и имя действия класса
   *
   * @param string &$module Имя модуля
   * @param string &$controller Имя контроллера
   * @param string &$action Имя действия
     *
     * @return Action
     * @throws RouteException Ошибка маршрутизации
     */
  public function createController(&$module = null, &$controller = null, &$action = null)
  {
    $file = $this->_getControllerFile($module, $controller, $action);

    // Если нужный контроллер не найден
    if ( !file_exists($file) ) {
      throw new RouteException('Router error: file controller "' . $file . '" is not found', 703);
    }

    include_once $file;

    $controller = new $this->_controllerClass($this->_view);

    return $controller;
  }

    /**
     * Метод выполняет действие указанного контроллера
     *
     * @param Action $controller Объект контроллера
     * @param array $params
     *
     * @throws RouteException
     * @return Front
     */
  public function runActionController(Action $controller, $params = array())
  {
    if ( !method_exists($controller, $this->_actionClass) ) {
      throw new RouteException('Router error: method "' . $this->_actionClass  . '" not fount in controller', 704);
    }

    call_user_func_array(array($controller, $this->_actionClass), $params);
//    $controller->{$this->_actionClass}();

    return $this;
  }

    /**
     * Метод изменяет текущий модуль на указанный, создает контроллер и
     * выполняет указанное действие контроллера. Тоесть, делает внутреннюю
     * переадресацию на другой модуль
     *
     * @param string $module Имя модуля
     * @param string $controller Имя контроллера
     * @param string $action Имя действия
     * @param array $params
     *
     * @return Action
     */
  public function forward($module, $controller, $action, $params = array())
  {
    if ( !is_array($params) ) {
      $params = array();
    }

    $this->_forwarded = true;
    // Устанавливаем переменные маршрутизации для запроса
    $this->getRequest()->setModuleName($module)
                        ->setControllerName($controller)
                        ->setActionName($action);

    // Создаем новый контроллер и выполняем указанное действие
    $controllerClass = $this->createController();
    $this->runActionController($controllerClass, $params);

    return $controllerClass;
  }

  /**
   * Метод рендерит переданный контроллер
   *
   * @param Action $controller Контроллер, который нужно визуализировать
     *
   * @return bool
   */
  public static function render($controller)
  {
    $layoutFile = Layout::getInstance()->getLayoutFile();

    $htmlPage = $controller->getView()->render($layoutFile);

        self::getInstance()->getRouter()->cachePage($htmlPage);

        echo $htmlPage;

    return true;
  }

  /**
   * Метод класса, который запускает приложение и выводит отрендереный текст
   * в браузер
   *
   * @return bool
   */
  public static function run()
  {
    $front = self::getInstance();

    $front->_router->setRouteVariable($front->getRequest()->get('route'));

    if ( false === $front->_router->route() ) {
        return true;
    }

    $controller = $front->createController();

    $canonical = $front->getRequest()->getCurrentUrl(true, true);
    $page = $front->getRequest()->getParam('page');

    $router = [
      'base_canonical' => 'http://' . SERVER_NAME . $canonical . '/',
    ];
    if ( !empty($page) ) {
      $router['page'] = (int)$page;
    }

    $controller->getView()
      ->setGlobal('router', $router)
      ->setGlobal('route_withot_params', $canonical . '/');

    $front->runActionController($controller);

    $controller->getView()
      ->appendVariables($front->_pageFeatures);

    // var_dump($controller->getView()->getVariables());
    self::render($controller);

    return true;
  }

  /**
   * @return string
   */
  public function getMode()
  {
    return $this->_mode;
  }

  /**
   * @param string $mode
     *
     * @return Front
     */
  public function setMode($mode)
  {
    $this->_mode = $mode;

    return $this;
  }
}
