<?php
namespace Ubiquity\devtools\cmd\commands;

use Ubiquity\cache\CacheManager;
use Ubiquity\cache\ClassUtils;
use Ubiquity\contents\validation\ValidatorsManager;
use Ubiquity\controllers\Startup;
use Ubiquity\db\utils\DbTypes;
use Ubiquity\devtools\cmd\Command;
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
use Ubiquity\orm\parser\Reflexion;
use Ubiquity\orm\reverse\DatabaseReversor;
use Ubiquity\db\reverse\DbGenerator;
use Ubiquity\devtools\cmd\ConsoleTable;
use Ubiquity\devtools\cmd\Screen;
use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UFileSystem;
use Ubiquity\utils\base\UIntrospection;
use Ubiquity\utils\base\UString;

class NewModelCmd extends AbstractCmd {
	use DbCheckTrait;

	const CACHE_KEY = 'new-models/';

	private static array $allModels = [];

	private static string $currentModelName = '';

	private static ?NewModel $currentModel;

	private static $loadCurrentModels = false;

	private static ?string $defaultPkValue = null;

	private static function getModelNamespace($domain, $dbOffset) {
		$modelNS = Startup::getNS('models');
		if ($dbOffset !== '' && $dbOffset !== 'default') {
			$modelNS .= $dbOffset . '\\';
		}
		return $modelNS;
	}

