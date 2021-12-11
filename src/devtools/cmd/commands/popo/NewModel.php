<?php
namespace Ubiquity\devtools\cmd\commands\popo;

use Ubiquity\cache\CacheManager;
use Ubiquity\orm\creator\Member;
use Ubiquity\orm\creator\Model;

class NewModel {

	private string $originalModelName;

	private ?string $tableName = null;

	private ?string $defaultPk = null;

	private array $pks = [];

	private array $fields = [];

	private array $manyToOne = [];

	private array $oneToMany = [];

	private array $manyToMany = [];

	private bool $loaded = false;

	private bool $loadedFromCache = false;

	private bool $updated = false;

	public function __construct($modelName) {
		$this->originalModelName = $modelName;
	}

	/**
	 *
	 * @return string
	 */
	public function getOriginalModelName(): string {
		return $this->originalModelName;
	}

	/**
	 *
	 * @param string $originalModelName
	 */
	public function setOriginalModelName(string $originalModelName): void {
		$this->originalModelName = $originalModelName;
	}

	/**
	 *
	 * @return string
	 */
	public function getTableName(): string {
		return $this->tableName ?? (\lcfirst($this->originalModelName));
	}

	/**
	 *
	 * @param string|null $tableName
	 */
	public function setTableName(?string $tableName): void {
		$this->tableName = $tableName;
	}

	/**
	 *
	 * @return string|null
	 */
	public function getDefaultPk(): ?string {
		return $this->defaultPk;
	}

	public function getDefaultFk(): string {
		return 'id' . \ucfirst($this->originalModelName);
	}

	public function hasDefaultPk(): bool {
		return isset($this->defaultPk) && $this->defaultPk != '';
	}

	/**
	 *
	 * @param string|null $defaultPk
	 */
	public function setDefaultPk(?string $defaultPk): void {
		$this->defaultPk = $defaultPk;
	}

	/**
	 *
	 * @return array
	 */
	public function getPks(): array {
		return $this->pks;
	}

	public function resetPks() {
		$this->pks = [];
	}

	/**
	 *
	 * @param array $pks
	 */
	public function setPks(array $pks): void {
		$this->pks = $pks;
	}

