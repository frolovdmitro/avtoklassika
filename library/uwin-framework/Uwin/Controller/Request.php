<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Controller\Request, который служит для работы
 * с параметрами пришедшими от пользователя
 *
 * @category   Uwin
 * @package    Uwin\Controller
 * @subpackage Request
 * @author     Yurii Khmelevskii (y@uwinart.com)
 * @copyright  Copyright (c) 2009-2013 UwinArt Development (http://uwinart.com)
 * @version    $Id$
 */

/**
 * Объявляем пространсто имен Uwin\Controller, к которому относится класс Request
 */
namespace Uwin\Controller;

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Front             as Front;
use \Uwin\Controller\Request\Exception as RequestException;

/**
 * Класс, который служит для работы с параметрами пришедшими от пользователя
 *
 * @category  Uwin
 * @package   Uwin\Controller
 * @subpackage Request
 * @author    Khmelevskiy Yuriy (yuriy@uwinart.com.ua)
 * @copyright Copyright (c) 2009-2010 UwinArt Studio (http://uwinart.com.ua)
 */
class Request
{
  /**
   * Ссылка на экзепляр класса
   * @var Request
   */
  private static $_instance = null;

  /**
   * Имя модуля
   * @var string
   */
  private $_moduleName;

  /**
   * Имя контроллера
   * @var string
   */
  private $_controllerName;

  /**
   * Имя действия
   * @var string
   */
  private $_actionName;

  /**
   * Массив параметров полученных от моршрутизатора
   * @var array
   */
  private $_params = array();


  /**
   * Приватный конструктор класса, так как класс реализует паттерн Singlton.
   *
   * @return Request
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
     * Метод возвращает ссылку на объект класса Uwin\Controller\Request
     *
     * @return Request
     */
    public static function getInstance()
    {
      if ( empty(self::$_instance) )  {
        self::$_instance = new self;
      }

      return self::$_instance;
    }

  /**
   * Метод, который возвращает имя модуля
   *
   * @return string
   */
  public function getModuleName()
  {
    if (null === $this->_moduleName) {
      $this->_moduleName = $this->getParam('module');
    }

    return $this->_moduleName;
  }

  /**
   * Метод, который устанавливает имя модуля
   *
   * @param string $name Имя модуля
   * @return Request
   */
  public function setModuleName($name)
  {
    $this->_moduleName = $name;

    return $this;
  }

  /**
   * Метод, который возвращает имя контроллера
   *
   * @return string
   */
  public function getControllerName()
  {
    if (null === $this->_controllerName) {
      $this->_controllerName = $this->getParam('controller');
    }

    return $this->_controllerName;
  }

  /**
   * Метод, который устанавливает имя контроллера
   *
   * @param string $name Имя контроллера
   * @return Request
   */
  public function setControllerName($name)
  {
    $this->_controllerName = $name;

    return $this;
  }

  /**
   * Метод, который возвращает имя действия
   *
   * @return string
   */
  public function getActionName()
  {
    if (null === $this->_actionName) {
      $this->_actionName = $this->getParam('action');
    }

    return $this->_actionName;
  }

  /**
   * Метод, который устанавливает имя действия
   *
   * @param string $name Имя действия
   * @return Request
   */
  public function setActionName($name)
  {
    $this->_actionName = $name;

    return $this;
  }

  /**
   * Метод, который возвращает значение переменной
   *
   * @param string $param Имя переменной
   * @param string $default Значение по умолчанию
   * @return string
   */
  public function getParam($param, $default = null)
  {
    $param = (string) $param;

    if ( isset($this->_params[$param]) ) {
      return htmlspecialchars($this->_params[$param]);
    }

    return $default;
  }

  /**
   * Метод, который устанавливает значение переменной
   *
   * @param string $param Имя переменной
   * @param mixed $value Значение переменной
   *
   * @return Request
   */
  public function setParam($param, $value)
  {
    $this->_params[$param] = $value;

    return $this;
  }

  /**
   * Метод, который возвращает массив всех переменных
   *
   * @return array
   */
  public function getParams()
  {
    return $this->_params;
  }

  /**
   * Метод, который безопасно извлекает нужную GET-переменную, если имя
   * переменной не указано - возвращает массив всех GET-переменных
   *
   * @param string $name Имя переменной
   * @return string|Array
   */
  public function get($name = null)
  {
    if (null === $name) {
      $value = $_GET;
      if ( isset($value['route']) ) {
        unset($value['route']);
      }

      return $value;
    }

    if ( isset($_GET[$name]) ) {
      $value = htmlspecialchars($_GET[$name]);
    } else {
      $value = null;
    }

    return $value;
  }

  /**
   * Метод, который безопасно извлекает нужную POST-переменную
   *
   * @param string $name Имя переменной
   * @return string|Array
   */
  public function post($name = null)
  {
    if (null === $name) {
      if ( empty($_POST) ) {
        return null;
      }

      return $_POST;
    }

    if ( isset($_POST[$name]) ) {
      $value = $_POST[$name];
    } else {
      $value = null;
    }

    return $value;
  }

  /**
   * @param null $name
   * @param bool $use_htmlspecialchars
   *
   * @return array|null|string
   */
  public function cookie($name = null, $use_htmlspecialchars = true)
  {
    if (null === $name) {
      if ( empty($_COOKIE) ) {
        return null;
      }

      return $_COOKIE;
    }

    if ( isset($_COOKIE[$name]) ) {
      $value = $_COOKIE[$name];

      if ($use_htmlspecialchars) {
        $value = htmlspecialchars($_COOKIE[$name]);
      }
    } else {
      $value = null;
    }

    return $value;
  }

  public function setCookie($name, $value, $json = false, $expire = 2147483647, $path = '/', $domain = null)
  {
    if (null === $domain) {
      $domain = COOKIE_HOST;
    }

    if (true === $json) {
      $value = json_encode($value);
    }

    setcookie($name, $value, $expire, $path, $domain);

    return $this;
  }

  /**
   * undocumented function
   *
   * @return void
   */
  public function removeCookie($name, $path = '/', $domain = null) {
    if (null === $domain) {
      $domain = COOKIE_HOST;
    }

    setcookie($name, null, time() - 3600, '/', $domain);

    return true;
  }

  /**
   *
   * @param string $name
   *
   * @return array|null
   */
  public function files($name = null)
  {
    if (null === $name) {
      if ( empty($_FILES) ) {
        return null;
      }

      return $_FILES;
    }

    if ( isset($_FILES[$name]) ) {
      $value = $_FILES[$name];
    } else {
      $value = null;
    }

    return $value;
  }

  public function getVariableExists($name)
  {
    if ( null == $this->get() ) {
      return false;
    }

    if ( !array_key_exists( $name, $this->get() ) ) {
      return false;
    }

    return true;
  }

  /**
   *
   * @param string $name
   *
   * @return bool
   */
  public function postVariableExists($name)
  {
    if ( null == $this->post() ) {
      return false;
    }

    if ( !array_key_exists( $name, $this->post() ) ) {
      return false;
    }

    return true;
  }

  /**
   * Метод возвращает переменную глобального массива $_SERVER
   *
   * @param $name string Имя переменной
   *
   * @return string
   * @throws RequestException Ошибка работы c запросом
   */
  public function getServerVariable($name)
  {
    if ( !array_key_exists($name, $_SERVER) ) {
      throw new RequestException('Request error: failure get SERVER variable "' . $name . '"', 201);
    }

    return $_SERVER[$name];
  }

  /**
   * Метод возвращает тип запроса (GET или POST)
   *
   * @return string
   */
  public function getMethod()
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * Метод возвращает признак того, является ли запрос типа GET
   *
   * @return bool
   */
  public function isGet()
  {
    if ( 'GET' == $this->getMethod() ) {
      return true;
    }

    return false;
  }

  /**
   * Метод возвращает признак того, является ли запрос типа POST
   *
   * @return bool
   */
  public function isPost()
  {
    if ( 'POST' == $this->getMethod() ) {
      return true;
    }

    return false;
  }

  /**
   * Метод возвращает адресс текущей страницы без GET-переменных и,
   * по-умолчаниию, без страницы
   *
   * @param bool $withoutPage Флаг присутствия страницы в адресе
   * @return string
   */
  public function getCurrentUrl($withoutPage = true, $withoutSort = false)
  {
    // Получение адреса текущей страницы
    $url = rtrim( parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/' );

    // Проверка, указан ли номер страницы, если указан, а он не нужен,
    // вырезаем его
    if (true == $withoutPage) {
      $url = explode('/', $url);
      $lastIndex = count($url) - 1;
      if ( isset($url[$lastIndex]) ) {
        if (false !== strpos($url[$lastIndex], 'page')) {
          unset($url[$lastIndex]);
        }
      }

      $url = implode('/', $url);
    }

    if (true == $withoutSort) {
      $url = explode('/', $url);
      $lastIndex = count($url) - 1;
      if ( isset($url[$lastIndex]) ) {
        if (false !== strpos($url[$lastIndex], 'sort=')) {
          unset($url[$lastIndex]);
        }
      }

      $url = implode('/', $url);
    }

    return $url;
  }

  /**
   * Метод возвращает адресс текущей страницы со всеми GET-переменными
   *
   * @return string
   */
  public function getCurrentUrlWithGets()
  {
    return $_SERVER['REQUEST_URI'];
  }

  /**
   * Метод проверяет на соответствие переданного адреса и текущей страницы
   *
   * @param string $url Проверяемый адрес
   * @param bool $without_page
   *
   * @return bool
   */
  public function equalUrl($url, $without_page = true)
  {
    if ( $this->getCurrentUrl($without_page) == rtrim($url, '/') ) {
      return true;
    }

    return false;
  }

  /**
   * Метод проверяет на соответствие переданного адреса и текущей страницы с
   * помощью указанного регулярного выражения
   *
   * @param string $regexp
   * @param string $url Проверяемый адрес
   *
   * @return bool
   */
  public function equalRegexpUrl($regexp, $url)
  {
    $currentUrl = $this->getCurrentUrl();
    $url = rtrim($url, '/');

    // Если проверяемый адрес и текущий адрес страницы пусты, то возвращаем
    // истину, так как эти адреса идентичны
    if ( ($currentUrl == '') && ($url == '') ) {
      return true;
    }

    if ( (0 !== preg_match($regexp, $currentUrl, $arr)) && ($url != '') ) {
      return true;
    };

    return false;
  }

  /**
   * Метод проверяет на вхождение переданного адреса в адрес текущей страницы
   *
   * @param string $url Проверяемый адрес
   * @return bool
   */
  public function equalUrlWithTail($url)
  {
    $currentUrl = $this->getCurrentUrl(false);
    $url = rtrim($url, '/');

    // Если проверяемый адрес и текущий адрес страницы пусты, то возвращаем
    // истину, так как эти адреса идентичны
    if ( ($currentUrl == '') && ($url == '') ) {
      return true;
    }

    $regexp = '#' . $url . '(?:.*)#s';
    if ( (0 !== preg_match($regexp, $currentUrl, $arr)) && ($url !== '') ) {
      return true;
    };

    return false;
  }

  /**
   * Метод выполняет 503 редирект страницы на указанный адресс
   *
   * @param string $url Адрес, куда выполняется переадресация
   * @return bool
   */
  public function redirect($url)
  {
    // Посылаем заголовок редиректа на указанный url
    Header("HTTP/1.1 301 Moved Permanently");
    Header('Location: ' . $url);

    // Завершаем приложение
    die();
  }

  public function sendPost($host, $port, $script, $data)
  {
    $scheme = null;
    if (443 == $port) {
      $scheme = 'ssl://';
    }

    if ( is_array($data) ) {
      $data = http_build_query($data);
    }

    $fp = fsockopen($scheme . $host, $port, $errno, $errstr, 30);

    if ($fp) {
      fputs($fp, "POST $script HTTP/1.0\n");
      fputs($fp, "Host: $host\n");
      fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
      fputs($fp, "Content-length: " . mb_strlen($data, "UTF-8") . "\n");
      fputs($fp, "User-Agent: PHP Script\n");
      fputs($fp, "Connection: close\n\n");
      fputs($fp, $data);
      while( fgets($fp, 2048) != "\r\n" && !feof($fp));
      $buf = null;
      while( !feof($fp) ) {
        $buf .= fread($fp,2048);
      }
      fclose($fp);
    }
    else{
      return false;
    }

    return $buf;
  }

  public function getProtocol() {
    $protocol = $_SERVER['SERVER_PROTOCOL'];

    if ( false !== mb_strpos(mb_strtolower($protocol), 'https') ) {
      return 'https';
    }

    if ( false !== mb_strpos(mb_strtolower($protocol), 'http') ) {
      return 'http';
    }

    if ( false !== mb_strpos(mb_strtolower($protocol), 'ftp') ) {
      return 'ftp';
    }

    return false;
  }

  public function getHost($with_scheme = false) {
    $host = $_SERVER['HTTP_HOST'];
    if ($with_scheme) {
      $host = $this->getProtocol() . '://' . $host;
    }

    return $host;
  }

  /**
   * Метод добавляет заголовки с кодом 404, который говорит, что
   * страница не найдена
   *
   * @return Request
   */
  public function sendHeaderNotFound() {
    header("HTTP/1.0 404 Not Found");

    return $this;
  }

  /**
   * Метод добавляет заголовки с кодом 501, который говорит, что
   * произошла ошибка
   *
   * @return Request
   */
  public function sendHeaderError() {
    header("HTTP/1.0 501 Application error");
    header('Status: 501 Application error');

    return $this;
  }

  /**
  /**
   * Метод добавляет заголовки с кодом 503, который говорит, что
   * страница на ремонте. Можно указать через какое время она будет доступна
   *
   * @param int $seconds - Кол-во секунд, после которых страница заработает
   *
   * @return Request
   */
  public function sendHeaderUnavailable($seconds = null) {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');

    if (null !== $seconds) {
      header('Retry-After: ' . (int)$seconds);
    }

    return $this;
  }

  public function getRemoteIp() {
    return $this->getServerVariable('REMOTE_ADDR');
  }

  public function getUserAgent() {
    return $this->getServerVariable('HTTP_USER_AGENT');
  }

  public function getReferer($only_domain = false) {
    if ( !isset($_SERVER['HTTP_REFERER']) ) {
      return null;
    }

    if ($only_domain) {
      return parse_url($this->getServerVariable('HTTP_REFERER'),
        PHP_URL_HOST);
    } else {
      return $this->getServerVariable('HTTP_REFERER');
    }
  }

  public function subdomain()
  {
    $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    $host = str_replace($_SERVER['SERVER_NAME'], '', $host);
    $subdomain = array_shift((explode(".", $host)));

    if (empty($subdomain)) {
      return null;
    }

    return $subdomain;
  }

  public function serverName()
  {
    $server_name = $_SERVER['SERVER_NAME'];

    return $server_name;
  }

  public function withWww()
  {
    list($subdomain, $rest) = explode('.', $_SERVER['HTTP_HOST'], 2);
    if ('www' == $subdomain) {
      return $subdomain . '.';
    }

    return null;
  }

  /**
   * Метод возвращает текущее значение указанного параметра фильтра
   *
   * @param string $name - имя параметра фильтра
   *
   * @return string
   */
  public function getCurrentFilterValue($name) { // {{{
    $filter = $this->getParam('filter');

    if ( empty($filter) ) {
      return null;
    }

    $filter = trim($filter, ';');
    $filter_vars = explode(';', $filter);

    $result = [];
    foreach ($filter_vars as $var) {
      $params = explode('=', $var);

      if (false === strpos($params[1], ',')) {
        $result[$params[0]] = $params[1];
      } else {
        $values = explode(',', $params[1]);

        foreach ($values as $value) {
          $result[$params[0]][] = $value;
        }
      }
    }

    if ( isset($result[$name]) ) {
      return $result[$name];
    }

    return null;
  } // }}}
}
