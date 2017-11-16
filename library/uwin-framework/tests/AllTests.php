<?php

use \PHPUnit_Framework_TestSuite as PHPUnit_TestSuite;
use \Uwin\Xml\AllTests    as XmlAllTests;

/** @noinspection PhpIncludeInspection */
require_once 'Uwin/Xml/AllTests.php';

class AllTests {
	public static function suite() {
		$suite = new PHPUnit_TestSuite('UwinFramework');
		// Добавляем набор тестов
		$suite->addTest( XmlAllTests::suite() );

		return $suite;
	}
}
