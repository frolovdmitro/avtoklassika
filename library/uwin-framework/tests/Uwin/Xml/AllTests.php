<?php
namespace Uwin\Xml;

use \PHPUnit_Framework_TestSuite as PHPUnit_TestSuite;

/** @noinspection PhpIncludeInspection */
require_once 'Uwin/XmlTest.php';

class AllTests {
	public static function suite() {
		$suite = new PHPUnit_TestSuite('UwinFramework - Uwin/Xml');
		// Добавляем тест в набор
		$suite->addTestSuite('\Uwin\XmlTest');

		return $suite;
	}

	protected function setUp() {
	}

	protected function tearDown() {
	}
}
