<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleReplyPostFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('thread.reply');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$category = $args['category'];
		$thread = $args['thread'];
		$replyText = trim($args['replyText']);

		// Check that a category with the given name actually exists
		$matches = count(array_filter(get_forum_categories(), function($cat) use ($category) {
			return $cat['name'] === $category;
		}));
		$categoryExists = $matches === 1;

		// Check for invalid category
		if(!isset($category) || !$categoryExists) {
			// If the category is invalid, the user can't fix much.
			// Just redirect them back to the forum view page.
			header('Location: /forum');
			die();
		}
		// Check for valid reply text
		elseif(!isset($replyText) || $replyText === '') {
			$replyError = 'Reply cannot be empty.';
			header('Location: /forum/view?category=' . htmlspecialchars($category) . '&thread=' . htmlspecialchars($thread) . '&replyError=' . htmlspecialchars($replyError));
			die();
		}

		// We're good to go, try to add the reply.
		$error = add_thread_comment($category, $thread, $userIdentifier, $replyText);

		if($error) {
			header('Location: /forum/view?category=' . htmlspecialchars($category) . '&thread=' . htmlspecialchars($thread) . '&replyError=' . htmlspecialchars($error));
			die();
		}

		// Reply success, go back to viewing the post
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