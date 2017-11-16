<?php
/**
 * Uwin CMS
 *
 * Файл содержащий модель модуля по умолчанию, который обрабатывает:
 *  - Главную страницу
 *  - Статическую страницу
 *  - Страницы ошибок
 *  - Страницу 404
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_;
use \Uwin\Registry;
use \Uwin\Config\Xml;
use \Uwin\Exception\Notice   as NoticeException;
use \Uwin\Exception\Warning  as WarningException;
use \Uwin\Exception\System   as SystemException;
use \Uwin\Exception\Route    as RouteException;
use \Uwin\Exception\Validate as ValidateException;
use \Uwin\Exception\Security as SecurityException;
use \Uwin\Db\Exception       as DbException;

/**
 * Модель модуля по умолчанию, который обрабатывает:
 *  - Главную страницу
 *  - Статическую страницу
 *  - Страницы ошибок
 *  - Страницу 404
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Errors extends Abstract_
{
	/**
	 * Метод определяет и возвращает тип исключения
	 *
	 * @param Exception $exception - Исключение
	 *
	 * @return string
	 */
	private function _getErrorType(Exception $exception) {
		$type = 'UNDEFINED ERROR';
		if ($exception instanceof WarningException) {
			$type =  'PHP WARNING';
		}
		elseif ($exception instanceof NoticeException) {
			$type =  'PHP NOTICE';
		}
		elseif ($exception instanceof SystemException) {
			$type =  'PHP SYSTEM ERROR';
		}
		elseif ($exception instanceof RouteException) {
			$type =  'PHP ROUTE ERROR';
		}
		elseif ($exception instanceof ValidateException) {
			$type =  'PHP VALIDATE ERROR';
		}
		elseif ($exception instanceof SecurityException) {
			$type =  'PHP SECURITY ERROR';
		}
		elseif ($exception instanceof DbException) {
			$type =  'PHP DATABASE ERROR';
		}

		return $type;
	}

	/**
	 * Метод проверяет нужно логировать данный тип переданной ошибки и если
	 * нужно, сохраняет информацию о ней в указанный файл
	 * 
	 * @param array $variables - массив переменных описывающих исключение
	 *
	 * @return Index
	 */
	private function _saveErrorToLog($variables) {
		//TODO Сделать логирование ошибок

		return $this;
	}

	/**
	 * Метод проверяет нужно отправлять информацию на email о данном типе
	 * переданной ошибки и если нужно, отправляет на указанные email'ы
	 *
	 * @param array $variables - массив переменных описывающих исключение
	 *
	 * @return Index
	 */
	private function _sendErrorToEmails($variables) {
		// Сделать отправку на email ошибок
		return $this;
	}

	/**
	 * Метод формирует переменные страницы ошибки 404, которые будут переданы
	 * шаблонизатору
	 *
	 * @return \Index
	 */
	public function getNotFound() {
		$this->setVariable( 'title', $this->getVariable('lng_error404_title') );

		return $this;
	}

	/**
	 * Метод формирует переменные страницы внутренней ошибки, которые будут
	 * переданы шаблонизатору
	 *
	 * @return \Index
	 */
	public function getError() {
		return $this;
	}

	/**
	 * Метод формирует переменные страницы "Технические работы", которые будут
	 * переданы шаблонизатору
	 *
	 * @return \Index
	 */
	public function getMaintenance() {
		return $this;
	}

	/**
	 * Метод анализирует информацию о выброшенном исключении и подготавливает
	 * данные для сохранения, отправки на email'ы и выводе на странице
	 * информации о ошибке
	 * 
	 * @param Exception $exception - Исключение
	 *
	 * @return Index
	 */
	public function getDevError(Exception $exception) {
		$trace   = $exception->getTrace();

		// Получаем имя файла и номер строки, где произошла ошибка
		$file = $exception->getFile();
		$line = $exception->getLine();
		if (array_key_exists('function', $trace[0]) &&
			'throwException' == $trace[0]['function']) {
			if ( array_key_exists('file', $trace[0]) ) {
				$file    = $trace[0]['file'];
				$line    = $trace[0]['line'];
			} else {
				$file    = $trace[1]['file'];
				$line    = $trace[1]['line'];
			}
		}
		$result['file'] = $file;
		$result['line'] = $line;

		// Получаем тип ошибки
		$result['type'] = $this->_getErrorType($exception);

		// Получаем сообщение о ошибке
		$message = highlight_string('<?php ' . $exception->getMessage() . '?>',
									true);
		$result['message'] = preg_replace('#(&lt;\?php)?(\?&gt;)?#s','',
										  $message);

		// Получаем URL страницы, которая вызвала ошибку
		$request = $this->getRequest();
		$result['url'] = $request->getHost(true). $request->getCurrentUrlWithGets();

		// Получаем стек вызовов функций до ошибки
		$file = null;
		foreach ($trace as $key => $value) {
			if ( !isset($value['file']) && null !== $file ) {
				$value['file'] = $file;
			}

			if ( !isset($value['file']) ) {
				continue;
			}

			$breakpoint = array();
			$breakpoint['file'] = $value['file'];
			if ( isset($value['line']) ) {
				$breakpoint['line'] = $value['line'];
			}

			$class = null;
			$type  = null;
			if ( isset($value['class']) ) {
				$class = $value['class'];
				$type  = $value['type'];
			}

			$code = highlight_string('<?php ' . $class . $type
									 . $value['function'] . '();' . '?>', true);

			$breakpoint['code'] = preg_replace('#(&lt;\?php)?(\?&gt;)?#s','',
										  $code);

			$result['stack'][] = $breakpoint;

			if ( isset($value['file']) ) {
				$file = $value['file'];
			}
		}

		//TODO Выводить GET, POST, COOKIE, SESSION переменные, где случилась ошибка
		$getVariables = $request->get();
		$postVariables = $request->post();

		// запись информации о ошибке в лог-файл
		$this->_saveErrorToLog($result);

		// отправка информации о ошибке на e-mail`ы
		$this->_sendErrorToEmails($result);

		// Передаем все переменные о ошибке в шаблонизатор
		$this->setVariables($result);

		return $this;
	}

	/**
	 * Метод возвращает тексты ошибок указанной формы
	 * 
	 * @return \Index
	 */
	public function getErrorsTexts() {
		$request = $this->getRequest();
		$registry = Registry::getInstance();

		// Получаем Имя модуля и формы
		$module = $request->getParam('moduleName');
		$form = $request->getParam('form');

		// Определение каталога, где размещен языковый файл модуля
		$dirLanguage = $this->getVariable('path_modules') . $module
					   . DIR_SEP . 'languages' . DIR_SEP ;

		// Определение каталога, где размещен языковый файл модуля со значениями
		// по умолчанию
		$dirDefaultLanguage = $dirLanguage . 'default' . DIR_SEP;

		// Решаем какой языковый файл использовать
		$language = $this->getVariable('stg_language');

		// Определение полного имени языкового файла модуля со значениями
		// по-умолчанию
		$fileDefaultLanguage = $dirDefaultLanguage . $language . '.xml';

		//TODO Сделать запись резутата в кешер
		
		// Определение полного имени языкового файла модуля
		$fileLanguage = $dirLanguage . $language . '.xml';

		// Получаем путь к узлу с текстами ошибок нужной формы
		$xmlPathToErrors = '/' . $module . '/forms/' . $form . '/errors';

		$config_default_values = array();
		if ( file_exists($fileDefaultLanguage) ) {
			// Загрузка перемнных конфига модуля с xml файла и преобразование их
			// в многомерный ассоцеативный массив
			$configLoader = new Xml($fileDefaultLanguage, $xmlPathToErrors);
			$config_default_values = $registry->getFlatArray( $configLoader->get() );
		}

		$config_values = array();
		if ( file_exists($fileLanguage) ) {
			// Загрузка перемнных конфига модуля с xml файла и преобразование их
			// в многомерный ассоцеативный массив
			$configLoader = new Xml($fileLanguage, $xmlPathToErrors);
			$config_values = $registry->getFlatArray( $configLoader->get() );
		}

		$config_values = array_merge($config_values, $config_default_values);

		$this->setVariable('result', $config_values);
		
		return $this;
	}
}
