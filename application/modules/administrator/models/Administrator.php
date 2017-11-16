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
use \Uwin\Model\Abstract_  as Abstract_;
use \Uwin\Auth             as Auth;
use \Uwin\Auth\Exception   as AuthException;
use \Uwin\Registry         as Registry;
use \Uwin\Controller\Request as Request;
use \Uwin\Fs               as Fs;
use \Uwin\Xml              as Xml;
use \Uwin\Config           as Config;
use \Uwin\Forms\Table      as Table;
use \Uwin\TemplaterBlitz   as Templater;
use \Uwin\Validator      as Validator;
use \Uwin\Linguistics    as Linguistics;
use \Uwin\Cacher\Memcached as Memcached;

/**
 * Модель модуля панели управления
 *
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Administrator extends Abstract_
{

  private function _typograf($text)
  {
    //    $xml = new Xml;
    //    $xml->createXml('preferences');

    $request = $this->getRequest();
    $data_typograf = $request->sendPost('www.typograf.ru', 80, '/webservice/','text=' . urlencode($text) . '&chr=' . urlencode("UTF-8"));

    return $data_typograf;
  }

  private function _array_extend($a, $b) {
    foreach($b as $k=>$v) {
      if( is_array($v) ) {
        if( !isset($a[$k]) ) {
          $a[$k] = $v;
        } else {
          $a[$k] = $this->_array_extend($a[$k], $v);
        }
      } else {
        $a[$k] = $v;
      }
    }

    return $a;
  }

  /**
   * Метод устанавливает текст сообщения указанной ошибки
   *
   * @param int $numError - Номер ошибки аутентификации
   *
   * @return array
   */
  private function _setErrorMessage($numError)
  {
    $prefix_errors = 'lng_login_errors_';

    $errors = array(
      Auth::FAILURE_IDENTITY_EMPTY => array(
        'id'   => 'name',
        'text' => $this->getVariable($prefix_errors . 'name_empty'),
      ),
      Auth::FAILURE_PASSWORD_EMPTY => array(
        'id'   => 'password',
        'text' => $this->getVariable($prefix_errors . 'password_empty'),
      ),
      Auth::FAILURE_IDENTITY_NOT_FOUND => array(
        'id'   => 'password',
        'text' => $this->getVariable($prefix_errors . 'failed'),
      ),
    );

    $result['error'] = $errors[$numError];

    return $result;
  }

  /**
   * Метод, который используется для сортировки пунктов меню в функции usort
   *
   * @param int $item1 - Элемент сортировки
   * @param int $item2 - Элемент сортировки
   *
   * @return int
   */
  private function _sortMenu($item1, $item2)
  {
    if ($item1['order'] == $item2['order']) {
      return 0;
    }

    if ($item1['order'] < $item2['order']) {
      return -1;
    }

    return 1;
  }

  /**
   * Метод формирует полный URL модуля на основе его имени и адреса(он
   * может отсутсвовать или быть относительным)
   *
   * @param string $address    - Адрес модуля
   * @param string $moduleName - Имя модуля
   *
   * @return string
   */
  public function _getAddressModule($address, $moduleName) {
    if (null == $address) {
      $address = '/administrator/' . $moduleName . '/';
    } else
      if ( !( ('/' == $address[0]) ||
        ('http://' == substr($address, 0, 7) )) )
      {

        $address = '/administrator/' . $address;
      }

    // Добавление окончательного слеша к адресу если это нужно
    if ('/' != $address[strlen($address)-1]) {
      $address .= '/';
    }

    return $address;
  }

  /**
   * Метод возвращает массив переменных подпунктов указанного пункта меню
   *
   * @param array  $module
   * @param array  $submenu     - Массив переменных подменю
   * @param array  $submenuLang - Массив языковых парметров подменю
   * @param string $address     - Адрес пункта меню, у которого формируются подпункты
   *
   * @return array
   */
  public function _getSubmenu($module, $submenu, $submenuLang, $address) {
    $result = array();

    if ( empty($submenu) ) {
      return array();
    }

    $rules = $this->_getSubRules($module);

    foreach ($submenu as $key => $value) {
      if ( array_key_exists($key, $rules) &&
        't' == $rules[$key]['ara_hide_module'] )
      {
        continue;
      }

      if (isset($value['type']) && 'data' == $value['type']) {
        $address_field = null;
        $address_field_sql = null;
        if ( isset($value['address_field']) ) {
          $address_field = $value['address_field'];
          $address_field_sql = ',' . $address_field;
        }

        $addition_fields = null;
        $addition_fields_sql = null;
        if ( isset($value['addition_fields']) ) {
          $addition_fields = $value['addition_fields'];
          $addition_fields_sql = ',' . $addition_fields;
        }

        $select = $this->db()->query()
          ->clearSql()
          ->addSql('select ' . $value['pk'] . ',')
          ->addSql($value['title_field'] . $address_field_sql . $addition_fields_sql)
          ->addSql('from ' . $value['table']);
        if ( isset($value['where']) ) {
          $select->addSql('where ' . $value['where']);
        }

        $selectResult = $select->fetchResult(false);

        $result = array();
        if ( !empty($selectResult) ) {
          foreach ($selectResult as $item) {
            if ( !empty($item[$address_field]) && array_key_exists($item[$address_field], $rules) &&
              't' == $rules[$item[$address_field]]['ara_hide_module'] )
            {
              continue;
            }

            $item_address = $address;
            if (!empty($item[$address_field])) {
              $item_address .= $item[$address_field] . '/';
            }

            if ( !empty($value['link'])) {
              $item_address .= $value['link']['page'] . '/';
              $delimeter = '?';
              foreach ($value['link']['params'] as $name => $param) {
                $item_value = null;
                if ( isset($item[$name])) {
                  $item_value = $item[$name];
                }

                $item_name = $param;
                if ( is_array($param) && array_key_exists('value', $param) ) {
                  $item_name = $param['value'];
                }

                $item_address .= $delimeter . $item_name . '=' . $item_value;
                $delimeter = '&';
              }
            }
            // Проверка, является ли данный пункт меню текущим
            $active = null;
            if ( $this->getRequest()->equalUrl($item_address, false) ) {
              $active = 'active';
            }

            $result[] = array(
              'address' => $item_address,
              'caption' => $item[$value['title_field']],
              'active'  => $active
            );
          }
        }

        continue;
      }

      $url_suffix = null;
      if ( isset($value['url_suffix']) ) {
        $url_suffix = $value['url_suffix'];
      }

      $item_address = $address . $key . '/' . $url_suffix;

      // Проверка, является ли данный пункт меню текущим
      $active = null;
      if ( $this->getRequest()->equalUrl($item_address, false) ) {
        $active = 'active';
      }

      $result[] = array(
        'address' => $item_address,
        'caption' => $submenuLang[$key],
        'active'  => $active
      );
    }

    return $result;
  }

  private function _getRules() {
    $identity = Auth::getInstance()->getStorage()->identity;
    $tmp_rules = $this->db()->query()
      ->addSql('select ara.* from access_rules_administrators_tbl ara')
      ->addSql('left join administrators_tbl on adm_id_pk=ara_adm_id_fk')
      ->addSql('where adm_username=$1 and ara_enabled=true and ara_parent_id_fk is null')
      ->addParam($identity)
      ->fetchResult(false);

    $rules = array();
    if ( !empty($tmp_rules) ) {
      foreach ($tmp_rules as $rule) {
        $rules[$rule['ara_module_name']] = $rule;
      }
    }

    return $rules;
  }

  private function _getSubRules($submodule) {
    $identity = Auth::getInstance()->getStorage()->identity;
    $tmp_rules = $this->db()->query()
      ->addSql('select ara.* from access_rules_administrators_tbl ara')
      ->addSql('left join administrators_tbl on adm_id_pk=ara_adm_id_fk')
      ->addSql('left join access_rules_administrators_tbl p_ara on ara.ara_parent_id_fk=p_ara.ara_id_pk')
      ->addSql('where adm_username=$1 and ara.ara_enabled=true and p_ara.ara_module_name=$2')
      ->addParam($identity)
      ->addParam($submodule)
      ->fetchResult(false);

    $rules = array();
    if ( !empty($tmp_rules) ) {
      foreach ($tmp_rules as $rule) {
        $rules[$rule['ara_module_name']] = $rule;
      }
    }

    return $rules;
  }
  /**
   * Метод получает переменные указанного типа меню панели управления для
   * дальнейшего формирование меню
   *
   * @param  string $type - Меню которое нужно вернуть (MODULE|MAIN)
   *
   * @return array
   */
  private function _getMenu($type) {
    // Сканируем все модули приложения и ищем их описание админки в admin.xml
    $registry = Registry::getInstance();

    $fs = new Fs($registry['path']['modules']);
    $modulesConfigs = $fs->getFilesRecursive('admin.xml');
    unset($fs);

    $result = array();

    $rules = $this->_getRules();

    // Проходимся по всем найденным описаниям модулей
    foreach ($modulesConfigs as $config) {
      // Получаем имя модуля
      $moduleName = basename( dirname( dirname($config) ) );

      if ( array_key_exists($moduleName, $rules) &&
        't' == $rules[$moduleName]['ara_hide_module'] )
      {
        continue;
      }

      // Получаем переменных с xml-файла модуля
      $configer = new Config(Config::XML);
      $moduleValues = $configer
        ->open($config, $moduleName)
        ->getAttr();

      // Если конфиг админики указанного типа
      if ( $moduleValues['type'] === $type ) {
        // Узначем на каком языке должна быть панель управления
        $language = 'ru';
        if ( isset($registry['languageAdmin']) ) {
          $language = $registry['languageAdmin'];
        }

        // Получаем путь к языковому файлу модуля
        $langNameFile = dirname( dirname( dirname($config) ) ) . DIR_SEP
          . $moduleName . DIR_SEP . 'languages' . DIR_SEP
          . 'admin' . DIR_SEP . $language . '.xml';

        // Если языковый файл есть, получаем его переменные и склеиваем
        // переменные модуля с ними
        if ( file_exists($langNameFile) ) {
          $langer = new Config(Config::XML);
          $langValues = $langer
            ->open($langNameFile, $moduleName)
            ->get();

          $moduleValues = array_merge($moduleValues, $langValues);
        }

        if ( !isset($moduleValues['address']) ) {
          $moduleValues['address'] = null;
        }

        $moduleValues['class'] = $moduleName;
        $moduleValues['address'] =
          $this->_getAddressModule($moduleValues['address'],
            $moduleName);

        if ( $configer->exists('fastActions') ) {
          if ( isset($langer) ) {
            $moduleValues['modulesSubmenu'] = $this->_getSubmenu(
              $moduleName,
              $configer->get('fastActions', true),
              $langer->get('fastActions'),
              $moduleValues['address']);
          }

        }

        // Проверка, является ли данный пункт меню текущим
        if ('MODULE' == $type) {
          if ( $this->getRequest()->equalUrlWithTail($moduleValues['address']) ) {
            $moduleValues['active'] = 'active';
          }
        } else {
          if ( $this->getRequest()->equalUrl($moduleValues['address'], false) ) {
            $moduleValues['active'] = 'active';
          }
        }

        $result[] = $moduleValues;
      }
    }

    // Сортировка полученного массива пунктов меню модулей
    usort($result, array($this, "_sortMenu") );

    return $result;
  }

  /**
   * Метод возвращет имя модуля, который выбран в панели управления
   *
   * @return string
   */
  private function _getModuleRoute() {
    $moduleRoute = $this->getRequest()->getParam('moduleRoute');

    if ( empty($moduleRoute) ) {
      return 'default';
    }

    return $moduleRoute;
  }

  private function _getModulePage() {
    $modulePage = $this->getRequest()->getParam('moduleRoute');
    //    $moduleType   = $this->getRequest()->getParam('type');
    //    if (null != $moduleType) {
    //      $modulePage = '/subpages/' . $moduleType;
    //    }

    return $modulePage;
  }

  /**
   * Метод возвращает полный путь к файлу, в котором содержится описание
   * страницы администрирования указанного модуля
   *
   * @return string
   */
  private function _getConfigFilePath() {
    $moduleRoute = $this->_getModuleRoute();

    // Сканируем все модули приложения и ищем их описание админки в admin.xml
    $registry = Registry::getInstance();
    $configFile = $registry['path']['modules'] . $moduleRoute . DIR_SEP .
      'settings' . DIR_SEP . 'admin.xml';

    return $configFile;
  }

  /**
   * Метод возвращает полный путь к файлу, в котором содержатся языковые
   * переменные страницы администрирования указанного модуля
   *
   * @return string
   */
  private function _getLanguageFilePath() {
    $moduleRoute = $this->_getModuleRoute();

    // Сканируем все модули приложения и ищем их описание админки в admin.xml
    $registry = Registry::getInstance();

    // Узначем на каком языке должна быть панель управления
    $language = 'ru';
    if ( isset($registry['languageAdmin']) ) {
      $language = $registry['languageAdmin'];
    }

    $langFile = $registry['path']['modules'] . $moduleRoute . DIR_SEP .
      'languages' . DIR_SEP . 'admin' . DIR_SEP .
      $language . '.xml';

    return $langFile;
  }

  /**
   * Метод возвращает табы указанного модуля панели управления
   *
   * @param array $configValue - Переменные, описывающие табы модуля
   * @param array $langValue   - Языковые параметры табов модуля
   *
   * @return array
   */
  private function _getTabsModule($configValue, $langValue) {
    $moduleRoute = $this->_getModuleRoute();

    $active = null;
    if ( $this->getRequest()->equalUrl('/administrator/' . $moduleRoute . '/', false) ||
      $this->getRequest()->equalUrl('/administrator/content/' . $moduleRoute . '/', false)
    ) {
      $active = 'active';
    }

    $tabsValues = array();
    // Формируем переменные для таба "На главную"
    $tabsValues[] = array(
      'caption' => '',
      'active'  => $active,
      'small'   => 'small',
      'address' => '/administrator/' . $moduleRoute . '/',
      'class'   => 'default',
    );

    if ( empty($configValue) ) {
      return null;
    }

    $rules = $this->_getSubRules($moduleRoute);

    // Формируем переменные для всех описанных табов модуля
    foreach ($configValue as $name => $null) {
      if ( array_key_exists($name, $rules) &&
        't' == $rules[$name]['ara_hide_module'] )
      {
        continue;
      }

      $active = null;
      if ( $this->getRequest()->equalUrl('/administrator/' . $moduleRoute . '/' . $name . '/', false) ||
        $this->getRequest()->equalUrl('/administrator/content/' . $moduleRoute . '/' . $name . '/', false)
      ) {
        $active = 'active';
      }

      $tabsValues[] = array(
        'caption' => $langValue[$name],
        'active'  => $active,
        'address' => '/administrator/' . $moduleRoute . '/' . $name . '/',
        'class'   => $name,
      );
    }

    return $tabsValues;
  }

  private function _getConfigValues($path = null) {
    $type   = $this->getRequest()->getParam('type');

    if (null === $type || 'null' == $type) {
      $type = 'index';
    }

    $configValues = array();
    // Получаем данные описания таблицы модуля
    if ('/' != $path[0] || empty($path) ) {
      $datasourcesPath = $this->_getModulePage() . '/' . $type . '/' . $path;
    } else {
      $datasourcesPath = $path;
    }

    $configer = new Config(Config::XML);
    $configer->open( $this->_getConfigFilePath() );
    if ( $configer->exists($datasourcesPath) ) {
      $configValues = $configer->get($datasourcesPath, true);
    }

    if ($configer->exists($this->_getModulePage() . '/' . $type . '/reference') ) {
      $reference = $configer->get($this->_getModulePage() . '/' . $type . '/reference', true);
      if ( !isset($reference['module']) ) {
        $reference['module'] = $this->_getModuleRoute();
      }

      $registry = Registry::getInstance();
      $configFile = $registry['path']['modules'] . $reference['module'] . DIR_SEP .
        'settings' . DIR_SEP . 'admin.xml';

      $configer->open($configFile, $reference['module'] . '/' . $reference['page']);
      if ( !empty($path) && $configer->exists($path) ) {
        $configValues = $this->_array_extend($configer->get($path, true), $configValues);
      }
    }

    return $configValues;
  }

  private function _getLanguageValues($path = null, $reference = null) {
    $type   = $this->getRequest()->getParam('type');

    if (null === $type || 'null' == $type) {
      $type = 'index';
    }

    // Получаем данные описания таблицы модуля
    if ('/' != $path[0] || empty($path) ) {
      $datasourcesPath = $this->_getModulePage() . '/' . $type . '/' . $path;
    } else {
      $datasourcesPath = $path;
    }


    $configer = new Config(Config::XML);
    $configer->open( $this->_getLanguageFilePath() );
    $langValues = array();
    if ( $configer->exists($datasourcesPath) ) {
      $langValues = $configer->get($datasourcesPath);
    }

    if ( null != $reference ) {
      if ( !isset($reference['module']) ) {
        $reference['module'] = $this->_getModuleRoute();
      }

      $registry = Registry::getInstance();
      $language = 'ru';
      if ( isset($registry['languageAdmin']) ) {
        $language = $registry['languageAdmin'];
      }

      $langFile = $registry['path']['modules'] . $reference['module'] . DIR_SEP .
        'languages' . DIR_SEP . 'admin' . DIR_SEP .
        $language . '.xml';

      $configer->open($langFile, $reference['module'] . '/' . $reference['page']);

      if ( !empty($path) && $configer->exists($path) ) {
        $langValues = $this->_array_extend($configer->get($path), $langValues);
      }
    }

    return $langValues;
  }

  /**
   * Метод возвращает переменные, которые описывают указанную таблицу модуля
   * или все таблицы модуля и ее языковые парметры
   *
   * @param string $tableName = null - Имя таблицы
   *
   * @return array|null
   */
  private function _getVariablesTables($tableName = null) {
    $configValues = $this->_getConfigValues('datasources/' . $tableName);

    $reference = $this->_getConfigValues('reference');
    $langValues = $this->_getLanguageValues('datasources/' . $tableName, $reference);

    return array($configValues, $langValues);
  }

  private function _getRowData($table, $id) {
    list($configValues, $langValues) = $this->_getVariablesTables($table);

    if ( empty($configValues) ) {
      return null;
    }

    $sql = 'select ';

    $language = $this->getRequest()->get('language');
    if ( empty($language) ) {
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $language = $defaultLanguage['lng_synonym'];
    }

    $language = '_' . $language;
    // Получаем список полей запроса
    foreach ($configValues['fields'] as $name => $values) {
      if ( isset($values['languageField']) && 'true' == isset($values['languageField']) ) {
        if ( isset($values['field']) && null != $values['field'] ) {
          if ( false === mb_strpos($values['field'], '#lang#') ) {
            $name = $values['field'] . $language . ' ' . $name;
          } else {
            $name = str_replace('#lang#', $language, $values['field'])  . ' ' . $name;
          }
        } else {
          $name = $name . $language . ' ' . $name;
        }
      } else {
        if ( isset($values['field']) ) {
          $name = $values['field'] . ' ' . $name;
        }
      }

      $sql .= $name . ', ';
    }
    $sql = rtrim($sql, ', ');

    $prefix = null;
    if ( isset($configValues['prefix']) ) {
      $prefix = $configValues['prefix'];
    }

    // Получаем таблицу с которой будет производится выборка
    $sql .= ' from ' . $table . ' ' . $prefix;

    $joinsSql = null;
    if ( isset($configValues['joins']) ) {
      $joins = $configValues['joins'];

      foreach ($joins as $prefix => $vars) {
        if (empty($vars)) {
          continue;
        }
        $where = null;
        if ( isset($vars['where']) ) {
          $where = 'and ' . $vars['where'];
        }

        $joinsSql .= $vars['type'] . ' join ' . $vars['name'] . ' ' . $prefix . ' on '
          . $vars['pk'] . '='  . $prefix . '.' . $vars['fk'] . ' '
          . $where . chr(13);
      }
    }

    $sql .= ' ' . $joinsSql;

    $sql .= ' where ' . $configValues['pk'] . '=$1';

    if ( isset($configValues['group_by']) && null != $configValues['group_by'] ) {
      $sql .= ' group by ' . $configValues['group_by'];
    }

    if ( isset($configValues['query']) ) {
      $sql = 'select * from (' . $configValues['query'] . ') q where ' . $configValues['pk'] . '=$1';
    }

    preg_match_all('/{{(.*?)}}/si', $sql, $matches);
    $matches = $matches[1];

    foreach ($matches as $param){
      $sql = str_replace('{{' . $param . '}}', Request::getInstance()->get($param), $sql);
    }

    // $sql = str_replace('{{id}}', Request::getInstance()->get('id'), $sql);
    $selectResult = $this->db()->query()
      ->sql($sql)
      ->addParam( (int)$id )
      ->fetchRow(0, false);

    return $selectResult;
  }

  /**
   * Метод выполняет авторизацию пользователя, в случае неудачи формирует
   * сообщение о ошибке и записывает ее в переменную модели result
   *
   * @return Administrator
   */
  public function getLogin() {
    $result['address'] = '/administrator/';
    $lastAdminPage = $this->getRequest()->post('last-admin-page');
    if ( null !== $lastAdminPage ) {
      $result['address'] = $lastAdminPage;
    }

    $username = $this->getRequest()->post('name');
    $password = $this->getRequest()->post('password');

    // Установка коннектора к базе данных в классе авторизации
    $auth = Auth::getInstance()->setDb( $this->db() );

    // Установка переменных авторизации
    $auth->setTableName('(select * from administrators_tbl where adm_enabled=true) adm')
      ->setIdentityColumn('adm_username')
      ->setPasswordColumn('adm_password')
      ->setSaltColumn('adm_salt')

      ->setIdentity($username)
      ->setPassword($password);

    // Если при аутентификации призошла ошибка, обработать ee
    try {
      // Аутентификация пользователя
      $auth->authenticate();

      // Получаем имя пользователя
      $select = $this->db()->query()
        ->clearSql()
        ->addSql('select adm_name')
        ->addSql('from administrators_tbl')
        ->addSql('where adm_username = $1')
        ->addParam($username);
      $selectResult = $select->fetchRow(0, false);

      /** @noinspection PhpUndefinedFieldInspection */
      $auth->getStorage()->user_name = $selectResult['adm_name'];

      //TODO Сделать запись в таблицу истории логинов в админку юзерами
    } catch (AuthException $e) {
      //       Выдать сообщение о ошибке
      $result = $this->_setErrorMessage( $e->getCode() );
    }

    $this->setVariable('result', $result);

    return $this;
  }

  /**
   * Формирование панели пользователя в панели управления
   *
   * @return Administrator
   */
  public function getUserbar() {
    return array(
      'username' => Auth::getInstance()->getStorage()->user_name,
    );
  }

  /**
   * Формирование главного меню панели управления
   *
   * @return Administrator
   */
  public function getMainmenu() {
    return array(
      'mainmenu' => $this->_getMenu('MAIN'),
    );
  }

  /**
   * Формирование меню модулей панели управления
   *
   * @return Administrator
   */
  public function getModulesMenu() {
    return array(
      'modulesMenu' => $this->_getMenu('MODULE'),
    );
  }

  /**
   * Метод возвращает контентную часть понели управления
   *
   * @return Administrator
   */
  public function getContent() {
    $module = $this->_getModuleRoute();
    $type   = $this->getRequest()->getParam('type');
    $page   = $this->_getModulePage();

    $this->setVariable('module_type', $module . $type);
    $this->setVariable('moduleRoute', $module);
    $this->setVariable('type', $type);

    $langFile = $this->_getLanguageFilePath();
    $configFile = $this->_getConfigFilePath();

    // Получаем переменные с xml-файла модуля
    $langer = new Config(Config::XML);
    $langValues = $langer->open($langFile, $page)->get();

    $this->setVariables($langValues);

    $configer = new Config(Config::XML);
    $configer->open($configFile, $page);

    // Формируем табы модуля
    if ( $configer->exists('tabs') ) {
      $tabsValues = $this->_getTabsModule( $configer->get('tabs'),
        $langer->get('tabs') );

      $this->setVariable('tabs', $tabsValues);
    }

    return $this;
  }

  /**
   * @return Administrator
   */
  public function getSubPage() {
    // Если нужно вернуть субмодуль
    $datasources_params = $this->_getConfigValues('datasources/');
    if ( !empty($datasources_params) ) {
      $datasources_type = 'table';
      if ( isset($datasources_params['type']) ) {
        $datasources_type = $datasources_params['type'];
      }
      $this->setVariable('datasources', true);
      $this->setVariable($datasources_type, true);
      $this->setVariables($datasources_params);

      $reference = $this->_getConfigValues('reference');
      $langValues = $this->_getLanguageValues(null, $reference);
      $this->setVariables($langValues);

      return $this;
    }

    // Если нужно вернуть форму настроек
    $datasources_params = $this->_getConfigValues('configs/');
    if ( !empty($datasources_params) ) {
      $this->setVariables( $datasources_params );

      $reference = $this->_getConfigValues('reference');
      $langValues = $this->_getLanguageValues(null, $reference);
      $this->setVariables( $langValues );

      return $this;
    }

    // Если нужно вернуть языковые файлы
    $datasources_params = $this->_getConfigValues('language/');
    if ( !empty($datasources_params) ) {
      $this->setVariable('language', true);

      $this->setVariables( $datasources_params );

      $reference = $this->_getConfigValues('reference');
      $langValues = $this->_getLanguageValues(null, $reference);
      $this->setVariables( $langValues );

      return $this;
    }

    // Если нужно вернуть страницу графиков
    if ( 'charts' == $this->getRequest()->getParam('type') ) {
      $this->setVariable('charts', true);

      return $this;
    }

    return $this;
  }

  private function _getFormPageFields($group, $fields, $language, $settings, $level = 0) {
    if ( !empty($group) ) {
      $group .= '_';
    }

    $result = array();

    $level++;
    foreach ($fields as $name => $values) {
      $path = null;
      if ( isset($values['path']) ) {
        $path = '_' . $values['path'];
      }

      if (!isset($values['type'])) {
        $result[] = array(
          'group' => $language[$name]['caption'],
          'level' => $level + 2,
        );

        $result = array_merge($result, $this->_getFormPageFields($group . $name, $values, $language[$name], $settings, $level));

        continue;
      };

      $caption = null;
      if ( isset($language[$name]['caption']) ) {
        $caption = $language[$name]['caption'];
      }

      $hint = '';
      if ( isset($language[$name]['hint']) ) {
        $hint = htmlspecialchars($language[$name]['hint']);
      }

      // Получаем установленное значение
      $valueField = null;
      if ( isset($settings[$group . $name . $path]) &&
        !empty($settings[$group . $name . $path]) &&
        'false' != $settings[$group . $name . $path])
      {
        $valueField = $settings[$group . $name . $path];
      }

      $row = true;
      if ('bool' == $values['type']) {
        $row = false;
      }

      $labelStyle = '';
      $style = '';
      if ( isset($values['inputStyle']) ) {
        $style = $values['inputStyle'];
      }
      if ( isset($values['labelStyle']) ) {
        $labelStyle = $values['labelStyle'];
      }

      $required = false;
      if ( isset($values['required']) && 'true' == $values['required']) {
        $required = true;
      }

      $focused = false;
      if ( isset($values['focused']) && 'true' == $values['focused']) {
        $focused = true;
      }

      $richedit = false;
      if ( isset($values['rich']) && 'true' == $values['rich']) {
        $richedit = true;
      }

      $richContainerClass = null;
      if ( isset($values['richContainerClass']) ) {
        $richContainerClass = $values['richContainerClass'];
      }

      $richContainerClassDinamyc = null;
      if ( isset($values['richContainerClassDinamyc']) ) {
        $richContainerClassDinamyc = $values['richContainerClassDinamyc'];
      }

      $richNoStyleFile = false;
      if ( isset($values['richNoStyleFile']) && 'true' == $values['richNoStyleFile']) {
        $richNoStyleFile = true;
      }

      $readonly = false;
      if ( isset($values['readonly']) && 'true' == $values['readonly']) {
        $readonly = true;
      }

      $rows = 3;
      if ( isset($values['rows']) ) {
        $rows = $values['rows'];
      }

      $code = null;
      if ( isset($values['code']) ) {
        $code = $values['code'];
      }

      $items = array();
      if ( isset($values['list']) ) {
        if ( 'static' == $values['list'] ) {
          foreach ($language[$name]['values'] as $pk => $item) {
            $defaultItem = null;
            if ( !empty($valueField) && $pk == $valueField ) {
              $defaultItem = 'selected';
            }

            $items[] = array(
              'id' => $pk,
              'caption' => $item,
              'default' => $defaultItem,
            );
          }
        } elseif ( 'function' == $values['list'] ) {
          $defaultItem = null;
          if ( !empty($valueField) ) {
            $defaultItem = $valueField;
          }

          $path = Registry::get('path');
          $module_path = $path['modules'];
          /** @noinspection PhpIncludeInspection */
          include_once($module_path . $values['module'] . DIR_SEP  . 'models' . DIR_SEP . $values['model'] . '.php');
          $class = new $values['model'];
          $items = $class->{$values['function']}($defaultItem);
        }
      }

      $result[] = array(
        'id'            => $group . $name,
        'row'           => $row,
        $values['type'] => true,
        'caption'       => $caption,
        'description'   => $hint,
        'value'         => $valueField,
        'rows'          => $rows,
        'style'         => $style,
        'label_style'   => $labelStyle,
        'required'      => $required,
        'focused'       => $focused,
        'richedit'      => $richedit,
        'code'          => $code,
        'richContainerClass' => $richContainerClass,
        'richContainerClassDinamyc' => $richContainerClassDinamyc,
        'richNoStyleFile' => $richNoStyleFile,
        'readonly'      => $readonly,
        'item'      => $items,
      );
    }

    return $result;
  }

  private function _getVariablesConfig2($path, $fileName)
  {
    $fileDefaultConfig = $path . $fileName;
    // Определение полного имени файла конфига модуля со значеиями по-умолчанию
    if ($fileName == 'general.xml') {
      $fileConfig = str_replace('/application/settings/', '/settings/', $fileDefaultConfig);
    }
    if ($fileName == 'config.xml') {
      $fileConfig = str_replace('/application/', '/settings/', $fileDefaultConfig);
      $fileConfig = str_replace('/settings/' . $fileName, '/' . $fileName, $fileConfig);
    }
    // $pathInfo = pathinfo($fileConfig);
    // $fileDefaultConfig = $pathInfo['dirname'] . DIR_SEP
    //           . $pathInfo['filename'] . '.default.'
    //           . $pathInfo['extension'];

    $config_default_values = array();
    // Если файл конфига модуля со значениями по умолчанию существует
    if ( file_exists($fileDefaultConfig) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileDefaultConfig);
      $config_default_values = $configLoader->getValues();
    }

    $config_values = null;
    if ( file_exists($fileConfig) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileConfig);
      $config_values = $configLoader->getValues();
    }
    $config_values['default_variables']['config'] = $config_default_values;

    $result = array( 'config' =>  $config_values);

    return $result;
  }

  private function _getVariablesLang($path, $lang)
  {
    // Определение каталога, где размещен языковый файл модуля со значениями по умолчанию
    $defaultPath = $path;

    // Определение полного имени файла конфига модуля со значеиями по-умолчанию
    $fileDefaultLanguage = $defaultPath . $lang . '.xml';

    $config_default_values = array();
    if ( file_exists($fileDefaultLanguage) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileDefaultLanguage);
      $config_default_values = $configLoader->getValues();
    }

    // Определение полного имени файла конфига модуля
    $fileLanguage = $path . $lang . '.xml';
    $fileLanguage = str_replace('/application/', '/settings/', $fileLanguage);
    // var_dump($fileDefaultLanguage);
    // var_dump($fileLanguage);

    $config_values = array();
    if ( file_exists($fileLanguage) ) {
      // Загрузка перемнных конфига модуля с xml файла и преобразование их
      // в многомерный ассоцеативный массив
      $configLoader = new Xml;
      $configLoader->setFileSettings($fileLanguage);
      $config_values = $configLoader->getValues();
    }

    // Объединяем значения по умолчанию со значениеми указанными в языковом
    // файле (значение в языковом файле заменяет значение по умолчанию)
    $config_values['default_variables']['language'] = $config_default_values;

    $result = array( 'language' =>  $config_values);

    return $result;
  }

  private function _getModuleSettings($module, $language = null) {
    $registry = Registry::getInstance();

    if ( empty($language) ) {
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $language = $defaultLanguage['lng_synonym'];
    }

    if ('root' == $module) {
      // Получаем настройки с конфига
      $settings_path  = $this->getVariable('path_settings');
      $settings = $this->_getVariablesConfig2($settings_path, 'general.xml');

      // Получаем настройки языкового файла
      $languages_path = $this->getVariable('path_languages');
      $languages = $this->_getVariablesLang($languages_path, $language);
    } else {
      // Получаем настройки с конфига
      $settings_path  = $this->getVariable('path_modules') .  $module . DIR_SEP . 'settings' . DIR_SEP;
      $settings = $this->_getVariablesConfig2($settings_path, 'config.xml');

      // Получаем настройки языкового файла
      $languages_path = $this->getVariable('path_modules') .  $module . DIR_SEP . 'languages' . DIR_SEP;
      $languages = $this->_getVariablesLang($languages_path, $language);
      // var_dump($languages);
    }

    $settings_default = $registry->getFlatArray($settings['config']['default_variables']['config']);
    unset($settings['config']['default_variables']);
    $settings = $registry->getFlatArray($settings['config']);

    $languages_default = $registry->getFlatArray($languages['language']['default_variables']['language']);
    unset($languages['language']['default_variables']);
    $languages = $registry->getFlatArray($languages['language']);

    $result = array_merge($settings_default, $settings,
      $languages_default, $languages);

    return $result;
  }

  public function getFormPage() {
    $request = $this->getRequest();
    $module = $request->getParam('moduleRoute');
    $type = $request->getParam('type');
    $operation = 'save';

    if ( empty($type) ) {
      $type = 'index';
    }

    $this->setVariables( array(
      $operation => true,
      'module'   => $module,
      'type'     => $type,
      'action'   => $operation,
    ) );

    $style = $this->_getConfigValues();
    if ( isset($style['style']) ) {
      $this->setVariable('style', $style['style']);
    }
    $style = null;

    $configValues = $this->_getConfigValues('configs');
    $langValues = $this->_getLanguageValues('configs');

    $fields = array();
    $language = $this->getRequest()->get('language');

    foreach ($configValues as $module => $variables) {
      $settings = $this->_getModuleSettings($module, $language);

      $fields = array_merge($fields,
        $this->_getFormPageFields(null, $variables,
        $langValues[$module], $settings, 0) );
    }

    return array(
      'field' => $fields,
    );
  }

  public function getLanguagePage() {
    $request = $this->getRequest();
    $module = $request->getParam('moduleRoute');
    $type = $request->getParam('type');
    $operation = 'saveLanguage';

    if ( empty($type) ) {
      $type = 'index';
    }

    $this->setVariables( array(
      $operation => true,
      'module'   => $module,
      'type'     => $type,
      'action'   => $operation,
    ) );

    $style = $this->_getConfigValues();
    if ( isset($style['style']) ) {
      $this->setVariable('style', $style['style']);
    }
    $style = null;

    $configValues = $this->_getConfigValues('language');
    $language = $this->getRequest()->get('language');
    if ( empty($language) ) {
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $language = $defaultLanguage['lng_synonym'];
    }

    $path = Registry::get('path');

    $file_path = $path['userSettings'] . $configValues['file']['path'] . $language . '.' . $configValues['file']['type'];
    $file_default_path = $path['application'] . $configValues['file']['path'] . $language . '.' . $configValues['file']['type'];

    $value = null;

    $language_default_vars = array();
    if (file_exists($file_default_path)) {
      $language_default = new Config(Config::XML);
      $language_default->open($file_default_path);
      $language_default_vars = $language_default->get();
    }

    $language_vars = array();
    if ( file_exists($file_path) ) {
      $language = new Config(Config::XML);
      $language->open($file_path);
      $language_vars = $language->get();
    }

    $result = new Config(Config::XML);
    $result->open(Registry::array_merge_recursive_unique($language_default_vars, $language_vars));
    $value = htmlspecialchars($result->getContent());

    return array(
      'value' => $value,
      'file' => $file_path,
    );
  }

  public function getChartsPage() {
    $tab = $this->getRequest()->getParam('tab');
    if ( empty($tab) ) {
      $charts_params = $this->_getConfigValues('index');
    } else {
      $charts_params = $this->_getConfigValues($tab);
    }

    $charts = array();
    foreach ($charts_params as $name => $params) {
      if ('line' == $params['type']) {
        include_once('ChartLine.php');
        $chart = new ChartLine($name, 'asdasd', $params['style']);
        $chart->setDb( $this->db() )
          ->setQuery('select *, rsv_sum as rsv_total_sum from reserves_tbl')
          ->setXKey($params['xkey'])
          ->setBeginDate('01.01.2011')
          ->setEndDate('29.03.2012');

        foreach ($params['ykeys'] as $y_name => $y_value) {
          $chart->addYKeys($y_name, $y_value['field'], 'sdsdsd', $y_value['color']);
        }

        $charts[] = $chart->getMorrisData();
      }

    }

    return array(
      'chart' => $charts
    );
  }

  private function _getCrumbData($type, $values, $params, $caption) {
    $crumb = array();

    if ('static' == $type) {
      $crumb['caption'] = $caption;
      $crumb['url'] = '/administrator/' . $values['value'];
      $crumb['url'] = str_replace('//', '/', $crumb['url']);

      return array($crumb);
    }

    $parent_field = null;
    if ( isset($values['parent_field'])) {
      $parent_field = ', ' . $values['parent_field'];
    }

    $sqlFields = null;
    foreach ($values['params'] as $param => $value) {
      $sqlFields .= ', ' . $param;
    }

    $language = $this->getRequest()->get($values['table'] . '_language');

    if ( empty($language) ){
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $language = $defaultLanguage['lng_synonym'];
    }

    $language = '_' . $language;
    if ( false !== mb_strpos($values['title_field'], '#lang#') ) {
      $values['title_field'] = str_replace('#lang#', $language, $values['title_field']);
    }

    $select = $this->db()->query()
      ->clearSql()
      ->addSql('select ' . $values['pk'] . ',')
      ->addSql($values['title_field'] . $parent_field . $sqlFields)
      ->addSql('from ' . $values['table']);
    $where = 'where ';
    $i = 1;
    foreach ($values['params'] as $field_name => $get_name) {
      if (array_key_exists($get_name, $params)) {
        $where .= $field_name . '=$' . $i . ' and ';
        $select->addParam($params[$get_name]);

        $i++;
      }
    }
    $where = rtrim($where, 'and ');
    $select->addSql($where);

    $selectResult = $select->fetchRow(0, false);

    if (!empty($selectResult) ) {
      $crumb['caption'] = str_replace('{{caption}}',
        $selectResult[$values['title_field']],
        $caption);

      $crumb['url'] = str_replace('{{value}}',
        $selectResult[$values['pk']],
        $values['url']);

      foreach ($values['params'] as $param => $value) {
        $crumb['url'] = str_replace('{{' . $param . '}}',
          $selectResult[$param],
          $crumb['url']);
      }

    }

    $crumbs = array();
    if ( !empty($crumb) ) {
      $crumbs[] = $crumb;
    }

    if ('recursive' == $type && !empty($selectResult)) {
      $crumb = array();

      $params = array();
      $params['parent_id'] = $selectResult[$values['parent_field']];

      $crumb = $this->_getCrumbData($type, $values, $params, $caption);
      if ( !empty($crumb) ) {
        $crumbs = array_merge($crumb, $crumbs);
      }
    }

    return $crumbs;
  }

  /**
   * Метод возвращает хлебные крошки данных в модуле
   *
   * @return Administrator
   */
  public function getCrumbs() {
    $request = $this->getRequest();

    $configValues = $this->_getConfigValues('crumbs/');

    $reference = $this->_getConfigValues('reference');
    $langValues = $this->_getLanguageValues('crumbs/', $reference);

    $crumbs = array();
    foreach ($configValues as $name => $values) {
      if ( empty($values) ) {
        continue;
      }

      if ( !is_array($values) ) {
        $values = array('value' => $values);
      }

      if ( isset($values['getVarNotNull']) ) {
        if (null === $request->get($values['getVarNotNull']) ) {
          continue;
        }
      }

      $crumb = array();
      $type = 'static';
      if ( isset($values['type']) ) {
        $type = $values['type'];
      }

      $crumb['caption'] = $langValues[$name];

      if ( !isset($values['url']) ) {
        $values['url'] = null;
      }

      $params = array();
      if ( !empty($values['params']) ) {
        foreach ($values['params'] as $field_name => $get_name) {
          $params[$get_name] = $request->get($get_name);
        }
      }

      $crumbsData = $this->_getCrumbData($type, $values, $params, $crumb['caption']);

      foreach($crumbsData as $crumbData) {
        $crumbs[] = $crumbData;
      }
    }

    return array(
      'crumbs' => $crumbs,
    );
  }

  /**
   * Метод возвращает блок управления фильтрами для таблицы модуля
   *
   * @return Administrator
   */
  public function getFilters() {
    return $this;
  }

  /**
   * Метод возвращает блок управления маркерами для таблицы модуля
   *
   * @return Administrator
   */
  public function getMarkers() {
    return $this;
  }

  /**
   * Метод возвращает кнопки действий для модуля панели управления
   *
   * @return Administrator
   */
  public function getActions() {
    $request = $this->getRequest();
    $moduleRoute = $request->getParam('moduleRoute');
    $typeModule = $request->getParam('type');

    if (null === $typeModule || 'null' === $typeModule) {
      $typeModule = 'index';
    }

    $moduleUrl = '/administrator/' . $moduleRoute . '/' . $typeModule . '/';

    $configValues = $this->_getConfigValues('actions/');

    $reference = $this->_getConfigValues('reference');
    $langValues = $this->_getLanguageValues('actions/', $reference);

    $rules = $this->_getRules();
    $subRules = $this->_getSubRules($moduleRoute);

    $actions = array();
    foreach ($configValues as $name => $values) {
      if ( isset($values['visible']) && 'false' == $values['visible']) {
        continue;
      }
      if ( 'index' == $typeModule && array_key_exists($moduleRoute, $rules) ) {
        if ( !isset($rules[$moduleRoute]['ara_' . $name . '_records']) ) {
          continue;
        }

        if ('NOT' == $rules[$moduleRoute]['ara_' . $name . '_records']) {
          continue;
        }
      }

      if ( array_key_exists($typeModule, $subRules) ) {
        if ( !isset($rules[$moduleRoute]['ara_' . $name . '_records']) ) {
          continue;
        }

        if ('NOT' == $subRules[$typeModule]['ara_' . $name . '_records']) {
          continue;
        }
      }

      $actions[] = array(
        'name' => $name,
        'module_url' => $moduleUrl,
        'caption' => $langValues[$name]
      );
    }

    return array(
      'action' => $actions,
    );
  }

  /**
   * Метод возвращает сформированные таблицы указанного модуля
   *
   * @return null|string
   */
  public function getTable() {
    list($configValues, $langValues) = $this->_getVariablesTables();

    if ( empty($configValues) ) {
      return null;
    }

    $rules = $this->_getRules();
    $modulePage = $this->getRequest()->getParam('moduleRoute');
    $subRules = $this->_getSubRules($modulePage);
    $subModulePage = $this->getRequest()->getParam('type');

    //TODO Исправить, стделать поддержку отображения нескольких таблиц

    // Формируем таблицы панели управления модуля
    $table = new Table();
    $table->setTemplater('blitz', new Templater())
      ->setDb($this->db());

    $quickFilter = null;
    foreach ($configValues as $tableName => $vars) {
      if ( empty($subModulePage) && array_key_exists($modulePage, $rules) ) {
        if ('NOT' == $rules[$modulePage]['ara_edit_records']) {
          unset($vars['actions']['edit']);
        }
      }
      if ( empty($subModulePage) && array_key_exists($modulePage, $rules) ) {
        if ('NOT' == $rules[$modulePage]['ara_delete_records']) {
          unset($vars['actions']['delete']);
        }
      }

      if ( array_key_exists($subModulePage, $subRules) ) {
        if ('NOT' == $subRules[$subModulePage]['ara_edit_records']) {
          unset($vars['actions']['edit']);
        }
      }
      if ( array_key_exists($subModulePage, $subRules) ) {
        if ('NOT' == $subRules[$subModulePage]['ara_delete_records']) {
          unset($vars['actions']['delete']);
        }
      }

      //      var_dump($vars);
      foreach ($vars['fields'] as $field_id => $field) {
        if (isset($field['link'])) {
          if ( array_key_exists($field['link']['page'], $subRules) ) {
            if ('t' == $subRules[$field['link']['page']]['ara_hide_module']) {
              unset($vars['fields'][$field_id]['link']);
            }
          }
        }
      }

      $orderColumn = null;
      $orderType = null;
      if ( !(isset($configValues[$tableName]['userOrder']) &&
        'false' == $configValues[$tableName]['userOrder']) )
      {
        $orderColumn = $this->getRequest()->get($tableName . '_orderColumn');
        $orderType = $this->getRequest()->get($tableName . '_orderType');
      }
      $rowsOnPage = $this->getRequest()->get($tableName . '_rowsOnPage');
      $currentPage = $this->getRequest()->get($tableName . '_currentPage');
      $quickFilter = $this->getRequest()->get($tableName . '_quickFilter');
      $language = $this->getRequest()->get($tableName . '_language');

      if (-1 == $rowsOnPage) {
        $rowsOnPage = null;
      }

      if ( empty($currentPage) ) {
        $currentPage = 1;
      }

      if (null != $orderColumn) {
        $vars['order_by'] = $orderColumn . ' ' . $orderType;
      }

      if ( empty($language) ) {
        $defaultLanguage = $this->db()->query()
          ->addSql('select lng_synonym from languages_tbl')
          ->addSql('where lng_default=true')
          ->fetchRow(0, false);

        $language = $defaultLanguage['lng_synonym'];
      }

      $table->setLanguage($language)
        ->load($tableName, $vars, $langValues[$tableName])
        ->setCountOnPage($rowsOnPage)
        ->setCurrentPage($currentPage)
        ->setFindValue($quickFilter);
    }

    if ( empty($subModulePage) && array_key_exists($modulePage, $rules) ) {
      if ( !empty($rules[$modulePage]['ara_filter']) ) {
        $where = $table->getWhere();
        if ( empty($where) ) {
          $table->setWhere($rules[$modulePage]['ara_filter']);
        } else {
          $table->appendWhere(' and (' . $rules[$modulePage]['ara_filter'] . ')');
        }
      }
    }
    if ( array_key_exists($subModulePage, $subRules) ) {
      if ( !empty($subRules[$subModulePage]['ara_filter']) ) {
        $where = $table->getWhere();
        if ( empty($where) ) {
          $table->setWhere($subRules[$subModulePage]['ara_filter']);
        } else {
          $table->appendWhere(' and (' . $subRules[$subModulePage]['ara_filter'] . ')');
        }
      }
    }

    $table->setAdditionVars( array_merge($this->getVariables(), array(
      'countPages'  => $table->getCountPages(),
      'currentPage' => $table->getCurrentPage(),
      'quickFilter' => $quickFilter,
    ) ) );

    $result = $table->getTableHtml();

    return $result;
  }

  /**
   * Метод возвращает сформированные строки указанной таблицы модуля с
   * указанными параметрами
   *
   * @return Administrator
   */
  public function getTableRows() {
    $tableName   = $this->getRequest()->get('table');
    $rowsOnPage  = $this->getRequest()->get('rowsOnPage');
    $numPage     = $this->getRequest()->get('numPage');
    $quickFilter = $this->getRequest()->get('quickFilter');
    $language = $this->getRequest()->get('language');

    if (-1 == $rowsOnPage) {
      $rowsOnPage = null;
    }

    if (0 === (int)$numPage) {
      $numPage = 1;
    }

    list($configValues, $langValues) = $this->_getVariablesTables($tableName);

    $orderColumn = null;
    $orderType = null;

    if ( !(isset($configValues['userOrder']) &&
      'false' == $configValues['userOrder']) )
    {
      $orderColumn = $this->getRequest()->get('orderColumn');
      $orderType   = $this->getRequest()->get('orderType');
    }

    $rules = $this->_getRules();
    $modulePage = $this->getRequest()->getParam('moduleRoute');
    $subRules = $this->_getSubRules($modulePage);
    $subModulePage = $this->getRequest()->getParam('type');

    if ( empty($subModulePage) && array_key_exists($modulePage, $rules) ) {
      if ('NOT' == $rules[$modulePage]['ara_edit_records']) {
        unset($configValues['actions']['edit']);
      }
    }
    if ( empty($subModulePage) && array_key_exists($modulePage, $rules) ) {
      if ('NOT' == $rules[$modulePage]['ara_delete_records']) {
        unset($configValues['actions']['delete']);
      }
    }

    if ( array_key_exists($subModulePage, $subRules) ) {
      if ('NOT' == $subRules[$subModulePage]['ara_edit_records']) {
        unset($configValues['actions']['edit']);
      }
    }
    if ( array_key_exists($subModulePage, $subRules) ) {
      if ('NOT' == $subRules[$subModulePage]['ara_delete_records']) {
        unset($configValues['actions']['delete']);
      }
    }

    foreach ($configValues['fields'] as $field_id => $field) {
      if (isset($field['link'])) {
        if ( array_key_exists($field['link']['page'], $subRules) ) {
          if ('t' == $subRules[$field['link']['page']]['ara_hide_module']) {
            unset($configValues['fields'][$field_id]['link']);
          }
        }
      }
    }

    $order = '';
    if ( !empty($orderColumn) ) {
      $order = $orderColumn . ' ' . $orderType;
    }

    if ( empty($language) ) {
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $language = $defaultLanguage['lng_synonym'];
    }

    $table = new Table();
    $table->setTemplater('blitz', new Templater())
      ->setDb( $this->db() )
      ->setLanguage($language)
      ->load($tableName, $configValues, $langValues)
      ->setFindValue($quickFilter)
      ->setCurrentPage($numPage)
      ->setAdditionVars($this->getVariables() );

    if ( !empty($order) ) {
      $table->setOrderBy($order);
    }

    if ( empty($subModulePage) && array_key_exists($modulePage, $rules) ) {
      if ( !empty($rules[$modulePage]['ara_filter']) ) {
        $where = $table->getWhere();
        if ( empty($where) ) {
          $table->setWhere($rules[$modulePage]['ara_filter']);
        } else {
          $table->appendWhere(' and (' . $rules[$modulePage]['ara_filter'] . ')');
        }
      }
    }
    if ( array_key_exists($subModulePage, $subRules) ) {
      if ( !empty($subRules[$subModulePage]['ara_filter']) ) {
        $where = $table->getWhere();
        if ( empty($where) ) {
          $table->setWhere($subRules[$subModulePage]['ara_filter']);
        } else {
          $table->appendWhere(' and (' . $subRules[$subModulePage]['ara_filter'] . ')');
        }
      }
    }

    if (null !== $rowsOnPage) {
      $table->setCountOnPage($rowsOnPage);
    }

    $result = array(
      'rows' => $table->getRowsHtml(),
      'count_rows' => $table->getCountPages(),
    );

    $this->setVariable('result', $result);

    return $this;
  }

  public function getCustomForm() {
    return $this;
  }

  //TODO Исправить
  private function _getCustomForm() {
    $request = $this->getRequest();
    $operation = $request->getParam('operation');
    $tableName = $request->get('tableName');
    $id = $request->getParam('id');
    // Если форма относится к модулю в целом
    if ( empty($id) ) {
      $root = 'actions/';
    } else {
      $root = 'datasources/' . $tableName . '/actions/';
    }

    $action = $this->_getConfigValues($root . $operation);
    $reference = $this->_getConfigValues('reference');

    $actionLang = $this->_getLanguageValues($root, $reference);
    // Выполняю указанную функцию и возвращаю результат ее выполнения
    if ('confirm' == $action['form']) {
      $formValues['confirm_text'] = $actionLang[$operation . '_confirm_text'];
      $this->setVariable('confirm', $formValues);
      $this->setVariable('custom_form_class', 'confirm-form');
    } else {
      $form = $this->_getConfigValues('/' . $this->_getModulePage() . '/forms/' . $operation);
      if ( isset($form['style']) ) {
        $this->setVariable('style', $form['style']);
      }
      $fields = $this->getForm('/' . $this->_getModulePage() . '/forms/' . $operation);

      $tabs = array();
      foreach ($fields as $field) {
        $tabs[$field['tab']]['id'] = $field['tab'];
        $tabs[$field['tab']]['field'][] = $field;
      }
      $this->setVariable('form_fields', array('tabs' => array_values($tabs)) );
      $this->setVariable('langConfig', $form );
    }

    return $this;
  }

  private function _customAction() {
    $request = $this->getRequest();
    $operation = $request->getParam('operation');
    $tableName = $request->get('tableName');
    $id = $request->getParam('id');
    // Если форма относится к модулю в целом
    if ( empty($id) ) {
      $root = 'actions/';
    } else {
      $root = 'datasources/' . $tableName . '/actions/';
    }

    $action = $this->_getConfigValues($root . $operation);

    $validate = $this->_validate('/' . $this->_getModulePage() . '/forms/' . $operation);
    if ( true !== $validate ) {
      return $validate;
    }

    $path = Registry::get('path');
    $module_path = $path['modules'];
    /** @noinspection PhpIncludeInspection */
    include_once($module_path . $action['module'] . DIR_SEP  . 'models' . DIR_SEP . $action['model'] . '.php');
    $class = new $action['model'];
    $class->{$action['function']}();

    return $this;
  }

  public function getModalForm() {
    $request = $this->getRequest();
    $tableName = $request->get('tableName');
    $module = $request->getParam('moduleRoute');
    $type = $request->getParam('type');
    $operation = $request->getParam('operation');
    $id = $request->getParam('id');

    if ('add_child' == $operation) {
      $operation = 'add';
    }

    list($configValues, $langValues) = $this->_getVariablesTables();

    if ( 'edit' == $operation && isset($configValues[$tableName]['listLanguagesField']) ) {
      $languages = $this->db()->query()
        ->addSql('select ' . $configValues[$tableName]['listLanguagesField'])
        ->addSql('from ' . $tableName . ' where ' . $configValues[$tableName]['pk'] . '=$1')
        ->addParam($id)
        ->fetchRow(0, false);

      $languages = trim($languages[$configValues[$tableName]['listLanguagesField']], '|');
      $languages = explode('|', $languages);

      $languagesRow = null;
      if ( !empty($languages) ) {
        foreach ($languages as $lang) {
          $languagesRow[$lang] = true;
        }
      }
      $this->setVariable('languagesRow', $languagesRow);
    }

    $tabs = null;
    if ( isset($configValues[$tableName]['tabs']) ) {
      foreach ($configValues[$tableName]['tabs'] as $id_tab => $values_tab) {
        $tabs[] = array(
          'id' => $id_tab,
          'caption' => $langValues[$tableName]['tabs'][$id_tab],
        );
      }
    }

    $style = null;
    if ( isset($configValues[$tableName]['style']) ) {
      $style = $configValues[$tableName]['style'];
    }

    $result[$operation] = true;
    $result['module'] = $module;
    $result['type'] = $type;
    $result['action'] = $operation;
    $result['id'] = $id;
    $result['table'] = $tableName;
    $result['style'] = $style;
    $result['tabs'] = $tabs;
    $result['caption'] = $langValues[$tableName]['actions']
      [$operation]['caption'];

    if ( isset($langValues[$tableName]['actions'][$operation]['button']) ) {
      $result['action_button_caption'] =
        $langValues[$tableName]['actions'][$operation]['button'];
    } else {
      $result['action_button_caption'] = $this->getVariable('lng_forms_'
        . $operation);
    }
    switch ($operation) {
    case 'delete':
      if ( true !== $this->_relativeTablesDelete('block_tables') ) {
        $result['error'] = true;
      }

      unset($result['tabs']);
      break;

    case 'add':
      break;

    case 'add_child':
      break;

    case 'edit':
      break;

    case 'info':
      $result['only_close'] = true;
      break;

    default:
      unset($result['style']);
      unset($result['tabs']);
      $this->_getCustomForm();
      $result['custom'] = true;
    }

    $this->setVariables($result);

    return $this;
  }

  private function _getRelativeRows(array $tables, $id) {
    $block_tables = array();
    foreach ($tables as $table => $variables) {
      $fkeys = explode(',', $variables['fk']);
      $select = $this->db()->query()
        ->addSql('select count(*) as cnt from ' . $table)
        ->addSql('where ');
      $sql = '';
      foreach ($fkeys as $fk) {
        $sql .= $fk . '=$1 or ';
      }
      $sql = rtrim($sql, 'or ');

      $selectResult = $select
        ->addSql($sql)
        ->addParam( (int)$id )
        ->fetchRow(0, false);

      if ( 0 != $selectResult['cnt'] ) {
        $block_tables[] = array(
          'table'      => $this->getVariable('lng_tables_name_' . $table),
          'count_rows' => $selectResult['cnt']
        );
      }
    }

    return $block_tables;
  }

  private function _relativeTablesDelete($type) {
    $request = $this->getRequest();
    $tableName = $request->get('tableName');
    $operation = $request->getParam('operation');
    $id = $request->getParam('id');

    list($configValues) = $this->_getVariablesTables($tableName);

    $action = $configValues['actions'][$operation];
    // Проверяем, есть ли блокирующие таблицы, если их нет выходим с
    // функции, вернув true
    if ( !isset($action[$type]) ) {
      return true;
    }

    $tables = $this->_getRelativeRows($action[$type], $id);

    if ( !empty($tables) ) {
      return $tables;
    }

    return true;
  }

  public function getDeleteForm() {
    $request = $this->getRequest();
    $tableName = $request->get('tableName');
    $operation = $request->getParam('operation');
    $id = $request->getParam('id');
    $rowData = $this->_getRowData($tableName, $id);

    list($configValues, $langValues) = $this->_getVariablesTables();

    $text = $langValues[$tableName]['actions'][$operation]['text'];
    $text = str_replace('{{name}}', $rowData[$configValues[$tableName]['title_field']], $text);
    $result['text'] = $text;

    if ( true !== $block_tables = $this->_relativeTablesDelete('block_tables') ) {
      $error = $langValues[$tableName]['actions'][$operation]['error'];
      $error = str_replace('{{name}}', $rowData[$configValues[$tableName]['title_field']], $error);
      $result['error'] = $error;
      $result['block_table'] = $block_tables;
    } else
      if ( true !== $relative_tables = $this->_relativeTablesDelete('relatives_tables') ) {
        $relative = $langValues[$tableName]['actions'][$operation]['relative'];
        $relative = str_replace('{{name}}', $rowData[$configValues[$tableName]['title_field']], $relative);
        $result['relative'] = $relative;
        $result['relative_table'] = $relative_tables;
      }

    return $result;
  }

  private function _sortFormFields(array $fields) {
    function sortByTabIndex($key1, $key2) {
      if ( !isset($key1['tabindex']) ) {
        $key1['tabindex'] = -1;
      }

      if ( !isset($key2['tabindex']) ) {
        $key2['tabindex'] = -1;
      }

      return $key1['tabindex'] > $key2['tabindex'];
    }

    usort($fields, "sortByTabIndex");

    return $fields;
  }

  public function getField($operation, $langValues, $field, $values, $i) {
    $request = $this->getRequest();
    $tableName = $request->get('tableName');

    if ( isset($values['noForm']) && 'true' == $values['noForm'] && !isset($values['fromfile']) ) {
      return null;
    }

    $id = $field;
    if ( isset($values['id']) ) {
      $id = $values['id'];
    }

    if ( !isset($values['tabindex']) ) {
      $values['tabindex'] = $i;
    }

    $row = true;
    if ('bool' == $values['type']) {
      $row = false;
    }

    $fromfile = null;
    if ( isset($values['fromfile']) && 'true' == $values['fromfile']) {
      $fromfile = true;
    }

    $filename = null;
    if ( isset($values['filename']) ) {
      $filename = $values['filename'];
    }


    $labelStyle = '';
    $style = '';
    if ( isset($values['inputStyle']) ) {
      $style = $values['inputStyle'];
    }
    if ( isset($values['labelStyle']) ) {
      $labelStyle = $values['labelStyle'];
    }

    $caption = $langValues[$field]['caption'];
    if ( isset($langValues[$field]['form_caption']) ) {
      $caption = $langValues[$field]['form_caption'];
    }

    $required = false;
    if ( isset($values['required']) && 'true' == $values['required']) {
      $required = true;
    }

    $focused = false;
    if ( isset($values['focused']) && 'true' == $values['focused']) {
      $focused = true;
    }

    $richedit = false;
    if ( isset($values['rich']) && 'true' == $values['rich']) {
      $richedit = true;
    }

    $richContainerClass = null;
    if ( isset($values['richContainerClass']) ) {
      $richContainerClass = $values['richContainerClass'];
    }

    $richContainerClassDinamyc = null;
    if ( isset($values['richContainerClassDinamyc']) ) {
      $richContainerClassDinamyc = $values['richContainerClassDinamyc'];
    }

    $richNoStyleFile = false;
    if ( isset($values['richNoStyleFile']) && 'true' == $values['richNoStyleFile']) {
      $richNoStyleFile = true;
    }

    $code = null;
    if ( isset($values['code']) ) {
      $code = $values['code'];
    }

    $readonly = false;
    if ( isset($values['readonly']) && 'true' == $values['readonly']) {
      $readonly = true;
    }

    if ( 'edit' == $operation && isset($values['noedit']) && 'true' == $values['noedit']) {
      $readonly = true;
    }

    $rows = 3;
    if ( isset($values['rows']) ) {
      $rows = $values['rows'];
    }

    $default = null;
    if ( isset($values['default']) && 'false' != $values['default']) {
      if ('now' == $values['default']) {
        if ( isset($values['default_addition']) ) {
          $default = date($values['default_format'], strtotime(date('Y-m-d H:i') . ' ' . $values['default_addition']));
        } else {
          $default = date($values['default_format']);
          //                    var_dump($default);
        }
      } else
        if ( isset($values['defaultIsVariable']) && 'true' == $values['defaultIsVariable']) {
          if ( isset($values['default']['type']) && 'language' == $values['default']['type']) {
            $registry = Registry::getInstance();
            // Получаем настройки языкового файла
            $module = $request->getParam('moduleRoute');
            if ( isset($values['default']['module']) ) {
              $module = $values['default']['module'];
            }
            //                    $lng = $registry['settings']['language'];
            if ( isset($values['language']) ) {
              $lng = $values['language'];
            } else {
              $defaultLanguage = $this->db()->query()
                ->addSql('select lng_synonym from languages_tbl')
                ->addSql('where lng_default=true')
                ->fetchRow(0, false);

              $lng = $defaultLanguage['lng_synonym'];
            }
            $language_file = $this->getVariable('path_modules') . $module . DIR_SEP . 'languages' . DIR_SEP . $lng . '.xml';

            if ( file_exists($language_file) ) {
              $language = new Config(Config::XML);
              $language->open($language_file, $values['default']['path']);

              $default = $language->get($values['default']['value']);
            }
          } else
            if ( isset($values['default']['type']) && 'config' == $values['default']['type']) {
              //TODO Доделать получение данных с конфига
            } else
              if ( isset($values['default']['type']) && 'function' == $values['default']['type']) {
                $path = Registry::get('path');
                $module_path = $path['modules'];

                include_once($module_path . $values['default']['module'] . DIR_SEP  . 'models' . DIR_SEP . $values['default']['model'] . '.php');
                $class = new $values['default']['model'];
                $default = $class->{$values['default']['function']}($request->getParam('id'));
              } else {
                $idRow = $request->getParam('id');
                $defaultTableName = $values['default']['table'];
                $datasourceValues = $this->_getConfigValues($values['default']['path'] . '/datasources/' . $defaultTableName);

                // Получаем все значения записи
                $sql = 'select *';
                $sql .= ' from ' . $defaultTableName . ' where ' . $datasourceValues['pk'] . '=$1';

                $rowValues = $this->db()->query()
                  ->addSql($sql)
                  ->addParam($idRow)
                  ->fetchRow(0, false);

                $default = $rowValues[$values['default']['value']];
              }
        } else {
          $default = $values['default'];
        }
    }

    $hint = '';
    if ( isset($langValues[$field]['form_hint']) ) {
      $hint = $langValues[$field]['form_hint'];
    }elseif ( isset($langValues[$field]['hint']) ) {
      $hint = $langValues[$field]['hint'];
    }

    $input_type = $values['type'];

    if ( 'list' != $values['type'] && 'bool' != $values['type']
      && 'file' != $values['type'] && 'image' != $values['type'] && 'password' != $values['type']
      && 'textarea' != $values['type'] && 'html' != $values['type']) {
        $values['type'] = 'input';
      }

    $items = array();
    if ( isset($values['list']) ) {
      if ( 'static' == $values['list'] ) {
        foreach ($langValues[$field]['values'] as $pk => $item) {
          $defaultItem = null;
          if ( isset($values['default']) && $pk == $values['default'] ) {
            $defaultItem = 'selected';
          }

          $items[] = array(
            'id' => $pk,
            'caption' => $item,
            'default' => $defaultItem,
          );
        }
      } elseif ( 'function' == $values['list'] ) {
        $defaultItem = null;
        if ( isset($values['default']) ) {
          $defaultItem = $values['default'];
        }

        $path = Registry::get('path');
        $module_path = $path['modules'];
        /** @noinspection PhpIncludeInspection */
        include_once($module_path . $values['module'] . DIR_SEP  . 'models' . DIR_SEP . $values['model'] . '.php');
        $class = new $values['model'];
        $items = $class->{$values['function']}($defaultItem);
      } else {
        if ( isset($values['list']['with_null']) &&
          'true' == $values['list']['with_null'])
        {
          $items[] = array(
            'id' => 'null',
            'caption' => '-',
          );
        }

        if ( !isset($values['list']['calculate_name']) ) {
          $values['list']['calculate_name'] = $values['list']['name'];
        }

        $select = $this->db()->query()
          ->clearSql()
          ->addSql('select ' . $values['list']['pk'] . ', ' . $values['list']['calculate_name'] . ' as ' . $values['list']['name'])
          ->addSql('from ' . $values['list']['table']);

        $where = false;
        $where_sql = '';
        if ( isset($values['list']['where']) ) {
          $where_sql = ' and ' . $values['list']['where'];
        }

        if ( isset($values['list']['fk']) ) {
          $where = true;
          $select->addSql('where ' . $values['list']['fk'] . '=$1' . $where_sql)
            ->addParam($request->get('id'));
        }

        if ( isset($values['list']['where']) ) {
          preg_match_all('/{{(.*?)}}/si', $values['list']['where'], $matches);
          if ( !empty($matches) ) {
            $matches = $matches[1];
            $idItem = $request->getParam('id');
            foreach ($matches as $itemMatch) {
              if ( isset($values['list']['values']) &&
                isset($values['list']['values'][$itemMatch]) ) {
                  $itemId[$itemMatch] = $request->get($values['list']['values'][$itemMatch]);
                } else
                  if ( null !== $request->get($itemMatch) ) {
                    $itemId[$itemMatch] = $request->get($itemMatch);

                  } else {
                    $sqlItem = 'select ' . $itemMatch;
                    $sqlItem .= ' from ' . $tableName . ' where ' . $values['list']['pk'] . '=$1';
                    $itemId = $this->db()->query()
                      ->addSql($sqlItem)
                      ->addParam($idItem)
                      ->fetchRow(0, false);
                  }

              if ( empty($itemId) ) {
                $itemId[$itemMatch] = 'null';
              } else {
                if ( empty($itemId[$itemMatch])) {
                  $itemId[$itemMatch] = 'null';
                }
              }
              $values['list']['where'] = str_replace('{{' . $itemMatch . '}}',
                $itemId[$itemMatch],
                $values['list']['where']);
            }
          }

          if ($where) {
            $select->addSql('and ' . $values['list']['where']);
          } else {
            $select->addSql('where ' . $values['list']['where']);
          }
        }

        if ( isset($values['list']['order']) ) {
          $select->addSql('order by ' . $values['list']['order']);
        }

        $selectResult = $select->fetchResult(false);
        if ( !empty($selectResult) ) {
          foreach ($selectResult as $item) {
            $defaultItem = null;
            if ( isset($values['list']['default']) ) {
              $default_value = $values['list']['default']['value'];
              if ( isset($values['list']['default']['type']) &&
                'get' == $values['list']['default']['type'] ) {
                  $default_value = $this->getRequest()->get($default_value);
                }

              if ( $item[ $values['list']['pk'] ] == $default_value ) {
                $defaultItem = 'selected';
              }
            }

            $items[] = array(
              'id' => $item[ $values['list']['pk'] ],
              'caption' => $item[ $values['list']['name'] ],
              'default' => $defaultItem,
            );
          }
        }
      }
    }

    $func = null;
    if ( isset($values['field']) ) {
      $func = $values['field'];
    }

    $tab = 'main';
    if ( isset($values['tab']) ) {
      $tab = $values['tab'];
    }

    $tmp_field = array(
      'id'            => $id,
      'tab'     => $tab,
      'tabindex'      => $values['tabindex'],
      'field'         => $func,
      'row'           => $row,
      $values['type'] => true,
      'input_type'    => $input_type,
      'caption'       => $caption,
      'description'   => $hint,
      'style'         => $style,
      'label_style'   => $labelStyle,
      'required'      => $required,
      'focused'       => $focused,
      'fromfile'      => $fromfile,
      'filename'      => $filename,
      'code'          => $code,
      'richedit'      => $richedit,
      'richContainerClass' => $richContainerClass,
      'richContainerClassDinamyc' => $richContainerClassDinamyc,
      'richNoStyleFile' => $richNoStyleFile,
      'readonly'      => $readonly,
      'rows'        => $rows,
      'value'     => $default,
      'item'      => $items,
    );

    return $tmp_field;
  }

  public function getForm($operation) {
    $request = $this->getRequest();
    $tableName = $request->get('tableName');

    if ('/' == $operation[0]) {
      $configValues = $this->_getConfigValues($operation);
      $langValues = $this->_getLanguageValues($operation);
    } else {
      list($configValues, $langValues) = $this->_getVariablesTables($tableName);
    }

    $o = explode('/', $operation);
    $c = $this->_getConfigValues();

    if (isset($c['actions'][end($o)]['forward'])) {
      $this->setVariable('forward', true);
    }

    $langValues = $langValues['fields'];

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    $current_language = $this->getRequest()->get('language');
    if ( empty($current_language) ) {
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $current_language = $defaultLanguage['lng_synonym'];
    }

    foreach ($configValues['fields'] as $field => $values) {
      if ( isset($values['languageField']) && 'true' == $values['languageField']) {
        if ( !empty($languages) ) {
          $configValues['fields'][$field] = array();
          foreach ($languages as $lang_values) {
            $values['id'] = $field . '_' . $lang_values['synonym'];
            $values['language'] = $lang_values['synonym'];
            $values['language_hidden'] = true;

            if ($current_language == $lang_values['synonym']) {
              $values['language_hidden'] = false;
            }

            $configValues['fields'][$field]['languageFields'][] = $values;
          }
        }
      }
    }

    $fields = array();
    $i = 1;
    foreach ($configValues['fields'] as $field => $values) {
      if ( isset($values['languageFields']) ) {
        foreach ($values['languageFields'] as $lng_field_values) {
          $tmp_field = $this->getField($operation, $langValues, $field, $lng_field_values, $i);

          if ( empty($tmp_field) ) {
            continue;
          }
          $tmp_field['language'] = $lng_field_values['language'];
          $tmp_field['language_hidden'] = $lng_field_values['language_hidden'];
          $tmp_field['id'] = $lng_field_values['id'];

          $fields[] = $tmp_field;
        }

        $i++;
      } else {
        $tmp_field = $this->getField($operation, $langValues, $field, $values, $i);

        if ( empty($tmp_field) ) {
          continue;
        }

        $fields[] = $tmp_field;
        $i++;
      }
    }

    $fields = $this->_sortFormFields($fields);

    return $fields;
  }

  public function getAddForm() {
    $fields = $this->getForm('add');

    $tabs = array();
    foreach ($fields as $field) {
      $tabs[$field['tab']]['id'] = $field['tab'];
      $tabs[$field['tab']]['field'][] = $field;
    }
    $this->setVariable('tabs', array_values($tabs));

    return $this;
  }

  public function getEditForm() {
    $fields = $this->getForm('edit');
    //var_dump($fields);
    $request = $this->getRequest();
    $tableName = $request->get('tableName');
    $id = $request->getParam('id');

    list($configValues) = $this->_getVariablesTables($tableName);

    if ( !empty($fields) ) {
      $sql = 'select ';
      foreach ($fields as $field) {
        $func = null;
        if ( isset($field['field']) ) {
          $func = $field['field'];
        }
        $sql .= $func . ' ' . $field['id'] . ', ';
      }

      $sql = trim($sql, ', ');
      $sql .= ' from ' . $tableName;
      $joinsSql = null;
      if ( isset($configValues['joins']) ) {
        $joins = $configValues['joins'];

        foreach ($joins as $prefix => $vars) {
          if (empty($vars)) {
            continue;
          }
          $where = null;
          if ( isset($vars['where']) ) {
            $where = 'and ' . $vars['where'];
          }

          $joinsSql .= $vars['type'] . ' join ' . $vars['name'] . ' ' . $prefix . ' on '
            . $vars['pk'] . '='  . $prefix . '.' . $vars['fk'] . ' '
            . $where . chr(13);
        }
      }

      $sql .= ' ' . $joinsSql;
      $sql .= ' where ' . $configValues['pk'] . '=$1';

      //      echo $sql;
      $values = $this->db()->query()
        ->addSql($sql)
        ->addParam($id)
        ->fetchRow(0, false);

      // var_dump($sql);
      //            $languages = $this->db()->query()
      //                ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      //                ->addSql('where lng_enabled=true order by lng_order')
      //                ->fetchResult(false);

      foreach ($fields as $field_id => $field) {
        //        var_dump($field);
        // Если значение хранится в файле
        if ( 'true' == $field['fromfile'] ) {
          preg_match_all('/{{(.*?)}}/si', $field['filename'], $matches);
          if ( !empty($matches) ) {
            $matches = $matches[1];
            foreach ($matches as $itemMatch) {
              $val = 'null';
              if ('row_id' == $itemMatch) {
                $val = $id;
              } else
                if ( null !== $request->get($itemMatch) ) {
                  $val = $request->get($itemMatch);
                } elseif ( null !== $request->post($itemMatch) ) {
                  $val = $request->post($itemMatch);
                }

              $field['filename'] = str_replace('{{'.$itemMatch.'}}', $val, $field['filename']);
            }
          }

          $registry = Registry::getInstance();
          $root = $registry['path']['uploadDir'];

          $filename = $root . $field['filename'];

          //          echo $filename;
          if ( file_exists( $filename ) ) {
            $file = new \Uwin\Fs\File($filename, 'r');
            $fields[$field_id]['value'] = htmlspecialchars($file->read());
            $file->close();
          }

          continue;
        }



        $value = htmlspecialchars($values[$field['id']]);
        if ( isset($field['bool']) ) {
          if ('t' == $value) {
            $value = true;
          }  else {
            $value = false;
          }
        }

        if ( isset($field['list']) ) {
          foreach ($field['item'] as $item_id => $item) {
            if ($value == $item['id']) {
              $fields[$field_id]['item'][$item_id]['default'] = 'selected';
            } else {
              $fields[$field_id]['item'][$item_id]['default'] = false;
            }
          }
        }

        //                var_dump($field['id']);
        //                var_dump($field['input_type']);
        if ( $field['input_type'] == 'date' ) {
          if ( !empty($value) ) {
            $value = date( 'd.m.Y', strtotime($value));
          }
        }

        if ( $field['input_type'] == 'datetime' ) {
          if ( !empty($value) ) {
            $value = date( 'd.m.Y H:i', strtotime($value));
          }
        }

        $fields[$field_id]['value'] = $value;
      }

    }

    $tabs = array();
    foreach ($fields as $field) {
      $tabs[$field['tab']]['id'] = $field['tab'];
      $tabs[$field['tab']]['field'][] = $field;
    }
    $this->setVariable('tabs', array_values($tabs));

    return $this;
  }


  public function getInfoForm() {
    $fields = $this->getForm('info');

    $request = $this->getRequest();
    $tableName = $request->get('tableName');
    $id = $request->getParam('id');

    list($configValues) = $this->_getVariablesTables($tableName);

    if ( !empty($fields) ) {
      $sql = 'select ';
      foreach ($fields as $field) {
        $func = null;
        if ( isset($field['field']) ) {
          $func = $field['field'];
        }
        $sql .= $func . ' ' . $field['id'] . ', ';
      }

      $sql = trim($sql, ', ');
      $sql .= ' from ' . $tableName;

      $joinsSql = null;
      if ( isset($configValues['joins']) ) {
        $joins = $configValues['joins'];

        foreach ($joins as $prefix => $vars) {
          if (empty($vars)) {
            continue;
          }
          $where = null;
          if ( isset($vars['where']) ) {
            $where = 'and ' . $vars['where'];
          }

          $joinsSql .= $vars['type'] . ' join ' . $vars['name'] . ' ' . $prefix . ' on '
            . $vars['pk'] . '='  . $prefix . '.' . $vars['fk'] . ' '
            . $where . chr(13);
        }
      }

      $sql .= ' ' . $joinsSql;

      $sql .= ' where ' . $configValues['pk'] . '=$1';

      $values = $this->db()->query()
        ->addSql($sql)
        ->addParam($id)
        ->fetchRow(0, false);

      foreach ($fields as $field_id => $field) {
        $value = htmlspecialchars($values[$field['id']]);
        if ( isset($field['bool']) ) {
          if ('t' == $value) {
            $value = true;
          }  else {
            $value = false;
          }
        }

        if ( isset($field['list']) ) {
          foreach ($field['item'] as $item_id => $item) {
            if ($value == $item['id']) {
              $fields[$field_id]['item'][$item_id]['default'] = 'selected';
            } else {
              $fields[$field_id]['item'][$item_id]['default'] = false;
            }
          }
        }

        if ( isset($configValues['fields'][$field['id']]) ) {
          if ( $configValues['fields'][$field['id']]['type'] == 'date' ) {
            if ( !empty($value) ) {
              $value = date( 'd.m.Y', strtotime($value));
            }
          }

          if ( $configValues['fields'][$field['id']]['type'] == 'datetime' ) {
            if ( !empty($value) ) {
              $value = date( 'd.m.Y H:i', strtotime($value));
            }
          }
        }

        $fields[$field_id]['value'] = $value;
      }

    }

    $tabs = array();
    foreach ($fields as $field) {
      $tabs[$field['tab']]['id'] = $field['tab'];
      $tabs[$field['tab']]['field'][] = $field;
    }
    $this->setVariable('tabs', array_values($tabs));

    return $this;
  }


  private function _validateField($preprocessing, $rules, $values, $files, $field, $pk, $id, $field_values, $langValues, $language = null) {
    $result = array();
    $validator = new Validator;

    $lang_field = $field;

    if ( !empty($language) ) {
      $lang_field = $field . '_' . $language;
    }

    foreach ($preprocessing as $function => $preprocessing_values) {
      switch ($function) {
      case 'trim':
        $values[$lang_field] = trim($values[$lang_field]);
        break;

      case 'lowercase':
        $values[$lang_field] = mb_strtolower($values[$lang_field]);
        break;

      case 'uppercase':
        $values[$lang_field] = mb_strtoupper($values[$lang_field]);
        break;
      }
    }

    foreach ($rules as $rule => $rule_values) {
      $error = false;

      switch ($rule) {
      case 'empty':
        if ('file' != $field_values['type'] && 'image' != $field_values['type']) {
          $v = $values[$lang_field];
        } else {
          $v = $files[$lang_field]['name'];
        }

        if ( !(!empty($id) && ('file' == $field_values['type'] || 'image' == $field_values['type']))) {
          if ( $validator->isEmpty($v) ) {
            $error = true;
          }
        }
        break;

      case 'unique':
        if ( $validator->isExistsInDb($values[$lang_field],
          $this->db()->query(),
          $rule_values['table'],
          $rule_values['column'],
          $pk, $id) ) {
            $error = true;
          }
        break;

      case 'regexp':
        if ( !empty($values[$lang_field]) && !$validator->equalRegexp($values[$lang_field],
          $rule_values) ) {
            $error = true;
          }
        break;

      case 'parseInt':
        if ( !$validator->parseInt($values[$lang_field]) ) {
          $error = true;
        }
        break;

      case 'parseUrl':
        if ( !$validator->parseUrl($values[$lang_field]) ) {
          $error = true;
        }
        break;

      case 'parseFloat':
        if ( !$validator->parseFloat($values[$lang_field]) ) {
          $error = true;
        }
        break;

      case 'parseDate':
        if ( !$validator->parseDate($values[$lang_field]) ) {
          $error = true;
        }
        break;

      case 'parseTime':
        if ( !$validator->parseTime($values[$lang_field]) ) {
          $error = true;
        }
        break;

      case 'parseDateTime':
        if ( !$validator->parseDateTime($values[$lang_field]) ) {
          $error = true;
        }
        break;

      case 'email':
        if ( !$validator->isEmail($values[$lang_field]) ) {
          $error = true;
        }
        break;

      case 'extension':
        if ( !empty($files[$lang_field]['name']) ) {
          $path_info = pathinfo($files[$lang_field]['name']);
          $ext = mb_strtolower($path_info['extension']);
          if ( !$validator->fileExtension($ext, explode('|', $rule_values)) ) {
            $error = true;
          }
        }
        break;

      case 'maxSize':
        if ( !empty($files[$lang_field]['name']) ) {
          $size = $files[$lang_field]['size'] /1024;
          if ((float)$size > (float)$rule_values) {
            $error = true;
          }
        }
        break;

      case 'width':
        if ( !empty($files[$lang_field]['name']) ) {
          if ( !$validator->imageWidth($files[$lang_field]['tmp_name'], $rule_values) ) {
            $error = true;
          }
        }
        break;

      case 'height':
        if ( !empty($files[$lang_field]['name']) ) {
          if ( !$validator->imageHeight($files[$lang_field]['tmp_name'], $rule_values) ) {
            $error = true;
          }
        }
        break;

      case 'maxWidth':
        if ( !empty($files[$lang_field]['name']) ) {
          if ( !$validator->imageMaxWidth($files[$lang_field]['tmp_name'], $rule_values) ) {
            $error = true;
          }
        }
        break;

      case 'maxHeight':
        if ( !empty($files[$lang_field]['name']) ) {
          if ( !$validator->imageMaxHeight($files[$lang_field]['tmp_name'], $rule_values) ) {
            $error = true;
          }
        }
        break;
      }

      if (false !== $error) {
        $result = array(
          'id'       => $lang_field,
          'text'     => $langValues[$field]['validate'][$rule],
          'language' => $language,
        );

        break;
      }
    }

    return $result;
  }

  private function _validate($tableName, $id = null) {
    $request = $this->getRequest();
    $values = $request->post();
    $files = $request->files();

    if ('/' == $tableName[0]) {
      $configValues = $this->_getConfigValues($tableName);
      $langValues = $this->_getLanguageValues($tableName);

      $pk = null;
      $configValues = $configValues['fields'];
      $langValues   = $langValues['fields'];
    } else {
      list($configValues, $langValues) = $this->_getVariablesTables($tableName);

      $pk = $configValues['pk'];
      $configValues = $configValues['fields'];
      $langValues   = $langValues['fields'];
    }

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    $result = array();
    foreach ($configValues as $field => $field_values) {
      $preprocessing = $rules = array();
      if ( isset($field_values['preprocessing']) ) {
        $preprocessing = $field_values['preprocessing'];
      }
      if ( isset($field_values['validate']) ) {
        $rules = $field_values['validate'];
      }

      if ( isset($field_values['languageField']) && 'true' == $field_values['languageField']) {
        if ( !empty($languages) ) {
          foreach ($languages as $language) {
            if ( isset($values['form-lang-' . $language['synonym']]) &&
              'true' == $values['form-lang-' . $language['synonym']] )
            {
              $error = $this->_validateField($preprocessing, $rules, $values, $files, $field, $pk, $id, $field_values, $langValues, $language['synonym']);

              if ( !empty($error) ) {
                $result['errors'][] = $error;
              }
            }
          }
        }
      } else {
        $error = $this->_validateField($preprocessing, $rules, $values, $files, $field, $pk, $id, $field_values, $langValues);

        if ( !empty($error) ) {
          $result['errors'][] = $error;
        }
      }
    }

    if ( empty($result) ) {
      $result = true;
    }

    return $result;
  }

  private function _deleteRow($table, $id) {
    list($configValues) = $this->_getVariablesTables($table);

    if ( isset($configValues['actions_function']) &&
      isset($configValues['actions_function']['delete']) )
    {
      $function_parts = $configValues['actions_function']['delete'];
      $path = Registry::get('path');
      $module_path = $path['modules'];
      /** @noinspection PhpIncludeInspection */
      include_once($module_path . $function_parts['module'] . DIR_SEP  . 'models' . DIR_SEP . $function_parts['model'] . '.php');
      $class = new $function_parts['model'];
      $class->{$function_parts['function']}($id);

      return true;
    }

    if ( isset($configValues['actions']['delete']['callback']) ) {
      $callback = $configValues['actions']['delete']['callback'];

      $path = Registry::get('path');
      $module_path = $path['modules'];
      /** @noinspection PhpIncludeInspection */
      include_once($module_path . $callback['module'] . DIR_SEP  . 'models' . DIR_SEP . $callback['model'] . '.php');
      $class = new $callback['model'];
      $class->{$callback['function']}($id);
    }

    $query = $this->db()->query()
      ->addSql('delete from ' . $table . ' where ' . $configValues['pk']
      . ' = $1;');

    if ( isset($configValues['actions']['delete']['relatives_tables']) ) {
      $relative_tables = $configValues['actions']['delete']['relatives_tables'];
      foreach ($relative_tables as $relative_table => $variables) {
        $query->addSql('delete from ' . $relative_table . ' where '
          . $variables['fk'] . ' = $1;');
      }
    }

    $query
      ->addParam($id)
      ->execute();

    return $this;
  }

  private function _addRow($tableName) {
    $request = $this->getRequest();
    $values = $request->post();
    $files = $request->files();

    if ( true !== $validate = $this->_validate($tableName) ) {
      return $validate;
    }

    // Если есть функции которые вычисляют поле, выполняем их
    list($configValues) = $this->_getVariablesTables($tableName);

    if ( isset($configValues['actions_function']) &&
      isset($configValues['actions_function']['add']) )
    {
      $function_parts = $configValues['actions_function']['add'];
      $path = Registry::get('path');
      $module_path = $path['modules'];
      /** @noinspection PhpIncludeInspection */
      include_once($module_path . $function_parts['module'] . DIR_SEP  . 'models' . DIR_SEP . $function_parts['model'] . '.php');
      $class = new $function_parts['model'];
      $class->{$function_parts['function']}();

      return true;
    }

    if ( isset($configValues['actions']['add']['callback']) ) {
      $callback = $configValues['actions']['add']['callback'];

      $path = Registry::get('path');
      $module_path = $path['modules'];
      /** @noinspection PhpIncludeInspection */
      include_once($module_path . $callback['module'] . DIR_SEP  . 'models' . DIR_SEP . $callback['model'] . '.php');
      $class = new $callback['model'];
      $_tmp_values = $values;
      $class->{$callback['function']}($_tmp_values, $files);
    }

    // Получаю параметры
    if ( isset($configValues['params']) ) {
      foreach($configValues['params'] as $param => $param_values) {
        if ( isset($configValues['fields'][$param]) &&
          isset($configValues['fields'][$param]['noTable']) &&
          'true' != $configValues['fields'][$param]['noTable']) {
            continue;
          }

        if ( isset($param_values['disabled']) && 'true' == $param_values['disabled'] ) {
          continue;
        }

        $configValues['fields'][$param] = true;
        if ('get' == $param_values['type']) {
          $values[$param] = $request->get($param_values['value']);
        }

        if ( isset($param_values['default']) && empty($values[$param]) ) {
          $values[$param] = $param_values['default'];
        }
      }
    }

    // Формирую INSERT
    $query = $this->db()->query()
      ->addSql('insert into ' . $tableName . '(');

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    $sql = '';
    foreach($configValues['fields'] as $field => $field_values) {
      if ( !isset($field_values['noTable']) || 'true' != $field_values['noTable']) {

        if ( !(isset($field_values['languageField']) && 'true' == $field_values['languageField']) ) {
          $sql .= $field . ',';
        } else {
          if ( !empty($languages) ) {
            foreach ($languages as $language) {
              $sql .= $field . '_' . $language['synonym'] . ',';
            }
          }
        }
      }
    }

    if ( isset($configValues['listLanguagesField']) ) {
      $sql .= $configValues['listLanguagesField'] . ',';
    }

    $sql = trim($sql, ',');

    $query->addSql($sql . ')values(');

    $sql = '';
    $i = 1;
    $list_languages = null;
    foreach($configValues['fields'] as $field => $field_values) {
      if ( !isset($field_values['noTable']) || 'true' != $field_values['noTable']) {
        if ( !(isset($field_values['languageField']) && 'true' == $field_values['languageField']) ) {
          $sql .= '$' . $i .  ',';

          if ( !isset($field_values['callback']) ) {
            if ( !isset($values[$field]) ) {
              if ('bool' == $field_values['type']) {
                $values[$field] = 'false';
              } else {
                $values[$field] = 'null';
              }
            }

            if ('date' == $field_values['type'] && null != $values[$field]) {
              $values[$field] = date( 'Y/m/d', strtotime($values[$field]) );
            }

            if ('datetime' == $field_values['type'] && null != $values[$field]) {
              $values[$field] = date( 'Y/m/d H:i', strtotime($values[$field]) );
            }
          } else {
            // Если есть функция, которая вычисляет значение поля, выполняем ее
            $function_parts = $field_values['callback'];
            $path = Registry::get('path');
            $module_path = $path['modules'];
            /** @noinspection PhpIncludeInspection */
            include_once($module_path . $function_parts['module'] . DIR_SEP  . 'models' . DIR_SEP . $function_parts['model'] . '.php');
            $class = new $function_parts['model'];
            $params = array();
            if ( isset($function_parts['params']) ) {
              foreach($function_parts['params'] as $name => $field_name) {
                if ( !isset($values[$field_name]) ) {
                  $values[$field_name] = null;
                }
                $params[$name] = $values[$field_name];
              }
            }
            if ( !isset($values[$field]) ) {
              $values[$field] = null;
            }
            $values[$field] = $class->{$function_parts['function']}($values[$field], $params);
          }

          $query->addParam($values[$field]);

          $i++;
        } else {
          if ( !empty($languages) ) {
            $list_languages = '';
            foreach ($languages as $language) {
              if ( isset($values['form-lang-' . $language['synonym']]) &&
                'true' == $values['form-lang-' . $language['synonym']] )
              {
                $list_languages .= $language['synonym'] . '|';
              }

              $sql .= '$' . $i .  ',';

              $lang_field = $field . '_' . $language['synonym'];
              if ( !isset($field_values['callback']) ) {
                if ( !isset($values[$lang_field]) ) {
                  if ('bool' == $field_values['type']) {
                    $values[$field] = 'false';
                  } else {
                    $values[$field] = 'null';
                  }
                }

                if ('date' == $field_values['type'] && null != $values[$lang_field]) {
                  $values[$lang_field] = date( 'Y/m/d', strtotime($values[$lang_field]) );
                }

                if ('datetime' == $field_values['type'] && null != $values[$lang_field]) {
                  $values[$lang_field] = date( 'Y/m/d H:i', strtotime($values[$lang_field]) );
                }
              } else {
                // Если есть функция, которая вычисляет значение поля, выполняем ее
                $function_parts = $field_values['callback'];
                $path = Registry::get('path');
                $module_path = $path['modules'];
                /** @noinspection PhpIncludeInspection */
                include_once($module_path . $function_parts['module'] . DIR_SEP  . 'models' . DIR_SEP . $function_parts['model'] . '.php');
                $class = new $function_parts['model'];
                $params = array();
                if ( isset($function_parts['params']) ) {
                  foreach($function_parts['params'] as $name => $field_name) {
                    if ( !isset($values[$field_name]) ) {
                      $values[$field_name] = null;
                    }
                    $params[$name] = $values[$field_name];
                  }
                }
                $values[$lang_field] = $class->{$function_parts['function']}($values[$lang_field], $params);
              }

              if ( isset($values[$lang_field]) ) {
                $query->addParam($values[$lang_field]);
              } else {
                $query->addParam(null);
              }

              $i++;
            }
          }
        }
      }
    }

    if ( isset($configValues['listLanguagesField']) ) {
      $sql .= '$' . $i .  ',';
      $list_languages = '|' . trim($list_languages, '|') . '|';
      $query->addParam($list_languages);
    }

    $sql = trim($sql, ',');
    $pk = $query->addSql($sql . ')')
      ->execute($configValues['pk']);



    // Проходимся по всем полям, смотрим, нет ли такого, которое хранится в файле
    foreach($configValues['fields'] as $field => $field_values) {
      if ( isset($field_values['fromfile']) && 'true' == $field_values['fromfile'] ) {
        preg_match_all('/{{(.*?)}}/si', $field_values['filename'], $matches);
        if ( !empty($matches) ) {
          $matches = $matches[1];
          foreach ($matches as $itemMatch) {
            $val = 'null';
            if ('row_id' == $itemMatch) {
              $val = $pk;
            } else
              if ( null !== $request->get($itemMatch) ) {
                $val = $request->get($itemMatch);
              } elseif ( null !== $request->post($itemMatch) ) {
                $val = $request->post($itemMatch);
              }

            $field_values['filename'] = str_replace('{{'.$itemMatch.'}}', $val, $field_values['filename']);
          }
        }

        $registry = Registry::getInstance();
        $root = $registry['path']['uploadDir'];

        $filename = $root . $field_values['filename'];

        if ( !file_exists( dirname($filename) ) ) {
          mkdir(dirname($filename), 0777, true);
        }

        if ( !empty($values[$field]) ) {
          $file = new \Uwin\Fs\File($filename, 'w+');
          $file->write($values[$field]);
          $file->close();
        } else {
          if ( file_exists( $filename) ) {
            unlink($filename);
          }
        }
      }
    }

    // Если есть файлы которые нужно загрузить, загружаю их
    if ( !empty($files) ) {
      foreach ($files as $fileId => $file) {
        if ( empty($file['name']) ) {
          continue;
        }
        $registry = Registry::getInstance();
        $root = $registry['path']['uploadDir'];

        $path_info = pathinfo($file['name']);
        $ext = mb_strtolower($path_info['extension']);

        // загружаю файл
        $fileName = 'tmp';
        $lang_file = null;
        if ( !isset($configValues['fields'][$fileId]) ) {
          $lang_file = substr($fileId, -3);
          $fileId = substr($fileId, 0, -3);
        }
        if ( isset($configValues['fields'][$fileId]['fileName']['function']) &&
          'web-translit' == $configValues['fields'][$fileId]['fileName']['function'] ) {
            $ling = new Linguistics;
            $fileName = $ling->getWebTranslit($values[$configValues['fields'][$fileId]['fileName']['value']]);
          } else {
            $fileName = time();
          }

        if ( mb_strlen($fileName) >= 128 ) {
          $fileName = mb_substr($fileName, 0, 127);
        }

        if ( isset($configValues['fields'][$fileId]['fileName']['sufix']) ) {
          $fileName .= $configValues['fields'][$fileId]['fileName']['sufix'];
        }

        preg_match_all('/{{(.*?)}}/si', $configValues['fields'][$fileId]['destination'], $matches);
        if ( !empty($matches) ) {
          $matches = $matches[1];
          foreach ($matches as $itemMatch) {
            $val = 'null';
            if ('row_id' == $itemMatch) {
              $val = $pk;
            } else
              if ( null !== $request->get($itemMatch) ) {
                $val = $request->get($itemMatch);
              } elseif ( null !== $request->post($itemMatch) ) {
                $val = $request->post($itemMatch);
              }

            $configValues['fields'][$fileId]['destination'] = str_replace('{{'.$itemMatch.'}}', $val, $configValues['fields'][$fileId]['destination']);
          }
        }

        $destination = $root . $configValues['fields'][$fileId]['destination'];
        if ( !file_exists($destination) ) {
          mkdir($destination, 0777, true);
        }

        $retina = false;
        if ( isset($configValues['fields'][$fileId]['retina']) && $configValues['fields'][$fileId]['retina'] == 'true') {
          $retina = true;
        }
        if (!$retina) {
          $pathFile = $destination . $fileName . '.' . $ext;
          move_uploaded_file($file['tmp_name'],$pathFile);

          $registry = Registry::getInstance();
          $rpath = $registry['path']['root'];
          if('jpg' == $ext) {
          exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
            . $pathFile . ' ' . $pathFile);
          }

        } else {
          $pathFile = $destination . $fileName . '@2x.' . $ext;
          move_uploaded_file($file['tmp_name'],$pathFile);
          $registry = Registry::getInstance();
          $rpath = $registry['path']['root'];
          if('jpg' == $ext) {
          exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
            . $pathFile . ' ' . $pathFile);
          }

          $im = new Imagick($pathFile);
          $newsize = $im->getImageGeometry();
          $width = $newsize['width'] / 2;
          $height = $newsize['height'] / 2;
          $im->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1, TRUE);
          $pathFile = $destination . $fileName . '.' . $ext;
          $im->writeImage($pathFile);
          $registry = Registry::getInstance();
          $rpath = $registry['path']['root'];
          if('jpg' == $ext) {
          exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
            . $pathFile . ' ' . $pathFile);
          }
        }
        // делаю миниатюру
        $this->createThumbnails('uploads/' . $configValues['fields'][$fileId]['destination']
          . $fileName . '.' . $ext, true, false, $retina);

        // Создаю изображения с измененными размерами если нужно
        if ( isset($configValues['fields'][$fileId]['resizes']) ) {
          foreach ($configValues['fields'][$fileId]['resizes'] as $resize) {
            if ( isset($resize['fileName']['function']) &&
              'web-translit' == $resize['fileName']['function'] ) {
                $ling = new Linguistics;
                $rsFileName = $ling->getWebTranslit($values[$resize['fileName']['value']]);
              } else {
                $rsFileName = time();
              }

            if ( mb_strlen($rsFileName) >= 128 ) {
              $rsFileName = mb_substr($rsFileName, 0, 127);
            }

            if ( isset($resize['fileName']['sufix']) ) {
              $rsFileName .= $resize['fileName']['sufix'];
            }

            preg_match_all('/{{(.*?)}}/si', $resize['destination'], $matches);
            if ( !empty($matches) ) {
              $matches = $matches[1];
              foreach ($matches as $itemMatch) {
                $val = 'null';
                if ('row_id' == $itemMatch) {
                  $val = $pk;
                } else
                  if ( null !== $request->get($itemMatch) ) {
                    $val = $request->get($itemMatch);
                  } elseif ( null !== $request->post($itemMatch) ) {
                    $val = $request->post($itemMatch);
                  }

                $resize['destination'] = str_replace('{{'.$itemMatch.'}}', $val, $resize['destination']);
              }
            }

            $destination = $root . $resize['destination'];
            if ( !file_exists($destination) ) {
              mkdir($destination, 0777, true);
            }

            $rsPathFile = $destination . $rsFileName . '.' . $ext;

            if (!$retina) {
              $rsPathFile = $destination . $rsFileName . '.' . $ext;
              $this->_imgResize($pathFile, $rsPathFile, $resize['width'], $resize['height']);
            } else {
              $rsPathFile = $destination . $rsFileName . '@2x.' . $ext;
              $pathFile2x = pathinfo($pathFile)['dirname'] . DIR_SEP . pathinfo($pathFile)['filename'] . '@2x.' . pathinfo($pathFile)['extension'];
              $this->_imgResize($pathFile2x, $rsPathFile, $resize['width'], $resize['height']);

              $im = new Imagick($rsPathFile);
              $newsize = $im->getImageGeometry();
              $width = $newsize['width'] / 2;
              $height = $newsize['height'] / 2;
              $im->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1, TRUE);
              $rsPathFile = $destination . $rsFileName . '.' . $ext;
              $im->writeImage($rsPathFile);
              $registry = Registry::getInstance();
              $rpath = $registry['path']['root'];
              if('jpg' == $ext) {
              exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
                . $rsPathFile . ' ' . $rsPathFile);
              }
            }
          }
        }

        // получаю имя файла
        $values[$fileId] = '/uploads/' . $configValues['fields'][$fileId]['destination']
          . $fileName . '.' . $ext;

        $field_values = $configValues['fields'][$fileId];

        if ('image' == $field_values['type'] ) {
          $retina = false;
          if ( isset($field_values['retina']) && $field_values['retina'] == 'true') {
            $retina = true;
          }

          $registry = Registry::getInstance();
          $root_path = rtrim($registry['path']['static_server'], '/');
          $pathFile = $root_path . $values[$fileId];

          if (!$retina) {
            $im = new Imagick($pathFile);
            $size = $im->getImageGeometry();
            $width = $size['width'];
            $height = $size['height'];

            $pathinfo = pathinfo($pathFile);
            $thm = $pathinfo['dirname'] . DIR_SEP . $pathinfo['filename'] . '-thm.' . $pathinfo['extension'];
            $imThm = new Imagick($thm);
            $sizeThm = $imThm->getImageGeometry();
            $widthThm = $sizeThm['width'];
            $heightThm = $sizeThm['height'];

            $pathinfoRel = pathinfo($values[$fileId]);
            $thmRel = $pathinfoRel['dirname'] . DIR_SEP . $pathinfoRel['filename'] . '-thm.' . $pathinfoRel['extension'];

            $imageArray = [
              'original' => [
                'path' => $values[$fileId],
                'width' => $width,
                'height' => $height,
              ],

              'thm' => [
                'path' => $thmRel,
                'width' => $widthThm,
                'height' => $heightThm,
              ],
            ];
          } else {
            $im = new Imagick($pathFile);
            $size = $im->getImageGeometry();
            $width = $size['width'] * 2;
            $height = $size['height'] * 2;

            $pathinfo = pathinfo($pathFile);
            $thm = $pathinfo['dirname'] . DIR_SEP . $pathinfo['filename'] . '-thm.' . $pathinfo['extension'];
            $imThm = new Imagick($thm);
            $sizeThm = $imThm->getImageGeometry();
            $widthThm = $sizeThm['width'] * 2;
            $heightThm = $sizeThm['height'] * 2;

            $pathinfoRel = pathinfo($values[$fileId]);
            $thmRel = $pathinfoRel['dirname'] . DIR_SEP . $pathinfoRel['filename'] . '-thm.' . $pathinfoRel['extension'];

            $imageArray = [
              'original' => [
                'path' => $values[$fileId],
                'width' => $width,
                'height' => $height,
              ],

              'thm' => [
                'path' => $thmRel,
                'width' => $widthThm,
                'height' => $heightThm,
              ],
            ];
          }
        }

        if (isset($field_values['resizes'])) {
          foreach ($field_values['resizes'] as $resize_key => $resize) {
            if ( isset($resize['fileName']['function']) &&
              'web-translit' == $resize['fileName']['function'] ) {
                $ling = new Linguistics;
                $rsFileName = $ling->getWebTranslit($values[$resize['fileName']['value']]);
              } else {
                $rsFileName = time();
              }

            if ( mb_strlen($rsFileName) >= 128 ) {
              $rsFileName = mb_substr($rsFileName, 0, 127);
            }

            if ( isset($resize['fileName']['sufix']) ) {
              $rsFileName .= $resize['fileName']['sufix'];
            }

            preg_match_all('/{{(.*?)}}/si', $resize['destination'], $matches);
            if ( !empty($matches) ) {
              $matches = $matches[1];
              foreach ($matches as $itemMatch) {
                $val = 'null';
                if ('row_id' == $itemMatch) {
                  $val = $pk;
                } else
                  if ( null !== $request->get($itemMatch) ) {
                    $val = $request->get($itemMatch);
                  } elseif ( null !== $request->post($itemMatch) ) {
                    $val = $request->post($itemMatch);
                  }

                $resize['destination'] = str_replace('{{'.$itemMatch.'}}', $val, $resize['destination']);
              }
            }

            $rsPathFile = $destination . $rsFileName . '.' . $ext;
            $rsPathFile2x = $destination . $rsFileName . '@2x.' . $ext;
            if (!file_exists($rsPathFile2x)) {
              $im = new Imagick($rsPathFile);
            } else {
              $im = new Imagick($rsPathFile2x);
            }
            $size = $im->getImageGeometry();
            $width = $size['width'];
            $height = $size['height'];

            $registry = Registry::getInstance();
            $root_path = rtrim($registry['path']['static_server'], '/');
            $rsPathFile = str_replace($root_path, '', $rsPathFile);
            $imageArray[$resize_key] = [
              'path' => $rsPathFile,
              'width' => $width,
              'height' => $height,
            ];
          }
        }

        if ('image' == $field_values['type'] ) {
          $imageJson = json_encode($imageArray, JSON_UNESCAPED_SLASHES);
        }
        if ('file' == $field_values['type'] ) {
          $imageJson = $values[$fileId];
        }
        $values[$fileId] = $imageJson;

        $this->db()->query()
          ->addSql('update ' . $tableName . ' set ' . $fileId . $lang_file . '=$2')
          ->addSql('where ' . $configValues['pk'] . '=$1')
          ->addParam($pk)
          ->addParam($values[$fileId])
          ->execute();
      }
    }

    return true;
  }

  private function _editRow($tableName, $id) {
    $request = $this->getRequest();
    $values = $request->post();
    $files = $request->files();

    if ( true !== $validate = $this->_validate($tableName, $id) ) {
      return $validate;
    }

    list($configValues) = $this->_getVariablesTables($tableName);

    if ( isset($configValues['actions_function']) &&
      isset($configValues['actions_function']['edit']) )
    {
      $function_parts = $configValues['actions_function']['edit'];
      $path = Registry::get('path');
      $module_path = $path['modules'];
      /** @noinspection PhpIncludeInspection */
      include_once($module_path . $function_parts['module'] . DIR_SEP  . 'models' . DIR_SEP . $function_parts['model'] . '.php');
      $class = new $function_parts['model'];
      $class->{$function_parts['function']}();

      return true;
    }

    if ( isset($configValues['actions']['edit']['callback']) ) {
      $callback = $configValues['actions']['edit']['callback'];

      $path = Registry::get('path');
      $module_path = $path['modules'];
      /** @noinspection PhpIncludeInspection */
      include_once($module_path . $callback['module'] . DIR_SEP  . 'models' . DIR_SEP . $callback['model'] . '.php');
      $class = new $callback['model'];
      $_tmp_values = $values;
      $_tmp_values['id'] = $id;
      $class->{$callback['function']}($_tmp_values, $files);
    }

    // Если есть файлы которые нужно загрузить, загружаю их
    if ( !empty($files) ) {
      foreach ($files as $fileId => $file) {
        if ( empty($file['name']) ) {
          continue;
        }
        $registry = Registry::getInstance();
        $root = $registry['path']['uploadDir'];

        $path_info = pathinfo($file['name']);
        $ext = mb_strtolower($path_info['extension']);

        // загружаю файл
        $fileName = 'tmp';
        $lang_file = null;
        if ( !isset($configValues['fields'][$fileId]) ) {
          $lang_file = substr($fileId, -3);
          $fileId = substr($fileId, 0, -3);
        }
        if ( isset($configValues['fields'][$fileId]['fileName']['function']) &&
          'web-translit' == $configValues['fields'][$fileId]['fileName']['function'] ) {
            $ling = new Linguistics;
            $fileName = $ling->getWebTranslit($values[$configValues['fields'][$fileId]['fileName']['value']]);
          } else {
            $fileName = time();
          }

        if ( mb_strlen($fileName) >= 128 ) {
          $fileName = mb_substr($fileName, 0, 127);
        }

        $clearFileName = $fileName;

        if ( isset($configValues['fields'][$fileId]['fileName']['sufix']) ) {
          $fileName .= $configValues['fields'][$fileId]['fileName']['sufix'];
        }

        //TODO Сделать проверку существует или нет путь к файлу
        preg_match_all('/{{(.*?)}}/si', $configValues['fields'][$fileId]['destination'], $matches);
        if ( !empty($matches) ) {
          $matches = $matches[1];
          foreach ($matches as $itemMatch) {
            $val = 'null';
            if ('row_id' == $itemMatch) {
              $val = $id;
            } else
              if ( null !== $request->get($itemMatch) ) {
                $val = $request->get($itemMatch);
              } elseif ( null !== $request->post($itemMatch) ) {
                $val = $request->post($itemMatch);
              }

            $configValues['fields'][$fileId]['destination'] = str_replace('{{'.$itemMatch.'}}', $val, $configValues['fields'][$fileId]['destination']);
          }
        }

        $destination = $root . $configValues['fields'][$fileId]['destination'];
        if ( !file_exists($destination) ) {
          mkdir($destination, 0777, true);
        }

        $num_version = '1';
        $g_num_version = '';
        while( file_exists($destination . $fileName . '.' . $ext) ) {
          preg_match('#-v([0-9]+)\.#si', $fileName . '.', $version);
          if ( isset($version[1]) ) {
            $g_num_version = $num_version = (int)$version[1]+1;
            $fileName = str_replace('-v' . ($num_version-1) . '.',
              '-v' . $num_version . '.',
              $fileName . '.');
            $fileName = rtrim($fileName, '.');
          } else {
            $g_num_version = $num_version;
            $fileName .= '-v' . $num_version;
          }
        }

        $retina = false;
        if ( isset($configValues['fields'][$fileId]['retina']) && $configValues['fields'][$fileId]['retina'] == 'true') {
          $retina = true;
        }
        if (!$retina) {
          $pathFile = $destination . $fileName . '.' . $ext;
          move_uploaded_file($file['tmp_name'],$pathFile);
          $registry = Registry::getInstance();
          $rpath = $registry['path']['root'];
          if('jpg' == $ext) {
          exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
            . $pathFile . ' ' . $pathFile);
          }
        } else {
          $pathFile = $destination . $fileName . '@2x.' . $ext;
          move_uploaded_file($file['tmp_name'],$pathFile);
          $registry = Registry::getInstance();
          $rpath = $registry['path']['root'];
          if('jpg' == $ext) {
          exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
            . $pathFile . ' ' . $pathFile);
          }

          $im = new Imagick($pathFile);
          $newsize = $im->getImageGeometry();
          $width = $newsize['width'] / 2;
          $height = $newsize['height'] / 2;
          $im->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1, TRUE);
          $pathFile = $destination . $fileName . '.' . $ext;
          $im->writeImage($pathFile);
          $registry = Registry::getInstance();
          $rpath = $registry['path']['root'];
          if('jpg' == $ext) {
          exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
            . $pathFile . ' ' . $pathFile);
          }
        }
        // делаю миниатюру
        $this->createThumbnails('uploads/' . $configValues['fields'][$fileId]['destination']
          . $fileName . '.' . $ext, true, false, $retina);


        // Создаю изображения с измененными размерами если нужно
        if ( isset($configValues['fields'][$fileId]['resizes']) ) {
          foreach ($configValues['fields'][$fileId]['resizes'] as $resize) {
            $rsFileName = $clearFileName;

            if ( isset($resize['fileName']['sufix']) ) {
              $rsFileName .= $resize['fileName']['sufix'];
            }
            if ( !empty($g_num_version) ) {
              $rsFileName .= '-v' . $g_num_version;
            }

            preg_match_all('/{{(.*?)}}/si', $resize['destination'], $matches);
            if ( !empty($matches) ) {
              $matches = $matches[1];
              foreach ($matches as $itemMatch) {
                $val = 'null';
                if ('row_id' == $itemMatch) {
                  $val = $id;
                } else
                  if ( null !== $request->get($itemMatch) ) {
                    $val = $request->get($itemMatch);
                  } elseif ( null !== $request->post($itemMatch) ) {
                    $val = $request->post($itemMatch);
                  }

                $resize['destination'] = str_replace('{{'.$itemMatch.'}}', $val, $resize['destination']);
              }
            }

            $destination = $root . $resize['destination'];
            if ( !file_exists($destination) ) {
              mkdir($destination, 0777, true);
            }

            if (!$retina) {
              $rsPathFile = $destination . $rsFileName . '.' . $ext;
              $this->_imgResize($pathFile, $rsPathFile, $resize['width'], $resize['height']);
            } else {
              $rsPathFile = $destination . $rsFileName . '@2x.' . $ext;
              $pathFile2x = pathinfo($pathFile)['dirname'] . DIR_SEP . pathinfo($pathFile)['filename'] . '@2x.' . pathinfo($pathFile)['extension'];
              $this->_imgResize($pathFile2x, $rsPathFile, $resize['width'], $resize['height']);

              $im = new Imagick($rsPathFile);
              $newsize = $im->getImageGeometry();
              $width = $newsize['width'] / 2;
              $height = $newsize['height'] / 2;
              $im->resizeImage($width, $height, imagick::FILTER_LANCZOS, 1, TRUE);
              $rsPathFile = $destination . $rsFileName . '.' . $ext;
              $im->writeImage($rsPathFile);
              $registry = Registry::getInstance();
              $rpath = $registry['path']['root'];
              if('jpg' == $ext) {
              exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
                . $rsPathFile . ' ' . $rsPathFile);
              }
            }
          }
        }

        // получаю имя файла
        $values[$fileId . $lang_file] = '/uploads/' . $configValues['fields'][$fileId]['destination']
          . $fileName . '.' . $ext;
      }
    }

    // Получаю параметры
    if ( isset($configValues['params']) ) {
      foreach($configValues['params'] as $param => $param_values) {
        if ( isset($configValues['fields'][$param]) &&
          isset($configValues['fields'][$param]['noTable']) &&
          'true' != $configValues['fields'][$param]['noTable']) {
            continue;
          }

        if ( isset($param_values['disabled']) && 'true' == $param_values['disabled'] ) {
          continue;
        }

        $configValues['fields'][$param] = true;
        if ('get' == $param_values['type']) {
          $values[$param] = $request->get($param_values['value']);
        }
      }
    }

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    // Формирую UPDATE
    $query = $this->db()->query()
      ->addSql('update ' . $tableName . ' set ')
      ->addParam($id);

    $sql = '';
    $i = 2;
    $list_languages = null;
    foreach($configValues['fields'] as $field => $field_values) {
      // Если значение поля хранится в файле
      if ( isset($field_values['fromfile']) && 'true' == $field_values['fromfile'] ) {
        preg_match_all('/{{(.*?)}}/si', $field_values['filename'], $matches);
        if ( !empty($matches) ) {
          $matches = $matches[1];
          foreach ($matches as $itemMatch) {
            $val = 'null';
            if ('row_id' == $itemMatch) {
              $val = $id;
            } else
              if ( null !== $request->get($itemMatch) ) {
                $val = $request->get($itemMatch);
              } elseif ( null !== $request->post($itemMatch) ) {
                $val = $request->post($itemMatch);
              }

            $field_values['filename'] = str_replace('{{'.$itemMatch.'}}', $val, $field_values['filename']);
          }
        }

        $registry = Registry::getInstance();
        $root = $registry['path']['uploadDir'];

        $filename = $root . $field_values['filename'];

        if ( !file_exists( dirname($filename) ) ) {
          mkdir(dirname($filename), 0777, true);
        }

        if ( !empty($values[$field]) ) {
          $file = new \Uwin\Fs\File($filename, 'w+');
          $file->write($values[$field]);
          $file->close();
        } else {
          if ( file_exists( $filename) ) {
            unlink($filename);
          }
        }

        continue;
      }

      if ( (!isset($field_values['noTable']) || 'true' != $field_values['noTable']) && (!isset($field_values['noedit']) || 'true' != $field_values['noedit']) ) {
        if ( !(isset($field_values['languageField']) && 'true' == $field_values['languageField']) ) {

          if ('file' == $field_values['type'] || 'image' == $field_values['type']) {
            if ( !isset($values[$field]) ) {
              continue;
            }
          }

          $sql .= $field . '=$' . $i . ', ';

          if ( !isset($field_values['callback']) ) {
            if ( !isset($values[$field]) ) {
              if ('bool' == $field_values['type']) {
                $values[$field] = 'false';
              } else {
                $values[$field] = 'null';
              }
            }

            if ('image' == $field_values['type'] && null != $values[$field]) {
              $retina = false;
              if ( isset($field_values['retina']) && $field_values['retina'] == 'true') {
                $retina = true;
              }

              $registry = Registry::getInstance();
              $root_path = rtrim($registry['path']['static_server'], '/');
              $pathFile = $root_path . $values[$field];
              if (!$retina) {
                $im = new Imagick($pathFile);
                $size = $im->getImageGeometry();
                $width = $size['width'];
                $height = $size['height'];

                $pathinfo = pathinfo($pathFile);
                $thm = $pathinfo['dirname'] . DIR_SEP . $pathinfo['filename'] . '-thm.' . $pathinfo['extension'];
                $imThm = new Imagick($thm);
                $sizeThm = $imThm->getImageGeometry();
                $widthThm = $sizeThm['width'];
                $heightThm = $sizeThm['height'];

                $_pref = '';
                if ( !empty($g_num_version) ) {
                  $_pref .= '-v' . $g_num_version;
                }
                $pathinfoRel = pathinfo($values[$field]);
                $thmRel = $pathinfoRel['dirname'] . DIR_SEP . $pathinfoRel['filename'] . '-thm' . $_pref . '.' . $pathinfoRel['extension'];

                $imageArray = [
                  'original' => [
                    'path' => $values[$field],
                    'width' => $width,
                    'height' => $height,
                  ],

                  'thm' => [
                    'path' => $thmRel,
                    'width' => $widthThm,
                    'height' => $heightThm,
                  ],
                ];
              } else {
                $im = new Imagick($pathFile);
                $size = $im->getImageGeometry();
                $width = $size['width'] * 2;
                $height = $size['height'] * 2;

                $pathinfo = pathinfo($pathFile);
                $thm = $pathinfo['dirname'] . DIR_SEP . $pathinfo['filename'] . '-thm.' . $pathinfo['extension'];
                $imThm = new Imagick($thm);
                $sizeThm = $imThm->getImageGeometry();
                $widthThm = $sizeThm['width'] * 2;
                $heightThm = $sizeThm['height'] * 2;

                $_pref = '';
                if ( !empty($g_num_version) ) {
                  // $_pref .= '-v' . $g_num_version;
                }

                $pathinfoRel = pathinfo($values[$field]);
                $thmRel = $pathinfoRel['dirname'] . DIR_SEP . $pathinfoRel['filename'] . '-thm' . $_pref . '.' . $pathinfoRel['extension'];

                $imageArray = [
                  'original' => [
                    'path' => $values[$field],
                    'width' => $width,
                    'height' => $height,
                  ],

                  'thm' => [
                    'path' => $thmRel,
                    'width' => $widthThm,
                    'height' => $heightThm,
                  ],
                ];
              }

              if (isset($field_values['resizes'])) {
                foreach ($field_values['resizes'] as $resize_key => $resize) {
                  if ( isset($resize['fileName']['function']) &&
                    'web-translit' == $resize['fileName']['function'] ) {
                      $ling = new Linguistics;
                      $rsFileName = $ling->getWebTranslit($values[$resize['fileName']['value']]);
                    } else {
                      $rsFileName = time();
                    }

                  if ( mb_strlen($rsFileName) >= 128 ) {
                    $rsFileName = mb_substr($rsFileName, 0, 127);
                  }

                  if ( isset($resize['fileName']['sufix']) ) {
                    $rsFileName .= $resize['fileName']['sufix'];
                  }

                  $_pref = '';
                  if ( !empty($g_num_version) ) {
                    $_pref .= '-v' . $g_num_version;
                  }

                  $rsFileName .= $_pref;

                  preg_match_all('/{{(.*?)}}/si', $resize['destination'], $matches);
                  if ( !empty($matches) ) {
                    $matches = $matches[1];
                    foreach ($matches as $itemMatch) {
                      $val = 'null';
                      if ('row_id' == $itemMatch) {
                        $val = $pk;
                      } else
                        if ( null !== $request->get($itemMatch) ) {
                          $val = $request->get($itemMatch);
                        } elseif ( null !== $request->post($itemMatch) ) {
                          $val = $request->post($itemMatch);
                        }

                      $resize['destination'] = str_replace('{{'.$itemMatch.'}}', $val, $resize['destination']);
                    }
                  }

                  $rsPathFile = $destination . $rsFileName . '.' . $ext;
                  $rsPathFile2x = $destination . $rsFileName . '@2x.' . $ext;
                  $im = new Imagick($rsPathFile2x);
                  $size = $im->getImageGeometry();
                  $width = $size['width'];
                  $height = $size['height'];

                  $rsPathFile = str_replace($root_path, '', $rsPathFile);
                  $imageArray[$resize_key] = [
                    'path' => $rsPathFile,
                    'width' => $width,
                    'height' => $height,
                  ];
                }
              }
              $imageJson = json_encode($imageArray, JSON_UNESCAPED_SLASHES);
              $values[$field] = $imageJson;
            }

            if ('date' == $field_values['type'] && null != $values[$field]) {
              $values[$field] = date( 'Y/m/d', strtotime($values[$field]) );
            }

            if ('datetime' == $field_values['type'] && null != $values[$field]) {
              $values[$field] = date( 'Y/m/d H:i', strtotime($values[$field]) );
            }
          } else {
            // Если есть функция, которая вычисляет значение поля, выполняем ее
            $function_parts = $field_values['callback'];
            $path = Registry::get('path');
            $module_path = $path['modules'];
            /** @noinspection PhpIncludeInspection */
            include_once($module_path . $function_parts['module'] . DIR_SEP  . 'models' . DIR_SEP . $function_parts['model'] . '.php');
            $class = new $function_parts['model'];
            $params = array();
            if ( isset($function_parts['params']) ) {
              foreach($function_parts['params'] as $name => $field_name) {
                if ( !isset($values[$field_name]) ) {
                  $values[$field_name] = null;
                }
                $params[$name] = $values[$field_name];
              }
            }
            if ( !isset($values[$field]) ) {
              $values[$field] = null;
            }
            $values[$field] = $class->{$function_parts['function']}($values[$field], $params);
          }

          $query->addParam($values[$field]);

          $i++;
        } else {
          if ( !empty($languages) ) {
            $list_languages = '';
            foreach ($languages as $language) {
              if ( isset($values['form-lang-' . $language['synonym']]) &&
                'true' == $values['form-lang-' . $language['synonym']] )
              {
                $list_languages .= $language['synonym'] . '|';
              }
              $lang_field = $field . '_' . $language['synonym'];

              if ('file' == $field_values['type'] || 'image' == $field_values['type']) {
                if ( !isset($values[$lang_field]) ) {
                  continue;
                }
              }

              $sql .= $lang_field . '=$' . $i . ', ';

              if ( !isset($field_values['callback']) ) {
                if ( !isset($values[$lang_field]) ) {
                  if ('bool' == $field_values['type']) {
                    $values[$field] = 'false';
                  } else {
                    $values[$field] = 'null';
                  }
                }

                if ('image' == $field_values['type'] && null != $values[$lang_field]) {
                  $retina = false;
                  if ( isset($field_values['retina']) && $field_values['retina'] == 'true') {
                    $retina = true;
                  }

                  $registry = Registry::getInstance();
                  $root_path = rtrim($registry['path']['static_server'], '/');
                  $pathFile = $root_path . $values[$lang_field];
                  if (!$retina) {
                    $im = new Imagick($pathFile);
                    $size = $im->getImageGeometry();
                    $width = $size['width'];
                    $height = $size['height'];

                    $pathinfo = pathinfo($pathFile);
                    $thm = $pathinfo['dirname'] . DIR_SEP . $pathinfo['filename'] . '-thm.' . $pathinfo['extension'];
                    $imThm = new Imagick($thm);
                    $sizeThm = $imThm->getImageGeometry();
                    $widthThm = $sizeThm['width'];
                    $heightThm = $sizeThm['height'];

                    $pathinfoRel = pathinfo($values[$lang_field]);
                    $thmRel = $pathinfoRel['dirname'] . DIR_SEP . $pathinfoRel['filename'] . '-thm.' . $pathinfoRel['extension'];

                    $imageArray = [
                      'original' => [
                        'path' => $values[$lang_field],
                        'width' => $width,
                        'height' => $height,
                      ],

                      'thm' => [
                        'path' => $thmRel,
                        'width' => $widthThm,
                        'height' => $heightThm,
                      ],
                    ];
                  } else {
                    $im = new Imagick($pathFile);
                    $size = $im->getImageGeometry();
                    $width = $size['width'] * 2;
                    $height = $size['height'] * 2;

                    $pathinfo = pathinfo($pathFile);
                    $thm = $pathinfo['dirname'] . DIR_SEP . $pathinfo['filename'] . '-thm.' . $pathinfo['extension'];
                    $imThm = new Imagick($thm);
                    $sizeThm = $imThm->getImageGeometry();
                    $widthThm = $sizeThm['width'] * 2;
                    $heightThm = $sizeThm['height'] * 2;

                    $pathinfoRel = pathinfo($values[$lang_field]);
                    $thmRel = $pathinfoRel['dirname'] . DIR_SEP . $pathinfoRel['filename'] . '-thm.' . $pathinfoRel['extension'];

                    $imageArray = [
                      'original' => [
                        'path' => $values[$lang_field],
                        'width' => $width,
                        'height' => $height,
                      ],

                      'thm' => [
                        'path' => $thmRel,
                        'width' => $widthThm,
                        'height' => $heightThm,
                      ],
                    ];
                  }
                  $imageJson = json_encode($imageArray, JSON_UNESCAPED_SLASHES);
                  $values[$lang_field] = $imageJson;
                }

                if ('date' == $field_values['type'] && null != $values[$lang_field]) {
                  $values[$lang_field] = date( 'Y/m/d', strtotime($values[$lang_field]) );
                }

                if ('datetime' == $field_values['type'] && null != $values[$lang_field]) {
                  $values[$lang_field] = date( 'Y/m/d H:i', strtotime($values[$lang_field]) );
                }
              } else {
                // Если есть функция, которая вычисляет значение поля, выполняем ее
                $function_parts = $field_values['callback'];
                $path = Registry::get('path');
                $module_path = $path['modules'];
                /** @noinspection PhpIncludeInspection */
                include_once($module_path . $function_parts['module'] . DIR_SEP  . 'models' . DIR_SEP . $function_parts['model'] . '.php');
                $class = new $function_parts['model'];
                $params = array();
                if ( isset($function_parts['params']) ) {
                  foreach($function_parts['params'] as $name => $field_name) {
                    if ( !isset($values[$field_name]) ) {
                      $values[$field_name] = null;
                    }
                    $params[$name] = $values[$field_name];
                  }
                }
                $values[$lang_field] = $class->{$function_parts['function']}($values[$lang_field], $params);
              }

              $query->addParam($values[$lang_field]);

              $i++;
            }
          }
        }
      }
    }

    if ( isset($configValues['listLanguagesField']) ) {
      $sql .= $configValues['listLanguagesField'] . '=$' . $i;
      $list_languages = '|' . trim($list_languages, '|') . '|';
      $query->addParam($list_languages);
    }

    $sql = trim($sql, ', ');

    $query->addSql($sql . ' where ' . $configValues['pk'] . ' = $1')
      ->execute();

    return true;
  }

  /**
   * @param \Uwin\Config $config
   * @param \Uwin\Config $language
   * @param string $path
   * @param $values
   * @param $languageValues
   * @return array
   */
  private function _setValuesConfig($config, $language, $path, $values, $languageValues) {
    $fieldsValues = $this->getRequest()->post();

    if ( !empty($path) ) {
      $path .= '/';
    }

    $result = array();
    foreach ($values as $name => $field) {
      if ( !isset($field['type']) ) {
        $r = $this->_setValuesConfig($config, $language, $path . $name, $field, $languageValues[$name]);

        if (true !== $r) {
          $result = array_merge_recursive($result, $r);
        }
        continue;
      }

      $var_name = str_replace('/', '_', $path . $name);
      $fieldValue = 'false';
      if ( isset($fieldsValues[$var_name]) ) {
        $fieldValue = $fieldsValues[$var_name];
      }

      // Валидация
      $validate = $this->_validateFieldForm($var_name, $field, $fieldValue, $languageValues[$name]);
      if (true !== $validate) {
        $result[] = $validate;
      }

      $configer = &$config;
      if ( isset($field['language']) && 'true' == $field['language']) {
        $configer = &$language;
      }

      $add_path = null;
      if ( isset($field['path']) ) {
        $add_path = $name . '/';
        $name = $field['path'];
      }

      if ( $configer->exists($path . $add_path . $name) ) {
        $configer->set($path . $add_path . $name, $fieldValue);
      } else {
        $parts_path = explode( '/', trim($path . $add_path, '/') );

        $root = null;
        foreach ($parts_path as $part) {
          if ($configer->exists($root . $part) ) {
            $root .= $part . '/';
            continue;
          }

          $configer->add($root, $part);
          $root .= $part . '/';
        }
        $configer->add($path . $add_path,  $name, $fieldValue);
      }
    }

    if ( empty($result) ) {
      $result = true;
    }

    return $result;
  }

  private function _saveForm() {
    $result = array();

    $registry = Registry::getInstance();

    $configValues = $this->_getConfigValues('configs');

    $languageValues = $this->_getLanguageValues('configs');

    $languageName = $this->getRequest()->post('language');

    if ( empty($languageName) || 'null' == $languageName) {
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $languageName = $defaultLanguage['lng_synonym'];
    }

    foreach ($configValues as $module => $values) {
      if ('root' == $module) {
        $config_file = $this->getVariable('path_userSettings') . 'general.xml';
        $language_file = $this->getVariable('path_userSettings') . 'languages' . DIR_SEP . $languageName . '.xml';
      } else {
        $config_file = $this->getVariable('path_userSettings') . 'modules' . DIR_SEP . $module . DIR_SEP . 'config.xml';
        $language_file = $this->getVariable('path_userSettings') . 'modules' . DIR_SEP . $module . DIR_SEP . 'languages' . DIR_SEP . $languageName . '.xml';
      }

      if ( !file_exists( dirname($config_file) ) ) {
        mkdir(dirname($config_file), 0755, true);
      }
      if ( !file_exists( dirname($language_file) ) ) {
        mkdir(dirname($language_file), 0755, true);
      }

      $config = new Config(Config::XML);
      if ( file_exists($config_file) ) {
        $config->open($config_file, $module);
      } else {
        $config->open(array($module => array('null')), $module);
      }

      $language = new Config(Config::XML);
      if ( file_exists($language_file) ) {
        $language->open($language_file, $module);
      } else {
        $language->open(array($module => array('null')), $module);
      }

      $r = $this->_setValuesConfig($config, $language, null, $values, $languageValues[$module]);
      if (true !== $r) {
        $result = array_merge_recursive($result, $r);
      } else {
        $config->save($config_file);
        $language->save($language_file);

        Memcached::getInstance()->tagsVersions(md5($config_file), true);
        Memcached::getInstance()->tagsVersions(md5($language_file), true);
      }
    }

    if ( empty($result) ) {
      $result_tmp = true;
    } else {
      $result_tmp['errors'] = $result;
    }

    return $result_tmp;
  }

  private function _saveLanguageForm() {
    $filename = $this->getRequest()->post('file');
    if ( !file_exists( dirname($filename) ) ) {
      mkdir(dirname($filename), 0755, true);
    }

    $config = new Config(Config::XML);
    $config->open($this->getRequest()->post('content'));
    $config->save($filename);

    Memcached::getInstance()->tagsVersions(md5($filename), true);

    return true;
  }

  private function _validateFieldForm($fieldName, $field, $value, $langValues) {
    $validator = new Validator;
    $result = array();

    $preprocessing = $rules = array();
    if ( isset($field['preprocessing']) ) {
      $preprocessing = $field['preprocessing'];
    }
    if ( isset($field['validate']) ) {
      $rules = $field['validate'];
    }

    foreach ($preprocessing as $function => $preprocessing_values) {
      switch ($function) {
      case 'trim':
        $value = trim($value);
        break;

      case 'lowercase':
        $value = mb_strtolower($value);
        break;

      case 'uppercase':
        $value = mb_strtoupper($value);
        break;
      }
    }

    foreach ($rules as $rule => $rule_values) {
      $error = false;

      switch ($rule) {
      case 'empty':
        if ( $validator->isEmpty($value) ) {
          $error = true;
        }
        break;

      case 'regexp':
        if ( !empty($value) && !$validator->equalRegexp($value,
          $rule_values) ) {
            $error = true;
          }
        break;

      case 'parseInt':
        if ( !$validator->parseInt($value) ) {
          $error = true;
        }
        break;

      case 'parseUrl':
        if ( !$validator->parseUrl($value) ) {
          $error = true;
        }
        break;

      case 'parseFloat':
        if ( !$validator->parseFloat($value) ) {
          $error = true;
        }
        break;

      case 'parseDate':
        if ( !$validator->parseDate($value) ) {
          $error = true;
        }
        break;

      case 'parseTime':
        if ( !$validator->parseTime($value) ) {
          $error = true;
        }
        break;

      case 'parseDateTime':
        if ( !$validator->parseDateTime($value) ) {
          $error = true;
        }
        break;

      case 'email':
        if ( !$validator->isEmail($value) ) {
          $error = true;
        }
        break;
      }

      if (false !== $error) {
        $result = array(
          'id'   => $fieldName,
          'text' => $langValues['validate'][$rule],
        );

        break;
      }
    }

    if ( empty($result) ) {
      $result = true;
    }

    return $result;
  }

  public function operation() {
    $request = $this->getRequest();
    $tableName = $request->get('tableName');
    $operation = $request->getParam('operation');
    $id = $request->getParam('id');

    $result = true;
    try {
      switch ($operation) {
      case 'delete':
        $this->_deleteRow($tableName, $id);
        break;

      case 'edit':
        $result = $this->_editRow($tableName, $id);
        break;

      case 'add':
        $result = $this->_addRow($tableName);
        break;

      case 'save':
        $result = $this->_saveForm();
        break;

      case 'saveLanguage':
        $result = $this->_saveLanguageForm();
        break;

      default:
        $result = $this->_customAction();
      }

      $this->setVariable('result', $result);
    } catch (\Exception $e) {
      $errors['errors'][] = array('id' => '', 'text' => $e->getMessage());
      $this->setVariable('result', $errors);
    }

    $params_table = $this->_getVariablesTables($tableName);
    if ( isset($params_table[0]['tags'] )) {
      $tags = explode('|', $params_table[0]['tags']);

      foreach($tags as $tag) {
        preg_match_all('/{{(.*?)}}/si', $tag, $matches);
        if ( !empty($matches) ) {
          $matches = $matches[1];
          foreach ($matches as $itemMatch) {
            $tag = str_replace('{{' . $itemMatch . '}}', $request->get($itemMatch), $tag);
          }
        }

        Memcached::getInstance()->tagsVersions($tag, true);
      }
    }

    return $this;
  }

  public function _imgResize($src, $dest, $width, $height, $quality=100)
  {
    $path_info = pathinfo($src);

    // Если у файла указано расширение, получаем его
    if ( array_key_exists('extension', $path_info) ) {
      $ext = $path_info['extension'];
    }
    $extentions = array('jpg', 'jpeg', 'gif', 'png', 'bmp'); // Определяем фор

    if ( !in_array($ext, $extentions) ) {
      return false;
    }

    $size = getimagesize($src);

    if (false === $size) {
      return false;
    }

    //    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    //    $icfunc = "imagecreatefrom" . $format;
    //    if ( !function_exists($icfunc) ) {
    //      return false;
    //    }

    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];

    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);

    $new_width = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);

    $img = new Imagick($src);
    $img->thumbnailImage($new_width, $new_height, TRUE);
    $img->writeimage($dest);
    $img->clear();
    $img->destroy();

    $registry = Registry::getInstance();
    $rpath = $registry['path']['root'];
    if('jpg' == $ext) {
    exec($rpath . 'node_modules/imagemin-mozjpeg/node_modules/mozjpeg/vendor/jpegtran -copy none -outfile'
      . $dest . ' ' . $dest);
    }
    //    $isrc = $icfunc($src);
    //    $idest = imagecreatetruecolor($new_width, $new_height);
    //
    //    imagecopyresampled($idest, $isrc, 0, 0, 0, 0,
    //      $new_width, $new_height, $size[0], $size[1]);
    //
    //    // Создаем изображение
    //    switch ($ext) {
    //      case 'jpg':
    //        imagejpeg($idest, $dest, $quality);
    //        break;
    //
    //      case 'gif':
    //        imagegif($idest, $dest);
    //        break;
    //
    //      case 'png':
    //        imagepng($idest, $dest);
    //        break;
    //
    //      case 'bmp':
    //        imagewbmp($idest, $dest);
    //        break;
    //    }
    //
    //    imagedestroy($isrc);
    //    imagedestroy($idest);

    return true;
  }

  public function createThumbnails($image, $replace = false, $absolute_path = false, $retina = false)
  {
    $registry = Registry::getInstance();
    $root_path = $registry['path']['static_server'];
    $relative_path = $image;
    if (!$absolute_path) {
      if (DIR_SEP == $relative_path[0]) {
        $relative_path = substr($relative_path, 1);
      }

      $src = $root_path . $relative_path;
    } else {
      $src = $relative_path;
    }

    $dest_dir = dirname($src);
    if (DIR_SEP != $dest_dir[strlen($dest_dir)-1]) {
      $dest_dir .= DIR_SEP;
    }
    // $dest_file = $dest_dir . '.thm-' . basename($src);
    if ( file_exists($src) ) {
      $pathinfo = pathinfo($src);
      $dest_file = $dest_dir . $pathinfo['filename'] . '-thm.' . $pathinfo['extension'];
      if ( !file_exists($dest_file) || $replace) {
        if (!$retina) {
          $this->_imgResize($src, $dest_file, 90, 50, 50);
        } else {
          $dest_file2x = $dest_dir . $pathinfo['filename'] . '-thm@2x.' . $pathinfo['extension'];
          $this->_imgResize($src, $dest_file, 90, 50, 85);
          $this->_imgResize($src, $dest_file2x, 180, 100, 85);
        }
      }
    }
  }

  public function typografy() {
    $result = array(
      'result' => $this->_typograf( $this->getRequest()->post('text') ),
    );

    $this->setVariable('result', $result);

    return $this;
  }

  public function loginRedmine() {
    $url = 'redmine.uwinart.com';
    $fp = fsockopen($url, 80, $errno, $errstr, 30);
    $header = '';
    $header .= "GET /login HTTP/1.0\r\n";
    $header .= "Host: redmine.uwinart.com\r\n";
    $header .= "User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)\r\n\r\n";
    $result = "";
    $headers = "";
    if ($fp) {
      fputs ($fp, $header);

      do {
        $headers .= fgets ( $fp, 128 );
      } while ( strpos ( $headers, "\r\n\r\n" ) === false );

      while ( !feof($fp) ) {
        $result .= fgets ($fp, 1024);
      }
    }
    fclose ($fp);

    $matches = array();
    preg_match('#<meta name="csrf-token"\s*content="(.*?)"\s*/>#si', $result, $matches);

    $params = array(
      'username' => 'khmelevskii',
      'password' => '_yesnomaybe05',
      'authenticity_token' => $matches[1],
    );

    $this->setVariable('result', $params);

    return $this;
  }

  public function moveRow() {
    $request = $this->getRequest();
    $tableName = $request->post('table');
    $idColumn = $request->post('pk');
    $orderColumn = $request->post('orderColumn');
    $sourceId = $request->post('sourceId');
    $movedId = $request->post('movedId');

    $sourceRow = $this->db()->query()
      ->sql('select ' . $orderColumn . ' from ' . $tableName . ' where ' . $idColumn . '=$1')
      ->addParam($sourceId)
      ->fetchRow(0, false);

    $this->db()->query()
      ->addSql('START TRANSACTION;')
      ->addSql('update ' . $tableName . ' set ' . $orderColumn . '= (select ' . $orderColumn . ' from ' . $tableName . ' where ' . $idColumn . '=$2)')
      ->addSql('where ' . $idColumn . '= $1;')
      ->addSql('update ' . $tableName . ' set ' . $orderColumn . '= $3')
      ->addSql('where ' . $idColumn . '= $2;')
      ->addSql('COMMIT;')
      ->addParam($sourceId)
      ->addParam($movedId)
      ->addParam($sourceRow[$orderColumn])
      ->execute();

    $params_table = $this->_getVariablesTables($tableName);
    if ( isset($params_table[0]['tags'] )) {
      $tags = explode('|', $params_table[0]['tags']);

      foreach($tags as $tag) {
        preg_match_all('/{{(.*?)}}/si', $tag, $matches);
        if ( !empty($matches) ) {
          $matches = $matches[1];
          foreach ($matches as $itemMatch) {
            $tag = str_replace('{{' . $itemMatch . '}}', $request->get($itemMatch), $tag);
          }
        }

        Memcached::getInstance()->tagsVersions($tag, true);
      }
    }

    return $this;
  }

  public function getModuleLanguages() {
    $config = $this->_getConfigValues();
    if ( isset($config['useLanguages']) &&
      'false' == $config['useLanguages'] ) {
        return $this;
      }

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    if ( !empty($languages) ) {
      foreach ($languages as $id => $values) {
        $languages[$id]['active'] = false;

        if ('t' == $languages[$id]['lng_default']) {
          $languages[$id]['active'] = true;
          $this->setVariable('default_lang', $values['synonym']);
        }

        if ( count($languages) >= 9) {
          $languages[$id]['name'] = $values['lng_short_name'];
        }
      }
    }

    $this->setVariable('languages', $languages);

    return $this;
  }

  public function getFormLanguages($active_languages = null, $config = null) {
    $current_language = $this->getRequest()->get('language');
    $table = $this->getRequest()->get('tableName');
    if ( empty($config) ) {
      $config = $this->_getConfigValues('datasources/' . $table);
    }

    if ( !(isset($config['useLanguages']) &&
      'true' == $config['useLanguages']) ) {
        return $this;
      }

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    if ( !empty($languages) ) {
      foreach ($languages as $id => $values) {
        $languages[$id]['active'] = false;
        $languages[$id]['use'] = false;

        if ($current_language == $languages[$id]['synonym']) {
          $languages[$id]['active'] = true;
          $languages[$id]['use'] = true;
        }

        if ( !empty($active_languages) ) {
          if ( isset($active_languages[$languages[$id]['synonym']]) ) {
            $languages[$id]['use'] = true;
          }
        }

        if ( count($languages) >= 7) {
          $languages[$id]['name'] = $values['lng_short_name'];
        }
      }
    }

    $this->setVariable('languages', $languages);

    return $this;
  }

  public function getCustomFormLanguages($config = null) {
    $current_language = $this->getRequest()->get('language');
    if ( empty($current_language) ) {
      $defaultLanguage = $this->db()->query()
        ->addSql('select lng_synonym from languages_tbl')
        ->addSql('where lng_default=true')
        ->fetchRow(0, false);

      $current_language = $defaultLanguage['lng_synonym'];
    }
    $table = $this->getRequest()->get('tableName');
    if ( empty($config) ) {
      $config = $this->_getConfigValues('datasources/' . $table);
    }

    if ( !(isset($config['useLanguages']) &&
      'true' == $config['useLanguages']) ) {
        return $this;
      }

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    if ( !empty($languages) ) {
      foreach ($languages as $id => $values) {
        $languages[$id]['active'] = false;
        $languages[$id]['use'] = false;

        if ($current_language == $languages[$id]['synonym']) {
          $languages[$id]['active'] = true;
          $languages[$id]['use'] = true;
        }

        if ( count($languages) >= 7) {
          $languages[$id]['name'] = $values['lng_short_name'];
        }
      }
    }

    $this->setVariable('languages', $languages);

    return $this;
  }

  public function getSettingsLanguages() {
    $config = $this->_getConfigValues();
    if ( isset($config['useLanguages']) &&
      'false' == $config['useLanguages'] ) {
        return $this;
      }

    $languages = $this->db()->query()
      ->addSql('select lng_default, lng_name as name, lng_short_name, lng_synonym as synonym from languages_tbl')
      ->addSql('where lng_enabled=true order by lng_order')
      ->fetchResult(false);

    if ( !empty($languages) ) {
      foreach ($languages as $id => $values) {
        $languages[$id]['active'] = false;

        if ('t' == $languages[$id]['lng_default']) {
          $languages[$id]['active'] = true;
          $this->setVariable('default_lang', $values['synonym']);
        }

        if ( count($languages) >= 9) {
          $languages[$id]['name'] = $values['lng_short_name'];
        }
      }
    }

    $this->setVariable('languages', $languages);

    return $this;
  }
}
