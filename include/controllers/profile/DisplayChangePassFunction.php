<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayChangePassFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('user.change_pass');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$username = $args['user'];
		$showUpdateMessage = isset($args['showUpdateMessage']);

		// If no username was provided, redirect back to the profile page
		if(!isset($username)) {
			header('Location: /profile');
			die();
		}

		include(__DIR__ . '/../../../pages/change_password.php');
	}

	public function resolve_context(array $args): string {
		// If a user was provided, give the user context
		if(isset($args['user'])) {
			return 'user.' . $args['user'];
		}
		// Otherwise, global context since we'll error anyway.
		else {
			return '';
		}
	}

}

?>