<?php
use \Uwin\Profiler;
use \Uwin\Registry;
use \Uwin\Autoloader;
use \Uwin\Config\Xml;
use \Uwin\Db;

  function _imgResize($src, $dest, $width, $height, $quality=80)
  {
    $path_info = pathinfo($src);

    // Если у файла указано расширение, получаем его
    if ( array_key_exists('extension', $path_info) ) {
      $ext = $path_info['extension'];
    }
    $extentions = array('jpg', 'jpeg', 'gif', 'png', 'bmp'); // Определяем фор

    if ( !in_array($ext, $extentions) ) {
      return false;
    }

    $size = getimagesize($src);

    if (false === $size) {
      return false;
    }

//    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
//    $icfunc = "imagecreatefrom" . $format;
//    if ( !function_exists($icfunc) ) {
//      return false;
//    }

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);

    $img = new Imagick($src);
    $img->thumbnailImage($new_width, $new_height, TRUE);
    $img->writeimage($dest);
    $img->clear();
    $img->destroy();
//    $isrc = $icfunc($src);
//    $idest = imagecreatetruecolor($new_width, $new_height);
//
//    imagecopyresampled($idest, $isrc, 0, 0, 0, 0,
//      $new_width, $new_height, $size[0], $size[1]);
//
//    // Создаем изображение
//    switch ($ext) {
//      case 'jpg':
//        imagejpeg($idest, $dest, $quality);
//        break;
//
//      case 'gif':
//        imagegif($idest, $dest);
//        break;
//
//      case 'png':
//        imagepng($idest, $dest);
//        break;
//
//      case 'bmp':
//        imagewbmp($idest, $dest);
//        break;
//    }
//
//    imagedestroy($isrc);
//    imagedestroy($idest);

    return true;
  }


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

$images = $db->query()->addSql('select dpt_image, replace(dpt_image, \'-bg\', \'-mini\') as new_image from details_autoparts_tbl where dpt_image is not null and dpt_image != \'\'')
  ->fetchResult(false);

foreach ($images as $image){
  if (file_exists('/data/www/avtoclassika.com/static-servers/s1.avtoclassika.com' . $image['dpt_image'])) {
    _imgResize('/data/www/avtoclassika.com/static-servers/s1.avtoclassika.com' . $image['dpt_image'],
      '/data/www/avtoclassika.com/static-servers/s1.avtoclassika.com' . $image['new_image'],
      90, 90);
    var_dump($image);
  }
}

