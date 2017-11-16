<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Config, который является общим классом для
 * работы с файлами конфигурации
 *
 * @category  Uwin
 * @package   Uwin\Config
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Config
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Cacher\Interface_ as Cacher;
use \Uwin\Config\Exception  as Exception;

/**
 * Класс, который является общим классом для работы с файлами конфигурации
 *
 * @category  Uwin
 * @package   Uwin\Config
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Config
{
	/**
	 * Использование XML-конфигов
	 */
	const XML  = 'Xml';

	/**
	 * Использование JSON-конфигов
	 */
	const JSON = 'Json';

	/**
	 * Использование YAML-конфигов
	 */
	const YAML = 'Yaml';

	/**
	 * Ссылка на экземпляр класса конфига
	 * @var \Uwin\Config\Interface_
	 */
	private $_config = null;

	/**
	 * Ссылка на экземпляр класса отвечающего за кеширование
	 * @var \Uwin\Cacher\Interface_
	 */
	private $_cacher = null;

	/**
	 * Тег, который прикрепляется ко всем записям в кеше данного конфига
	 * @var string
	 */
	private $_tagCache = null;

	/**
	 * Отметка о том, использовать кеширование или нет
	 * @var bool
	 */
	private $_useCacher = false;

	/**
	 * Формат конфига. Должен быть равен значению одной из констант данного
	 * класса
	 * @var string
	 */
	private $_format = null;

	/**
	 * Наименование класса который используется для работы с конфигурационными
	 * файлами
	 * @var string
	 */
	private $_configClass = null;

	/**
	 * Имя файла, если был открыт файл, иначе содержит NULL
	 * @var string
	 */
	private $_fileName = null;


	/**
	 * Метод возвращаем имя тега, который будет присвоен всем записям в кеше
	 * данного конфига
	 *
	 * @param string|array $config - ОПЦИОНАЛЬНО Путь к файлу конфига или текст конфига или массив, который будет преобразован в конфиг
	 *
	 * @return string
	 */
	private function _buildTagName($config) {
		if ( is_array($config) ) {
			$tagName = md5(serialize($config));
		} else {
			$tagName = md5($config);
		}

		return $tagName;
	}

	/**
	 * Метод возвращает полный путь к текущеу или указанному узлу
	 *
	 * @param string $path = null - Путь к узлу
	 *
	 * @return string
	 */
	private function _getFullPath($path = null) {
		if (isset($path[0]) && '/' != $path[0]) {
			$path = rtrim($this->getPath(), '/') . '/' . $path;
		}

		if ('/' != $path) {
			$path = rtrim($path, '/');
		}

		return $path;
	}

	/**
	 * Метод вызывает указанную функцию с указанными параметрами, предварительно
	 * проверяя нет ли данных в кеше, если нет то по выполнении функции
	 * ложит возвращаемое ею значение в кеш
	 *
	 * @param string       $prefix        - Префикс ключа в кеше
	 * @param string       $name          - Путь к узлу
	 * @param string       $method        - Метод, который вызывается
	 * @param string|array $params = null - Параметры, с которыми вызывается метод
	 *
	 * @return mixed
	 */
	private function _execMethodAndCaching($prefix, $name, $method,
										   $params = null) {
		if (null == $params) {
			$params = array();
		}

		// Если передан один параметр, превращаем его в массив
		if ( !empty($params) && !is_array($params) ) {
			$params = array($name, $params);
		}

		$path = null;
		// Если указано имя узла
		if (null !== $name) {
			// Получаем полный путь к узлу
			$path = $this->_getFullPath($name);

			if (null === $params) {
				// Если параметры не указаны - парметр будет один - путь к узлу
				$params = array($name);
			} else {
				// иначе добавляем парметр пути к узлу первым в массиве параметров
				array_unshift( $params, $name);
			}
		}

		$key = null;
		// Если кеширование используется, проверям нет ли данных в кеше, если
		// есть - возвращаем их
		if ( $this->useCacher() ) {
			$key = $prefix . md5( $this->_tagCache . $path . implode('', $params) );
			$value = $this->_cacher->get($key);

			if (false !== $value) {
				return $value;
			}
		}

		// Иначе вызываем указанную функцию и данные ее кешируем если нужно
		$value = call_user_func_array(array($this->_config, $method), $params);
		if ( $this->useCacher() ) {
			$this->_cacher->set($key, $value, 7200, $this->_tagCache);
		}

		return $value;
	}


	/**
	 * Конструктор класса, устанавливающий формат конфига и указвающий
	 * использовать кеширование или нет
	 *
	 * @param string                       $format = self:XML - Формат конфига
	 * @param null|\Uwin\Cacher\Interface_ $cacher = null     - Ссылка на экземпляр класса конфига
	 *
	 * @return void
	 */
	public function __construct($format = self::XML, Cacher $cacher = null) {
		$this->setFormat($format);

		if ( !empty($cacher) ) {
			$this->setCacher($cacher);
		}
	}

	/**
	 * Метод устанавливает формат конфига (может принимать значение только
	 * констант данного класса)
	 *
	 * @param string $format - Формат конфига
	 *
	 * @return Config
	 * @throws Config\Exception
	 */
	public function setFormat($format) {
		$this->_configClass = $className = '\Uwin\Config\\' . $format;

		if (null != $this->_config) {
			throw new Exception('Config is open, dont use setFormat(). First close config.');
		}

		if ( !class_exists($className) ) {
			throw new Exception('Config class ' . $className . ' not found');
		}

		$this->_format = $format;

		return $this;
	}

	/**
	 * Метод возвращает используемый формат конфига
	 *
	 * @return string
	 */
	public function getFormat() {
		return $this->_format;
	}

	/**
	 * Метод устанавливает объект класса кеширования, также указывает что нужно
	 * использовать кеширование данных конфигов. Если $cacher = null - не
	 * использовать кеширование
	 *
	 * @param \Uwin\Cacher\Interface_ $cacher - Ссылка на экземпляр класса для кеширования
	 *
	 * @return Config
	 */
	public function setCacher(\Uwin\Cacher\Interface_ $cacher) {
		$this->_cacher = $cacher;
		if (null === $cacher) {
			$this->enabledCacher(false);
		} else {
			$this->enabledCacher(true);
		}

		return $this;
	}

	/**
	 * Метод устанавливает отметку о том, нужно кешировать или нет данные
	 * конфигов
	 *
	 * @param bool $enabled
	 *
	 * @return Config
	 */
	public function enabledCacher($enabled) {
		$this->_useCacher = $enabled;

		return $this;
	}

	/**
	 * Метод возвращает признак того, используется кеширование данных конфига
	 * или нет
	 *
	 * @return bool
	 */
	public function useCacher() {
		return $this->_useCacher;
	}

	/**
	 * Конструктор класса, который формирует конфиг с указанного файла или
	 * текстовой переменной или массива и устанавливает текущий узел, который
	 * указан с помощью $path. Если $config = NULL - создается пустой конфиг
	 *
	 * @param array|string $config = null - ОПЦИОНАЛЬНО Путь к файлу конфига или текст конфига или массив, который будет преобразован в конфиг
	 * @param string       $path   = null - ОПЦИОНАЛЬНО Путь к узлу, который будет сделан текущим
	 *
	 * @return Config
	 */
	public function open($config = null, $path = null) {
		$this->_config = new $this->_configClass($config, $path);

		$this->_fileName = null;
		// Если указано имя открываемого файла конфига, сохраняем его в свойстве
		// класса
		if ( !is_array($config) && file_exists($config) ) {
			$this->_fileName = $config;
		}
		$this->_tagCache = $this->_buildTagName($config);

		return $this;
	}

	/**
	 * Метод закрывает открытый конфиг
	 *
	 * @return Config
	 */
	public function close() {
		$this->_config = $this->_configClass
			= $this->_tagCache = $this->_fileName = null;

		return $this;
	}

	/**
	 * Метод устанавливает путь к узлу, который будет текущим
	 *
	 * @param string $path - Путь к узлу
	 *
	 * @return Config
	 */
	public function setPath($path) {
		$this->_config->setPath($path);

		return $this;
	}

	/**
	 * Метод возвращает путь к текущему узлу
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->_config->getPath();
	}

	/**
	 * Метод возвращает количество подузлов у текущего или указанного узла
	 *
	 * @param string $name = null - ОПЦИОНАЛЬНО Имя узла
	 *
	 * @return int
	 */
	public function count($name = null) {
		return $this->_execMethodAndCaching('cnt_', $name, 'count');
	}

	/**
	 * Метод возвращает признак того, существует указанный узел или нет,а также
	 * может возвращать признак того, существует указанный атрибут или нет
	 *
	 * @param string $name             - Имя узла
	 * @param string $attribute = null - ОПЦИОНАЛЬНО Имя атрибута
	 *
	 * @return bool
	 */
	public function exists($name, $attribute = null) {
		return $this->_execMethodAndCaching('exists_', $name, 'exists', $attribute);
	}

	/**
	 * Метод устанавливает значение указанного узла, если узел не найден,
	 * вызывает исключение
	 *
	 * @param string $name  - Имя узла
	 * @param mixed  $value - Значение узла
	 *
	 * @return Config
	 */
	public function set($name, $value) {
		$this->_config->set($name, $value);

		return $this;
	}

	/**
	 * Метод возвращает значение указанного узла со всеми его под-узлами, и если
	 * указано, то и с значениеми атрибутов
	 *
	 * @param string name             - Имя узла
	 * @param bool $with_attr = false - ОПЦИОНАЛЬНО Возвращать значения атрибутов ли нет
	 *
	 * @return array
	 */
	public function get($name = null, $with_attr = false) {
		return $this->_execMethodAndCaching('val_', $name, 'get', $with_attr);
	}

	/**
	 * Метод добавляет узел или массив узлов
	 *
	 * @param string       $path         - Путь к узлу
	 * @param string|array $name         - Имя узла
	 * @param mixed        $value = null - ОПЦИОНАЛЬНО Значение узла
	 *
	 * @return Config
	 */
	public function add($path, $name, $value = null) {
		$this->_config->add($path, $name, $value);

		return $this;
	}

	/**
	 * Метод удаляет указанный узел, или если указанно несколько узлов, то
	 * удаляет их все
	 *
	 * @param string $name - Имя узла
	 *
	 * @return Config
	 */
	public function del($name) {
		$this->_config->del($name);

		return $this;
	}

	/**
	 * Метод добавляет или изменяет аттрибут или массив аттрибутов в указанный
	 * узел
	 *
	 * @param string       $name         - Путь к узлу
	 * @param string|array $attr         - Имя аттрибута
	 * @param mixed        $value = null - ОПЦИОНАЛЬНО Значение аттрибута, если не указано, значит добавляется массив аттрибутов
	 *
	 * @return Config
	 */
	public function setAttr($name, $attr, $value = null) {
		$this->_config->setAttr($name, $attr, $value);

		return $this;
	}

	/**
	 * Метод возвращает значение атрибута указанного узла или массив аттрибутов
	 * с их значениями
	 *
	 * @param string $name      = null - ОПЦИОНАЛЬНО Имя узла
	 * @param strung $attribute = null - ОПЦИОНАЛЬНО Имя аттрибута
	 *
	 * @return array|mixed
	 */
	public function getAttr($name = null, $attribute = null) {
		return $this->_execMethodAndCaching('val_attr_', $name, 'getAttr',
											$attribute);
	}

	/**
	 * Метод удаляет указанный аттрибут или все атрибуты у указанного узла
	 *
	 * @param string $name             - Имя узла
	 * @param strung $attribute = null - ОПЦИОНАЛЬНО Имя аттрибута
	 *
	 * @return Config
	 */
	public function delAttr($name, $attribute = null) {
		$this->_config->delAttr($name, $attribute);

		return $this;
	}

	/**
	 * Метод возвращает сформированный конфиг
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->_execMethodAndCaching('cnt_', null, 'getContent');
	}

	//TODO Когда тоявятся другие форматы конфигов, сделать функцию конвертирования с текущего формата в указанный

	/**
	 * Метод сохраняет конфиг в указанный файл
	 *
	 * @param string $filename = null - Имя файла
	 *
	 * @return Config
	 */
	public function save($filename = null) {
		$tagName = $this->_buildTagName($filename);
		if ( $this->useCacher() ) {
			$this->_cacher->tagsVersions($tagName, true);
		}

		if ( empty($filename) ) {
			$filename = $this->_fileName;
		}

		return $this->_config->save($filename);
	}
}
