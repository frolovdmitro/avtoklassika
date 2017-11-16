<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Autoloader, который реализует возможность
 * автоматической загрузки файлов классов, формируя путь к файлу класса по его
 * пространству имен и имени самого класса
 *
 * @category  Uwin
 * @package   Uwin\Autoloader
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2013 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Autoloader
 */
namespace Uwin;


/**
 * Класс, который реализует возможность автоматической загрузки файлов классов,
 * формируя путь к файлу класса по его пространству имен и имени самого класса
 *
 * @category  Uwin
 * @package   Uwin\Autoloader
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Autoloader
{
	/**
	 * Константа, хрянящая имя функции автозагрузки, которя используется
	 * по-умолчанию
	 */
	const DEFAULT_AUTOLOAD_FUNCTION = 'self::load';

	/**
	 * Статический метод, возвращающий имя файла, который содержит указанный к
	 * ласс
	 *
	 * @param string $className - Имя класса
	 *
	 * @return string
	 */
	private static function getFileByClass($className)
	{
		$className = str_replace('\\', DIR_SEP, $className) . '.php';

		return $className;
	}

	/**
	 * Статический метод, устанавливающий указанную функцию, как функцию
	 * автозагрузки (эта функция будет вызываться всякий раз, когда приложение
	 * не сможет найти класс, который оно пытается создать)
	 *
	 * @param string $nameFunction = null - Имя функции которая используется для автозагрузки, необязательный параметр
	 *
	 * @return bool
	 */
	public static function register($nameFunction = null)
	{
		if (null === $nameFunction) {
			spl_autoload_register(self::DEFAULT_AUTOLOAD_FUNCTION);
		} else {
			spl_autoload_register($nameFunction);
		}

		return true;
	}

	/**
	 * Статический метод, делегистрирует указанную функцию, как функцию
	 * автозагрузки
	 *
	 * @param string $nameFunction = null - Имя функции которая используется для автозагрузки, необязательный параметр
	 *
	 * @return bool
	 */
	public static function unregister($nameFunction = null)
	{
		if (null === $nameFunction) {
			spl_autoload_unregister(self::DEFAULT_AUTOLOAD_FUNCTION);
		} else {
			spl_autoload_unregister($nameFunction);
		}

		return true;
	}

	/**
	 * Статический метод, который используется по умолчанию для автозагрузки
	 * классов, ему передается полное имя класса, на основе которого он
	 * формирует путь к файлу класса и подключает его, если таков существует
	 *
	 * @param string $className - Имя класса, который нужно загрузить
	 *
	 * @return bool
	 */
	public static function load($className)
	{
		Profiler::getInstance()->startCheckpoint('Autoload', 'Autoload class ' . $className);

		// Формирование полногу пути к классу, включая само имя файла и подключение его
		$classFile = self::getFileByClass($className);
		include($classFile);

		Profiler::getInstance()->stopCheckpoint();

		return true;
	}
}
