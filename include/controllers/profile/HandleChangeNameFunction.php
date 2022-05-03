<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../Validation.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleChangeNameFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('user.change_name');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// If no username was provided, redirect back to the profile page
		if(!isset($args['user'])) {
			header('Location: /profile');
			die();
		}

		// If no new name was provided, redirect back to the profile page
		if(!isset($args['newName'])) {
			header('Location: /profile');
			die();
		}

		$username = $args['user'];
		$newName = trim($args['newName']);

		// Check username validity
		if(!is_valid_username($newName)) {
			$changeError = 'Username invalid.';
			include(__DIR__ . '/../../../pages/change_username.php');
			die();
		}

		// Check username availability
		$user = get_user_by_username($newName);
		if($user !== null) {
			$changeError = 'Username already in use.';
			include(__DIR__ . '/../../../pages/change_username.php');
			die();
		}

		// We're good to go, update the username
		$user = get_user_by_username($username);
		update_username($user['id'], $newName);

		// Update session info
		$_SESSION['user'] = get_user_by_username($newName);

		// Go back to the profile page now that we're done
		header('Location: /profile/view?user=' . htmlspecialchars($newName));
		die();
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