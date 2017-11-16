<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Auth, который отвечает за аутентификацию пользователей
 *
 * @category  Uwin
 * @package   Uwin\Auth
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2013 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Auth
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Auth\Exception     as AuthException;
use \Uwin\Controller\Request as Request;

/**
 * Класс, который отвечает за аутентификацию пользователей
 *
 * @category  Uwin
 * @package   Uwin\Auth
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 */
class Auth
{
  /**
   * Личность не была указана
   */
  const FAILURE_IDENTITY_EMPTY = 101;

  /**
   * Пароль не был указан
   */
  const FAILURE_PASSWORD_EMPTY = 102;

  /**
   * Личность не была найдена
   */
  const FAILURE_IDENTITY_NOT_FOUND = 103;

  /**
   * Пространство имен, которое используется в сессии по умолчанию
   */
  const NAMESPACE_DEFAULT = 'UwinAuth';

  /**
   * Ссылка на экзепляр класса
   * @var Auth
   */
  private static $_instance = null;

  /**
   * Ссылка на экземпляр класса базы данных. С помощью этого класса происходит
   * вся работа с базой данных
   * @var Db
   */
  private $_db = null;

  /**
   * Ссылка на экземпляр класса Uwin\Session, с помощью которой производится
   * работа с сессией
   * @var Session
   */
  private $_storage = null;

  private $_use_crypt_password = false;

  /**
   * Пространство имен, которое используется в сессии
   * @var string
   */
  private $_namespace = self::NAMESPACE_DEFAULT;

  /**
   * Имя таблицы, где расположены пользователи
   * @var string
   */
  private $_tableName = null;

  /**
   * Имя столбца в таблице, в котором сохранено имя пользователя
   * @var string
   */
  private $_identityColumn = null;

  /**
   * Имя столбца в таблице, в котором сохранен пароль пользователя
   * @var string
   */
  private $_passwordColumn = null;

  /**
   * Имя столбца в таблице, где сохраняется "соль", которая используется для
   * более сложной зашифровки пароля
   * @var string
   */
  private $_saltColumn = null;

  /**
   * Имя пользователя, переданное при аутентификации
   * @var string
   */
  private $_identity = null;

  /**
   * Пароль пользователя, переданный при аутентификации
   * @var string
   */
  private $_password = null;

  /**
   * Приватный конструктор класса, так как класс реализует паттерн Singlton.
   * При создании класса создается объект класса Uwin\Session который является
   * хранилищем переменных в сессии
   *
   * @return bool
   */
  private function __construct()
  {
    $this->setStorage();

    return true;
  }

  /**
   * Приватный магический метод класса, который запрещает клонировать объекты
   * этого класса, так как класс реализует паттерн Singlton.

   * @return void
   */
  private function __clone() {}


  /**
   * Метод возвращает ссылку на объект класса Uwin\Auth
   *
   * @return Auth
   */
  public static function getInstance()
  {
    if ( empty(self::$_instance) )  {
      self::$_instance = new self;
    }

    return self::$_instance;
  }

  /**
   * Метод устанавлтвает экземпляр класса базы данных
   *
   * @param Db $db Экземпляр класса базы данных
   * @return Auth
   */
  public function setDb(Db $db)
  {
    $this->_db = $db;

    return $this;
  }

  /**
   * Метод возвращает экземпляр класса базы данных
   *
   * @return Db
   */
  public function getDb()
  {
    return $this->_db;
  }

  public function useCryptPassword() {
    $this->_use_crypt_password = true;

    return $this;
  }

  /**
   * Метод устанавдивает хранилище, где будут сохранятся параметры
   * авторизованного пользователя
   *
   * @param Session $storage Объект класса Uwin\Session, который используется для работы с сессией
   *
   * @return Auth
   */
  public function setStorage(Session $storage = null)
  {
    if (null === $storage) {
      $this->_storage = new Session( $this->_namespace );
    } else {
      $this->_storage = $storage;
    }

    return $this;
  }

