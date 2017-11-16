<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Db, который отвечает за работу с базой данных
 * PostgreSQL
 *
 * @category  Uwin
 * @package   Uwin\Db
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Db
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Db\Query;
use \Uwin\Cacher\Memcached;
use \Uwin\Db\Exception as DbException;

/**
 * Класс, который отвечает за работу с базой данных PostgreSQL
 *
 * @category  Uwin
 * @package   Uwin\Db
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Db
{
  /**
   * Имя по умолчанию объекта данного класса, который выполняет роль
   * коннекотора к базе данных
   */
  const DEFAULT_NAME_DB_CONNECTOR = 'default';

  /**
   * Массив ссылкок на объекты данного классы, которые выполняют роль
   * коннекторов к различным базам данных
   * @var array
   */
  private static $_dbConnectors = array();

  /**
   * Текущее имя объекта данного класса, который выполняет роль
   * коннекотора к базе данных
   * @var string
   */
  private static $_currentNameDbConnector = self::DEFAULT_NAME_DB_CONNECTOR;

  /**
   * Массив параметров подключения к базе данных
   * @var array
   */
  private $_dbParams = array();

  /**
   * Ресурс соединения к базе данных (с помощью этого ресурса происходит
   * вся работа с базой данных)
   * @var resource
   */
  private $_dbAdapter = null;

  /**
   * Имя объекта данного класса, который выполняет роль коннекотора к базе
   * данных
   * @var string
   */
  private $_nameDbConnector = null;

  /**
   * Признак того, использует база данных Memcached или нет
   * @var Memcached = null
   */
  private $_memcached = null;

  /**
   * Язык всех запросов, поля которых будут извлекаться
   * @var null
   */
  private $_language = null;

  private $_calledClass = null;

  /**
   * Приватный конструктор класса, так как класс реализует паттерн Singlton.
   */
  private function __construct() {}

  /**
   * Приватный магический метод класса, который запрещает клонировать объекты
   * этого класса, так как класс реализует паттерн Singlton.
   *
   * @return void
   */
    private function __clone() {}

    /**
     * Метод создает объект данного класса, если он не был создан ранне,
     * сохраняет ссылку на него в статическом массиве данного класса,
     * в котором хранятся все коннекторы и возвращает его
     *
     * @param string $nameDbConnector - Имя коннектора к базе данных
     *
     * @return Db
     */
    public static function db($nameDbConnector = null, $calledClass = null)
    {
      // Если имя коннектора не указано, использовать текущее имя коннектора
      if (null == $nameDbConnector) {
        $nameDbConnector = self::$_currentNameDbConnector;
      }

      // Если коннектора с таким именем не существует, создаем его и сохраняем
      // в статическом массиве данного класса, в котором хранятся все коннекторы
      if ( !isset(self::$_dbConnectors[$nameDbConnector]) ) {
        self::$_dbConnectors[$nameDbConnector] = new self;
        self::$_dbConnectors[$nameDbConnector]->_nameDbConnector = $nameDbConnector;
      }

      self::$_dbConnectors[$nameDbConnector]->_calledClass = $calledClass;

      // Возвращаем объект коннектора базы данных
      return self::$_dbConnectors[$nameDbConnector];
    }

  /**
   * Метод возвращает список имен коннекторов к базе данных
   *
   * @return array
   */
  public static function getDbConnectorsNames()
  {
    return array_keys(self::$_dbConnectors);
  }

  /**
   * Метод изменяет коннектор, который является текущим
   *
   * @param string $nameDbConnector Имя коннектора к базе данных
   *
   * @return Db
   *
   * @throws DbException Ошибка работы с базой данных
   */
  public static function changeCurrentDb($nameDbConnector = self::DEFAULT_NAME_DB_CONNECTOR)
  {
    // Если такого конекотора не существует - возвращаем ложь
    if ( !isset(self::$_dbConnectors[$nameDbConnector]) ) {
      throw new DbException('Db error: failure change current DB on ' . $nameDbConnector, 504);
    }

    self::$_currentNameDbConnector = $nameDbConnector;

    return self::$_dbConnectors[$nameDbConnector];
  }

  /**
   * Метод возвращает имя коннектора, данного объекта класса
   *
   * @return string
   */
  public function getNameDb()
  {
    return $this->_nameDbConnector;
  }

  /**
   * Метод устанавливает признак того, используется Memcached или нет
   *
   * @param Memcached $memcached
   *
   * @return Db
   */
  public function setMemcached(Memcached $memcached)
  {
    $this->_memcached = $memcached;

    return $this;
  }

  /**
   * Метод возвращает признак того, используется Memcached или нет
   *
   * @return Memcached
   */
  public function getMemcached()
  {
    return $this->_memcached;
  }

  /**
   * Метод возвращает дескриптор соединение с базой данных
   *
   * @return resource
   */
  public function dbAdapter()
  {
    return $this->_dbAdapter;
  }

  /**
   * Метод устанавливает параметры подключения к базе данных
   *
   * @param array $params - Массив параметров соединения с базой данных
   *
   * @return Db
   */
  public function setDbParams(array $params)
  {
    $this->_dbParams = $params;

    return $this;
  }

  /**
   * Метод возвращает параметры подключения к базе данных в виде массива
   *
   * @return array
   */
  public function getDbParams()
  {
    return $this->_dbParams;
  }

  /**
   * Метод устанавливает указанный параметр подключения к базе данных
   *
   * @param string $name Имя параметра
   * @param mixed $value Значение параметра
   *
   * @return Db
   */
  public function setDbParam($name, $value)
  {
    $this->_dbParams[$name] = $value;

    return $this;
  }

  /**
   * Метод возвращает указанный параметр подключения к базе данных
   *
   * @param string $name Имя параметра
   *
   * @return mixed
   */
  public function getDbParam($name)
  {
    return $this->_dbParams[$name];
  }

  /**
   * Метод, который выполняет соединение с базой данных
   *
   * @return Db
   *
   * @throws DbException Ошибка работы с базой данных
   */
  public function connect()
  {
    // Если соединение не установлено
    if ( !$this->isConnected() ) {
      Profiler::getInstance()->startCheckpoint('Db', 'Connect to DataBase ');

      // Формирования строки соединения
      $paramsLine = '';
      foreach ($this->_dbParams as $key=>$value) {
        if ('caption' == $key) {
          continue;
        }

        $paramsLine .= $key . '=' . $value . ' ';
      }

      try {
        // Соединение с базой данных
        if ( !$this->_dbAdapter = pg_connect($paramsLine) ) {
          throw new DbException('Db error: failure connect to PostgreSQL database with params "' . $paramsLine . '"', 501);
        }
      } catch (\Exception $e) {
        echo($e->getMessage());
        die();
      }

      Profiler::getInstance()->stopCheckpoint();
    }

    return $this;
  }

  /**
   * Разрыв соединения с базой данных
   *
   * @return Db
   *
   * @throws DbException Ошибка работы с базой данных
   */
  public function disconnect()
  {
    if ($this->_dbAdapter === null) {
      return $this;
    }

    if ( !pg_close($this->_dbAdapter) ) {
      throw new DbException('Db error: failure disconnect to PostgreSQL database', 502);
    }

    $this->_dbAdapter = null;

    return $this;
  }

  /**
   * Метод проверяет установленно соединение или нет
   *
   * @return bool
   */
  public function isConnected()
  {
    if (null == $this->_dbAdapter) {
      return false;
    }

    return true;
  }

  /**
   * Метод создает объект класса Query, с помощью которого формируются запросы
   * к базе данных
   *
   * @return Db\Query
   */
  public function query()
  {
    return new Query($this);
  }

  /**
   * Метод возвращает последнюю ошибку, которую сгенерировал PostgreSQL
   *
   * @return string
   */
  public function getLastError()
  {
    return pg_last_error($this->dbAdapter());
  }

  /**
   * Метод устанавливает язык, который будет использоватся для отбора записей
   * указанного языка в запросах
   *
   * @param string $language
   *
   * @return Db
   */
  public function setLanguage($language) {
    $this->_language = $language;

    return $this;
  }

  /**
   * Метод возвращает язык данных, которые будут возвращаться запросами
   *
   * @return null|string
   */
  public function getLanguage() {
    return $this->_language;
  }

  public function getCalledClass() {
    return $this->_calledClass;
  }
}
