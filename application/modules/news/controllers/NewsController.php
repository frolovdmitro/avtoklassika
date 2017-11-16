<?php
/**
 * UwinCMS
 *
 * Файл содержащий контроллер статических страниц
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Action,
    \Uwin\Layout;

/**
 * Контроллер статических страниц
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class NewsController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var News
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
    Layout::getInstance()->setLayoutName('index');

    // Создание модели данного модуля
    $this->_model = $this->createModel('news');
  }

  /**
   * Метод контроллера для страницы новости
   *
   * @return bool
   */
  public function indexAction() {
    $this->getView()
      ->setVariables( $this->_model->getIndex() )
      ->setGlobals($this->_model->getVariables());

    return true;
  }

  public function listAction() {
    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariables($this->_model->getList());

    return true;
  }


  public function pageAjaxAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
        ->useTemplater(false)
        ->setVariable('result', $this->_model->getPageAjax())
        ->printVariable('result', true);
  }
}
