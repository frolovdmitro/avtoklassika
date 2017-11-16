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
class OrdersController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var Orders
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
    $this->_model = $this->createModel('orders');
  }

  public function indexAction() {
    Layout::getInstance()->setLayoutName('index');

    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables( $this->_model->getIndex()->getVariables() );

    return true;
  }

  public function orderInfoAction() {
    Layout::getInstance()->setLayoutName('index');

    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables( $this->_model->getOrderInfo() );

    return $this;
  }

  public function orderReportAction()
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

    $this->_model->getOrderReport();

    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->useTemplater(false);
  }

  public function quickBuyAction() {
    Layout::getInstance()->unsetLayout();

    $detail_id = $this->getRequest()->getParam('id');

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->quickBuy($detail_id))
      ->printVariable('result', true);
  }

  public function changeStatusPlatonOrderAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->changeStatusPlatonOrder());
  }

  public function changeBasketAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable( 'result', $this->_model->changeBasket() )
      ->printVariable('result', true);
  }

  public function getBasketAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable( 'result', $this->_model->getBasket() )
      ->printVariable('result', true);
  }

  public function deleteBasketItemAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable( 'result', $this->_model->deleteBasketItem() )
      ->printVariable('result', true);
  }

  public function promocodeAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable( 'result', $this->_model->promocode() )
      ->printVariable('result', true);
  }

  public function basketPageAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables( $this->_model->getBasketPage() );

    return true;
  }

  public function createOrderAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->createOrder())
      ->printVariable('result', true);
  }

  public function continueOrderAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->continueOrder())
      ->printVariable('result', true);
  }

  public function orderSetStatusAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()->useTemplater(false);
    $this->_model->equeringSetStatus();
  }

  public function saveOrderUserInfoAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->saveOrderUserInfo())
      ->printVariable('result', true);
  }

}
