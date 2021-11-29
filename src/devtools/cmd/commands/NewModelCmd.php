<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\contents\validation\ValidatorsManager;
use Ubiquity\controllers\Startup;
use Ubiquity\db\utils\DbTypes;
use Ubiquity\devtools\cmd\commands\traits\DbCheckTrait;
use Ubiquity\devtools\cmd\Console;
use Ubiquity\devtools\cmd\ConsoleFormatter;
use Ubiquity\devtools\utils\arrays\ClassicArray;
use Ubiquity\devtools\utils\arrays\ReflectArray;
use Ubiquity\domains\DDDManager;
use Ubiquity\exceptions\DAOException;
use Ubiquity\exceptions\UbiquityException;
use Ubiquity\orm\creator\Member;
use Ubiquity\orm\creator\Model;
use Ubiquity\orm\DAO;
use Ubiquity\orm\OrmUtils;
use Ubiquity\orm\reverse\DatabaseReversor;
use Ubiquity\db\reverse\DbGenerator;
use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\cmd\Screen;
use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UFileSystem;
use Ubiquity\utils\base\UString;

class NewModelCmd extends AbstractCmd {
	use DbCheckTrait;

	const CACHE_KEY = 'new-models/';

	private static string $originalModelName;

	private static string $tableName;

	private static string $defaultPk;

	private static array $pks = [];

	private static array $fields = [];

	private static function getModelNamespace($domain, $dbOffset) {
		$modelNS = Startup::getNS('models');
		if ($dbOffset !== '' && $dbOffset !== 'default') {
			$modelNS .= $dbOffset . '\\';
		}
		return $modelNS;
	}

	private static function updateFirstPk() {
		if (isset(self::$defaultPk) && self::$defaultPk != '') {
			self::$fields[self::$defaultPk] = [
				'Type' => 'int(11)',
				'Nullable' => 'false'
			];
			self::addPk(self::$defaultPk);
		}
	}

	private static function setFieldsOrder() {
		$result = [];
		$fields = self::$fields;
		foreach (self::$pks as $pk) {
			$result[$pk] = $fields[$pk];
			unset($fields[$pk]);
		}
		foreach ($fields as $field => $fieldInfos) {
			$result[$field] = $fieldInfos;
		}
		self::$fields = $result;
	}

	private static function addPk($pk) {
		if (! \in_array($pk, self::$pks)) {
			self::$pks[] = $pk;
		}

		if (! isset(self::$fields[$pk])) {
			echo ConsoleFormatter::showMessage("$pk is not in field list", 'warning', 'Add primary keys');
			$q = Console::question("Would you like to add $pk in field list?", [
				'yes',
				'no'
			]);
			if (Console::isYes($q)) {
				$fieldType = Console::question("Enter field type : ");
				self::addFields($pk, $fieldType, '');
			}
		}
	}

	private static function updatePks($pks) {
		$pks = \explode(',', $pks);
		self::$pks = [];
		self::updateFirstPk();
		foreach ($pks as $pk) {
			self::addPk($pk);
		}
	}

	private static function getFieldNames() {
		if (\is_array(self::$fields)) {
			return \array_keys(self::$fields);
		}
		return [];
	}

	private static function addFields($fields, $fieldTypes, $nullables) {
		$fields = \explode(',', $fields);
		$fieldTypes = \explode(',', $fieldTypes);
		$nullables = \explode(',', $nullables);
		foreach ($fields as $index => $field) {
			$field = \trim($field);
			if ($field != '') {
				$nullable = \array_search($field, $nullables) !== false;
				self::$fields[$field] = [
					'Type' => $fieldTypes[$index] ?? DbTypes::DEFAULT_TYPE,
					'Nullable' => $nullable ? 'true' : 'false'
				];
			}
		}
	}

