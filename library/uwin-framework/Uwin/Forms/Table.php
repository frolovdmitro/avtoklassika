<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Forms\Table, который отвечает за работу с
 * таблицами
 *
 * @category   Uwin
 * @package    Uwin\Forms
 * @author     Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Forms, к которому относится класс Table
 */
namespace Uwin\Forms;

use \Uwin\Forms\Table\Field  as Field;
use \Uwin\Db                 as Db;
use \Uwin\TemplaterBlitz     as Templater;
use \Uwin\Forms\Exception    as Exception;
use \Uwin\Controller\Request as Request;

/**
 * Класс, который отвечает за работу с таблицами
 *
 * @category   Uwin
 * @package    Uwin\Forms
 * @author     Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Table
{
  /**
   * Ссылка на объект класса коннектора к БД
   * @var \Uwin\Db
   */
  private $_db = null;

  /**
   * Шаблонизатор, который будет использоваться для рендеринга таблицы
   * @var \Uwin\TemplaterBlitz
   */
  private $_templater = null;

  /**
   * Имя шаблонизатора, который будет использоваться для рендеринга таблицы
   * @var string
   */
  private $_templaterName = null;

  /**
   * Путь к файлу шаблона таблицы
   * @var string
   */
  private $_viewFile = null;

  /**
   * Имя таблицы в базе данных
   * @var string
   */
  private $_table = null;

  /**
   * Запрос
   * @var string
   */
  private $_query = null;

  /**
   * Псевдоним таблицы в запросе
   * @var string
   */
  private $_prefix = null;

  /**
   * Имя поля, которое явлется первичным ключом в таблице
   * @var string
   */
  private $_primaryKey = null;

  /**
   * SQL-запрос
   * @var string
   */
  private $_sql = null;

  /**
   * Дополнительное where-выражение
   * @var string
   */
  private $_where = null;

  /**
   * Поля через запятую по которым будем производится сортировка результатов
   * запроса (можно использовать desc|asc)
   * @var null
   */
  private $_orderBy = null;

  /**
   * Поля через запятую, по которым будет производиться группировка
   * @var string
   */
  private $_groupBy = null;

  /**
   * Массив подключенных таблиц к запросу (JOIN'ов)
   * @var array
   */
  private $_joins = array();

  /**
   * Массив полей таблицы
   * @var array
   */
  private $_fields = array();

  /**
   * Массив действий, которые могут быть применены к записи в таблице
   * @var array
   */
  private $_actions = array();

    /**
     * Запрос, который получает общие значения для футера
     * @var string
     */
    private $_footer_query = null;

    /**
     * Массив полей для футера
     * @var array
     */
    private $_footer_fields = array();

  /**
   * Нумеровать записи таблицы или нет
   * @var bool
   */
  private $_paginationRows = true;

    /**
     * Если указано значение, то возможно перемещать строки перетаскиванием
     * @var string
     */
    private $_draggableRow = null;

    /**
     * Язык, который используется
     * @var string
     */
    private $_language = null;

    private $_languageFilter = null;

  /**
   * Текущая страница таблицы
   * @var int
   */
  private $_currentPage = 1;

  /**
   * Отметка о том, есть возможность в таблице изменять кол-во записей на
   * странице или нет
   * @var bool
   */
  private $_changedCountOnPage = true;

  /**
   * Количество записей на странице
   * @var int
   */
  private $_countOnPage = 10;

  /**
   * Признак того, можно перемещать drag&drop записи таблицы или нет
   * @var bool
   */
  private $_movedRows = false;

  /**
   * Признак того, можно выбирать несколько записей таблицы checkbox'ами
   * или нет
   * @var bool
   */
  private $_multiselectedRows = false;

  /**
   * Признак того, отображать строку поиска по таблице или нет
   * @var bool
   */
  private $_findInput = false;

  /**
   * Массив полей по которым будет проводится поиск
   * @var array
   */
  private $_findColumns = null;

  private $_findValue = null;

  /**
   * Массив данных полученный с БД или false, если данные еще не извлекались
   * @var bool|array
   */
  private $_data = false;

