<?php
namespace base\data\repository;

/**
 * Base implementation to a data repository
 */
interface IDataRepository extends IBaseRepository {
	/**
	 * Creates a new object in the repository
	 * 
	 * @param  mixed $objects the object(s) to create
	 * @return array newly created objects
	 */
	public function create($objects): array;

	/**
	 * Updates an object in the repository
	 * 
	 * @param  mixed $objects the object(s) to update values from
	 * @return array old objects
	 */
	public function update($objects): array;

	/**
	 * Deletes an object from the repository
	 * 
	 * @param  mixed $objects the object(s) to delete
	 * @return void
	 */
	public function delete($objects);
}
?>