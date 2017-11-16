<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\XmlTest
 *
 * @category  Uwin
 * @package   Uwin\XmlTest
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Xml
 */
namespace Uwin;

require_once 'Uwin/Config/Xml.php';
require_once 'Uwin/Config/Xml/Exception.php';


// Объявление псевдонимов для всех используемых классов в данном файле
use \PHPUnit_Framework_TestCase as PHPUnitTestCase;
use \Uwin\Config\Xml            as Xml;
use \Uwin\Config\Xml\Exception  as XmlException;

class XmlTest extends PHPUnitTestCase
{
	public static $fileName;

	public static $general0;
	public static $general1Title;
	public static $general1;
	public static $data0;

	public static $data1;
	public static $data1WithAttr;

	public static $data2Var0;
	public static $data2Var1;
	public static $data2Var1WithAttr;
	public static $data2Var2;
	public static $data2Var2WithAttr;
	public static $data2;
	public static $data2WithAttr;

	public static $data3Var0;
	public static $data3Var1;
	public static $data3Var1WithAttr;
	public static $data3Var2;
	public static $data3Var2WithAttr;
	public static $subvar0;
	public static $subvar0WithAttr;
	public static $subvar1;
	public static $subvar1WithAttr;
	public static $data3Var3;
	public static $data3Var3WithAttr;
	public static $data3;
	public static $data3WithAttr;

	public static $root;
	public static $rootWithAttr;
	public static $head;
	public static $headWithAttr;

	/**
	 * Переменная в которой хранится ссылка на созданный экземпляр
	 * класса \Uwin\Config\Xml
	 * @var \Uwin\Config\Xml
	 */
	protected $_fixture = null;

	/**
	 * Метод устанавливаем все переменные массивы, которые соответствуют
	 * узлам XML
	 *
	 * @static
	 * @return void
	 */
	public static function startUp() {
		self::$fileName = dirname(dirname(__FILE__)) . '/resources/Xml/data.xml';
		
		self::$general1Title = 'Проверка связи';
		self::$general0 = array( 'country' => array('Украина', 'Россия') );
		self::$general1 = array('title' => self::$general1Title, 'var' => null);
		self::$data0    = array( 'general' =>
								 array(self::$general0, self::$general1) );

		self::$data1         = 'Проверка&связи №2';
		self::$data1WithAttr = array('attr1' => 'value', 'value' => self::$data1);

		self::$data2Var0         = 'Test1';
		self::$data2Var1         = null;
		self::$data2Var1WithAttr = array('attr' => 'Test2');
		self::$data2Var2         = 'ValuesTest3';
		self::$data2Var2WithAttr = array('attr' => 'Test3',
										 'value' => self::$data2Var2);
		self::$data2             = array( 'var' =>
										  array(self::$data2Var0,
												self::$data2Var1,
												self::$data2Var2) );
		self::$data2WithAttr     = array( 'var' =>
										  array(self::$data2Var0,
												self::$data2Var1WithAttr,
												self::$data2Var2WithAttr) );

		self::$data3Var0         = 'Test1';
		self::$data3Var1         = null;
		self::$data3Var1WithAttr = array('attr' => 'Test2');
		self::$data3Var2         = 'ValuesTest3';
		self::$data3Var2WithAttr = array('attr' => 'Test3',
										 'value' => self::$data3Var2);
		self::$subvar0           = 'test subvar';
		self::$subvar0WithAttr   = array('attr' => 'sbv',
										 'value' => self::$subvar0);
		self::$subvar1           = 'test subvar';
		self::$subvar1WithAttr   = array('attr' => 'sbv',
										 'value' => self::$subvar1);
		self::$data3Var3         = array('subvar' =>
										 array(self::$subvar0, self::$subvar1));
		self::$data3Var3WithAttr = array('attr' => 'TestVar2',
										 'subvar' => array(
											 	  self::$subvar0WithAttr,
												  self::$subvar1WithAttr));
		self::$data3             = array('var' =>
										 array(self::$data3Var0,
											   self::$data3Var1,
											   self::$data3Var2),
										 'var2' => self::$data3Var3);
		self::$data3WithAttr     = array('var' =>
										 array(self::$data3Var0,
											   self::$data3Var1WithAttr,
											   self::$data3Var2WithAttr),
										 'var2' => self::$data3Var3WithAttr);

		self::$root = array('data' => array(self::$data0, self::$data1),
							'data2' => self::$data2,
							'data3' => self::$data3);

		self::$rootWithAttr = array('data' => array(self::$data0,
													self::$data1WithAttr),
									'data2' => self::$data2WithAttr,
									'data3' => self::$data3WithAttr);

		self::$head = array('root' => self::$root);

		self::$headWithAttr = array('root' => self::$rootWithAttr);
	}

