<?php

require_once(__DIR__ . '/Core.php');
require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/WebForumRoleProvider.php');

class WebForumController extends Controller {

	public function __construct() {
		parent::__construct(new WebForumRoleProvider());
	}

	protected function get_user_identifier(): mixed {
		return get_user_info()['id'];
	}

}

?>