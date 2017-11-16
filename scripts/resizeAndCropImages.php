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


$images = $db->query()->addSql("select adv_id_pk as id, adv_image as image from adverts_tbl where adv_image is not null and adv_image != ''")
  ->fetchResult(false);

foreach ($images as $image){
  $file = '/data/www/newest.avtoclassika.com/static-servers/s1.avtoclassika.com' .
    $image['image'];
  var_dump($file);
  if ( file_exists($file) ) {
    $file_vars = pathinfo($file);
    $dir = str_replace('adverts', 'ads', $file_vars['dirname']);
    $ext = $file_vars['extension'];
    $new_rel_file = '/' . $image['id'] . '-bg.' . strtolower($ext);
    $dir = str_replace('av.com', 'newest.avtoclassika.com', $dir);
    $dir = str_replace('/img/', '/images/', $dir);
    $new_file = $dir . $new_rel_file;
    $new_file = str_replace('av.com', 'newest.avtoclassika.com', $new_file);
    // var_dump($file);

    $db->query()->addSql("update adverts_tbl set adv_image = $2 where adv_id_pk = $1")
      ->addParam($image['id'])
      ->addParam( str_replace('/data/www/av.com/static-servers/s1.avtoclassika.com', '', $new_file) )
      ->execute();
    // if (!file_exists($dir)) {
    //   mkdir($dir, 0777, true);
    // }
    // copy($file, $new_file);

    try {
    $new_medium_file = $dir . '/' . $image['id'] . '-md.' . strtolower($ext);
    $im = new imagick($file);
    $im->cropThumbnailImage(300, 230);
    $im->writeImage($new_medium_file);

    $new_small_file = $dir . '/' . $image['id'] . '-sm.' . strtolower($ext);
    $im = new imagick($file);
    $im->cropThumbnailImage(222, 152);
    $im->writeImage($new_small_file);

    $new_mini_file = $dir . '/' . $image['id'] . '-mini.' . strtolower($ext);
    $im = new imagick($file);
    $im->cropThumbnailImage(193, 132);
    $im->writeImage($new_mini_file);

    $new_micro_file = $dir . '/' . $image['id'] . '-micro.' . strtolower($ext);
    $im = new imagick($file);
    $im->cropThumbnailImage(180, 123);
    $im->writeImage($new_micro_file);

    $new_thm_file = $dir . '/' . $image['id'] . '-thm.' . strtolower($ext);
    $im = new imagick($file);
    $im->thumbnailImage(60, 60, true);
    $im->writeImage($new_thm_file);

    $new_thmadm_file = $dir . '/.thm-' . $image['id'] . '-bg.' . strtolower($ext);
    $im = new imagick($file);
    $im->thumbnailImage(75, 75, true);
    $im->writeImage($new_thmadm_file);

    var_dump($new_file);
    var_dump($new_medium_file);
    var_dump($new_small_file);
    } catch (\Exception $e) {
      var_dump('!!!!!!' . $file);
    }

  }
}

// // доп фотки
// $images = $db->query()->addSql("select ada_id_pk as id, ada_adv_id_fk as adv_id, replace(ada_image, 'images', 'img') as image from adverts_attachments_tbl where ada_image like '%def_%'")
//   ->fetchResult(false);
// $i = 1;
// foreach ($images as $image){
//   $file = '/data/www/av.com/static-servers/s1.avtoclassika.com' .
//     $image['image'];
//   if ( file_exists($file) ) {
//     try{
//     $file_vars = pathinfo($file);
//     $dir = str_replace('adverts', 'ads', $file_vars['dirname']);
//     $dir = str_replace('av.com', 'newest.avtoclassika.com', $dir);
//     $dir = str_replace('/img/', '/images/', $dir);
//     $ext = $file_vars['extension'];
//     $new_rel_file = '/' . $image['id'] . '-bg.' . strtolower($ext);
//     $new_file = $dir . $new_rel_file;
//     $db->query()->addSql("update adverts_attachments_tbl set ada_image = $2 where ada_id_pk = $1")
//       ->addParam($image['id'])
//       ->addParam( str_replace('/data/www/newest.avtoclassika.com/static-servers/s1.avtoclassika.com', '', $new_file) )
//       ->execute();
//     if (!file_exists($dir)) {
//       mkdir($dir, 0777, true);
//     }
//     copy($file, $new_file);
//     $new_small_file = $dir . '/' . $image['id'] . '-mini.' . strtolower($ext);
//     $im = new imagick($file);
//     $im->thumbnailImage(60, 60, true);
//     $im->writeImage($new_small_file);
//
//     $new_thmadm_file = $dir . '/.thm-' . $image['id'] . '-bg.' . strtolower($ext);
//     $im = new imagick($file);
//     $im->thumbnailImage(75, 75, true);
//     $im->writeImage($new_thmadm_file);
//     var_dump($file);
//     var_dump($new_file);
//     var_dump(str_replace('/data/www/newest.avtoclassika.com/static-servers/s1.avtoclassika.com', '', $new_file));
//     var_dump($i);
//     $i++;
//     } catch (\Exception $e) {
//       var_dump('!!!!!!' . $file);
//     }
//   } else {
//     // var_dump($file);
//     $i++;
//   }
// }
