<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Xml
 *
 * @category  Uwin
 * @package   Uwin\Xml
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Config, к которому относится класс Xml
 */
namespace Uwin\Config;

// Объявление псевдонимов для всех используемых классов в данном файле
use \DOMDocument               as Document;
use \DOMNode                   as Node;
use \DOMElement                as Element;
use \DOMNodeList               as NodeList;
use \DOMAttr	     		   as Attr;
use \Uwin\Config\Xml\Exception as Exception;

/**
 * Класс, который отвечает за работу с XML файлами
 *
 * @category   Uwin
 * @package    Uwin\Config
 * @subpackage Xml
 * @author     Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Xml implements Interface_
{
	/**
	 * Объект класса DOMDocument, с помощью которого проходит вся работа с XML
	 * @var \DOMDocument = null
	 */
	private $_handler = null;

	/**
	 * Текущий узел, относительно которого проводятся все манипуляции с XML
	 * @var \DOMNode = null
	 */
	private $_currentNode = null;

	/**
	 * Путь к текущему узлу XML
	 * @var string = null
	 */
	private $_path = null;


	/**
	 * Метод возвращает признак того, нужно для указанного текста использовать
	 * CDATA или нет
	 *
	 * @param string $text - Текст
	 *
	 * @return bool
	 */
	private function _isCdataText($text) {
		$cdata = false;
		foreach ( array('"', "'", '&', '<', '>') as $cdataSymbol ) {
			if ( false !== strpos($text, $cdataSymbol) ) {
				$cdata = true;

				break;
			}
		}

		return $cdata;
	}

	/**
	 * Метод устанавливает для указанного узла указанное значение, с проверкой,
	 * нужно его вставлять как CDATA или нет
	 *
	 * @param \DOMNode $node  - Узел в который будет установлено значение
	 * @param string   $value - Устанавливаемое значение
	 *
	 * @return Xml
	 */
	private function _addValueToNode($node, $value) {
		if ( $this->_isCdataText($value) ) {
			/** @noinspection PhpUndefinedMethodInspection */
			$node->appendChild(
				$this->_handler->createCDATASection($value) );
		} elseif (null != $value) {
			/** @noinspection PhpUndefinedFieldInspection */
			$node->nodeValue = $value;
		}

		return $this;
	}

	/**
	 * Метод добавляет в указанный узел новый подузел или если указан массив, то
	 * рекурсивно добавляет узлы
	 *
	 * @param \DOMNode     $node         - Узел, куда будут добавлен указанный узел или массив подузлов
	 * @param string|array $name         - Имя добавляемого узла, или массив подузлов
	 * @param string       $value = null - Значение добавляемого узла(не используется если добавляется массив подузлов)
	 *
	 * @return Xml
	 */
	private function _addNode($node, $name, $value = null) {
		$newNode = $node;

		// Если добавляется несколько узлов
		if ( is_array($name) ) {
			// Проходимся по всем добавляемым улам и рекурсивно их добавляем
			foreach ($name as $nodeName => $nodeValue) {
				// Если добавляемый массив содержит не кдюч/значение данные, а
				// только значение, формируем имя узла и значение его = null
				if ( is_int($nodeName) ) {
					$nodeName = $nodeValue;
					$nodeValue = null;
				}

				$this->_addNode($newNode, $nodeName, $nodeValue);
			}

			return $this;
		}

		// Если родительский узел содержит значение и не содержит подузлов,
		// переносим это значение в под-узел value
		if ( null != $node->nodeValue && !$this->_hasChild($node) ) {
			// Получаем значение узла и очищаем его
			$valueNode = $node->nodeValue;
			$node->nodeValue = null;

			// Добавляем узел
			/** @noinspection PhpParamsInspection */
			$newNode = new Element('value');
			$node->appendChild($newNode);

			// Устанавливаем значение добавленного узла
			$this->_addValueToNode($newNode, $valueNode);
		}

		// Создаем новый узел
		/** @noinspection PhpParamsInspection */
		$newNode = new Element($name);
		$node->appendChild($newNode);

		//Если зачение является массивом, вызываем данную функцию рекурсовно
		if ( is_array($value) ) {
			$this->_addNode($newNode, $value);
		} else {
			// иначе добавляем занчение
			$this->_addValueToNode($newNode, $value);
		}

		return $this;
	}

	/**
	 * Метод возвращает имя узла на основе пути к этому узлу
	 *
	 * @param string $path - Путь вместе с пееменной или просто имя переменной
	 *
	 * @return string
	 */
	private function _getName($path) {
		$name = $path;

		// Если указано не просто имя переменной, а с путем, тогда разделяем их
		// по разным переменным
		if ( false !== strpos($path, '/') ) {
			$path = trim($path, '/');

			$nodeParts = explode('/', $path);
			$name = array_pop($nodeParts);
		}

		//Убераем у переменной индекс, если таковой имеется
		if ( false !== $idxPos = strpos($name, '[') ) {
			$name = substr($name, 0, $idxPos);
		}

		return $name;
	}

	/**
	 * Метод возвращает под-узлы с указанным именем(если имя не указано, будут
	 * выбраны все под-узлы)
	 *
	 * @param \DOMNode $parentNode  - Родительский узел в котором будут выбираться узлы
	 * @param string   $name = null - Имя узла
	 *
	 * @return null|array|\DOMNode
	 */
	private function _getChilds($parentNode, $name = null) {
		$node = null;

		// Проверяем, указан ли индекс узла (в случае если таких узлов
		// несколько), если указан, получаем отдельно имя узла, отдельно
		// индекс
		$index = null;
		if ( null != $name && false !== $idxPos = strpos($name, '[') ) {
			$index = (int)str_replace( ']', '', substr($name, $idxPos + 1) );
			$name = substr($name, 0, $idxPos);
		}

		// Поиск узла в XML
		for ($i = $j = 0; $i < $parentNode->childNodes->length; $i++) {
			$child = $parentNode->childNodes->item($i);

			// Если имя узла совпадает
			if ( ( XML_ELEMENT_NODE == $child->nodeType
				&& (null == $name || $name == $child->nodeName) )
			) {
				// А также если не указан индекс узла или индекс равен
				// указанному получаем
				if ($j == $index || null === $index) {
					$node[] = $child;
				}
				// Если индекс указан и совпал выходм с цикла
				if ($j === $index) {
					break;
				}

				$j++;
			}
		}

		// Если найден один узел, возвращаем его, а не массив узлов
		if ( is_array($node) && 1 == count($node) ) {
			$node = $node[0];
		}

		return $node;
	}

	/**
	 * Метод возвращает признак того, содержит ли указанный узел под-узлы
	 *
	 * @param \DOMNode $node - Узел в котором проверяется, есть ли под-узлы
	 *
	 * @return bool
	 */
	private function _hasChild($node) {
		if ( null == $this->_getChilds($node) ) {
			return false;
		}

		return true;
	}

	/**
	 * Метод возвращает ссылку на объект узла по указанному пути, если узел не
	 * существует - вызывает исключение.
	 * Пример пути: /path/to[1]/node[4]
	 *
	 * @param string $path - Путь к узлу XML, где части пути разделены символом "/" а также можно указать индекс с помощью квадратных скобок
	 *
	 * @return \DOMElement|\DOMNode
	 * @throws Xml\Exception
	 */
	private function _getNodeByPath($path) {
		$node = null;
		$pathLine = $path;

		// Определяемся, формировать путь к узлу от корня или от текущего узла
		$parentNode = $this->_currentNode;
		if (isset($path[0]) && '/' == $path[0]) {
			$parentNode = $this->_handler;
		}

		// Формируем массив составных частей пути
		$path = trim($path, '/\\');
		if ( !empty($path) ) {
			if (']' != $path[mb_strlen($path)-1]) {
				$path .= '[0]';
			}
			$path = explode('/', $path);
		}

		// Если путь не указан, возвращаем текущий узел
		if (null == $path) {
			return $parentNode;
		}

		// Проходимся по составным частям пути для поиска конечного узла
		foreach ($path as $item) {
			if ( empty($parentNode) ) {
				throw new Exception('Узел не найден по пути: ' . $pathLine);
			}

			$node = $parentNode = $this->_getChilds($parentNode, $item);

			// Если выбрано несколько узлов, берем первый
			if ( is_array($parentNode) ) {
				$parentNode = $parentNode[0];
			}
		}

		// Если узел по указаному пути не найден, вызываем исключение
		if (null == $node) {
			throw new Exception('Узел не найден по пути: ' . $pathLine);
		}

		return $node;
	}

	/**
	 * Метод возвращает значение указанного атрибута или массив всех атрибутов
	 * указанного узла
	 *
	 * @param \DOMNode $node            - Узел, в  котором ищутся атрибуты
	 * @param string  $attribute = null - Имя атрибута
	 *
	 * @return array|null|mixed
	 */
	private function _getAttr($node, $nameAttr = null) {
		$attributes = null;

		if ( empty($node->attributes) ) {
			return null;
		}

		// Проходимя по всем атрибутам узла, выбираем нужные и формируем массив
		// атрибутов
		/** @noinspection PhpUndefinedFieldInspection */
		for ($i = 0; $i < @$node->attributes->length; $i++) {
			$attr = $node->attributes->item($i);

			if ( XML_ATTRIBUTE_NODE == $attr->nodeType
				 && (null === $nameAttr || $attr->nodeName == $nameAttr)
			) {
				if (null !== $nameAttr) {
					$attributes = $attr->nodeValue;

					break;
				}

				$attributes[$attr->nodeName] = $attr->nodeValue;
			}
		}

		return $attributes;
	}

	/**
	 * Метод возвращает признак того, содержит ли указанный узел атрибут с
	 * указанным именем, или если имя не указано, то седержит ли он атрибуты
	 *
	 * @param \DOMNode $node            - Узел, в котором проверяется есть ли атрибут
	 * @param string   $nameAttr = null - Имя атрибута
	 *
	 * @return bool
	 */
	private function _hasAttr($node, $nameAttr = null) {
		if ( null == $this->_getAttr($node, $nameAttr) ) {
			return false;
		}

		return true;
	}

	/**
	 * Метод формирует массив значений указанного узла
	 *
	 * @param \DomNode $node              - Узел, значения которого нужно получить
	 * @param bool     $with_attr = false - Возвращать атрибуты или нет
	 *
	 * @return array
	 */
	private function _getValues($node, $with_attr = false) {
		$values = null;

		// Если нужно получать значение узлов вместе с атрибутами, получаем атрибуты
		if ( $with_attr ) {
			$values = $this->_getAttr($node);
		}

		// Если узел не содержит подузлов, возвращаем его значение
		if ( !$this->_hasChild($node) ) {
			// Если нужно возвращать значени вместе с атрибутами и атрибуты
			// у узла существуют, формируем массив атрибутов
			if ( empty($values) ) {
				$values = trim($node->nodeValue);
			} else {
				$values['value'] = trim($node->nodeValue);
			}

			return $values;
		}

		// Мы уже знаем что под-узлы у переданного узла есть, получаем их, если
		// он один, формируем массив из одного под-узла
		$nodeChilds = $this->_getChilds($node);
		if ($nodeChilds instanceof Node) {
			$nodeChilds = array($nodeChilds);
		}

		// Проходимся по дочерним узлам чтобы сформировать массив
		foreach ($nodeChilds as $child) {
			// Получаем атрибуты узла, если они есть
			$attributes = null;
			if ($with_attr) {
				$attributes = $this->_getAttr($child);
			}

			// Если узел содержит под-узлы вызываем эту функцию рекурсивно, иначе
			// полчаем значение узла
			if ( $this->_hasChild($child) ) {
				$value = $this->_getValues($child, $with_attr);
			} else {
				/** @noinspection PhpUndefinedFieldInspection */
				$value = trim($child->nodeValue);
			}

			// Если значение равно пустой строке, значит значение равно NULL
			if ('' == $value) {
				$value = null;
			}

			// Если в сформированном массиве узел с указанным именем не
			// существует - создаем, присваиваем ему значение и переходим к
			// следующему XML узлу
			/** @noinspection PhpUndefinedFieldInspection */
			if ( !isset($values[$child->nodeName]) ) {
				if ( $with_attr && $this->_hasAttr($child) ) {
					if ( is_array($value) ) {
						/** @noinspection PhpUndefinedFieldInspection */
						$values[$child->nodeName] = array_merge($value, $attributes);
					} else {
						$value = array('value' => $value);

						/** @noinspection PhpUndefinedFieldInspection */
						$values[$child->nodeName] = array_merge($value, $attributes);
					}
				} else {
					/** @noinspection PhpUndefinedFieldInspection */
					$values[$child->nodeName] = $value;
				}

				continue;
			}

			// Если узел содержит под-узлы и если узел не в виде ассоциативного
			// массива, дабавляем новый элемент массива и переходим к следующему
			// XML узлу
			/** @noinspection PhpUndefinedFieldInspection */
			if ( is_array($values[$child->nodeName])
				&& isset($values[$child->nodeName][0])
			) {
				if ( $with_attr && $this->_hasAttr($child) ) {
					$value = array('value' => $value);
					/** @noinspection PhpUndefinedFieldInspection */
					$values[$child->nodeName][] = array_merge($value, $attributes);
				} else {
					/** @noinspection PhpUndefinedFieldInspection */
					$values[$child->nodeName][] = $value;
				}

				continue;
			}

			// Если в сформированном массиве узел с указанным именем существует
			// и если узел в видео ассоциативного массива, превращаем значения
			// узла в список
			if ( $with_attr && $this->_hasAttr($child) ) {
				if ( null != $value) {
					$value = array('value' => $value);
				} else {
					$value = array();
				}

				/** @noinspection PhpUndefinedFieldInspection */
				$values[$child->nodeName] = array($values[$child->nodeName], array_merge($value, $attributes));
			} else {
				/** @noinspection PhpUndefinedFieldInspection */
				$values[$child->nodeName] = array($values[$child->nodeName], $value);
			}
		}

		return $values;
	}

	/**
	 * Конструктор класса, который формирует XML с указанного файла или
	 * текстовой переменной или массива и устанавливает текущий узел, который
	 * указан с помощью $path. Если $xml = NULL - создается пустой XML
	 *
	 * @param array|string $xml  = null - Путь к xml-файлу или XML-текст или массив, который будет преобразован в XML
	 * @param string       $path = null - Путь к узлу, который будет сделан текущим
	 *
	 * @return \Uwin\Config\Xml
	 */
	public function __construct($xml = null, $path = null) {
		$this->_handler = new Document('1.0', 'UTF-8');
		$this->_handler->formatOutput = true;

		// Устанавливаем текущий узел в корень
		$this->_currentNode = $this->_handler;

		// Если передан массив, формируем на основе его XML
		if ( is_array($xml) ) {
			$this->add(null, $xml);
		} elseif ( false !== strpos($xml, '<?xml') ) {
			// Если передан текст XML, загрущаем его
			if ( false === @$this->_handler->loadXML($xml) ) {
				throw new Exception('Строка "' . $xml . '" содержит невалидный XML');
			}
		} elseif (null !== $xml) {
			// Иначе считаем что передан путь к XML-файлу, который и загружаем
			if ( !file_exists($xml) ||  false === $this->_handler->load($xml) ) {
				throw new Exception('Ошибка загрузки XML-файла: ' . $xml);
			}
		}

		// Устанавливаем текущий узел, в соответствии с переданным путем $path
		$this->setPath($path);

		return $this;
	}


	/**
	 * Метод устанавливает путь к узлу, который будет текущим
	 *
	 * @param string $path - Путь к узлу
	 *
	 * @return Xml
	 */
	public function setPath($path) {
		// Формируем путь от корня
		if (isset($path[0]) && '/' != $path[0]) {
			$path = rtrim($this->_path, '/') . '/' . $path;
		}

		if ('/' != $path) {
			$path = rtrim($path, '/');
		}

		$this->_path = $path;
		$node = $this->_getNodeByPath($this->_path);

		if ( $node != $this->_handler && !$this->_hasChild($node) ) {
			throw new Exception('Невозможно выбрать в качстве пути к узлам, узел со значением: ' . $path);
		}

		$this->_currentNode = $node;

		return $this;
	}

	/**
	 * Метод возвращает путь к текущему узлу
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->_path;
	}

	/**
	 * Метод возвращает количество подузлов у текущего или указанного узла
	 *
	 * @param string $name = null - Имя узла
	 *
	 * @return int
	 */
	public function count($name = null) {
		$node = $this->_getNodeByPath($name);

		return count( $this->_getChilds($node) );
	}

	/**
	 * Метод возвращает признак того, существует указанный узел или нет,а также
	 * может возвращать признак того, существует указанный атрибут или нет
	 *
	 * @param string $name             - Имя узла
	 * @param string $attribute = null - Имя атрибута
	 *
	 * @return bool
	 */
	public function exists($name, $attribute = null) {
		$result = true;

		// Ищем указанный узел
		try {
			$node = $this->_getNodeByPath($name);
		} catch (Exception $e) {
			return false;
		}

		// Если узел найден и нужно узнать есть ли в этом узле указанный атрибут
		if ($result && null !== $attribute) {
			$result = $this->_hasAttr($node, $attribute);
		}

		return $result;
	}

	/**
	 * Метод устанавливает значение указанного узла, если узел не найден,
	 * вызывает исключение
	 *
	 * @param string $name  - Имя узла
	 * @param mixed  $value - Значение узла
	 *
	 * @return Xml
	 * @throws Xml\Exception
	 */
	public function set($name, $value) {
		$path = $name;
		$name = $this->_getName($name);
		$node = $this->_getNodeByPath($path);

		if ( $this->_isCdataText($value) ) {
			/** @noinspection PhpParamsInspection */
			$newNode = new \DomElement($name);

			/** @noinspection PhpUndefinedMethodInspection */
			$node->parentNode->replaceChild($newNode, $node);
			$newNode->appendChild($this->_handler->createCDATASection($value));

			return $this;
		}

		$node->nodeValue = $value;

		return $this;
	}

	/**
	 * Метод возвращает значение указанного узла со всеми его под-узлами, и если
	 * указано, то и с значениеми атрибутов
	 *
	 * @param null $name              - Имя узла
	 * @param bool $with_attr = false - Возвращать значения атрибутов ли нет
	 *
	 * @return array
	 */
	public function get($name = null, $with_attr = false) {
		$nodes = $this->_getNodeByPath($name);

		return $this->_getValues($nodes, $with_attr);
	}

	/**
	 * Метод добавляет узел или массив узлов
	 *
	 * @param string       $path         - Путь к узлу
	 * @param string|array $name         - Имя узла
	 * @param mixed        $value = null - Значение узла
	 *
	 * @return Xml
	 */
	public function add($path, $name, $value = null) {
		$node = $this->_getNodeByPath($path);

		$this->_addNode($node, $name, $value);

		return $this;
	}

	/**
	 * Метод удаляет указанный узел, или если указанно несколько узлов, то
	 * удаляет их все
	 *
	 * @param string $name - Имя узла
	 *
	 * @return Xml
	 * @throws Xml\Exception
	 */
	public function del($name) {
		$node = $this->_getNodeByPath($name);

		/** @noinspection PhpUndefinedMethodInspection */
		$node->parentNode->removeChild($node);

		return $this;
	}


	/**
	 * Метод добавляет или изменяет аттрибут или массив аттрибутов в указанный
	 * узел
	 *
	 * @param string       $name         - Путь к узлу
	 * @param string|array $attr         - Имя аттрибута
	 * @param mixed        $value = null - Значение аттрибута, если не указано, значит добавляется массив аттрибутов
	 *
	 * @return Xml
	 * @throws Xml\Exception
	 */
	public function setAttr($name, $attr, $value = null) {
		$node = $this->_getNodeByPath($name);

		if ( $this->_isCdataText($value) ) {
			throw new Exception('Нельзя атрибуту присваивать текст содержащий символы < > " \' &');
		}

		if (is_array($attr) ) {
			foreach ($attr as $nameAttr => $valueAttr){
				/** @noinspection PhpUndefinedMethodInspection */
				$node->setAttribute($nameAttr, $valueAttr);
			}

			return $this;
		}

		$node->setAttribute($attr, $value);

		return $this;
	}

	/**
	 * Метод возвращает значение атрибута указанного узла или массив аттрибутов
	 * с их значениями
	 *
	 * @param string $name      = null - Имя узла
	 * @param strung $attribute = null - Имя аттрибута
	 *
	 * @return array|mixed
	 * @throws Xml\Exception
	 */
	public function getAttr($name = null, $attribute = null) {
		$nodes = $this->_getNodeByPath($name);

		return $this->_getAttr($nodes, $attribute);
	}

	/**
	 * Метод удаляет указанный аттрибут или все атрибуты у указанного узла
	 *
	 * @param string $name             - Имя узла
	 * @param strung $attribute = null - Имя аттрибута
	 *
	 * @return Xml
	 */
	public function delAttr($name, $attribute = null) {
		$node = $this->_getNodeByPath($name);

		$attributes = $this->_getAttr($node, $attribute);

		if ( is_array($attributes) ) {
			foreach ($attributes as $attrName => $attrValue) {
				$node->removeAttribute($attrName);
			}

			return $this;
		}

		/** @noinspection PhpUndefinedMethodInspection */
		$node->removeAttribute($attribute);

		return $this;
	}

	/**
	 * Метод сохраняет XML в указанный файл
	 *
	 * @param string $filename - Имя файла
	 *
	 * @return Xml
	 * @throws Xml\Exception
	 */
	public function save($filename) {
		if ( false === @$this->_handler->save($filename) ) {
			throw new Exception('Не удалось сохранить XML в файл: ' . $filename);
		}

		return $this;
	}

	/**
	 * Метод возвращает сформированный XML
	 *
	 * @return string
	 * @throws Xml\Exception
	 */
	public function getContent() {
		return $this->_handler->saveXML();
	}
}
