<?php
/**
 * UwinCMS
 *
 * Файл содержащий
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Action,
    \Uwin\Layout,
    \Uwin\Auth;

/**
 *
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 */
class UsersController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var Users
   */
  private $_model = null;

  /**
   * Метод, который выполняется перед запуском любого действия данного
   * контроллера
   *
   * @return void
   */
  public function init() {
    // Создание модели данного модуля
    $this->_model = $this->createModel('users');
  }

  public function indexAction() {
    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables( $this->_model->getIndex()->getVariables() );

    return true;
  }

  public function subscribeAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->subscribe())
      ->printVariable('result', true);
  }

  public function unsubscribeAction() {
    // if ( !$this->getRequest()->isPost() ) {
    //   throw new RouteException;
    // }

    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->unsubscribe())
      ->printVariable('result', true);
  }

  public function addReviewFormAction() {
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->setGlobals($this->_model->getVariables());
  }

  public function registerFormAction() {
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->setGlobals($this->_model->getVariables());
  }

  public function addReviewAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->addReview())
      ->printVariable('result', true);
  }

  public function setCurrencyAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false);

    $this->_model->setCurrency();
  }

  public function authAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();

    $auth = Auth::getInstance();
    if ( $auth->hasIdentity() ) {
      return true;
    }

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->auth())
      ->printVariable('result', true);
  }

  public function repairPasswordAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->repairPassword())
      ->printVariable('result', true);
  }

  public function logoutAction() {
    Auth::getInstance()->clearIdentity();

    // Переход на главную
    $this->getRequest()->redirect('/');
  }

  public function registerAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->register())
      ->printVariable('result', true);
  }

  public function cabinetAction() {

    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      throw new RouteException;
    }

    Layout::getInstance()->setLayoutName('index');

    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables($this->_model->getCabinet());

    return true;
  }

  public function editAdvertsAction() {
    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      throw new RouteException;
    }

    Layout::getInstance()->setLayoutName('index');

    $this->getView()
      ->setVariables($this->_model->getCurrency())
      ->setGlobals($this->_model->getVariables());

    return true;
  }

  public function saveDataCabinetAction() {
    $auth = Auth::getInstance();
    if ( !$auth->hasIdentity() ) {
      throw new RouteException;
    }

    // if ( !$this->getRequest()->isPost() ) {
    //   throw new RouteException;
    // }
    //
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->saveDataCabinet())
      ->printVariable('result', true);
  }

  public function oAuthenticateAction() {
    Layout::getInstance()->setLayoutName('index');

    $this->getView()
      ->setVariable('module', 'oAuth')
      ->setVariable('result', $this->_model->oAuthenticate());
  }

  public function newUsersExcelAction()
  {
    // Получаем объект Uwin\Auth
    $auth = Auth::getInstance()
      ->setStorageNamespace('UwinAuthAdmin');

    // Если пользователь не идентифицирован
    if ( !$auth->hasIdentity() ) {
      throw new RouteException;
    }

    // Данная форма не использует макет
    Layout::getInstance()->unsetLayout();

    $request = $this->getRequest();
    $from = $request->get('from');
    $to = $request->get('to');

    $this->_model->newUsersExcel($from, $to);

    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->useTemplater(false);
  }
}
