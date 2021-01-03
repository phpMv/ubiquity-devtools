<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\devtools\cmd\Command;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\security\data\EncryptionManager;
use Ubiquity\security\data\Encryption;

/**
 * Generate a new key for encryption.
 * Ubiquity\devtools\cmd\commands$EncyptNewKey
 *
 * @author jc
 * @version 1.0.0
 *
 */
class EncryptNewKey extends AbstractCmd {

	public static function run(&$config, $what) {
		if (! \class_exists(\Ubiquity\security\data\EncryptionManager::class, true)) {
			$answer = Console::question("\n\tUbiquity-security is not available. Would you like to install it now with composer?", [
				"y",
				"n"
			]);
			if (Console::isYes($answer)) {
				\system('composer require phpmv/ubiquity-security');
			} else {
				echo ConsoleFormatter::showMessage('aborted operation!', 'warning', 'new-key');
				die();
			}
		}
		switch ($what) {
			case '128':
				$cypher = Encryption::AES128;
				break;
			case '192':
				$cypher = Encryption::AES192;
				break;
			case '256':
				$cypher = Encryption::AES256;
				break;
			default:
				$cypher = Encryption::AES128;
				break;
		}
		EncryptionManager::start($config, $cypher);

		echo ConsoleFormatter::showMessage("Encryption key is generated with {$cypher}!\nCheck if EncryptionManager is started in app/config/services.php with:\n\\Ubiquity\\security\\data\\EncryptionManager::start(\$config,'{$cypher}');", 'success', 'new-key');
	}
}