	/**
	 * Метод создает экзмепляр класса \Uwin\Config\Xml перед каждым запуском теста
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->_fixture = new
			Xml(self::$fileName, '/root');
	}

	/**
	 * Метод удаляет экзмепляр класса \Uwin\Config\Xml после выполнения каждого теста
	 *
	 * @return void
	 */
	protected function tearDown() {
		$this->_fixture = null;
	}

	/**
	 * Тестирование создание XML с файла/текста/массива
	 *
	 * @param string|array $xmlData - Путь к xml-файлу или XML-текст или массив, который будет преобразован в XML
	 * @param string       $path    - Путь к узлу, который будет сделан текущим
	 * @param string       $result  - Проверочный результат
	 * @param string       $message - Текст, выводимый при провале теста
	 *
	 * @dataProvider provideCreateXml
	 *
	 * @return void
	 */
	public function testCreateXml($xmlData, $path, $result, $message) {
		$this->_fixture = new Xml($xmlData, $path);
		if (null == $xmlData) {
			$this->_fixture->add('/', 'root', null);
		}

		$this->assertXmlStringEqualsXmlString($result,
			$this->_fixture->getContent(), $message);
	}

	/**
	 * Данные для тестирования создания XML
	 *
	 * @return array
	 */
	public function provideCreateXml() {
		self::startUp();
		$array['root'][] = array('node' => 1);
		$array['root'][] = array('node' => 2);
		$array['root']['newNode'] = array('subnode', 'subnode' => 'test&test');

		return array(
			array(null, null, '<?xml version="1.0" encoding="UTF-8"?><root/>',
				'Uwin\Config\Xml: Ошибка при создании пустого Xml'),

			array(self::$fileName, null,
				'<?xml version="1.0" encoding="UTF-8"?><root><data><general><country>Украина</country><country>Россия</country></general><general><title>Проверка связи</title><var/></general></data><data attr1="value"><![CDATA[Проверка&связи №2]]></data><data2><var>Test1</var><var attr="Test2"/><var attr="Test3">ValuesTest3</var></data2><data3><var>Test1</var><var attr="Test2"/><var attr="Test3">ValuesTest3</var><var2 attr="TestVar2"><subvar attr="sbv">test subvar</subvar><subvar attr="sbv">test subvar</subvar></var2></data3></root>',
				'Uwin\Config\Xml: Ошибка при открытии Xml-файла'),

			array($array, null,
				"<?xml version=\"1.0\" encoding=\"UTF-8\"?><root><node>1</node><node>2</node><newNode><subnode/><subnode><![CDATA[test&test]]></subnode></newNode></root>",
				'Uwin\Config\Xml: Ошибка при создании Xml с массива'),
		);
	}

	/**
	 * Тестирование создание XML неправильного формата с выбросом исключения
	 *
	 * @param string|array $xmlData - Путь к xml-файлу или XML-текст
	 * @param string       $path    - Путь к узлу, который будет сделан текущим
	 *
	 * @dataProvider provideCreateXmlError
	 * @expectedException \Uwin\Config\Xml\Exception
	 *
	 * @return void
	 */
	public function testCreateXmlError($xmlData, $path) {
		$this->_fixture = new Xml($xmlData, $path);
	}

