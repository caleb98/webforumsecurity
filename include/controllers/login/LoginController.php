<?php

require_once(__DIR__ . '/../../WebForumController.php');

require_once(__DIR__ . '/DisplayLoginFunction.php');
require_once(__DIR__ . '/DiscordLoginFunction.php');
require_once(__DIR__ . '/HandleLoginFunction.php');
require_once(__DIR__ . '/GoogleLoginFunction.php');

class LoginController extends WebForumController {

	public function __construct() {
		parent::__construct('default', 'default');

		// Default mapping to show the login page
		$this->add_get_mapping('default', new DisplayLoginFunction());

		// Discord mapping to send the user to the Discord login flow
		$this->add_get_mapping('discord', new DiscordLoginFunction());

		// Add mapping for default login post
		$this->add_post_mapping('default', new HandleLoginFunction());

		// Add mapping for google login post
		$this->add_post_mapping('google', new GoogleLoginFunction());
	}

}

?>