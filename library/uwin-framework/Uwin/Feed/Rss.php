<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Feed\Rss, который отвечает за выполнение запросов
 * к базе данных
 *
 * @category   Uwin
 * @package    Uwin\Db
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Feed, к которому относится класс Rss
 */
namespace Uwin\Feed;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Linguistics as Linguistics;

/**
 * Класс, который отвечает за выполнение запросов к базе данных
 *
 * @category  Uwin
 * @package   Uwin\Feed
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */

//TODO Дописать полностью доку + доработать методы нужные для более гобкой манипуляции над RSS

class Rss
{
	/**
	 * Переменная, где содержится rss
	 * @var DOMDocument
	 */
	private $_rss;

	/**
	 * Текущий канал rss
	 * @var DOMNode
	 */
	private $_currentChannel;

	/**
	 * Текущий узел rss
	 * @var DOMNode
	 */
	private $_currentNode;

	/**
	 * Объект класса \Uwin\Linguistics, который используется для преобразования
	 * html сымволов
	 * @var \Uwin\Linguistics
	 */
	private $_linguistics;

	/**
	 * Метод формирует время в формате, который используется в RSS
	 *
	 * @param int $timestamp = null Дата/Время
	 * @return string
	 */
	private function _rssDate($timestamp = null)
	{
		if (null == $timestamp) {
			$timestamp = time();
		}

		return date(DATE_RSS, $timestamp);
	}

	/**
	 * Конструктор класса, в котором содается xml с подготовленной первоначальной
	 * структурой, которая используется в RSS
	 *
	 * @return this
	 */
	public function __construct()
	{
		// Создаем xml
		$this->_rss = new \DOMDocument('1.0', 'UTF-8');
		$this->_rss->formatOutput = true;

		// Создаем узел RSS и присваеваем ему атрибут version=2.0
		$node = $this->_rss->createElement('rss');
		$this->_rss->appendChild($node);
		$attribute = $this->_rss->createAttribute('version');
		$attribute_node = $node->appendChild($attribute);
		$attribute_text = $this->_rss->createTextNode('2.0');
		$attribute_node->appendChild($attribute_text);

		$this->_linguistics = new Linguistics;

		return $this;
	}

	/**
	 * Метод создает канал rss
	 *
	 * @return this
	 */
	public function addChannel()
	{
		$node = $this->_rss->createElement('channel');
		$this->_currentChannel = $this->_rss->getElementsByTagName('rss')->item(0)->appendChild($node);

		return $this;
	}

	//TODO Сделать метод для удаления канала

	/**
	 * Метод устанавливает заглавие rss канала
	 *
	 * @param string $title Заглавие rss канала
	 * @return this
	 */
	public function setTitle($title)
	{
		$title = strip_tags($this->_linguistics->replaceSpecSymbol($title));
		$title = str_replace('&', '&amp;', $title);

		$node = $this->_rss->createElement('title', $title);
		$this->_currentChannel->appendChild($node);

		return $this;
	}

