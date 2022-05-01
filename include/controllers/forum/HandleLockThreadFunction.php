<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleLockThreadFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('thread.lock');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$category = $args['category'];
		$thread = $args['thread'];

		// Check thread exists
		$threadInfo = get_thread_info($category, $thread);
		if($threadInfo === null) {
			header('Location: /forum');
			die();
		}

		// Set new status
		$newStatus = !$threadInfo['isLocked'];
		$error = set_thread_locked($category, $thread, $newStatus);

		// Redirect with error if error occurred
		if($error) {
			$lockError = $error;
			header('Location: /forum/view?category=' . htmlspecialchars($category) . '&thread=' . htmlspecialchars($thread) . '&lockError=' . htmlspecialchars($lockError));
			die();
		}

		// Otherwise just go back to the thread
		header('Location: /forum/view?category=' . htmlspecialchars($category) . '&thread=' . htmlspecialchars($thread));
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