<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Controller\Router\Route\StaticRoute, который реализует
 * разбор статических правил маршрутизации
 *
 * @category   Uwin
 * @package    Uwin\Controller
 * @subpackage Router
 * @subpackage Route
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс StaticRoute
 */
namespace Uwin\Controller\Router\Route;

/**
 * Класс, который реализует разбор статических правил маршрутизации
 *
 * @category  Uwin
 * @package   Uwin\Controller
 * @subpackage Router
 * @subpackage Route
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class StaticRoute extends Abstract_
{
	/**
	 * Метод, который служит для разбора правила маршрутизации и который
	 * формирует все переменные маршрута
	 *
	 * @param array $valueVariable Массив переменных маршрута, переданных маршрутизатору
	 * @return bool|array Массив, правил маршрута к модулю/контроллеру/действию, содержащий все переменные маршрута
	 */
	public function match($valueVariable)
	{
		// Если массив переменных правила пуст, то значит это правило маршрутизации
		// подходит
		if ( empty($this->_rule[0]) ) {
			return $this->_routeRules;
		}

		// Если массив переменных правила савпадает с массивом переданных
		// значение маршрута, то значит это правило маршрутизации подходит
		if ($valueVariable == $this->_rule) {
			return $this->_routeRules;
		}

		return false;
	}
}
