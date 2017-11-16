<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Session, который отвечает за работу с сессиями
 *
 * @category  Uwin
 * @package   Uwin\Session
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Session
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Session\Exception as SessionException;

/**
 * Класс, который отвечает за работу с сессиями
 *
 * @category  Uwin
 * @package   Uwin\Session
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Session
{
	/**
	 * Признак того, стартовала сессия или нет
	 * @var bool
	 */
	private static $_started = false;

	/**
	 * Признак того, уничтожена сессия или нет
	 * @var bool
	 */
	private static $_destroyed = false;

	/**
	 * Имя текущего пространства имен в сессии
	 * @var string
	 */
	private $_namespace = 'default';

	/**
	 * Метод удаляет куки, связанные с данной сессией
	 *
	 * @throws Uwin\Session\Exception Ошибка работы с сессиями
	 * @return bool
	 */
	private static function deleteSessionCookie()
	{
		if ( isset($_COOKIE[session_name()]) ) {
			$cookie_params = session_get_cookie_params();

			if (! setcookie(
					session_name(),
					'',
					315554400, // strtotime('1980-01-01'),
					$cookie_params['path'],
					$cookie_params['domain'],
					$cookie_params['secure']
				) ) {
				throw new SessionException('Session error: failure delete cookies session', 404);
			}
		}

		return true;
	}

	/**
	 * Статический метод, который стартует сессию, если она не была до этого
	 * запущена
	 *
	 * @throws Uwin\Session\Exception Ошибка работы с сессиями
	 * @return bool
	 */
	public static function start()
	{
		if (self::$_started) {
			return true;
		}

		Profiler::getInstance()->startCheckpoint('Session', 'Start session');

		if ( !session_start() ) {
			throw new SessionException('Session error: failure start session', 401);
		}
		self::$_started = true;

		Profiler::getInstance()->stopCheckpoint();

		return true;
	}

	/**
	 * Метод который уничтожает все переменные (и все пространства имен),
	 * которые были в сессии
	 *
	 * @return bool
	 */
	public static function unsetSession()
	{
		unset($_SESSION);

		return true;
	}

	/**
	 * Метод, который уничтожает сессию, и если указано удаляет и куки связанные
	 * с ней
	 *
	 * @param bool $removeCookie Удалять куки или нет
	 * @throws Uwin\Session\Exception Ошибка работы с сессиями
	 * @return bool
	 */
	public static function destroy($removeCookie = true)
	{
		// Если сессия уничтожена - возвращает null
		if (self::$_destroyed) {
			return null;
		}

		Profiler::getInstance()->startCheckpoint('Session', 'Destroy session');

		//Уничтожение сессии
		self::unsetSession();
		if ( !session_destroy() ) {
			throw new SessionException('Session error: failure destroy session', 402);
		}
		self::$_destroyed = true;

		// Если нужно удаляем куки, связанные с сессией
		if (true == $removeCookie) {
			self::deleteSessionCookie();
		}

		Profiler::getInstance()->stopCheckpoint();

		return true;
	}

	/**
	 * Метод возвращает признак запуска сессии
	 *
	 * @return bool
	 */
	public static function isStarted()
	{
		return self::$_started;
	}

	/**
	 * Метод возвращает признак уничтожения сессии
	 *
	 * @return bool
	 */
	public static function isDestroyed()
	{
		return self::$_destroyed;
	}

	/**
	 * Метод устанавливает новый идентификатор сессии
	 *
	 * @param int $id Идентификатор чессии
	 * @return bool
	 */
	public static function setId($id)
	{
		if ( !is_string($id) || '' === $id ) {
			return false;
		}

		$id = (int)$id;

		session_id($id);

		return true;
	}

	/**
	 * Метод возвращает идентификатор сессии
	 *
	 * @return int
	 */
	public static function getId()
	{
		return session_id();
	}

	/**
	 * Метод, генерирует новый идентификатор сессии и устанавливает его
	 *
	 * @throws Uwin\Session\Exception Ошибка работы с сессиями
	 * @return bool
	 */
	public static function regenerateId()
	{
		if ( !session_regenerate_id(true) ) {
			throw new SessionException('Session error: failure regenerate session id', 403);
		}

		return true;
	}

	/**
	 * Конструктор класса, в котором указывается пространство имен, к которому
	 * будет относится данный объект класса
	 *
	 * @param string $namespace Имя пространства имен
	 * @return \Uwin\Session
	 */
	public function __construct($namespace = 'default')
	{
		$this->_namespace = $namespace;

		return $this;
	}

	/**
	 * Магический метод, который позволяет создавать переменные в объекте данного
	 * класса, как его свойства и устанавливаеть им значение. Сами переменные
	 * находятся в сессии, в пространстве имен данного класса
	 *
	 * @param string $name Имя переменной
	 * @param mixed $value Значение переменной
	 * @return Uwin\Session
	 */
	public function __set($name, $value)
	{
		$_SESSION[$this->_namespace][$name] = $value;

		return $this;
	}

	/**
	 * Магический метод, который позволяет считать переменные в объекте данного
	 * класса, как его свойства. Сами переменные находятся в сессии, в
	 * пространстве имен данного класса
	 *
	 * @param string $name Имя переменной
	 * @return mixed
	 */
	public function __get($name)
	{
		if ( isset($_SESSION[$this->_namespace][$name]) ) {
			return $_SESSION[$this->_namespace][$name];
		}

		return null;
	}

	/**
	 * Магический метод, который проверяет существует ли указанная переменная
	 * в сесси в пространстве имен данного класса
	 *
	 * @param string $name Имя переменной
	 * @return bool
	 */
	public function __isset($name)
	{
		if ( !isset($_SESSION[$this->_namespace][$name]) ) {
			return false;
		}

		return true;
	}

	/**
	 * Магический метод, который позволяет уничтожить указанную переменную
	 * в сесси в пространстве имен данного класса
	 *
	 * @param string $name Имя переменной
	 * @return bool
	 */
	public function __unset($name)
	{
		unset($_SESSION[$this->$_namespace][$name] );

		return true;
	}

	/**
	 * Метод возвращает все переменные сессии в указанном пространстве имен, или
	 * в текущем пространстве имен
	 *
	 * @param string $namespace Пространство имен
	 * @return array
	 */
	public function getParams($namespace = null)
	{
		if (null == $namespace) {
			$namespace = $this->_namespace;
		}

		$params = $_SESSION[$namespace];

		return $params;
	}

	/**
	 * Метод устанавливает пространство имен для данного объекта класса
	 *
	 * @param string $namespace Имя пространства имен
	 * @return Uwin\Session
	 */
	public function setNamespace($namespace)
	{
		$this->_namespace = $namespace;

		return $this;
	}

	/**
	 * Метод возвращает пространство имен, которое используется вданном объекте
	 * класса
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->_namespace;
	}

	/**
	 * Метод проверяет существует ли указанное пространство имен в сессии
	 *
	 * @param string $namespace Имя пространстова имен
	 * @return bool
	 */
	public function issetNamespace($namespace)
	{
		if ( !isset($_SESSION[$namespace]) ) {
			return false;
		}

		return true;
	}

	/**
	 * Метод уничтожает указанное пространство имен в сессии
	 *
	 * @param string $namespace Имя пространстова имен
	 * @return bool
	 */
	public function unsetNamespace($namespace = null)
	{
		if ( null === $namespace ) {
			$namespace = $this->_namespace;
		}

		unset($_SESSION[$namespace]);

		return true;
	}

	/**
	 * Метод устанавливает массив переменных в сесси, в пространстве имен
	 * данного объекта класса
	 *
	 * @param array $variables Массив переменные
	 * @return bool
	 */
	public function write(array $variables)
	{
		self::start();

		Profiler::getInstance()->startCheckpoint('Session', 'Write variables');

		foreach ($variables as $name=>$value) {
			$_SESSION[$this->_namespace][$name] = $value;
		}

		Profiler::getInstance()->stopCheckpoint();

		return true;
	}
}