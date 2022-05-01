<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayBanUserFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('user.ban');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		include(__DIR__ . '/../../../pages/ban_user.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>