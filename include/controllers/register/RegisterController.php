<?php

require_once(__DIR__ . '/../../WebForumController.php');

require_once(__DIR__ . '/DisplayRegisterFunction.php');
require_once(__DIR__ . '/DisplayAlternateRegisterFunction.php');
require_once(__DIR__ . '/DiscordRegisterFunction.php');
require_once(__DIR__ . '/GoogleRegisterFunction.php');
require_once(__DIR__ . '/HandleRegisterFunction.php');
require_once(__DIR__ . '/HandleAlternateRegisterFunction.php');

class RegisterController extends WebForumController {

	public function __construct() {
		parent::__construct('default', 'default');

		// Default mapping to show the registration page
		$this->add_get_mapping('default', new DisplayRegisterFunction());

		// Alternate registration mapping to allow redirects to registration
		// if the user tries to login using a 3rd party account that has not
		// yet registered.
		$this->add_get_mapping('alternate', new DisplayAlternateRegisterFunction());

		// Discord mapping to send the user to the Discord login flow
		$this->add_get_mapping('discord', new DiscordRegisterFunction());

		// Google mapping to handle google login flow
		$this->add_post_mapping('google', new GoogleRegisterFunction());

		// Default login post mapping
		$this->add_post_mapping('default', new HandleRegisterFunction());

		// Alternate OAuth registration flow mapping
		$this->add_post_mapping('alternate', new HandleAlternateRegisterFunction());
	}

}

?>