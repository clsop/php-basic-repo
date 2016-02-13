<?php
namespace data\repository;

require_once('base_repository.inc.php');

/**
 * Base implementation to a data repository
 */
interface IDataRepository extends IBaseRepository {
	/**
	 * Creates a new object in the repository
	 * 
	 * @param  mixed $objects the object(s) to create
	 * @return mixed newly created object
	 */
	public function create($objects);

	/**
	 * Updates an object in the repository
	 * 
	 * @param  mixed $objects the object(s) to update values from
	 * @return mixed old object
	 */
	public function update($objects);

	/**
	 * Deletes an object from the repository
	 * 
	 * @param  mixed $objects the object(s) to delete
	 * @return void
	 */
	public function delete($objects);
}
?>