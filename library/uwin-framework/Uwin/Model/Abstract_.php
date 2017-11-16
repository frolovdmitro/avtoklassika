<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Model\Abstract_, который должен быть родителем
 * всех классов моделей
 *
 * @category  Uwin
 * @package   Uwin\Model
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Model, к которому относится класс Abstract_
 */
namespace Uwin\Model;

// Объявление псевдонимов для всех используемых классов в данном файле
use \ReflectionClass,
  \Uwin\Controller\Request,
  \Uwin\Controller\Front,
  \Uwin\Profiler,
  \Uwin\Cacher\Memcached,
  \Uwin\Registry,
  \Uwin\Db,
  \Uwin\Xml,
  \Uwin\Validator;


/**
 * Класс, который должен быть родителем всех классов моделей
 *
 * @category  Uwin
 * @package   Uwin\Model
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
abstract class Abstract_
{
  private $_context = null;

  /**
   * Ссылка на коннектор к базе данных
   * @var Uwin\Db
   */
  private $_db = null;

  /**
   * Массив, содержащий переменные конфигурации для модуля
   * @var array
   */
  private $_variablesConfig = array();

  /**
   * Массив, содержащий языковые переменные для модуля
   * @var array
   */
  private $_variablesLanguage = array();

  /**
   * Массив, содержащий все переменные полученные в результате выполнения
   * методов в модели
   * @var array
   */
  private $_variables = array();

  /**
   * Признак того были ли получены переменны от модели
   * @var bool
   */
  private $_variablesIsGet = false;

  /**
   * Массив тегов которые прикреплены к функциям модели, возвращаемые знаяения
   * которых могут быть закешированы
   *
   * @var array
   */
  private $_tags = array();

  /**
   * Время жизни кеша
   *
   * @var int
   */
  private $_cacheLive = 86400;

  /**
   * Функция, которая возвращает полный путь к файлу модели
   *
   * @return string
   */
  private function _getPathByFileModel()
  {
    $class = new ReflectionClass(get_class($this));

    return $class->getFileName();
  }

  /**
   * Метод, который считывает переменные конфигурации с xml конфига
   * модуля и формирует многомерный ассоцеативный массив этих переменных,
   * который и возвращает
   *
   * @return array
   */
  private function _getVariablesConfig()
  {
    // Определение полного имени файла конфига модуля
    $dirConfig = dirname( dirname( $this->_getPathByFileModel() ) )
      . DIR_SEP . 'settings' . DIR_SEP;
    $fileDefaultConfig = $dirConfig . 'config.xml';

    // Определение полного имени файла пользовательского конфига модуля
    $fileConfig = $this->getConfigFile();

    $config_default_values = array();
    // Если файл конфига модуля со значениями по умолчанию существует
    if ( file_exists($fileDefaultConfig) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileDefaultConfig);
      $config_default_values = $configLoader->getValues();
    }

    $config_values = null;
    if ( file_exists($fileConfig) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileConfig);
      $config_values = $configLoader->getValues();
    }
    $config_values['default_variables']['stg'] = $config_default_values;

    $result = array( 'stg' =>  $config_values);

    // Передача сформированного массива приватной переменной класса, так как
    // этот массив используют другие методы класса
    $this->_variablesConfig = $result;

    return $result;
  }

  /**
   * Метод, который считывает языковые переменные с xml файла
   * модуля и формирует многомерный ассоцеативный массив этих переменных,
   * который и возвращает
   *
   * @return array
   */
  private function _getVariablesLanguage()
  {
    // Получение объекта класса реестра
    $registry = Registry::getInstance();

    // Получение имени модели
    // $nameModel = lcfirst(get_class($this));
    $class = new ReflectionClass(get_class($this));
    $nameModel = @end(
      explode(DIR_SEP, dirname(dirname($class->getFileName())))
    );

    // Определение каталога, где размещен языковый файлы модуля
    $dirDefaultLanguage = dirname( dirname( $this->_getPathByFileModel() ) )
      . DIR_SEP . 'languages' . DIR_SEP ;

    // Определение каталога, где размещен пользовательские языковый файлы
    // модуля
    $path_root = $registry['path']['userSettings'] . 'modules' . DIR_SEP;
    $dirLanguage = $path_root . $nameModel . DIR_SEP . 'languages' . DIR_SEP;

    // По умолчанию считается, что модель не относится к административной
    // части
    $pageForAdmin = false;

    // Проверка, существование в конфиге модуля свойства, которое говорит
    // относится данная модель к административной части или нет (это нужно
    // для того, чтобы определить какой язык использовать, так как язык
    // административной части может отличатся от языка сайта)
    if ( isset($this->_variablesConfig['stg'][$nameModel]) ) {
      if (
        (is_array($this->_variablesConfig['stg'][$nameModel]))
        &&
        (array_key_exists( 'pageForAdmin', $this->_variablesConfig['stg'][$nameModel] ))
      ) {
        $pageForAdmin = $this->_variablesConfig['stg']
          [$nameModel]
          ['pageForAdmin'];
      }
    }

    // На основе полученной переменной выше, решается какой языковый файл
    // использовать
    $language = 'ru';
    if ($pageForAdmin) {
      if ( isset($registry['site']['languageAdmin']) ) {
        $language = $registry['site']['languageAdmin'];
      }
    } else {
      if ( isset($registry['current_language']) ) {
        $language = $registry['current_language'];
      }
    }

    // Определение полного имени файла конфига модуля со значеиями по-умолчанию
    $fileDefaultLanguage = $dirDefaultLanguage . $language . '.xml';

    $config_default_values = array();
    if ( file_exists($fileDefaultLanguage) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileDefaultLanguage);
      $config_default_values = $configLoader->getValues();
    }

    // Определение полного имени файла конфига модуля
    $fileLanguage = $dirLanguage . $language . '.xml';

    $config_values = array();
    if ( file_exists($fileLanguage) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileLanguage);
      $config_values = $configLoader->getValues();
    }

    // Объединяем значения по умолчанию со значениеми указанными в языковом
    // файле (значение в языковом файле заменяет значение по умолчанию)
    $config_values['default_variables']['lng'] = $config_default_values;
    $result = array( 'lng' =>  $config_values);

    // Передача сформированного массива приватной переменной класса, так как
    // этот массив используют другие методы класса
    $this->_variablesLanguage = $result;

    return $result;
  }

  /**
   * Метод возвращает коннектор к базе данных, если он уже был установлен до
   * этого и не изменилось имя коннекотра, или создает новый коннектор с
   * новыми параметрами подключения и возвращает его
   *
   * @param array $params Параметры подключения
   * @param string $name Имя коннектора к базе данных
   *
   * @return \Uwin\Db
   */
  protected function db(array $params = null, $name = null)
  {
    if ( (null == $this->_db) || (null != $name) ) {
      $db = Db::db($name, $this);

      if ( null != $params ) {
        $db->setDbParams($params);
      }

      $this->_db = $db;
    }

    $registry = Registry::getInstance();
    if ( isset($registry['current_language']) ) {
      /** @noinspection PhpUndefinedFieldInspection */
      $this->_db->setLanguage( $registry->current_language );
    }

    return $this->_db;
  }

  /**
   * Метод, который получает все переменные которые могут быть нужны для
   * модуля (глобальные переменные с реестра, переменные конфигурации
   * модуля и языковые переменные модуля) и формирует многомерный
   * ассоцеативный массив этих переменных
   *
   * @return array
   */
  protected function _getVariablesModel()
  {
    $memcached = Memcached::getInstance();

    $variables_config   = $this->_getVariablesConfig();
    $variables_language = $this->_getVariablesLanguage();
    $variables_config['route'] = $this->getRequest()->get('route');

    $keyMemcached = md5( serialize($variables_config) .
      serialize($variables_language) );
    $dataMemcached = $memcached->get($keyMemcached);

    if (false === $dataMemcached) {
      $registry = Registry::getInstance();

      $variables_config_default = $variables_config['stg']['default_variables'];
      unset($variables_config['stg']['default_variables']);
      $variables_config = $registry->getFlatArray($variables_config);
      $variables_config_default = $registry->getFlatArray($variables_config_default);

      $variables_language_default = $variables_language['lng']['default_variables'];
      unset($variables_language['lng']['default_variables']);
      $variables_language = $registry->getFlatArray($variables_language);
      $variables_language_default = $registry->getFlatArray($variables_language_default);

      $dataMemcached = array_merge($variables_config_default, $variables_config,
        $variables_language_default, $variables_language
      );

      if ( isset($dataMemcached['lng_general_address']) ) {
        $dataMemcached['language_general_address'] = nl2br($dataMemcached['language_general_address']);
      }
      $memcached->set($keyMemcached, $dataMemcached);
    }

    return $dataMemcached;
  }

  /**
   * Метод возвращает ключ, который используется при кешировании данных методов
   * класса
   *
   * @param string $name
   * @param mixed $params
   *
   * @return string
   */
  private function _getKey($name, $params) {
    $key = strtolower(Registry::get('current_language')
      . get_called_class() . $name
      . (empty($params) ? '' : md5(serialize($params))) );

    return $key;
  }

  /**
   * Метод возвращает ссылку на объект класса запроса
   *
   * @return \Uwin\Controller\Request
   */
  protected function getRequest()
  {
    return Request::getInstance();
  }

  /**
   * Получение имени файла конфируации модуля
   *
   * @return string
   */
  public function getConfigFile()
  {
    // Получение объекта класса реестра
    $registry = Registry::getInstance();
    $path_root = $registry['path']['userSettings'] . 'modules' . DIR_SEP;

    // Получение имени модели
    // $nameModel = lcfirst(get_class($this));
    $class = new ReflectionClass(get_class($this));
    $nameModel = @end(
      explode(DIR_SEP, dirname(dirname($class->getFileName())))
    );

    // Определение каталога, где размещен файл конфига модуля
    $dirConfig = $path_root . $nameModel . DIR_SEP;

    // Определение полного имени файла конфига модуля
    $fileConfig = $dirConfig . 'config.xml';

    return $fileConfig;
  }

  /**
   * Получение имени языкового файла модуля
   *
   * @return string
   */
  public function getLanguageFile()
  {
    // Получение объекта класса реестра
    $registry = Registry::getInstance();
    $path_root = $registry['path']['userSettings'] . 'modules' . DIR_SEP;

    $class = new ReflectionClass(get_class($this));
    $nameModel = @end(
      explode(DIR_SEP, dirname(dirname($class->getFileName())))
    );
    // Определение каталога, где размещен языковый файл модуля
    $dirLanguage = $path_root . $nameModel . DIR_SEP . 'languages' . DIR_SEP;

    return $dirLanguage . $registry['current_language'] . '.xml';
  }

  /**
   * Метод устанавливает или изменяет указанную переменную модели
   *
   * @param string $name Имя переменной
   * @param mixed $value Значение переменной
   *
   * @return Abstract_
   */
  public function setVariable($name, $value)
  {
    $this->_variables[$name] = $value;

    return $this;
  }

  /**
   * Метод добавляет массив переменных в модель
   *
   * @param array $values Массив переменных
   *
   * @return Abstract_
   */
  public function setVariables(array $values)
  {
    $this->_variables = array_merge($this->_variables, $values);

    return $this;
  }

  /**
   * Метод возвращает указанную переменную модели
   *
   * @param string $name Имя переменной
   * @return mixed
   */
  public function getVariable($name)
  {
    // Если переменные не были получены от модели - получить их
    if (!$this->_variablesIsGet) {
      $this->getVariables();
    }

    if ( array_key_exists($name, $this->_variables) ) {
      return $this->_variables[$name];
    }

    return false;
  }

  /**
   * Метод, который формирует одномерный ассоцеативный массив всех переменных,
   * которые может использовать модель
   *
   * @param bool $recreateVariables = false
   * @param bool $withContext       = true
   *
   * @return array
   */
  public function getVariables($recreateVariables = false, $withContext = true)
  {
    Profiler::getInstance()->startCheckpoint('Config', 'Get module ' . $this->_getPathByFileModel() . ' config/language data');

    if ( (true === $recreateVariables) || (false === $this->_variablesIsGet) ) {
      $variablesModel = $this->_getVariablesModel();

      if ( $withContext && !empty($this->_context) ) {
        $variablesModel = array($this->_context => $variablesModel);
      }

      $registry = Registry::getInstance();
      $action = $this->getRequest()->getActionName();

      $globalVariables = $registry->getFlatArray( (array)$registry );

      foreach( array('title', 'description', 'keywords') as $variable ) {
        $fullVariableName = 'lng_' . $action . '_' . $variable;

        if ( array_key_exists($fullVariableName, $variablesModel) ) {
          $globalVariables[$variable] = strip_tags($variablesModel[$fullVariableName]);
        }
      }

      $this->_variables = array_merge($globalVariables,
        $this->_variables, $variablesModel);

      $this->_variablesIsGet = true;
    }

    $action = $this->getRequest()->getParam('action');
    if ( isset($this->_variables['lng_' . $action . '_title']) ) {
      $this->setVariable('title',
        strip_tags($this->_variables['lng_' . $action . '_title']));
    }
    if ( isset($this->_variables['lng_' . $action . '_description']) ) {
      $this->setVariable('description',
        $this->_variables['lng_' . $action . '_description']);
    }
    if ( isset($this->_variables['lng_' . $action . '_keywords']) ) {
      $this->setVariable('keywords',
        $this->_variables['lng_' . $action . '_keywords']);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this->_variables;
  }

  /**
   * @param  $context
   * @return Abstract_
   */
  public function setContext($context)
  {
    $this->_context = $context;

    return $this;
  }

  public function getContext()
  {
    return $this->_context;
  }

  public function createModel($module, $model = null) {
    if (null == $model) {
      $model = $module;
    }

    $model = ucfirst($model);

    $class = new ReflectionClass(get_class($this));
    $modules_path = dirname(dirname(dirname($class->getFileName())));

    $fileModel = $modules_path . DIR_SEP . $module . DIR_SEP .
      'models' . DIR_SEP . $model . '.php';

    if ( !file_exists($fileModel) ) {
      throw new Exception('Model error: model "' . $fileModel . '" not fount', 1001);
    }

    require_once ($fileModel);

    return new $model();
  }

  /**
   * Метод возвращает правила валидации для формы модуля
   *
   * @param string $form - Имя формы, которая валидируется
   *
   * @return array
   */
  public function getValidateRules($form) {
    $result = array();
    $variables = $this->_getVariablesConfig();
    $variables = $variables['stg']['default_variables']['stg'];
    // var_dump($variables);

    if ( !isset($variables['validate']) ) {
      return $result;
    }

    if ( !isset($variables['validate'][$form]) ) {
      return $result;
    }

    return $variables['validate'][$form];
  }

  /**
   * Валидация формы и возврат либо массива ошибок либо true. Правила
   * валидации расположены в config.xml, тексты ошибок в языковых файлах
   * модуля
   *
   * @param string $form -  имя формы
   * @param array $data - данные для валидации
   *
   * @return array|bool
   */
  public  function validate($form, array $data) {
    $validator = new Validator();

    $errors = $validator->validate($form,
      $this->getValidateRules($form),
      $data,
      $this->getVariables()
    );

    if ( !empty($errors) ) {
      $this->getRequest()->sendHeaderError();

      return $errors;
    }

    return true;
  }

  /**
   * Метод устанавливает массив тегов для значения указанной функции, которое
   * будет складыватся в кеш
   *
   * @param string $name - имя функции, для которой добавляются теги
   * @param array  $tags - массив тегов
   * @param mixed  $params = null - массив параметров метода
   *
   * @return Abstract_
   */
  public function setTags($name, array $tags, $params = null) {
    $key = $this->_getKey($name, $params);

    $this->_tags[$key] = $tags;

    return $this;
  }

  /**
   * Метод возваращет массив тегов для указанной функции модели
   *
   * @param string $name - имя функции
   * @param mixed $params - параметры метода
   *
   * @return array
   */
  public function getTags($name, $params = null) {
    $key = $this->_getKey($name, $params);

    if ( !isset($this->_tags[$key]) ) {
      return array();
    };

    return $this->_tags[$key];
  }

  /**
   * Метод удаляет массив тегов для указанного метода модели
   *
   * @param string $name - Имя метода
   * @param mixed  $params - параметры метода
   *
   * @return Abstract_
   */
  public function removeTags($name, $params = null) {
    $key = $this->_getKey($name, $params);

    if ( !isset($this->_tags[$key]) ) {
      return $this;
    };

    unset($this->_tags[$key]);

    return $this;
  }

  /**
   * Метод очищает все теги для всех методов модели
   *
   * @return Abstract_
   */
  public function clearTags() {
    $this->_tags = array();

    return $this;
  }

  /**
   * Метлд устанавливает время на которое будет сохранен кеш
   *
   * @param int $seconds - Кол-во секунд
   *
   * @return Abstract_
   */
  public function setCacheLive($seconds) {
    $this->_cacheLive = (int)$seconds;

    return $this;
  }

  /**
   * Метод возварщает кол-во секунд на которое будет сохранен кеш
   *
   * @return int
   */
  public function getCacheLive() {
    return $this->_cacheLive;
  }

  /**
   * Метод возвращает кеш указанного метода
   *
   * @param string $method - имя метода
   * @param mixed  $params - массив параметров метода
   *
   * @return mixed
   */
  public function getCache($method, $params = null) {
    $key = $this->_getKey($method, $params);

    return Memcached::getInstance()->get($key);
  }

  /**
   * Метод сохраняет кеш указанного метода
   *
   * @param string $method
   * @param mixed $value
   * @param mixed $params
   *
   * @return Abstract_
   */
  public function saveCache($method, $value, $params = null) {
    $key = $this->_getKey($method, $params);

    Memcached::getInstance()->set($key
      , array('_global' => $this->getVariables(), '_block' => $value)
      , $this->getCacheLive(), $this->getTags($method, $params) );

    return $this;
  }

  public function getJsonFilterByEnv() {
    $request = $this->getRequest();
    $get_result = $routes_result = $referels_result = $userdata_results = null;

    $get = $request->get();
    if ( !empty($get) ) {
      foreach (array_keys($get) as $val){
        $get_result[] = [$val => $get[$val]];
      }

      $get_result = json_encode($get_result);
    }

    $route = $request->getParams();
    if ( !empty($route) ) {
      foreach (array_keys($route) as $val){
        $routes_result[] = [$val => $route[$val]];
      }

      $routes_result = json_encode($routes_result);
    }

    $referel = $request->getReferer(true);
    if ( !empty($referel) ) {
      $referels_result = json_encode([[$referel => true]]);
    }

    return [
      $get_result,
      $routes_result,
      $referels_result,
      $userdata_results,
    ];
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function getModulesPath() {
    $registry = Registry::getInstance();
    $path = $registry->path['modules'];

    if ( array_key_exists('stg', $registry)
       && array_key_exists('status', $registry['stg'])
       && array_key_exists('minify_enabled', $registry['stg']['status'])
       && 'true' == $registry['stg']['status']['minify_enabled']) {
      $path = $registry->path['build']['modules'];
    }

    return $path;
  }
}
