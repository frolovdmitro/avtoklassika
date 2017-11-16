<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Auth\Exception, который отвеечает за обработку исключений в классе
 * Uwin\Auth
 *
 * @category   Uwin
 * @package    Uwin\Auth
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Auth, к которому относится класс Exception
 */
namespace Uwin\Auth;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Exception\Validate as ValidateException;

/**
 * Класс Uwin\Auth\Exception, который отвеечает за обработку исключений в классе Uwin\Auth
 *
 * @category  Uwin
 * @package   Uwin\Auth
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Exception extends ValidateException
{
}