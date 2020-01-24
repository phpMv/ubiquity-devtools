<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\mailer\MailerManager;

class SendMailQueueCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		\Ubiquity\cache\CacheManager::startProd($config);
		$index = $what;
		MailerManager::start();
		if (isset($index)) {
			if (\is_numeric($index)) {
				self::_sendMailQueue($index);
			} else {
				echo ConsoleFormatter::showMessage('Please specify an existing numerical index', 'error', 'Send mails from queue');
			}
		} else {
			$count = MailerManager::sendQueue();
			if ($count > 0) {
				MailerManager::saveQueue();
				echo ConsoleFormatter::showMessage($count . ' email(s) sent with success!', 'success', 'Send mails from Queue');
			} else {
				echo ConsoleFormatter::showMessage('No mail sent!', 'info', 'Send mails from Queue');
			}
		}
	}

	private static function _sendMailQueue($index) {
		if (MailerManager::sendQueuedMail(-- $index)) {
			MailerManager::saveQueue();
			echo ConsoleFormatter::showMessage('Email sent with success!', 'success', 'Send mail from Queue');
		} else {
			echo ConsoleFormatter::showMessage(MailerManager::getErrorInfo(), 'error', 'Send mail from Queue');
		}
	}
}