	/**
	 * Метод возвращает заглавие текущего rss канала
	 *
	 * @return string|false
	 */
	public function getTitle()
	{
		$node = $this->_currentChannel->getElementsByTagName('title')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * Метод устанавливает ссылку на страницу с которой ведется rss трансляция
	 *
	 * @param string $link Ссылка на страницу сайта с которой ведется rss трансляция
	 * @return this
	 */
	public function setLink($link)
	{
		$node = $this->_rss->createElement('link', $link);
		$this->_currentChannel->appendChild($node);

		return $this;
	}

	/**
	 * Метод возвращает ссылку на страницу с которой ведется rss трансляция
	 *
	 * @return string|false
	 */
	public function getLink()
	{
		$node = $this->_currentChannel->getElementsByTagName('link')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * Метод устанавливает описание текущего rss канала
	 *
	 * @param string $description Описание
	 * @return this
	 */
	public function setDescription($description)
	{
		$node = $this->_rss->createElement('description');
		$node = $this->_currentChannel->appendChild($node);
		$cdata = $this->_rss->createCDATASection($description);
		$node->appendChild($cdata);

		return $this;
	}

	/**
	 * Метод возвращает описание текущего rss канала
	 * @return string|false;
	 */
	public function getDescription()
	{
		$node = $this->_currentChannel->getElementsByTagName('description')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * Метод устанавливает язык текущего rss канала
	 *
	 * @param string $language Язые
	 * @return this
	 */
	public function setLanguage($language)
	{
		$node = $this->_rss->createElement('language', $language);
		$this->_currentChannel->appendChild($node);

		return $this;
	}

	/**
	 * Метод возвращает язык текущего rss канала
	 *
	 * @return string|false
	 */
	public function getLanguage()
	{
		$node = $this->_currentChannel->getElementsByTagName('language')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * Метод устаналивает редактора для текущего rss канала
	 * @param string $managingEditor Email редактора
	 */
	public function setManagingEditor($managingEditor)
	{
		$node = $this->_rss->createElement('managingEditor', $managingEditor);
		$this->_currentChannel->appendChild($node);

		return $this;
	}

	/**
	 * Метод возвращает редактора для текущего rss канала
	 *
	 * @return string|false
	 */
	public function getManagingEditor()
	{
		$node = $this->_currentChannel->getElementsByTagName('managingEditor')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	public function setGenerator($generator)
	{
		$node = $this->_rss->createElement('generator', $generator);
		$this->_currentChannel->appendChild($node);

		return $this;
	}

	public function getGenerator()
	{
		$node = $this->_currentChannel->getElementsByTagName('generator')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	public function setPubDate($pubDate = null)
	{
		$node = $this->_rss->createElement('pubDate', $this->_rssDate($pubDate));
		$this->_currentChannel->appendChild($node);

		return $this;
	}

	public function getPubDate()
	{
		$node = $this->_currentChannel->getElementsByTagName('pubDate')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	public function setCopyright($copyright)
	{
		$node = $this->_rss->createElement('copyright', $copyright);
		$this->_currentChannel->appendChild($node);

		return $this;
	}

	public function getCopyright()
	{
		$node = $this->_currentChannel->getElementsByTagName('copyright')->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	public function setImage($url, $link = null, $title = null)
	{
		$node = $this->_rss->createElement('image');
		$this->_currentChannel->appendChild($node);

		$url_node = $this->_rss->createElement('url', $url);
		$node->appendChild($url_node);

		if (null != $link) {
			$link_node = $this->_rss->createElement('link', $link);
			$node->appendChild($link_node);
		}

		if (null != $title) {
			$title_node = $this->_rss->createElement('title', $title);
			$node->appendChild($title_node);
		}

		return $this;
	}

	public function getImage($property = null)
	{
		//TODO Доделать возврат массива в переменной image
		if ( empty($property) ) {
			return $this->_image;
		}

		$image_node = $this->_currentChannel->getElementsByTagName('image')->item(0);
		if ( empty($image_node) ) {
			return false;
		}

		$node = $image_node->getElementsByTagName($property)->item(0);
		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	public function addItem()
	{
		$node = $this->_rss->createElement('item');
		$this->_currentNode = $this->_currentChannel->appendChild($node);

		return $this;
	}

	/**
	 * @param string $title
	 */
	public function setItemTitle($title)
	{
		$title = strip_tags($this->_linguistics->replaceSpecSymbol($title));

		$node = $this->_rss->createElement('title');
		$node = $this->_currentNode->appendChild($node);
		$cdata = $this->_rss->createCDATASection($title);
		$node->appendChild($cdata);

		return $this;
	}

	/**
	 * @param int $num_item = null
	 */
	public function getItemTitle($num_item = null)
	{
		if (null == $num_item) {
			$node = $this->_currentNode->getElementsByTagName('title')->item(0);
		} else {
			$node = $this->_currentChannel->getElementsByTagName('item')
				->item($num_item)->getElementsByTagName('title')->item(0);
		}

		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * @param string $description
	 */
	public function setItemDescription($description)
	{
		$node = $this->_rss->createElement('description');
		$node = $this->_currentNode->appendChild($node);
		$cdata = $this->_rss->createCDATASection($description);
		$node->appendChild($cdata);

		return $this;
	}

	/**
	 * @param int $num_item = null
	 */
	public function getItemDescription($num_item = null)
	{
		if (null == $num_item) {
			$node = $this->_currentNode->getElementsByTagName('description')->item(0);
		} else {
			$node = $this->_currentChannel->getElementsByTagName('item')
				->item($num_item)->getElementsByTagName('description')->item(0);
		}

		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * @param string $link
	 */
	public function setItemLink($link)
	{
		$node = $this->_rss->createElement('link', $link);
		$this->_currentNode->appendChild($node);

		return $this;
	}

	/**
	 * @param int $num_item = null
	 */
	public function getItemLink($num_item = null)
	{
		if (null == $num_item) {
			$node = $this->_currentNode->getElementsByTagName('link')->item(0);
		} else {
			$node = $this->_currentChannel->getElementsByTagName('item')
				->item($num_item)->getElementsByTagName('link')->item(0);
		}

		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * @param string $author
	 */
	public function setItemAuthor($author)
	{
		$node = $this->_rss->createElement('author', $author);
		$this->_currentNode->appendChild($node);

		return $this;
	}

	/**
	 * @param int $num_item = null
	 */
	public function getItemAuthor($num_item = null)
	{
		if (null == $num_item) {
			$node = $this->_currentNode->getElementsByTagName('author')->item(0);
		} else {
			$node = $this->_currentChannel->getElementsByTagName('item')
				->item($num_item)->getElementsByTagName('author')->item(0);
		}

		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * @param int pubDate
	 */
	public function setItemPubDate($pubDate = null)
	{
		$node = $this->_rss->createElement('pubDate', $this->_rssDate($pubDate));
		$this->_currentNode->appendChild($node);

		return $this;
	}

	/**
	 * @param int $num_item = null
	 */
	public function getItemPubDate($num_item = null)
	{
		if (null == $num_item) {
			$node = $this->_currentNode->getElementsByTagName('pubDate')->item(0);
		} else {
			$node = $this->_currentChannel->getElementsByTagName('item')
				->item($num_item)->getElementsByTagName('pubDate')->item(0);
		}

		if ( empty($node) ) {
			return false;
		}

		return $node->textContent;
	}

	/**
	 * @param array categories
	 */
	public function setItemCategories($categories)
	{
		foreach ($categories as $category) {
			$category = strip_tags($this->_linguistics->replaceSpecSymbol($category));
			$category = str_replace('&', '&amp;', $category);

			$node = $this->_rss->createElement('category', $category);
			$this->_currentNode->appendChild($node);
		}

		return $this;
	}

	public function addItemCategory($category)
	{
		$category = strip_tags($this->_linguistics->replaceSpecSymbol($category));
		$category = str_replace('&', '&amp;', $category);

		$node = $this->_rss->createElement('category', $category);
		$this->_currentNode->appendChild($node);

		return $this;
	}

	public function getCountItems()
	{
//		return count($this->_items);
	}

	public function printRss(&$variable = false)
	{
		if (false === $variable ) {
			echo $this->_rss->saveXML();
		} else {
			$variable = $this->_rss->saveXML();
		}

		return true;
	}
}
