<?php
namespace base\data\exception;

class TransactionException extends \ErrorException {
	public function __construct($message) {
		parent::__construct($message);
	}
}
?>