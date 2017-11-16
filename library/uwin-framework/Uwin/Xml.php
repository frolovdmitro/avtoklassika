<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Xml, который отвечает за работу с XML файлами
 * конфигурации
 *
 * @category  Uwin
 * @package   Uwin\Xml
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Xml
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \SimpleXMLElement   as SimpleXMLElement;
use \DOMDocument        as DOMDocument;
use \Uwin\Xml\Exception as XmlException;

/**
 * Класс, который отвечает за работу с XML файлами конфигурации
 *
 * @category  Uwin
 * @package   Uwin\Xml
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Xml
{
	/**
	 * Путь к xml файлу конфигурации
	 * @var string
	 */
	private $_fileSettings = null;

	/**
	 * Массив, состоящий из частей пути к текущей ветке xml
	 * @var array
	 */
	private $_pathNode = array();

	/**
	 * Ссылка на корневой элемент xml файла
	 * @var SimpleXMLElement
	 */
	private $_rootNode = null;

	/**
	 * Ссылка на текущий элемент xml файла
	 * @var SimpleXMLElement
	 */
	private $_currentNode  = null;

	/**
	 * Возвращает все элементы с их значениями в указанном узле xml дерева в
	 * виде ассоциативного массива
	 *
	 * @param SimpleXMLElement $node Элемент в xml дереве
	 * @return array
	 */
	private function _getNode(SimpleXMLElement $node, $asList = false)
	{
		// Создаем пустой массив
		$arrayNodes = array();

		// В цикле проходим по всем потомкам переданного функции элемента xml дерева
		foreach ($node->children() as $key => $value) {
			// Если текущий потомок, в свою очередь также содержит потомков
			if ( 0 !== $value->count() ) {
				// Создаем элемент в массиве, а для получения значений, вызываем
				// эту же функцию рекурсивно
				if (!$asList) {
					$arrayNodes[$key] = $this->_getNode($value);
				} else {
					$arrayNodes[][$key] = $this->_getNode($value, true);
				}
			} else { // иначе
				// Создаем элемент в массиве и присваиваем ему значениев
				$arrayNodes[$key] = (string)$value;
			}
		}

		// Возвращаем сформированный массив элементов в указанном узле xml дерева
		return $arrayNodes;
	}

	/**
	 * Устанавливает свойства и их значения, переданные в виде массива, в
	 * текущий узел xml дерева, если узел не указан, иначе в указанный узел.
	 * Если свойство существует - изменяет его значение, если нет, создает
	 * это свойство и присваивает ему значение
	 *
	 * @param array $values Массив свойств=>значений
	 * @param SimpleXMLElement|null $node Узел в xml дереве, куда нужно установить свойства
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return bool
	 */
	private function _setValuesByNode($values, $node = null)
	{
		// Если узел xml дерева не указан
		if (null == $node) {
			// Если не установлен текущий узел xml дерева (тоесть, если
			// xml-файл не открыт), возвращаем false и выходим с функции
			if ( false == $this->_fileSettings) {
				throw new XmlException('Xml error: not open xml file', 1101);
			}

			// Устанавливаем текущий узел xml-дерева
			$node = $this->_currentNode;
		}

		// Проходим в цикле по всем элементам переданного массива
		foreach ($values as $key => $value) {
			// Если текущий элемент массива, также является массивом
			if ( is_array($value) ) {
				// Если текущего элемента нет в xml-дереве
				if ( empty($node->$key) ) {
					// Добавляем текущий элемент в xml-дерево и вызываем
					// функцию рекурсивно, чтобы создать вложенные подэлементы
					$this->_setValuesByNode($value, $node->addChild($key));
				} else {
					// Вызываем функцию рекурсивно, чтобы создать вложенные подэлементы
					$this->_setValuesByNode($value, $node->$key);
				}
			} else {
				// Если текущего элемента нет в xml-дереве
				if ( empty($node->$key) ) {
					// Добавляем текущий элемент в xml-дерево
					$node->addChild($key, $value);
				} else {
					// Изменяем занчение текущего элемента, найденного в xml-дереве
					$node->$key = $value;
				}
			}
		}

		return true;
	}

	/**
	 * Считывает xml-дерево с файла, и устанавливает корневой и текущий элемент
	 * в начале xml-дерева
	 *
	 * @param string $file Путь к xml-файлу
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return Uwin\Xml
	 */
	public function setFileSettings($file)
	{
		Profiler::getInstance()->startCheckpoint('file', 'open file ' . $file);

		// Если файл не сущетвует выходим с функции
		if ( !file_exists($file) ) {
			$this->_fileSettings = null;
			$this->_rootNode = $this->_currentNode = null;

			throw new XmlException('Xml error: xml file "' . $file . '" not found', 1102);
		}

		// Открываем xml-дерево, и присваиваем корневой и текущий узел
    // соответствующим переменным
    $feed = file_get_contents($file);
		$this->_rootNode = $this->_currentNode = simplexml_load_string($feed);

		// Присваиваем путь к xml-файлу соответствуйщей переменной
		$this->_fileSettings = $file;

		Profiler::getInstance()->stopCheckpoint();

		return $this;
	}

	/**
	 * Метод возвращает полное имя открытого xml-файла
	 *
	 * @return string
	 */
	public function getFileSettings()
	{
		return $this->_fileSettings;
	}

	/**
	 * Устанавливает текущий узел в xml-дереве на основе переданного методу пути,
	 * и сохраняет в соответствующую переменную класса части этого пути.
	 * Путь предается в виде "/root/node/children", тоесть разделителем является
	 * символ "/", если вначале пути указан разделитель, то путь считается от
	 * корня, иначе от текущего узла дерева
	 *
	 * @param string $path путь к узлу в xml-дереве
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return Uwin\Xml
	 */
	public function setPathNode($path)
	{
		// FIXME Ошибка в формировании массива частей пути, если используется относительное представление пути

		// Если не установлен текущий узел xml дерева (тоесть, если
		// xml-файл не открыт), возвращаем false и выходим с функции
		if ( null === $this->_rootNode ) {
			throw new XmlException('Xml error: not open xml file', 1101);
		}

		// Если первые символ пути - разделитель
		if ( '/' == $path[0]) {
			// Устанавливем начальный узел xml-дерева, от которого строится
			// путь - в корень
			$node = $this->_rootNode;
		} else {
			// Устанавливем начальный узел xml-дерева, от которого строится
			// путь - в текущий узел xml-дерева
			$node = $this->_currentNode;
		}

		// Формируем массив составных частей пути
		$path = trim($path, '/\\');
		if ( !empty($path) ) {
			$this->_pathNode = explode('/', $path);
		}

		// Устанавливаем текущий узел в xml-дереве в соответствии с переданным путем
		foreach ($this->_pathNode as $value) {
			if ( !array_key_exists($value, $node) ) {
				throw new XmlException('Xml error: path node ' . $path . ' not fount', 1104);
			}

			$node = $node->$value;
		}
		$this->_currentNode = $node;

		return $this;
	}

	/**
	 * Возвращает массив составных частей пути к текущему узлу xml-дерева
	 *
	 * @return array
	 */
	public function getPathNode()
	{
		return $this->_pathNode;
	}

	public function existsPathNode($path)
	{
	}

	public function createXml($parent_node)
	{
		$this->_rootNode = $this->_currentNode = simplexml_load_string("<?xml version='1.0' encoding=\"utf-8\" ?><" . $parent_node . "></" . $parent_node . ">");
//var_dump($this->_currentNode);
		return $this;
	}

	/**
	 * Возвращает все элементы с их значениями в текущем узле xml дерева в
	 * виде ассоциативного массива
	 *
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return array
	 */
	public function getValues()
	{
		Profiler::getInstance()->startCheckpoint('File', 'Get values in file ' . $this->getFileSettings());

		if ( empty($this->_fileSettings) ) {
			throw new XmlException('Xml error: not open xml file', 1101);
		}

		$values = $this->_getNode($this->_currentNode);

		Profiler::getInstance()->stopCheckpoint();

		return $values;
	}

	/**
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return array
	 */
	public function getValuesList()
	{
		Profiler::getInstance()->startCheckpoint('File', 'Get values list in file ' . $this->getFileSettings());

		if ( empty($this->_fileSettings) ) {
			throw new XmlException('Xml error: not open xml file', 1101);
		}

		$values = $this->_getNode($this->_currentNode, true);

		Profiler::getInstance()->stopCheckpoint();

		return $values;
	}

	/**
	 * Устанавливает свойства и их значения, переданные в виде массива, в
	 * текущий узел xml дерева. Если свойство существует - изменяет его значение,
	 * если нет, создает это свойство и присваивает ему значение
	 *
	 * @param array $values Массив свойств=>значений
	 * @return Uwin\Xml
	 */
	public function setValues($values)
	{
		$this->_setValuesByNode($values);

		return $this;
	}

	/**
	 * Устанавливает свойство и его значения в текущем узле xml дерева
	 *
	 * @param string $name Имя свойства
	 * @param mixed $value Значение свойства
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return Uwin\Xml
	 */
	public function setValue($name, $value = null)
	{
		if ( null ===$this->_currentNode ) {
			throw new XmlException('Xml error: not open xml file', 1101);
		}

		$this->_currentNode->$name = $value;

		return $this;
	}

	/**
	 * Возвращает значение указанного свойства в текущем узле xml дерева
	 *
	 * @param string $name Имя свойства
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return string
	 */
	public function getValue($name)
	{
		if ( empty($this->_fileSettings) ) {
			throw new XmlException('Xml error: not open xml file', 1101);
		}

		return $this->_currentNode->$name;
	}

	/**
	 * Удаляет указанное свойства в текущем узле xml дерева
	 *
	 * @param string $name Имя свойства
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return Uwin\Xml
	 */
	public function unsetProperty($name)
	{
		if ( empty($this->_currentNode) ) {
			throw new XmlException('Xml error: not open xml file', 1101);
		}

		if ( empty($this->_currentNode->$name) ) {
			return false;
		}

		unset($this->_currentNode->$name);

		return $this;
	}

	public function getXml()
	{
		return $this->_rootNode->asXml();
	}

	/**
	 * Сохранение xml объекта в файл
	 *
	 * @param string|null $file Полное имя xml файла
	 * @throws Uwin\Xml\Exception Ошибка работы c xml
	 * @return bool
	 */
	public function save($file = null)
	{
		Profiler::getInstance()->startCheckpoint('File', 'Save values in file ' . $this->getFileSettings());

		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($this->_rootNode->asXML());

		if ( empty($file) ) {
			$file = $this->_fileSettings;
		}

		if ( !$dom->save($file) ) {
			throw new XmlException('Xml error: failure save xml file "' . $file . '"', 1103);
		}

		Profiler::getInstance()->stopCheckpoint();

		return true;
	}
}
