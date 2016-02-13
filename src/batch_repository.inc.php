<?php
namespace data\repository;

require_once('base_repository.inc.php');

/**
 * Base implementation to a data repository
 */
interface IBatchRepository extends IBaseRepository {
	/**
	 * Creates a new object in the repository
	 * 
	 * @param  array $objects map in form of ['creates' => array, 'updates' => array, 'deletes' => array]
	 * @return array old objects from updates array
	 */
	public function batchTransaction($objects);
}
?>