<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayLoginFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		include(__DIR__ . '/../../../pages/login.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>