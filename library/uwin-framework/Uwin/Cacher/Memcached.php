<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Memcached, который отвечает за работу с кеширующим
 * сервером Memcached
 *
 * @category   Uwin
 * @package    Uwin\Cacher
 * @subpackage Memcached
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Cacher, к которому относится класс Memcached
 */
namespace Uwin\Cacher;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Cacher\Memcached\Exception as Exception;
use \Uwin\Profiler                   as Profiler;

// Объявление псевдонимов для всех используемых классов в данном файле

/**
 * Класс, который отвечает за работу с кеширующим сервером Memcache
 *
 * @category   Uwin
 * @package    Uwin\Cacher
 * @subpackage Memcached
 * @author     Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Memcached implements Interface_
{
  /**
   * Имя группы memcached серверов по умолчанию
   */
  const DEFAULT_NAME = 'main';

  /**
   * Массив ссылок на экземпляры данного класса(массив групп серверов)
   * @var array
   */
  private static $_memcacheds = array();

  /**
   * Текущее имя объекта данного класса, который выполняет роль
   * коннекотора к memcached
   * @var string
   */
  private static $_currentMemcachedName = self::DEFAULT_NAME;

  /**
   * Отметка о том используется кеширование или нет
   * @var bool
   */
  private static $_enabled = false;

  /**
   * Отметка о том, используется кеширование у группы серверов данного объекта
   * или нет
   */
  private $_enabledGroup = true;

  /**
   * Класс Memcached, через который и выполняется работа с сервером
   * @var \Memcached
   */
  private $_memcached = null;

  /**
   * Имя коннектора Memcached
   * @var string
   */
  private $_memcachedName = null;

    /**
     * Приватный конструктор класса, так как класс реализует паттерн Singlton
     *
     * @return Memcached
     */
  private function __construct() {}

  /**
   * Приватный магический метод класса, который запрещает клонировать объекты
   * этого класса, так как класс реализует паттерн Singlton.
   *
   * @return void
   */
  private function __clone() {}

  /**
   * Метод возвращает сформированный массив со значением тегами ключа
   *
   * @param string $key     - Ключ
   * @param mixed  $value   - Значение
   * @param bool   $changed - Отметка о том изменилось значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
   *
   * @return bool|array
   */
  private function _valueArrayExistsKey($key, $value, $changed) {
    $valueCache = array();
    $valueCache['value'] = $value;

    /** @noinspection PhpUndefinedMethodInspection */
    if (false === $tags = $this->_memcached->get($key) ) {
      return false;
    }
    $tags = null;
    if ( isset($tags['tags']) ) {
      $tags = $tags['tags'];
    }

    //Если указаны теги добавляемого значение, добавляем их в кеш
    if ( !empty($tags) ) {
      $valueCache['tags'] = $tags;
      if ($changed) {
        $valueCache['tags'] = $this->tagsVersions($tags, true);
      }
    }

    return $valueCache;
  }

  //TODO Не срочно. Сдлеать возможность включать защиту от одновременного пеестроения кеша

  /**
   * Метод возвращает ссылку на объект класса Memcached(группу серверов)
   * по-умолчанию, или на объект класса с указанным именем
   *
   * @param string $name = null - Имя группы memcached серверов
   *
   * @return Interface_
   */
  public static function getInstance($name = null)
  {
    // Если имя коннектора не указано, использовать текущее имя коннектора
    if (null == $name) {
      $name = self::$_currentMemcachedName;
    }

    // Если коннектора с таким именем не существует, создаем его и сохраняем
    // в статическом массиве данного класса, в котором хранятся все коннекторы
    if ( !isset(self::$_memcacheds[$name]) ) {
      self::$_memcacheds[$name] = new self;
      self::$_memcacheds[$name]->_memcachedName = $name;
    }

    // Возвращаем объект группы Memcached серверов
    return self::$_memcacheds[$name];
  }

  /**
   * Метод возвращает список имен групп Memcached серверов
   *
   * @return array
   */
  public static function getServersNamesList()
  {
    return array_keys(self::$_memcacheds);
  }

  /**
   * Метод устанавливает имя группы серверов, которая будет использоваться
   * по-умолчанию
   *
   * @param string $name - Имя группы серверов
   *
   * @return Interface_
   */
  public static function changeCurrentCacher($name)
  {
    self::$_currentMemcachedName = $name;

    return self::$_memcacheds[$name];
  }

  /**
   * Метод включает/отключает кеширование
   *
   * @param bool $enabled - Включить/выключить кеширование
   *
   * @return void
   */
  public static function enabled($enabled)
  {
    if ('false' === $enabled) {
      $enabled = false;
    }

    if ('true' === $enabled) {
      $enabled = true;
    }

    self::$_enabled = (bool)$enabled;
  }

  /**
   * Метод возвращает признак того включено кеширование или нет
   *
   * @return bool
   */
  public static function useCacher()
  {
    return self::$_enabled;
  }

  /**
   * Метод включает/отключает кеширование у группы серверов данного объекта
   *
   * @param bool $enabled - Включить/выключить кеширование
   *
   * @return void
   */
  public function enabledGroup($enabled)
  {
    $this->_enabledGroup = (bool)$enabled;
  }

  /**
   * Метод возвращает признак того включено кеширование или нет у группы
   * серверов данного объекта
   *
   * @return bool
   */
  public function useGroup()
  {
    // Если откоючено кеширование для всего Memcached, то и для текущей
    // группы отключено
    if ( !self::useCacher() ) {
      return false;
    }

    return $this->_enabledGroup;
  }

  /**
   * Метод добавляет сервер в список используемых серверов
   *
   * @param string $host       - Адрес сервера
   * @param int    $port       - Порт сервера
   * @param int    $weight = 0 - Вес сервера по отношению ко всем остальным серверам данной группы
   *
   * @return Interface_
   * @throws Exception
   */
  public function addServer($host, $port, $weight = 0)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Add server');

    // Если в списке групп серверов, нет группы с таким именем, создать
    // новый экземпляр класса memcached
    if ( empty($this->_memcached) ) {
      /** @noinspection PhpUndefinedClassInspection */
      $this->_memcached = new \Memcached;
    }

    // Добавить новый сервер в текущую группу серверов
    /** @noinspection PhpUndefinedMethodInspection */
    if ( !$this->_memcached->addServer($host, $port, $weight) ) {
      throw new Exception('Memcached error: failure add memcached server "host=' . $host . ' port=' . $port, 301);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод добавляет сервера переданные ему массивом в список используемых
   * серверов. Массив должен быть такого формата array(host, port, weight)
   *
   * @param array $servers - Массив адресов серверов
   *
   * @return Interface_
   * @throws Exception
   */
  public function addServers(array $servers)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Add servers');

    // Если в списке групп серверов, нет группы с таким именем, создать
    // новый экземпляр класса memcached
    if ( empty($this->_memcached) ) {
      /** @noinspection PhpUndefinedClassInspection */
      $this->_memcached = new \Memcached;
    }

    // Добавить новые сервера в текущую группу серверов
    /** @noinspection PhpUndefinedMethodInspection */
    if ( !$this->_memcached->addServers($servers) ) {
      throw new Exception('Memcached error: failure add memcached servers', 302);
    }
    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод удаляет Memcached сервера
   *
   * @return Interface_
   */
  public function deleteServers()
  {
    //Удалить группу серверов
    $this->_memcached = null;

    return $this;
  }

  /**
   * Метод возвращает список серверов
   *
   * @return array
   * @throws Exception
   */
  public function getServerList()
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return false;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Get server list');

    // Если в списке групп серверов, нет группы с таким именем, покинуть
    // функцию вернув ложь
    if ( empty($this->_memcached) ) {
      throw new Exception('Memcached error: failure get server', 303);
    }

    // Получить массив серверов
    /** @noinspection PhpUndefinedMethodInspection */
    $result = $this->_memcached->getServerList();

    Profiler::getInstance()->stopCheckpoint();

    return $result;
  }

  /**
   * Метод возвращает имя группы серверов
   *
   * @return string
   */
  public function getServerName()
  {
    return $this->_memcachedName;
  }

  /**
   * Метод возвращает массив со списокм указанных тегов и их версиями, а также,
   * если указано что нужно изменить версии тегов, то изменяет версии этих
   * тегов в Memcached
   *
   * @param string|array $tag             - Тег ил массив тегов
   * @param bool         $changed = false - Отметка о том, изменять версью указанных тегов, или брать текущую
   *
   * @return array
   */
  public function tagsVersions($tag, $changed = false) {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    if ( !is_array($tag) ) {
      $tag = array($tag);
    }

    // Если добавляемый кеш изменился, изменяем версии тегов
    if ($changed) {
      $tag = array_fill_keys( $tag, (string)microtime(1) );
      /** @noinspection PhpUndefinedMethodInspection */
      $this->_memcached->setMulti($tag);

      return $tag;
    }

    /** @noinspection PhpUndefinedMethodInspection */
    $tagsInCache = $this->_memcached->getMulti($tag);
        if (false === $tagsInCache) {
            $tagsInCache = array();
        }

    // Если есть какие-то теги, у которых нет установленной версии
    // в кеше, устанавливаем для них текущую версию
    if ( $tag != array_keys($tagsInCache) ) {
      $newTags = array_diff_key($tag, $tagsInCache);
      $newTags = array_fill_keys( $newTags, (string)microtime(1) );
      /** @noinspection PhpUndefinedMethodInspection */
      $this->_memcached->setMulti($newTags);

      $tagsInCache = array_merge($tagsInCache, $newTags);
    }

    return $tagsInCache;
  }

  /**
   * Метод тестирует соединение
   *
   * @return bool
   */
  public function testConnection() {
    // Если кеширование отключено, считаем что тест прошел успешно
    if ( !$this->useGroup() ) {
      return true;
    }

    try {
      $this->set('uwin-test' . time(), 1);
    } catch (\Exception $e) {
      return false;
    }

    return true;
  }

  /**
   * Метод добавляет указанный ключ/занчение в memcached. Этот метод
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
   * @throws Exception
   */
  public function add($key, $value, $expiration = 7200, array $tags = null, $changed = false)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Add key ' . $key);

    $valueCache = array();
    $valueCache['value'] = $value;

    //Если указаны теги добавляемого значение, добавляем их в кеш
    if ( !empty($tags) ) {
      $valueCache['tags'] = $this->tagsVersions($tags, $changed);
    }

    /** @noinspection PhpUndefinedMethodInspection */
    if ( !$this->_memcached->add($key, $valueCache, $expiration) ) {
      /** @noinspection PhpUndefinedMethodInspection */
      throw new Exception('Memcached error: failure add value by key "'
                . $key . '". Result code: "'
                . $this->_memcached->getResultCode() . '"', 304);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод устанавливает указанный ключ/занчение в memcached. Если такой ключ
   * уже существует, его значение будет заменено
   *
   * @param string $key                - Ключ
   * @param mixed  $value              - Значение
   * @param int    $expiration = 7200  - Время хранения ключа
   * @param array  $tags       = null  - Массив тегов, которые относятся к устанавливаемому значению
   * @param bool   $changed    = false - Отметка о том изменилось устанавливоемое значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
   *
   * @return Interface_
   * @throws Exception
   *
   */
  public function set($key, $value, $expiration = 7200, $tags = null, $changed = false)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Set key ' . $key . serialize($value));

    $valueCache = array();
    $valueCache['value'] = $value;

    //Если указаны теги добавляемого значение, добавляем их в кеш
    if ( !empty($tags) ) {
      $valueCache['tags'] = $this->tagsVersions($tags, $changed);
    } else {
      if (false !== $v = $this->_valueArrayExistsKey($key, $value, $changed) ) {
        $valueCache = $v;
      };
    }
    /** @noinspection PhpUndefinedMethodInspection */
    if ( !$this->_memcached->set($key, $valueCache, $expiration) ) {
      /** @noinspection PhpUndefinedMethodInspection */
      throw new Exception('Memcached error: failure set value by key "'
                . $key . '" Result code: "' .
                $this->_memcached->getResultCode() . '"', 305);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод устанавливает указанные в массиве ключи/занчения в memcached за
   * одну атомарную операцию
   * Массив должен быть такого формата:
   * [key1] =>
   *    [value] => Value of key
   *    [tags]  =>
   *        [tag1]
   *        [tag2]
   *        [tag3]
   *    [changed] => false
   *
   * @param array $items             - Ассоциативный массив ключей и их значений
   * @param int   $expiration = 7200 - Время хранения ключей
   *
   * @return Interface_
   * @throws Exception
   */
  public function setMulti(array $items, $expiration = 7200)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Multi set keys');

    // Проходимся по всем устаналиваемым значением и присваиваем тегам
    // значений версию
    foreach ($items as $key => $value) {
      //Если указаны теги добавляемого значение, добавляем их в кеш
      if ( !empty($value['tags']) ) {
        $items[$key]['tags'] = $this->tagsVersions($value['tags'],
                                $value['changed']);
      } else {
        if (false !== $v = $this->_valueArrayExistsKey($key, $value['value'], $value['changed']) ) {
          $items[$key] = $v;
        };
      }
    }

    /** @noinspection PhpUndefinedMethodInspection */
    if( !$this->_memcached->setMulti($items, $expiration) ) {
      /** @noinspection PhpUndefinedMethodInspection */
      throw new Exception('Memcached error: failure multi set values. '
                . 'Result code: "' .
                $this->_memcached->getResultCode() . '"', 306);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод возвращает занчение указанного ключа в текущей группе серверов,
   * если ключа не существует, возвращает flase
   *
   * $cache_cb - Вызывается в случае если ключ не найден. Получает 3
   * аргумента: объект memcached, имя переменной и пустую переменную по
   * ссылке. Для установки значения для ключа следует записать его в третий
   * аргумент и вернуть TRUE. При этом происходит запись в memcached и get()
   * возвращает записанное значение.
   *
   * @param string $key        - Ключ
   * @param callback $cache_cb - Функция, вызываемая при отсутствии ключа
   * @param float &$cas_token  - Уникальное значение, ассоциированное с элементом. Генерируется Memcached
   *
   * @return mixed
   */
  public function get($key, $cache_cb = null, &$cas_token = null)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return false;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Get key ' .$key);

    /** @noinspection PhpUndefinedMethodInspection */
    $value = $this->_memcached->get($key, $cache_cb, $cas_token);

    $tags = array();
    /** @noinspection PhpParamsInspection */
    if ( is_array($value) && array_key_exists('value', $value) ) {
      if ( isset($value['tags']) ) {
        $tags = $value['tags'];
      }
      $value = $value['value'];
    }

    // Если теги указаны, сравниваем их версии с текущими версиями тегов
    if ( !empty($tags) ) {
      /** @noinspection PhpUndefinedMethodInspection */
      $tagsInCache = $this->_memcached->getMulti( array_keys($tags) );
      // Если версии тегов отличаются, возвращаем ложь(тег отсутствует)
      if ($tagsInCache != $tags) {
        $value = false;
      }
    }

    Profiler::getInstance()->stopCheckpoint();

    return $value;
  }

  /**
   * Метод получает значения указанных ключей за одну итерацию, и возвращает
   * их в виде массива
   *
   * @param array $keys               - Массив списка ключей, значения которых нужно получить
   * @param array &$cas_tokens = null - уникальное значение, ассоциированное с элементом. Генерируется Memcached
   * @param int   $flags       = null
   *
   * @return array
   */
  public function getMulti(array $keys, array &$cas_tokens = null, $flags = null)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return false;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Multi get keys');

    /** @noinspection PhpUndefinedMethodInspection */
    $values = $this->_memcached->getMulti($keys, $cas_tokens, $flags);

    // Проходимся по всем полученным значениям и проверяем, соответсвуют ли
    // их версии тегов
    foreach ($values as $key => $value) {
      $tags = array();
      /** @noinspection PhpParamsInspection */
      if ( is_array($value) && array_key_exists('tags', $value) ) {
        $tags = $value['tags'];
      }

      // Если теги указаны, сравниваем их версии с текущими версиями тегов
      if ( !empty($tags) ) {
        /** @noinspection PhpUndefinedMethodInspection */
        $tagsInCache = $this->_memcached->getMulti( array_keys($tags) );
        // Если версии тегов отличаются, возвращаем ложь(тег отсутствует)
        if ($tagsInCache != $tags) {
          unset($values[$key]);
        }
      }
    }

    Profiler::getInstance()->stopCheckpoint();

    return $values;
  }

  /**
   * Метод выполняет атомарную операцию установки (проверяет и устанавливает).
   * Значение ключа будет установлен только при отсутствии других клиентов,
   * которые также пытаются установить это ключ. Проверка осуществляется
   * через переменную $cas_token, которая является уникальным 64-разрядным
   * значением.
   *
   * @param float  $cas_token          - Уникальное значение асоцированное с существующим ключом. Генерируется сервером memcached
   * @param string $key                - Ключ
   * @param mixed  $value              - Значение
   * @param int    $expiration = 7200  - Время хранения ключа
   * @param array  $tags       = null  - Массив тегов, которые относятся к устанавливаемому значению
   * @param bool   $changed    = false - Отметка о том изменилось устанавливоемое значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
   *
   * @return Interface_
   */
  public function cas($cas_token, $key, $value, $expiration = 7200, $tags = null, $changed = false)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Check and set key '. $key);

    $valueCache = array();
    $valueCache['value'] = $value;

    //Если указаны теги добавляемого значение, добавляем их в кеш
    if ( !empty($tags) ) {
      $valueCache['tags'] = $this->tagsVersions($tags, $changed);
    } else {
      if (false !== $v = $this->_valueArrayExistsKey($key, $value, $changed) ) {
        $valueCache = $v;
      };
    }

    /** @noinspection PhpUndefinedMethodInspection */
    $this->_memcached->cas($cas_token, $key, $valueCache, $expiration);

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод добавялет в конец существующего значения указанного ключа
   * переданное значение
   *
   * @param string $key            - Ключ
   * @param mixed  $value          - Значение
   * @param bool   $changed = true - Отметка о том изменилось значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
   *
   * @return Interface_
   * @throws Exception
   */
  public function append($key, $value, $changed = true)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Append value to key '. $key);

    $value = $this->_valueArrayExistsKey($key, $value, $changed);

    /** @noinspection PhpUndefinedMethodInspection */
    if ( false === $value && !$this->_memcached->append($key, $value) ) {
      /** @noinspection PhpUndefinedMethodInspection */
      throw new Exception('Memcached error: failure append value to key "'
                . $key . '". Result code: "'
                . $this->_memcached->getResultCode() . '"', 307);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод добавялет в начало существующего значения указанного ключа
   * переданное значение
   *
   * @param string $key            - Ключ
   * @param mixed  $value          - Значение
   * @param bool   $changed = true - Отметка о том изменилось значение или нет(тоесть спрасывать группу кешей с указанными тегами или нет)
   *
   * @return Interface_
   * @throws Exception
   */
  public function prepend($key, $value, $changed = true)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Prepend value to key '. $key);

    $value = $this->_valueArrayExistsKey($key, $value, $changed);

    /** @noinspection PhpUndefinedMethodInspection */
    if ( false === $value && !$this->_memcached->prepend($key, $value) ) {
      /** @noinspection PhpUndefinedMethodInspection */
      throw new Exception('Memcached error: failure prepend value to key "'
                . $key . '". Result code: "'
                . $this->_memcached->getResultCode() . '"', 308);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

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
   * @throws Exception
   */
  public function replace($key, $value, $expiration = 7200, $changed = true)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Replace key ' . $key);

    $value = $this->_valueArrayExistsKey($key, $value, $changed);

    /** @noinspection PhpUndefinedMethodInspection */
    if ( false === $value && !$this->_memcached->replace($key, $value, $expiration) ) {
      /** @noinspection PhpUndefinedMethodInspection */
      throw new Exception('Memcached error: failure replace value to key "'
                . $key . '". Result code: "'
                . $this->_memcached->getResultCode() . '"', 309);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод удаляет указанный ключ через указанное время
   *
   * @param string $key      - Ключ
   * @param int    $time = 0 - Время в секкундах
   *
   * @return Interface_
   */
  public function delete($key, $time = 0)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Delete key ' . $key);

    /** @noinspection PhpUndefinedMethodInspection */
    $this->_memcached->delete($key, $time);

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

  /**
   * Метод полностью очищает всю память текущего сервера/серверов с указанной
   * задержкой
   *
   * @param int $delay = 0 - Задержка в секундах
   *
   * @return Interface_
   * @throws Exception
   */
  public function flush($delay = 0)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return $this;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Flush all data');

    /** @noinspection PhpUndefinedMethodInspection */
    if ( !$this->_memcached->flush($delay) ) {
      throw new Exception('Memcached error: failure flush servers "' .
                $this->_memcachedName . '"', 310);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $this;
  }

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
  public function increment($key, $offset = 1, $tags = null)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return false;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Increment key ' . $key);

    /** @noinspection PhpUndefinedMethodInspection */
    $result = $this->_memcached->increment($key, $offset);

    // Если ключа не существует, устанавливаем ключ равным 0
    if (false === $result) {
      /** @noinspection PhpUndefinedMethodInspection */
      $this->_memcached->set($key, 0);
      $result = 0;
    }

    //Если указаны теги инкрементируемого значения, обновляем их версию
    if ( !empty($tags) ) {
      $this->tagsVersions($tags, true);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $result;
  }

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
  public function decrement($key, $offset = 1, $tags = null)
  {
    // Если кеширование отключено, выходим с функции
    if ( !$this->useGroup() ) {
      return false;
    }

    Profiler::getInstance()->startCheckpoint('Memcached', 'Decrement key ' . $key);

    /** @noinspection PhpUndefinedMethodInspection */
    $result = $this->_memcached->decrement($key, $offset);

    // Если ключа не существует, устанавливаем ключ равным 0
    if (false === $result) {
      /** @noinspection PhpUndefinedMethodInspection */
      $this->_memcached->set($key, 0);
      $result = 0;
    }

    //Если указаны теги декрементируемого значения, обновляем их версию
    if ( !empty($tags) ) {
      $this->tagsVersions($tags, true);
    }

    Profiler::getInstance()->stopCheckpoint();

    return $result;
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function getStats() {
    return $this->_memcached->getStats();
  }
}
