<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\utils\FrameworkParts;
use Ubiquity\devtools\utils\arrays\ReflectArray;

class InfoMailerCmd extends AbstractCmd {

	public static function run(&$config, $options, $what) {
		$limit = self::getOption($options, 'l', 'limit');
		$offset = self::getOption($options, 'o', 'offset');

		$tbl = new ConsoleTable();

		switch ($what) {
			case 'queue':
				echo ConsoleFormatter::showMessage('Mailer queue (To send)');
				$mails = FrameworkParts::getMailerQueue($config);
				$fields = self::getOption($options, 'f', 'fields', 'num,shortname,subject,from,to');
				break;
			case 'dequeue':
				echo ConsoleFormatter::showMessage('Mailer deQueue (Sent)');
				$mails = FrameworkParts::getMailerDeQueue($config);
				$fields = self::getOption($options, 'f', 'fields', 'name,to,sentAt');
				break;
			default:
				echo ConsoleFormatter::showMessage('Mailer classes');
				$mails = FrameworkParts::getMailerClasses($config);
				$fields = self::getOption($options, 'f', 'fields', 'name,subject,from,to');
		}

		if ($limit != null || $offset != null) {
			$offset = $offset ? (int) $offset : 0;
			$limit = ($limit) ? (int) $limit : null;
			$mails = array_slice($mails, $offset, $limit);
		}
		$rArray = new ReflectArray();
		$rArray->setProperties(explode(",", $fields));
		$rArray->setObjects($mails);
		$tbl->setDatas($rArray->parse());
		echo $tbl->getTable();
		if ($rArray->hasMessages()) {
			echo ConsoleFormatter::showMessage(implode("\n", $rArray->getMessages()), 'error');
		}
		echo ConsoleFormatter::showInfo(sizeof($mails) . " mails in {$what}\n");
	}
}

