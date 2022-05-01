<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayRemoveCategoryFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('category.remove');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$categories = get_forum_categories();
		include(__DIR__ . '/../../../pages/remove_category.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>