<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\TemplaterBlitz, который парсит файл шаблона
 *
 * @category   Uwin
 * @package    Uwin\TemplaterBlitz
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс TemplaterBlitz
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Blitz;
use \Uwin\View;
use \Uwin\Controller\Front;
use \Uwin\Linguistics;
use \Uwin\Registry;
use \Uwin\Cacher\Memcached;
use \Uwin\TemplaterBlitz\Exception as BlitzException;

/**
 * Класс, который парсит переданный ему файл шаблона
 *
 * @category  Uwin
 * @package   Uwin\TemplaterBlitz
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class TemplaterBlitz extends Blitz
{
  private $templateFile = null;

  private $_includePath = null;

  private $_componentsPath = null;

  private $_linguistics = null;

  /**
   * Ссылка на объект класса \View
   * @var View
   */
  private $_view;

  private $_currentLang = null;

  /**
   * Конструктор класса. Переопределен только потому, что после вызова
   * родительского конструктора нужно в данном классе определить переменную
   * объекта вида
   *
   * @param string $templateFile Путь к файлу шаблона
   * @param View $view Ссылка на объект класса Uwin\View
   * @throws BlitzException Ошибка работы c шаблонизатором Blitz
   * @return TemplaterBlitz
   */
  public function __construct($templateFile = null, View $view = null)
  {
    $this->_view = $view;
    $this->_currentLang = strtolower(Registry::get('current_language'));

    if (null === $templateFile) {
      parent::__construct();

      return $this;
    }

    if ( !file_exists($templateFile) ) {
      // throw new BlitzException('Templater Blitz error: template file "' . $templateFile . '" not found', 901);
      return null;
    }

    parent::__construct($templateFile);

    $this->templateFile = $templateFile;

    return $this;
  }

  /**
   * Магический метод, который используется для подключения дополнительно
   * определенных хелперов
   *
   * @param string $methodName Имя метода
   * @param array $args Массив аргументов передаваемых казанному методу
   * @return mixed
   */
  public function __call($methodName, array $args = array())
  {
    if ( !method_exists($this, $methodName) ) {
      $className = '\Uwin\View\Helper\\' . ucfirst($methodName);
      $class = new $className($this->_view);

      return $class->$methodName();
    } else {
      return null;
    }
  }

  /**
   * Метод, которй подключает в шаблон контентую часть
   *
   * @throws BlitzException Ошибка работы c шаблонизатором Blitz
   * @return string
   */
  public function content()
  {
    Profiler::getInstance()->startCheckpoint('Template', 'Content');

    $templateFile = $this->_view->getTemplateFile();

    $result = null;
    if ( file_exists($templateFile) ) {
      $result = $this->include($templateFile);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $result;
  }

  /**
   * Метод, который рендерит шаблон для указанного модуля/котроллера/действия.
   * Предпочтительно вместо этого метода использовать includeBlock(), так как
   * этот метод полчает доступ к модели и представлению через контроллер, что
   * вызывает дополнительные затраты времени
   *
   * @param string $module Имя модуля
   * @param string $controller Имя контроллера
   * @param string $action Имя действия
   * @param array $args Массив переменных
     *
   * @return string
   */
  public function action($module, $controller, $action, array $args = array())
  {
    Profiler::getInstance()->startCheckpoint('Template', 'Action ' . $module . '::' . $controller . '::' . $action);

    $front = Front::getInstance();

    $controller = $front->createController($module, $controller, $action);
    $view = new View;
    $controller->setView($view);
    $controller->$action();

    $result = $view->render();

    Profiler::getInstance()->stopCheckpoint();

    return $result;
  }

    /**
     * Метод, который подключает в главный макет указанный шаблон и определеят
     * все переменны полученные с модели
     *
     * @param string $module Имя модуля
     * @param string $controller Имя контроллера
     * @param string $action Имя действия
     * @param array $vars массив переменных
     * @param null $name_tamplate
     *
     * @return string
     * @throws BlitzException
     */
  public function includeBlock($module, $controller, $action, $vars = null, $name_tamplate = null, $tags = null, $useParams = null)
  {
    Profiler::getInstance()->startCheckpoint('Template', 'Include ' . $module . '::' . $controller . '::' . $action);

    $templateFile = $this->_view->getTemplateFile($module, $controller, $action);

    if (null !== $name_tamplate) {
      if ('/' !== $name_tamplate[0]) {
        $templateFile = $this->_view->getTemplateFile($module, $controller, $name_tamplate);
      } else {
        $registry = Registry::getInstance();
        $path = $registry->path['modules'];

        if ( array_key_exists('stg', $registry)
          && array_key_exists('status', $registry['stg'])
          && array_key_exists('minify_enabled', $registry['stg']['status'])
          && 'true' == $registry['stg']['status']['minify_enabled']) {
          $path = $registry->path['build']['modules'];
        }

        $templateFile = rtrim($path, DIR_SEP) . $name_tamplate . '.tpl';
      }
    }

    if ( !file_exists($templateFile) && false !== $name_tamplate) {
      // throw new BlitzException('Templater Blitz error: template file "' . $templateFile . '" not found', 901);
      return null;
    }

    $paramsSerialized = '';
    $paramsArray = [];
    if (!empty($useParams)) {
      $params = json_decode($useParams, true);
      $request = Front::getInstance()->getRequest();
      if (isset($params['params'])) {
        foreach ($params['params'] as $param) {
          $paramsArray['params'][$param] = $request->getParam($param);
        }
      }

      if (!empty($paramsArray)) {
        $paramsSerialized = md5(serialize($paramsArray));
      }
    }

    $cacheKey = strtolower(Registry::get('current_language')
      . $controller . 'get' . $action
      . (empty($vars) ? '' : md5(serialize($vars))) . $paramsSerialized);

    $cache = Memcached::getInstance()->get($cacheKey);
    $cacheBody = Memcached::getInstance()->get($cacheKey . '__body');

    $variables = $result = array();

    if (false !== $cacheBody && !empty($tags) ) {
      Profiler::getInstance()->stopCheckpoint();
      return $cacheBody;
    } elseif (false !== $cache) {
      if ( isset($cache['_global']) ) {
        $variables = $cache['_global'];
      }

      if ( isset($cache['_block']) ) {
        $result = $cache['_block'];
      }
    } else {
      $fileModel = Front::getInstance()->getModelFile($module, $controller);

      if ( file_exists($fileModel) ) {
        require_once($fileModel);

        /**
        * @var Model\Abstract_ $model
        */
        $model = new $controller;
        $variables = $model->getVariables();

        $method = 'get' . $action;
        if ( method_exists($model, $method) ) {
          if ( empty($vars) ) {
            $result = $model->$method();
          } else {
            if (!is_array($vars) && $vars[0] == '[' && $vars[strlen($vars) - 1] == ']') {
              $vars = explode(',', substr($vars, 1, -1));
              $result = call_user_func_array([$model, $method], $vars);
            } else {
              $result = $model->$method($vars);
            }
          }
          $variables = $model->getVariables();

          if (false === $name_tamplate) {
            Profiler::getInstance()->stopCheckpoint();

            return $result;
          }
        }
      }
      if ( is_array($vars) ) {
        $variables = array_merge_recursive($variables, $vars);
      }
    }

    if ( !is_array($result) ) {
      //TODO Временная заглушка
      $result = $variables;
    }

    $this->setGlobals($variables);
    $result = $this->include($templateFile, $result);

    if ( !empty($tags) ) {
      $tags = explode(',', $tags);
      Memcached::getInstance()->set($cacheKey . '__body', $result, 86400, $tags);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $result;
  }

  public function ib($module, $controller, $action, $vars = null, $name_tamplate = null, $tags = null) {
    return $this->includeBlock($module, $controller, $action,
      $vars, $name_tamplate, $tags);
  }

  public function ibm($module, $action, $vars = null, $name_tamplate = null, $tags = null, $useParams = null) {
    $controller = $module;

    return $this->includeBlock($module, $controller, $action,
      $vars, $name_tamplate, $tags, $useParams);
  }

  public function ic($component, $vars = []) {
    $script_path = Registry::get('path');
    if ( null != $this->getComponentsPath() ) {
      $script_path['components'] = $this->getComponentsPath();
    }

    $templateFile = $script_path['components'] .  $component . '/index.tpl';

    if ( !file_exists($templateFile) ) {
      // throw new BlitzException('Templater Blitz error: template file "' . $templateFile . '" not found', 901);
      return null;
    }

    $result = $this->include($templateFile, $vars);

    return $result;
  }

  public function json($json, $path, $x2 = false) {
    $json = json_decode($json, true);
    $path_arr = explode('.', $path);

    foreach ($path_arr as $path_el) {
      $json = $json[$path_el];
    }

    if ($x2) {
      $json = str_replace('.', '@2x.', $json);
    }

    return $json;
  }

  public function imgsrc($image, $name, $_ = null) {
    $image = json_decode($image, true);
    if ( empty($_) ) {
      $staticServer = $this->_view->getVariable('url_staticServer');
    } else {
      $staticServer = $_['url_staticServer'];
    }

    $html = 'src="' . $staticServer . $image[$name]['path']
      . '" width="' . $image[$name]['width']
      . '" height="' . $image[$name]['height'] . '"';

    return $html;
  }

  public function imgsrcset($image, $name = 'original', $_ = null) {
    $image = json_decode($image, true);
    if ( empty($_) ) {
      $staticServer = $this->_view->getVariable('url_staticServer');
    } else {
      $staticServer = $_['url_staticServer'];
    }

    $path = $staticServer . $image[$name]['path'];
    $pathinfo = pathinfo($staticServer . $image[$name]['path']);
    $pathX2 = $pathinfo['dirname'] . DIR_SEP . $pathinfo['filename'] . '@2x.'
      . $pathinfo['extension'];
    $html = 'src="' . $path
      . '" srcset="' . $path . ' 1x, ' . $pathX2 . ' 2x"'
      . ' width="' . (int)($image[$name]['width']/2)
      . '" height="' . (int)($image[$name]['height']/2) . '"';

    return $html;
  }

  public function cost($value, $decimal = 0, $show_currency = true) {
    $currency = Registry::get('currency');
    $name = str_replace('__CL', '', $currency['synonym']);

    $value_unformatted = $value;
    $value_unformatted = round($value_unformatted, $decimal);

    $value /= $currency['rate'];
    $value = round($value, $decimal);

    setlocale(LC_MONETARY, $name . '.UTF8');
    $value = str_replace('Eu', '&euro;', money_format('%.0n', $value) );
    if (false === mb_strpos($value, 'грн')) {
      $value = str_replace('гр', ' грн.', $value);
    }

    if ($name == 'en_US') {
      $value = str_replace(',', ' ', $value);
    }

    $abbr = '';
    if (!$show_currency) {
      $value = str_replace(' грн.', '', $value);
      $value = str_replace(' руб.', '', $value);

      $abbr = ' data-hide-abbr="1"';
    }

    return '<span class="__price" data-value="' . $value_unformatted . '" '
      . $abbr . '>' . $value . '</span>';
  }

  public function costEncode($value, $decimal = 0, $show_currency = true) {
    $result = $this->cost($value, $decimal = 0, $show_currency = true);

    return htmlentities($result);
  }

  public function inc($value) {
    return ++$value;
  }

  public function dec($value) {
    return --$value;
  }

  public function mod($value, $mod) {
    return $value%$mod;
  }

  public function shorted($value, $length, $strip_tags = true, $html_decode = false) {
    if ( empty($this->_linguistics) ) {
      $this->_linguistics = new Linguistics();
    }

    if ($html_decode) {
      $value = html_entity_decode($value);
    }

    return $this->_linguistics->shortedText($value, $length, $strip_tags);
  }

  public function replace($value, $search, $replace = '') {
    return str_replace($search, $replace, $value);
  }

  public function float($value) {
    $result = (float)$value;
    if (0 == $result) {
      return null;
    }

    return $result;
  }

  public function floatUS($value) {
    $result = (string)$value;
    if (0 == $result) {
      return null;
    }

    return str_replace(',', '.', $result);
  }

  public function bylang($name, $_, $strip_tags = false) {
    $result = $_[$name . '_' . $this->_currentLang];
    if ($strip_tags) {
      $result = strip_tags($result);
    }

    return $result;
  }

  public function fromlang($prefix, $name, $_, $strip_tags = false) {
    if ( !isset($_['lng_' . $prefix . $name]) ) {
      return null;
    }

    $result = $_['lng_' . $prefix . $name];
    if ($strip_tags) {
      $result = strip_tags($result);
    }

    return $result;
  }

  public function setIncludePath($path) {
    $this->_includePath = $path;

    return $this;
  }

  public function getIncludePath() {
    return $this->_includePath;
  }

  public function setComponentsPath($path) {
    $this->_componentsPath = $path;

    return $this;
  }

  public function getComponentsPath() {
    return $this->_componentsPath;
  }

  public function includeScript($script)
  {
    Profiler::getInstance()->startCheckpoint('Template', 'Include script ' . $script);

    $script_path = Registry::get('path');
    if ( null != $this->getIncludePath() ) {
      $script_path['viewscript'] = $this->getIncludePath();
    }

    $result = $this->include($script_path['viewscript'] . $script);

    Profiler::getInstance()->stopCheckpoint();

    return $result;
  }

  public function is($script) {
    return $this->includeScript($script);
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function tmpl($body, $vars, $strip_tags = false) {
    $blitz = new Blitz;
    $blitz->load($body);
    $text = $blitz->parse($vars);
    if ($strip_tags) {
      $text = strip_tags($text);
    }
    $text = preg_replace('/\s\s+/u', ' ', $text);

    return $text;
  }

  public function var_export_br($data) {
    ob_start();
    var_dump($data);
    $data = ob_get_contents();
    ob_end_clean();

    $data = str_replace(' ','&nbsp;',$data);
    $data = nl2br($data);

    return $data;
  }
}