	/**
	 * Данные для тестирования создания XML неправильного формата с выбросом
	 * исключения
	 *
	 * @return array
	 */
	public function provideCreateXmlError() {
		return array(
			array(dirname(dirname(__FILE__)) . '/resources/Xml/data-not-found.xml', null),
			array(null, '/root'),
			array(null, 'root'),
			array("<?xml version=\"1.0\" encoding=\"UTF-8\"?>sdfsdg", null),
		);
	}

	/**
	 * Тестирование установки текущего XML-узла при создании XML
	 *
	 * @param string|array $xmlData - Путь к xml-файлу или XML-текст или массив, который будет преобразован в XML
	 * @param string       $path    - Путь к узлу, который будет сделан текущим
	 * @param string       $result  - Проверочный результат(установленный путь)
	 * @param string       $message - Текст, выводимый при провале теста

	 * @dataProvider provideCreateXmlWithPath
	 *
	 * @return void
	 */
	public function testCreateXmlWithPath($xmlData, $path, $result, $message) {
		$this->_fixture = new Xml($xmlData, $path);

		$this->assertEquals($result, $this->_fixture->getPath(), $message);
	}

	/**
	 * Данные для тестирования создания XML с установкой текущего XML-узла
	 *
	 * @return array
	 */
	public function provideCreateXmlWithPath() {
		return array(
			array(self::$fileName, '/root/data/general/', '/root/data/general',
				'Uwin\Config\Xml: Ошибка при установке текущего узла при открытии Xml'),

			array(self::$fileName,
				'root/data/general/', '/root/data/general',
				'Uwin\Config\Xml: Ошибка при установке текущего узла при открытии Xml'),
		);
	}

	/**
	 * Метод тестирует установку текущего узла
	 *
	 * @param string $path   - Путь к текущему узлу
	 * @param string $result - Результат успешного теста(содержимое текущего узла)
	 *
	 * @dataProvider provideSetPath
	 *
	 * @return void
	 */
	public function testSetPath($path, $result) {
		$this->_fixture->setPath($path);

		$this->assertEquals( $result, $this->_fixture->get() );
	}

	/**
	 * Данные для тестирования установки текущего узла
	 *
	 * @return array
	 */
	public function provideSetPath() {
		return array(
			array(null, self::$root), // /root
			array('/', self::$head),
			array('/root/data/general', self::$general0),
			array('/root/data[0]/general[1]', self::$general1),
			array('data', self::$data0),
			array('data[0]/general', self::$general0),
			array('data/general[1]', self::$general1),
		);
	}

	/**
	 * Метод тестирует выброс исключения при установке текущим несуществующего
	 * узла
	 *
	 * @param string $path - Путь к узлу
	 *
	 * @dataProvider provideSetPathError
	 * @expectedException \Uwin\Config\Xml\Exception
	 * 
	 * @return void
	 */
	public function testSetPathError($path) {
		$this->_fixture->setPath($path);
	}

	/**
	 * Данные для тестирования выброса исключения при установке текущим
	 * несуществующего узла
	 *
	 * @return array
	 */
	public function provideSetPathError() {
		return array(
			array('/root/data[1]'),
			array('/root/data/none'),
			array('/root/data/general[3]'),
		);
	}

	/**
	 * Метод тестирует получение кол-ва под-узлов в указанном или текущем узле
	 *
	 * @param string $name   - Имя узла
	 * @param string $result - результат тестирования (Кол-во под-узлов)
	 *
	 * @dataProvider provideCount
	 *
	 * @return void
	 */
	public function testCount($name, $result) {
		$this->assertEquals( $result, $this->_fixture->count($name) );
	}

