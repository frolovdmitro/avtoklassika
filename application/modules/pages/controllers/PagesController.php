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
use \Uwin\Controller\Action;
use \Uwin\Layout;

/**
 * Контроллер статических страниц
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class PagesController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var Index
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
    $this->_model = $this->createModel('pages');
  }

  /**
   * Метод контроллера для статической страницы
   *
   * @return bool
   */
  public function indexAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getIndex()->getVariables() );

    return true;
  }
}
