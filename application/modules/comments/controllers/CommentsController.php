<?php
/**
 * UwinCMS
 *
 * Файл содержащий контроллер главной страницы, страниц ошибок и тизера
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Action,
    \Uwin\Layout,
    \Uwin\Exception\Route   as RouteException;

/**
 * Контроллер главной страницы, страниц ошибок и тизера
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class CommentsController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var Comments
   */
  private $_model = null;

  public function indexAction() {
    return $this;
  }

  /**
   * Метод, который выполняется перед запуском любого действия данного
   * контроллера
   *
   * @return void
   */
  public function init() {
    // В основном, страницы данного контроллера содержат не стандартный
    // макет, который мы определяем
    Layout::getInstance()->unsetLayout();

    // Создание модели данного модуля
    $this->_model = $this->createModel('Comments');
  }

  public function addUserCommentAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->addUserComment())
      ->printVariable('result', true);
  }
}
