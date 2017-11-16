<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Fs\File, который отвечает за работу с файлами
 * в файловой системе
 *
 * @category   Uwin
 * @package    Uwin\Forms
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Fs, к которому относится класс File
 */
namespace Uwin\Forms\Table;

use \Uwin\Registry           as Registry;
use \Uwin\Controller\Request as Request;

/**
 * Класс, который отвечает за работу с файлами в файловой системе
 *
 * @category   Uwin
 * @package    Uwin\Forms
 * @author     Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright  Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Field
{
  private $_name = null;

  private $_type = 'input';

  private $_retina = false;

  private $_caption = null;

  private $_field = null;

  private $_hint = null;

  private $_align = 'left';

  private $_width = null;

  private $_ordered = false;

  private $_order = null;

  private $orderType = 'desc';

  private $_expression = null;

  private $_ifNull = null;

  private $_format = null;

  private $_link = null;

  private $_value = null;

  private $_enabled = true;

  private $_visible = true;

  private $_style = null;

  private $_style_scope = 'all';

  private $_expIfNull = true;

  private $_useLanguageValue = false;

  private $_languageValues = array();

    private $_is_footer_field = false;

    private $_isLanguageField = false;

    private $_use_in_footer = true;

  /**
   * @var \Uwin\Forms\Table
   */
  private $_table = null;

  public function __construct($name) {
    $this->setName($name);
  }

  public function setName($name) {
    $this->_name = $name;

    return $this;
  }

  public function getName() {
    return $this->_name;
  }

  public function setType($type) {
    $this->_type = $type;

    return $this;
  }

  public function getType() {
    return $this->_type;
  }

  public function setRetina($retina) {
    $this->_retina = $retina;

    return $this;
  }

  public function getRetina() {
    return $this->_retina;
  }

  public function setCaption($caption) {
    $this->_caption = $caption;

    return $this;
  }

  public function getCaption() {
    if ( empty($this->_caption) ) {
      return $this->getName();
    }

    return $this->_caption;
  }

    public function isLanguageField($yes = null) {
        if (null !== $yes) {
            if ('true' == $yes) {
              $this->_isLanguageField = true;
            } else {
                $this->_isLanguageField = false;
            }
        }

      return $this->_isLanguageField;
    }

  public function setField($field) {
    $this->_field = $field;

    return $this;
  }

  public function getField() {
    $field = $this->_field;
    if ( $this->useLanguageValue() ) {
      $field = 'case';
      foreach ($this->getLanguageValues() as $value => $name) {
        $field .= " when " . $this->getName() . " = '" . $value . "' then '" . $name . "'";
      }
      $field .= " else " . $this->getName() . " end";
    }

    return $field;
  }

  public function setHint($hint) {
    $this->_hint = $hint;

    return $this;
  }

  public function getHint() {
    return $this->_hint;
  }

  public function setAlign($align) {
    $this->_align = $align;

    return $this;
  }

  public function getAlign() {
    return $this->_align;
  }

  public function setWidth($width) {
    $width = (int)$width;

    if (0 !== $width) {
      $this->_width = $width;
    }

    return $this;
  }

  public function getWidth() {
    return $this->_width;
  }

  public function setOrdered($ordered) {
    $this->_ordered = (bool)$ordered;

    return $this;
  }

  public function getOrdered() {
    return $this->_ordered;
  }

  public function setEnabled($enabled) {
    $this->_enabled = (bool)$enabled;

    return $this;
  }

  public function getEnabled() {
    return $this->_enabled;
  }

    public function setFooterField($enabled) {
      $this->_is_footer_field = (bool)$enabled;

      return $this;
    }

    public function isFooterField() {
      return $this->_is_footer_field;
    }

  public function setVisible($visible) {
    $this->_visible = $visible;

    return $this;
  }

  public function getTableVisible() {
    if ('table' == $this->_visible
      || 'all' == $this->_visible
      || true === $this->_visible) {

      return true;
    }

    return false;
  }

  public function getFormVisible() {
    if ('form' == $this->_visible
      || 'all' == $this->_visible
      || true === $this->_visible) {

      return true;
    }

    return false;
  }

  public function getInfoVisible() {
    if ('info' == $this->_visible
      || 'all' == $this->_visible
      || true === $this->_visible) {

      return true;
    }

    return false;
  }

  public function load($config, $language) {
    if ( isset($config['type']) ) {
      $this->setType($config['type']);
    }

    if ( isset($config['retina']) ) {
      if ($config['retina'] == 'true') {
        $this->setRetina(true);
      }
    }

    if ( isset($language['caption']) ) {
      $this->setCaption($language['caption']);
    }

    if ( isset($config['field']) ) {
      $this->setField($config['field']);
    }

    if ( isset($config['languageValue']) ) {
      $this->useLanguageValue($config['languageValue']);

      if ( isset($language['values']) ) {
        $this->setLanguageValues($language['values']);
      }
    }

        if ( isset($config['languageField']) ) {
            $this->isLanguageField($config['languageField']);
        }

    if ( isset($language['hint']) ) {
      $this->setHint($language['hint']);
    }

    if ( isset($config['align']) ) {
      $this->setAlign($config['align']);
    }

    if ( isset($config['format']) ) {
      $this->setFormat($config['format']);
    }

    if ( isset($config['link']) ) {
      $title = null;
      if ( isset($language['link']) ) {
        $title = $language['link'];
      }
      $this->setLink( array_merge($config['link'],
              array('title' => $title)) );
    }

    if ( isset($config['expifnull']) ) {
      $this->setExpIfNull($config['expifnull']);
    }

    if ( isset($config['use_in_footer']) ) {
      $this->setUseinFooter($config['use_in_footer']);
    }

    if ( isset($config['expression']) ) {
      $this->setExpression($config['expression']);
    }

    if ( isset($config['width']) ) {
      $this->setWidth($config['width']);
    }

    if ( isset($config['style']) ) {
      $this->setStyle($config['style']['value']);
      if ( isset($config['style']['scope']) ) {
        $this->setStyleScope($config['style']['scope']);
      }
    }

    if ( isset($config['ordered']) ) {
      $this->setOrdered($config['ordered']);
    }

    if ( isset($config['enabled']) ) {
      $this->setEnabled($config['enabled']);
    }

    if ( isset($config['visible']) ) {
      $this->setVisible($config['visible']);
    }

    return $this;
  }

  public function getData() {
    $data = array(
      'name'    => $this->getName(),
      'caption' => $this->getCaption(),
      'hint'    => $this->getHint(),
      'width'   => $this->getWidth(),
      'visible' => $this->getTableVisible(),
      'enabled' => $this->getEnabled(),
    );

    if ( $this->getOrder() ) {
      $data['order'] = true;
      $data['order_type'] = $this->getOrderType();
    }

    if ( $this->getOrdered() ) {
      $data['ordered'] = 'ordered';
    }

    return $data;
  }

  public function getOrder()
  {
    return $this->_order;
  }

  public function setOrder($order)
  {
    $this->_order = $order;

    return $this;
  }

  public function getOrderType()
  {
    return $this->orderType;
  }

  public function setOrderType($orderType)
  {
    $this->orderType = $orderType;

    return $this;
  }

  public function getFormatedValue() {
    $value = $this->getValue();

    if (null == $value) {
      return null;
    }

    if ( null != $this->getFormat() ) {
      $formatVars  = explode(';', $this->getFormat());
      $formatClass  = array_shift($formatVars);
      $formatMethod = array_shift($formatVars);

      $callFunction = $formatMethod;
      if ('null' != $formatClass) {
        $callFunction = array(new $formatClass, $formatMethod);
      }

      $valueKey = array_search('value', $formatVars);
      if (false !== $valueKey) {
        if ( 'datetime' == $this->getType() ||
           'date' == $this->getType())
        {
          $value = strtotime($value);
        }

        $formatVars[$valueKey] = $value;
      }

      $value = call_user_func_array($callFunction, $formatVars);
    }

    // Если указано, что значение поля - ссылка, формируем ее
    $linkValues = $this->getLink();

    if (null != $linkValues) {
      $value = '<a title="' . $linkValues['title']
           . '" href="' . $linkValues['url'] . '">' . $value . '</a>';
    }

    return $value;
  }

  public function getExpressionValue() {
    if ( !$this->getExpIfNull() && null == $this->getValue() ) {
      return null;
    }
    $value = $this->getFormatedValue();

    if ( null != $this->getExpression() ) {
      $nameFields = array();

      preg_match_all("#{{(.*?)}}#s", $this->getExpression(), $nameFields);
      $nameFields = $nameFields[1];

      $valueFields = array();
      foreach ($nameFields as $key => $name) {
        $nameFields[$key] = '{{' . $name . '}}';

        if ('value' == $name) {
          $valueFields[$key] = $value;

          continue;
        }

                if ( !$this->isFooterField() ) {
                    $field = $this->getTable()->getFields($name);
                } else {
                    $field = $this->getTable()->getFooterFields($name);
                }
        $valueFields[$key] = $field->getExpressionValue();

        if ( null != $field->getStyle() &&
           ('all' == $field->getStyleScope() ||
            'field' == $field->getStyleScope() )
        ) {
          $valueFields[$key] = '<abr title="' . $field->getCaption()
             . '" style="' . $field->getStyle() . '">'
             . $valueFields[$key] . '</abr>';
        }

      }

      $value = str_replace($nameFields, $valueFields, $this->getExpression());
    }

    return $value;
  }

  public function getFieldValues($formated = true) {
    if ($formated) {
      $value = $this->getExpressionValue();
    } else {
      $value = $this->getValue();
    }

    $type = 'text';
    $thm = $thmWidth = $thmHeight = null;
    if ( 'file' == $this->getType() ) {
      $type = 'file';
      $registry = Registry::getInstance();

      if ( empty($value) || 'false' == $value || false == $value) {
        return array();
      }

      $relative_image = $registry['path']['static_server'] . $value;
      $relative_image_thm = pathinfo($relative_image);
      $value = $registry['url']['staticServer'] . $value;
      $thm = pathinfo($value);

      if ( !empty($thm['basename']) ) {
        if ('jpg' == $thm['extension'] || 'jpeg' == $thm['extension'] ||
          'gif' == $thm['extension'] || 'png' == $thm['extension'] ||
          'bmp' == $thm['extension']) {
            $thm = $thm['dirname'] . '/.thm-' . $thm['basename'];
            $relative_image_thm = $relative_image_thm['dirname'] . '/' . $relative_image_thm['filename'] . '-thm.' . $relative_image_thm['extension'];
          }
      }
      if (file_exists($relative_image_thm)) {
        $thm = pathinfo($value);
        $thm = $thm['dirname'] . '/' . $thm['filename'] . '-thm.' . $thm['extension'];
      }

    } else
    if ( 'image' == $this->getType() ) {
      $type = 'image';
      $registry = Registry::getInstance();

      if ( empty($value) || 'false' == $value || false == $value) {
        return array();
      }

      $thm = null;
      $images = json_decode($value, true);
      $sizeDiv = 1;
      if ($this->getRetina()) {
      $sizeDiv = 2;
      }
      if ( isset($images['thm']) ) {
        $value = $registry['url']['staticServer'] . $images['original']['path'];
        $thm = $registry['url']['staticServer'] . $images['thm']['path'];
        $thmWidth = $images['thm']['width'] / $sizeDiv;
        $thmHeight = $images['thm']['height'] / $sizeDiv;
      }
    } else
    if ( 'bool' == $this->getType() ) {
      $type = 'bool';
      if ('t' != $value) {
        $value = null;
      }
    }


    $style = null;
    if ($formated) {
      if ( 'all' == $this->getStyleScope() ||
         'field' == $this->getStyleScope() )
      {
        $style = $this->getStyle();
      }
    }

    return array(
      'value'     => $value,
      'align'     => $this->getAlign(),
      'type'      => $this->getType(),
      $type       => true,
      'thm'       => $thm,
      'thmWidth'  => $thmWidth,
      'thmHeight' => $thmHeight,
      'style'     => $style,
    );
  }

  public function getFormat()
  {
    return $this->_format;
  }

  public function setFormat($format)
  {
    $this->_format = $format;

    return $this;
  }

  public function getLink()
  {
    if ( empty($this->_link) ) {
      return null;
    }

    $link = $this->_link;
    if ( !isset($link['module']) ) {
      $link['module'] = Request::getInstance()->getParam('moduleRoute');
    }

    if ( !isset($link['url']) ) {
      $link['url'] = '/administrator/' . $link['module'];
    }

    if ( isset($link['page']) ) {
      $link['url'] .= '/' . $link['page'] . '/';

      preg_match_all("#{{(.*?)}}#s", $link['url'], $nameFields);
      $nameFields = $nameFields[1];

      $valueFields = array();
      foreach ($nameFields as $key => $name) {
        $nameFields[$key] = '{{' . $name . '}}';

        $field = $this->getTable()->getFields($name);
        $valueFields[$key] = $field->getValue();
      }

      $link['url'] = str_replace($nameFields, $valueFields, $link['url']);

    }

    $delimeter = '?';
    if ( !isset($link['noId']) ) {
      $link['url'] .= $delimeter . 'id=' . $this->_table->getFields( $this->_table->getPrimaryKey() )->getValue();
      $delimeter = '&';
    }

    if ( isset($link['params']) ) {
      foreach ($link['params'] as $field => $values) {
        if ( is_array($values) ) {
          if ( array_key_exists('getFieldNotNull', $values) &&
             null === $this->_table->getFields($values['getFieldNotNull'])->getValue() )
          {
            continue;
          }

          $value = null;
          if ( array_key_exists('nullValue', $values) ) {
            $value = null;
          } else
          if ( array_key_exists('getFieldNotNull', $values) ) {
            $value = $this->_table->getFields($values['getFieldNotNull'])->getValue();
          }

          $link['url'] .= $delimeter . $values['value'] . '=' . $value;
          $delimeter = '&';
        }
      }
    }

    return $link;
  }

  public function setUseInFooter($use)
  {
    $this->_use_in_footer = $use;

    return $this;
  }

  public function getUseInFooter()
  {
    return $this->_use_in_footer;
  }

  public function setLink($link)
  {
    $this->_link = $link;

    return $this;
  }

  public function getExpression()
  {
    return $this->_expression;
  }

  public function setExpression($expression)
  {
    $this->_expression = $expression;

    return $this;
  }

  /**
   * @return \Uwin\Forms\Table
   */
  public function getTable()
  {
    return $this->_table;
  }

  public function setTable($table)
  {
    $this->_table = $table;

    return $this;
  }

  public function getValue()
  {
    $value = $this->_value;

    if ('bool' == $this->getType()) {
      if ('t' == $value) {
        $value = true;
      } else {
        $value = false;
      }
    }

    return $value;
  }

  public function setValue($value)
  {
    $this->_value = $value;

//    if ( $this->useLanguageValue() ) {
//      $languageValues = $this->getLanguageValues();
//      if ( isset($languageValues[$value]) ) {
//        $this->_value = $languageValues[$value];
//      }
//    }

    return $this;
  }

  public function getStyle()
  {
    $style = $this->_style;

    preg_match_all("#{{(.*?)}}#s", $this->_style, $nameFields);
    $nameFields = $nameFields[1];
    if ( !empty($nameFields) ) {
      $value = $this->getValue();

      $valueFields = array();
      foreach ($nameFields as $key => $name) {
        $nameFields[$key] = '{{' . $name . '}}';

        if ('value' == $name) {
          $valueFields[$key] = $value;

          continue;
        }

        $field = $this->getTable()->getFields($name);
        $valueFields[$key] = $field->getValue();
      }

      $style = str_replace($nameFields, $valueFields, $this->_style);
    }

    return $style;
  }

  public function setStyle($style)
  {
    $this->_style = $style;

    return $this;
  }

  public function getStyleScope()
  {
    return $this->_style_scope;
  }

  public function setStyleScope($style_scope)
  {
    $this->_style_scope = $style_scope;

    return $this;
  }

  public function getExpIfNull()
  {
    return $this->_expIfNull;
  }

  public function setExpIfNull($expIfNull)
  {
    if ('false' == $expIfNull) {
      $expIfNull = false;
    }

    $this->_expIfNull = (bool)$expIfNull;

    return $this;
  }

  public function useLanguageValue($use = null)
  {
    if (null !== $use) {
      $this->_useLanguageValue = $use;
    }

    return $this->_useLanguageValue;
  }

  public function getLanguageValues()
  {
    return $this->_languageValues;
  }

  public function setLanguageValues($languageValues)
  {
    $this->_languageValues = $languageValues;

    return $this;
  }
}
