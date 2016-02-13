<?php
namespace data\repository\exception;

class TransactionException extends \ErrorException {
	public function __construct($message) {
		parent::__construct($message);
	}
}
?>