  /**
   * Метод возвращает хранилище, где будут сохранятся параметры
   * авторизованного пользователя
   *
   * @return Session
   */
  public function getStorage()
  {
    return $this->_storage;
  }

  /**
   * Метод устанавливает пространство имен, которое используется в хранилище
   * (в сессии)
   *
   * @param string $namespace Пространство имен
   * @return Auth
   */
  public function setStorageNamespace($namespace)
  {
    $this->_namespace = $namespace;
    $this->_storage->setNamespace($namespace);

    return $this;
  }

  /**
   * Метод возвращает пространство имен, которое используется в хранилище (в
   * сессии)
   *
   * @return string
   */
  public function getStorageNamespace()
  {
    return $this->_namespace;
  }

  /**
   * Метод устанавливает имя таблицы, где хранится информаци о пользователях
   *
   * @param string $name Имя таблицы
   * @return Auth
   */
  public function setTableName($name)
  {
    $this->_tableName = $name;

    return $this;
  }

  /**
   * Метод возвращает имя таблицы, где хранится информаци о пользователях
   *
   * @return string
   */
  public function getTableName()
  {
    return $this->_tableName;
  }

  /**
   * Метод устанавливает имя столбца в таблице, где хранится имя пользователя
   *
   * @param string $name Имя столбца в таблице, где хранится имя пользователя
   * @return Auth
   */
  public function setIdentityColumn($name)
  {
    $this->_identityColumn = $name;

    return $this;
  }

  /**
   * Метод возвращает имя столбца в таблице, где хранится имя пользователя
   *
   * @return string
   */
  public function getIdentityColumn()
  {
    return $this->_identityColumn;
  }

  /**
   * Метод устанавливает имя столбца в таблице, где хранится пароль
   * пользователя
   *
   * @param string $name Имя столбца в таблице, где хранится пароль пользователя
   * @return Auth
   */
  public function setPasswordColumn($name)
  {
    $this->_passwordColumn = $name;

    return $this;
  }

  /**
   * Метод возвращает имя столбца в таблице, где хранится пароль пользователя
   *
   * @return string
   */
  public function getPasswordColumn()
  {
    return $this->_passwordColumn;
  }

  /**
   * Метод устанавливает имя столбца в таблице, где хранится "соль", которая
   * используется для более сложной зашифровки пароля
   *
   * @param string $name Соль, которя используется для более сложной шифровки пароля
   * @return Auth
   */
  public function setSaltColumn($name)
  {
    $this->_saltColumn = $name;

    return $this;
  }

  /**
   * Метод возвращает имя столбца в таблице, где хранится "соль", которая
   * используется для более сложной зашифровки пароля
   *
   * @return string
   */
  public function getSaltColumn()
  {
    return $this->_saltColumn;
  }

  /**
   * Метод устанавливает логин пользователя, переданный для аутентификации
   *
   * @param string $identity Логин пользователя
   * @return Auth
   */
  public function setIdentity($identity)
  {
    $this->_identity = $identity;

    return $this;
  }

  /**
   * Метод возвращает логин пользователя, переданный для аутентификации
   *
   * @return string
   */
  public function getIdentity()
  {
    return $this->_identity;
  }

  /**
   * Метод устанавливает пароль пользователя, переданный для аутентификации
   *
   * @param string $password Пароль пользователя
   * @return Auth
   */
  public function setPassword($password)
  {
    $this->_password = $password;

    return $this;
  }

