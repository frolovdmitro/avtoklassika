<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Xml, который отвечает за работу с XML файлами
 * конфигурации
 *
 * @category  Uwin
 * @package   Uwin\Xml
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Sitemap
 */
namespace Uwin;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Sitemap\Exception as SitemapException;
use \Uwin\Linguistics as Linguistics;

/**
 * Класс, который отвечает за работу с XML файлами конфигурации
 *
 * @category  Uwin
 * @package   Uwin\Sitemap
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Sitemap
{
	/**
	 * Адрес для Google на который можно послать http-запрос, для указания
	 * Google, что Sitemap изменился
	 * @var string
	 */
	const PING_GOOGLE = 'http://google.com/webmasters/sitemaps/ping?sitemap=';

	/**
	 * Адрес для Яндекс на который можно послать http-запрос, для указания
	 * Яндекс, что Sitemap изменился
	 * @var string
	 */
	const PING_YANDEX = 'http://webmaster.yandex.ru/wmconsole/sitemap_list.xml?host=';

	/**
	 * Адрес для Yahoo! на который можно послать http-запрос, для указания
	 * Yahoo!, что Sitemap изменился
	 * @var string
	 */
	const PING_YAHOO  = 'http://search.yahooapis.com/SiteExplorerService/V1/ping?sitemap=';

	/**
	 * Адрес для Bing на который можно послать http-запрос, для указания
	 * Bing, что Sitemap изменился
	 * @var string
	 */
	const PING_BING   = 'http://www.bing.com/webmaster/ping.aspx?siteMap=';

	/**
	 * Экземпляр класса \Uwin\Linguistic, который используется для
	 * преобразования html символов в UTF-8
	 *
	 * @var \Uwin\Lingustics
	 */
	private $_linguistics;

	/**
	 * Имя файла индекса для Sitemap
	 * @var string = sitemap
	 */
	private $_nameIndex = 'sitemap';

	/**
	 * Максимальное количество URL в одном файле Sitemap
	 * @var int = 5000
	 */
	private $_maxCountLocInSitemap = 5000;

	/**
	 * URL к папке, где будет храниться файл индекса Sitemap
	 * @var string
	 */
	private $_indexUrl = null;

	/**
	 * Путь к директории, где будет храниться файл индекса Sitemap
	 * @var string
	 */
	private $_indexPath = null;

	/**
	 * URL к папке, где будут храниться файлы Sitemap
	 * @var string
	 */
	private $_sitemapsUrl = null;

	/**
	 * Путь к директории, где будут храниться файлы Sitemap
	 * @var string
	 */
	private $_sitemapsPath = null;

	/**
	 * Признак использования gzip-сжатия или нет для Sitemap
	 * @var boolean = true
	 */
	private $_usageGzip = true;

	/**
	 * Массив в котором хранятся все Sitemap
	 * @var array
	 */
	private $_sitemaps = array();

	/**
	 * Имя текущего Sitemap
	 * @var string
	 */
	private $_currentSitemap = null;

	/**
	 * Имя текущей URL в Sitemap
	 * @var string
	 */
	private $_currentLocation = null;


	/**
	 * Метод Возвращает ссылку на указанный URL в Sitemap, или если URL не
	 * указано, то на текущий
	 *
	 * @param string $location = null
	 * @return &array
	 */
	private function &_locationNode($location = null)
	{
		if ( !empty($location) ) {
			return $this->_sitemaps[$this->_currentSitemap]['locations'][$location];
		}

		return $this->_sitemaps[$this->_currentSitemap]['locations'][$this->_currentLocation];
	}

	/**
	 * Метод возвращает сформированный DOMDocument для файла индекса Sitemap
	 *
	 * @return DOMDocument
	 */
	private function _getIndexSitemap()
	{
		// Создаем xml
		$sitemapindex = new \DOMDocument('1.0', 'UTF-8');
		$sitemapindex->formatOutput = true;

		// Создаем узел sitemapindex и присваеваем ему атрибут xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		$index_node = $sitemapindex->createElement('sitemapindex');
		$sitemapindex->appendChild($index_node);
		$attribute = $sitemapindex->createAttribute('xmlns');
		$attribute_node = $index_node->appendChild($attribute);
		$attribute_node->appendChild( $sitemapindex->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9') );

		// Пройтись по всем Sitemap которые есть в индексе
		foreach ($this->_sitemaps as $name=>$value) {
			// Расширение gzip
			$gzip_ext = '';
			if ($this->_usageGzip) {
				$gzip_ext = '.gz';
			}

			// Получаем количество URL в Sitemap
			$locations_count = count($value['locations']);
			$i = 1;
			// В цикле проверяем больше ли в файле sitemap URL чем указано в
			// $this->_maxCountLocInSitemap, если нет то создаем ссылку на один
			// файл sitemap, иначе создаем столько ссылок на файлы sitemap
			// сколько нужно, с учетом того, что в файле должно быть не больше
			// $this->_maxCountLocInSitemap URL
			do {
				$node = $sitemapindex->createElement('sitemap');
				$sitemap = $index_node->appendChild($node);

				// Номер sitemap, если он один, номер не указывается
				$num_sitemap = null;
				if ( ($locations_count > $this->_maxCountLocInSitemap) || ($i>1) ) {
					$num_sitemap = '.' . $i;
				}

				// Формируем адрес к файлу Sitemap
				$loc_sitemap = $this->_sitemapsUrl . $name . $num_sitemap . '.xml' . $gzip_ext;
				// Добавляем тег loc в файл sitemap
				$node = $sitemapindex->createElement('loc', $loc_sitemap);
				$node = $sitemap->appendChild($node);

				// Если у sitemap указана дата модификации (lastmod), добавляем
				// тег lastmod к первому sitemap
				if ( !empty($value['lastmod']) && (1 == $i) ) {
					$node = $sitemapindex->createElement('lastmod', (string)$value['lastmod']);
					$node = $sitemap->appendChild($node);
				}

				$locations_count -= $this->_maxCountLocInSitemap;
				$i++;
			} while ($locations_count > 0);
		}

		return $sitemapindex;
	}

	/**
	 * Метод возвращает сформированный DOMDocument для файла Sitemap. Если
	 * указана переменная $part то возвращает Sitemap указанной части
	 *
	 * @param string $name = null Имя sitemap, если не указано - текущий sitemap
	 * @param int $part = null Когда sitemap расбит на части, это номер sitemap
	 * @return DOMDocument
	 */
	private function _getSitemap($name = null, $part = null)
	{
		// Создаем xml
		$sitemap = new \DOMDocument('1.0', 'UTF-8');
		$sitemap->formatOutput = true;

		// Создаем узел sitemapindex и присваеваем ему атрибут xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		$urlset_node = $sitemap->createElement('urlset');
		$sitemap->appendChild($urlset_node);
		$attribute = $sitemap->createAttribute('xmlns');
		$attribute_node = $urlset_node->appendChild($attribute);
		$attribute_node->appendChild( $sitemap->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9') );

		// Если не указано имя sitemap, использовать текущий
		if ( empty($name) ) {
			$name = $this->_currentSitemap;
		}

		$i = 0;

		// В цикле прохожу по всем URL sitemap и добавляю их в DOM
		foreach ($this->_sitemaps[$name]['locations'] as $name => $value) {
			// Если указана кокой номер sitemap формировать, пропускаем ненужные URL
			if ( !empty($part) ) {
				if ( ($i < $this->_maxCountLocInSitemap*($part-1) || $i >= $this->_maxCountLocInSitemap*$part) ) {
					$i++;

					continue;
				}
			}

			// Создаем узел URL
			$node = $sitemap->createElement('url');
			$url = $urlset_node->appendChild($node);

			// Создаем узел loc иприсваиваем ему значение адреса страницы
			$node = $sitemap->createElement('loc');
			$node = $url->appendChild($node);
			$node->appendChild( $sitemap->createTextNode($name) );

			$video_node = null;

			if ( is_array($value) ) {
				foreach ($value as $key => $val) {
					// Устанавливаем текущий узел
					$loc = $url;
					// Если создается видео Sitemap добавляем родительский узел video:video
					if ( empty($video_node) && false !== strpos($key, 'video:') ) {
						$node = $sitemap->createElement('video:video');
						$video_node = $loc = $url->appendChild($node);
					}
					// Если добавляемый узел относится к видео Sitemap, устанваливаем
					// текущий узел в узел видео Sitemap
					if ( false !== strpos($key, 'video:') ) {
						$loc = $video_node;
					}

					// Эсли узел без атрибутов
					if ( !is_array($val) ) {
						$node = $sitemap->createElement($key, $val);
						$node = $loc->appendChild($node);
					} else {
						// Если в значении указаны массивом список атрибутов
						if ( array_key_exists('_name_', $val) ) {
							$node = $sitemap->createElement($key, $val['_name_']);
							$loc->appendChild($node);
							unset($val['_name_']);

							// Пройтись по всем атрибутам
							foreach ($val as $atr_key => $atr_val) {
								$attribute = $sitemap->createAttribute($atr_key);
								$attribute_node = $node->appendChild($attribute);
								$attribute_node->appendChild( $sitemap->createTextNode($atr_val) );
							}
						} else {
							foreach ($val as $node_val) {
								$node = $sitemap->createElement($key, $node_val);
								$loc->appendChild($node);
							}
						}
					}
				}
			}

			$i++;
		}

		if ( !empty($video_node) ) {
			$attribute = $sitemap->createAttribute('xmlns:video');
			$attribute_node = $urlset_node->appendChild($attribute);
			$attribute_node->appendChild( $sitemap->createTextNode('http://www.google.com/schemas/sitemap-video/1.1') );
		}

		return $sitemap;
	}

	/**
	 * Метод шлет GET запрос по указанному URL поисковой системы, плюс добавляет
	 * в конец URL имя файла индекса Sitemap
	 *
	 * @param string $url_tool
	 * @return $this
	 */
	private function _pingSitemap($url_tool)
	{
		$curl = curl_init($url_tool . $this->_indexUrl . $this->_nameIndex . '.xml');
		curl_exec($curl);
		curl_close($curl);

		return $this;
	}

	/**
	 * Метод возвразает значение указанного свойства в Sitemap в текущем или
	 * указанном URL
	 *
	 * @param string $name
	 * @param string $location = null
	 */
	private function _getProperty($name, $location = null)
	{
		$location_node = $this->_locationNode($location);

		if ( !isset($location_node[$name]) ) {
			return null;
		}

		return $location_node[$name];
	}

	/**
	 * Метод устанавливает значение указанного свойства в Sitemap в текущем или
	 * указанном URL
	 *
	 * @param string $name Имя свойства
	 * @param mixed $value Значение свойства
	 * @param string $location = null URL, куда пудет установлено свойство
	 */
	private function _setProperty($name, $value, $location = null)
	{
		$location_node = &$this->_locationNode($location);
		if ( empty($value) ) {
			unset($location_node[$name]);

			return $this;
		}

		if ( !is_array($value) ) {
			$location_node[$name] =
				htmlspecialchars( strip_tags( trim( $this->_linguistics->replaceSpecSymbol($value) ) ) );
		} else {
			foreach ($value as $key=>$val) {
				if ( empty($val) ) {
					unset($location_node[$name][$key]);
				} else {
					$location_node[$name][$key] = $val;
				}
			}
		}

		return $this;
	}

	/**
	 * Конструктор класса
	 *
	 * @return $this
	 */
	public function __construct()
	{
		$this->_linguistics = new Linguistics;

		return $this;
	}

	/**
	 * Метод устанавилвает имя файла индекса Sitemap
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setNameIndex($name)
	{
		$this->_nameIndex = $name;

		return $this;
	}

	/**
	 * Метод возвращает имя файла индекса Sitemap
	 *
	 * @return string
	 */
	public function getNameIndex()
	{
		return $this->_nameIndex;
	}

	/**
	 * Метод устанавилвает масимальное количество URL, которое может быть в
	 * одном файле Sitemap
	 *
	 * @param int $count
	 * @return $this
	 */
	public function setMaxCounLocInSitemap($count)
	{
		$this->_maxCountLocInSitemap = $count;

		return $this;
	}

	/**
	 * Метод возвращает максимальное количество URL, которое может быть в
	 * одном файле Sitemap
	 *
	 * @return int
	 */
	public function getMaxCountLocInSitemap()
	{
		return $this->_maxCountLocInSitemap;
	}

	/**
	 * Метод устанавилвает http адрес папки, где будет расположен файл индекса Sitemap
	 *
	 * @param string $url
	 * @return $this
	 */
	public function setIndexUrl($url)
	{
		$this->_indexUrl = $url;

		return $this;
	}

	/**
	 * Метод возвращает http адрес папки, где будет расположен файл индекса Sitemap
	 *
	 * @return string
	 */
	public function getIndexUrl()
	{
		return $this->_indexUrl;
	}

	/**
	 * Метод устанавилвает адрес директории в системе, где будет расположен
	 * файл индекса Sitemap
	 *
	 * @param string $path
	 * @return $this
	 */
	public function setIndexPath($path)
	{
		$this->_indexPath = $path;

		return $this;
	}

	/**
	 * Метод возвращает адрес директории в системе, где будет расположен файл
	 * индекса Sitemap
	 *
	 * @return string
	 */
	public function getIndexPath()
	{
		return $this->_indexPath;
	}

	/**
	 * Метод устанавилвает http адрес папки, где будут расположены файлы Sitemap
	 *
	 * @param string $url
	 * @return $this
	 */
	public function setSitemapsUrl($url)
	{
		$this->_sitemapsUrl = $url;

		return $this;
	}

	/**
	 * Метод возвращает http адрес папки, где будут расположены файлы Sitemap
	 *
	 * @return string
	 */
	public function getSitemapsUrl()
	{
		return $this->_sitemapsUrl;
	}

	/**
	 * Метод устанавилвает адрес директории в системе, где будут расположены
	 * файлы Sitemap
	 *
	 * @param string $path
	 * @return $this
	 */
	public function setSitemapsPath($path)
	{
		$this->_sitemapsPath = $path;

		return $this;
	}

	/**
	 * Метод возвращает адрес директории в системе, где будут расположены файлы
	 * Sitemap
	 *
	 * @return string
	 */
	public function getSitemapsPath()
	{
		return $this->_sitemapsPath;
	}

	/**
	 * Метод устанавливает отметку о том, будет использоваться Gzip сжатие для
	 * файлов Sitemap или нет
	 *
	 * @param boolean $gzip
	 * @return $this
	 */
	public function setUsageGzip($gzip)
	{
		$this->_usageGzip = $gzip;

		return $this;
	}

	/**
	 * Метод возвращает отметку о том, будет использоваться Gzip сжатие для
	 * файлов Sitemap или нет
	 *
	 * @return boolean
	 */
	public function getUsageGzip()
	{
		return $this->_usageGzip;
	}

	/**
	 * Метод возвращает количество Url в Sitemap
	 *
	 * @param string $sitemap = null Имя Sitemap, если не указано - текущий Sitemap
	 * @return int
	 */
	public function getCountLocInSitemap($sitemap = null)
	{
		// Если не указано имя Sitemap, использовать текущий
		if ( empty($sitemap) ) {
			$sitemap = $this->_currentSitemap;
		}

		return count($this->_sitemaps[$sitemap]['locations']);
	}

	/**
	 * Метод добавляет Sitemap в индекс
	 *
	 * @param string $name Наименование Sitemap
	 * @param timestamp $lastmod Дата последней модификации Sitemap
	 * @return $this
	 */
	public function addSitemap($name, $lastmod = null)
	{
		$this->_sitemaps[$name] = array();

		$this->changeCurrentSitemap($name)
			->setLastmodSitemap($lastmod);

		return $this;
	}

	/**
	 * Метод удаляет указанный Siteamp с индекса, или если он не указан,
	 * то текущий
	 *
	 * @param string $name Наименование Sitemap
	 * @return $this
	 */
	public function delSitemap($name = null)
	{
		// Если не указано имя Sitemap, использовать текущий
		if ( empty($name) ) {
			$name = $this->_currentSitemap;
		}

		unset($this->_sitemaps[$name]);

		// Если удаляемый Sitemap является текущим, устанавилваем текущий Sitemap = NULL
		if ($name == $this->_currentSitemap) {
			$this->_currentSitemap = null;
		}

		return $this;
	}

	/**
	 * Метод устанавливает новове имя текущего Sitemap
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setNameSitemap($name)
	{
		// Получаем имя текущего Sitemap
		$current_sitemap = $this->_currentSitemap;

		// Копируем данные текущего Sitemap в новый с новым именем
		$this->_sitemaps[$name] = $this->_sitemaps[$current_sitemap];

		//Изменяем текущий Sitemap на новый и удаляем старый Sitemap
		$this->changeCurrentSitemap($name)
			->delSitemap($current_sitemap);

		return $this;
	}

	/**
	 * Метод возвращает имя текущего Sitemap
	 *
	 * @return string
	 */
	public function getNameSitemap()
	{
		return $this->_currentSitemap;
	}

	/**
	 * Метод изменяет текущий Sitemap на указанный
	 *
	 * @param string $name
	 * @throws SitemapException
	 * @return $this
	 */
	public function changeCurrentSitemap($name)
	{
		// Если указанного Sitemap не существует, вызываем исключение
		if ( !array_key_exists($name, $this->_sitemaps) ) {
			throw new SitemapException('Sitemap "' . $name . '" not found.', 1501);
		}

		$this->_currentSitemap = $name;

		return $this;
	}

	/**
	 * Метод устанавливает или удаляет значение lastmod у текущего Sitemap
	 *
	 * @param timestamp $lastmod
	 * @return $this
	 */
	public function setLastmodSitemap($lastmod)
	{
		// Если $lastmod не указан, удаляем это значениу у Sitemap
		if ( empty($lastmod) ) {
			unset($this->_sitemaps[$this->_currentSitemap]['lastmod']);

			return $this;
		}

		// Устанавливаем новое значение lastmod у текущего Sitemap
		$this->_sitemaps[$this->_currentSitemap]['lastmod'] = date(DATE_RSS, $lastmod);

		return $this;
	}

	/**
	 * Метод возвращает дату/время последней модификации (lastmod) у текущего
	 * Sitemap
	 *
	 * @return timestamp
	 */
	public function getLastmodSitemap()
	{
		if ( !array_key_exists('lastmod', $this->_sitemaps[$this->_currentSitemap]) ) {
			return null;
		}

		return strtotime($this->_sitemaps[$this->_currentSitemap]['lastmod']);
	}

	/**
	 * Метод добавляет URL страницы в Sitemap
	 *
	 * @param string $location URL страницы
	 * @param timestamp $lastmod = null Время последней модификации
	 * @param string $changefreq = null Частота обновления
	 * @param integer $priority = null Приоритет
	 * @return $this
	 */
	public function addLocation($location, $lastmod = null, $changefreq = null, $priority = null)
	{
		// Создаем переменную-ссылку на текущий Sitemap
		$current_sitemap = &$this->_sitemaps[$this->_currentSitemap];

		$current_sitemap['locations'][$location] = array();
		$this->_currentLocation = $location;

		$this->setLastmod($lastmod)
			->setChangefreq($changefreq)
			->setPriority($priority);

		return $this;
	}

	/**
	 * Метод удаляет указанный URL с Sitemap
	 *
	 * @param string $location = null
	 * @return $this
	 */
	public function delLocation($location = null)
	{
		// Если не указано имя URL, использовать текущий
		if ( empty($location) ) {
			$location = $this->_currentLocation;
		}

		unset($this->_sitemaps[$this->_currentSitemap]['locations'][$location]);

		// Если удаляемый URL является текущим, устанавиливаем текущий Url = NULL
		if ($location == $this->_currentLocation) {
			$this->_currentLocation = null;
		}

		return $this;
	}

	/**
	 * Метод изменяет текущий URL в текущем Sitemap на указанный
	 *
	 * @param string $location
	 * @throws SitemapException
	 * @return $this
	 */
	public function changeCurrentLocation($location)
	{
		// Если указанного Sitemap не существует, вызываем исключение
		if ( !array_key_exists($location, $this->_sitemaps[$this->_currentSitemap]['locations']) ) {
			throw new SitemapException('Location "' . $location . '" not found.', 1501);
		}

		$this->_currentLocation = $location;

		return $this;
	}

	/**
	 * Метод изменяет текущий URL
	 *
	 * @param string $location
	 * @return $this
	 */
	public function setLocationName($location)
	{
		// Получаем ссылку на текущий URL
		$current_location = &$this->_locationNode();
		// Получаем имя текущего URL
		$current_name_location = $this->_currentLocation;

		// Создаем новый URL и копируем в него данный текущего URL
		$this->_sitemaps[$this->_currentSitemap]['locations'][$location] = $current_location;

		// Изменяем текущий URL на новый и удаляем старый
		$this->changeCurrentLocation($location)
			->delLocation($current_name_location);

		return $this;
	}

	/**
	 * Метод возвращает имя текущего URL
	 *
	 * @return string
	 */
	public function getLocationName()
	{
		return $this->_currentLocation;
	}

	/**
	 * Метод возвращает признак того, существует URL  с таким именем в Sitemap
	 * или нет
	 *
	 * @param string $location
	 * @return boolean
	 */
	public function hasLocation($location)
	{
		if ( !array_key_exists('locations', $this->_sitemaps[$this->_currentSitemap]) ) {
			return false;
		}

		if ( !array_key_exists($location, $this->_sitemaps[$this->_currentSitemap]['locations']) ) {
			return false;
		}

		return true;
	}

	/**
	 * Метод устанавливает время последней модификации URL
	 *
	 * @param timestamp $lastmod Дата модификации
	 * @param string $location = null URL страницы
	 * @return $this
	 */
	public function setLastmod($lastmod, $location = null)
	{
		if ( !empty($lastmod) ) {
			$lastmod = date(DATE_RSS, $lastmod);
		}
		$this->_setProperty('lastmod', $lastmod, $location);

		return $this;
	}

	/**
	 * Метод возвразает время последней модификации текущего или указанного URL
	 *
	 * @param string $location = null
	 * @return timestamp
	 */
	public function getLastmod($location = null)
	{
		$value = $this->_getProperty('lastmod', $location);

		if ( empty($value) ) {
			return null;
		}

		return strtotime($value);
	}

	/**
	 * Метод устанавливает частоту обновления для текущего или указнного URL
	 *
	 * @param string $changefreq
	 * @param string $location = null
	 * @return $this
	 */
	public function setChangefreq($changefreq, $location = null)
	{
		$this->_setProperty('changefreq', $changefreq, $location);

		return $this;
	}

	/**
	 * Метод возвращает частоту обновления для текущего или указанного URL
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getChangefreq($location = null)
	{
		return $this->_getProperty('changefreq', $location);
	}

	/**
	 * Метод устанавливает приоритет для текущего или указнного URL
	 *
	 * @param float $priority
	 * @param string $location = null
	 * @return $this
	 */
	public function setPriority($priority, $location = null)
	{
		$this->_setProperty('priority', $priority, $location);

		return $this;
	}

	/**
	 * Метод возвращает приоритет для текущего или указанного URL
	 *
	 * @param string $location = null
	 * @return int
	 */
	public function getPriority($location = null)
	{
		return $this->_getProperty('priority', $location);
	}

	/**
	 * Метод устанавливает URL файла изображения для значка видео для текущего
	 * или указнного URL
	 *
	 * @param string $thumbmail
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoThumbmail($thumbmail, $location = null)
	{
		$this->_setProperty('video:thumbnail_loc', $thumbmail, $location);

		return $this;
	}

	/**
	 * Метод возвращает URL файла изображения для значка видео для текущего
	 * или указнного URL
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getVideoThumbmail($location = null)
	{
		return $this->_getProperty('video:thumbnail_loc', $location);
	}

	/**
	 * Метод устанавливает название видео для текущего или указнного URL
	 *
	 * @param string $title
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoTitle($title, $location = null)
	{
		$this->_setProperty('video:title', $title, $location);

		return $this;
	}

	/**
	 * Метод возвращает название видео для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getVideoTitle($location = null)
	{
		return $this->_getProperty('video:title', $location);
	}

	/**
	 * Метод устанавливает описание видео для текущего или указнного URL
	 *
	 * @param string $description
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoDescription($description, $location = null)
	{
		$this->_setProperty('video:description', $description, $location);

		return $this;
	}

	/**
	 * Метод возвращает описание видео для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getVideoDescription($location = null)
	{
		return $this->_getProperty('video:description', $location);
	}

	/**
	 * Метод устанавливает URL файла видео для текущего или указнного URL
	 *
	 * @param string $file
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoFile($file, $location = null)
	{
		$this->_setProperty('video:content_loc', $file, $location);

		return $this;
	}

	/**
	 * Метод возвращает URL файла видео для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getVideoFile($location = null)
	{
		return $this->_getProperty('video:content_loc', $location);
	}

	/**
	 * Метод устанавливает URL, указывающий на Flash-проигрыватель для текущего
	 * или указнного URL
	 *
	 * @param string $player
	 * @param string $allow_embed = null (yes|no)
	 * @param string $autoplay = null
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoPlayer($player, $allow_embed = null, $autoplay = null, $location = null)
	{
		$value = array(
			'_name_' => $player,
			'allow_embed' => $allow_embed,
			'ap' => $autoplay,
		);
		$this->_setProperty('video:player_loc', $value, $location);

		return $this;
	}

	/**
	 * Метод возвращает URL, указывающий на Flash-проигрыватель для текущего
	 * или указнного URL
	 *
	 * @param string $location = null
	 * @return array
	 */
	public function getVideoPlayer($location = null)
	{
		return $this->_getProperty('video:player_loc', $location);
	}

	/**
	 * Метод устанавливает продолжительность видео в секундах для текущего или
	 * указнного URL
	 *
	 * @param int $duration
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoDuration($duration, $location = null)
	{
		$this->_setProperty('video:duration', $duration, $location);

		$location_node['video:duration'] = $duration;

		return $this;
	}

	/**
	 * Метод возвращает продолжительность видео в секундах для текущего или
	 * указнного URL
	 *
	 * @return int
	 */
	public function getVideoDuration($location = null)
	{
		return $this->_getProperty('video:duration', $location);
	}

	/**
	 * Метод устанавливает дату, после которой видео станет недоступным для
	 * текущего или указнного URL
	 *
	 * @param timestamp $date
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoExpirationDate($date, $location = null)
	{
		if ( !empty($date) ) {
			$date = date(DATE_RSS, $date);
		}
		$this->_setProperty('video:expiration_date', $date, $location);

		return $this;
	}

	/**
	 * Метод возвращает дату, после которой видео станет недоступным для
	 * текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return timestamp
	 */
	public function getVideoExpirationDate($location = null)
	{
		$value = $this->_getProperty('video:expiration_date', $location);

		if ( empty($value) ) {
			return null;
		}

		return strtotime($value);
	}

	/**
	 * Метод устанавливает оценку видео для текущего или указнного URL.
	 * Значение должно быть десятичной дробью от 0 до 5
	 *
	 * @param float $rating
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoRating($rating, $location = null)
	{
		$this->_setProperty('video:rating', $rating, $location);

		return $this;
	}

	/**
	 * Метод возвращает оценку видео для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return float
	 */
	public function getVideoRating($location = null)
	{
		return $this->_getProperty('video:rating', $location);
	}

	/**
	 * Метод устанавливает количество просмотров видео для текущего или
	 * указнного URL
	 *
	 * @param int $count
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoViewCount($count, $location = null)
	{
		$this->_setProperty('video:view_count', $count, $location);

		return $this;
	}

	/**
	 * Метод возвращает количество просмотров видео для текущего или
	 * указнного URL
	 *
	 * @param string $location = null
	 * @return int
	 */
	public function getVideoViewCount($location = null)
	{
		return $this->_getProperty('video:view_count', $location);
	}

	/**
	 * Метод устанавливает дату публикации видео для текущего или указнного URL
	 *
	 * @param timestamp $date
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoPublicDate($date, $location = null)
	{
		if ( !empty($date) ) {
			$date = date(DATE_RSS, $date);
		}
		$this->_setProperty('video:publication_date', $date, $location);

		return $this;
	}

	/**
	 * Метод возвращает дату публикации видео для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return timestamp
	 */
	public function getVideoPublicDate($location = null)
	{
		$value = $this->_getProperty('video:publication_date', $location);

		if ( empty($value) ) {
			return null;
		}

		return strtotime($value);
	}

	/**
	 * Метод устанавливает категорию видео для текущего или указнного URL
	 *
	 * @param string $category
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoCategory($category, $location = null)
	{
		$this->_setProperty('video:category', $category, $location);

		return $this;
	}

	/**
	 * Метод возвращает категорию видео для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getVideoCategory($location = null)
	{
		return $this->_getProperty('video:category', $location);
	}

	/**
	 * Метод устанавливает отметку для текущего или указнного URL о том должно
	 * быть видео доступно только пользователям с отключенной функцией
	 * безопасного поиска
	 *
	 * @param string $family_friendly (yes|no)
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoFamilyFriendly($family_friendly, $location = null)
	{
		$this->_setProperty('video:family_friendly', $family_friendly, $location);

		return $this;
	}

	/**
	 * Метод возвращает отметку для текущего или указнного URL о том должно
	 * быть видео доступно только пользователям с отключенной функцией
	 * безопасного поиска
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getVideoFamilyFriendly($location = null)
	{
		return $this->_getProperty('video:family_friendly', $location);
	}

	/**
	 * Метод устанавливает список стран, где видео может или не может быть
	 * показано, в формате ISO 3166 для текущего или указнного URL
	 *
	 * @param string $restriction Список стран
	 * @param string $relationship (allow|deny) Показывать|Скрывать видео для указанного списка стран
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoRestriction($restriction, $relationship, $location = null)
	{
		$value = array(
			'_name_' => $restriction,
			'relationship' => $relationship,
		);
		$this->_setProperty('video:restriction', $value, $location);

		return $this;
	}

	/**
	 * Метод возвращает список стран, где видео может или не может быть
	 * показано, в формате ISO 3166 для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return array
	 */
	public function getVideoRestriction($location = null)
	{
		return $this->_getProperty('video:restriction', $location);
	}

	/**
	 * Метод устанавливает ссылку на галерею (коллекцию видеороликов), в
	 * которой размещено это видео, а также название этой галереи для текущего
	 * или указнного URL
	 *
	 * @param string $gallery URL галереи
	 * @param string $title Название галереи
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoGallery($gallery, $title = null, $location = null)
	{
		$value = array(
			'_name_' => $gallery,
			'title' => $title,
		);
		$this->_setProperty('video:gallery_loc', $value, $location);

		return $this;
	}

	/**
	 * Метод возвращает ссылку на галерею (коллекцию видеороликов), в
	 * которой размещено это видео, а также название этой галереи для текущего
	 * или указнного URL
	 *
	 * @param string $location = null
	 * @return array
	 */
	public function getVideoGallery($location = null)
	{
		return $this->_getProperty('video:gallery_loc', $location);
	}

	/**
	 * Метод устанавливает стоимость загрузки или просмотра видео, а также
	 * в какой валюте производится оплата, в формате ISO 4217 для текущего
	 * или указнного URL
	 *
	 * @param float $price Стоимость
	 * @param string $currency Наименование валюты
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoPrice($price, $currency, $location = null)
	{
		$value = array(
			'_name_' => $price,
			'currency' => $currency,
		);
		$this->_setProperty('video:price', $value, $location);

		return $this;
	}

	/**
	 * Метод возвращает стоимость загрузки или просмотра видео, а также
	 * в какой валюте производится оплата, в формате ISO 4217 для текущего
	 * или указнного URL
	 *
	 * @param string $location = null
	 * @return array
	 */
	public function getVideoPrice($location = null)
	{
		return $this->_getProperty('video:price', $location);
	}

	/**
	 * Метод устанавливает отметку о том требуется ли подписка для просмотра
	 * видео или нет для текущего или указнного URL
	 *
	 * @param string $requires_subscription (yes|no)
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoRequiresSubscription($requires_subscription, $location = null)
	{
		$this->_setProperty('video:requires_subscription', $requires_subscription, $location);

		return $this;
	}

	/**
	 * Метод возвращает отметку о том требуется ли подписка для просмотра
	 * видео или нет для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return string
	 */
	public function getVideoRequiresSubscription($location = null)
	{
		return $this->_getProperty('video:requires_subscription', $location);
	}

	/**
	 * Метод устанавливает имя или псевдоним пользователя, который загрузил
	 * видео, а также URL страницы с дополнительной информацией о этом
	 * пользователе для текущего или указнного URL
	 *
	 * @param string $uploader Имя или псевдоним пользователя
	 * @param string $info URL страницы с дополнительной информацией
	 * @param string $location = null
	 * @return $this
	 */
	public function setVideoUploader($uploader, $info = null, $location = null)
	{
		$value = array(
			'_name_' => $uploader,
			'info' => $info,
		);
		$this->_setProperty('video:uploader', $value, $location);

		return $this;
	}

	/**
	 * Метод возвращает имя или псевдоним пользователя, который загрузил
	 * видео, а также URL страницы с дополнительной информацией о этом
	 * пользователе для текущего или указнного URL
	 *
	 * @param string $location = null
	 * @return array
	 */
	public function getVideoUploader($location = null)
	{
		return $this->_getProperty('video:uploader', $location);
	}

	/**
	 * Метод добавляет тег, связанный с видео для текущего или указнного URL.
	 * Можно использовать не более 32 тегов.
	 *
	 * @param string $tag
	 * @param string $location = null
	 * @throws SitemapException
	 * @return $this
	 */
	public function addVideoTag($tag, $location = null)
	{
		$location_node = &$this->_locationNode($location);
		if ( isset($location_node['video:tag']) && 32 < count($location_node['video:tag']) ) {
			return $this;
		}

		$tag = htmlspecialchars( strip_tags( trim( $this->_linguistics->replaceSpecSymbol($tag) ) ) );

		$location_node['video:tag'][$tag] = $tag;

		return $this;
	}

	/**
	 * Метод удаляет тег, связанный с видео для текущего или указнного URL.
	 *
	 * @param string $tag
	 * @param string $location = null
	 * @return $this
	 */
	public function delVideoTag($tag, $location = null)
	{
		$location_node = &$this->_locationNode($location);
		unset($location_node['video:tag'][$tag]);

		return $this;
	}

	/**
	 * Метод возвращает список тегов, связанных с видео для текущего или
	 * указнного URL.
	 *
	 * @param string $location = null
	 * @return array
	 */
	public function getVideoTags($location = null)
	{
		$value = $this->_getProperty('video:tag', $location);

		if ( empty($value) ) {
			return null;
		}

		return array_keys($value);
	}

	/**
	 * Метод выводит xml-файл индекса Sitemap
	 *
	 * @return $this
	 */
	public function printIndexSitemap()
	{
		echo $this->_getIndexSitemap()->saveXML();

		return $this;
	}

	/**
	 * Метод сохраняет в файл индекс Sitemap
	 *
	 * @return $this
	 */
	public function saveIndexSitemap()
	{
		$this->_getIndexSitemap()
			->save($this->_indexPath . $this->_nameIndex . '.xml');

		return $this;
	}

	/**
	 * Метод выводит xml-файл указанного или текушего Sitemap
	 *
	 * @param string $name = null Имя sitemap, если не указано - текущий sitemap
	 * @param int $part = null Когда sitemap расбит на части, это номер sitemap
	 * @return $this
	 */
	public function printSitemap($name = null, $part = null)
	{
		echo $this->_getSitemap($name, $part)->saveXML();

		return $this;
	}

	/**
	 * Метод сохраняет файлы Sitemap
	 *
	 * @return $this
	 */
	public function saveSitemaps()
	{
		foreach ($this->_sitemaps as $name => $sitemap) {
			// Получаем кол-во URL в Sitemap
			$locations_count = count($sitemap['locations']);
			$i = 1;

			do {
				// Получаем данный Sitemap указанного имени и части
				$sitemap_data = $this->_getSitemap($name, $i);

				// Если кол-во URL в Sitemap больше максимального кол-ва URL в
				// одном Sitemap или текущая итерация больше 1 - формируем
				// номер файла sitemap
				$num_sitemap = null;
				if ($locations_count > $this->_maxCountLocInSitemap || $i>1) {
					$num_sitemap = '.' . $i;
				}

				// Если используется gzip сжатие файлов sitemap, делаем архив
				if ($this->_usageGzip) {
					$gz = gzopen($this->_sitemapsPath . $name. $num_sitemap . '.xml.gz','w6');
					gzwrite( $gz, $sitemap_data->saveXML() );
					gzclose($gz);
				} else {
					$sitemap_data->save($this->_sitemapsPath . $name . $num_sitemap. '.xml');
				}

				$locations_count -= $this->_maxCountLocInSitemap;
				$i++;
			} while ($locations_count > 0);
		}

		return $this;
	}

	/**
	 * Метод посылает пинг Google, чтобы он получил новый Sitemap
	 *
	 * @return $this
	 */
	public function pingGoogleSitemap()
	{
		$this->_pingSitemap(self::PING_GOOGLE);

		return $this;
	}

	/**
	 * Метод посылает пинг Яндекс, чтобы он получил новый Sitemap
	 *
	 * @return $this
	 */
	public function pingYandexSitemap()
	{
		$this->_pingSitemap(self::PING_YANDEX);

		return $this;
	}

	/**
	 * Метод посылает пинг Yahoo!, чтобы он получил новый Sitemap
	 *
	 * @return $this
	 */
	public function pingYahooSitemap()
	{
		$this->_pingSitemap(self::PING_YAHOO);

		return $this;
	}

	/**
	 * Метод посылает пинг Bing, чтобы он получил новый Sitemap
	 *
	 * @return $this
	 */
	public function pingBingSitemap()
	{
		$this->_pingSitemap(self::PING_BING);

		return $this;
	}
}