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
use \Uwin\Registry;
use \Uwin\Auth;
use \Uwin\Exception\Route   as RouteException;

/**
 * Контроллер главной страницы, страниц ошибок и тизера
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class IndexController extends Action
{
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
  }

  /**
   * Метод контроллера для главной страницы
   *
   * @return bool
   */
  public function indexAction()
  {
        /**
         * @var Index $model
         */
        $model = $this->createModel('Index');

    // Передача в шаблонизатор переменных полученных в модели
    $this->getView()->appendVariables( $model->getIndex()->getVariables() );

    return true;
  }

  /**
   * Метод контроллера для страницы 404 ошибки (страница не найдена)
   *
   * @return bool
   */
  public function notFoundAction() {
    /**
      * @var Errors $model
      */
    $model = $this->createModel('errors');

    $this->getRequest()->sendHeaderNotFound();
    $this->getRequest()->redirect('/');

    // // Передаем в вид все полученные в модели переменные и итерации
    // // контекстов
    // $this->getView()->appendVariables( $model->getNotFound()
    //         ->getVariables() );

    return true;
  }

  /**
   * Метод контроллера для страницы "ошибка на странице"
   *
   * @param Exception $exception - выброшенное исключение
   *
   * @return bool
   */
  public function errorAction($exception) {
    // Устанавливаем переменную пути к файлам стилей, javascript и изображениям
    $this->getView()
      ->useTemplater(true)
      ->setVariable('backend', 'backend/');

        /**
         * @var Errors $model
         */
        $model = $this->createModel('errors');

    // Устанавливаем заголовок с кодом ошибки 503(страница на ремонте)
    $this->getRequest()->sendHeaderUnavailable(3600);

    if ( Auth::getInstance()->setStorageNamespace('UwinAuthAdmin')
                ->hasIdentity() ) {
      Layout::getInstance()->setLayoutName('devErrorLayout');

            $model->getDevError($exception);
    } else {
            $model->getError($exception);
    }

    // Передаем в вид все полученные в модели переменные и итерации
    // контекстов
    $this->getView()->appendVariables( $model->getVariables() );

    return true;
  }

  /**
   * Метод контроллера для страницы, сайт на техническом ремонте
   *
   * @return bool
   */
  public function maintenanceAction() {
        /**
         * @var Errors $model
         */
        $model = $this->createModel('errors');

    // Устанавливаем заголовок с кодом ошибки 503(страница на ремонте)
    $settings = Registry::get('stg');
    $seconds = null;
    if ( array_key_exists('retry_after', (array)$settings['maintenance']) ) {
      $seconds = $settings['maintenance']['retry_after'];
    }
    $this->getRequest()->sendHeaderUnavailable($seconds);

    // Передаем в вид все полученные в модели переменные и итерации
    // контекстов
    $this->getView()->appendVariables( $model->getMaintenance()
            ->getVariables() );

    return true;
  }

  /**
   * Метод контроллера для страницы тизера
   *
   * @throws \Uwin\Exception\Route
   * @return bool
   */
  public function teaserAction() {
    /**
      * @var Index $model
      */
    $model = $this->createModel('Index');

    $request = $this->getRequest();
    $module = $request->getParam('module');
    $controller = $request->getParam('controller');
    $action = $request->getParam('action');

    if (!('default' == $module &&
        'index'   == $controller &&
        'index'   == $action
    )) {
      throw new RouteException;
    }

    // Передаем в вид все полученные в модели переменные и итерации
    // контекстов
    $this->getView()
      ->setTemplate($module, $controller, 'teaser')
      ->appendVariables( $model->getTeaser()->getVariables() );

    return true;
  }

  /**
   * Метод возвращает JSON массив текстов ошибок указанной формы
   *
   * @return bool
   */
  public function errorsTextsAction() {
    $model = $this->createModel('errors');

    $this->getView()
       ->useTemplater(false)
       ->appendVariables( $model->getErrorsTexts()->getVariables() )
       ->printVariable('result', true);

    return true;
  }

  public function robotsTxtAction() {
    Layout::getInstance()->unsetLayout();

    return true;
  }

  public function sitemapXmlAction() {
    $model = $this->createModel('index');

    Layout::getInstance()->unsetLayout();

    $this->getView()->useTemplater(false);
    $model->getSitemap();

    return true;
  }

  public function uploadFilesAction() {
    Layout::getInstance()->unsetLayout();

    $this->getView()
       ->useTemplater(false);
    $upload_dir = '/tmp';
    $options = array('upload_dir' => $upload_dir);
    $upload_handler = new UploadHandler($options);

    // var_dump($_FILES);

    return true;
  }
}
