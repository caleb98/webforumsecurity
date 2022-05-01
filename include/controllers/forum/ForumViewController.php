<?php

require_once(__DIR__ . '/../../WebForumController.php');

require_once(__DIR__ . '/DisplayCategoriesFunction.php');
require_once(__DIR__ . '/DisplayCategoryFunction.php');
require_once(__DIR__ . '/DisplayCreatePostFunction.php');
require_once(__DIR__ . '/HandleCreatePostFunction.php');
require_once(__DIR__ . '/HandleReplyPostFunction.php');
require_once(__DIR__ . '/HandleLockThreadFunction.php');


class ForumViewController extends WebForumController {

	public function __construct() {
		parent::__construct('categories');

		// Shows all categories the user can see
		$this->add_get_mapping('categories', new DisplayCategoriesFunction());

		// Mapping for viewing categories
		$this->add_get_mapping('view', new DisplayCategoryFunction());

		// Mapping for creating a new post
		$this->add_get_mapping('post', new DisplayCreatePostFunction());
		$this->add_post_mapping('post', new HandleCreatePostFunction());

		// Mapping for replying to a post
		$this->add_post_mapping('view', new HandleReplyPostFunction());

		// Mapping for locking/unlocking a post
		$this->add_post_mapping('lock', new HandleLockThreadFunction());

	}

}

?>