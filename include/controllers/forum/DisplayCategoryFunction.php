<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayCategoryFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('category.view');
	}

	public function run(string $context, array $args): void {
		$category = $args['category'];

		// If a thread is specified, show the thread
		if(isset($args['thread'])) {
			$thread = $args['thread'];
			$threadName = get_thread_name($category, $thread);
			$replies = get_thread_replies($category, $thread);

			include(__DIR__ . '/../../../pages/forum_thread.php');
		}

		// Otherwise, show all threads
		else {
			$page = $args['page'] ?? 0;
			$threads = get_category_threads($category, 25, $page);

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