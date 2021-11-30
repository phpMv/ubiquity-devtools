<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\contents\validation\ValidatorsManager;
use Ubiquity\controllers\Startup;
use Ubiquity\db\utils\DbTypes;
use Ubiquity\devtools\cmd\commands\popo\NewModel;
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

	private static array $allModels = [];

	private static string $currentModelName = '';

	private static ?NewModel $currentModel;

	private static function getModelNamespace($domain, $dbOffset) {
		$modelNS = Startup::getNS('models');
		if ($dbOffset !== '' && $dbOffset !== 'default') {
			$modelNS .= $dbOffset . '\\';
		}
		return $modelNS;
	}

	private static function getNewModel(string $modelName, bool $updateCurrent = true): NewModel {
		if (! isset(self::$allModels[$modelName])) {
			self::$allModels[$modelName] = $m = new NewModel($modelName);
			$m->setDefaultPk('id');
		}
		if ($updateCurrent) {
			self::$currentModelName = $modelName;
			return self::$currentModel = self::$allModels[$modelName];
		}
		return self::$allModels[$modelName];
	}

	private static function getAllModelsAsString(): string {
		$result = [];
		foreach (self::$allModels as $name => $model) {
			if (\strtolower($name) === \strtolower(self::$currentModelName)) {
				$result[] = "<b>$name</b>";
			} else {
				$result[] = $name;
			}
		}
		return \implode(',', $result);
	}

	private static function addPk($pk) {
		$class = self::$currentModel;
		$class->addPk($pk);

		if (! $class->hasField($pk)) {
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

	private static function getFieldNames() {
		return self::$currentModel->getFieldNames();
	}

	private static function addFields($fields, $fieldTypes, $nullables) {
		$fields = \explode(',', $fields);
		$fieldTypes = \explode(',', $fieldTypes);
		$nullables = \explode(',', $nullables);
		$newModel = self::$currentModel;
		foreach ($fields as $index => $field) {
			$field = \trim($field);
			if ($field != '') {
				$nullable = \array_search($field, $nullables) !== false;
				$newModel->addField($field, [
					'Type' => $fieldTypes[$index] ?? DbTypes::DEFAULT_TYPE,
					'Nullable' => $nullable ? 'true' : 'false'
				]);
			}
		}
	}

	private static function generateClasses(string $namespace, string $dbOffset) {
		$messages = [];
		$classes = [];
		foreach (self::$allModels as $name => $newModel) {
			$class = $newModel->generateClass($name, $namespace, $dbOffset);
			$classes[$name] = $class;
		}

		self::createRelations($classes, $namespace);

		foreach (self::$allModels as $name => $newModel) {
			$class = $classes[$name];
			$msg = self::createClass($class, $newModel, $name, $namespace);
			if ($msg['type'] === 'success') {
				$messages['success'][] = $msg['message'];
			} else {
				$messages['error'][] = $msg['message'];
			}
		}
		self::showMessages('success', $messages);
		self::showMessages('error', $messages);
		$rep = Console::question('Do you want to re-init models cache?', [
			'yes',
			'no'
		]);
		if (Console::isYes($rep)) {
			$config = Startup::$config;
			CacheManager::initModelsCache($config, false, true);
			echo ConsoleFormatter::showMessage('Models cache updated', 'succes', 'Cache reinitialization');
		}
		die();
	}

	private static function createRelations($classes, $namespace) {
		foreach (self::$allModels as $name => $newModel) {
			$manyToOnes = $newModel->getManyToOne();
			$oneToManys = $newModel->getOneToMany();
			$manyToManys = $newModel->getManyToMany();
			$class = $classes[$name];
			foreach ($manyToOnes as $member => $manyToOne) {
				$class->addManyToOne($member, $manyToOne['fkField'], $namespace . $manyToOne['className'], $member);
			}
			foreach ($oneToManys as $member => $oneToMany) {
				$class->addOneToMany($member, $oneToMany['mappedBy'], $namespace . $oneToMany['className'], $member);
			}

			foreach ($manyToManys as $member => $manyToMany) {
				$class->addManyToMany($member, $namespace . $manyToMany['otherClassName'], $manyToMany['otherMember'], $manyToMany['joinTable'], $manyToMany['joinColumn'], $manyToMany['otherJoinColumn']);
			}
		}
	}

	private static function showMessages(string $type, array $messages): void {
		if (isset($messages[$type])) {
			echo ConsoleFormatter::showMessage(\implode(PHP_EOL, $messages[$type]), $type, 'Classes creation');
		}
	}

	private static function store(string $className, NewModel $newModel): void {
		$content = [
			'defaultPk' => ($newModel->getDefaultPk() ?? ''),
			'pks' => $newModel->getPks(),
			'fields' => $newModel->getFields()
		];
		CacheManager::$cache->store(self::CACHE_KEY . $className, $content);
	}

	private static function loadFromCache(string $className): void {
		$result = CacheManager::$cache->fetch(self::CACHE_KEY . $className);
		$newModel = self::$currentModel;
		$newModel->setFields($result['fields']);
		$newModel->setDefaultPk($result['defaultPk']);
		$newModel->setPks($result['pks']);
	}

	private static function reloadFromExistingClass(string $completeClassName): bool {
		if (CacheManager::modelCacheExists($completeClassName)) {
			$newModel = self::$currentModel;
			$metaDatas = CacheManager::getOrmModelCache($completeClassName);
			self::checkAutoInc($newModel, $completeClassName);
			$newModel->setPks(\array_keys($metaDatas['#primaryKeys']));
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
			$newModel->setFields($fields);
			$newModel->setTableName($metaDatas['#tableName']);
			return true;
		}
		return false;
	}

	private static function checkAutoInc(NewModel $newModel, string $completeClassName) {
		$validationMetas = ValidatorsManager::getCacheInfo($completeClassName);
		foreach ($validationMetas as $member => $validators) {
			foreach ($validators as $infos) {
				if ($infos['type'] === 'id') {
					if (($infos['constraints']['autoinc'] ?? false) === true) {
						$newModel->setDefaultPk($member);
						return;
					}
				}
			}
		}
	}

	private static function createClass(Model $model, NewModel $newModel, string $modelName, string $namespace): array {
		$className = $model->getName();
		$modelsDir = UFileSystem::getDirFromNamespace($namespace);
		echo ConsoleFormatter::showInfo("Creating the {$className} class");
		$classContent = $model->__toString();
		self::store($modelName, $newModel);
		if (UFileSystem::save($modelsDir . \DS . $model->getSimpleName() . '.php', $classContent)) {
			return [
				'type' => 'success',
				'message' => "Class $className created with success!"
			];
		}
		return [
			'type' => 'error',
			'message' => "Class $className not generated!"
		];
	}

	public static function run(&$config, $options, $what) {
		$domain = self::updateDomain($options);
		$dbOffset = self::getOption($options, 'd', 'database', 'default');
		self::checkDbOffset($config, $dbOffset);
		$domainStr = '';
		if ($domain != '') {
			$domainStr = " in the domain <b>$domain</b>";
		}

		CacheManager::start($config);
		$modelName = \ucfirst($what);
		$newModel = self::getNewModel($modelName);
		$modelCompleteName = self::getModelNamespace($domain, $dbOffset) . $modelName;
		if (CacheManager::$cache->exists(self::CACHE_KEY . $modelName) && ! \class_exists($modelCompleteName)) {
			$rep = Console::question("A model with this name was already created.\nWould you like to reload it from cache?", [
				'yes',
				'no'
			]);
			if (Console::isYes($rep)) {
				self::loadFromCache($modelName);
			}
		}

		$fields = '';
		$checkExisting = [];
		do {
			$restrict = false;
			$modelCompleteName = self::getModelNamespace($domain, $dbOffset) . $modelName;
			$tableName = $newModel->getTableName() ?? (\lcfirst($modelName));
			echo ConsoleFormatter::showMessage("Creation: <b>$modelCompleteName</b>", 'info', 'New model');

			$caseChangeDbOffset = "Change dbOffset [<b>$dbOffset</b>]";
			$caseChangeActiveDomain = "Change active Domain [<b>$domain</b>]";
			$caseSwitchModel = "Add/switch to model [" . self::getAllModelsAsString() . "]";
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
			$fields = \implode(',', $newModel->getFieldNames());
			$caseAddFields = "Add fields [<b>$fields</b>]";
			$caseAddDefaultPk = "Add default auto-inc primary key [<b>" . ($newModel->getDefaultPk() ?? '') . "</b>]";
			$caseChangeTableName = "Change table name [<b>$tableName</b>]";

			if (! $restrict) {
				$choices = [
					$caseAddFields,
					$caseAddDefaultPk,
					'Add primary keys',
					'Add relations',
					$caseChangeTableName,
					$caseChangeDbOffset,
					$caseChangeActiveDomain,
					$caseSwitchModel,
					'Generate classes',
					'Quit'
				];
			}
			$rep = Console::question('Select your choices:', $choices);

			switch ($rep) {

				case 'Change class name':
					$modelName = Console::question("Enter model name: ");
					$modelName = \ucfirst($modelName);
					unset(self::$allModels[self::$currentModelName]);
					self::$allModels[$modelName] = self::$currentModel;
					self::$currentModelName = $modelName;
					break;

				case $caseChangeTableName:
					$tbl = Console::question('Enter table name:');
					$newModel->setTableName(($tbl == '') ? null : $tbl);
					break;

				case $caseAddFields:
					$field = Console::question("Enter field names: ");
					if ($field != '') {
						$fieldTypes = Console::question("Enter field types : ");
						$nullables = Console::question("Nullable fields : ");
						self::addFields($field, $fieldTypes, $nullables);
					}
					break;

				case $caseSwitchModel:
					$modelName = Console::question('Enter an existing or a new model name:');
					$modelName = \ucfirst($modelName);
					$newModel = self::getNewModel($modelName);
					break;

				case 'Add primary keys':
					$pks = Console::question('Enter primary keys: ');
					$newModel->updatePks($pks);
					break;

				case 'Add relations':
					$rType = Console::question('Type: ', [
						'manyToOne',
						'oneToMany',
						'manyToMany'
					]);
					self::addRelation($rType, $newModel);
					break;

				case $caseAddDefaultPk:
					$newModel->setDefaultPk(Console::question('Primary key name: '));
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

				case 'Generate classes':
					self::generateClasses(self::getModelNamespace($domain, $dbOffset), $dbOffset);
					break;

				default:
					echo ConsoleFormatter::showInfo('Operation terminated, Bye!');
			}
		} while ($rep !== 'Quit');
	}

	private static function addRelation(string $rType, NewModel $newModel) {
		$modelName = $newModel->getOriginalModelName();

		switch ($rType) {
			case 'manyToOne':
				$fkClass = Console::question('Foreign member className:', \array_keys(self::$allModels), [
					'ignoreCase' => true
				]);
				$otherModel = self::getNewModel($fkClass, false);
				$otherModelName = $otherModel->getOriginalModelName();

				$fkField = Console::question('Foreign key name:', null, [
					'default' => 'id' . \ucfirst($otherModelName)
				]);
				$member = Console::question('Member name:', null, [
					'default' => \lcfirst($otherModelName)
				]);
				$manyMember = Console::question("OneToMany member name in $otherModelName:", null, [
					'default' => \lcfirst($modelName) . 's'
				]);

				$newModel->addManyToOne($member, $fkField, $otherModelName);
				$otherModel->addOneToMany($manyMember, $member, $modelName);
				break;
			case 'manyToMany':
				$fkClass = Console::question('Associated className:', \array_keys(self::$allModels), [
					'ignoreCase' => true
				]);
				$otherModel = self::getNewModel($fkClass, false);
				$otherModelName = $otherModel->getOriginalModelName();

				$member = Console::question("Associated member name in $modelName:", null, [
					'default' => \lcfirst($otherModelName) . 's'
				]);
				$otherAssociatedFk = Console::question("Associated fk name for $otherModelName:", null, [
					'default' => 'id' . \ucfirst($otherModelName)
				]);

				$otherMember = Console::question("Associated member name in $otherModelName:", null, [
					'default' => \lcfirst($modelName) . 's'
				]);
				$associatedFk = Console::question("Associated fk name for $modelName:", null, [
					'default' => 'id' . \ucfirst($modelName)
				]);
				$jointable = Console::question('Jointable:', null, [
					'default' => \lcfirst($modelName) . '_' . \lcfirst($otherModelName) . 's'
				]);

				$joinColumn = [
					'name' => $associatedFk,
					'referencedColumnName' => $newModel->getFirstPk()
				];
				$otherJoinColumn = [
					'name' => $otherAssociatedFk,
					'referencedColumnName' => $otherModel->getFirstPk()
				];

				$newModel->addManyToMany($member, $otherModelName, $otherMember, $jointable, $joinColumn, $otherJoinColumn);
				$otherModel->addManyToMany($otherMember, $modelName, $member, $jointable, $otherJoinColumn, $joinColumn);

				break;
			case 'oneToMany':
				$fkClass = Console::question('Associated member className:', \array_keys(self::$allModels), [
					'ignoreCase' => true
				]);
				$otherModel = self::getNewModel($fkClass, false);
				$otherModelName = $otherModel->getOriginalModelName();

				$fkField = Console::question('Foreign key name:', null, [
					'default' => 'id' . \ucfirst($modelName)
				]);
				$member = Console::question('Member name:', null, [
					'default' => \lcfirst($otherModelName) . 's'
				]);
				$mappedBy = Console::question("MappedBy member name in $otherModelName:", null, [
					'default' => \lcfirst($modelName)
				]);

				$newModel->addOneToMany($member, $mappedBy, $otherModelName);
				$otherModel->addManyToOne($mappedBy, $fkField, $modelName);
				break;
		}
	}
}