	private static function getNewModel(string $modelName, bool $updateCurrent = true): NewModel {
		$modelName = \ucfirst($modelName);
		if (! isset(self::$allModels[$modelName])) {
			self::$allModels[$modelName] = new NewModel($modelName);
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
			$oName = $name;
			if ($model->isUpdated()) {
				$name .= '*';
			}
			if (\strtolower($oName) === \strtolower(self::$currentModelName)) {
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
			$q = Console::yesNoQuestion("Would you like to add $pk in field list?", [
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
		$fields = Console::explodeResponse($fields);
		$fieldTypes = Console::explodeResponse($fieldTypes);
		$nullables = Console::explodeResponse($nullables);
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
			if ($newModel->isUpdated()) {
				$class = $classes[$name];
				$msg = self::createClass($class, $newModel, $name, $namespace);
				if ($msg['type'] === 'success') {
					$messages['success'][] = $msg['message'];
				} else {
					$messages['error'][] = $msg['message'];
				}
			}
		}
		self::showMessages('success', $messages);
		self::showMessages('error', $messages);
		$rep = Console::yesNoQuestion('Do you want to re-init models cache?', [
			'yes',
			'no'
		]);
		if (Console::isYes($rep)) {
			global $argv;
			echo shell_exec($argv[0] . ' init-cache -t=models');
		}
		self::saveModelsInCache();
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
			'fields' => $newModel->getFields(),
			'manyToOne' => $newModel->getManyToOne(),
			'oneToMany' => $newModel->getOneToMany(),
			'manyToMany' => $newModel->getManyToMany()
		];
		CacheManager::$cache->store(self::CACHE_KEY . $className, $content);
	}

	private static function loadFromCache(string $className, ?NewModel $newModel = null): void {
		$result = CacheManager::$cache->fetch(self::CACHE_KEY . $className);
		$newModel ??= self::$currentModel;
		$newModel->setFields($result['fields']);
		$newModel->setDefaultPk($result['defaultPk']);
		$newModel->setPks($result['pks']);
		$newModel->setManyToOne($result['manyToOne'] ?? []);
		$newModel->setOneToMany($result['oneToMany'] ?? []);
		$newModel->setManyToMany($result['manyToMany'] ?? []);
		$newModel->setLoadedFromCache(true);
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
			self::reloadFromExistingClassRelations($newModel, $metaDatas);
			$newModel->setLoadedFromCache(true);
			$newModel->setLoaded(true);
			return true;
		}
		return false;
	}

	private static function reloadFromExistingClassRelations(NewModel $newModel, array $metaDatas) {
		$manyToOnes = $metaDatas['#manyToOne'] ?? [];
		$joinColumns = $metaDatas['#joinColumn'] ?? [];
		foreach ($manyToOnes as $manyToOne) {
			$joinColumn = $joinColumns[$manyToOne];
			$newModel->addManyToOne($manyToOne, $joinColumn['name'], ClassUtils::getClassSimpleName($joinColumn['className']));
		}

		$oneToManys = $metaDatas['#oneToMany'] ?? [];
		foreach ($oneToManys as $member => $oneToMany) {
			$newModel->addOneToMany($member, $oneToMany['mappedBy'], ClassUtils::getClassSimpleName($oneToMany['className']));
		}

		$manyToManys = $metaDatas['#manyToMany'] ?? [];
		$joinTables = $metaDatas['#joinTable'] ?? [];
		foreach ($manyToManys as $member => $manyToMany) {
			$jointable = $joinTables[$member];
			$joinColumn = $jointable['joinColumns'] ?? [];
			$inverseJoinColumn = $jointable['inverseJoinColumns'] ?? [];
			$newModel->addManyToMany($member, ClassUtils::getClassSimpleName($manyToMany['targetEntity']), $manyToMany['inversedBy'], $jointable['name'], $joinColumn, $inverseJoinColumn);
		}
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

	private static function loadModel(NewModel $newModel, string $modelName, string $namespace): bool {
		$modelCompleteName = $namespace . $modelName;

		if (self::$loadCurrentModels && ! $newModel->isLoadedFromCache()) {
			self::reloadFromExistingClass($modelCompleteName);
			return false;
		}
		$restrict = false;
		if (! $newModel->isLoaded()) {
			if (CacheManager::$cache->exists(self::CACHE_KEY . $modelName) && ! \class_exists($modelCompleteName)) {
				$rep = Console::yesNoQuestion("A model with the name <b>$modelName</b> was already created.\nWould you like to reload it from cache?", [
					'yes',
					'no'
				]);
				if (Console::isYes($rep)) {
					self::loadFromCache($modelName);
				}
			}
			if (\class_exists($modelCompleteName)) {
				echo ConsoleFormatter::showMessage("The class <b>$modelCompleteName</b> already exists!", 'warning', 'Update model');
				$rep = Console::yesNoQuestion('Would you like to modify the existing class?', [
					'yes',
					'no'
				]);
				if (Console::isYes($rep)) {
					if (self::reloadFromExistingClass($modelCompleteName)) {
						echo ConsoleFormatter::showMessage("Loading infos for class <b>$modelCompleteName</b> from DAO cache.", 'info', 'Update model');
					} else {
						echo ConsoleFormatter::showMessage("No cache infos for <b>$modelCompleteName</b>.", 'error', 'Update model');
						die();
					}
				} else {
					$restrict = true;
				}
			}
			$newModel->setLoaded(true);

			if (! $newModel->isLoadedFromCache() && isset(self::$defaultPkValue)) {
				$newModel->setDefaultPk(self::$defaultPkValue);
			}
		}

		return $restrict;
	}

	private static function firstLoadAllModels(array $models) {
		foreach ($models as $index => $modelName) {
			self::getNewModel($modelName, $index === 0);
		}
	}

	private static function loadModelsFrom(array $config, string $dbOffset = 'default'): array {
		$models = CacheManager::getModels($config, true, $dbOffset);
		if (\count($models) > 0) {
			return \array_map(function ($model) {
				return ClassUtils::getClassSimpleName($model);
			}, $models);
		}
		return [];
	}

	public static function run(&$config, $options, $what) {
		$domain = self::updateDomain($options);
		$dbOffset = self::getOption($options, 'd', 'database', 'default');
		self::$defaultPkValue = self::getOption($options, 'k', 'autoincPk', 'id');
		self::checkDbOffset($config, $dbOffset);

		CacheManager::start($config);

		$models = Console::explodeResponse($what ?? '', function ($item) {
			return \ucfirst(\trim($item));
		});

		if (\count($models) === 0) {
			$models = self::loadModelsFrom($config, $dbOffset);
			if (count($models) > 0) {
				$rep = Console::yesNoQuestion("Would you like to load the current classes [<b>" . \implode(',', $models) . "</b>]?");
				if (Console::isNo($rep)) {
					$models = [];
				} else {
					self::$loadCurrentModels = true;
				}
			}
		}
		if (\count($models) > 0) {
			self::firstLoadAllModels($models);

			$modelName = self::$currentModelName;
			$newModel = self::$currentModel;
		} else {
			$modelName = Console::question("Enter a model name: ");
			$newModel = self::getNewModel($modelName);
		}

		$fields = '';
		$checkExisting = [];
		do {
			$namespace = self::getModelNamespace($domain, $dbOffset);
			$modelCompleteName = $namespace . $modelName;

			$restrict = self::loadModel($newModel, $modelName, $namespace);
			$tableName = $newModel->getTableName();

			echo ConsoleFormatter::showMessage("Model: <b>$modelCompleteName</b>", 'info', 'Model add/update');

			$caseChangeDbOffset = "Change dbOffset [<b>$dbOffset</b>]";
			$caseChangeActiveDomain = "Change active Domain [<b>$domain</b>]";
			$caseSwitchModel = "Add/switch to model [" . self::getAllModelsAsString() . "]";

			$fields = \implode(',', $newModel->getFieldNames());
			$caseAddFields = "Add fields [<b>$fields</b>]";
			$caseAddDefaultPk = "Add default auto-inc primary key [<b>" . ($newModel->getDefaultPk() ?? '') . "</b>]";
			$caseChangeTableName = "Change table name [<b>$tableName</b>]";
			$caseAddRelations = "Add relations [<b>" . $newModel->getRelationsAsString() . "</b>]";

			if (! $restrict) {
				$choices = [
					$caseAddFields,
					$caseAddDefaultPk,
					'Add primary keys',
					$caseAddRelations,
					$caseChangeTableName,
					$caseChangeDbOffset,
					$caseChangeActiveDomain,
					$caseSwitchModel,
					'Generate classes',
					'Quit'
				];
			} else {
				$choices = [
					'Change class name',
					$caseChangeDbOffset,
					$caseChangeActiveDomain,
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
					$newModel->setUpdated(true);
					break;

				case $caseChangeTableName:
					$tbl = Console::question('Enter table name:');
					$newModel->setTableName(($tbl == '') ? null : $tbl);
					$newModel->setUpdated(true);
					break;

				case $caseAddFields:
					$field = Console::question("Enter field names: ");
					if ($field != '') {
						$fieldTypes = Console::question("Enter field types: ");
						$nullables = Console::question("Nullable fields:   ");
						self::addFields($field, $fieldTypes, $nullables);
						$newModel->setUpdated(true);
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
					$newModel->setUpdated(true);
					break;

				case $caseAddRelations:
					$rType = Console::question('Type: ', [
						'manyToOne',
						'oneToMany',
						'manyToMany'
					]);
					self::addRelation($rType, $newModel, $namespace);
					break;

				case $caseAddDefaultPk:
					$newModel->setDefaultPk(Console::question('Primary key name: '));
					$newModel->setUpdated(true);
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
					self::saveModelsInCache();
					echo ConsoleFormatter::showInfo('Operation terminated, Bye!');
			}
		} while ($rep !== 'Quit');
	}

	private static function saveModelsInCache() {
		$cached = [];
		foreach (self::$allModels as $modelName => $newModel) {
			if ($newModel->isUpdated()) {
				self::store($modelName, $newModel);
				$cached[] = $modelName;
			}
		}
		if (\count($cached) > 0) {
			echo ConsoleFormatter::showInfo("Data caching for the models: " . \implode(',', $cached));
		}
	}

	private static function addRelation(string $rType, NewModel $newModel, string $namespace) {
		$modelName = $newModel->getOriginalModelName();

		switch ($rType) {
			case 'manyToOne':
				$fkClass = Console::question('Foreign member className:', \array_keys(self::$allModels), [
					'ignoreCase' => true
				]);
				$otherModel = self::getNewModel($fkClass, false);
				$otherModelName = $otherModel->getOriginalModelName();

				$padmax = \strlen("OneToMany member name in $otherModelName:");

				$fkField = Console::question(\str_pad('Foreign key name:', $padmax), null, [
					'default' => 'id' . \ucfirst($otherModelName)
				]);
				$member = Console::question(\str_pad('Member name:', $padmax), null, [
					'default' => \lcfirst($otherModelName)
				]);

				$manyMember = Console::question(\str_pad("OneToMany member name in $otherModelName:", $padmax), null, [
					'default' => \lcfirst($modelName) . 's'
				]);

				$newModel->addManyToOne($member, $fkField, $otherModelName);
				if (! $otherModel->isLoaded()) {
					self::loadModel($otherModel, $otherModelName, $namespace);
				}
				$otherModel->addOneToMany($manyMember, $member, $modelName);

				$newModel->setUpdated(true);
				$otherModel->setUpdated(true);
				break;
			case 'manyToMany':
				$padmax = 45;
				$fkClass = Console::question(\str_pad('Associated className:', $padmax), \array_keys(self::$allModels), [
					'ignoreCase' => true
				]);
				$otherModel = self::getNewModel($fkClass, false);
				$otherModelName = $otherModel->getOriginalModelName();

				$member = Console::question(\str_pad("Associated member name in $modelName:", $padmax), null, [
					'default' => \lcfirst($otherModelName) . 's'
				]);
				$otherAssociatedFk = Console::question(\str_pad("Associated fk name for $otherModelName:", $padmax), null, [
					'default' => 'id' . \ucfirst($otherModelName)
				]);

				$otherMember = Console::question(\str_pad("Associated member name in $otherModelName:", $padmax), null, [
					'default' => \lcfirst($modelName) . 's'
				]);
				$associatedFk = Console::question(\str_pad("Associated fk name for $modelName:", $padmax), null, [
					'default' => 'id' . \ucfirst($modelName)
				]);
				$jointable = Console::question(\str_pad('Jointable:', $padmax), null, [
					'default' => \lcfirst($modelName) . '_' . \lcfirst($otherModelName) . 's'
				]);

				$joinColumn = ($associatedFk !== $newModel->getDefaultFk()) ? [
					'name' => $associatedFk,
					'referencedColumnName' => $newModel->getFirstPk()
				] : [];
				$otherJoinColumn = ($otherAssociatedFk !== $otherModel->getDefaultFk()) ? [
					'name' => $otherAssociatedFk,
					'referencedColumnName' => $otherModel->getFirstPk()
				] : [];

				$newModel->addManyToMany($member, $otherModelName, $otherMember, $jointable, $joinColumn, $otherJoinColumn);
				if (! $otherModel->isLoaded()) {
					self::loadModel($otherModel, $otherModelName, $namespace);
				}
				$otherModel->addManyToMany($otherMember, $modelName, $member, $jointable, $otherJoinColumn, $joinColumn);
				$newModel->setUpdated(true);
				$otherModel->setUpdated(true);

				break;
			case 'oneToMany':
				$padmax = 45;
				$fkClass = Console::question(\str_pad('Associated member className:', $padmax), \array_keys(self::$allModels), [
					'ignoreCase' => true
				]);
				$otherModel = self::getNewModel($fkClass, false);
				$otherModelName = $otherModel->getOriginalModelName();

				$fkField = Console::question(\str_pad('Foreign key name:', $padmax), null, [
					'default' => 'id' . \ucfirst($modelName)
				]);
				$member = Console::question(\str_pad('Member name:', $padmax), null, [
					'default' => \lcfirst($otherModelName) . 's'
				]);
				$mappedBy = Console::question(\str_pad("MappedBy member name in $otherModelName:", $padmax), null, [
					'default' => \lcfirst($modelName)
				]);

				$newModel->addOneToMany($member, $mappedBy, $otherModelName);
				if (! $otherModel->isLoaded()) {
					self::loadModel($otherModel, $otherModelName, $namespace);
				}
				$otherModel->addManyToOne($mappedBy, $fkField, $modelName);
				$newModel->setUpdated(true);
				$otherModel->setUpdated(true);
				break;
		}
	}
}
