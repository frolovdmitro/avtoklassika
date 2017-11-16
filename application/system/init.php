<?php
/**
 * Uwin CMS
 *
 * Файл инициализирующий параметры окружения перед запуском приложения.
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

use \Uwin\Exception\Notice  as NoticeException,
    \Uwin\Exception\Warning as WarningException,
    \Uwin\Exception\System  as SystemException,
    \Uwin\Exception;

// Включаем директиву PHP - отображение всех ошибок
error_reporting(E_ALL & ~E_STRICT);
ini_set('display_errors', 1);
session_set_cookie_params(0, '/', COOKIE_HOST);

/**
 * Подключаем файл настроек, хранящий все глобальные переменные приложения
 * @var array $config
 */
require dirname(__DIR__) . DIR_SEP . 'settings' . DIR_SEP . 'config.php';

// Подключаем автолоадинг библиотек, установленных с помощью Composer
$autoload_file = $config['path']['vendor'] . 'autoload.php';
if ( file_exists($autoload_file) ) {
  require $autoload_file;
}

// Создаем строку путей, где будут расположены классы приложения
$paths = implode (PATH_SEP,
  [
    $config['path']['uwin-framework'],
    $config['path']['system'],
    $config['path']['assets'],
    '/usr/local/share/pear',
  ]
);

// И устанавливаем пути по которым происходит поиск подключаемых файлов
set_include_path($paths);

/**
 * Перехватывать ошибки php и преобразовывать их в нужные исключения
 *
 * @param int $errno     - id типа ошибки
 * @param string $errstr - текст ошибки
 *
 * @return void
 * @throws Exception
 * @throws SystemException
 * @throws NoticeException
 * @throws WarningException
 */
function throwException($errno, $errstr) {
  switch ($errno) {
    case E_NOTICE:
      throw new NoticeException($errstr, $errno);
      break;

    case E_WARNING:
      throw new WarningException($errstr, $errno);
      break;

    case E_ERROR:
      throw new SystemException($errstr, $errno);
      break;

    default:
      throw new Exception($errstr, $errno);
      break;
  }
}

// set_error_handler('throwException', E_ERROR|E_WARNING|E_PARSE);
