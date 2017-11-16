<?php
/**
 * Uwin CMS
 *
 * Файл содержащий класс маршрутизации ошибки в нужную функцию, которая
 * обработает эту ошибку
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Exception\Validate as ValidateException;
use \Uwin\Exception\Route    as RouteException;
use \Uwin\Exception\Security as SecurityException;
use \Uwin\Exception\System   as SystemException;
use \Uwin\Controller\Front;
use \Uwin\Auth;

/**
 * Класс маршрутизации ошибки в нужную функцию, которая обработает эту ошибку
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Error {
    /**
     * Метод отливливает брошенное приложением исключение и определяет в какую
     * функцию переслать обработку данного типа исключений
     *
     * @param Exception $exception - Пойманное исключение
     *
     * @return bool
     */
	public static function catchException(Exception $exception)
	{
		// Если выброшено исключение маршрутизации
		if ( $exception instanceof RouteException ) {
			// И пользователь администратор, пересылать на страницу детальной
			// информации о ошибке
			if ( Auth::getInstance()->setStorageNamespace('UwinAuthAdmin')
									->hasIdentity() ) {
				$controller = Front::getInstance()
					->forward('default', 'index', 'error', array($exception));
			} else { // Иначе на страницу 404 ошибки
				$controller = Front::getInstance()
					->forward('default', 'index', 'notFound');
			}

			// Отрендерить страницу ошибки
			Front::render($controller);

			return true;
		}

		// Если выброшено исключение безопасности
		if ( $exception instanceof SecurityException ) {
			//TODO На будущее подумать как обрабатывать проблемы безопастности

			return true;
		}

		// Если выброшено исключение системное или неопределенное исключение
		// Перенапрвить на обработчик ошибки PHP
		$controller = Front::getInstance()->forward('default', 'index', 'error', array($exception));

		// Отрендерить страницу ошибки PHP
		Front::render($controller);

		return true;
	}
}