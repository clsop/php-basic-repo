<?php
namespace base\data\exception;

class UOWException extends \ErrorException {
	public function __construct($message) {
		parent::__construct($message);
	}
}
?>