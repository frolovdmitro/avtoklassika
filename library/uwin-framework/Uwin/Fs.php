<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Fs, который отвечает за работу с файловой системой
 *
 * @category  Uwin
 * @package   Uwin\Fs
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Fs
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Fs\Exception as FsException;

/**
 * Класс, который отвечает за работу с файловой структурой операционной системы
 *
 * @category  Uwin
 * @package   Uwin\Fs
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Fs
{
	/**
	 * Дескриптор открытой директории
	 * @var resource
	 */
	private $_handler;

	/**
	 * Путь к директории
	 * @var string
	 */
	private $_path;

	/**
	 * Метод считывает содержимае директории и возвращает полученные элементы в
	 * виде массива
	 *
	 * @param int $type Тип возвращаемых элементов: 0 - все элементы, 1 - директории, 2 - файлы
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @param bool $fullpath Выводить полный путь или нет
	 * @param string $path Читать содержимое данного адреса
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return array
	 */
	private function _getContentDir($type = 0, $mask = null, $reverse = false, $fullpath = true, $path = null)
	{
		// Если указан адрес, в котором нужно прочитать содержимое, создается
		// экземпляр данного класса
		$obj = $this;
		if (null !== $path) {
			$obj = new self($path);
		}

		// Если директория до этого не открыта, вызываем исключение
		if (null == $obj->_handler) {
			throw new FsException('File system error: failure read dir, not opening dir', 1215);
		}

		// Использовать полный путь при выводи полученных файлов или нет
		$path_prefix = null;
		if ( $fullpath ) {
			$path_prefix = $obj->getPath();
		}

		// Переменная говорит что нужно выводить все содержимое директории
		$type_bool = true;

		// Инициализируем массив с содержимым и переходим в начало директории
		$content = array();
		rewind($obj->_handler);

		// Чтение содержимого каталога
		while (false !== ($file = readdir($obj->_handler) ) ) {
			// Формирование переменной, которая будет говорить что выводить,
			// файлы или каталоги
			if (1 == $type) {
				$type_bool = is_dir($obj->getPath() . $file );
			} elseif (2 == $type) {
				$type_bool = !is_dir($obj->getPath() . $file );
			}

			// Если считанный элемент совпадает по требуемому типу и имя не
			// начинается с "." и если указана маска, элемент совпадает с ней
			if ( $type_bool && ( '.' != $file[0] ) &&
				( (null == $mask) || ( fnmatch($mask, $file) ) )
			) {
				$content[] = $path_prefix . $file;
			}
		}

		// Если ничего не найдено, возвращаем null
		if ( empty($content) ) {
			return null;
		}

		// Если сортировать не нужно, возвращаем полученный массив
		if (null === $reverse) {
			return $content;
		}

		// Выбор функции для сортировки
		$sort_function = 'asort';
		if ($reverse) {
			$sort_function = 'arsort';
		}

		// Сортируем полученный массив файлов
		if ( !$sort_function($content) ) {
			throw new FsException('File system error: failure sort content in dir', 1216);
		}

		return $content;
	}

	/**
	 * Метод рекурсивно считывает содержимае директории и возвращает полученные
	 * элементы в виде массива
	 *
	 * @param int $type Тип возвращаемых элементов: 0 - все элементы, 1 - директории, 2 - файлы
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @param string $path Читать содержимое данного адреса
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return array
	 */
	private function _getContentDirRecursive($type = 0, $mask = null, $reverse = null, $path = null)
	{
		// Получаем все содержимое нужной директории, не сортируем его и выводим
		// только полученные имена, без полного пути
		$content = $this->_getContentDir(0, null, null, false, $path);

		$result = array();

		// Если содержимое директории пусто, возвращаем null
		if (null == $content) {
			return null;
		}

		// Если путь не указан, используем путь открытой директории
		if (null == $path) {
			$path = $this->getPath();
		}

		// Если в конце полученного пути не присутствует символ "/", добавляем его
		if ( DIR_SEP != $path[strlen($path)-1] ) {
			$path .= DIR_SEP;
		}

		// Переменная говорит что нужно выводить все содержимое директории
		$type_bool = true;

		// Проходим в цикле по содержимому директории
		foreach ($content as $value) {
			// Формирование переменной, которая будет говорить что выводить,
			// файлы или каталоги
			if (1 == $type) {
				$type_bool = is_dir($path . $value);
			} elseif (2 == $type) {
				$type_bool = !is_dir($path . $value);
			}

			// Если считанный элемент совпадает по требуемому типу и если
			// указана маска, элемент совпадает с ней, записываем его в
			// результирующий массив
			if ( $type_bool && ( (null == $mask) || ( fnmatch($mask, $value) ) ) ) {
				$result[] = $path . $value;
			}

			// Если элемент является директорией
			if ( is_dir($path . $value) ) {
				// Получаем его содержимое
				$tmp_content = $this->_getContentDirRecursive($type, $mask, null, $path . $value);
				// Если содержимое не равно null, прибавляем его к
				// результирующему массиву
				if (null !== $tmp_content) {
					$result = array_merge($result,  $tmp_content);
				}
			}
		}

		// Если сортировать не нужно, возвращаем полученный массив
		if (null === $reverse) {
			return $result;
		}

		// Выбор функции для сортировки
		$sort_function = 'asort';
		if ($reverse) {
			$sort_function = 'arsort';
		}

		// Сортируем полученный массив файлов
		if ( !$sort_function($result) ) {
			throw new FsException('File system error: failure sort content in dir', 1216);
		}

		return $result;
	}

	/**
	 * Конструктор класса. При создании класса можно указать какой каталог
	 * нужно открыть
	 *
	 * @param string $path Путь к открываемой директории
	 * @return \Uwin\Fs\File
	 */
	public function __construct($path = null)
	{
		if (null != $path) {
			$this->openDir($path);
		}

		return $this;
	}

	/**
	 * Деструктор класса. Если была открыта директория, в деструкторе она
	 * закрывается
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->closeDir();
	}

	/**
	 * Метод открывает директория
	 *
	 * @param string $path Путь к открываемой директории
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return \Uwin\Fs
	 */
	public function openDir($path)
	{
		// Если до этого уже директория открыта, вызываем исключение
		if (null != $this->_handler) {
			throw new FsException('File system error: failure opening dir, now opening dir "' . $this->_path . '"', 1217);
		}

		// Если произошла ошибка при открытии директории, вызываем исключение
		if ( false === ($this->_handler = opendir($path)) ) {
			throw new FsException('File system error: failure opening dir "' . $path. '"', 1212);
		}

		// Если последний символ путь к директории не является "/", добавляем его
		if ( DIR_SEP != $path[strlen($path)-1] ) {
			$path .= DIR_SEP;
		}

		$this->_path = $path;

		return $this;
	}

	/**
	 * Метод закрывает открытую директорию
	 *
	 * @return \Uwin\Fs
	 */
	public function closeDir()
	{
		if ( !$this->isOpenDir() ) {
			return false;
		}

		closedir($this->_handler);

		$this->_handler = null;
		$this->_path = null;

		return $this;

	}

	/**
	 * Метод возвращает признак того, открыта директория или нет
	 *
	 * @return bool
	 */
	public function isOpenDir()
	{
		if (null == $this->_handler) {
			return false;
		}

		return true;
	}

	/**
	 * Метод переоткрывает другую директорию
	 *
	 * @param string $path Путь к открываемой директории
	 * @return \Uwin\Fs
	 */
	public function changeDir($path)
	{
		$this->closeDir();
		$this->openDir($path);

		return $this;
	}

	/**
	 * Метод возвращаем путьк открытой директории
	 *
	 * @return string
	 */
	public function getPath()
	{
		if ( !$this->isOpenDir() ) {
			return false;
		}

		return $this->_path;
	}

	/**
	 * Метод возвращает имя открытой директории
	 *
	 * @return string
	 */
	public function getDirName()
	{
		$dirname = basename( $this->getPath() );

		return $dirname;
	}

	/**
	 * Метод считывает поддиректории в открытой директориии и возвращает
	 * их в виде массива
	 *
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @param bool $fullpath Выводить полный путь или нет
	 * @return array
	 */
	public function getDirs($mask = null, $reverse = false, $fullpath = true)
	{
		$content = $this->_getContentDir(1, $mask, $reverse, $fullpath);

		return $content;
	}

	/**
	 * Метод рекурсивно считывает поддиректории в открытой директориии и
	 * возвращает их в виде массива
	 *
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @return array
	 */
	public function getDirsRecursive($mask = null, $reverse = false)
	{
		$result = $this->_getContentDirRecursive(1, $mask, $reverse);

		return $result;
	}

	/**
	 * Метод считывает файлы в открытой директориии и возвращает их в
	 * виде массива
	 *
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @param bool $fullpath Выводить полный путь или нет
	 * @return array
	 */
	public function getFiles($mask = null, $reverse = false, $fullpath = true)
	{
		$content = $this->_getContentDir(2, $mask, $reverse, $fullpath);

		return $content;
	}

	/**
	 * Метод рекурсивно считывает файлы в открытой директориии и
	 * возвращает их в виде массива
	 *
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @return array
	 */
	public function getFilesRecursive($mask = null, $reverse = false)
	{
		$result = $this->_getContentDirRecursive(2, $mask, $reverse);

		return $result;
	}

	/**
	 * Метод считывает файлы и поддиректории в открытой директориии и возвращает
	 * их в виде массива
	 *
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @param bool $fullpath Выводить полный путь или нет
	 * @return array
	 */
	public function getDirsAndFiles($mask = null, $reverse = false, $fullpath = true)
	{
		$content = $this->_getContentDir(0, $mask, $reverse, $fullpath);

		return $content;
	}

	/**
	 * Метод рекурсивно считывает файлы и поддиректории в открытой директориии и
	 * возвращает их в виде массива
	 *
	 * @param string $mask Маска
	 * @param bool $reverse Сортировать в прямом или обратном порядке. Если указан NULL - не соритировать
	 * @return array
	 */
	public function getDirsAndFilesRecursive($mask = null, $reverse = false)
	{
		$result = $this->_getContentDirRecursive(0, $mask, $reverse);

		return $result;
	}

	/**
	 * Метод создает указанную директорию с указанными правами доступа
	 *
	 * @param string $name Имя директории
	 * @param int $mode Права доступа директории
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return \Uwin\Fs
	 */
	public function makeDir($name, $mode = 0755)
	{
		if (false === mkdir($this->getPath() . $name) ) {
			throw new FsException('File system error: failure make dir "' . $path. '"', 1213);
		}

		if (false === chmod($this->getPath() . $name, $mode) ) {
			throw new FsException('File system error: failure change mode for directory "' . $path. '"', 1214);
		}

		return $this;
	}

	public function removeDir($path)
	{
		if(file_exists($path) && is_dir($path)) {
			$dirHandle = opendir($path);
			while(false!==($file = readdir($dirHandle))) {
				if($file!='.' && $file!='..') {
					$tmpPath = $path.'/'.$file;
					chmod($tmpPath, 0777);
					if(is_dir($tmpPath)) {
						RemoveDir($tmpPath);
					} else {
						unlink($tmpPath);
					}
				}
			}

			closedir($dirHandle);
			// удаляем текущую папку
			rmdir($path);
		}
	}
/*
	public function remove($name)
	{
		;
	}

	public function copy($source, $dest)
	{
		;
	}

	public function move($source, $dest)
	{
		;
	}
*/
}