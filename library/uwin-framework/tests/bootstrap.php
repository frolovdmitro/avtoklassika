<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

setlocale(LC_ALL, 'ru_RU.Utf-8');
date_default_timezone_set("Europe/Kiev");

require_once('PHPUnit/Autoload.php');

$paths = array(
	dirname( dirname(__FILE__) ) . '/library/',
	get_include_path()
);

set_include_path( implode(PATH_SEPARATOR, $paths) );