	private static function generateClass($className, $namespace, $dbOffset) {
		$memberAccess = 'private';
		self::updateFirstPk();
		self::setFieldsOrder();
		$engine = CacheManager::getAnnotationsEngineInstance();
		$class = new Model($engine, \lcfirst($className), $namespace, $memberAccess);
		$class->setTable(self::$tableName ?? (lcfirst($className)));
		$class->setDatabase($dbOffset);
		$fieldsInfos = self::$fields;
		$class->setSimpleMembers(self::getSimpleMembers());
		$keys = self::$pks;
		foreach ($fieldsInfos as $field => $info) {
			$member = new Member($class, $engine, $field, $memberAccess);
			if (\in_array($field, $keys)) {
				$member->setPrimary();
			}
			$member->setDbType($info);
			$member->addValidators();
			$member->setTransformer();
			$class->addMember($member);
		}
		$class->addMainAnnots();
		self::createClass($class, $namespace);
	}

	private static function getSimpleMembers() {
		$members = [];
		foreach (self::$fields as $name => $fieldInfos) {
			if ($fieldInfos['Type'] !== 'mixed') {
				$members[] = $name;
			}
		}
		return $members;
	}

	private static function store($className) {
		$content = [
			'defaultPk' => (self::$defaultPk ?? ''),
			'pks' => self::$pks,
			'fields' => self::$fields
		];
		CacheManager::$cache->store(self::CACHE_KEY . $className, $content);
	}

	private static function loadFromCache($className) {
		$result = CacheManager::$cache->fetch(self::CACHE_KEY . $className);
		self::$fields = $result['fields'];
		self::$defaultPk = $result['defaultPk'];
		self::$pks = $result['pks'];
	}

	private static function reloadFromExistingClass($completeClassName) {
		if (CacheManager::modelCacheExists($completeClassName)) {
			$metaDatas = CacheManager::getOrmModelCache($completeClassName);
			self::checkAutoInc($completeClassName);
			self::$pks = \array_keys($metaDatas['#primaryKeys']);
			$memberNames = \array_keys($metaDatas['#fieldNames']);
			$types = $metaDatas['#fieldTypes'];
			$nullables = $metaDatas['#nullable'];
			$fields = [];
			foreach ($memberNames as $memberName) {
				$fields[$memberName] = [
					'Type' => $types[$memberName] ?? 'mixed',
					'Nullable' => in_array($memberName, $nullables) ? 'true' : 'false'
				];
			}
			self::$fields = $fields;
			self::$tableName = $metaDatas['#tableName'];
			return true;
		}
		return false;
	}

	private static function checkAutoInc($completeClassName) {
		$validationMetas = ValidatorsManager::getCacheInfo($completeClassName);
		foreach ($validationMetas as $member => $validators) {
			foreach ($validators as $infos) {
				if ($infos['type'] === 'id') {
					if (($infos['constraints']['autoinc'] ?? false) === true) {
						self::$defaultPk = $member;
						return;
					}
				}
			}
		}
	}

	private static function createClass(Model $model, $namespace) {
		$className = $model->getName();
		$modelsDir = UFileSystem::getDirFromNamespace($namespace);
		echo ConsoleFormatter::showInfo("Creating the {$className} class");
		$classContent = $model->__toString();
		if (UFileSystem::save($modelsDir . \DS . $model->getSimpleName() . '.php', $classContent)) {
			echo ConsoleFormatter::showMessage("Class $className created with success!", 'success', 'Model creation');
			self::store(self::$originalModelName);
			$rep = Console::question('Do you want to re-init models cache?', [
				'yes',
				'no'
			]);
			if (Console::isYes($rep)) {
				$config = Startup::$config;
				CacheManager::initModelsCache($config, false, true);
				echo ConsoleFormatter::showMessage('Models cache updated', 'succes', 'Cache reinitialization');
			}
		} else {
			throw new UbiquityException("$className not generated");
		}
		die();
	}

