<?php
/**
 * UwinCMS
 *
 * Файл настроек, в котором объявляется массив $config, содержащий все
 * глобальные переменные приложения
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Путь к корню сайта
$root = dirname( dirname(__DIR__) ) . DIR_SEP;
// Адрес сайта
$baseUrl = '/';
// Адрес сайта со статикой
$staticServer = '//' . STATIC_SERVER_ID . '.' . SERVER_NAME;
// путь к сайту со статикой
$staticServerPath = $root . 'static-servers' . DIR_SEP . STATIC_SERVER_ID
  . '.' . PROJECT_NAME . DIR_SEP;

// Объявление массива, содержащего все глабальные переменные приложения
$config = [
  // Определение физических путей
  'path' => [
    // Путь к корню сайта
    'root'          => $root,
    // Путь к document_root
    'public'        => $root . 'public' . DIR_SEP,
    // Путь к директории со статикой
    'static_server' => $staticServerPath,
    // Путь к приложению
    'application'   => $root . 'application' . DIR_SEP,
    // Путь к системным файлам
    'system'        => $root . 'application' . DIR_SEP . 'system' . DIR_SEP,
    // Путь к дополнительным файлам приложения
    'assets'        => $root . 'application' . DIR_SEP . 'assets' . DIR_SEP,
    // Путь к компонентам
    'components'    => $root . 'application' . DIR_SEP . 'components' . DIR_SEP,
    // Путь к библиотекам
    'library'       => $root . 'library' . DIR_SEP,
    // Путь к библиотекам установленным с помощью Composer
    'vendor'       => $root . 'vendor' . DIR_SEP,
    // Путь к фреймворку
    'uwin-framework'=> $root . 'library' . DIR_SEP . 'uwin-framework' . DIR_SEP,
    // Путь к конфигурационным файлам
    'settings'      => $root . 'application'  . DIR_SEP . 'settings' . DIR_SEP,
    // Путь к пользовательским конфигурационным файлам
    'userSettings'  => $root . 'settings' . DIR_SEP,
    // Путь к шаблонам макетов
    'layout'        => $root . 'application'  . DIR_SEP . 'views' . DIR_SEP . 'layouts' . DIR_SEP,
    // Путь к минифицированным шаблонам макетов
//    'layoutMin'     => $root . 'build' . DIR_SEP . 'views' . DIR_SEP . 'layouts' . DIR_SEP,
      'layoutMin'     => $root . 'application' . DIR_SEP . 'views' . DIR_SEP . 'layouts' . DIR_SEP,
    // Путь к общим шаблонным скриптам
    'viewscript'    => $root . 'application'  . DIR_SEP . 'views' . DIR_SEP . 'scripts' . DIR_SEP,
    // Путь к минифицированным общим шаблонным скриптам
//      'viewscriptMin' => $root . 'build' . DIR_SEP . 'views' . DIR_SEP . 'scripts' . DIR_SEP,
    'viewscriptMin' => $root . 'application' . DIR_SEP . 'views' . DIR_SEP . 'scripts' . DIR_SEP,
    // Путь к директории с модулями
    'modules'       => $root . 'application'  . DIR_SEP . 'modules' . DIR_SEP,
    // Путь к директории с общими языковыми файлами
    'languages'     => $root . 'application'  . DIR_SEP . 'languages' . DIR_SEP,
    // Пути к минифицированным файлам
    'build' => [
      // шаблонам модулей
//      'modules'     => $root . 'build' . DIR_SEP . 'views' . DIR_SEP . 'modules' . DIR_SEP,
        'modules'     => $root . 'application' . DIR_SEP . 'modules' . DIR_SEP,
      'components'     => $root . 'build' . DIR_SEP . 'views' . DIR_SEP . 'components' . DIR_SEP,
    ],
    // Путь к директории с файлами сессий
    'sessions'      => DIR_SEP . 'tmp' . DIR_SEP,
    // Путь к директории с файлами журнала
    'logs'          => $root . 'logs'  . DIR_SEP,
    // Пути к каталогу, где расположены файлы которые заливаются на сервер
    'uploadDir'      => $staticServerPath . 'uploads' . DIR_SEP,
    'upload'        => [
      //Директория для заливаемых изображений
      'images' => $staticServerPath . 'uploads' . DIR_SEP . 'images' . DIR_SEP,
      //Директория для заливаемых видеофайлов
      'videos' => $staticServerPath . 'uploads' . DIR_SEP . 'videos' . DIR_SEP,
      //Директория для заливаемых файлов
      'files'  => $staticServerPath . 'uploads' . DIR_SEP . 'files' . DIR_SEP,
    ],
  ],

  // Определение URL адресов
  'url' => [
    // Адрес корня сайта
    'base'             => $baseUrl,
    'staticServer'     => $staticServer,
    // Адрес директории с стилями сайта
    'css'              => $baseUrl . 'css/',
    // Адрес директории с минифицированными стилями сайта
    'cssMin'           => $staticServer . '/css/',
    // Адрес директории с javascript файлами
    'js'               => $baseUrl . 'js/',
    // Адрес директории с минифицированными javascript файлами
    'jsMin'            => $staticServer . '/js/',
    // Адрес директории с изображениями сайта
    'images'           => $baseUrl . 'img/',
    // Адрес директории с изображениями панели управления
    'imageAdmin'       => $baseUrl . 'img/backend/',
    // Адрес директории с изображениями модулей панели управления
    'imageAdminModule' => $baseUrl . 'img/backend/modules/',
    // Адрес директории, где расположены файлы которые заливаются на сервер
    'uploadDir'        => '/uploads/',
    'upload'           => [
      // Адрес директории с загруженными изображеиями
      'images' => '/uploads/images/',
      // Адрес директории с загруженными видеофайлами
      'videos' => '/uploads/videos/',
      // Адрес директории с загруженными файлами
      'files'  => '/uploads/files/',
    ],
  ],
];
