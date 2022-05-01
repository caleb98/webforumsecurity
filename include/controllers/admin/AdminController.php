<?php

require_once(__DIR__ . '/../../WebForumController.php');

require_once(__DIR__ . '/DisplayAdminFunction.php');
require_once(__DIR__ . '/DisplayAddCategoryFunction.php');
require_once(__DIR__ . '/HandleAddCategoryFunction.php');
require_once(__DIR__ . '/DisplayRemoveCategoryFunction.php');
require_once(__DIR__ . '/HandleRemoveCategoryFunction.php');
require_once(__DIR__ . '/DisplayBanUserFunction.php');
require_once(__DIR__ . '/HandleBanUserFunction.php');

class AdminController extends WebForumController {

	public function __construct() {
		parent::__construct('default');

		// Default admin page view
		$this->add_get_mapping('default', new DisplayAdminFunction());
		
		// Category adding mappings
		$this->add_get_mapping('addcategory', new DisplayAddCategoryFunction());
		$this->add_post_mapping('addcategory', new HandleAddCategoryFunction());

		// Category removal mappings
		$this->add_get_mapping('removecategory', new DisplayRemoveCategoryFunction());
		$this->add_post_mapping('removecategory', new HandleRemoveCategoryFunction());

		// User ban mappings
		$this->add_get_mapping('ban', new DisplayBanUserFunction());
		$this->add_post_mapping('ban', new HandleBanUserFunction());
	}

}

?>