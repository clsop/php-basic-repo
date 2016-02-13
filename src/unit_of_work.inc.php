<?php
namespace data\repository;

require_once('batch_repository.inc.php');
require_once('uow_object.inc.php');
require_once('exception/uow_exception.inc.php');

/**
 * Base for implementing Unit of work (referred as UOW)
 */
class UnitOfWork {
	private $dataRepository;
	private $newEntities;
	private $dirtyEntities;
	private $oldDirtyEntities;
	private $deleteEntities;
	private $deleteById;
	private $commited;

	public function __construct($dataRepository = NULL, $deleteById = true) {
		$this->dataRepository = $dataRepository;
		$this->newEntities = [];
		$this->dirtyEntities = [];
		$this->deleteEntities = [];

		$this->deleteById = $deleteById;
		$this->commited = false;
	}

	public function __get($prop) {
		if (property_exists($this, $prop) && $prop == 'deleteById' || $prop == 'dataRepository') {
			return $this->$prop;
		}

		return NULL;
	}

	public function __set($name, $value) {
		if (property_exists($this, $name) && $name == 'dataRepository') {
			$this->dataRepository = $value;
		}
	}

	/**
	 * Register a new object
	 * 
	 * @param  mixed $obj the object to register
	 * @return void
	 * @throws UOWException if object is not uow compliant
	 */
	public function asNew($obj) {
		$this->setData($obj, $this->newEntities);
	}

	/**
	 * Register a dirty object that needs updating
	 * 
	 * @param  mixed $obj the object to update
	 * @return void
	 * @throws UOWException if object is not uow compliant
	 */
	public function asUpdate($obj) {
		$this->setData($obj, $this->dirtyEntities);
	}

	/**
	 * Register a object to be deleted
	 * 
	 * @param  mixed $obj the object to delete
	 * @return void
	 * @throws UOWException if object is not uow compliant
	 */
	public function asDelete($obj) {
		$this->setData($obj, $this->deleteEntities);
	}

	/**
	 * Clear the underlying entity states (ussually after a commit and rollback)
	 * 
	 * @return void
	 */
	private function clearEntities() {
		unset($this->newEntities);
		unset($this->dirtyEntities);
		unset($this->oldDirtyEntities);
		unset($this->deleteEntities);

		$this->newEntities = [];
		$this->dirtyEntities = [];
		$this->oldDirtyEntities = [];
		$this->deleteEntities = [];

		$this->commited = false;
	}

	private function setData($obj, &$set) {
		if (!($obj instanceof IUOWObject)) {
			throw new exception\UOWException('objects must implement IUOWObject');
		}
		
		$uid = $obj->getObjectId();

		if (array_key_exists($uid, $set)) {
			throw new exception\UOWException('key already present in uow');
		}
		
		$set[$uid] = $obj;
	}

	/**
	 * Commit any changes to the transaction
	 * @return void
	 * @throws UOWException if data repository is missing
	 */
	public function commit() {
		if (!($this->dataRepository instanceof IBaseRepository)) {
			throw new exception\UOWException('must supply a data repository');
		}

		// set commit flag, a commit has been started
		$this->commited = true;

		// check if a batch repository is used
		if ($this->dataRepository instanceof IBatchRepository) {
			$batchObjects = ['creates' => $this->newEntities, 'updates' => $this->dirtyEntities, 'deletes' => $this->deleteEntities];

			$this->oldDirtyEntities = $this->dataRepository->batchTransaction($batchObjects);
		} else {
			$newEntitiesSize = count($this->newEntities);
			$dirtyEntitiesSize = count($this->dirtyEntities);
			$deleteEntitiesSize = count($this->deleteEntities);

			// create
			if ($newEntitiesSize > 0) $this->dataRepository->create($this->newEntities);

			// update
			if ($dirtyEntitiesSize > 0) {
				$this->oldDirtyEntities = $this->dataRepository->update($this->dirtyEntities);
			}

			// delete
			if ($deleteEntitiesSize > 0) {
				if ($this->deleteById) {
					$this->dataRepository->deleteById(array_keys($this->deleteEntities));
				} else {
					$this->dataRepository->delete($this->deleteEntities);
				}
			}
		}
	}

	/**
	 * Rollback any changes done by last commit
	 * @return void
	 */
	public function rollback() {
		// if no transaction yet
		if (!$this->commited) {
			return;
		}

		if ($this->dataRepository instanceof IBatchRepository) {
			$batchObjects = ['creates' => $this->deleteEntities, 'updates' => $this->oldDirtyEntities, 'deletes' => $this->newEntities];
			$this->dataRepository->batchTransaction($batchObjects);
		} else {
			$newEntitiesSize = count($this->newEntities);
			$dirtyEntitiesSize = count($this->dirtyEntities);
			$deleteEntitiesSize = count($this->deleteEntities);

			// delete new objects
			if ($newEntitiesSize > 0) $this->dataRepository->delete($this->newEntities);

			// update old objects
			if ($dirtyEntitiesSize > 0) $this->dataRepository->update($this->oldDirtyEntities);

			// create deleted objects
			if ($deleteEntitiesSize > 0) $this->dataRepository->create($this->deleteEntities);
		}

		$this->clearEntities();
		$this->commited = false;
	}
}
?>