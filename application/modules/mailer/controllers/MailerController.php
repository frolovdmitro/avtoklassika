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
use \Uwin\Layout;
use \Uwin\Exception\Route   as RouteException;

/**
 * Контроллер главной страницы, страниц ошибок и тизера
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class MailerController extends Action
{
    /**
   	 * Объект класса модели данного модуля
   	 * @var Mailer
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
   		$this->_model = $this->createModel('mailer');
   	}

	public function indexAction() {
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

        return true;
     }

    public function unsubscribeAction() {
        if ( !$this->getRequest()->isPost() ) {
            throw new RouteException;
        }

        Layout::getInstance()->unsetLayout();

        $this->getView()
             ->useTemplater(false)
             ->setVariable('result', $this->_model->unsubscribe())
             ->printVariable('result', true);

        return true;
    }

    public function mailOpenStatAction() {
   		header('Content-type: image/jpeg');

   		$this->getView()
   			 ->setVariables( $this->_model->mailOpenStat(true)->getVariables() )
   			 ->useTemplater(false);
   	}
}