	/**
	 * Данные для тестирования получение кол-ва под-узлов в указанном или
	 * текущем узле
	 *
	 * @return array
	 */
	public function provideCount() {
		return array(
			array(null, 4),
			array('/', 1),
			array('/root', 4),
			array('/root/data', 2),
			array('data[1]', 0),
			array('/root/data[0]', 2),
			array('/root/data/general', 2),
			array('data/general[0]', 2),
			array('data/general[1]', 2),
			array('/root/data[0]/general/country', 0),
			array('/root/data[0]/general/country[1]', 0),
		);
	}

	/**
	 * Метод тестирует выброс исключения при получении кол-ва под-узлов в
	 * указанном несуществующем узле
	 *
	 * @param string $name - Имя узла
	 *
	 * @dataProvider provideCountError
	 * @expectedException \Uwin\Config\Xml\Exception
	 *
	 * @return void
	 */
	public function testCountError($name) {
		$this->_fixture->count($name);
	}

	/**
	 * Данные для тестирования выброса исключения при получении кол-ва
	 * под-узлов в указанном несуществующем узле
	 *
	 * @return array
	 */
	public function provideCountError() {
		return array(
			array('/root/data[1]/general/country', 'Exception'),
			array('/root/data[3]', 'Exception'),
		);
	}

	/**
	 * Метод тестирует проверку на существование указанного узла или аттрибута
	 *
	 * @param string $name      - Имя узла
	 * @param string $attribute - Имя аттрибута
	 * @param string $result    - Результат тестирования (Существует узел/аттрибут или нет)
	 *
	 * @dataProvider provideExist
	 *
	 * @return void
	 */
	public function testExist($name, $attribute, $result) {
		$this->assertEquals( $result, $this->_fixture->exists($name, $attribute) );
	}

	/**
	 * Данные для тестирования проверки на существования узла/аттрибута
	 *
	 * @return array
	 */
	public function provideExist() {
		return array(
			array(null, null, true),
			array('/', null, true),
			array('data[1]', null, true),
			array('/root/data[3]', null, false),
			array('/root/data[3]/general', null, false),
			array('/root/data[0]/general[2]', null, false),
			array('data/general[1]', null, true),
			array('/root/data[0]/general/country', null, true),
			array('/root/data[0]/general/country[1]', null, true),
			array('/root/data[0]/general/country[2]', null, false),

			array('data', 'attr1', false),
			array('data[1]', 'attr1', true),
			array('data[1]', 'attr2', false),
			array('data[3]', 'attr1', false),
			array('/root/data[1]', 'attr1', true),
		);
	}

	/**
	 * Метод тестирует проверку на существование указанного узла или аттрибута
	 *
	 * @param string $name      - Имя узла
	 * @param string $attribute - Значение узла
	 *
	 * @dataProvider provideSet
	 *
	 * @return void
	 */
	public function testSet($name, $value) {
		$this->_fixture->set($name, $value);
		$this->assertEquals( $value, $this->_fixture->get($name) );
	}

	/**
	 * Данные для тестирования установки значения узлу
	 *
	 * @return array
	 */
	public function provideSet() {
		return array(
			array('/root', 'test'),
			array('data/general', 'test'),
			array('data', 'test'),
			array('/root/data[1]', 'test'),
			array('/root/data[0]/general[1]/title', 'test'),
			array('data[0]/general[1]/title', 'test&test'),
			array('/root/data[0]/general[1]', '<strong>test</strong>'),
			array('/root/data[0]/general[1]', 'test\'s'),
		);
	}

	/**
	 * Метод тестирует выброс исключения при установке значения узла
	 *
	 * @param string $name  - Имя узла
	 * @param string $value - Значение узла
	 *
	 * @dataProvider provideSetError
	 * @expectedException \Uwin\Config\Xml\Exception
	 *
	 * @return void
	 */
	public function testSetError($name, $value) {
		$this->_fixture->set($name, $value);
	}

