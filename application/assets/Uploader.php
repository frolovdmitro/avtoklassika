<?php
/**
 * Uwin CMS
 *
 * Файл содержит модель модуля управления комплексами
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2012 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Registry           as Registry;
use \Uwin\Controller\Request as Request;
use \Uwin\Fs\File            as File;

/**
 * Модель модуля управления комплексами
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Uploader
{
    private function _imgResize($src, $dest, $width, $height, $quality=100)
   	{
   		$path_info = pathinfo($src);
        $ext = mb_strtolower($path_info['extension']);

   		// Если у файла указано расширение, получаем его
   		if ( array_key_exists('extension', $path_info) ) {
   			$ext = $path_info['extension'];
   		}
   		$extentions = array('jpg', 'gif', 'png', 'bmp'); // Определяем фор

   		if ( !in_array($ext, $extentions) ) {
   			return false;
   		}

   		$size = getimagesize($src);

   		if (false === $size) {
   			return false;
   		}

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

   		return true;
   	}

    private function _getVersionFile($path, $name, $ext) {
        $num_version = 1;

        while( file_exists($path . $name . '.' . $ext) ) {
            preg_match('#-v([0-9]+)\.#si', $name . '.', $version);
            if ( isset($version[1]) ) {
                $num_version = (int)$version[1]+1;
                $name = str_replace('-v' . ($num_version-1) . '.',
                                    '-v' . $num_version . '.',
                                    $name . '.');
                $name = rtrim($name, '.');
            } else {
                $name .= '-v' . $num_version;
            }
        }

        return $num_version;
    }

    public function uploadFile($file_name, $rel_path, $name, $use_version = true, $thumbnail = false, array $resize = array()) {
        $file = Request::getInstance()->files($file_name);

        if (null === $file) {
            return false;
        }

        $registry = Registry::getInstance();
        $root = $registry['path']['uploadDir'];
        $rel_root = $registry['url']['uploadDir'];
        $path_info = pathinfo($file['name']);
        $ext = mb_strtolower($path_info['extension']);

        $rel_path = trim($rel_path, '/');

        $def_suffix = null;
        if ( !empty($resize) ) {
            $def_suffix = '-bg';
        }

        $basenameFile = $name . $def_suffix;
        $pathFile = $root . $rel_path . '/' . $basenameFile . '.' . $ext;
        $relFile = $rel_root . $rel_path . '/' . $basenameFile . '.' . $ext;
        $versionFile = null;
        if ($use_version) {
            $versionFile = '-v1';
            $basenameFile = $name . $def_suffix . $versionFile;

            $num_version = $this->_getVersionFile($root . $rel_path . '/', $basenameFile, $ext);
            $versionFile = '-v' . $num_version;
            $basenameFile = $name . $def_suffix . $versionFile;
            $pathFile = $root . $rel_path . '/' . $basenameFile . '.' . $ext;
            $relFile = $rel_root . $rel_path . '/' . $basenameFile . '.' . $ext;
        }


        // Если директории нет, создаем ее рекурсивно
        if ( !file_exists($root . $rel_path) ) {
            mkdir($root . $rel_path, 0777, true);
        }

        move_uploaded_file($file['tmp_name'], $pathFile);

        // Если нужно создать иконку
        if ($thumbnail) {
            $dest_file = $root . $rel_path . '/.thm-' . $basenameFile . '.' . $ext;
            $this->_imgResize($pathFile, $dest_file, 90, 50, 50);
        }

        if ( !empty($resize) ) {
            foreach ($resize as $size) {
                $suffix = null;
                $prefix = null;
                if ( isset($size['suffix']) ) {
                    $suffix = '-' . $size['suffix'];
                }

                if ( isset($size['prefix']) ) {
                    $prefix = $size['prefix'] . '-';
                }

                $dest_file = $root . $rel_path . '/' . $prefix . $name . $suffix . $versionFile . '.' . $ext;
                $this->_imgResize($pathFile, $dest_file, $size['width'], $size['height'], 70);
            }
        }

        return $relFile;
    }

    public function saveFile($file, $data) {
        $registry = Registry::getInstance();
        $root = $registry['path']['uploadDir'];

        $file = $root . $file;

        if ( !file_exists( dirname($file) ) ) {
            mkdir(dirname($file), 0777, true);
        }

        if ( !empty($data) ) {
            $file = new File($file, 'w+');
            $file->write($data);
            $file->close();
        } else {
            if ( file_exists($file) ) {
                unlink($file);
            }
        }
    }
}