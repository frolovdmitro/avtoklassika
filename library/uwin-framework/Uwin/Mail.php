<?php
/**
 * Uwin Framework
 *
 * Файл содержащий класс Uwin\Mail, который отвечает за отправку почтовых
 * сообщений
 *
 * @category  Uwin
 * @package   Uwin\Mail
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

/**
 * Объявляем пространсто имен Uwin, к которому относится класс Mail
 */
namespace Uwin;

/**
 * Подключаем классы Mail и Mime из PEAR, с помощью которых и отправляються
 * почновые сообщения
 */
include_once('/usr/local/share/pear/Mail.php');
include_once('/usr/local/share/pear/Mail/mime.php');

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Mail\Exception as Exception;

/**
 * Класс, который отвечает за отправку почтовых сообщений. Отправка производится
 * только с помощью SMTP сервера
 *
 * @category  Uwin
 * @package   Uwin\Mail
 * @author    Khmelevskiy Yuriy (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class Mail
{
    //TODO Откразать от использования сторонних библиотек с PEAR
    //TODO Сделать поддержку TLS, возможность указывать порт и пароль к SMTP серверу

    /**
     * Адрес SMTP-сервера
     * @var string = null
     */
    private $_smtpHost = null;

    /**
     * Порт SMTP-сервера
     * @var int = 25
     */
    private $_smtpPort = 25;

    /**
     * Использовать TLS или нет для подключения к SMTP-серверу
     * @var bool = false
     */
    private $_smtpTls = false;

    /**
     * Имя пользователя, которое используется для подключения к SMTP-серверу
     * @var string
     */
    private $_smtpUsername = '';

    /**
     * Пароль, который используется для подключения к SMTP-серверу
     * @var string
     */
    private $_smtpPassword = '';

    /**
     * Имя почтового ящика, от которого идет отправка письма
     * @var string = null
     */
    private $_fromName = null;

    /**
     * Email ящика, от которого идет отправка письма
     * @var string = null
     */
    private $_fromEmail = null;

    /**
     * Имя почтового ящика, который занимается пересылкой письма. Используем
     * тогда, когда нужно отправить имейл от имену кого-то другого
     * @var string = null
     */
    private $_envelopeName = null;

    /**
     * Email ящика, от которого идет отправка письма. Используем
     * тогда, когда нужно отправить имейл от имену кого-то другого
     * @var string = null
     */
    private $_envelopeEmail = null;

    /**
     * Email ящика, от которого идет отправка письма указаный самим пользователем
     * @var string = null
     */
    private $_userMail = null;

    /**
     * Массив email адресов, на которые будут отправляться письма
     * @var array
     */
    private $_emails = array();

    /**
     * Тема письма
     * @var string = null
     */
    private $_subject = null;

    /**
     * Текст письма
     * @var string = null
     */
    private $_body = null;


    /**
     * Конструктор класса, в который можно передать массив параметров
     * подключения к SMTP серверу.
     * Массив должен быть такого вида:
     *    array(
     *    'host'     => '',
     *    'port'     => '',
     *    'tls'      => 'false',
     *    'username' => '',
     *    'password' => '',
     *  )
     *
     * @param array $params = array() ОПЦИОНАЛЬНО параметры SMTP сервера
     *
     * @return Mail
     */
    public function __construct(array $params = array())
    {
        $this->setSmtpParams($params);
    }

    /**
     * Метод устанавливает адрес SMTP-сервера
     *
     * @param string $host - Адрес SMTP-сервера
     *
     * @return Mail
     */
    public function setSmtpHost($host)
    {
        $this->_smtpHost = $host;

        return $this;
    }

    /**
     * Метод возвращает адрес SMTP-сервера
     *
     * @return null|string
     */
    public function getSmtpHost()
    {
        return $this->_smtpHost;
    }

    /**
     * Метод устанавливает порт SMTP-сервера
     *
     * @param int $port - Порт SMTP-сервера
     *
     * @return Mail
     */
    public function setSmtpPort($port)
    {
        $this->_smtpPort = $port;

        return $this;
    }

    /**
     * Метод возвращает номер порта SMTP-сервера
     *
     * @return null|int
     */
    public function getSmtpPort()
    {
        return $this->_smtpPort;
    }

    /**
     * Метод устанавливает признак того, использовать TLS для подклюения к
     * SMTP-серверу или нет
     *
     * @param boolean $enabled - Признак того, использовать TLS или нет
     *
     * @return Mail
     */
    public function setSmtpTls($enabled)
    {
        $this->_smtpTls = $enabled;

        return $this;
    }

    /**
     * Метод возвращает признак того, использовать TLS для подклюения к
     * SMTP-серверу или нет
     *
     * @return null|boolean
     */
    public function getSmtpTls()
    {
        return $this->_smtpTls;
    }

    /**
     * Метод устанавливает имя пользователя, которое используется при
     * подключении к SMTP-серверу
     *
     * @param string $username - Имя пользователя
     *
     * @return Mail
     */
    public function setSmtpUsername($username)
    {
        $this->_smtpUsername = $username;

        return $this;
    }

    /**
     * Метод возвращает имя пользователя, которое используется при
     * подключении к SMTP-серверу
     *
     * @return null|string
     */
    public function getSmtpUsername()
    {
        return $this->_smtpUsername;
    }

    /**
     * Метод устанавливает пароль пользователя, который используется при
     * подключении к SMTP-серверу
     *
     * @param string $password - Пароль
     *
     * @return Mail
     */
    public function setSmtpPassword($password)
    {
        $this->_smtpPassword = $password;

        return $this;
    }

    /**
     * Метод возвращает пароль пользователя, который используется при
     * подключении к SMTP-серверу
     *
     * @return null|string
     */
    public function getSmtpPassword()
    {
        return $this->_smtpPassword;
    }

    /**
     * Метод устанавливает параметры подключения к SMTP серверу.
     * Массив должен быть такого вида:
     *    array(
     *    'host'     => '',
     *    'port'     => '',
     *    'tls'      => 'false',
     *    'username' => '',
     *    'password' => '',
     *  )
     *
     * @param array $params - Параметры SMTP сервера
     *
     * @return Mail
     */
    public function setSmtpParams(array $params)
    {
        if (isset($params['host'])) {
            $this->setSmtpHost($params['host']);
        }

        if (isset($params['port'])) {
            $this->setSmtpPort($params['port']);
        }

        if (isset($params['tls'])) {
            $this->setSmtpTls($params['tls']);
        }

        if (isset($params['username'])) {
            $this->setSmtpUsername($params['username']);
        }

        if (isset($params['password'])) {
            $this->setSmtpPassword($params['password']);
        }

        return $this;
    }

    /**
     * Метод возвращает параметры подключения к SMTP серверу
     *
     * @return array
     */
    public function getSmtpParams()
    {
        $params = array();

        if (!empty($this->_smtpHost)) {
            $params['host'] = $this->_smtpHost;
        }

        if (!empty($this->_smtpPort)) {
            $params['port'] = $this->_smtpPort;
        }

        if (!empty($this->_smtpTls)) {
            $params['tls'] = $this->_smtpTls;
        }

        if (!empty($this->_smtpUsername)) {
            $params['username'] = $this->_smtpUsername;
        }

        if (!empty($this->_smtpPassword)) {
            $params['password'] = $this->_smtpPassword;
        }

        return $params;
    }

    /**
     * Метод устанавливает email, от имени которого идет рассылка писем
     *
     * @param string $email
     * @param string $name = null - ОПЦИОНАЛЬНО Имя, связанное с email`ом
     *
     * @return Mail
     */
    public function setFromEmail($email, $name = null)
    {
        $this->_fromEmail = $email;
        $this->_fromName = $name;

        return $this;
    }

    /**
     * Метод возвращает email с его именем(если указано), от имени которого
     * будут отправляться письма
     *
     * @return null|string
     */
    public function getFromEmail()
    {
        $email = $this->_fromEmail;

        if (!empty($this->_fromName)) {
            $email = $this->_fromName . ' <' . $this->_fromEmail . '>';
        }

        return $email;
    }

    /**
     * Метод устанавливает email, кто занимается пересылкой письма. Если нужно
     * отправлять от имени от имени которого идет рассылка писем
     *
     * @param string $email
     * @param string $name = null - ОПЦИОНАЛЬНО Имя, связанное с email`ом
     *
     * @return Mail
     */
    public function setEnvelopeEmail($email, $name = null)
    {
        $this->_envelopeEmail = $email;
        $this->_envelopeName = $name;

        return $this;
    }

    /**
     * Метод возвращает email с его именем(если указано), кто занимается
     * пересылкой письма. Если нужно отправлять от имени от имени которого идет
     * рассылка писем
     *
     * @return null|string
     */
    public function getEnvelopeEmail()
    {
        $email = $this->_envelopeEmail;

        if (!empty($this->_envelopeName)) {
            $email = $this->_envelopeName . ' <' . $this->_envelopeEmail . '>';
        }

        return $email;
    }

    /**
     * Метод устанавливает один или несколько email`ов, на которые будут
     * отправляться письма
     *
     * @param string|array $email - Email или массив email`ов
     *
     * @return Mail
     */
    public function addEmail($email)
    {
        if (is_array($email)) {
            $this->_emails = array_merge($this->_emails, $email);
        } else {
            $this->_emails[] = $email;
        }

        return $this;
    }

    /**
     * Метод удаляет указанный email
     *
     * @param string $email
     *
     * @return Mail
     */
    public function removeEmail($email)
    {
        unset($this->_emails[$email]);

        return $this;
    }

    /**
     * Метод удаляет все email`ы
     *
     * @return Mail
     */
    public function clearEmails()
    {
        $this->_emails = array();

        return $this;
    }

    /**
     * Метод возвращает email(или массив email`ов) на которые будет отправляться
     * почта
     *
     * @return array|string
     */
    public function getEmail()
    {
        if (1 == count($this->_emails)) {
            return $this->_emails[0];
        }

        return $this->_emails;
    }

    /**
     * Метод устанавливает тему письма
     *
     * @param string $subject - Тема
     *
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;

        return $this;
    }

    /**
     * Метод возвращает тему письма
     *
     * @return null|string
     */
    public function getSubject()
    {
        return $this->_subject;
    }

    /**
     * Метод устанавливает текст письма
     *
     * @param string $text - Текст
     *
     * @return Mail
     */
    public function setText($text)
    {
        $this->_body = $text;

        return $this;
    }

    /**
     * Метод возвращает текст письма
     *
     * @return string
     */
    public function getText()
    {
        return $this->_body;
    }

    /**
     * Метод отправляет письмо
     *
     * @return bool|Mail
     *
     * @throws Mail\Exception
     */
    public function send()
    {
        $crlf = "\r\n";

        /** @noinspection PhpUndefinedClassInspection */
        $mime = new \Mail_mime($crlf);
        /** @noinspection PhpUndefinedClassInspection */
        @$mail =& \Mail::factory('smtp', array('host' => $this->getSmtpHost()));

        if (empty($this->_emails)) {
            throw new Exception('Не указан email');
        }

        set_error_handler('throwException', E_ERROR | E_WARNING | E_PARSE);
        foreach ($this->_emails as $email) {
            if (empty($email)) {
                continue;
            }

            $headers = array(
                'From' => $this->getFromEmail(),
                'To' => $email,
                'Subject' => $this->getSubject(),
                'Reply-To' => $this->getFromEmail(),
            );

            // Если письмо отправляется от имени другого имейла
            if ( null != $this->getEnvelopeEmail() ) {
                $headers['X-Envelope-From'] = $this->getEnvelopeEmail();
                $headers['Sender'] = $this->getEnvelopeEmail();
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $mime->setHTMLBody($this->getText());

            /** @noinspection PhpUndefinedMethodInspection */
            $body = $mime->get(array(
                    "html_charset" => "utf-8",
                    "text_encoding" => "8bit",
                    "head_charset" => "utf-8")
            );

            /** @noinspection PhpUndefinedMethodInspection */
            $headers = $mime->headers($headers);
            /** @noinspection PhpUndefinedMethodInspection */
            $status = $mail->send($email, $headers, $body);

            /** @noinspection PhpUndefinedClassInspection */
            if (@\PEAR::isError($status)) {
                return false;
            }
        }

        return $this;
    }
}
