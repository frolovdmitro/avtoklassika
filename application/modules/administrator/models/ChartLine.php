<?php
/**
 * Uwin CMS
 *
 * Файл содержащий модель модуля панели управления
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Model\Abstract_ as Abstract_;

/**
 * Модель модуля панели управления
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class ChartLine
{
    private $_db = null;

    private $_name = null;

    private $_caption = null;

    private $_datasource = null;

    private $_functionsource = null;

    private $_query = null;

    private $_buildQuery = null;

    private $_xkey = null;

    private $_useWhere = false;

    private $_style = null;

    private $_where = null;

    private $_xkeyCaption = null;

    private $_xkeyFormat = null;

    private $_xkeyInterval = 'day'; //day|week|month

    private $_ykeys = array();

    private $_beginDate = null;

    private $_endDate = null;


    public function __construct($name, $caption = null, $style = null) {
        $this->setName($name)
             ->setCaption($caption)
             ->setStyle($style);

        return $this;
    }

    public function setDb(\Uwin\Db $db) {
        $this->_db = $db;

        return $this;
    }

    /**
     * @return \Uwin\Db
     */
    private function _getDb() {
        return $this->_db;
    }

    public function setName($name) {
        $this->_name = $name;

        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function setCaption($caption) {
        $this->_caption = $caption;

        return $this;
    }

    public function getCaption() {
        return $this->_caption;
    }

    public function setStyle($style) {
        $this->_style = $style;

        return $this;
    }

    public function getStyle() {
        return $this->_style;
    }

    public function setXKey($xkey) {
        $this->_xkey = $xkey;

        return $this;
    }

    public function getXKey() {
        return $this->_xkey;
    }

    public function setXKeyInterval($xkey_interval) {
        if ('day' != $xkey_interval && 'week' != $xkey_interval && 'month' != $xkey_interval) {
            //TODO Вызывать исключение
            return false;
        }

        $this->_xkeyInterval = $xkey_interval;

        return $this;
    }

    public function getXKeyInterval() {
        return $this->_xkeyInterval;
    }

    public function setXKeyFormat($xkey_format) {
        $this->_xkeyFormat = $xkey_format;

        return $this;
    }

    public function getXKeyFormat() {
        return $this->_xkeyFormat;
    }

    public function setBeginDate($begin_date) {
        $this->_beginDate = $begin_date;

        return $this;
    }

    public function getBeginDate() {
        return $this->_beginDate;
    }

    public function setEndDate($end_date) {
        $this->_endDate = $end_date;

        return $this;
    }

    public function getEndDate() {
        return $this->_endDate;
    }

    public function setQuery($query) {
        $this->_query = $query;

        return $this;
    }

    public function getQuery($rebuild_sql = false) {
        // Если указан запрос, а не набор данных
        if ( null != $this->_query ) {
            $this->_buildQuery = $this->_query;

            return $this->_buildQuery;
        }

        $query = null;

        //TODO Сделать формировани sql на основе данных

        $this->_buildQuery = $query;

        return $this->_buildQuery;
    }

    public function clearYKeys() {
        $this->_ykeys = array();

        return $this;
    }

    public function addYKeys($name, $field, $caption, $color) {
        $this->_ykeys[$name] = new ChartLineYKeys($name, $field, $caption, $color);

        return $this;
    }

    public function removeYKeys($name) {
        unset($this->_ykeys[$name]);

        return $this;
    }

    public function getYKeys($name = null) {
        if ( null != $name ) {
            if ( !isset($this->_ykeys[$name]) ) {
                //TODO сделать исключение
                return false;
            }

            return $this->_ykeys[$name];
        }

        return $this->_ykeys;
    }

    public function getData() {
        $data = array();
        $data['y_keys'] = array();
        $data['points'] = array();

        $ykeys = array_keys( $this->getYKeys() );
        if ( empty($ykeys) ) {
            return $data;
        }

        $sql_fields = null;
        foreach ($ykeys as $key) {
            $sql_fields .= $this->getYKeys($key)->getField() . ' as ' . $key . ',';
        }
        $sql_fields = rtrim($sql_fields, ',');

        $queryResult = $this->_getDb()->query()
             ->addSql('select to_char(' . $this->getXKey() . '::DATE, \'DD.MM.YYYY\') as ' . $this->getXKey())
             ->addSql(',' . $sql_fields . ' from (' . $this->getQuery() )
             ->addSql(') chr where ' . $this->getXKey() . ' between $1 and $2')
             ->addSql('group by ' . $this->getXKey() . '::DATE')
             ->addSql('order by ' . $this->getXKey() . '::DATE asc')
             ->addParam( date('Y-m-d', strtotime($this->getBeginDate())) )
             ->addParam( date('Y-m-d', strtotime($this->getEndDate())) )
             ->fetchResult(false);

        /**
         * @var ChartLineYKeys $ykey
         */
        foreach( $this->getYKeys() as $ykey) {
            $data['y_keys'][] = array(
                'name'    => $ykey->getName(),
                'caption' => $ykey->getCaption(),
                'color'   => $ykey->getColor(),
            );

            if ( empty($queryResult) ) {
                continue;
            }

            $current_date = $this->getBeginDate();
            $i = 0;
//            var_dump($queryResult);
            while ($current_date != $this->getEndDate() ) {
                if ( !isset($queryResult[$i]) ) {
                    break;
                }

                if ($current_date == $queryResult[$i][$this->getXKey()]) {
//                    var_dump($current_date);
                    $xykeys_values = array();
                    $xykeys_values['x_key'] = date('Y-m-d', strtotime($current_date));
                    foreach ($ykeys as $key) {
                        $xykeys_values['y_keys']['name'] = $key;
                        $xykeys_values['y_keys']['value'] = (int)$queryResult[$i][$key];
                    }
                    $data['points'][] = $xykeys_values;

                    $i++;
                } else {
                    $xykeys_values = array();
                    $xykeys_values['x_key'] = date('Y-m-d', strtotime($current_date));
                    foreach ($ykeys as $key) {
                        $xykeys_values['y_keys']['name'] = $key;
                        $xykeys_values['y_keys']['value'] = 0;
                    }
//                    $data['points'][] = $xykeys_values;
                }

                $current_date = date("d.m.Y", strtotime(date("d.m.Y", strtotime($current_date)) . " +1 day"));
            }

        }

        return $data;
    }

    public function getMorrisData() {
        $morris_data = array();
        $morris_data['name'] = $this->getName();
        $morris_data['caption'] = $this->getCaption();
        $morris_data['style'] = $this->getStyle();

        $data = $this->getData();
        $morris_data['points'] = $data['points'];
        $morris_data['y_keys'] = $data['y_keys'];


        return $morris_data;
    }
}


class ChartLineYKeys
{
    private $_name = null;

    private $_caption = null;

    private $_color = null;

    private $_where = null;

    private $_expression = null;

    private $_field = null;

    public function __construct($name, $field, $caption, $color) {
        $this->setName($name)
             ->setField($field)
             ->setCaption($caption)
             ->setColor($color);

        return $this;
    }

    public function setName($name) {
        $this->_name = $name;

        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function setCaption($caption) {
        $this->_caption = $caption;

        return $this;
    }

    public function getCaption() {
        return $this->_caption;
    }

    public function setColor($color) {
        $this->_color = $color;

        return $this;
    }

    public function getColor() {
        return $this->_color;
    }

    public function setWhere($where) {
        $this->_where = $where;

        return $this;
    }

    public function getWhere() {
        return $this->_where;
    }

    public function setField($field) {
        $this->_field = $field;

        return $this;
    }

    public function getField() {
        return $this->_field;
    }

    public function setExpression($expression) {
        $this->_expression = $expression;

        return $this;
    }

    public function getExpression() {
        return $this->_expression;
    }

    public function getValue() {

    }
}