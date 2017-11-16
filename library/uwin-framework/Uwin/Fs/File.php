<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Fs\File, который отвечает за работу с файлами
 * в файловой системе
 *
 * @category   Uwin
 * @package    Uwin\Fs
 * @subpackage File
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Fs, к которому относится класс File
 */
namespace Uwin\Fs;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Fs\Exception as FsException;

/**
 * Класс, который отвечает за работу с файлами в файловой системе
 *
 * @category   Uwin
 * @package    Uwin\Fs
 * @subpackage File
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class File
{
	/**
	 * Открыть файл только на чтение. Указатель перемещается в начало файла
	 * @var string
	 */
	const READ_ONLY = 'r';

	/**
	 * Открыть файл на чтение и запись. Указатель перемещается в начало файла
	 * @var string
	 */
	const READ_WRITE = 'r+';

	/**
	 * Открыть файл только на запись. Указатель перемещается в начало файла и
	 * файл очищается, если файл не существует, он будет создан
	 * @var string
	 */
	const WRITE_ONLY = 'w';

	/**
	 * Открыть файл на чтение и запись. Указатель перемещается в начало файла и
	 * файл очищается, если файл не существует, он будет создан
	 * @var string
	 */
	const RECREATE_READ_WRITE = 'w+';

	/**
	 * Открыть файл только на запись. Указатель перемещается в конец файла,
	 * если файл не существует, он будет создан
	 * @var string
	 */
	const READ_END_ONLY = 'a';

	/**
	 * Открыть файл на чтение и запись. Указатель перемещается в конец файла,
	 * если файл не существует, он будет создан
	 * @var string
	 */
	const APPEND = 'a+';

	/**
	 * Дескриптор открытого файла
	 * @var resource
	 */
	private $_handler = null;

	/**
	 * Полное имя открытого файла
	 * @var string
	 */
	private $_filename = null;

	/**
	 * Режим, в котором открыт файл
	 * @var string
	 */
	private $_mode = self::READ_WRITE;

	/**
	 * Метод удаляет завершающие переводы строки
	 *
	 * @param string $source Текст в котором нужно удалить последний перевод строки
	 * @return string
	 */
	private function _rtrim_nr($source)
	{
		if ( !empty($source) && "\n" === $source[strlen($source)-1] ) {
			$source = substr($source, 0, strlen($source)-1);
		}

		return $source;
	}

	/**
	 * Метод перемещает указательв файле на начало указанной строки
	 *
	 * @param int $line Номер строки
	 * @return bool
	 */
	private function _seekToLine($line)
	{
		// Если номер строки отсчитвается от начала файла
		if ( 0 <= $line ) {
			// Переводим курсор в начало файла
			fseek($this->_handler, 0, SEEK_SET);

			$counter = 0;
			// проходим в цикле до нужной строки
			while( $counter++ <= $line-1 ) {
				fgets($this->_handler);

				// если достигнут конец файла, значит строки с таким номером
				// нет, выходим с цикла
				if ( feof($this->_handler) ) {
					break;
				}
			}
		} else {
		// Если номер строки отсчитвается от конца файла
			$eol = false;
			$cursor = -1;

			// Считываем последний символ
			fseek($this->_handler, $cursor, SEEK_END);
			$char = fgetc($this->_handler);

			while ( 0 !== $line+1 ) {
				// Пропускаем символ перевода строки \n
				if ( "\n" === $char ) {
					fseek($this->_handler, --$cursor, SEEK_END);
					$char = fgetc($this->_handler);
				}

				// В цикле ищем начало строки
				while ( "\n" !== $char ) {
					fseek($this->_handler, --$cursor, SEEK_END);

					// Если достигнуто начало файла, значит строки с таким
					// номером нет, выходим с цикла
					if ( 0 === ftell($this->_handler) ) {
						$eol = true;
						break;
					}

					$char = fgetc($this->_handler);
				}

				++$line;

				if ($eol) {
					break;
				}
			}
		}

		return true;
	}

	/**
	 * Метод возвращает текст указанной строки, 0 первая строка, -1 последняя
	 * строка. length может принимать отрицательное значение, тогда записи будут
	 * возвращатся в обратном порядке
	 *
	 * @param int $line Номер строки в фале
	 * @param int $length Количество возвращаемых строк
	 * @return string
	 */
	private function _readLine($line, $length = 1)
	{
		// Если прочитать строку с начала файла
		if (0 <= $line) {
			$linetext = null;
			$counter = 0;
			$reverse = false;

			// Если указан отрицательный размер
			if (0 > $length) {
				// проверить не больше ли он номера строки
				if ( $line < abs($length) ) {
					if (0 == $line) {
						$length = 1;
					} else {
						$length = $line+1;
					}
					$line = 0;
				} else {
					$line = $line + $length + 1;
				}

				$reverse = true;
				$length = abs($length);
			}

			// Переводим курсор в начало файла
			fseek($this->_handler, 0, SEEK_SET);

			// проходим в цикле до нужной строки
			while( $counter++ <= $line ) {
				$linetext = fgets($this->_handler);

				// если достигнут конец файла, значит строки с таким номером
				// нет, выходим с цикла и возвращаем null
				if ( feof($this->_handler) ) {
					$linetext = null;
					break;
				}
			}

			// Если строка была получена
			if ( null !== $linetext ) {
				// Если нужно выбрать все последующие строки
				if (0 == $length) {
					while ( !feof($this->_handler) ) {
						$linetext .= fgets($this->_handler);
					}
				} else {
					--$length;

					while (0 !== $length && !feof($this->_handler) ) {
						if (!$reverse) {
							$linetext .= fgets($this->_handler);
						} else {
							$linetext = fgets($this->_handler) . $linetext;
						}

						--$length;
					}
				}
			}
		} else {
			// Если прочитать строку с конца файла
			$eol = false;
			$cursor = -1;
			$linetext = null;
			$current_line = $line;

			// Считываем последний символ
			fseek($this->_handler, $cursor, SEEK_END);
			$char = fgetc($this->_handler);

			while ( 0 !== $current_line ) {
				// Пропускаем символ перевода строки \n
				if ( "\n" === $char ) {
					fseek($this->_handler, --$cursor, SEEK_END);
					$char = fgetc($this->_handler);
				}

				// В цикле ищем начало строки
				while ( "\n" !== $char ) {
					fseek($this->_handler, --$cursor, SEEK_END);

					// Если достигнуто начало файла, значит строки с таким
					// номером нет, выходим с цикла
					if ( 0 === ftell($this->_handler) ) {
						$eol = true;
						break;
					}

					$char = fgetc($this->_handler);
				}

				++$current_line;

				if ($eol) {
					break;
				}
			}

			// если строка найдена
			if (0 === $current_line) {
				$linetext = fgets($this->_handler);

				// Если нужно выбрать все последующие строки
				if (0 == $length) {
					$result = false;
					--$line;
					while ( null !==  $result ) {
						$result = $this->_readLine($line, 1);
						$linetext .= $result;
						--$line;
					}
				} else {
					if (0 < $length) {
						for ($i = 1; $i <= $length-1; $i++) {
							$linetext .= $this->_readLine($line-$i, 1);
						}
					} else {
						for ($i = -1; $i >= $length+1; $i--) {
							if (0 <= $line-$i) {
								break;
							}
							$linetext .= $this->_readLine($line-$i, 1);
						}
					}
				}
			}
		}

		return $linetext;
	}

	/**
	 * Конструктор класса. При создании класса можно указать какой файл открыть
	 * и в каком режиме
	 *
	 * @param string $file Полное имя файла
	 * @param string $mode Режим, в котором открывается файл
	 * @return \Uwin\Fs\File
	 */
	public function __construct($file = null, $mode = null)
	{
		if (null != $file) {
			$this->open($file, $mode);
		}

		return $this;
	}

	/**
	 * Деструктор класса. Если был открыт файл, в деструкторе он закрывается
	 *
	 * @return void
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Метод открывает файл в указанном режиме
	 *
	 * @param string $file Полное имя файла
	 * @param string $mode Режим, в котором открывается файл
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return bool
	 */
	public function open($file, $mode = null)
	{
		// Если какой-то файл уже открыт, вызываем исключение
		if (null != $this->_handler) {
			throw new FsException('File system error: now opening file "' . $this->_filename . '"', 1202);
		}

		if (null != $mode) {
			$this->_mode = $mode;
		}

		// открываем файл, если произошла ошибка, вызываем исключение
		if ( !$this->_handler = @fopen($file, $this->_mode) ) {
			$this->_handler = null;
			throw new FsException('File system error: failure opening file "' . $file . '"', 1201);
		}
		$this->_filename = $file;

		return true;
	}

	/**
	 *  Метод создает файл
	 *
	 * @param string $file Полное имя файла
	 * @return bool
	 */
	public function create($file)
	{
		// создаем файл
		$this->open($file, self::WRITE_ONLY);
		// и закрываем его
		$this->close();

		return true;
	}

	/**
	 * Метод закрывает открытый файл и открывает новый файл в указанноме режиме,
	 * если режим не указан, открывает в том режиме, в котором был открыт
	 * предыдущий файл
	 *
	 * @param string $file Полное имя файла
	 * @param string $mode Режим, в котором открывается файл
	 * @return bool
	 */
	public function change($file, $mode = null)
	{
		$this->close();
		$this->open($file, $mode);

		return true;
	}

	/**
	 * Метод закрывает открытый файл
	 *
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return bool
	 */
	public function close()
	{
		// Если файл до этого не открыт, вызываем исключение
		if (null === $this->_handler) {
			return true;
		}

		// закрываем файл, если возникла ошибка при закрытии, вызываем
		// исключительную ситуацию
		if ( !fclose($this->_handler) ) {
			throw new FsException('File system error: failure close file', 1204);
		}

		$this->_handler = null;
		$this->_filename = null;

		return true;
	}

	/**
	 * Метод переоткрывает файл в новом режиме
	 *
	 * @return bool
	 */
	public function chmod($mode)
	{
		$this->change($this->filename, $mode);

		return true;
	}

	/**
	 * Метод читает файл целиком
	 *
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return string
	 */
	public function read()
	{
		// Если файл до этого не открыт, вызываем исключение
		if (null == $this->_handler) {
			throw new FsException('File system error: failure read file, not opening file', 1206);
		}

		if (false === ( $result = file_get_contents( $this->getName() ) ) ) {
			throw new FsException('File system error: failure read file "' . $this->getName() . '"', 1207);
		}

		$result = $this->_rtrim_nr($result);

		return $result;
	}

	/**
	 * Метод возвращает указанные строки с файла
	 *
	 * @param int $line Номер строки
	 * @param int $length = 1 Количество возвращаемых строк
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return string
	 */
	public function readLines($line, $length = 1)
	{
		// Если файл до этого не открыт, вызываем исключение
		if (null == $this->_handler) {
			throw new FsException('File system error: failure read file, not opening file', 1206);
		}

		$result = $this->_readLine($line, $length);

		$result = $this->_rtrim_nr($result);

		return $result;
	}

	/**
	 * Метод возвращает указанные строки с файла в виде массива
	 *
	 * @param int $line Номер строки
	 * @param int $length = 1 Количество возвращаемых строк
	 * @return array
	 */
	public function readLinesInArray($line, $length = 1)
	{
		$result = $this->readLines($line, $length);

		$result = explode("\n", $result);

		return $result;
	}

	/**
	 * Метод записывает текст в файл в текущую позицию
	 *
	 * @param string $string Текст, который нужно записать
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return bool
	 */
	public function write($string)
	{
		// Если файл до этого не открыт, вызываем исключение
		if (null == $this->_handler) {
			throw new FsException('File system error: failure write to file, not opening file', 1208);
		}

		if ( false === fwrite($this->_handler, $string) ) {
			throw new FsException('File system error: failure write to file "' . $this->getName() . '"', 1209);
		}

		return true;
	}

	/**
	 * Метод возвращает количество строк в файле
	 *
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return int
	 */
	public function getCountLines()
	{
		if (null == $this->_handler) {
			throw new FsException('File system error: not opening file', 1202);
		}

		$countLines = 0;

		while ( fgets($this->_handler) ) {
			++$countLines;
		}

		return $countLines;
	}

	/**
	 * Метод вставляет строку в  начало файла
	 *
	 * @param string $string Текст, который будет вставлен в файл
	 * @return bool
	 */
	public function prependLine($string)
	{
		// Проверяем, есть ли в конце текста перевод строки, чтобы не вставлять
		// его дважды
		$br = null;
		if ( 0 < strlen($string) && "\n" !== $string[strlen($string)-1] ) {
			$br = "\n";
		}

		// Получаем содержимое файла
		$old_content = $this->read();

		// Переходим на начало файла
		$this->_seekToLine(0);

		// Записываем новое содиержимое в файл
		$this->write($string . $br . $old_content);

		return true;
	}

	/**
	 * Метод вставляет строку в  начало файла
	 *
	 * @param string $string Текст, который будет вставлен в файл
	 * @return bool
	 */
	public function appendLine($string)
	{
		// Проверяем, есть ли в начале текста перевод строки, чтобы не вставлять
		// его дважды
		$br = null;
		if ( 0 < strlen($string) && "\n" !== $string[0] ) {
			$br = "\n";
		}

		// Переходим на конец файла
		$this->_seekToLine(-1);

		// Записываем новое содиержимое в файл
		$this->write($br . $string);

		return true;
	}

	/**
	 * Метод очищает файл
	 *
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return bool
	 */
	public function clear()
	{
		if (null == $this->_handler) {
			throw new FsException('File system error: not opening file', 1202);
		}

		if ( false === file_put_contents($this->getName(), '') ) {
			throw new FsException('File system error: failure clear file "' . $this->getName() . '"', 1210);
		}

		return true;
	}

	/**
	 * Метод урезает файл до указанного количества строк
	 *
	 * @param int $line Количество строк
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return bool
	 */
	public function truncate($line)
	{
		if (null == $this->_handler) {
			throw new FsException('File system error: not opening file', 1202);
		}

		// Если линия указана с конца файла, нужно уменьшить ее на 1, так как
		// -1 это конец файла
		if (0 > $line) {
			$line--;
		}

		// Перемещаемся в начало указанной строки и вычисляем адрес ее начала
		$this->_seekToLine($line);
		$cursor = ftell($this->_handler);

		// удаляем все содержимое после указанного адреса
		if ( false === ftruncate($this->_handler, $cursor) ) {
			throw new FsException('File system error: failure truncate file "' . $this->getName() . '"', 1211);
		}

		return true;
	}

	/**
	 * Метод возвращает полное имя открытого файла, включая адрес к нему
	 *
	 * @throws \Uwin\Fs\Exception Ошибка работы с файловой системой
	 * @return string
	 */
	public function getName()
	{
		if (null == $this->_handler) {
			throw new FsException('File system error: not opening file', 1202);
		}

		return $this->_filename;
	}

	/**
	 * Метод возвращает полный путь к каталогу, где расположен файл
	 *
	 * @return string
	 */
	public function getDirname()
	{
		return dirname( $this->getName() );
	}

	/**
	 * Метод возвращает имя открытого файла
	 *
	 * @return string
	 */
	public function getBasename()
	{
		return basename( $this->getName() );
	}

	/**
	 * Метод возвращает полное имя открытого файла, включая адрес к нему
	 *
	 * @return string
	 */
	public function getExtension()
	{
		$ext = null;

		// Получаем информацию о файле
		$path_info = pathinfo( $this->getName() );

		// Если у файла указано расширение, получаем его
		if ( array_key_exists('extension', $path_info) ) {
			$ext = $path_info['extension'];
		}

		return $ext;
	}

	/**
	 * Метод возвращает полное имя открытого файла, включая адрес к нему
	 *
	 * @param string $unit='b' Единица измерения размера файла. Может быть равно "b", "kb", "mb", "gb"
	 * @return float
	 */
	public function getFileSize($unit = 'b')
	{
		// Делитель по умолчанию
		$divider = 1;

		// Если указана несуществующая единица измерения, использовать байты
		if ( $unit != 'b' && $unit != 'kb' && $unit != 'mb' && $unit != 'gb') {
			$unit = 'b';
		}

		// Вычисление размера файла
		$size = filesize( $this->getName() );

		// Получение множителя
		if ('kb' == $unit) {
			$divider = 1024;
		} elseif ('mb' == $unit) {
			$divider = 1024 * 1024;
		} elseif ('gb' == $unit) {
			$divider = 1024 * 1024 * 1024;
		}

		// Рачет размера
		$size = round($size / $divider, 2);

		return $size;
	}
}
