<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Db\Query, который отвечает за выполнение запросов
 * к базе данных
 *
 * @category   Uwin
 * @package    Uwin\Db
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2012 UwinArt Studio (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Db, к которому относится класс Query
 */
namespace Uwin\Db;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Profiler,
    \Uwin\Controller\Front,
    \Uwin\Controller\Request,
    \Uwin\Db\Exception as DbException;

/**
 * Класс, который отвечает за выполнение запросов к базе данных
 *
 * @category  Uwin
 * @package   Uwin\Db
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Query
{
  /**
   * Ссылка на объект класса базы данных
   * @var null|\Uwin\Db
   */
  private $_db = null;

  /**
   * SQL-запрос
   * @var null|string
   */
  private $_sql = null;

  /**
   * Параметры переданные в sql-запрос
   * @var array
   */
  private $_params = array();

  /**
   * Тексто-заменители переданные в sql-запрос
   * @var array
   */
  private $_replacements = array();

  /**
   * Язык полей которые должен вернуть запрос
   * @var null
   */
  private $_language = null;

  /**
   * Русурс выполнения запроса
   * @var resource
   */
  private $_resultQuery = null;

  /**
   * Массив тегов для кеширвоания запроса
   * @var array
   */
  private $_tags = array();

  /**
   * Таблица к которой делается запрос
   * @var string
   */
  private $_table = null;

  private $_typeByTable = null;

  /**
   * Поля, которые будут возвращены с запроса
   * @var string
   */
  private $_fields = null;

  /**
   * Условия фильтрации запроса
   * @var string
   */
  private $_where = null;

  private $_joins = [];

  /**
   * Группировка Group By
   * @var string
   */
  private $_groupby = null;

  /**
   * Перечень полей сортировки
   * @var string
   */
  private $_orderby = null;

  /**
   * Ограничение кол-ва выводимых запросов
   * @var int
   */
  private $_limit = null;

  /**
   * Смещение записей
   * @var int
   */
  private $_offset = null;

  /**
   * Контсруктор класса, в который обязательно нужно предать объект базы
   * данных Uwin\Db
   *
   * @param \Uwin\Db $db - Объект класса базы данных Uwin\Db
   *
   * @return Query
   */
  public function __construct($db) { // {{{
    $this->_db = $db;

    return $this;
  } // }}}

  /**
   * Метод проверяет наличие ключа в Memcached, если такой существует,
   * возвращаетего значение, иначе выполняет sql-запрос к базе данных и если
   * запроса вернул результат возвращает истину
   *
   * @param string $keyMemcached - Ключь Memcached
   *
   * @return bool|null
   *
   * @throws DbException - Ошибка работы с базой данных
   */
  private function _fetch($keyMemcached) { // {{{
    // Обнуляем дескриптор на предедущий запрос, так как если данные запроса
    // будут братся с кэша, дескриптор не изменится и будет указывать на
    // старый запрос
    $this->_resultQuery = null;

    // Получаем данный ключ с memcached
    $dataMemcached = false;
    $memcached = $this->_db->getMemcached();
    if (null != $keyMemcached && !empty($memcached) ) {
      $dataMemcached = $memcached->get($keyMemcached);
    }

    // Если ключ не найден
    if (false === $dataMemcached) {
      Profiler::getInstance()->startCheckpoint('Query', $this->_getSqlWithReplacements());

      // Если соединение не установлено - установить его
      $this->_db->connect();

      // Выполняем запрос и устанавливаем дескриптор запроса
      if ( !$this->_resultQuery = pg_query_params(
        $this->_db->dbAdapter(), $this->_getSqlWithReplacements(), $this->_params) ) {
        throw new DbException('Db error: failure "' .
          pg_result_error($this->_resultQuery) . '" in query "' .
          $this->getSql(true) . '"', 503);
      }

      // Если запрос ничего не вернул, выходим с функции, вернув пустой массив
      if ( 0 == $this->countRows() ) {
        Profiler::getInstance()->stopCheckpoint();

        return array();
      }

      Profiler::getInstance()->stopCheckpoint();

      return true;
    }

    return $dataMemcached;
  } // }}}

  /**
   * Метод возвращает запрос с подстваленными уже тексто-заменителями и
   * парамерами
   *
   * @return null|string
   */
  private function _getSqlWithParams() { // {{{
    $sql = $this->_getSqlWithReplacements();

    $i = 1;
    foreach ($this->_params as $value) {
      $value = (string)$value;
      if ( '' == $value || 'null' == $value ) {
        $value = 'null';
      } else
        if ( 'true' != $value && 'false' != $value )  {
          $this->_db->connect();
          $value = "'" . pg_escape_string($this->_db->dbAdapter(), $value) . "'";
        }

      $sql = preg_replace('#\$(' . $i. ')(\D)#s', $value . "$2", $sql . ' ');
      $i++;
    }

    return $sql;
  } // }}}


  private function _getCalculatedSql() { // {{{
    if ($this->_table === null) {
      return $this->_sql;
    }

    $sql = 'select ';
    if ( !empty($this->_fields) ) {
      $sql .= $this->_fields . ' ';
    } else {
      $sql .= '* ';
    }


    if ('test' !== Front::getInstance()->getMode()) {
      $sql .= 'from ' . $this->_table;
    } else {
      $jsonData = '[]';

      $class = new \ReflectionClass(get_class($this->_db->getCalledClass()));
      $testDataFile = dirname(dirname( $class->getFileName() )) . DIR_SEP
        . 'tests/data.json';

      $table = $this->_table;
      if ( !empty($this->_typeByTable) ) {
        $table = $this->_typeByTable;
      }

      if ( file_exists($testDataFile) ) {
        $testData = json_decode( file_get_contents($testDataFile), true);
        if ( array_key_exists($table, $testData) ) {
          $jsonData = json_encode($testData[$table], JSON_UNESCAPED_UNICODE);
        }
      }

      $sql .= 'from json_populate_recordset(null::' . $table . ', \''
        . $jsonData . '\') tbl ';
    }

    if ( !empty($this->_joins) ) {
      foreach ($this->_joins as $join){
        $sql .= ' ' . $join;
      }
    }

    if ( !empty($this->_where) ) {
      $sql .= ' where ' . $this->_where;
      $sql = rtrim(rtrim($sql, 'and'), 'or');
    }
    if ( !empty($this->_groupby) ) {
      $sql .= ' group by ' . $this->_groupby;
    }
    if ( !empty($this->_orderby) ) {
      $sql .= ' order by ' . $this->_orderby;
    }
    if ( !empty($this->_limit) ) {
      $sql .= ' limit ' . $this->_limit;
    }
    if ( !empty($this->_offset) ) {
      $sql .= ' offset ' . $this->_offset;
    }

    return $sql;
  } // }}}

  /**
   * Метод влзвращает запрос с подставленными в него текстозаменителями
   *
   * @return null|string
   */
  private function _getSqlWithReplacements() { // {{{
    $sql = $this->_getCalculatedSql();

    $lang = $this->getLanguage();

    if ( null != $lang ) {
      // Зменяем синоним языка
      $sql = str_replace('#lang#', $lang, $sql);

      // Заменяем языковой фильтр
      $sql = preg_replace('%\#(\S+)#%s',
        "$1 like '%|" . $lang . "|%'", $sql);
    }

    $i = 1;
    foreach ($this->_replacements as $value) {
      $value = (string)$value;

      $sql = preg_replace('%\#(' . $i. ')(\D)%s', $value . "$2", $sql . ' ');
      $i++;
    }

    return $sql;
  } // }}}

  /**
   * Метод устанавливает текст sql-запроса
   *
   * @param string $sql - SQL-запрос
   *
   * @return Query
   */
  public function sql($sql) { // {{{
    $this->_sql = $sql;

    return $this;
  } // }}}

  /**
   * Метод добавляет до существующего sql-запроса указанный текст с новой
   * строки
   *
   * @param string $sql - Часть текста sql-запроса
   * @param mixed $if_exists = true  - Если значение идентично null, значит не добавлять SQL-выражение
   *
   * @return Query
   */
  public function addSql($sql, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $br = '';
    if (null != $this->_sql) {
      $br = chr(13);
    }

    $this->_sql .= $br.$sql;

    return $this;
  } // }}}

  /**
   * Метод возвращает текст sql-запроса(возвращаемый текст в любом случае
   * с подставленными тексто-заменителями)
   *
   * @param bool $noBr = false      - Отметка о том, нужно ли вырезать символы перевода строки
   * @param bool $withParams = true - Возвращать SQL с параметрами или нет
   *
   * @return string
   */
  public function getSql($noBr = false, $withParams = true) { // {{{
    if ($withParams) {
      $sql = $this->_getSqlWithParams();
    } else {
      $sql = $this->_getSqlWithReplacements();
    }

    if (true === $noBr) {
      return str_replace(chr(13), ' ', $sql);
    }

    return $sql;
  } // }}}

  public function printSql($noBr = false, $withParams = true) { // {{{
    echo $this->getSql($noBr, $withParams);

    return $this;
  } // }}}

  /**
   * Метод очищает текст sql-запроса
   *
   * @return Query
   */
  public function clearSql() { // {{{
    $this->_sql = null;

    return $this;
  } // }}}

  /**
   * Метод устанавливает параметры, которые будут переданы в sql-запрос
   *
   * @param array $params - Параметры, передаваемые в sql-запрос
   *
   * @return Query
   */
  public function params(array $params) { // {{{
    $this->_params = $params;

    return $this;
  } // }}}

  public function getParams() { // {{{
    return $this->_params;
  } // }}}

  /**
   * Метод устанавливает значение очередного параметра, который будет
   * передан в sql-запрос
   *
   * @param mixed $value            - Значение параметра sql-запроса
   * @param mixed $if_exists = true - Если значение идентично null, значит не добавлять параметр
   *
   * @return Query
   */
  public function addParam($value, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    if ('test' == Front::getInstance()->getMode() && !empty($this->_typeByTable) ) {
      return $this;
    }

    if ( null == trim($value) || 'null' == trim($value) ){
      $value = null;
    }
    $this->_params[] = $value;

    return $this;
  } // }}}


  public function addParams($value, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    if ('test' == Front::getInstance()->getMode() && !empty($this->_typeByTable) ) {
      return $this;
    }

    foreach ($value as $valueItem){
      $this->_params[] = $valueItem;
    }

    return $this;
  } // }}}

  /**
   * Метод удаляет все параметры sql-запроса
   *
   * @return Query
   */
  public function clearParams() { // {{{
    $this->_params = array();

    return $this;
  } // }}}

  /**
   * Метод устанавливает тесто-заменители, которые будут переданы в sql-запрос
   *
   * @param array $replacements - Тексто-заменители, передаваемые в sql-запрос
   *
   * @return Query
   */
  public function replacements(array $replacements) { // {{{
    $this->_replacements = $replacements;

    return $this;
  } // }}}

  /**
   * Метод добавляет значение тексто0заменителя в запрос. В запросе
   * тексто-заменители указываются в виде #1, #2, ... #n
   *
   * @param mixed $value           - Значение тексто-заменителя sql-запроса
   * @param bool $if_exists = true - Если значение идентично null, значит не добавлять тексто-заменитель
   *
   * @return Query
   */
  public function addReplacement($value, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_replacements[] = $value;

    return $this;
  } // }}}

  /**
   * Метод удаляет все тексто-заменители sql-запроса
   *
   * @return Query
   */
  public function clearReplacements() { // {{{
    $this->_replacements = array();

    return $this;
  } // }}}

  /**
   * Метод устанавливает язык, который будет использоватся для отбора записей
   * указанного языка в запросе. Языковое поле в запросе указывается с помощью
   * обрамления его символами ##, например: ##nw_name##
   *
   * @param string $language
   *
   * @return Query
   */
  public function setLanguage($language) { // {{{
    $this->_language = $language;

    return $this;
  } // }}}

  /**
   * Метод возвращает язык данных, который будут возвращаться запросом
   *
   * @return null|string
   */
  public function getLanguage() { // {{{
    return null !== $this->_language
      ? $this->_language
      : $this->_db->getLanguage();
  } // }}}

  /**
   * Метод возвращает указанное поле выполненного запроса
   *
   * @param int|string $field = 0  - Номер поля или имя поля, которое нужно вернуть
   * @param bool $use_cache = true - Использовать кеширование или нет
   * @param int $time_cache = 86400 - Время, на которое закешировать результат
   *
   * @return mixed
   *
   * @throws DbException
   */
  public function fetchField($field = 0, $use_cache = true, $time_cache = 86400) { // {{{
    // Формируем имя ключа на основе sql-запроса и сериализованных
    // параметров запроса
    $keyMemcached = null;
    if ($use_cache) {
      $keyMemcached = 'ffd' . $field . '_' .
        md5( $this->_getSqlWithReplacements() . serialize($this->_params) );
    }

    $result = $this->_fetch($keyMemcached);

    // Если метод _fetch вернул истину (значит что данных с мемкеша он не
    // брал, но запрос выполнил без ошибок)
    if (true === $result) {
      // Получаем данные выполненного запроса
      $result = pg_fetch_array($this->_resultQuery, 0, PGSQL_ASSOC);

      if ( empty($result) ) {
        $result = null;
      } else {
        if ( is_int($field) ) {
          $result = array_values($result);
        }

        if ( !isset($result[$field]) ) {
          throw new DbException('Db error: failure "' . pg_result_error($this->_resultQuery) . '" in query "' . $this->getSql(true) . '"', 513);
        }

        $result = $result[$field];

        if ('t' === $result) {
          $result = true;
        } else
          if ('f' === $result) {
            $result = false;
          }
      }

      // Устанавливаем полученные данные запроса в кэш
      $memcached = $this->_db->getMemcached();
      if ( $use_cache && !empty($memcached) ) {
        $memcached->set($keyMemcached, $result, $time_cache, $this->getTags());
      }
    }

    return $result;
  } // }}}

  /**
   * Метод возвращает одну указанную строку выполненного запроса в виде
   * ассоциативного массива, где ключами массива являются поля таблицы
   *
   * @param int $numRow = 0            - Номер строки, который нужно вернуть
   * @param bool $use_cache = true - Использовать кеширование или нет
   * @param int $time_cache = 86400 - Время, на которое закешировать результат
   *
   * @return array
   */
  public function fetchRow($numRow = 0, $use_cache = true, $time_cache = 86400) { // {{{
    // Формируем имя ключа на основе sql-запроса и сериализованных
    // параметров запроса
    $keyMemcached = null;
    if ($use_cache) {
      $keyMemcached = 'frw' . $numRow . '_'
        . md5( $this->_getSqlWithReplacements() . serialize($this->_params) );
    }

    $result = $this->_fetch($keyMemcached);

    // Если метод _fetch вернул истину (значит что данных с мемкеша он не
    // брал, но запрос выполнил без ошибок)
    if (true === $result) {
      // Получаем данные выполненного запроса
      $result = pg_fetch_array($this->_resultQuery, $numRow, PGSQL_ASSOC);

      // Устанавливаем полученные данные запроса в кэш
      $memcached = $this->_db->getMemcached();
      if ( $use_cache && !empty($memcached) ) {
        $memcached->set($keyMemcached, $result, $time_cache, $this->getTags());
      }
    } elseif ([] === $result) {
      // Устанавливаем полученные данные запроса в кэш
      $memcached = $this->_db->getMemcached();
      if ( $use_cache && !empty($memcached) ) {
        $memcached->set($keyMemcached, $result, $time_cache, $this->getTags());
      }
    }

    return $result;
  } // }}}

  /**
   * Метод возвращает результат запроса в виде многомерного ассоциативного
   * массива
   *
   * @param bool $use_cache = true - Использовать кеширование или нет
   * @param int $time_cache = 86400 - Время, на которое закешировать результат
   *
   * @return array
   */
  public function fetchResult($use_cache = true, $time_cache = 86400) { // {{{
    // Формируем имя ключа на основе sql-запроса и сериализованных
    // параметров запроса
    $keyMemcached = null;
    if ($use_cache) {
      $keyMemcached = 'frs_'
        . md5( $this->_getSqlWithReplacements() . serialize($this->_params) );
    }

    $result = $this->_fetch($keyMemcached);

    // Если метод _fetch вернул истину (значит что данных с мемкеша он не
    // брал, но запрос выполнил без ошибок)
    if (true === $result) {
      // Получаем данные выполненного запроса
      $result = pg_fetch_all($this->_resultQuery);

      // Устанавливаем полученные данные запроса в кэш
      $memcached = $this->_db->getMemcached();
      if ( $use_cache && !empty($memcached) ) {
        $memcached->set($keyMemcached, $result, $time_cache, $this->getTags());
      }
    }

    return $result;
  } // }}}

  /**
   * Метод выполняет sql-запрос
   *
   * @param string $return_field = null - Имя поля, значение которого нужно вернуть после выполнения запроса
   *
   * @return bool|mixed
   *
   * @throws DbException
   */
  public function execute($return_field = null) { // {{{
    Profiler::getInstance()->startCheckpoint('Query', $this->_getSqlWithReplacements());

    // Если соединение не установлено - установить его
    $this->_db->connect();

    $sql = $this->_getSqlWithParams();
    if ( !empty($return_field) ) {
      $sql .= ' returning ' . $return_field;
    }

    // Выполняем запрос и устанавливаем дескриптор запроса
    if ( !$this->_resultQuery = pg_query(
      $this->_db->dbAdapter(), $sql) ) {
      throw new DbException('Db error: failure "' . pg_result_error($this->_resultQuery) . '" in query "' . $this->getSql(true) . '"', 503);
    }

    if ( !empty($return_field) ) {
      $insert_row = pg_fetch_row($this->_resultQuery);

      $insert_id = $insert_row[0];

      Profiler::getInstance()->stopCheckpoint();

      return $insert_id;
    }

    Profiler::getInstance()->stopCheckpoint();

    return true;
  } // }}}

  /**
   * Метод возвращает количество строк которые вернул выполненный ранне
   * sql-запрос
   *
   * @param bool $use_cache = true - Использовать кеширование или нет
   * @param int $time_cache = 86400 - Время, на которое закешировать результат
   *
   * @return int
   */
  public function countRows($use_cache = true, $time_cache = 86400) { // {{{
    // Формируем имя ключа на основе sql-запроса и сериализованных
    // параметров запроса
    $keyMemcached = null;
    if ($use_cache) {
      $keyMemcached = 'cnt_'
        . md5( $this->_getSqlWithReplacements() . serialize($this->_params) );
    }

    // Если до этого запрос был выполнен и есть дескриптор этого запроса
    if (null !== $this->_resultQuery) {
      // Получаем количество строк запроса
      $result = pg_num_rows($this->_resultQuery);

      // Кешируем количество строк запроса
      $memcached = $this->_db->getMemcached();
      if ( $use_cache && !empty($memcached) ) {
        $memcached->set($keyMemcached, $result, $time_cache, $this->getTags());
      }

      // Возвращаем результат
      return $result;
    }

    // Если запрос до этого не был выполнен, получаем дескриптор запроса
    // или его результат с Memcached
    $result = $this->_fetch($keyMemcached);

    // Если метод _fetch вернул истину (значит что данных с мемкеша он не
    // брал, но запрос выполнил без ошибок)
    if (true === $result) {
      // получаем количество записей
      $dataMemcached = pg_num_rows($this->_resultQuery);
      // Кешируем количество записей данного запроса
      $memcached = $this->_db->getMemcached();
      if ( $use_cache && !empty($memcached) ) {
        $memcached->set($keyMemcached, $dataMemcached, $time_cache, $this->getTags());
      }

      return $dataMemcached;
    }

    return (int)$result;
  } // }}}

  /**
   * Методу устаналвиает массив тегов для ключа запроса в мемкеше
   *
   * @param array $tags - массив тегов
   *
   * @return Query
   */
  public function setTags(array $tags) { // {{{
    $this->_tags = $tags;

    return $this;
  } // }}}

  /**
   * Методу добавляет тег для ключа запроса в мемкеше
   *
   * @param string $name - имя тега
   *
   * @return Query
   */
  public function addTag($name) { // {{{
    $this->_tags[] = $name;

    return $this;
  } // }}}

  /**
   * Методу удаляет тег для ключа запроса в мемкеше
   *
   * @param string $name - имя тега
   *
   * @return Query
   */
  public function removeTag($name) { // {{{
    unset($this->_tags[$name]);

    return $this;
  } // }}}

  /**
   * Методу очищает все теги для ключа запроса в мемкеше
   *
   * @return Query
   */
  public function clearTags() { // {{{
    $this->_tags = array();

    return $this;
  } // }}}

  /**
   * Методу возвращает массив тегов для ключа запроса в мемкеше
   *
   * @return array
   */
  public function getTags() { // {{{
    return $this->_tags;
  } // }}}


  public function setTable($name, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_table = $name;

    return $this;
  } // }}}


  public function setTypeByTable($type) { // {{{
    $this->_typeByTable = $type;

    return $this;
  } // }}}

  public function setFields($fields) { // {{{
    $this->_fields = $fields;

    return $this;
  } // }}}

  public function addFields($fields) { // {{{
    $this->_fields .= $fields;

    return $this;
  } // }}}

  public function setWhere($where, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_where = $where;

    return $this;
  } // }}}

  public function addWhere($where, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_where .= ' ' . $where;

    return $this;
  } // }}}

  public function getWhere() { // {{{
    return $this->_where;
  } // }}}

  public function addJoin($join, $type = '', $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_joins[] = $type . ' join ' . $join;

    return $this;
  } // }}}

  public function addJoinFilter($permittedFilter, $typeUnused = null, $if_exists = true, $equals = null) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $params = $this->_getFilterSql($permittedFilter, $typeUnused, $equals);

    if ( empty($params) || empty($params['sql']) ) {
      return $this;
    }

    $this->_joins[] = $params['sql'];
    $this->addParams($params['params']);

    return $this;
  } // }}}

  public function addJoinWhere($join, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_joins[] = $join;

    return $this;
  } // }}}

  public function addParamEqual($name, $value, $andOr = 'and', $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $numParam = count($this->_params) + 1;
    $this->_where .= ' ' . $name . ' = $' . $numParam . ' ' . $andOr;

    return $this;
  } // }}}

  public function setGroupBy($groupby, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_groupby = $groupby;

    return $this;
  } // }}}

  public function addGroupBy($groupby, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_groupby .= ' ' . $groupby;

    return $this;
  } // }}}

  public function setOrderBy($orderby, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_orderby = $orderby;

    return $this;
  } // }}}

  public function addOrderBy($orderby, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_orderby .= ' ' . $orderby;

    return $this;
  } // }}}

  public function setLimit($limit, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_limit = (int)$limit;

    return $this;
  } // }}}

  public function setOffset($offset, $if_exists = true) { // {{{
    if (null === $if_exists) {
      return $this;
    }

    $this->_offset = (int)$offset;

    return $this;
  } // }}}

  /**
   * Метод формирует и возвращает SQL на основе URL фильтра
   *
   * @param Query $query
   *
   * @return bool
   */
  private function _getFilterSql($permittedFilter, $typeUnused = null, $equals = null) { // {{{
    $filter = Request::getInstance()->getParam('filter');
    if ( empty($filter) ) {
      return [];
    }

    $filter = trim($filter, ';');
    $filter_vars = explode(';', $filter);

    $result = [];
    $i = count($this->getParams()) + 1;
    $break = false;
    foreach ($filter_vars as $var) {
      $sql = '';

      $params = explode('=', $var);
      if ($params[0] === 'sort') {
        continue;
      }

      if ( !in_array($params[0], $permittedFilter) ) {
        $break = true;
        break;
      }

      if ($params[0] == $typeUnused) {
        continue;
      }

      $values = explode(',', $params[1]);

      $result_item = [];
      foreach ($values as $value) {
        $equal = '= $' . $i;
        if ( is_array($equals) && isset($equals[$params[0]]) ) {
          if (strpos($equals[$params[0]], '$$') === false) {
            $equal = $equals[$params[0]] . ' $' . $i;
          } else {
            $equal = str_replace('$$', '$' . $i, $equals[$params[0]]);
          }
        }
        if ( false === strpos($value, '~') ) {
          $sql .= $params[0] . ' ' . $equal . ' or ';
        } else {
          $sql .= $params[0] . ' between $' . $i . ' and $' .  ++$i . ' or ';
        }

        if ( false === strpos($value, '~') ) {
          $result_item['values'][] = $value;
        } else {
          $minmax = explode('~', $value);
          $result_item['values'][] = $minmax[0];
          $result_item['values'][] = $minmax[1];
        }

        $i++;
      }
      $sql = '(' . rtrim($sql, 'or ') . ') ';

      $result[] = [
        'sql' => $sql,
        'values' => $result_item['values'],
      ];
    }

    $result_sql = '';
    if ( !empty($this->getWhere()) ) {
      $result_sql .= ' and';
    }

    $params = [];
    if ( !empty($result) ) {
      foreach ($result as $item){
        $result_sql .= ' ' . $item['sql'] . ' and';
        // if ( false === strpos($item['values'], '~') ) {
        $params = array_merge($params, $item['values']);
      }
    }

    // var_dump($params);
    $result_sql = rtrim($result_sql, 'and');

    if ($break) {
      return [
        'sql' => 'and false',
        'params' => [],
      ];
    }

    return [
      'sql' => $result_sql,
      'params' => $params,
    ];
  } // }}}

  public function addSqlFilter($permittedFilter, $typeUnused = null, $equals = null) {
    $params = $this->_getFilterSql($permittedFilter, $typeUnused, $equals);

    if ( empty($params) || empty($params['sql']) ) {
      return $this;
    }

    $this->addSql($params['sql'])
      ->addParams($params['params']);

    return $this;
  }

  public function addWhereFilter($permittedFilter, $typeUnused = null, $equals = null) {
    $params = $this->_getFilterSql($permittedFilter, $typeUnused, $equals);

    if ( empty($params) || empty($params['sql']) ) {
      return $this;
    }

    $this->addWhere($params['sql'])
      ->addParams($params['params']);

    return $this;
  }
}
