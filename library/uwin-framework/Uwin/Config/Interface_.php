<?php
/**
 * Uwin Framework
 *
 * Файл содержащий интерфейс Uwin\Config\Interface_
 *
 * @category  Uwin
 * @package   Uwin\Config
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Config, к которому относится интерфейс
 * конфигов
 */
namespace Uwin\Config;


/**
 * Интерфейс, описывающий методы для работы с файлами конфигов
 *
 * @category  Uwin
 * @package   Uwin\Config
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
interface Interface_ {
	/**
	 * Конструктор класса, который формирует конфиг с указанного файла или
	 * текстовой переменной или массива и устанавливает текущий узел, который
	 * указан с помощью $path. Если $config = NULL - создается пустой конфиг
	 *
	 * @param array|string $config = null - ОПЦИОНАЛЬНО Путь к файлу конфига или текст конфига или массив, который будет преобразован в конфиг
	 * @param string       $path   = null - ОПЦИОНАЛЬНО Путь к узлу, который будет сделан текущим
	 *
	 * @return Interface_
	 */
	public function __construct($config = null, $path = null);

	/**
	 * Метод устанавливает путь к узлу, который будет текущим
	 *
	 * @param string $path - Путь к узлу
	 *
	 * @return Interface_
	 */
	public function setPath($path);

	/**
	 * Метод возвращает путь к текущему узлу
	 *
	 * @return string
	 */
	public function getPath();

	/**
	 * Метод возвращает количество подузлов у текущего или указанного узла
	 *
	 * @param string $name = null - ОПЦИОНАЛЬНО Имя узла
	 *
	 * @return int
	 */
	public function count($name = null);

	/**
	 * Метод возвращает признак того, существует указанный узел или нет,а также
	 * может возвращать признак того, существует указанный атрибут или нет
	 *
	 * @param string $name             - Имя узла
	 * @param string $attribute = null - ОПЦИОНАЛЬНО Имя атрибута
	 *
	 * @return bool
	 */
	public function exists($name, $attribute = null);

	/**
	 * Метод устанавливает значение указанного узла, если узел не найден,
	 * вызывает исключение
	 *
	 * @param string $name  - Имя узла
	 * @param mixed  $value - Значение узла
	 *
	 * @return Interface_
	 */
	public function set($name, $value);

	/**
	 * Метод возвращает значение указанного узла со всеми его под-узлами, и если
	 * указано, то и с значениеми атрибутов
	 *
	 * @param null $name              - Имя узла
	 * @param bool $with_attr = false - ОПЦИОНАЛЬНО Возвращать значения атрибутов ли нет
	 *
	 * @return array
	 */
	public function get($name = null, $with_attr = false);

	/**
	 * Метод добавляет узел или массив узлов
	 *
	 * @param string       $path         - Путь к узлу
	 * @param string|array $name         - Имя узла
	 * @param mixed        $value = null - ОПЦИОНАЛЬНО Значение узла
	 *
	 * @return Interface_
	 */
	public function add($path, $name, $value = null);

	/**
	 * Метод удаляет указанный узел, или если указанно несколько узлов, то
	 * удаляет их все
	 *
	 * @param string $name - Имя узла
	 *
	 * @return Interface_
	 */
	public function del($name);

	/**
	 * Метод добавляет или изменяет аттрибут или массив аттрибутов в указанный
	 * узел
	 *
	 * @param string       $name         - Путь к узлу
	 * @param string|array $attr         - Имя аттрибута
	 * @param mixed        $value = null - ОПЦИОНАЛЬНО Значение аттрибута, если не указано, значит добавляется массив аттрибутов
	 *
	 * @return Interface_
	 */
	public function setAttr($name, $attr, $value = null);

	/**
	 * Метод возвращает значение атрибута указанного узла или массив аттрибутов
	 * с их значениями
	 *
	 * @param string $name      = null - ОПЦИОНАЛЬНО Имя узла
	 * @param strung $attribute = null - ОПЦИОНАЛЬНО Имя аттрибута
	 *
	 * @return array|mixed
	 */
	public function getAttr($name = null, $attribute = null);

	/**
	 * Метод удаляет указанный аттрибут или все атрибуты у указанного узла
	 *
	 * @param string $name             - Имя узла
	 * @param strung $attribute = null - ОПЦИОНАЛЬНО Имя аттрибута
	 *
	 * @return Interface_
	 */
	public function delAttr($name, $attribute = null);

	/**
	 * Метод сохраняет конфиг в указанный файл
	 *
	 * @param string $filename - Имя файла
	 *
	 * @return Interface_
	 */
	public function save($filename);

	/**
	 * Метод возвращает сформированный конфиг
	 *
	 * @return string
	 */
	public function getContent();
}
