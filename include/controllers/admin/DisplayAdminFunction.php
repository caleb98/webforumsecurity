<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayAdminFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('admin.view');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		include(__DIR__ . '/../../../pages/admin.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>