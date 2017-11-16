<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Registry, использующийся как единое место для
 * хранения глобальных переменных приложения
 *
 * @category  Uwin
 * @package   Uwin\Registry
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2013 Uwinart Development (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Registry
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \ArrayObject             as ArrayObject;
use \Uwin\Registry\Exception as RegistryException;
use \Uwin\Cacher\Interface_  as Cacher;

/**
 * Класс, использующийся как единое место для хранения глобальных переменных
 * приложения
 *
 * @category  Uwin
 * @package   Uwin\Registry
 * @author    Yurii Khmelevskii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2013 Uwinart Development (http://uwinart.com)
 */
class Registry extends ArrayObject
{
	/**
	 * Ссылка на объект класса Registry
	 * @var Registry
	 */
	private static $_instance = null;

	/**
	 * Ссылка на объект класса кешировщика
	 * @var \Uwin\Cacher\Interface_
	 */
	private $_cacher = null;

	/**
	 * Метод, который получает многомерный ассоцеативный массив и рекурсивно
	 * формирует одномерный массив используя разделитель "_"
	 *
	 * @param array  $array         - Многомерный ассоцеативный массив этих переменных
	 * @param string $prefix = null - Префикс, который прибавляется к ключу
	 *
	 * @return array
	 */
	private function _getFlatArrayRecursive(array $array, $prefix = null)
	{
		$result = array();
		foreach ($array as $key => $value) {
			if ( is_array($value) ) {
				$result = array_merge(
					$result, $this->_getFlatArrayRecursive($value, $prefix . $key . '_')
				);
			} else {
				$result[$prefix . $key] = $value;
			}
		}

		return $result;
	}

    /**
     * Конструктор класса. Так как класса наследуется от
     * ArrayObject, мы не можем объявить конструктор приватным, но нам нужно
     * чтобы в приложении был всего один экземпляр класса \Uwin\Registry
     * (паттерн Singlton), поэтому если конструктор класса вызывается больше
     * чем один раз выззывается исключение
     *
     * @param array  $array = array() - Массив переменных
     * @param int|string $flags = parent::ARRAY_AS_PROPS
     *
     * @throws Exception
     * @return Registry
     */
	public function __construct($array = array(), $flags = parent::ARRAY_AS_PROPS)
	{
		if (null !== self::$_instance) {
			throw new Exception('Registry error: failure create Uwin::Registry, object is created', 601);
		}

		parent::__construct($array, $flags);
		self::$_instance = $this;
	}

	/**
	 * Метод возвращает ссылку на объект класса Registry
	 *
	 * @return Registry
	 */
	public static function getInstance()
	{
		if ( empty(self::$_instance) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Метод устанавливает в реестре переменную и ее значение, если переменная
	 * уже существовала, заменяет ее значение. Можно передавать ассоциативный
	 * массив переменных/значений
	 *
	 * @param mixed  $value        - Значение переменной, либо массив переменных, которые хранится в виде ассоциативного массива
	 * @param string $index = null - Имя переменной, можно не указывать если устанвливается массив переменных
	 *
	 * @return bool
	 */
	public static function set($value, $index = null)
	{
		// Если устанавливается массив переменных, заносим в реестр их все в
		// цикле, иначе просто устанавливает переменную
		if (null === $index) {
			foreach ($value as $k => $v) {
				self::getInstance()->offsetSet($k, $v);
			}
		} else {
			self::getInstance()->offsetSet($index, $value);
		}

		return true;
	}

	/**
	 * Метод возвращает занчение переменной $index, которое хранится в реестре
	 *
	 * @param string $index
	 *
	 * @return mixed
	 */
	public static function get($index)
	{
		return self::getInstance()->offsetGet($index);
	}

	/**
	 * Метод устаналивает объект класса, который будет отвечать за кеширование
	 *
	 * @param \Uwin\Cacher\Interface_ $cacher - Объект класса кешировщика
	 *
	 * @return Registry
	 */
	public function setCacher(Cacher $cacher)
	{
		$this->_cacher = $cacher;

		return $this;
	}

	/**
	 * Метод возвращает признак того, используется кеширование или нет
	 *
	 * @return bool
	 */
	public function useCacher()
	{
		if ( empty($this->_cacher) ) {
			return false;
		}

		return true;
	}

	/**
	 * Метод, который получает многомерный ассоциативный массив и формирует
	 * одномерный массив используя разделитель "_", предварительно проверяя
	 * нет ли его в кеше, если он используется
	 *
	 * @param array $array - Многомерный ассоцеативный массив этих переменных
	 *
	 * @return array
	 */
	public function getFlatArray(array $array)
	{
		// Если кеширвоание не используется
		if ( !$this->useCacher() ) {
			return $this->_getFlatArrayRecursive($array);
		}

		// Пытаемся получить данные с кеша
		$keyCache = 'flat_' . md5( serialize($array) );
		$dataCache = $this->_cacher->get($keyCache);

		// Возвращаем их, если они есть
		if (false !== $dataCache) {
			return $dataCache;
		}

		// Иначе формируем их и пишем в кеш
		$result = $this->_getFlatArrayRecursive($array);
		$this->_cacher->set($keyCache, $result, 86400); // Сохраняю данные на сутки

		return $result;
	}

    /**
     * Метод рекурсивно объединяет массивы с заменой значений
     *
     * @static
     * @param array $array1 - Первый массив
     * @param array $array2 - Второй массив
     *
     * @return array
     */
    public static function array_merge_recursive_unique($array1, $array2) {
        if ( empty($array1) ) {
            return $array2;
        }

        foreach ($array2 as $key => $value) {
            if ( is_array($value) && isset($array1[$key]) && is_array(@$array1[$key]) ) {
                $value = self::array_merge_recursive_unique($array1[$key], $value);
            }

            $array1[$key] = $value;
        }

        return $array1;
    }

}
