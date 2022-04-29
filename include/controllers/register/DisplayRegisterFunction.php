<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayRegisterFunction extends ControllerFunction {

	public function run(string $context, array $args): void {
		include(__DIR__ . '/../../../pages/register.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>