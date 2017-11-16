<?php
use \Uwin\Profiler;
use \Uwin\Registry;
use \Uwin\Autoloader;
use \Uwin\Config\Xml;
use \Uwin\Db;
use \Uwin\Mail;

const PROJECT_NAME     = 'avtoclassika.com',
      STATIC_SERVER_ID = 's1';

define('SERVER_NAME', 'avtoclassika.com');
define('COOKIE_HOST', '.' . SERVER_NAME);
define('DIR_SEP', DIRECTORY_SEPARATOR);
define('PATH_SEP', PATH_SEPARATOR);

// Установка локали по-умолчанию
setlocale(LC_ALL, 'ru_RU.Utf-8');
// Установка временной зоны по-умолчанию
date_default_timezone_set("Europe/Kiev");

mb_internal_encoding('UTF-8');

/**
 * Инициализация и первоначальная настройка профайлера
 * @noinspection PhpIncludeInspection
 */
include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'library' .
         DIRECTORY_SEPARATOR . 'uwin-framework/Uwin/' . DIRECTORY_SEPARATOR .'Profiler.php';

/** @noinspection PhpIncludeInspection */
require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'application' .
	     DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'init.php';

/** @noinspection PhpIncludeInspection */
require 'Uwin' . DIR_SEP . 'Autoloader.php';

Autoloader::register();

function getDbParams($config) {
    $xml = new Xml($config['path']['settings'] . 'general.xml', '/root/databases/default');

    return $xml->get();
}

function getSMTPparams($config) {
    $xml = new Xml($config['path']['settings'] . 'general.xml', '/root/mail/smtp');

    return $xml->get();
}

function sendMails($config) {
    $db = Db::db()->setDbParams( getDbParams($config) );
    $smtp = getSMTPparams($config);

	$emails = $db->query()->addSql('select mls_id_pk, mls_email, mls_subject as subject, mls_body as body,')
        ->addSql('mls_unsubscribe')
		->addSql('from mails_tbl')
		->addSql('join mailers_tbl on mlr_id_pk=mls_mlr_id_fk')
		->addSql('where (mls_status=$1) and mls_mlr_id_fk is not null')
		->addSql('order by mls_datetime desc, mls_id_pk desc')
		->addSql('limit 25')
		->addParam('wait')
		// ->addParam('error')
		->fetchResult(false);

	if ( empty($emails) ) {
		return true;
	}

    $global_settings = new Xml($config['path']['settings'] . 'general.xml', '/root');
    $global_settings = Registry::getInstance()
        ->getFlatArray( array('settings' => $global_settings->get()) );

    // Получаем имя и email от которого будут оправляться письма
    if ( false === $mailerName = $global_settings['settings_mail_name'] ) {
        $mailerName = null;
    }
    $mailerEmail = $global_settings['settings_mail_email'];

	foreach($emails as $email) {
    var_dump($email['mls_email']);
        // Отправляем почту на email зарегестрированного пользователя
        $mail = new Mail($smtp);
        $status = $mail->setFromEmail($mailerEmail, $mailerName)
             ->addEmail($email['mls_email'])
             ->setSubject($email['subject'])
             ->setText($email['body'])
      	     ->send();

        false === $status ? $status = 'error' : $status = 'send';

		$db->query()->addSql('update mails_tbl')
			->addSql('set mls_status=$1 where mls_id_pk=$2')
			->addParam($status)
			->addParam($email['mls_id_pk'])
			->execute();
	}

	return true;
}

/** @noinspection PhpUndefinedVariableInspection */
sendMails($config);