	/**
	 * Данные для тестирования выброса исключения при установке значения узла
	 *
	 * @return array
	 */
	public function provideSetError() {
		return array(
			array('/root/data[2]/title', null),
			array('/root/data[1]/general[2]', null),
		);
	}

	/**
	 * Метод тестирует получение значение указанного узла(если указано
	 * with_attr = true, тогда совместно с аттрибутами узла)
	 *
	 * @param string $name      - Имя узла
	 * @param bool   $with_attr - Получать значения аттрибутов или нет
	 * @param string $result    - Результат теста(значение указанного узла/аттрибута)
	 *
	 * @dataProvider provideGet
	 *
	 * @return void
	 */
	public function testGet($name, $with_attr, $result) {
		$this->assertEquals( $result, $this->_fixture->get($name, $with_attr) );
	}

	/**
	 * Данные для тестирования получения значения узла (совметно или нет с
	 * аттрибутами узла)
	 *
	 * @return array
	 */
	public function provideGet() {
		return array(
			array('/root/data/general', false, self::$general0),
			array('data[1]', true, self::$data1WithAttr),
			array('data[1]', false, self::$data1),
			array('/root/data[1]', true, self::$data1WithAttr),
			array('/root/data[0]/general[1]/title', true, self::$general1Title),
			array('/root/data[0]/general[1]', true, self::$general1),
			array(null, false, self::$root),
			array('data2', true, self::$data2WithAttr),
			array('data3', true, self::$data3WithAttr),
			array(null, true, self::$rootWithAttr),
			array('data3/var2', true, self::$data3Var3WithAttr),
		);
	}

	/**
	 * Метод тестирует на выброс исключения при получение значение указанного
	 * узла(если указано with_attr = true, тогда совместно с аттрибутами узла)
	 *
	 * @param string $name      - Имя узла
	 * @param bool   $with_attr - Получать значения аттрибутов или нет
	 *
	 * @dataProvider provideGetError
	 * @expectedException \Uwin\Config\Xml\Exception
	 *
	 * @return void
	 */
	public function testGetError($name, $with_attr) {
		$this->assertEquals( null, $this->_fixture->get($name, $with_attr) );
	}

	/**
	 * Данные для тестирования выброса исключения при получении значения узла
	 * (совметно или нет с аттрибутами узла)
	 *
	 * @return array
	 */
	public function provideGetError() {
		return array(
			array('/root/data[2]/title', true),
			array('/root/data[1]/general[2]', true),
		);
	}

	/**
	 * Метод тестирует получение значение указанного аттрибута или всех
	 * аттрибутов указанного узла
	 *
	 * @param string $name      - Имя узла
	 * @param string $attr      - Имя аттрибута
	 * @param string $result    - Результат теста(значение указанного аттрибута ил всех аттрибутов узла)
	 *
	 * @dataProvider provideGetAttr
	 *
	 * @return void
	 */
	public function testGetAttr($name, $attr, $result) {
		$this->assertEquals( $result, $this->_fixture->getAttr($name, $attr) );
	}

	/**
	 * Данные для тестирования получения значения указанного атрибута или всех
	 * аттрибутов указанного узла
	 *
	 * @return array
	 */
	public function provideGetAttr() {
		return array(
			array('/root/data[1]', 'attr1', 'value'),
			array('/root/data3/var[2]', 'attr', 'Test3'),
			array('/root/data3/var2', null, array('attr'=>'TestVar2')),
			array('/root/data3/var[2]', 'non-attr', null),
		);
	}

	/**
	 * Метод тестирует удаление указанного аттрибута или всех аттрибутов
	 * указанного узла
	 *
	 * @param string $name - Имя узла
	 * @param string $attr - Имя аттрибута
	 *
	 * @dataProvider provideDelAttr
	 *
	 * @return void
	 */
	public function testDelAttr($name, $attr) {
		$this->_fixture->delAttr($name, $attr);

		$this->assertEquals( null, $this->_fixture->getAttr($name, $attr) );
	}

