<?php
// phpinfo();
/**
 * UwinCMS
 *
 * Главный файл CMS. Единая точка входа для всего приложения. Все запросы
 * перенаправляются на этот файл с помощью mod_rewrite. Задача данного файла:
 * инициализировать переменные окружения и запуск приложения.
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Profiler,
    \Uwin\Registry,
    \Uwin\Auth;

// Объявляем глобальные константы
const PROJECT_NAME     = 'avtoclassika.com',
      STATIC_SERVER_ID = 's1';

define('SERVER_NAME', $_SERVER['SERVER_NAME']);
define('COOKIE_HOST', '.' . SERVER_NAME);
define('DIR_SEP', DIRECTORY_SEPARATOR);
define('PATH_SEP', PATH_SEPARATOR);

// Установка локали по-умолчанию
setlocale(LC_ALL, 'ru_RU.UTF-8');
// Установка временной зоны по-умолчанию
date_default_timezone_set("Europe/Kiev");
mb_internal_encoding('UTF-8');
// error_reporting(E_ALL);

/**
 * Инициализация и первоначальная настройка профайлера
 */
include dirname(__DIR__) . DIR_SEP . 'library' .  DIR_SEP . 'uwin-framework'
  . DIR_SEP . 'Uwin' . DIR_SEP .'Profiler.php';

$profiler = Profiler::getInstance()
  ->setIgnoreTypeOperations('Autoload')
  ->startCheckpoint('Request',
    date('H:i:s d.m.Y') . ' | Time request ' . urldecode($_SERVER['REQUEST_URI'])
  );

/**
 * Подключение файла, инициализирующего переменные окружения
 * @var $config
 */
require dirname(__DIR__) . DIR_SEP . 'application' .  DIR_SEP . 'system'
  . DIR_SEP . 'init.php';

/**
 * Подключение и создание главного системного класса, выполняющего настройку
 * и запуск приложения
 */
require 'Bootstrap.php';

$bootstrap = new Bootstrap();
$bootstrap->run($config);

$profiler->stopCheckpoint();

$registry = Registry::getInstance();
if ('true' == $registry['stg']['profile']['enabled']) {
  $logInFile = false;
  if ('true' == $registry['stg']['profile']['logInFile']) {
    $logInFile = true;
  }

  $profiler->setLogInFile($logInFile)
    ->setLogFile($registry['stg']['profile']['file'])
    ->saveStats();

  $storage = Auth::getInstance()
    ->setStorageNamespace('UwinAuthAdmin')
    ->getStorage();

  if ($storage->printProfile) {
    $profiler->printStatsHtml();
  }
}