  /**
   * Метод, выполняющий аутентификацию
   *
   * @throws Auth\Exception Ошибка аутентификации
   * @return bool
   */
  public function authenticate()
  {
    Profiler::getInstance()->startCheckpoint('Auth', 'Authenticate user ' . $this->_identity);

    // Если не указан логин, возвращаем код ошибки "логин не указан"
    if ( empty($this->_identity) ) {
      throw new AuthException('Auth error: failure identity empty', self::FAILURE_IDENTITY_EMPTY);
    }

    // Если не указан пароль, возвращаем код ошибки "пароль не указан"
    // if ( empty($this->_password) and $this->_password !== false) {
    //   throw new AuthException('Auth error: failure password empty', self::FAILURE_PASSWORD_EMPTY);
    // }

    // Создаем объект Query, устанавливаем sql запрос, передаем для него
    // параметры, выполняем и получаем строку
    $select = $this->getDb()->query()
      ->addSql('select ' . $this->_identityColumn)
      ->addSql('from ' . $this->_tableName)
      ->addSql('where ' . $this->_identityColumn . '=$1')
      ->addSql('and ' . $this->_passwordColumn, $this->_password);
    if ($this->_use_crypt_password) {
      $select->addSql('=$2', $this->_password);
    } else {
      $select->addSql('=md5(md5($2)||' . $this->_saltColumn . ')', $this->_password);
    }
    $select->addSql('limit 1')
      ->addParam($this->_identity)
      ->addParam($this->_password, $this->_password);
    $selectResult = $select->fetchRow(0, false);

    // Если запрос не вернул ниодной строки, возвращаем код ошибки
    // "пользователь не найден"
    if (null == $selectResult) {
      throw new AuthException('Auth error: failure identity "' . $this->_identity . '" not found', self::FAILURE_IDENTITY_NOT_FOUND);
    }

    // Если все проверки прошли успешно и пользователь найден - стартуем
    // сессию, меняем ей идентификатор
    Session::start();
    // Session::regenerateId();

    // Устанавливаем имя пользователя и информацию о его браузере в хранилище
    $this->getStorage()->identity  = $selectResult[$this->_identityColumn];
    $this->getStorage()->userAgent = md5( Request::getInstance()->getServerVariable('HTTP_USER_AGENT') );

    Profiler::getInstance()->stopCheckpoint();

    return true;
  }

  /**
   * Метод проверяет, был ли аутентификцырован пользователь
   *
   * @return bool
   */
  public function hasIdentity()
  {
    // Стартуем сессию, если она не была запущена до этого
    Session::start();

    // Если в сессии нет логина пользователя или информации о браузере
    // пользователя или информация о браузере пользователя различна
    // выбрасываем исключение
    if (
      (!isset($this->getStorage()->identity)) ||
      (!isset($this->getStorage()->userAgent)) ||
      ( md5( Request::getInstance()->getServerVariable('HTTP_USER_AGENT') ) != $this->getStorage()->userAgent )
    ) {

      return false;
    }

    return true;
  }

  /**
   * Метод, который очищает всю информацию в сессии(хранилище) в текущем
   * пространстве имен
   *
   * @return bool
   */
  public function clearIdentity()
  {
    // Стартуем сессию, если она не была запущена до этого
    Session::start();

    // Уничтожаем все переменные которые были в хранилище в указанном
    // пространстве имен
    $this->getStorage()->unsetNamespace();

    return true;
  }

    public function validate(){
    // Создаем объект Query, устанавливаем sql запрос, передаем для него
    // параметры, выполняем и получаем строку
    $select = $this->getDb()->query()
      ->addSql('select #1 from #2')
      ->addSql('where #3=$1 and #4')
            ->addReplacement($this->_identityColumn)
            ->addReplacement($this->_tableName)
            ->addReplacement($this->_identityColumn)
            ->addReplacement($this->_passwordColumn);
    if ($this->_use_crypt_password) {
      $select->addSql('=$2');
    } else {
      $select->addSql('=md5(md5($2)||#5)')
                ->addReplacement($this->_saltColumn);
    }
    $select->addSql('limit 1')
      ->addParam($this->_identity)
      ->addParam($this->_password);
    $result = $select->fetchField(0, false);

        if ( empty($result) ) {
            return false;
        }

        return true;
    }
}