	/**
	 * Данные для тестирования удаления указанного аттрибута или всех аттрибутов
	 * указанного узла
	 *
	 * @return array
	 */
	public function provideDelAttr() {
		return array(
			array('/root/data[1]', 'attr1'),
			array('/root/data3/var[2]', null),
			array('/root/data3/var2', 'attr'),
			array('/root/data3/var[2]', null),
		);
	}

	/**
	 * Метод тестирует удаление указанного узла
	 *
	 * @param string $name   - Имя узла
	 * @param string $path   - Путь к удаляемому узла
	 * @param string $result - Резульат удаления узла(массив под-узлов подительского узла, где был удаляемый узел)
	 *
	 * @dataProvider provideDel
	 *
	 * @return void
	 */
	public function testDel($name, $path, $result) {
		$this->_fixture->del($name);

		$this->assertEquals( $result, $this->_fixture->get($path) );
	}

	/**
	 * Данные для тестирования удаления указанного узла
	 *
	 * @return array
	 */
	public function provideDel() {
		return array(
			array('/root/data[1]', '/root', array('data' => self::$data0, 'data2' => self::$data2, 'data3' => self::$data3)),
			array('/root/data3/var[2]', '/root/data3', array( 'var' => array(self::$data3Var0, self::$data3Var1), 'var2' => self::$data3Var3) ),
			array('/root/data3/var2', '/root/data3', array( 'var' => array(self::$data3Var0, self::$data3Var1, self::$data3Var2)) ),
			array('data', '/root', array( 'data' => self::$data1, 'data2' => self::$data2, 'data3' => self::$data3 )),
			array('/root', '/', null),
		);
	}

	/**
	 * Метод тестирует установку значения аттрибута указанного узла
	 *
	 * @param string $name   - Имя узла
	 * @param string $attr   - Имя аттрибута
	 * @param string $value  - Значение аттрибута
	 * @param string $result - Результат
	 *
	 * @dataProvider provideSetAttr
	 *
	 * @return void
	 */
	public function testSetAttr($name, $attr, $value, $result) {
		$this->_fixture->setAttr($name, $attr, $value);

		$this->assertEquals( $result, $this->_fixture->getAttr($name) );
	}

	/**
	 * Данные для тестирования установки значения аттрибута указанного узла
	 *
	 * @return array
	 */
	public function provideSetAttr() {
		return array(
			array('/root/data[1]', 'attr2', 'value2', array('attr1' => 'value', 'attr2' => 'value2')),
			array('data[1]', 'attr1', 'value2', array('attr1' => 'value2')),
			array('data3/var', 'attr1', 'value2', array('attr1' => 'value2')),
			array('data3/var2', 'attr1', 'value2', array('attr' => 'TestVar2', 'attr1' => 'value2')),
			array('data3/var2', array('attr' => 'Var2', 'attr1' => 'value2'), null, array('attr' => 'Var2', 'attr1' => 'value2')),
			array('data3/var2', array('attr3' => 'Var3', 'attr1' => 'value2'), null, array('attr' => 'TestVar2', 'attr3' => 'Var3', 'attr1' => 'value2')),
			array('/root', 'attr', 'val', array('attr' => 'val')),
		);
	}

	/**
	 * Метод тестирует выброс исключения при удалении указанного узла
	 *
	 * @param string $name   - Имя узла
	 * @param string $attr   - Путь к удаляемому узла
	 * @param string $value  - Путь к удаляемому узла
	 *
	 * @dataProvider provideSetAttrError
	 * @expectedException \Uwin\Config\Xml\Exception
	 *
	 * @return void
	 */
	public function testSetAttrError($name, $attr, $value) {
		$this->_fixture->setAttr($name, $attr, $value);

		$this->assertEquals( null, $this->_fixture->getAttr($name) );
	}

	/**
	 * Данные для тестирования выброса исключения при установке значения
	 * аттрибута указанного узла
	 *
	 * @return array
	 */
	public function provideSetAttrError() {
		return array(
			array('/root', 'attr', 'val&val'),
		);
	}

