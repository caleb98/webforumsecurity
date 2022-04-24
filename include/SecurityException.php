<?php

class SecurityExcpetion extends Exception {

	public function __construct(string $message) {
		parent::__construct($message);
	}

	public function __toString() {
		return __CLASS__ . ': ${this->message}\n';
	}

}

?>