<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayCreatePostFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('thread.create');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		if(!isset($args['category'])) {
			header('Location: /forum');
			die();
		}

		$category = $args['category'];
		include(__DIR__ . '/../../../pages/forum_post.php');
	}

	public function resolve_context(array $args): string {
		$categories = get_forum_categories();
		foreach($categories as $category) {
			if($category['name'] === $args['category']) {
				// If the category being accessed is private, we need
				// to use the category context when accessing it.
				if($category['isPrivate']) {
					return 'forum.' . str_replace(' ', '_', $args['category']);
				}

				// Not private, so we can use the global context.
				else {
					return '';
				}
			}
		}

		// Forum category did not exist, so we'll use global context here
		// since an error will occur anyway.
		return '';
	}

}

?>