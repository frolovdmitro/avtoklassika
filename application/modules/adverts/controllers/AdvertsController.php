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
    \Uwin\Auth,
    \Uwin\Exception\Route as RouteException,
     Uwin\Controller\Request as Request;

/**
 *
 *
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2012-2012 UwinArt Studio (http://uwinart.com)
 */
class AdvertsController extends Action
{
  /**
   * Объект класса модели данного модуля
   * @var Adverts
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
    $this->_model = $this->createModel('adverts');
  }

  /**
   * Метод контроллера для страницы
   *
   * @return bool
   */
  public function indexAction() {
    $this->getView()
      // ->setGlobals($this->_model->getVariables())
      // ->setVariables( $this->_model->getIndex() );
      ->appendVariables($this->_model->getVars());

    return true;
  }

  public function listAction() {
    $requestparams = $this->_model->getPage();

    if (!empty($requestparams["page"])) {
      $pagestart = $requestparams["page"];
      $this->pagination($pagestart);
    } else {
      $pagestart = 1;
      $this->pagination($pagestart);
    }

    $this->getView()
      ->setGlobals($this->_model->getVariables());

    return true;
  }


  public function pageAjaxAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
        ->useTemplater(false)
        ->setVariable('result', $this->_model->getPageAjax())
        ->printVariable('result', true);
  }

  public function modalPaymentsAction()
  {
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->setVariables($this->_model->getAddForm())
      ->setGlobals($this->_model->getVariables());
  }

  public function addFormAction()
  {
    Layout::getInstance()->unsetLayout();
    $auth = Auth::getInstance();

    if (!$auth->hasIdentity()) {
      $this->getView()
        ->useTemplater(false)
        ->setVariable('result', array("need_register" => 1 ))
        ->printVariable('result', true);
    } else {
      $this->getView()
        ->setVariables($this->_model->getAddForm())
        ->setGlobals($this->_model->getVariables())
        ->appendVariables($this->_model->needPay());
    }

  }

  public function checkStatusAction() {
    Layout::getInstance()->unsetLayout();

    $status = $this->_model->getStatus();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', array("need_pay_anyway" => $status))
      ->printVariable('result', true);
  }

  public function rmPhotoAction() {
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->rmImage())
      ->printVariable('result', true);
  }

  public  function addadvertAction() {
    /* return $this->getRequest()->getMethod();
     if ( !$this->getRequest()->isPost() ) {
          throw new RouteException;
    }*/

    Layout::getInstance()->unsetLayout();

    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->addPremiumAdvert())
      ->printVariable('result', true);
  }

  public function addAction() {
    if ( !$this->getRequest()->isPost() ) {
      throw new RouteException;
    }

    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->addAdvert())
      ->printVariable('result', true);
  }

  public function uploadFilesAction() {
    Layout::getInstance()->unsetLayout();
    $this->getView()
      ->useTemplater(false)
      ->setVariable('result', $this->_model->uploadFiles())
      ->printVariable('result', true);
  }

  public function pagination ($pagestart)
  {
    $pagest = $this->_model->getPageCount();
    $count_pages = $pagest['pages'];
    $active = $pagestart;
    $pagestoshow = $count_show_pages = 7;

    if ($count_pages <= 7) {
      $pagestoshow = $count_pages;
      $start = 1;
      $end = $count_pages;
    }
    if ($count_pages > 7 ) {
      $pagestoshow = 7;
      $start = $active - 3;
      if ($active < 4) {   $start = 1;}
      if ($active < $count_pages) {
        $diff = $count_pages - $active;
        if ($diff > 3) {
          $end = $active + 3;
        } else {
          $end = $active + $diff;
          $start = $active -6 + $diff ;
        }
      } else if ($active == $count_pages){
        $active = $count_pages;
        $end = $active;
        $start = $active -6;
      } else {
        $active = $count_pages;
        $end = $active;
        $start = $count_pages -6;
      }
    }

    $result = '';
    $pageStart = '<li class="paging__item paging__item_type_current" data-page="'. $start .'"><a class="paging__link" href="/ads/'. $start . '/#page=' .$start.'">'. $start . '</a></li>';
    for ($i = $start + 1; $i < ($pagestoshow + $start); $i++) {
      $page = '<li class="paging__item" data-page="'.$i.'"><a class="paging__link" href="/ads/'. $i . '/#page=' .$i.'">'. $i . '</a></li>';
      $result .= $page ;
    }
    $result .= '<li class="paging__item paging__item_type_next"><a class="paging__link paging__link_type_next" href="/ads/'. $i . '/#page=' .$i.'">Следующая</a></li>';
    $result = $pageStart .  $result;

    $content =  $this->getPageContent();
    $this->getView()
      ->setGlobals($this->_model->getVariables())
      ->setVariable('advertsList', $content)
      ->setVariable('pagestoshow', $result);

    return true;
  }

  public function getPageContent()
  {
    $t =  $this->_model->getPageAdverts();

    return $t['html'];

  }
}
