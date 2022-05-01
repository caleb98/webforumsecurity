<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleAddCategoryFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('category.add');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// Check that the name was provided.
		if(!isset($args['categoryName']) || $args['categoryName'] === '') {
			$createError = 'Please enter a category name.';
			include(__DIR__ . '/../pages/create_category.php');
			die();
		}

		// Grab the name and isPrivate field
		$categoryName = trim(strtolower($args['categoryName']));
		$isPrivate = isset($args['isPrivate']);

		// Check that the category name is valid
		if(!preg_match('/[a-z0-9 ]+/i', $categoryName)) {
			$createError = 'Invalid category name. Use only alphanumeric characters.';
			include(__DIR__ . '/../pages/create_category.php');
			die();
		}

		// Check that the category name is available
		$categories = get_forum_categories();
		if(in_array($categoryName, $categories)) {
			$createError = 'A category with that name already exists.';
			include(__DIR__ . '/../pages/create_category.php');
			die();
		}

		// Create the category
		create_forum_category($categoryName, $isPrivate);

		// Add the current user as a moderator for the new category
		$userId = get_user_info()['id'];
		add_user_role($userId, '/forum\.' . str_replace(' ', '_', $categoryName) . '/', 'MODERATOR');

		// Redirect to standard forum view
		header('Location: /forum');
		die();
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>