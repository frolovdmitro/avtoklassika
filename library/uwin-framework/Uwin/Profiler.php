<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Profiler, который отвечает за профилирование
 * всех значимых операций запроса и за сам запрос
 *
 * @category  Uwin
 * @package   Uwin\Profiler
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Profiler
 */
namespace Uwin;

/**
 * Класс, который отвечает за профилирование всех значимых операций запроса
 * и за сам запрос
 *
 * @category  Uwin
 * @package   Uwin\Profiler
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Profiler
{
	/**
	 * Ссылка на экзепляр класса
	 * @var Uwin\Profiler
	 */
	private static $_instance = null;

	/**
	 * Количество открытых точек проверки
	 * @var int
	 */
	private $_countCheckpoints = 0;

	/**
	 * Массив содержащий статистику профилирования
	 * @var array
	 */
	private $_stats = array();

	/**
	 * Стэк в котором содержатся запущенные, но не закрытые задачи
	 * @var array
	 */
	private $_stack = array();

	/**
	 * Текущая позиция задачи в списке
	 * @var int
	 */
	private $_currentPosition = 0;

	/**
	 * Массив типов операций которые не нужно профилировать
	 * @var array
	 */
	private $_ignoreTypesOperations = array();

	/**
	 * Число указывающее через какой шаг записывать профилирующую информацию в
	 * лог-файл
	 * @var int
	 */
	private $_logEachRequest = 1;

	/**
	 * Отметка о том, нужно вести лог в файл или нет
	 * @var bool
	 */
	private $_logInFile = true;

	/**
	 * Полное имя файла куда будет сохраняться информация о профилировании
	 * @var string
	 */
	private $_logFile = null;

	private $_printed_stats = true;

	/**
	 * Приватный конструктор класса, так как класс реализует паттерн Singlton.
	 *
	 * @return void
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
	 * Метод возвращает ссылку на объект класса Uwin\Profiler
	 *
	 * @return \Uwin\Profiler
	 */
	public static function getInstance()
	{
		if ( empty(self::$_instance) )  {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Метод очищает массив типов операций которые не нужно профилировать
	 *
	 * @return Uwin\Profiler
	 */
	public function clearIgnoreTypesOperations()
	{
		$this->_ignoreTypesOperations = array();

		return $this;
	}

	/**
	 * Метод устанавливает тип операции которую не нужно профилировать
	 *
	 * @param string $nameType Тип операции
	 * @return \Uwin\Profiler
	 */
	public function setIgnoreTypeOperations($nameType)
	{
		$this->_ignoreTypesOperations[$nameType] = null;

		return $this;
	}

	/**
	 * Метод возвращает массив типов операций которые не нужно профилировать
	 *
	 * @return array
	 */
	public function getIgnoreTypesOperations()
	{
		return array_keys($this->_ignoreTypesOperations);
	}

	/**
	 * Метод возвращает признак того, игнорируется данный тип операции или нет
	 *
	 * @param string $nameType Тип операции
	 * @return bool
	 */
	public function isIgnoreTypeOperations($nameType)
	{
		$result = in_array( $nameType, $this->getIgnoreTypesOperations() );

		return $result;
	}

	/**
	 * Метод устанавливает/возвращает через какое число запросов записывать
	 * профилирующую информацию в лог-файл
	 *
	 * @param int $num
	 * @return int
	 */
	public function logEachRequest($num = null)
	{
		if (null !== $num) {
			$this->_logEachRequest = $num;
		}

		return $this->_logEachRequest;
	}

	/**
	 * Метод устанавливает, нужно вести лог в файл или нет
	 *
	 * @param bool $value
	 * 
	 * @return \Uwin\Profiler
	 */
	public function setLogInFile($value)
	{
		$this->_logInFile = $value;

		return $this;
	}

	/**
	 * Метод возвращает отметку о том, нужно вести лог в файл или нет
	 *
	 * @return bool
	 */
	public function getLogInFile()
	{
		return $this->_logInFile;
	}

	/**
	 * Метод устанавливает полное имя файла, куда будет сохраняться информация о
	 * профилировании
	 *
	 * @param string $file Полное имя файла
	 * @return \Uwin\Profiler
	 */
	public function setLogFile($file)
	{
		$this->_logFile = $file;

		return $this;
	}

	/**
	 * Метод возвращает полное имя файла, куда сохраняеться информация о
	 * профилировании
	 *
	 * @return string
	 */
	public function getLogFile()
	{
		return $this->_logFile;
	}

	/**
	 * Метод ставит контрольную точку от которой будет засекаться время
	 *
	 * @param $type Тип задачи
	 * @param $desription Описание задачи
	 * 
	 * @return \Uwin\Profiler
	 */
	public function startCheckpoint($type, $desription)
	{
		// Добавляем в конец стэка новые элемент
		array_push($this->_stack,
			array(
				'execTime' => microtime(true),
				'type'     => $type,
				'desription'     => "\"" . $desription . "\"",
				'position' => $this->_currentPosition,
			 )
		);

		$this->_currentPosition ++;
		$this->_countCheckpoints ++;

		return $this;
	}

	/**
	 * Метод cнимает установленную контрольную и вычисляет потраченное время.
	 * Полученные результаты записывает в массив
	 *
	 * @return Uwin\Profiler
	 */
	public function stopCheckpoint()
	{
		// Получаем последний элемент в стэке и удаляем его
		$checkpoint = array_pop($this->_stack);

		// Вычисляем потраченное время и уровень вложенности задачи
		$checkpoint['execTime'] = microtime(true) - $checkpoint['execTime'];
		$checkpoint['level'] = count($this->_stack);

		if ( array_key_exists('position', $checkpoint) ) {
			// Получаем позицию задачи в общем порядке
			$position = $checkpoint['position'];
			unset($checkpoint['position']);
			// Помещаем задачу в массив статистики
			$this->_stats[$position] = $checkpoint;
		}


		$this->_countCheckpoints--;

		return $this;
	}

	/**
	 * Метод возвращает количество открытых точек проверки
	 *
	 * @return int
	 */
	public function countCheckpoints()
	{
		return $this->_countCheckpoints;
	}

	/**
	 * Метод возвращает задачи в виде массива
	 *
	 * @return array
	 */
	public function getStatsArray()
	{
		// Сортируем массив по ключам
		ksort($this->_stats);

		return $this->_stats;
	}

	/**
	 * Метод возвращает операции которые были профилированы в виде строки
	 *
	 * @return string
	 */
	public function getStatsLines()
	{
		$this->startCheckpoint('Profiler', 'Get stats lines');
		$result = '';

		// Сортируем массив по ключам
		ksort($this->_stats);

		// Формирование информации о профилируемых задачах в виде текстового
		// блока
		foreach ($this->_stats as $value) {

			// Если статистику по данному типу операции не нужно собирать,
			// пропустить итерацию
			if ( $this->isIgnoreTypeOperations($value['type']) ) {
				continue;
			}

			$result .= str_repeat('  ', $value['level']);

			unset($value['level']);

			$result .= implode("\t", $value) . "\n";
		}

		$this->stopCheckpoint();

		return $result;
	}

	/**
	 * Метод возвращает операции которые были профилированы в виде html
	 *
	 * @return string
	 */
	public function getStatsHtml()
	{
		$result = '';

		// Сортируем массив по ключам
		ksort($this->_stats);

		// Формирование информации о профилируемых задачах в виде текстового
		// блока
		foreach ($this->_stats as $value) {

			// Если статистики по данному типу операции не нужно собирать,
			// пропустить итерацию
			if ( $this->isIgnoreTypeOperations($value['type']) ) {
				continue;
			}

			$result .= str_repeat('&nbsp;&nbsp;&nbsp;', $value['level']);

			unset($value['level']);

			$result .= implode("&nbsp;&nbsp;", $value) . "<br/>";
		}
		$result = '<div style="font-family: tahoma; font-size: 11px; line-height: 16px">' . $result . '</div>';

		return $result;
	}

	public function printedStats($printed)
	{
		$this->_printed_stats = $printed;
	}

	public function printStatsHtml()
	{
		if ($this->_printed_stats) {
			echo $this->getStatsHtml();
		}

		return null;
	}

	/**
	 * Метод записывает в лог-файл информацию о профилировании запроса
	 *
	 * @return Profiler
	 */
	public function saveStats()
	{
		$this->startCheckpoint('Profiler', 'Save stats');

		$numRequest = 0;

		// Если указано что нужно сохранять в лог-файл информацию о
		// профилировании не по каждому запросу
		if ($this->_logEachRequest > 1) {
			$memcached = Memcached::getInstance();
			// Инткрементируем счетчик в memcached
			$numRequest = $memcached->increment(SERVER_ID . '_prfitr');

			// Если счетчик не установлен, установить его в начальное значение
			if (false ===  $numRequest) {
				$memcached->set(SERVER_ID . '_prfitr', 1);
				$numRequest = 1;
			}
		}

		// Выполнять запись только с тем шагом, который был установлен
		if ( 0 === ( $numRequest % ($this->_logEachRequest+1) ) ) {
			// Если профилирование в лог-файл включено и путь к файлу указан
			if ( ($this->_logInFile) && ($this->_logFile != null) ) {
				// Записать информацию о профилировании запроса в конец файла
				$file = new Fs\File($this->_logFile);
				$file->appendLine($this->getStatsLines());
			}
		}

		$this->stopCheckpoint();

		return $this;
	}
}