	public static function run(&$config, $options, $what) {
		self::$originalModelName = $what;
		$domain = self::updateDomain($options);
		$dbOffset = self::getOption($options, 'd', 'database', 'default');
		self::checkDbOffset($config, $dbOffset);
		$domainStr = '';
		if ($domain != '') {
			$domainStr = " in the domain <b>$domain</b>";
		}

		CacheManager::start($config);
		$modelName = $what;
		$modelCompleteName = self::getModelNamespace($domain, $dbOffset) . \ucfirst($modelName);
		if (CacheManager::$cache->exists(self::CACHE_KEY . $modelName) && ! \class_exists($modelCompleteName)) {
			$rep = Console::question("A model with this name was already created.\nWould you like to reload it from cache?", [
				'yes',
				'no'
			]);
			if (Console::isYes($rep)) {
				self::loadFromCache(self::$originalModelName);
			}
		}

		$fields = '';
		$checkExisting = [];
		do {
			$restrict = false;
			$modelCompleteName = self::getModelNamespace($domain, $dbOffset) . \ucfirst($modelName);
			$tableName = self::$tableName ?? (\lcfirst($modelName));
			echo ConsoleFormatter::showMessage("Creation: <b>$modelCompleteName</b>", 'info', 'New model');

			$caseChangeDbOffset = "Change dbOffset [<b>$dbOffset</b>]";
			$caseChangeActiveDomain = "Change active Domain [<b>$domain</b>]";
			if (\class_exists($modelCompleteName) && ! \in_array($modelCompleteName, $checkExisting)) {
				$checkExisting[] = $modelCompleteName;
				echo ConsoleFormatter::showMessage("The class <b>$modelCompleteName</b> already exists!", 'warning', 'Update model');
				$rep = Console::question("Would you like to modify the existing class?", [
					'yes',
					'no'
				]);
				if (Console::isYes($rep)) {
					if (self::reloadFromExistingClass($modelCompleteName)) {
						echo ConsoleFormatter::showMessage("Loading infos for class <b>$modelCompleteName</b> from DAO cache.", 'info', 'Update model');
					} else {
						echo ConsoleFormatter::showMessage("No cache infos for <b>$modelCompleteName</b>.", 'error', 'Update model');
						break;
					}
				} else {
					$choices = [
						'Change class name',
						$caseChangeDbOffset,
						$caseChangeActiveDomain,
						'Quit'
					];
					$restrict = true;
				}
			}
			if (! $restrict) {
				$fields = \implode(',', self::getFieldNames());
				$caseAddFields = "Add fields [<b>$fields</b>]";
				$caseAddDefaultPk = "Add default auto-inc primary key [<b>" . (self::$defaultPk ?? '') . "</b>]";
				$caseChangeTableName = "Change table name [<b>$tableName</b>]";
				$choices = [
					$caseAddFields,
					$caseAddDefaultPk,
					'Add primary keys',
					$caseChangeTableName,
					$caseChangeDbOffset,
					$caseChangeActiveDomain,
					'Generate class',
					'Quit'
				];
			}
			$rep = Console::question('Select your choices:', $choices);

			switch ($rep) {

				case 'Change class name':
					$modelName = Console::question("Enter model name: ");
					break;

				case $caseChangeTableName:
					$tbl = Console::question('Enter table name:');
					self::$tableName = ($tbl == '') ? null : $tbl;
					break;

				case $caseAddFields:
					$field = Console::question("Enter field names: ");
					if ($field != '') {
						$fieldTypes = Console::question("Enter field types : ");
						$nullables = Console::question("Nullable fields : ");
						self::addFields($field, $fieldTypes, $nullables);
					}
					break;

				case 'Add primary keys':
					$pks = Console::question('Enter primary keys: ');
					self::updatePks($pks);
					break;

				case $caseAddDefaultPk:
					self::$defaultPk = Console::question('Primary key name: ');
					break;

				case $caseChangeDbOffset:
					$dbOffset = Console::question('Database offset: ');
					self::checkDbOffset($config, $dbOffset);
					break;

				case $caseChangeActiveDomain:
					$newDomain = Console::question('Domain: ');
					$domain = self::updateDomain([
						'o' => $newDomain
					]);
					if ($domain == '') {
						DDDManager::resetActiveDomain();
					}
					break;

				case 'Generate class':
					self::generateClass($modelName, self::getModelNamespace($domain, $dbOffset), $dbOffset);
					break;

				default:
					echo ConsoleFormatter::showInfo('Operation terminated, Bye!');
			}
		} while ($rep !== 'Quit');
	}
}
