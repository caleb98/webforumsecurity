<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayChangeNameFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('user.change_name');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$username = $args['user'];

		// If no username was provided, redirect back to the profile page
		if(!isset($username)) {
			header('Location: /profile');
			die();
		}

		include(__DIR__ . '/../../../pages/change_username.php');
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