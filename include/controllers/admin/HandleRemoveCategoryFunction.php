<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleRemoveCategoryFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('category.remove');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		if(!isset($args['confirmDelete'])) {
			$removeError = 'Please confirm the deletion by checking the radio button at the bottom of the form.';
			$categories = get_forum_categories();
			include(__DIR__ . '/../../../pages/remove_category.php');
			die();
		}

		// Remove the confirmation value
		unset($args['confirmDelete']);

		// Loop through arguments and extract ids of categories to delete
		$ids = [];
		foreach($args as $category => $val) {
			$category = intval(str_replace('category-', '', $category));
			$error = delete_forum_category($category);

			if($error) {
				$removeError = 'Unable to remove category: ' . $error;
				$categories = get_forum_categories();
				include(__DIR__ . '/../../../pages/remove_category.php');
				die();
			}
		}

		// Success, go back to forum display
		header('Location: /forum');
		die();
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>