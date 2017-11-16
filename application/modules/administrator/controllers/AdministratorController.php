<?php
/**
 * UwinCMS
 *
 * Файл содержащий контроллер панели управления сайтом

 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Action,
    \Uwin\Auth,
    \Uwin\Layout,
    \Uwin\Registry,
    \Uwin\Db,
    \Uwin\Exception\Route   as RouteException;


/**
 * Контроллер панели упарвления сайтом
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class AdministratorController extends Action
{
  /**
   * Ссылка на экземпляр класса авторизации
   * @var \Uwin\Auth
   */
  private $_auth = null;


  /**
   * Метод, который вызывается сразу после создания объекта данного класа
   *
   * @return void
   */
  public function init() {
    Layout::getInstance()->setLayoutName('administratorLayout');

    // Устанавливаем переменную пути к файлам стилей, javascript и изображениям
    $this->getView()->setVariable('backend', 'backend/');

    // Устанавливаем пространство имен в сессии, где будут хранится данные
    // о авторизации
    $this->_auth = Auth::getInstance()
       ->setStorageNamespace('UwinAuthAdmin');

    // Получение коннектора к базе данных
    $dbParams = Registry::getInstance()->get('stg');
    $dbParams = $dbParams['databases']['administrator'];
    Db::db()
      ->disconnect()
      ->setDbParams($dbParams);
  }

  /**
   * Метод главной страницы панели управления
   *
   * @return void
   * @throws \Uwin\Exception\Route
   */
  public function indexAction() {
    // Если пользователь не идентифицирован, выбрасываем исключении роутера
    // (404 ошибка)
    if ( !$this->_auth->hasIdentity() ) {
      throw new RouteException;
    }
        $this->_auth->getStorage()->getParams();

    /**
     * @var Administrator $model
     */
    $model = $this
      ->createModel('administrator')
      ->setContext('adminModule');

    $this->getView()->appendVariables( $model->getVariables() );
  }

  /**
   * Метод возвращает контентную часть страницы панели управления
   *
   * @return void
   * @throws Uwin\Exception\Route
   */
  public function contentAction() {
    // Если пользователь не идентифицирован, выбрасываем исключении роутера
    // (404 ошибка)
    if ( !$this->_auth->hasIdentity() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();

    /** @noinspection PhpUndefinedMethodInspection */
    $varsModel = $this->createModel('administrator')
              ->getContent()
              ->getVariables();

    $this->getView()->appendVariables($varsModel);
  }

  /**
   * Метод возвращает контент указанной вкладки модуля
   *
   * @return void
   * @throws Uwin\Exception\Route
   */
  public function subpageAction() {
    // Если пользователь не идентифицирован, выбрасываем исключении роутера
    // (404 ошибка)
    if ( !$this->_auth->hasIdentity() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();

    /** @noinspection PhpUndefinedMethodInspection */
    $varsModel = $this->createModel('administrator')
              ->getSubPage()
              ->getVariables();

    $this->getView()->appendVariables($varsModel);
  }

  /**
   * Метод возвращает массив данных в формате json указанной таблицы с
   * указанными параметрами в массиве $_GET
   *
   * @return void
   * @throws Uwin\Exception\Route
   */
  public function tableDataAction() {
    // Если пользователь не идентифицирован, выбрасываем исключении роутера
    // (404 ошибка)
    if ( !$this->_auth->hasIdentity() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();

    /** @noinspection PhpUndefinedMethodInspection */
    $model = $this->createModel('administrator')
                  ->getTableRows();

    $this->getView()
       ->useTemplater(false)
       ->appendVariables( $model->getVariables() )
       ->printVariable('result', true);
  }

    /**
     * Метод возвращает массив данных в формате json указанной таблицы с
     * указанными параметрами в массиве $_GET
     *
     * @return void
     * @throws Uwin\Exception\Route
     */
    public function moveRowAction() {
      // Если пользователь не идентифицирован, выбрасываем исключении роутера
      // (404 ошибка)
      if ( !$this->_auth->hasIdentity() ) {
        throw new RouteException;
      }

        if ( !$this->getRequest()->isPost() ) {
            throw new RouteException;
        }

      Layout::getInstance()->unsetLayout();

      /** @noinspection PhpUndefinedMethodInspection */
      $model = $this->createModel('administrator')
                    ->moveRow();

      $this->getView()
         ->useTemplater(false)
         ->appendVariables( $model->getVariables() )
         ->printVariable('result', true);
    }

  /**
   * Метод возвращает модальную форму
   *
   * @return void
   * @throws Uwin\Exception\Route
   */
  public function modalFormAction() {
    // Если пользователь не идентифицирован, выбрасываем исключении роутера
    // (404 ошибка)
    if ( !$this->_auth->hasIdentity() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    /** @noinspection PhpUndefinedMethodInspection */
    $model = $this->createModel('administrator')
                  ->getModalForm();

    $this->getView()->appendVariables( $model->getVariables() );
  }

  public function operationAction() {
    // Если пользователь не идентифицирован, выбрасываем исключении роутера
    // (404 ошибка)
    if ( !$this->_auth->hasIdentity() ) {
      throw new RouteException;
    }

    // Если доступ к действию идет не методом POST, выбрасываем исключение
    // маршрутизации
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    /** @noinspection PhpUndefinedMethodInspection */
    $model = $this->createModel('administrator')
                  ->operation();

    $this->getView()
       ->setVariables( $model->getVariables() )
       ->useTemplater(false)
       ->printVariable('result', true);
  }

  /**
   * Метод страницы логина в панель управления
   *
   * @return void
   */
  public function loginAction() {
    //  Проверяет, авторизован пользователь или нет, если авторизован,
    // перенаправляет его в панель управления
    if ( $this->_auth->hasIdentity() ) {
      $this->redirect('/administrator/');
    }
    /**
     * Создание класса модели
     * @var \Administrator $model
     */
    $model = $this
      ->createModel('administrator')
      ->setContext('loginModule');

    $this->getView()->appendVariables( $model->getVariables() );
  }

  /**
   * Метод выполняющий аутентификацию пользователя в панель управления сайтом
   *
   * @return void
   * @throws \Uwin\Exception\Route
   */
  public function authenticateAction() {
    // Если доступ к действию идет не методом POST, выбрасываем исключение
    // маршрутизации
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    // Данная страница не использует макет
    Layout::getInstance()->unsetLayout();

    /**
     * Создание класса модели
     * @var Administrator $model
     */
    $model = $this->createModel('administrator');

    // Выполняем метод модели, который выполняет авторизацию
    $model->getLogin();

    $this->getView()
       ->setVariables( $model->getVariables() )
       ->useTemplater(false)
       ->printVariable('result', true);
  }

  /**
   * Метод, выполняющий выход с панели управления сайтом
   * @return void
   */
  public function logoutAction() {
        // Очистка всех данных в сессии относительно авторизации
    $this->_auth->clearIdentity();

        // Перенаправление на страницу аторизации панели управления
        $this->redirect('/administrator/login/');
  }

  public function typografyAction() {
    // Если пользователь не идентифицирован, выбрасываем исключении роутера
    // (404 ошибка)
    if ( !$this->_auth->hasIdentity() ) {
      throw new RouteException;
    }

    // Если доступ к действию идет не методом POST, выбрасываем исключение
    // маршрутизации
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    /** @noinspection PhpUndefinedMethodInspection */
    $model = $this->createModel('administrator')
                  ->typografy();

    $this->getView()
       ->setVariables( $model->getVariables() )
       ->useTemplater(false)
       ->printVariable('result', true);
  }

    public function loginRedmineAction() {
      // Если пользователь не идентифицирован, выбрасываем исключении роутера
      // (404 ошибка)
      if ( !$this->_auth->hasIdentity() ) {
        throw new RouteException;
      }

    // Если доступ к действию идет не методом POST, выбрасываем исключение
    // маршрутизации
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

      Layout::getInstance()->unsetLayout();
      /** @noinspection PhpUndefinedMethodInspection */
      $model = $this->createModel('administrator')
                    ->loginRedmine();

      $this->getView()
         ->setVariables( $model->getVariables() )
         ->useTemplater(false)
         ->printVariable('result', true);
    }
}
