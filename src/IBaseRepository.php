<?php
namespace base\data\repository;

/**
 * Base implementation to a data repository
 */
interface IBaseRepository {
	/**
	 * Find objects by unique id
	 * 
	 * @param  array $uids ussually an integer but can vary
	 * @return mixed the objects found or NULL if not
	 */
	public function findById($uids);

	/**
	 * Find an object by searching
	 * @param  mixed $obj object to search for
	 * @return mixed the object found or NULL
	 */
	public function find($object);

	/**
	 * Get all objects from the repository of this type
	 * 
	 * @return array array of the objects
	 */
	public function getAll();

	/**
	 * Deletes object(s) from the repository
	 * 
	 * @param  array $uids ussually integer but can vary
	 * @return void
	 */
	public function deleteById($uids);
}
?>