	/**
	 * Метод тестирует добавление нового узла с указанным значением
	 *
	 * @param string $path   - Путь к родительскому узлу
	 * @param string $name   - Имя узла
	 * @param string $value  - Значение узла
	 * @param string $result - Результат
	 *
	 * @dataProvider provideAdd
	 *
	 * @return void
	 */
	public function testAdd($path, $name, $value, $result) {
		$this->_fixture->add($path, $name, $value);

		$this->assertEquals( $result, $this->_fixture->get($path) );
	}

	/**
	 * Данные для тестирования выброса исключения при добавление узла
	 *
	 * @return array
	 */
	public function provideAdd() {
		return array(
			array('/root/data[1]', 'newNode', 'New Node Value', array('newNode' => 'New Node Value', 'value' => 'Проверка&связи №2')),
			array('data2', 'newNode', null, array( 'var' => array('Test1', null, 'ValuesTest3'), 'newNode' => null)),
			array('data2', 'newNode', 'value&value', array( 'var' => array('Test1', null, 'ValuesTest3'), 'newNode' => 'value&value')),
			array('data2/var', array('newNode' => 'val&val', 'secondNode' => 'new<b>Val</b>'), null, array( 'value' => 'Test1', 'newNode' => 'val&val', 'secondNode' => 'new<b>Val</b>')),
		);
	}

	/**
	 * Метод тестирует получения сформированного XML
	 *
	 * @return void
	 */
	public function testGetContent() {
		$xmlString = '<?xml version="1.0" encoding="UTF-8"?><root>Test</root>';

		$xml = new Xml($xmlString);

		$this->assertXmlStringEqualsXmlString($xmlString, $xml->getContent());
	}

	/**
	 * Метод тестирует сохраниение в XML файл
	 *
	 * @param array $xmlArray - Массив узлов XML
	 * @param string $xmlFile - Имя xml файла
	 * @param string $result  - Результат
	 *
	 * @dataProvider provideSave
	 *
	 * @return void
	 */
	public function testSave($xmlArray, $xmlFile, $result) {
		$xml = new Xml($xmlArray);

		$xml->save($xmlFile);
		unlink($xmlFile);
	}

	/**
	 * Данные для тестирования сохранения в XML файл
	 *
	 * @return array
	 */
	public function provideSave() {
		$fileName = dirname(dirname(__FILE__)) . '/tmp/xml-test.xml';

		$array['root'][] = array('node' => 1);
		$array['root'][] = array('node' => 2);
		$array['root']['newNode'] = array('subnode', 'subnode' => 'test&test');

		return array(
			array($array, $fileName, "<?xml version=\"1.0\" encoding=\"UTF-8\"?><root><node>1</node><node>2</node><newNode><subnode/><subnode><![CDATA[test&test]]></subnode></newNode></root>"),
		);
	}

	/**
	 * Метод тестирует выброс исключения при сохраниении в XML файл
	 *
	 * @param array $xmlArray - Массив узлов XML
	 * @param string $xmlFile - Имя xml файла
	 *
	 * @dataProvider provideSaveError
	 * @expectedException \Uwin\Config\Xml\Exception
	 *
	 * @return void
	 */
	public function testSaveError($xmlArray, $xmlFile) {
		$xml = new Xml($xmlArray);

		$xml->save($xmlFile);
		unlink($xmlFile);
	}

	/**
	 * Данные для тестирования выброса исключени при сохранении в XML файл
	 *
	 * @return array
	 */
	public function provideSaveError() {
		$fileName = dirname(dirname(__FILE__)) . '/tmp/xml-test.xml';

		$array['root'][] = array('node' => 1);
		$array['root'][] = array('node' => 2);
		$array['root']['newNode'] = array('subnode', 'subnode' => 'test&test');

		return array(
			array($array, $fileName . '/sdsd'),
		);
	}
}