<?php
use \Uwin\Profiler;
use \Uwin\Registry;
use \Uwin\Autoloader;
use \Uwin\Config\Xml;
use \Uwin\Db;

const SERVER_ID = 's1';
// Установка локали по-умолчанию
setlocale(LC_ALL, 'ru_RU.Utf-8');
// Установка временной зоны по-умолчанию
date_default_timezone_set("Europe/Kiev");

mb_internal_encoding('UTF-8');

/**
 * Инициализация и первоначальная настройка профайлера
 * @noinspection PhpIncludeInspection
 */
include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'library' .
         DIRECTORY_SEPARATOR . 'Uwin' . DIRECTORY_SEPARATOR .'Profiler.php';

/** @noinspection PhpIncludeInspection */
require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'application' .
       DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'init.php';

/** @noinspection PhpIncludeInspection */
require 'Uwin' . DIR_SEP . 'Autoloader.php';

Autoloader::register();

function getDbParams($config) {
    $xml = new Xml($config['path']['settings'] . 'general.default.xml', '/root/databases/default');

    return $xml->get();
}


$db = Db::db()->setDbParams( getDbParams($config) );

$images = $db->query()
  ->addSql('select dpt_id_pk as id, dpt_image as image, replace(dpt_image, \'-bg\', \'-sm\') as image_small, car_synonym as car')
  ->addSql('from details_autoparts_tbl')
  ->addSql('left join autoparts_tbl on dpt_apt_id_fk = apt_id_pk')
  ->addSql('left join cars_tbl on apt_car_id_fk = car_id_pk')
  ->addSql('where dpt_image is not null and dpt_image != \'\' and car_synonym is not null order by car_order')
  ->fetchResult(false);

$root = $config['path']['static_server'];
$root = rtrim($root, '/');
$rootDest = $config['path']['static_server'] . 'uploads/tmp/all-details/';

// echo $root;
// echo count($images);
foreach ($images as $row) {
  if (!file_exists($rootDest . $row['car'])) {
    mkdir($rootDest . $row['car'], 0777, true);
  }
  // var_dump($root . $row['image']);
  if (!file_exists($root . $row['image'])) {
    var_dump($root . $row['image']);
    continue;
  }

  copy($root . $row['image'], $rootDest . $row['car'] . '/' . $row['id'] . '.jpg');
  copy($root . $row['image_small'], $rootDest . $row['car'] . '/' . $row['id'] . '-SMALL.jpg');
}
