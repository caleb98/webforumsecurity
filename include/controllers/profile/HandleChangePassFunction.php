<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../Security.php');
require_once(__DIR__ . '/../../Validation.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleChangePassFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('user.change_pass');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// If the username has not been provided, just go back to the profile page
		if(!isset($args['user'])) {
			header('Location: /profile');
			die();
		}
		// Check required arguments are provided
		elseif(!isset($args['oldPassword'])) {
			$changeError = 'Please provide your old password.';
			include(__DIR__ . '/../../../pages/change_password.php');
			die();
		}
		elseif(!isset($args['newPassword'])) {
			$changeError = 'Please enter a new password';
			include(__DIR__ . '/../../../pages/change_password.php');
			die();
		}
		elseif(!isset($args['newPasswordConfirm'])) {
			$changeError = 'Please enter your new password again in the confirm box.';
			include(__DIR__ . '/../../../pages/change_password.php');
			die();
		}

		// Grab args
		$username = $args['user'];
		$oldPass = $args['oldPassword'];
		$newPass = $args['newPassword'];
		$newConfirm = $args['newPasswordConfirm'];

		// Check user exists
		$user = get_user_by_username($username);
		if(!$user) {
			$changeError = 'User does not exist.';
			include(__DIR__ . '/../../../pages/change_password.php');
			die();
		}

		// Check that the old password matches
		if(!password_matches($oldPass, $user['password'])) {
			$changeError = 'Current password is incorrect.';
			include(__DIR__ . '/../../../pages/change_password.php');
			die();
		}

		// Check new passwords match
		if($newPass !== $newConfirm) {
			$changeError = 'New passwords do not match.';
			include(__DIR__ . '/../../../pages/change_password.php');
			die();
		}

		// Check password meets requirements
		if(!is_valid_password($newPass)) {
			$changeError = 'Password does not meet requirements. Must be between 8-50 characters long.';
			include(__DIR__ . '/../../../pages/change_password.php');
			die();
		}

		// Change the password
		$hashed = hash_password($newPass);
		update_user_password($user['id'], $hashed);

		// Update session data
		$user = get_user_by_username($username);
		$_SESSION['user'] = $user;

		// Return to profile page
		header('Location: /profile');
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