//  private $_footerVisible = false;

  /**
   * Дополнительные переменные, которые будут переды в шаблон таблицы
   * @var array
   */
  private $_additionVars = array();


  /**
   * Метод выбрасывает исключение, если шаблонизатор не определен или
   * шаблон не найден
   *
   * @param string $templateFile - Путь к файлу шаблона
   *
   * @return bool
   * @throws \Uwin\Forms\Exception
   */
  private function _isTemplaterDefine($templateFile) {
    if (!($this->getTemplater() instanceof Templater)) {
      throw new Exception('Table templater is not defined');
    }

    if ( !file_exists($templateFile) ) {
      throw new Exception('Table template file "' . $templateFile
                . '" not found');
    }

    return true;
  }


  /**
   * Метод возвращает первое поле и тип сортировки таблицы
   *
   * @return array
   */
  private function _getFirstOrderField() {
    $orderType = 'desc';

    $orderField = explode(', ', $this->getOrderBy());

    $orderField = $orderField[0];
    $orderField = explode(' ', $orderField);
    if ( isset($orderField[1]) ) {
      $orderType = trim($orderField[1]);
    }
    $orderField = trim($orderField[0]);

    return array($orderField, $orderType);
  }


  /**
   * Установка объекта класса для работы с базой данных
   *
   * @param \Uwin\Db $db - Коннектор к базе данных
   *
   * @return \Uwin\Forms\Table
   */
  public function setDb($db) {
    $this->_db = $db;

    return $this;
  }

  /**
   * Метод возвращает объект класса-коннектора к базе данных
   *
   * @return null|\Uwin\Db
   */
  public function getDb() {
    return $this->_db;
  }

  /**
   * Метод устанавливает шаблонизатор, который будет использоваться для
   * рендеринга таблицы
   *
   * @param string               $name      - Имя используемого шаблонизатора
   * @param \Uwin\TemplaterBlitz $templater - Объект класса шаблонизатора
   *
   * @return \Uwin\Forms\Table
   */
  public function setTemplater($name, Templater $templater) {
    $this->_templaterName = $name;
    $this->_templater = $templater;

    return $this;
  }

  /**
   * Метод возвращает имя шаблонизатора, который используется для рендеринга
   * таблицы
   *
   * @return null|string
   */
  public function getTemplaterName() {
    return $this->_templaterName;
  }

  /**
   * Метод возвращает шаблонизатор, который используется для рендеринга
   * таблицы
   *
   * @return null|\Uwin\TemplaterBlitz
   */
  public function getTemplater() {
    return $this->_templater;
  }

  /**
   * Метод устанавливает путь к файлу шаблона таблицы
   *
   * @param string $filename - Путь к файлу
   *
   * @return \Uwin\Forms\Table
   */
  public function setViewFile($filename) {
    $this->_viewFile = $filename;

    return $this;
  }

  /**
   * Метод возвращает путь к фалу шаблона таблицы
   *
   * @return string
   */
  public function getViewFile() {
    if (null === $this->_viewFile) {
      $this->_viewFile = __DIR__ . DIRECTORY_SEPARATOR . 'views'
               . DIRECTORY_SEPARATOR . $this->_templaterName
               . DIRECTORY_SEPARATOR . 'table.tpl';
    }

    return $this->_viewFile;
  }

  /**
   * Метод устанавливает имя таблицы в базе данных
   *
   * @param string $name - Имя таблицы
   *
   * @return \Uwin\Forms\Table
   */
  public function setTableName($name) {
    $this->_table = $name;

    return $this;
  }

  /**
   * Метод возвращает имя таблицы или, если указано, имя таблицы как ID в
   * HTML, таесть с заменой пробелов на знак тире
   *
   * @param bool $asId = false - отметка о том, возвращать имя таблицы как ID в HTML
   *
   * @return null|string
   */
  public function getTableName($asId = false) {
    if (!$asId) {
      return $this->_table;
    }

    return str_replace(' ', '-', $this->_table);
  }

  /**
   * Метод устанавливает имя таблицы в базе данных
   *
   * @param string $query - Текст запроса
   *
   * @return \Uwin\Forms\Table
   */
  public function setQuery($query) {
    preg_match_all('/{{(.*?)}}/si', $query, $matches);
    $matches = $matches[1];

    foreach ($matches as $param){
      $query = str_replace('{{' . $param . '}}', Request::getInstance()->get($param), $query);
    }

    $this->_query = $query;

    return $this;
  }

  /**
   * Метод возвращает имя таблицы или, если указано, имя таблицы как ID в
   * HTML, таесть с заменой пробелов на знак тире
   *
   * @return null|string
   */
  public function getQuery() {
    return $this->_query;
  }

  /**
   *
   * @param string $prefix -
   *
   * @return \Uwin\Forms\Table
   */
  public function setPrefix($prefix) {
    $this->_prefix = $prefix;

    return $this;
  }

  /**
   *
   * @return null|string
   */
  public function getPrefix() {
    return $this->_prefix;
  }

  /**
   * Метод устанавливает имя поля, которое явлется первичным ключом в таблице
   *
   * @param string $name - Имя поля, которое является первичным ключом
   *
   * @return \Uwin\Forms\Table
   */
  public function setPrimaryKey($name) {
    $this->_primaryKey = $name;

    $this->addField($name)
       ->setVisible(false)
       ->setTable($this);

    return $this;
  }

  /**
   * Метод возвращает имя поля, которое явлется первичным ключом в таблице
   *
   * @return null|string
   */
  public function getPrimaryKey() {
    return $this->_primaryKey;
  }

  /**
   * Метод устанавливает SQL-запрос, который будет использоваться для
   * получения данных таблицы
   *
   * @param string $sql - SQL-запрос
   *
   * @return \Uwin\Forms\Table
   */
  public function setSql($sql) {
    $this->_sql = $sql;

    return $this;
  }

  /**
   * Метод добавляет до существующего sql-запроса указанный текст с новой
   * строки

   * @param string $sql - Часть текста SQL-запроса
   *
   * @return \Uwin\Forms\Table
   */
  public function addSql($sql) {
    $br = '';
    if (null != $this->_sql) {
      $br = chr(13);
    }

    $this->_sql .= $br.$sql;

    return $this;
  }

  /**
   * Метод очищает SQL-запрос
   *
   * @return \Uwin\Forms\Table
   */
  public function clearSql() {
    $this->_sql = null;

    return $this;
  }

  /**
   * Метод возвращает SQL-запрос
   *
   * @param bool $full - Указывает, использовать сортировку и розбивку по страницам или нет
   *
   * @return null|string
   */
  public function getSql($full = false) {
    // Если указан специальный SQL-запрос - возвращаем его
    if (null != $this->_sql) {
      return $this->_sql;
    }

    $findColumns = $this->getFindColumns();

    $sql = '';
    if ( null != $this->getFindValue() && !empty($findColumns) )  {
      $sql .= 'select * from (';
    }
    // Если не указан специальный SQL-запрос, то формируем его
    $sql .= 'select ';
    // Получаем список полей запроса
    foreach ($this->getFields() as $name => $values) {
      if($full == true && $values->getUseInFooter() === 'false') {
        // var_dump($full, $values);
        continue;
      }
      /**
       * @var \Uwin\Forms\Table\Field $values
       */
            if ( !$values->isLanguageField() ) {
          $name = $values->getField() . ' ' . $name;
          $name = str_replace('{{id}}', '1', $name);
            } else {
                if ( null == $values->getField() ) {
                    $name = $name . $this->getLanguageSufix() . ' as ' . $name;
                } else {
                    if ( false === mb_strpos($values->getField(), '#lang#') ) {
                        $name = $values->getField() . $this->getLanguageSufix() . ' as ' . $name;
                    } else {
                        $name = str_replace('#lang#', $this->getLanguageSufix(), $values->getField())  . ' as ' . $name;
                    }
                }
            }

      $sql .= $name . ', ';
    }
    $sql = rtrim($sql, ', ');

    $prefix = $this->getPrefix();
    if ( null == $this->getQuery() ) {
      $table = $this->getTableName();
    } else {
      $table = '(' . $this->getQuery() . ')';
      if (null == $prefix) {
        $prefix = 'sq';
      }
    }

    // Получаем таблицу с которой будет производится выборка
    $sql .= ' from ' . $table . ' ' . $prefix;

    $sql .= ' ' . $this->getJoins(null, true, $full);

    // Если есть выражение WHERE - получаем его
    if ( null != $this->getWhere() ) {
      $sql .= ' where ' . $this->getWhere();
            if ( null != $this->getLanguageFilter() ) {
                $sql .= ' and ' . $this->getLanguageFilter();
            }
    } else {
            if ( null != $this->getLanguageFilter() ) {
                $sql .= ' where ' . $this->getLanguageFilter();
            }
        }

    if ( null != $this->getGroupBy() ) {
      $sql .= ' group by ' . $this->getGroupBy();
    }

    if ( null != $this->getFindValue() && !empty($findColumns) )  {
      $sql .= ') fltr where ';

      foreach ($findColumns as $column => $null) {
                $field_type = $this->getFields($column)->getType();

                $find_values = explode('^^', $this->getFindValue());

                $use_column = true;
                $sql .= '(';
                foreach ($find_values as $find_value) {
//                    var_dump($find_value);
                    $find_value = trim($find_value);

                    if ('datetime' == $field_type || 'date' == $field_type) {
                        $column_cast = 'to_char(' . $column . ', \'DD.MM.YYYY HH24:MI\')';
                    } else {
                        $column_cast = $column . '::VARCHAR';
                    }

                    // Если ищем точное вхождение
                    if ( '!' == $find_value[0] && '!' == $find_value[1]) {
                        $find_value = mb_substr($find_value, 2, mb_strlen($find_value)-2);
                        $sql .= ' ' . $column_cast . "='" . $find_value . "' and";
                        $use_column = true;
                    } else
                    // Если ищем больше
                    if ( '&gt;' == mb_substr($find_value, 0, 4) || '&lt;' == mb_substr($find_value, 0, 4) ) {
                        if ( '&gt;' == mb_substr($find_value, 0, 4) ) {
                            $sign = '>';
                        } else {
                            $sign = '<';
                        }

                        if ('=' == $find_value[4]) {
                            $sign .= '=';
                            $find_value = mb_substr($find_value, 5, mb_strlen($find_value)-5);
                        } else {
                            $find_value = mb_substr($find_value, 4, mb_strlen($find_value)-4);
                        }


                        // Если по полю нельзя сравнить больше, пееходим к следующему полю
                        if ('datetime' != $field_type && 'date' != $field_type
                            && 'int' != $field_type && 'float' != $field_type) {
                            $use_column = false;
                            continue;
                        }

                        if ('int' == $field_type || 'float' == $field_type) {
                            if (false === is_numeric($find_value)) {
                                $use_column = false;
                                continue;
                            }

                            $column_cast = $column . '::NUMERIC';
                        }

                        if ('datetime' == $field_type || 'date' == $field_type) {
                            if ( true === is_numeric($find_value) || false === date_parse($find_value) ) {
                                $use_column = false;
                                continue;
                            }
                            $find_value = date('Y-m-d H:i', strtotime($find_value));
                            $column_cast = $column . '::DATETIME';
                        }


                        $sql .= " " . $column_cast . $sign . "'" . $find_value . "' and";
                        $use_column = true;
                    } else {
                        $sql .= " upper(" . $column_cast . ") like upper('%" . $find_value . "%') and";
                        $use_column = true;
                    }
                }

                if ($use_column) {
                    $sql = rtrim($sql, 'and') . ') or';
                } else {
                    $sql = rtrim($sql, '(');
                }
            }
            $sql = rtrim($sql, 'or');
    }

    if ($full) {
      return $sql;
    }

    // Если есть указаны столбцы по которым производить сортировку -
    // получаем их
    if ( null != $this->getOrderBy() ) {
      $sql .= ' order by ' . $this->getOrderBy();
    }

    // Получаем значение, сколько записей запроса выводить и
    // с каким смещением
    if (null !== $this->getCountOnPage()) {
      $sql .=' limit ' . $this->getCountOnPage();
      $sql .=' offset ' . ($this->getCurrentPage() - 1)
                * $this->getCountOnPage();
    }

    return $sql;
  }

  /**
   * Метод устанавливает дополнительное where-выражение
   *
   * @param string $where - дополнительное where-выражение
   *
   * @return \Uwin\Forms\Table
   */
  public function setWhere($where) {
    $this->_where = $where;

    return $this;
  }

  public function appendWhere($where) {
    $this->_where .= $where;

    return $this;
  }

  /**
   * Метод возвращает where-выражение
   *
   * @return null|string
   */
  public function getWhere() {

    return $this->_where;
  }

  /**
   * Метод устаналивает поля по которым будем производится сортировка
   * результатов запроса
   *
   * @param string $orderSql - Поля через запятую по которым будем производится сортировка результатов запроса (можно использовать desc|asc)
   *
   * @return \Uwin\Forms\Table
   */
  public function setOrderBy($orderSql) {
    $this->_orderBy = $orderSql;

    $orderField = $this->_getFirstOrderField();

    /**
     * @var \Uwin\Forms\Table\Field $field
     */
    foreach ($this->getFields() as $fieldName => $field) {
      if ($orderField[0] == $fieldName) {
        $field->setOrder(true)
            ->setOrderType($orderField[1]);
      } else {
        $field->setOrder(false);
      }
    }

    return $this;
  }

  /**
   * Метод возвращает поля по которым будем производится сортировка
   * результатов запроса
   *
   * @return null|string
   */
  public function getOrderBy() {
    return $this->_orderBy;
  }

  /**
   * Метод устанавливает поля, по которым будет производиться группировка
   *
   * @param string $groupSql - Поля через запятую, по которым будет производиться группировка
   *
   * @return \Uwin\Forms\Table
   */
  public function setGroupBy($groupSql) {
    $this->_groupBy = $groupSql;

    return $this;
  }

  /**
   * Метод возвращает поля, по которым будет производиться группировка
   *
   * @return null|string
   */
  public function getGroupBy() {
    return $this->_groupBy;
  }

  /**
   * Метод добавляет к запросу JOIN'ом таблицу
   *
   * @param string $table     - Имя таблицы и псевдоним
   * @param string $pk       - Имя поля первичного ключа
   * @param string $fk       - Имя поля внешнего ключа
   * @param string $type  = 'left' - Тип JOIN'а (left|right|inner|outer)
   * @param string $where = null   - Фильтр (конструкция WHERE для JOIN'а)
   * @param string $prefix = null
   *
   * @return \Uwin\Forms\Table
   */
  public function addJoin($table, $pk, $fk, $type = 'left', $where = null, $prefix = null, $use_in_footer = true) {
    if ( null !== $prefix) {
      $table .= ' ' . $prefix;
    }

    $this->_joins[$table] = array(
      'type'   => $type,
      'pk'     => $pk,
      'fk'     => $fk,
      'use_in_footer'     => $use_in_footer,
    );

    if (null !== $where) {
      $this->_joins[$table]['where'] = $where;
    }

    if (null !== $prefix) {
      $this->_joins[$table]['prefix'] = $prefix;
    }

    return $this;
  }

  /**
   * Метод удаляет указанный JOIN
   *
   * @param string $name - Имя таблицы подключонной JOIN'ом (с псевдонимом, если есть)
   *
   * @return \Uwin\Forms\Table
   */
  public function removeJoin($name) {
    unset($this->_joins[$name]);

    return $this;
  }

  /**
   * Метод удаляет все JOIN'ы
   *
   * @return \Uwin\Forms\Table
   */
  public function clearJoins() {
    $this->_joins = array();

    return $this;
  }

  /**
   * Метод возвращает указанный или все JOIN'ы в виде массива или в виде SQL
   *
   * @param string $name = null - Имя таблицы подключонной JOIN'ом (с псевдонимом, если есть)
   * @param bool $asSql = false - Возвращать JOIN как массив или как SQL
   *
   * @return array|null|string
   */
  public function getJoins($name = null, $asSql = false, $full = true) {
    if ( empty($this->_joins) ) {
      return null;
    }

    // Если возвращает JOIN'ы как массив, возвращаем их
    if (!$asSql) {
      if (null !== $name) {
        return $this->_joins[$name];
      }

      return $this->_joins;
    }

    // Если возвращаем JOIN'ы как SQL-запрос - формируем его
    $joinsSql = null;
    $joins = $this->_joins;
    if (null !== $name) {
      $joins = $joins[$name];
    }

    foreach ($joins as $name => $vars) {
      if($full == true && $vars['use_in_footer'] === 'false') {
        // var_dump($full, $vars);
        continue;
      }

      $where = null;
      if ( isset($vars['where']) ) {
        $where = 'and ' . $vars['where'];
      }

      $prefix = null;
      if ( isset($vars['prefix']) ) {
        $prefix = $vars['prefix'] . '.';
      }

      $fk_sql = '';
      if ( !empty($vars['fk']) ) {
        $fk_sql = '='  . $prefix . $vars['fk'];
      }
      $joinsSql .= $vars['type'] . ' join ' . $name . ' on '
            . $vars['pk'] . $fk_sql . ' '
            . $where . chr(13);
    }

    return $joinsSql;
  }

  /**
   * Метод добавляет поле таблицы
   *
   * @param string $name           - Имя поля
   * @param array $config   = null - Массив параметров поля
   * @param array $language = null - Массив языковых переменных поля
   *
   * @return \Uwin\Forms\Table\Field
   */
  public function addField($name, $config = null, $language = null) {
    $field = $this->_fields[$name] = new Field($name);

    if ( !empty($config) ) {
      $field->load($config, $language);
    }

    return $field;
  }

  /**
   * Метод удаляет поле таблицы
   *
   * @param string $name - Имя поля (возможно с псевдонимом таблицы)
   *
   * @return \Uwin\Forms\Table
   */
  public function removeField($name) {
    unset($this->_fields[$name]);

    return $this;
  }

  /**
   * Метод удаляет все поля таблицы
   *
   * @return \Uwin\Forms\Table
   */
  public function clearFields() {
    $this->_fields = array();

    return $this;
  }

  /**
   * Метод возвращает поле таблицы,или, если имя поля не указано - все поля
   *
   * @param string $name = null - Имя поля (возможно с псевдонимом таблицы)
   *
   * @return \Uwin\Forms\Table\Field|array
   */
  public function getFields($name = null) {
    if (null === $name) {
      return $this->_fields;
    }

    return $this->_fields[$name];
  }

  public function existsField($name) {
    if ( !isset($this->_fields[$name]) ) {
      return false;
    }

    return true;
  }

    /**
     * Метод добавляет поле футера
     *
     * @param string $name           - Имя поля
     * @param array $config   = null - Массив параметров поля
     * @param array $language = null - Массив языковых переменных поля
     *
     * @return \Uwin\Forms\Table\Field
     */
    public function addFooterField($name, $config = null, $language = null) {
      $field = $this->_footer_fields[$name] = new Field($name);
        $field->setFooterField(true);

      if ( !empty($config) ) {
        $field->load($config, $language)
                  ->setTable($this);
      }

      return $field;
    }

    /**
     * Метод удаляет поле футера
     *
     * @param string $name - Имя поля (возможно с псевдонимом таблицы)
     *
     * @return \Uwin\Forms\Table
     */
    public function removeFooterField($name) {
      unset($this->_footer_fields[$name]);

      return $this;
    }

    /**
     * Метод удаляет все поля футера
     *
     * @return \Uwin\Forms\Table
     */
    public function clearFooterFields() {
      $this->_footer_fields = array();

      return $this;
    }

    /**
     * Метод возвращает поле футера,или, если имя поля не указано - все поля
     *
     * @param string $name = null - Имя поля (возможно с псевдонимом таблицы)
     *
     * @return \Uwin\Forms\Table\Field|array
     */
    public function getFooterFields($name = null) {
      if (null === $name) {
        return $this->_footer_fields;
      }

      return $this->_footer_fields[$name];
    }

    public function existsFooterField($name) {
      if ( !isset($this->_footer_fields[$name]) ) {
        return false;
      }

      return true;
    }

  /**
   * Метод добавляет действие, которое можно производить над записями таблицы
   *
   * @param string $name            - Имя/тип действия
   * @param string $caption         - Наименование действия
   * @param string $function        - Адресс к функции, которая будет выполнена при нажатии на это действие
   * @param bool   $form     = false - Использовать ли форму, чтобы отобразить результаты полученные от  выполнения действия
   *
   * @return \Uwin\Forms\Table
   */
  public function addAction($name, $caption, $function = null, $form = false, $newline = false, $hide_button = false) {
    $this->_actions[$name] = array(
      'caption'  => $caption,
      'form'     => $form,
      'function' => $function,
      'newline'  => $newline,
            'hide_button' => $hide_button,
    );

    return $this;
  }

  /**
   * Метод удаляет указанное действие
   *
   * @param string $name - Имя/тип действия
   *
   * @return \Uwin\Forms\Table
   */
  public function removeAction($name) {
    unset($this->_actions[$name]);

    return $this;
  }

  /**
   * Метод удаляет все дейстия таблицы
   *
   * @return \Uwin\Forms\Table
   */
  public function clearActions() {
    $this->_actions = array();

    return $this;
  }

  /**
   * Метод возвращает указанное действие, или если имя действия не указано, то
   * массив всех действий таблицы
   *
   * @param string $name - Имя/тип действия
   *
   * @return array
   */
  public function getActions($name = null) {
    if (null === $name) {
      return $this->_actions;
    }

    return $this->_actions[$name];
  }

    /**
     * Метод устанавливает запрос для футера
     *
     * @param string $query - Текст запроса
     *
     * @return \Uwin\Forms\Table
     */
    public function setFooterQuery($query) {
      $this->_footer_query = $query;

      return $this;
    }

    /**
     * Метод возвращает запрос для футера
     *
     * @return null|string
     */
    public function getFooterQuery() {
      return $this->_footer_query;
    }

  /**
   * Метод устанавливает отметку о том, нумеровать записи таблицы или нет, или
   * если не передан параметр $pagination, то возвращает признак нумеруются
   * записи или нет
   *
   * @param bool $pagination = null - Отметка о том, нумеровать записи таблицы или нет
   *
   * @return bool|\Uwin\Forms\Table
   */
  public function paginationRows($pagination = null) {
    if (null == $pagination) {
      return $this->_paginationRows;
    }

    if ('false' == $pagination) {
      $pagination = false;
    } else {
      $pagination = true;
    }

    $this->_paginationRows = $pagination;

    return $this;
  }

    public function draggableRow($column = null) {
      if (null == $column) {
        return $this->_draggableRow;
      }

      $this->_draggableRow = $column;

      return $this;
    }

  /**
   * Метод устанавливает номер страницы, данные которой нужно получить
   *
   * @param int $numPage - Номер страницы
   *
   * @return \Uwin\Forms\Table
   */
  public function setCurrentPage($numPage) {
    $this->_currentPage = $numPage;

    return $this;
  }

  /**
   * Метод возвращает номер страницы, данные которой нужно получить
   *
   * @return int
   */
  public function getCurrentPage() {
    if ( 0 === (int)$this->_currentPage) {
      $this->_currentPage = 1;
    }

    return $this->_currentPage;
  }

  /**
   * Метод устанавливает количество записей, выводимых на одной странице
   * таблицы. Если количество равно NULL, значит выводить все записи таблицы
   *
   * @param int $count - Количество записей на странице
   *
   * @return \Uwin\Forms\Table
   */
  public function setCountOnPage($count) {
    $count = (int)$count;

    if (0 == $count) {

      return $this;
    }
    $this->_countOnPage = $count;

    return $this;
  }

  /**
   * Метод возвращает количество записей, выводимых на одной странице
   * таблицы
   *
   * @return int
   */
  public function getCountOnPage() {
    return $this->_countOnPage;
  }

  /**
   * Метод указывает или возвращает признак того, можно в таблице изменять
   * количество записей выводимых на одной странице или нет
   *
   * @param bool $changed = null
   *
   * @return bool|\Uwin\Forms\Table
   */
  public function changedCountOnPage($changed = null) {
    if (null == $changed) {
      return $this->_changedCountOnPage;
    }

    $this->_changedCountOnPage = $changed;

    return $this;
  }

  /**
   * Метод устанавливает или возвращает признак того, можно в таблице
   * перемещать записи drag&drop или нет
   *
   * @param bool $moved = null
   *
   * @return bool|\Uwin\Forms\Table
   */
  public function movedRows($moved = null) {
    if (null == $moved) {
      return $this->_movedRows;
    }

    $this->_movedRows = $moved;

    return $this;
  }

  /**
   * Метод устанавливает или возвращает признак того, можно в таблице выбирать
   * сразу несколько записей с помощью checkbox или нет
   *
   * @param bool $multiselected = null
   *
   * @return bool|\Uwin\Forms\Table
   */
  public function multiselectedRows($multiselected = null) {
    if (null == $multiselected) {
      return $this->_multiselectedRows;
    }

    $this->_multiselectedRows = $multiselected;

    return $this;
  }

  /**
   * Метод возвращает или устанавливает признак того, отображать строку
   * поиска в таблице или нет
   *
   * @param bool $visible = null
   *
   * @return bool|\Uwin\Forms\Table
   */
  public function findInputVisible($visible = null) {
    if (null == $visible) {
      return $this->_findInput;
    }

    $this->_findInput = (bool)$visible;

    return $this;
  }

  /**
   * Массив полей таблицы, по которым будет производиться поиск
   *
   * @param array $columns
   *
   * @return array
   */
  public function setFindColumns($columns) {
    $this->_findColumns = $columns;

    return $this;
  }

  /**
   * Массив полей таблицы, по которым будет производиться поиск
   *
   * @return array
   */
  public function getFindColumns() {
    return $this->_findColumns;
  }

  public function setFindValue($value) {
    $this->_findValue = $value;

    return $this;
  }

  public function getFindValue() {
    return $this->_findValue;
  }

    public function setLanguage($value) {
      $this->_language = $value;

      return $this;
    }

    public function getLanguage() {
      return $this->_language;
    }

    public function getLanguageSufix() {
        if ( empty($this->_language) ) {
            return null;
        }

      return '_' . rtrim($this->_language, '/');
    }

    public function setLanguageFilter($value) {
      $this->_languageFilter = $value;

      return $this;
    }

    public function getLanguageFilter($calculate = true) {
       if ($calculate) {
            return str_replace('#lang#', $this->getLanguage(), $this->_languageFilter);
       }
      return $this->_languageFilter;
    }

  /**
   * Метод возвращает количество видимых полей таблицы
   *
   * @return int
   */
  public function getCountColumns() {
    $count = 0;

    if ( $this->paginationRows() ) {
      $count++;
    }

        if ( null != $this->draggableRow() ) {
            $count++;
        }

    /**
     * @var \Uwin\Forms\Table\Field $field
     */
    foreach ($this->getFields() as $field) {
      if ( $field->getEnabled() && $field->getTableVisible() ) {
        $count++;
      }
    }

    return $count;
  }

  /**
   * Метод устанавливает дополнительные переменные, которые будут переды в
   * шаблон таблицы для рендеринга
   *
   * @param array $additionVars - Дополнительние переменные таблицы
   *
   * @return \Uwin\Forms\Table
   */
  public function setAdditionVars($additionVars)
  {
    $this->_additionVars = $additionVars;

    return $this;
  }

  /**
   * Метод возвращает дополнительные переменные, которые будут переды в
   * шаблон таблицы для рендеринга
   *
   * @return array
   */
  public function getAdditionVars()
  {
    return $this->_additionVars;
  }

  /**
   * Метод получает массив данных о структуре таблицы и массив языковых данных
   * и формирует на основе этих данных свойства данного класса
   *
   * @param string $tableName - Имя таблицы
   * @param array  $config    - Массив данных о таблице
   * @param array  $language  - Массив языковых данных о таблице
   *
   * @return \Uwin\Forms\Table
   */
  public function load($tableName, $config, $language) {
    // Устанавливаем имя таблицы и поля по которым будет сортироватся
    // результат запроса
    $this->setTableName($tableName);
    if ( isset($config['query']) ) {
          $config['query'] = str_replace('{{id}}', Request::getInstance()->get('id'), $config['query']);
      $this->setQuery($config['query']);
    }

    if ( isset($config['prefix']) ) {
      $this->setPrefix($config['prefix']);
    }

    $this->setPrimaryKey($config['pk']);

    if ( isset($config['where']) ) {
      $this->setWhere($config['where']);
    }

    if ( isset($config['search']) ) {
      $this->findInputVisible($config['search']);
    }

    if ( isset($config['quick_filter']) ) {
      $this->setFindColumns($config['quick_filter']);
    }

        if ( isset($config['languageFilter']) ) {
            $this->setLanguageFilter($config['languageFilter']);
        }

    if ( !empty($config['params']) ) {
      foreach ($config['params'] as $name => $value) {
                if ( isset($value['disabled']) && 'true' == $value['disabled']) {
                    continue;
                }

                $default = null;
                if ( isset($value['default']) ) {
                  $default = $value['default'];
                }
                if ('get' == $value['type']) {
                  $value = Request::getInstance()->get($value['value']);
                } else {
                  $value = $value['value'];
                }
                $sql = null;
                if ( null != $this->getWhere() ) {
                  $sql .= ' and ';
                }

                if ( !empty($value) ) {
                  $sql .= " " . $name . "='" . $value . "'";
                } else {
                  if ( null === $default ) {
                    $sql .= " " . $name . " is null";
                  } else {
                    $sql .= " " . $name . " = '" . $default . "'";
                  }
                }

                $this->appendWhere($sql);
      }
    }

    if ( isset($config['paginationRows']) ) {
      $this->paginationRows($config['paginationRows']);
    }

        if ( isset($config['draggable']) ) {
            $this->draggableRow($config['draggable']);
          }

    if ( isset($config['joins']) ) {
      foreach ($config['joins'] as $prefix => $join) {
        if (empty($join)) {
          continue;
        }

        $where = null;
        if ( isset($join['value']) ) {
          $where = $join['value'];
        }
        $use_in_footer = true;
        if ( isset($join['use_in_footer']) ) {
          $use_in_footer = $join['use_in_footer'];
        }

        $this->addJoin($join['name'], $join['pk'], $join['fk'],
          $join['type'], $where, $prefix, $use_in_footer);
      }
    }

    // Прохожимся по всем полям таблицы указанным в конфиге
    foreach ($config['fields'] as $name => $vars) {
      $langField = null;
      if ( array_key_exists($name, $language['fields']) ) {
        $langField = $language['fields'][$name];
      }

      if ( $this->existsField($name) ) {
        $this->removeField($name);
      }
      $this->addField($name, $vars, $langField)
           ->setTable($this);
    }

        // Если есть футер, добавляем запрос для футера
        if ( isset($config['footer']) ) {
            $this->setFooterQuery($config['footer']['query']);

            // Прохожимся по всем полям футера указанным в конфиге
            foreach ($config['footer']['fields'] as $name => $vars) {
                $langField = null;
                if ( array_key_exists($name, $language['footer']['fields']) ) {
                    $langField = $language['footer']['fields'][$name];
                }

                if ( $this->existsFooterField($name) ) {
                    $this->removeFooterField($name);
                }
                $this->addFooterField($name, $vars, $langField);
            }
        }

    if ( isset($config['group_by']) ) {
      $this->setGroupBy($config['group_by']);
    }

    if ( !isset($config['without_user_order']) || 'false' == $config['without_user_order']) {
      $this->setOrderBy($config['order_by']);
    }

    // Добавляем действия к записям таблицы
    if ( isset($config['actions']) && !empty($config['actions']) ) {
      foreach ($config['actions'] as $name => $values) {
        $newline = false;
        if ( isset($values['newline']) && 'true' == $values['newline']) {
          $newline = true;
        }

                $hidebutton = false;
                if ( isset($values['hide_button']) ) {
                    $hidebutton = $values['hide_button'];
                }

        $this->addAction($name, $language['actions'][$name]['caption'], null, false, $newline, $hidebutton);
      }
    }

    return $this;
  }

  /**
   * Метод возвращает массив данных для формирования структуры таблицы
   *
   * @return array
   */
  public function getStructureData() {
    $tableData = array();
    $tableData['name'] = $this->getTableName(true);
        $tableData['pk'] = $this->getPrimaryKey();

    if ( $this->paginationRows() ) {
      $tableData['paginationRow'] = true;
    }

        $tableData['draggableRow'] = $this->draggableRow();

    if ( !$this->findInputVisible() ) {
      $tableData['search_visible'] = true;
    }

    if ( $this->changedCountOnPage() ) {
      $tableData['changedCountOnPage'] = true;
      if ( null === $this->getCountOnPage() ) {
        $tableData['allRowsOnPage'] = true;
      } else {
        $tableData['rowsOnPage' . $this->getCountOnPage()] = true;
      }
    }

    /**
     * @var \Uwin\Forms\Table\Field $field
     */
    foreach ($this->getFields() as $field) {
      if ( $field->getEnabled() ) {
        $tableData['columns'][] = $field->getData();
      }
    }

    $actions = $this->getActions();
    if ( !empty($actions) ) {
      $tableData['actions'][] = true;
    }

    return $tableData;
  }

  /**
   * Метод возвращает HTML-структуру таблицы
   *
   * @return string
   */
  public function getStructureHtml() {
    $templateFile = $this->getViewFile();
    if ( !$this->_isTemplaterDefine($templateFile) ) {
      return false;
    }

    $template = file_get_contents($templateFile);

    /** @noinspection PhpUndefinedMethodInspection */
    $this->getTemplater()->load($template);
    $result = $this->getTemplater()->parse($this->getStructureData());

    return $result;
  }

  /**
   * Метод возвращает количество записей, которые вернул запрос
   *
   * @return int
   */
  public function getCountRows() {
    $count = $this->getDb()->query()
       ->sql( $this->getSql(true) )
       ->countRows(false);

    return $count;
  }

  /**
   * Метод возвращает количество страниц таблицы
   *
   * @return int
   */
  public function getCountPages() {
    $count = ceil($this->getCountRows() / $this->getCountOnPage());

    return $count;
  }

  /**
   * Метод возвращает массив записей таблицы полученных с базы данных
   *
   * @return array
   */
  public function getRowsData() {
    // Если данные с БД не получены, получаем их
    if (false !== $this->_data) {
      return $this->_data;
    }

    // Формируем SQL-запрос и получаем данные с БД
    $selectResult = $this->getDb()->query()
      ->sql( $this->getSql() )
      // ->printSql()
      ->fetchResult(false);

    // var_dump($this->getSql());
    if ( empty($selectResult) ) {
      $this->_data['row'] = array();
      return $this->_data;
    }

    // Проходимся по всем полученным записям
    $i = 1;
    foreach($selectResult as $row) {
      $tmpRow = array();
      if ( $this->paginationRows() ) {
        $tmpRow['paginationRow'] = true;
        $tmpRow['num'] = $this->getCountOnPage()
                 * ($this->getCurrentPage()-1) + $i;
      }

            $tmpRow['draggableRow'] = $this->draggableRow();

      /**
       * Устанавливае для каждого поля полученное значение записи
       * @var \Uwin\Forms\Table\Field $field
       */
      foreach($this->getFields() as $fieldName => $field) {
        // Устанвадиаем значение первичного ключа, как ID записи
        // в таблице
        if ( $fieldName == $this->getPrimaryKey() ) {
                    $tmpRow['id'] = $row[$fieldName];
        }

                $field->setValue($row[$fieldName]);
      }

      /**
       * Если поле видимо в таблице - добавлем его переменные в массив
       * записи
       * @var \Uwin\Forms\Table\Field $field
       */
      foreach($this->getFields() as $field) {
        if ( $field->getEnabled() && $field->getTableVisible() ) {
          $tmpRow['field'][] = $field->getFieldValues();
        }
      }

      // Если есть действия у записи таблицы, добавляем переменный о них
      $actions = array();
      $moduleParams = Request::getInstance()->getParams();
      $moduleUrl = '/administrator/' . $moduleParams['moduleRoute'] . '/';
      if ( !empty($moduleParams['type']) ) {
        $moduleUrl .= $moduleParams['type'] . '/';
      } else {
        $moduleUrl .= 'null/';
      }

      foreach ($this->getActions() as $name => $variables) {

                if ( $variables['hide_button'] != false ) {
                    if ( 't' == $this->getFields($variables['hide_button'])->getExpressionValue() ) {
                        continue;
                    }
                }

        $actions[] = array(
          'id'         => $tmpRow['id'],
          'name'       => $name,
          'caption'    => $variables['caption'],
          'module_url' => $moduleUrl,
          'newline'    => $variables['newline'],
        );
      }
      if ( !empty($actions)  ) {
        $tmpRow['action'] = $actions;
      }

      // Формиреум массив записей и их полей
      $this->_data['row'][] = $tmpRow;

      $i++;
    }

    // Формируем переменные для футера
    if ( null != $this->getFooterQuery() ) {
        // Формирую запрос для футера, чтобы получить значения
        $footerResult = $this->getDb()->query()
              ->sql('select ' . $this->getFooterQuery() . ' from (' . $this->getSql(true) . ') footer')
              ->fetchRow(0,10);

        // var_dump('select ' . $this->getFooterQuery() . ' from (' . $this->getSql(true) . ') footer');
        $this->_data['footer'] = true;
        if ( !empty($actions)  ) {
            $this->_data['footer_actions'] = true;
        }

        /**
          * Устанавливае для каждого поля футера полученное значение записи
          * @var \Uwin\Forms\Table\Field $field
          */
        foreach($this->getFooterFields() as $field_name => $field) {
            $this->getFooterFields($field_name)->setValue($footerResult[$field_name]);
        }

        $footer_fields = array();
        foreach($this->getFields() as $field_name => $field) {
            if ( $field->getEnabled() && $field->getTableVisible() ) {

                if ( !$this->existsFooterField($field_name) ) {
                    $footer_fields[] = array('hide' => true);
                } else {
                    if ( !$this->getFooterFields($field_name)->getTableVisible() ) {
                        $footer_fields[] = array('hide' => true);
                    } else {
                        $footer_fields[] = $this->getFooterFields($field_name)->getFieldValues();
                    }
                }
            }
        }
        $this->_data['footer_field'] = $footer_fields;
    }

    return $this->_data;
  }

  /**
   * Метод возвращает строки таблицы в HTML (тоесть то что находится в теге
   * tbody)
   *
   * @return string
   */
  public function getRowsHtml() {
    $templateFile = dirname( $this->getViewFile() ) . DIRECTORY_SEPARATOR
            . 'row.tpl';

    if ( !$this->_isTemplaterDefine($templateFile) ) {
      return false;
    }

    $template = file_get_contents($templateFile);

    /** @noinspection PhpUndefinedMethodInspection */
    $this->getTemplater()
       ->load($template);

    $result = $this->getTemplater()->parse($this->getTableData());

    return $result;
  }

  /**
   * Метод возвращает массив данных для формирования структры таблицы и
   * массив данных самой таблицы
   *
   * @return array
   */
  public function getTableData() {
    $data = $this->getStructureData();
    $countColumns = $this->getCountColumns();
    $actions = $this->getActions();
    if ( !empty($actions) ) {
      $countColumns++;
    }
    $data['count_columns'] = $countColumns;
    $data = array_merge($this->getRowsData(), $data,
              $this->getAdditionVars());

    return $data;
  }

  /**
   * Метод возвращает сформированную таблицу с данными
   *
   * @return string
   */
  public function getTableHtml() {
    $templateFile = $this->getViewFile();
    if ( !$this->_isTemplaterDefine($templateFile) ) {
      return false;
    }

    $template = file_get_contents($templateFile);

    /** @noinspection PhpUndefinedMethodInspection */
    $this->getTemplater()
       ->setIncludePath( dirname( $this->getViewFile() ) . DIRECTORY_SEPARATOR )
       ->load($template);

    $result = $this->getTemplater()->parse($this->getTableData());

    return $result;
  }
}
