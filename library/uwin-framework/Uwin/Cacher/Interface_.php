<?php
/**
 * Uwin Framework
 *
 * Файл содержащий интерфейс Uwin\Cacher\Interface_, который описывает интерфейс
 * всех классов кешировщиков
 *
 * @category   Uwin
 * @package    Uwin\Cacher
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Cacher, к которому относится
 * интерфейс Interface_
 */
namespace Uwin\Cacher;

/**
 * Интерфейс, который описывает интерфейс всех классов кешировщиков
 *
 * @category   Uwin
 * @package    Uwin\Cacher
 * @author     Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
interface Interface_
{
	/**
	 * Метод возвращает ссылку на объект класса кешировщика (группу серверов)
	 * по-умолчанию, или на объект класса с указанным именем
	 *
	 * @param string $name = null - Имя группы кеширующих серверов
	 *
	 * @return Interface_
	 */
	public static function getInstance($name = null);

	/**
	 * Метод возвращает список имен групп кеширующих серверов
	 *
	 * @return array
	 */
	public static function getServersNamesList();

	/**
	 * Метод устанавливает имя группы серверов, которая будет использоваться
	 * по-умолчанию
	 *
	 * @param string $name - Имя группы серверов
	 *
	 * @return Interface_
	 */
	public static function changeCurrentCacher($name);

	/**
	 * Метод включает/отключает кеширование
	 *
	 * @param bool $enabled - Включить/выключить кеширование
	 *
	 * @return void
	 */
	public static function enabled($enabled);

	/**
	 * Метод возвращает признак того включено кеширование или нет
	 *
	 * @return bool
	 */
	public static function useCacher();

	/**
	 * Метод включает/отключает кеширование у группы серверов данного объекта
	 *
	 * @param bool $enabled - Включить/выключить кеширование
	 *
	 * @return void
	 */
	public function enabledGroup($enabled);

	/**
	 * Метод возвращает признак того включено кеширование или нет у группы
	 * серверов данного объекта
	 *
	 * @return bool
	 */
	public function useGroup();

	/**
	 * Метод добавляет сервер в список используемых серверов
	 *
	 * @param string $host       - Адрес сервера
	 * @param int    $port       - Порт сервера
	 * @param int    $weight = 0 - Вес сервера по отношению ко всем остальным серверам данной группы
	 *
	 * @return Interface_
	 */
	public function addServer($host, $port, $weight = 0);

	/**
	 * Метод добавляет сервера переданные ему массивом в список используемых
	 * серверов. Массив должен быть такого формата array(host, port, weight)
	 *
	 * @param array $servers - Массив адресов серверов
	 *
	 * @return Interface_
	 */
	public function addServers(array $servers);

	/**
	 * Метод удаляет кеширующие сервера
	 *
	 * @return Interface_
	 */
	public function deleteServers();

	/**
	 * Метод возвращает список серверов
	 *
	 * @return array
	 */
	public function getServerList();

	/**
	 * Метод возвращает имя группы серверов
	 *
	 * @return string
	 */
	public function getServerName();

	/**
	 * Метод возвращает массив со списокм указанных тегов и их версиями, а также,
	 * если указано что нужно изменить версии тегов, то изменяет версии этих
	 * тегов в кеше
	 *
	 * @param string|array $tag             - Тег ил массив тегов
	 * @param bool         $changed = false - Отметка о том, изменять версью указанных тегов, или брать текущую
	 *
	 * @return array
	 */
	public function tagsVersions($tag, $changed = false);

	/**
	 * Метод добавляет указанный ключ/занчение в кеш. Этот метод
	 * отличается от set() тем, что в случае присутствия такого ключа метод
	 * вызовет исключительную ситуацию
	 *
	 * @param string $key                - Ключ
	 * @param mixed  $value              - Значение
	 * @param int    $expiration = 7200  - Время хранения ключа
	 * @param array  $tags       = null  - Массив тегов, которые относятся к добавлемому значению
	 * @param bool   $changed    = false - Отметка о том изменилось додавляемое значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
	 *
	 * @return Interface_
	 */
	public function add($key, $value, $expiration = 7200, array $tags = null,
		$changed = false);

	/**
	 * Метод устанавливает указанный ключ/занчение в кеш. Если такой ключ
	 * уже существует, его значение будет заменено
	 *
	 * @param string $key                - Ключ
	 * @param mixed  $value              - Значение
	 * @param int    $expiration = 7200  - Время хранения ключа
	 * @param array  $tags       = null  - Массив тегов, которые относятся к устанавливаемому значению
	 * @param bool   $changed    = false - Отметка о том изменилось устанавливоемое значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
	 *
	 * @return Interface_
	 *
	 */
	public function set($key, $value, $expiration = 7200, $tags = null,
		$changed = false);

	/**
	 * Метод устанавливает указанные в массиве ключи/занчения в кеше за
	 * одну атомарную операцию
	 * Массив должен быть такого формата:
	 * [key1] =>
	 * 		[value] => Value of key
	 * 		[tags]  =>
	 * 				[tag1]
	 * 				[tag2]
	 * 				[tag3]
	 * 		[changed] => false
	 *
	 * @param array $items             - Ассоциативный массив ключей и их значений
	 * @param int   $expiration = 7200 - Время хранения ключей
	 *
	 * @return Interface_
	 */
	public function setMulti(array $items, $expiration = 7200);

	/**
	 * Метод возвращает занчение указанного ключа в текущей группе серверов,
	 * если ключа не существует, возвращает flase
	 *
	 * $cache_cb - Вызывается в случае если ключ не найден. Получает 3
	 * аргумента: объект кешировщика, имя переменной и пустую переменную по
	 * ссылке. Для установки значения для ключа следует записать его в третий
	 * аргумент и вернуть TRUE. При этом происходит запись в кеш и get()
	 * возвращает записанное значение.
	 *
	 * @param string $key        - Ключ
	 * @param callback $cache_cb - Функция, вызываемая при отсутствии ключа
	 * @param float &$cas_token  - Уникальное значение, ассоциированное с элементом. Генерируется кешировщиком
	 *
	 * @return mixed
	 */
	public function get($key, $cache_cb = null, &$cas_token = null);

	/**
	 * Метод получает значения указанных ключей за одну итерацию, и возвращает
	 * их в виде массива
	 *
	 * @param array $keys               - Массив списка ключей, значения которых нужно получить
	 * @param array &$cas_tokens = null - Уникальное значение, ассоциированное с элементом. Генерируется кешировщиком
	 * @param int   $flags       = null
	 *
	 * @return array
	 */
	public function getMulti(array $keys, array &$cas_tokens = null,
		$flags = null);

	/**
	 * Метод выполняет атомарную операцию установки (проверяет и устанавливает).
	 * Значение ключа будет установлен только при отсутствии других клиентов,
	 * которые также пытаются установить это ключ. Проверка осуществляется
	 * через переменную $cas_token, которая является уникальным 64-разрядным
	 * значением.
	 *
	 * @param float  $cas_token          - Уникальное значение асоцированное с существующим ключом. Генерируется кешировщиком
	 * @param string $key                - Ключ
	 * @param mixed  $value              - Значение
	 * @param int    $expiration = 7200  - Время хранения ключа
	 * @param array  $tags       = null  - Массив тегов, которые относятся к устанавливаемому значению
	 * @param bool   $changed    = false - Отметка о том изменилось устанавливоемое значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
	 *
	 * @return Interface_
	 */
	public function cas($cas_token, $key, $value, $expiration = 7200,
		$tags = null, $changed = false);

	/**
	 * Метод добавялет в конец существующего значения указанного ключа
	 * переданное значение
	 *
	 * @param string $key            - Ключ
	 * @param mixed  $value          - Значение
	 * @param bool   $changed = true - Отметка о том изменилось значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
	 *
	 * @return Interface_
	 */
	public function append($key, $value, $changed = true);

	/**
	 * Метод добавялет в начало существующего значения указанного ключа
	 * переданное значение
	 *
	 * @param string $key            - Ключ
	 * @param mixed  $value          - Значение
	 * @param bool   $changed = true - Отметка о том изменилось значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
	 *
	 * @return Interface_
	 */
	public function prepend($key, $value, $changed = true);

	/**
	 * Метод заменяет значение существующего ключа новым значением. Если ключа
	 * не существует, вызывается исключительная ситуация
	 *
	 * @param string $key               - Ключ
	 * @param mixed  $value             - Значение
	 * @param int    $expiration = 7200 - Время хранения ключа в секундах
	 * @param bool   $changed    = true - Отметка о том изменилось устанавливоемое значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
	 *
	 * @return Interface_
	 */
	public function replace($key, $value, $expiration = 7200, $changed = true);

	/**
	 * Метод удаляет указанный ключ через указанное время
	 *
	 * @param string $key      - Ключ
	 * @param int    $time = 0 - Время в секкундах
	 *
	 * @return Interface_
	 */
	public function delete($key, $time = 0);

	/**
	 * Метод полностью очищает всю память текущего сервера/серверов с указанной
	 * задержкой
	 *
	 * @param int $delay = 0 - Задержка в секундах
	 *
	 * @return Interface_
	 */
	public function flush($delay = 0);

	/**
	 * Метод инкрементриует значение указанного ключа на значение инкремента и
	 * возвращает полученное значение
	 *
	 * @param string $key           - Ключ
	 * @param int    $offset = 1    - Значение инкремента
	 * @param array  $tags   = null - Массив тегов, которые относятся к инкрементируемому ключу
	 *
	 * @return int
	 */
	public function increment($key, $offset = 1, $tags = null);

	/**
	 * Метод декрементриует значение указанного ключа на значение декремента и
	 * возвращает полученное значение
	 *
	 * @param string $key           - Ключ
	 * @param int    $offset = 1    - Значение декремента
	 * @param array  $tags   = null - Массив тегов, которые относятся к декрементируемому ключу
	 *
	 * @return int
	 */
	public function decrement($key, $offset = 1, $tags = null);
}
