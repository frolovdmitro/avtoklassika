<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Controller\Router\Route\DynamicRoute, который
 * реализует разбор динамических правил маршрутизации
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
 * Объявляем пространсто имен Uwin, к которому относится класс DynamicRoute
 */
namespace Uwin\Controller\Router\Route;

/**
 * Класс, который реализует разбор динамического правил маршрутизации
 *
 * @category   Uwin
 * @package    Uwin\Controller
 * @subpackage Router
 * @subpackage Route
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class DynamicRoute extends Abstract_
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
		// Проверяем, если строка адреса состоит из большего числа переменных,
		// чем правило, возвращаем ложь
		if (count($valueVariable) > count($this->_rule) ) {
			return false;
		}

		$i = 0;
		// Проход по массиву переменных маршрута
		foreach ($this->_rule as $ruleVariable) {
			// Приводим значение переменной к текстовму виду
			if ( isset($valueVariable[$i]) ) {
				$valueVariable[$i] = (string)$valueVariable[$i];
			} else {
				$valueVariable[$i] = "";
			}
			// Проверяем, встречается ли в переменной символ ":"
			$posDelimeter = strpos($ruleVariable, ':');
			// Если символ ":" не найден
			if (false ===  $posDelimeter) {
				// Проверяем, если значение переменной маршрута не указано, а
				// если объявлено, но не равняется переменной маршрута, то
				// возвращаем ложь и выходим с функции
				if ($ruleVariable != $valueVariable[$i]) {
					return false;
				}
			} elseif (0 === $posDelimeter) { // Если символ ":" найден самым первым
				// Вырезаем символ ":" с имени переменной
				$ruleVariable = trim($ruleVariable, ':');
				// Если значение переменной указано, добавляем ее в результирующий массив
				if ( !empty($valueVariable[$i]) ) {
					$this->_routeRules[$ruleVariable] = $valueVariable[$i];
				} else {
					// Иначе, если значение переменной не определено в правилах
					// маршрутизации, добавляем в результирующий массив значение
					// этой переменной равной NULL
					if ( !array_key_exists($ruleVariable, $this->_routeRules) ) {
						$this->_routeRules[$ruleVariable] = null;
					}
				}
			} else { // Иначе, если символ ":" найден, но он не первый (например, /page:pageId)
				// Если значение указано
				if ( !empty($valueVariable[$i]) ) {
					// определяем префикс до символа ":" у имени переменной
					$prefixVariable = substr($ruleVariable, 0, $posDelimeter);
					// определяем префикс такой же длины, как $prefixVariable
					// у значения пееменной
					$prefixValue = substr($valueVariable[$i], 0, $posDelimeter);
					// Если префиксы имени переменной и ее значения не совпадают,
					// возвращаем ложь и выходим с функции
					if ($prefixValue !== $prefixVariable) {
						return false;
					} else {
						// Получаем имя переменной, следующее за префиксом и символом ":"
						$ruleVariable = substr($ruleVariable,
												  $posDelimeter+1,
												  strlen($ruleVariable)-$posDelimeter-1);
						// Получаем значение переменной, следующее за префиксом
						$valueVariable[$i] = substr($valueVariable[$i],
													   $posDelimeter,
											  		   strlen($valueVariable[$i])-$posDelimeter);

						// Если префикс значения переменной указан, а само
						// значение не указано, возвращаем ложь
						if ( empty($valueVariable[$i]) ) {
							return false;
						};

						// Добавляем переменную и ее значение в результирующий массив
						$this->_routeRules[$ruleVariable] = $valueVariable[$i];
					}
				} else {
					// Получаем имя переменной, следующее за префиксом и символом ":"
					$ruleVariable = substr($ruleVariable,
											  $posDelimeter+1,
											  strlen($ruleVariable)-$posDelimeter-1);
					// Проверяем, если значение переменной не определено в правилах
					// маршрутизации, добавляем в результирующий массив значение
					// этой переменной равной NULL
					if ( !array_key_exists($ruleVariable, $this->_routeRules) ) {
						$this->_routeRules[$ruleVariable] = null;
					}
				}
			}

			$i++;
		}

		return $this->_routeRules;
	}
}
