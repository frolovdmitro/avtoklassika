<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Controller\Router\Route\Abstract_, который является
 * общим для всех классов, реализующих разбор правил маршрутизации
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
 * Объявляем пространсто имен Uwin, к которому относится класс Abstract_
 */
namespace Uwin\Controller\Router\Route;

/**
 * Класс, который является общим для всех классов, реализующих разбор правил
 * маршрутизации
 *
 * @category   Uwin
 * @package    Uwin\Controller
 * @subpackage Router
 * @subpackage Route
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
abstract class Abstract_
{
	/**
	 * Массив, содержащий элементы разобранной строки маршрута
	 * (например events/:categoryId/:newsId)
	 * @var array
	 */
	protected  $_rule  = array();

	/**
	 * Массив, правил маршрута к модулю/контроллеру/действию, содержащий все
	 * переменные маршрута
	 * @var array
	 */
	protected  $_routeRules = array();

    /**
     * Массив тегов кеша, которые относятся к маршуруту. Если теги указаны, то
     * это означает что кеширование маршрута выполняется
     * @var array
     */
    private $_cacheTags = array();

	/**
	 * Объщий конструктор для всех классов, реализующих разбор правил
	 * маршрутизации. Выполняет инициализацию переменных
	 *
	 * @param string $rule Cтрока правила маршрута (например events/:categoryId/:newsId)
	 * @param array $route Массив, правил маршрута к модулю/контроллеру/действию
     * @param array $tags = array() - Массив тегов для кеширвоания
	 *
	 * @return Abstract_
	 */
	public function __construct($rule, $route = array(), array $tags = array())
	{
		$this->_rule = explode('/', $rule);

		$this->_routeRules = $route;

        $this->_cacheTags = $tags;

		return $this;
	}

	/**
	 * Абстрактный метод, который служит для разбора правила маршрутизации и
	 * который формирует все переменные маршрута
	 *
	 * @param array $valueVariable Массив переменных маршрута, переданных маршрутизатору
	 * @return bool|array Массив, правил маршрута к модулю/контроллеру/действию, содержащий все переменные маршрута
	 */
	abstract public function match($valueVariable);

    public function getCacheTags() {
        return $this->_cacheTags;
    }
}
