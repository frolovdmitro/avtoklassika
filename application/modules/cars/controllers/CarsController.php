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
use \Uwin\Controller\Action;
use \Uwin\Layout,
    \Uwin\Auth;
use \Uwin\Exception\Route   as RouteException;

/**
 * Контроллер главной страницы, страниц ошибок и тизера
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class CarsController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var Cars
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
    $this->_model = $this->createModel('Cars');
  }

  public function indexAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getIndex()->getVariables() );

    return true;
  }

  public function docsListAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getDocsList()->getVariables() );

    return true;
  }

  public function docAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getDoc()->getVariables() );

    return true;
  }

  public function priceAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getPrice()->getVariables() );

    return true;
  }

  public function pricePdfAction() {
    Layout::getInstance()->unsetLayout();

    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->useTemplater(false)
      ->appendVariables( $this->_model->getPricePdf() );

    return true;
  }

  public function allDetailsAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getAllDetails()->getVariables() )
      ->setVariable('robots', 'noindexnofollow');

    return true;
  }

  public function newDetails2Action() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getNewDetails2()->getVariables() )
      ->setVariable('robots', 'noindexnofollow');

    return true;
  }

  public function searchPageAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getSearchPage()->getVariables() )
      ->setVariable('robots', 'noindexnofollow');

    return true;
  }

  public function detailsAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getDetails()->getVariables() );

    return true;
  }

  public function detailAction() {
    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->appendVariables( $this->_model->getDetail()->getVariables() );

    return true;
  }

  public function pageAjaxAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
        ->useTemplater(false)
        ->setVariable('result', $this->_model->getPageAjax())
        ->printVariable('result', true);
  }

  public function treeAutopartsAction() {
    Layout::getInstance()->unsetLayout();

    $car_id = $this->getRequest()->getParam('car_id');

    $this->getView()
      ->setVariables($this->_model->getTreeAutoparts($car_id));
  }

  public function colorSizeInfoAction() {
    Layout::getInstance()->unsetLayout();

    $detail_id = $this->getRequest()->getParam('id');

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->getColorSizeInfo($detail_id))
      ->printVariable('result', true);
  }

  public function searchDetailAction() {
    Layout::getInstance()->unsetLayout();

    $query = $this->getRequest()->getParam('query');

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->getSearchDetail($query))
      ->printVariable('result', true);
  }

  public function searchPresenceDetailAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->getSearchPresenceDetail())
      ->printVariable('result', true);
  }

  public function mailOpenStatAction() {
    header('Content-type: image/jpeg');

    $this->getView()
      ->setVariables( $this->_model->mailOpenStat(true)->getVariables() )
      ->useTemplater(false);
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function requestAutopartAction() {
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->setVariables( $this->_model->getRequestForm() )
      ->setGlobals($this->_model->getVariables());
  }

  public function addRequestAutopartAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->addRequestAutopart())
      ->printVariable('result', true);
  }

  public function autobazarExcelAction()
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

    $this->_model->autobazarExcel();

    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()
      ->useTemplater(false);
  }

  public function uploadRequestImageAction() {
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->uploadRequestImage())
      ->printVariable('result', true);
  }
}
