<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Exception, который является родительским классом
 * для всех исключений, которые порождаются в фреймворке
 *
 * @category  Uwin
 * @package   Uwin\Exception
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Exception
 */
namespace Uwin;

/**
 * Класс, который является родительским классом для всех исключений, которые
 * порождаются в фреймворке
 *
 * @category  Uwin
 * @package   Uwin\Exception
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Exception extends \Exception
{
	/**
	 * Конструктор класса исключения, которы проверяет, были ли открыты точки
	 * проверки (для профилирования) и если были открыты, всех их закрывает
	 *
	 * @param string $message = null - Текст исключения
	 * @param int    $code    = 0    - Код исключения
	 *
	 * @return \Uwin\Exception
	 */
	public function __construct($message = null, $code = 0)
	{
		$profiler = Profiler::getInstance();
		for ($i = 0; $i < $profiler->countCheckpoints(); $i++) {
			$profiler->stopCheckpoint();
		}

		parent::__construct($message, $code);

		return $this;
	}

	/**
	 * Метод возвращает имя класса исключения
	 *
	 * @return string
	 */
	public function getClassName()
	{
		return __CLASS__;
	}
}