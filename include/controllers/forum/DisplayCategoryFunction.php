<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayCategoryFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('category.view');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$category = $args['category'];
		$replyError = $args['replyError'] ?? '';

		// Check that a category with the given name actually exists
		$matches = count(array_filter(get_forum_categories(), function($cat) use ($category) {
			return $cat['name'] === $category;
		}));
		$categoryExists = $matches === 1;

		// No category specified (or invalid), so redirect to categories view.
		if(!isset($category) || !$categoryExists) {
			header('Location: /forum');
			die();
		}
		// If a thread is specified, show the thread
		elseif(isset($args['thread'])) {
			$thread = $args['thread'];
			$threadName = get_thread_name($category, $thread);

			// Check whether the thread exists
			if($threadName === null) {
				header('Location: /forum');
				die();
			}

			$replies = get_thread_replies($category, $thread);

			// Check whether the user has reply permissions
			$perms = get_user_permissions($userIdentifier, $context);
			$showReplyBox = in_array('thread.reply', $perms);

			include(__DIR__ . '/../../../pages/forum_thread.php');
		}

		// Otherwise, show all threads in the category
		else {
			$page = $args['page'] ?? 0;
			$threads = get_category_threads($category, 25, $page);

			// Check whether the user has post permissions
			$perms = get_user_permissions($userIdentifier, $context);
			$showPostButton = in_array('thread.create', $perms);

			include(__DIR__ . '/../../../pages/forum_category.php');
		}
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