<?php

require_once(__DIR__ . '/../../WebForumController.php');

require_once(__DIR__ . '/DisplayCategoriesFunction.php');
require_once(__DIR__ . '/DisplayAddCategoryFunction.php');
require_once(__DIR__ . '/HandleAddCategoryFunction.php');
require_once(__DIR__ . '/DisplayCategoryFunction.php');
require_once(__DIR__ . '/DisplayCreatePostFunction.php');
require_once(__DIR__ . '/HandleCreatePostFunction.php');
require_once(__DIR__ . '/HandleReplyPostFunction.php');

class ForumViewController extends WebForumController {

	public function __construct() {
		parent::__construct('categories');

		// Shows all categories the user can see
		$this->add_get_mapping('categories', new DisplayCategoriesFunction());

		// Mappings for adding new categories
		$this->add_get_mapping('newcategory', new DisplayAddCategoryFunction());
		$this->add_post_mapping('newcategory', new HandleAddCategoryFunction());

		// Mapping for viewing categories
		$this->add_get_mapping('view', new DisplayCategoryFunction());

		// Mapping for creating a new post
		$this->add_get_mapping('post', new DisplayCreatePostFunction());
		$this->add_post_mapping('post', new HandleCreatePostFunction());

		// Mapping for replying to a post
		$this->add_post_mapping('view', new HandleReplyPostFunction());

	}

}

?>