<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayAddCategoryFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('category.add');
	}

	public function run(string $context, array $args): void {
		include(__DIR__ . '/../../../pages/create_category.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>