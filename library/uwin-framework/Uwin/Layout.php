<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Layout, который отвечает за работу с макетом
 * страницы
 *
 * @category  Uwin
 * @package   Uwin\Layout
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Layout
 */
namespace Uwin;


/**
 * Класс, , который отвечает за работу с макетом страницы
 *
 * @category  Uwin
 * @package   Uwin\Layout
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Layout
{
	/**
	 * Ссылка на объект класса \Uwin\Layout
	 * @var Layout
	 */
	private static $_instance = null;

	/**
	 * Название файла макета
	 * @var string
	 */
	private $_layoutName = 'layout';

	/**
	 * Путь до скриптов макетов
	 * @var string
	 */
	private $_layoutPath = null;

	/**
	 * Приватный конструктор класса, так как класс реализует паттерн Singlton
	 *
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Метод возвращает ссылку на объект класса \Uwin\Layout
	 *
	 * @return Layout
	 */
	public static function getInstance()
	{
		if ( empty(self::$_instance) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Метод возвращет имя файла макета
	 *
	 * @return string
	 */
	public function getLayoutFileName()
	{
		if ( null === $this->_layoutName ) {
			return null;
		}

		return $this->getLayoutName() . '.tpl';
	}

	/**
	 * Метод, который устанавливает путь к файлам макетов
	 *
	 * @param string $path - Путь к файлам макетов
	 *
	 * @return bool
	 */
	public function setLayoutPath($path)
	{
		$this->_layoutPath = $path;

		return true;
	}

	/**
	 * Метод, который возвращает путь к файлам макетов
	 *
	 * @return string
	 */
	public function getLayoutPath()
	{
		return $this->_layoutPath;
	}


	/**
	 * Функция устанвливает имя макета
	 *
	 * @param string $name - Имя макета
	 *
	 * @return bool
	 */
	public function setLayoutName($name)
	{
		$this->_layoutName = $name;

		return true;
	}

	/**
	 * Функция возвращет имя макета
	 *
	 * @return string
	 */
	public function getLayoutName()
	{
		return $this->_layoutName;
	}

	/**
	 * Метод возвращет полный путь к файлу макета
	 *
	 * @return string
	 */
	public function getLayoutFile()
	{
		if ( null === $this->_layoutName ) {
			return null;
		}

		return $this->getLayoutPath() . $this->getLayoutFileName();
	}

	/**
	 * Метод, который очищает занчение переменной, которая хранит имя макета,
	 * тем самым говоря, что нужно рендерить страницу не использую макет
	 *
	 * @return bool
	 */
	public function unsetLayout()
	{
		$this->_layoutName = null;

		return true;
	}

	public function useLayout() {
		if (null === $this->_layoutName) {
			return false;
		}

		return true;
	}
}