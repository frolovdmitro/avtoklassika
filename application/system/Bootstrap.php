<?php
/**
 * UwinCMS
 *
 * Файл содержащий главный системный класс, выполняющий настройку и запуск
 * приложения

 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2013 Uwinart Development (http://uwinart.com)
 * @version   $Id$
 */

/**
 * @see Uwin\Autoloader
 */
/** @noinspection PhpIncludeInspection */
require 'Uwin' . DIR_SEP . 'Autoloader.php';

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Autoloader,
    \Uwin\Config,
    \Uwin\Registry,
    \Uwin\Db,
    \Uwin\Layout,
    \Uwin\View,
    \Uwin\Auth,
    \Uwin\Controller\Router,
    \Uwin\Controller\Front,
    \Uwin\Controller\Request;

/**
 * Главный системный класс, выполняющий настройку и запуск приложения
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Bootstrap
{
  /**
   * Имя класса кешировщика
   * @var \Uwin\Cacher\Interface_
   */
  private $_cacherClassName = null;


  private $_utils = null;

  /**
   * Метод возвращает путь к дирекории с указанными ресурсами
   *
   * @param bool   $minify   - Минификация включена или нет
   * @param array  $settings - Массив настроек приложения
   * @param string $path     - Имя переменной с неминифицированным путем
   * @param string $pathMin  - Имя переменной с минифицированным путем
   *
   * @return string
   */
  private function _getPathToResource($minify, $settings, $path, $pathMin) { // {{{
    $result = null;
    if ($minify) {
      $path = $pathMin;
    }

    if ( isset($settings[$path]) ) {
      $result = $settings[$path];
    }

    return $result;
  } // }}}


  /**
   * Метод возвращает массив имен файлов указанного типа
   *
   * @param bool   $minify   - Минификация включена или нет
   * @param string $type     - Тип файлов
   * @param array  $settings - Переменные
   *
   * @return array
   */
  private function _getFileNameResource($minify, $type, $settings) { // {{{
    $result = null;

    if ( isset($settings[$type]) ) {
      foreach ($settings[$type] as $name => $values) {
        if ( $minify ){
          $filename = $settings[$type][$name]['minify_name'];
        } else {
          $filename = $settings[$type][$name]['name'];
        }

        $result[$name] = $filename;
      }
    }

    return $result;
  } // }}}


  /**
   * Метод возвращает имя минифицированного файла указаного типа
   *
   * @param string $type    - Тип файла(css|js)
   * @param array  $config  - Массив с данными конфигурации
   * @param bool   $backend - Минифицированная версия панели управления или сайта
   *
   * @return string
   */
  private function _getMinifiedFilename($type, $config, $backend = false) { // {{{
    $backend_dirname = '';
    if ($backend) {
      $backend_dirname = 'backend/';
    }

    $path = $config['path']['static_server'] . $type . '/' . $backend_dirname;
    $files = glob($path . '*.' . $type);
    array_multisort(array_map('filemtime', $files), SORT_NUMERIC, SORT_DESC,
      $files);

    if ( empty($files) ) {
      return null;
    }

    return basename($files[0]);
  } // }}}


  /**
   * Метод схраняет в сессию информацию о том нужно выводить прфилирование на
   * странице или нет
   *
   * @return Bootstrap
   */
  private function _setProfilePrint() { // {{{
    // Включаем/выключаем вывод профайлера
    $auth = Auth::getInstance()->setStorageNamespace('UwinAuthAdmin');
    if ( $auth->hasIdentity() )
    {
      if ( 1 == (int)Request::getInstance()->get('profile') ) {
        $auth->getStorage()->printProfile = true;
      }
      if ( '0' === Request::getInstance()->get('profile') ) {
        $auth->getStorage()->printProfile = null;
      }
    }

    $auth->setStorageNamespace(Auth::NAMESPACE_DEFAULT);

    return $this;
  } // }}}


  /**
   * Метод проверяет есть ли редиректы и делает их при необходимости
   *
   * @return Bootstrap
   */
  private function _redirects() { // {{{
    $registry = Registry::getInstance();
    $request  = Request::getInstance();

    $redirects = $registry->stg['seo']['redirects'];
    if ( empty($redirects) ) {
      return $this;
    }

    $redirects = explode("\n", $redirects);
    $url = $request->getCurrentUrlWithGets();
    // if (false !== strpos($url, '?')) {
    //   $url = substr($url, 0, strpos($url, '?'));
    // }
    $url = htmlspecialchars_decode(rtrim($url, '/'));
    foreach ($redirects as $redirect){
      $data = preg_split("/[\s]+/", $redirect);

      // var_dump(htmlspecialchars_decode($url));
      // var_dump(htmlspecialchars_decode($data[0]));

      if ( rtrim(htmlspecialchars_decode($data[0]), '/') == $url) {
        $request->redirect($data[1]);
      }
    }

    return $this;
  } // }}}


  /**
   * Запуск приложения
   *
   * @param array $config - Массив с данными конфигурации
   *
   * @return bool
   */
  public function run($config) { // {{{
    try {
      $this
        // Включение автозагрузки классов Uwin Framework
        ->setLoader()
        // Настройки кешировщика
        ->setCacher($config)
        // Установка настроек
        ->setSettings($config)
        // Делаем редиректы если надо
        ->_redirects()
        // Настройка базы данных
        ->setDb()
        // Установка языковых переменных
        ->setLanguages()
        ->setCurrency()
        ->setSession($config);

      $registry = Registry::getInstance();
      $request = Request::getInstance();

      // Подключение маршрутизацтора
      $router = $this->setRouter($config);

      // Настройка Вида
      $view = $this->setView($registry['path']['layout']);

      // Создание объекта класса фронт-контроллера, его инициализация
      $module = explode('/', trim($request->get('route'), '/'))[0];
      $mode = $registry['stg']['status']['mode'];
      if('administrator' === $module) {
        $mode = 'index';
      }

      $auth = Auth::getInstance()->setStorageNamespace('UwinAuthAdmin');
      if ( $auth->hasIdentity() )
      {
        if (!empty($request->get('var_export'))) {
          $registry['var_export'] = true;
        }
      }

      $front = Front::getInstance()
        ->setRouter($router)
        ->setView($view)
        ->setMode($mode);

      $this->setPageFeatures();

      if ( $request->serverName() != $request->getHost() &&
        ('www.' . $request->serverName()) != $request->getHost() ) {
          $current_lang = $request->subdomain();
          $isset_lang = false;

          foreach($registry['languages'] as $lang) {
            if ($lang == $current_lang) {
              $isset_lang = true;
              break;
            }
          }

          if (!$isset_lang) {
            $request->redirect( '//' . $request->withWww()
              . $request->serverName() );
          }
        }

      // Если указан субдомен такой же как и язык по-умолчанию,
      // редиректим на домен
      if ( $request->subdomain() == $registry->get('default_language') ) {
        $request->redirect('//' . $request->withWww() . SERVER_NAME);
      }
      if (!in_array($request->subdomain(), $registry['languages'])) {
        if ( !isset($_COOKIE['no_redirect_lang']) ) {
          $lang = 'ru';
          if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
            $lang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
          }
          if (in_array($lang, $registry['languages'])) {
            if ($lang != $registry->get('default_language')) {
              $inTwoMonths = 60 * 60 * 24 * 60 + time();
              setcookie('no_redirect_lang', true, $inTwoMonths, '/',
                COOKIE_HOST);
              $request->redirect('//' . $lang . '.' . SERVER_NAME);
            }
          }
        }
      }

      // Запуск приложения
      $front::run();

    } catch (Exception $e) {
      //Перехват исключений и обработка их
      Error::catchException($e);
    }

    return true;
  } // }}}


  public function runCli($config, $argv) { // {{{
    if ( !isset($argv[1]) ) {
      echo "Not specified module:action\n";

      exit();
    }

    $params = explode(':', $argv[1]);
    if ( !isset($params['1']) ) {
      echo "Not specified action\n";

      exit();
    }

    $this
      // Включение автозагрузки классов Uwin Framework
      ->setLoader()
      // Настройки кешировщика
      ->setCacher($config)
      // Установка настроек
      ->setSettings($config)
      // Настройка базы данных
      ->setDb();

    $dbParams = Registry::getInstance()->get('stg');
    $dbParams = $dbParams['databases']['administrator'];
    Db::db()->setDbParams($dbParams);

    $modelFile = Front::getInstance()
      ->getModelFile($params[0], $params[0]);

    $modelFile = dirname($modelFile) . DIR_SEP . basename($modelFile, '.php')
      . 'Scripts.php';

    include_once $modelFile;

    $modalName = ucfirst($params[0]) . 'Scripts';
    $actionName = $params[1];

    $model = new $modalName;
    $model->$actionName();

    return true;
  } // }}}


  /**
   * Включение автозагрузки классов Uwin Framework
   *
   * @return Bootstrap
   */
  public function setLoader() { // {{{
    Autoloader::register();

    $this->_utils = new Utils;

    return $this;
  } // }}}


  /**
   * Метод устанавливает сервера memcached
   *
   * @param array $config - Массив настроек
   *
   * @return Bootstrap
   */
  public function setCacher(array $config) { // {{{
    $configer = new Config();
    $settingFile = $config['path']['settings'] . 'general.xml';
    $cacherSettings = $configer->open($settingFile, '/root/cacher')->get();

    /**
     * Получаю имя класса, который используется для кеширования
     * @var \Uwin\Cacher\Interface_ $className
     */
    $className = $this->_cacherClassName = '\Uwin\Cacher\\'
      . $cacherSettings['type'];

    if ( false === class_exists($className, true) ) {
      return $this;
    }

    // Включаем/Отключаем использование Memcached
    $className::enabled($cacherSettings['enabled']);

    // Проверяем, существуют ли группы серверов, если нет, выходим
    // с функции
    if ( empty($cacherSettings['groups']) ) {
      return $this;
    }

    // Добавляем все группы серверов
    foreach ($cacherSettings['groups'] as $name => $params) {
      // Формируем список используемых серверов в данной группе
      $servers = [];
      // Если сервер один
      if ( $params['server'] !== array_values($params['server']) ) {
        $params['server'] = [ $params['server'] ];
      }
      foreach ($params['server'] as $server) {
        // Проверяем, если сервер используется, добавляем его
        if ($server['enabled']) {
          $servers[] = [
            $server['host'],
            $server['port'],
            $server['weight']
          ];
        }
      }

      // Создаем группу серверов
      $group = $className::getInstance($name);
      // Включаем/Отключаем группу серверов
      $group->enabledGroup($params['enabled']);
      // Добавляем сервера memcached в группу
      if ( !empty($servers) ) {
        $group->addServers($servers);
      }
    }

    // Устанавливаем группу серверов, которая будет использоваться
    // по-умолчанию
    $className::changeCurrentCacher($cacherSettings['default_group']);

    // Тестируем соединение с куширующим сервером
    $cacher = $className::getInstance();
    if (!$cacher->testConnection()) {
      $servers = $cacher->getServerList();
      $error_msg = 'Memcached error: failure connection to servers: ';
      foreach ($servers as $server) {
        $error_msg .= $server['host'] . ':' . $server['port'] . ', ';
      }

      $error_msg = trim($error_msg, ', ');
      echo $error_msg;

      die();
    }

    // $cacher->flush();

    return $this;
  } // }}}


  /**
   * Переменные с массива конфигурации загружаем в объект Registry
   *
   * @param array $config - Массив с данными конфигурации
   *
   * @return Bootstrap
   */
  public function setSettings($config) { // {{{
    $cacher = $this->_cacherClassName;

    $configer = new Config();

    // Подключение объекта класса, который отвечает за кеширование
    $configer->setCacher( $cacher::getInstance() );
    Registry::getInstance()->setCacher( $cacher::getInstance() );

    // Загружаю главные конфигурационные параметры, которые расположены
    // в general.xml
    $settingDefaultFile = $config['path']['settings'] . 'general.xml';
    $settingsDefault = $settings = $configer->open($settingDefaultFile)
      ->get('root');

    // Загружаю главные конфигурационные параметры, которые расположены
    // в settings/general.xml
    $settingFile = $config['path']['userSettings'] . 'general.xml';
    if ( file_exists($settingFile) ) {
      $settings = $configer->open($settingFile)->get('root');
      $settings = Registry::array_merge_recursive_unique($settingsDefault,
        $settings);
    }

    // Получаем минифицированную версию css файла
    $settings['css']['main']['minify_name'] =
      $this->_getMinifiedFilename('css', $config);
    $settings['css']['backend_main']['minify_name'] =
      $this->_getMinifiedFilename('css', $config, true);

    // Получаем минифицированную версию js файла
    $settings['js']['main']['minify_name'] =
      $this->_getMinifiedFilename('js', $config);
    $settings['js']['backend_main']['minify_name'] =
      $this->_getMinifiedFilename('js', $config, true);

    // Узнаем какая версия скриптов используется, минифицированная или нет
    $minify = false;
    if ( isset($settings['status']['minify_enabled']) &&
      'true' == $settings['status']['minify_enabled'] )
    {
      $minify = true;
    }

    $request = Request::getInstance();
    if ( $request->getVariableExists('minify') ) {
      $minify = false;
      $settings['status']['minify_enabled'] = 'false';
      if ( 1 == (int)$request->get('minify') ) {
        $minify = true;
        $settings['status']['minify_enabled'] = 'true';
      }
    }

    $config['minify_enabled'] = $minify;
    $config['current_url'] = $request->getCurrentUrlWithGets();

    // Получаем пути к файлам ресурсов
    foreach (array('css', 'js') as $type)
    {
      $config['url'][$type] = $this->_getPathToResource($minify,
        $config['url'], $type, $type . 'Min');

      unset($config['url'][$type . 'Min']);
    }

    // Получаем пути к файлам ресурсов
    foreach (array('layout', 'viewscript') as $type)
    {
      $config['path'][$type] = $this->_getPathToResource($minify,
        $config['path'], $type, $type . 'Min');

      unset($config['path'][$type . 'Min']);
    }

    // Получаем имена файлов ресурсов
    $config['css'] = $this->_getFileNameResource($minify, 'css', $settings);
    $config['js'] = $this->_getFileNameResource($minify, 'js', $settings);

    // Если есть svg файлы которые нужно внедрить в html, добавляю их
    $sprites = ['general', 'tires', 'disks', 'calculator'];
    foreach ($sprites as $sprite){
      $config['svg_sprite'] = true;
      $file_svg_sprite = $config['path']['public'] . 'img/_sprite-'
        . $sprite . '.svg';

      if ( file_exists($file_svg_sprite) ) {
        $config['svg_sprite_' . $sprite] = file_get_contents(
          $file_svg_sprite
        );
      }
    }

    // Объединение всех полученных данных конфигов в один объщий массив
    $config['stg'] = $settings;

    Registry::set($config);

    return $this;
  } // }}}


  /**
   * Метод, устанвлтвающий глобальные языковые переменные в объекте Registry
   *
   * @return Bootstrap
   */
  public function setLanguages() { // {{{
    $config = (array)Registry::getInstance();

    $configer = new Config();

    $languages = $this->_utils->getLanguages();
    $config['languages'] = $languages['languages'];
    $config['default_language'] = $languages['default'];

    // Получаем информацию о текущем языке
    $subdomain = Request::getInstance()->subdomain();
    $lang_exists = false;
    if ( !empty($config['languages']) ) {
      foreach ($config['languages'] as $lang) {
        if ($lang == $subdomain) {
          $lang_exists = true;

          break;
        }
      }
    }

    $current_language = $subdomain;
    if (!$lang_exists) {
      $current_language = $config['default_language'];
    }
    $config['current_language'] = $current_language;
    $config['upper_current_language'] = ucfirst($current_language);
    $config['fullupper_current_language'] = strtoupper($current_language);
    $config[$current_language] = true;

    // Загружаю главные языковые параметры
    $languageDefaultFile = $config['path']['languages']
      . $config['current_language'] . '.xml';
    $languageDefaultData = $languageData = $configer
      ->open($languageDefaultFile)->get('root');

    // Загружаю главные языковые параметры, которые расположены
    // в settings/languages/*.xml
    $languageFile = $config['path']['userSettings'] . 'languages' . DIR_SEP
      . $config['current_language'] . '.xml';
    if ( file_exists($languageFile) ) {
      $languageData = $configer->open($languageFile)->get('root');
      $languageData = Registry::array_merge_recursive_unique(
        $languageDefaultData, $languageData);
    }

    $config['lng'] = $languageData;

    Registry::set($config);

    return $this;
  } // }}}


  public function setCurrency() { // {{{
    $currency = $this->_utils->getCurrentCurrency();
    Registry::set($currency, 'currency');

    return $this;
  } // }}}


  /**
   * Установка параметров коннектора к базе данных
   *
   * @return Bootstrap
   */
  public function setDb() { // {{{
    $settings = Registry::get('stg');

    $cacher = $this->_cacherClassName;

    $db = Db::db()->setDbParams($settings['databases']['default'])
      ->setMemcached($cacher::getInstance());

    return $this;
  } // }}}


  /**
   * Метод устанавлтивает параметры хранения сессий
   *
   * @param array $config - Массив с данными конфигурации
   *
   * @return Bootstrap
   */
  public function setSession($config) { // {{{
    $setting = Registry::get('stg');

    ini_set('session.gc_maxlifetime', $setting['session']['gc_maxlifetime']);
    ini_set('session.cookie_lifetime', $setting['session']['cookie_lifetime']);

    // Проверяем, если включено хранение сессий в файлах, устанавливаем
    // настроки
    if (array_key_exists('files', $setting['session']) &&
      'true' == $setting['session']['files']['enabled'] ) {

        ini_set('session.save_path', $config['path']['sessions']);

        $this->_setProfilePrint();

        return $this;
      }

    if (array_key_exists('memcached', $setting['session']) &&
      'true' == $setting['session']['memcached']['enabled'] ) {

        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path',
          $setting['session']['memcached']['host'] . ':' .
          $setting['session']['memcached']['port']);

        $this->_setProfilePrint();

        return $this;
      }

    return $this;
  } // }}}


  /**
   * Создание маршрутизатора Router, который отвечает за формирование
   * маршрута на основе адреса и определение всех правил маршрутизации
   *
   * @return Router
   */
  public function setRouter(array $config) { // {{{
    $cacher = $this->_cacherClassName;

    // Создание объекта класса Router, который отвечает за формирование
    // маршрута на основе адреса
    $router = new Router();
    // Устанавливает правило маршрутизации, которое будет использоваться
    // если модуль не указана (например для адресов типа
    // http://example.com/about/, http://example.com/contacts/ и т.д.)
    $router->setCacher( $cacher::getInstance() )
      ->setRouteNameDefault(':page');

    $router->addRoutesFiles(
      glob($config['path']['modules'] . '*/route.json')
    );

    return $router;
  } // }}}


  /**
   * Создание и настройка вида и макета приложения
   *
   * @param string $path_layout - Путь к файлу макета страницы
   *
   * @return View
   */
  public function setView($path_layout) { // {{{
    // Инициализация макета сайта Uwin\Layout
    $layout = Layout::getInstance();
    // Установка для Uwin::Layout месторасположение макетов приложения
    $layout->setLayoutPath($path_layout);

    // Создание объекта класса Uwin\View
    $view = new View;

    // Установка глобальных переменных для вида
    $registry = Registry::getInstance();
    $registry->sitename = $_SERVER['HTTP_HOST'];
    $registry->servername = SERVER_NAME;
    $viewData = array('stg' => $registry->stg,
      'lng' => $registry->lng);
    $view->setVariables( $registry->getFlatArray($viewData) );

    return $view;
  } // }}}

  /**
   * undocumented function
   *
   * @return void
   */
  public function setPageFeatures() { // {{{
    $data = $this->_utils->getPageFeatures();

    $front = Front::getInstance()
      ->setPageFeatures($data);

    return $this;
  } // }}}
}
