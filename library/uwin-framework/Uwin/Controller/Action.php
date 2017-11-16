<?php
/**
 * Uwin Framework
 *
 * Файл содержащий абстрактный класс Uwin\Controller\Action, который является
 * общим для всех классов контроллеров в модулях
 *
 * @category   Uwin
 * @package    Uwin\Controller
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Controller, к которому относится класс Action
 */
namespace Uwin\Controller;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\View                 as View;
use \ReflectionClass           as ReflectionClass;
use \Uwin\Controller\Exception as ControllerExeception;

/**
 * Класс Uwin\Controller\Action, который является общим для всех классов
 * контроллеров в модулях
 *
 * @category  Uwin
 * @package   Uwin\Controller
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
abstract class Action
{
  private $_model = null;

  /**
   * Объект вида
   * @var Uwin\View
   */
  private $_view = null;

  /**
   * Действие контроллера, выполняемое по умолчанию
   *
   * @return bool
   */
  abstract public function indexAction();

  /**
   * Функция, которая возвращает полный путь к фалу вызванного контроллера
   *
   * @return string
   */
    private function _getPathByFileController()
    {
        $class = new ReflectionClass(get_class($this));

        return $class->getFileName();
    }

    /**
     * Функция, которая возвращает директорию с моделями для данного модуля
     *
     * @return string
     */
    private function _getDirModelsByController()
    {
      $dir = dirname( dirname( $this->_getPathByFileController() ) ) .
          DIR_SEP . 'models' . DIR_SEP;

      return $dir;
    }

    /**
     * Конструктор класса, выполняющий его инициализацию сразу после создания
     * объекта этого класса
     *
     * @param View $view Ссылка на объект класса вида
     *
     * @return Action
     */
    public function __construct(View $view)
    {
      $this->setView($view);

      $this->init();
    }

    /**
     * Метод который выполняется перед созданием объекта класса. Нужен для того
     * чтобы в классах контроллеров в этом методе можно было бы выполнять
     * какие-то общие действия
     *
     * @return void
     */
    public function init() {}

    /**
     * Метод, с помощью которого создаются все модели с которыми должен работать
     * контроллер
     *
     * @param string $name Имя класса модели
     * @param array $params Массив переменных, передаваемых в конструктор модели
   *
   * @return \Uwin\Model\Abstract_
   * @throws \Uwin\Controller\Exception Ошибка контроллера
   */
  public function createModel($name, $params = array())
  {
    $nameModelClass = ucfirst($name);
    $fileModel = $this->_getDirModelsByController() . $nameModelClass . '.php';

    if ( !file_exists($fileModel) ) {
      throw new ControllerExeception('Controller error: model "' . $fileModel . '" not fount', 1001);
    }

    require_once ($fileModel);

    $this->_model = new $nameModelClass();

    return $this->_model;
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function getModel() {
    return $this->_model;
  }

  /**
   * Метод, устанавливающий объект вида для контроллера
   *
   * @return \Uwin\View
   */
  public function getView()
  {
    return $this->_view;
  }

  /**
   * Метод, который устанавливает объект вида для контроллера
   *
   * @param Uwin\View $view Объект вида
   * @return Uwin\Controller\Action
   */
  public function setView(View $view)
  {
    $this->_view = $view;

    return $this;
  }

  /**
   * Метод возвращает ссылку на объект класса запроса
   *
   * @return Request
   */
  public function getRequest()
  {
    return Request::getInstance();
  }

  /**
   * Метод выполняет редирект страницы на указанный адресс
   *
   * @param string $url Адрес, куда выполняется переадресация
   * @return bool
   */
  public function redirect($url)
  {
    $this->getRequest()->redirect($url);

    return true;
  }

  /**
   * Метод изменяет текущий модуль на указанный, создает контроллер и
   * выполняет указанное действие контроллера. Тоесть, делает внутреннюю
   * переадресацию на другой модуль
   *
   * @param string $module Имя модуля
   * @param string $controller Имя контроллера
   * @param string $action Имя действия
   * @return Uwin\Controller\Action
   */
  public function forward($module, $controller, $action)
  {
    $controllerClass = Front::getInstance()->forward($module, $controller, $action);

    return $controllerClass;
  }
}