	public function addPk(string $pk): bool {
		if (! \in_array($pk, $this->pks)) {
			$this->pks[] = $pk;
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return array
	 */
	public function getFields(): array {
		return $this->fields;
	}

	/**
	 *
	 * @param array $fields
	 */
	public function setFields(array $fields): void {
		$this->fields = $fields;
	}

	public function addField(string $name, array $fieldInfos) {
		$this->fields[$name] = $fieldInfos;
	}

	public function hasField(string $name): bool {
		return isset($this->fields[$name]);
	}

	public function getFieldNames(): array {
		if (\is_array($this->fields)) {
			return \array_keys($this->fields);
		}
		return [];
	}

	public function updateFirstPk() {
		if ($this->hasDefaultPk()) {
			$this->addField($this->defaultPk, [
				'Type' => 'int',
				'Nullable' => 'false'
			]);
			$this->addPk($this->defaultPk);
		}
	}

	public function getFirstPk(): ?string {
		return $this->defaultPk ?? ($this->pks[0] ?? null);
	}

	public function updatePks($pks) {
		$pks = \explode(',', $pks);
		$this->pks = [];
		$this->updateFirstPk();
		foreach ($pks as $pk) {
			$this->addPk($pk);
		}
	}

	public function setFieldsOrder() {
		$result = [];
		$fields = $this->fields;
		$pks = $this->getPks();
		foreach ($pks as $pk) {
			$result[$pk] = $fields[$pk];
			unset($fields[$pk]);
		}
		foreach ($fields as $field => $fieldInfos) {
			$result[$field] = $fieldInfos;
		}
		$this->fields = $result;
	}

	public function getSimpleMembers() {
		$members = [];
		foreach ($this->fields as $name => $fieldInfos) {
			if ($fieldInfos['Type'] !== 'mixed') {
				$members[] = $name;
			}
		}
		return $members;
	}

	public function generateClass($className, $namespace, $dbOffset): Model {
		$memberAccess = 'private';
		$this->updateFirstPk();
		$this->setFieldsOrder();
		$engine = CacheManager::getAnnotationsEngineInstance();
		$class = new Model($engine, \lcfirst($className), $namespace, $memberAccess);
		$class->setTable($this->getTableName());
		$class->setDatabase($dbOffset);
		$fieldsInfos = $this->selectFieldsForGeneration();
		$class->setSimpleMembers($this->getSimpleMembers());
		$keys = $this->pks;
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
		return $class;
	}

	private function selectFieldsForGeneration() {
		$fieldsInRelation = $this->getFieldsInRelations();
		$result = [];
		foreach ($this->fields as $f => $infos) {
			if (! in_array($f, $fieldsInRelation)) {
				$result[$f] = $infos;
			}
		}
		return $result;
	}

	private function getFieldsInRelations() {
		$result = [];
		foreach ($this->manyToOne as $f => $_) {
			$result[] = $f;
		}
		foreach ($this->oneToMany as $f => $_) {
			$result[] = $f;
		}
		foreach ($this->manyToMany as $f => $_) {
			$result[] = $f;
		}
		return $result;
	}

	public function addManyToOne($member, $fkField, $className) {
		$this->manyToOne[$member] = \compact('fkField', 'className');
	}

	public function addOneToMany($member, $mappedBy, $className) {
		$this->oneToMany[$member] = \compact('mappedBy', 'className');
	}

	public function addManyToMany($member, $otherClassName, $otherMember, $joinTable, $joinColumn, $otherJoinColumn) {
		$this->manyToMany[$member] = compact('otherClassName', 'otherMember', 'joinTable', 'joinColumn', 'otherJoinColumn');
	}

	/**
	 *
	 * @return array
	 */
	public function getManyToOne(): array {
		return $this->manyToOne;
	}

	/**
	 *
	 * @return array
	 */
	public function getOneToMany(): array {
		return $this->oneToMany;
	}

	/**
	 *
	 * @return array
	 */
	public function getManyToMany(): array {
		return $this->manyToMany;
	}

	/**
	 *
	 * @return bool
	 */
	public function isLoaded(): bool {
		return $this->loaded;
	}

	/**
	 *
	 * @param bool $loaded
	 */
	public function setLoaded(bool $loaded): void {
		$this->loaded = $loaded;
	}

	/**
	 *
	 * @return bool
	 */
	public function isUpdated(): bool {
		return $this->updated;
	}

	/**
	 *
	 * @param bool $updated
	 */
	public function setUpdated(bool $updated): void {
		$this->updated = $updated;
	}

	/**
	 *
	 * @param array $manyToOne
	 */
	public function setManyToOne(array $manyToOne): void {
		$this->manyToOne = $manyToOne;
	}

	/**
	 *
	 * @param array $oneToMany
	 */
	public function setOneToMany(array $oneToMany): void {
		$this->oneToMany = $oneToMany;
	}

	/**
	 *
	 * @param array $manyToMany
	 */
	public function setManyToMany(array $manyToMany): void {
		$this->manyToMany = $manyToMany;
	}

	public function getRelationsAsString() {
		$result = '';
		if (\count($this->manyToOne) > 0) {
			$result .= ' (1)=>(' . \implode(',', \array_keys($this->manyToOne)) . ')';
		}
		if (\count($this->oneToMany) > 0) {
			$result .= ' (1-*)=>(' . \implode(',', \array_keys($this->oneToMany)) . ')';
		}
		if (\count($this->manyToMany) > 0) {
			$result .= ' (*-*)=>(' . \implode(',', \array_keys($this->manyToMany)) . ')';
		}
		return $result;
	}

	/**
	 *
	 * @return bool
	 */
	public function isLoadedFromCache(): bool {
		return $this->loadedFromCache;
	}

	/**
	 *
	 * @param bool $loadedFromCache
	 */
	public function setLoadedFromCache(bool $loadedFromCache): void {
		$this->loadedFromCache = $loadedFromCache;
	}
}
