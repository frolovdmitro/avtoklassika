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
    \Uwin\Exception\Route as RouteException;

/**
 *
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 */
class CarMarketController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var CarMarket
   */
  private $_model = null;

  /**
   * Метод, который выполняется перед запуском любого действия данного
   * контроллера
   *
   * @return void
   */
  public function init() {
    // В основном, страницы данного контроллера содержат не стандартный
    // макет, который мы определяем
    Layout::getInstance()->setLayoutName('carMarket');

    // Создание модели данного модуля
    $this->_model = $this->createModel('carMarket');
  }

  /**
   * Метод контроллера для страницы
   *
   * @return bool
   */
  public function indexAction() {
    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables( $this->_model->getIndex() );

    return true;
  }

  public function listAction() {
    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables($this->_model->getList());

    return true;
  }
}
