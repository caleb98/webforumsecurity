<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayAlternateRegisterFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$alternateRegister = true;
		include(__DIR__ . '/../../../pages/register.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>