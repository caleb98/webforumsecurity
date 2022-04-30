<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleCreatePostFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('thread.create');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$category = $args['category'];
		$title = trim($args['title']);
		$text = trim($args['postText']);

		// Check that a category with the given name actually exists
		$matches = count(array_filter(get_forum_categories(), function($cat) use ($category) {
			return $cat['name'] === $category;
		}));
		$categoryExists = $matches === 1;

		if(!isset($category) || !$categoryExists) {
			// If the category is invalid, the user can't fix much.
			// Just redirect them back to the forum view page.
			header('Location: /forum');
			die();
		}
		elseif(!isset($title) || $title === '') {
			$postCreationError = 'Invalid title.';
			include(__DIR__ . '/../../../pages/forum_post.php');
			die();
		}
		elseif(!isset($text) || $text === '') {
			$postCreationError = 'Post text cannot be empty.';
			include(__DIR__ . '/../../../pages/forum_post.php');
			die();
		}

		// Try to create the post
		$postCreationResult = add_forum_thread($category, $title, $text, $userIdentifier);
		if(gettype($postCreationResult) === 'string') {
			$postCreationError = $postCreationResult;
			include(__DIR__ . '/../../../pages/forum_post.php');
			die();
		}

		// Creation was successful, go ahead and redirect to the new thread
		header('Location: /forum/view?category=' . htmlspecialchars($category) . '&thread=' . htmlspecialchars($postCreationResult));
		die();
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