<?php
namespace data\repository;

/**
 * Represent an object used by uow for traching changes
 * 
 */
interface IUOWObject {
	public function getObjectId();
}
?>