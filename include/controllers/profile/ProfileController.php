<?php

require_once(__DIR__ . '/../../WebForumController.php');

require_once(__DIR__ . '/DisplayProfileFunction.php');
require_once(__DIR__ . '/DisplayChangeNameFunction.php');
require_once(__DIR__ . '/HandleChangeNameFunction.php');
require_once(__DIR__ . '/DisplayChangePassFunction.php');
require_once(__DIR__ . '/HandleChangePassFunction.php');

class ProfileController extends WebForumController {

	public function __construct() {
		parent::__construct('view');

		// Default profile viewing function
		$this->add_get_mapping('view', new DisplayProfileFunction());

		// Username changing functions
		$this->add_get_mapping('changename', new DisplayChangeNameFunction());
		$this->add_post_mapping('changename', new HandleChangeNameFunction());

		// Password changing functions
		$this->add_get_mapping('changepass', new DisplayChangePassFunction());
		$this->add_post_mapping('changepass', new HandleChangePassFunction());

	}

}

?>