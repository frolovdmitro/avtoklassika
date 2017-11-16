<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Exception\Validate, который отвеечает за обработку
 * исключений связанных с валидацией данных
 *
 * @category   Uwin
 * @package    Uwin\Exception
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Exception, к которому относится класс Validate
 */
namespace Uwin\Exception;

/**
 * Класс Uwin\Exception\Validate, который отвеечает за обработку исключений
 * связанных с валидацией данных
 *
 * @category  Uwin
 * @package   Uwin\Exception
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Validate extends \Uwin\Exception
{
	/**
	 *
	 * @var unknown_type
	 */
	private $_exceptions = array();

	public function addException($code, $text)
	{
		$this->_exceptions['exceptions'][$code][] = $text;

		return $this;
	}

	public function addExceptions($exceptions)
	{
		$this->_exceptions = array_merge( $this->_exceptions, array('exceptions' => $exceptions) );

		return $this;
	}

	public function getExceptions()
	{
		return $this->_exceptions;
	}
}