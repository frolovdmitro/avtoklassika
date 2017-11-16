<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\View, который служит для отображения вида
 *
 * @category   Uwin
 * @package    Uwin\View
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс View
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Request as Request;
use \Uwin\TemplaterBlitz     as TemplaterBlitz;

/**
 * Класс, который служит для рендеринга вида
 *
 * @category  Uwin
 * @package   Uwin\View
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class View
{
  /**
   * Массив для хранения всех переменных вида
   * @var array
   */
  private $_vars = array();

  private $_globals = array();

  private $_useTamplater = true;

  private $_printVariable = null;

  private $_useJSON = false;

  private $_templateFile = null;
  /**
   * Метод, который с помощью шаблонизатора Blitz, парсит файл шаблона,
   * который передан ему аргументом
   *
   * @param string $templateFile Имя шаблона который нужно парсить
   * @return string
   */
  private function _parse($templateFile)
  {
    // Создание экземпляра класса TemplaterBlitz и передача ему пути к
    // файлу шаблона который следует разобрать и ссылки на объект вида
    $template = new TemplaterBlitz($templateFile, $this);
    $registry = Registry::getInstance();

    if ( array_key_exists('stg', $registry)
       && array_key_exists('status', $registry['stg'])
       && array_key_exists('minify_enabled', $registry['stg']['status'])
       && 'true' == $registry['stg']['status']['minify_enabled']) {
      $template->setComponentsPath($registry->path['build']['components']);
    }
    // Передача всех переменных установленных в виде шаблонизатору
    $template->set($this->_vars);
    $template->setGlobals($this->_globals);

    // Парсинг шаблона
    $result = $template->parse();

    return $result;
  }

    /**
     * @param $use
     * @return View
     */
    public function useTemplater($use)
  {
    $this->_useTamplater = $use;

    Profiler::getInstance()->printedStats($use);

    return $this;
  }

  /**
   * Метод устанавливает переменные используемые в данном виде, все старые
   * переменные при этом уничтожаются
   *
   * @param array $variables Массив переменных
   * @return View
   */
  public function setVariables(array $variables)
  {
    $this->_vars = $variables;

    return $this;
  }

  /**
   * Метод возвращает массив переменных используемых в данном виде
   *
   * @return array
   */
  public function getVariables()
  {
    return $this->_vars;
  }

  public function setGlobals(array $variables)
  {
    $this->_globals = $variables;

    return $this;
  }

  public function setGlobal($name, $value)
  {
    $this->_globals[$name] = $value;

    return $this;
  }

  public function getGlobals()
  {
    return $this->_globals;
  }

  /**
   * Метод добавляет указанные переменные в общий массив переменных, которые
   * используются в данном виде
   *
   * @param array $variables Массив переменных
   * @return \Uwin\View
   */
  public function appendVariables(array $variables)
  {
    $this->_vars = array_merge( $this->_vars, $variables);

    return $this;
  }

  /**
   * Метод устанавливает новую переменную или изменяет существующую в массиве
   * переменных вида
   *
   * @param string $name Имя переменной
   * @param mixed $value Значение переменной
   * @return View
   */
  public function setVariable($name, $value)
  {
    $this->_vars[$name] = $value;

    return $this;
  }

  /**
   * Метод устанавливает или изменяет значение указанной переменной в массиве
   * переменных вида
   *
   * @param string $name Имя переменной
   * @return mixed
   */
  public function getVariable($name)
  {
    if ( isset($this->_vars[$name]) ) {
      return $this->_vars[$name];
    }

    return null;
  }

  public function printVariable($variable, $json = false)
  {
    $this->_useJSON = $json;

    $this->_printVariable = $variable;

    return $this;
  }

  /**
   * Метод, который возвращает путь к файлу шаблона? строя его на основе имени
   * модуля, контроллера, действия
   *
   * @param string $module Имя модуля. Если не указано, используется $this->_module
   * @param string $controller Имя контроллера. Если не указано, используется $this->_controller
   * @param string $action Имя действия. Если не указано, используется $this->_action
   * @return string
   */
  public function getTemplateFile($module = null, $controller = null, $action = null)
  {
    // Если не переданы значения имен модуля, контроллера, действия,
    // определяем их
    if (null == $module) {
      $request = Request::getInstance();

      if ( !empty($this->_templateFile) ) {
        return $this->_templateFile;
      }

      $module = $request->getModuleName();
      $controller = $request->getControllerName();
      $action = $request->getActionName();
    }

    // Получаю ссылку на объект реестра
    $registry = Registry::getInstance();
    $path = $registry->path['modules'];

    if ( array_key_exists('stg', $registry)
       && array_key_exists('status', $registry['stg'])
       && array_key_exists('minify_enabled', $registry['stg']['status'])
       && 'true' == $registry['stg']['status']['minify_enabled']) {
      $path = $registry->path['build']['modules'];
    }

    $templateFile = $path . $module . DIR_SEP . 'views' . DIR_SEP .
      $controller . DIR_SEP . $action . '.tpl';

    return $templateFile;
  }

  public function setTemplate($module, $controller, $action)
  {
    $this->_templateFile = $this->getTemplateFile($module, $controller, $action);

    return $this;
  }

  /**
   * Метод получает имя шаблона, парсит его и возвращает отпарсенное
   * представление вида
   *
   * @param string $templateFile Имя файла шаблона который нужно отрендерить, если не указао, используется $this->getTemplateFile()
   * @return string
   */
  public function render($templateFile = null)
  {
    Profiler::getInstance()->startCheckpoint('View', 'Render template');

    if ($this->_useTamplater) {
      if (null === $templateFile) {
        $templateFile = $this->getTemplateFile();
      }

      $result = $this->_parse($templateFile);

    } else {
      $result = null;
      if ( array_key_exists( $this->_printVariable, $this->getVariables() ) ) {
        if ($this->_useJSON) {
          $result = json_encode( $this->getVariable($this->_printVariable), JSON_UNESCAPED_UNICODE );
        } else {
          $result = $this->getVariable($this->_printVariable);
        }
      }

    }

    Profiler::getInstance()->stopCheckpoint();

    return $result;

  }
}
