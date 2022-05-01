<?php

require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleBanUserFunction extends ControllerFunction {

	public function __construct() {
		parent::__construct('user.ban');
	}

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$username = trim($args['username'] ?? '');
		$unban = isset($args['unban']);

		// Check that a username was actually entered
		if($username === '') {
			$banError = 'Please enter a username.';
			include(__DIR__ . '/../../../pages/ban_user.php');
			die();			
		}

		// Check that the user exists
		if(!username_exists($username)) {
			$banError = 'No user with that username found.';
			include(__DIR__ . '/../../../pages/ban_user.php');
			die();
		}

		// Update the ban status
		$newStatus = !$unban;
		$user = get_user_by_username($username);
		$error = set_user_ban_status($user['id'], $newStatus);

		// Show error if occurred
		if($error) {
			$banError = $error;
			include(__DIR__ . '/../../../pages/ban_user.php');
			die();		
		}

		// Otherwise, redirect back to the admin page
		header('Location: /admin');
		die();
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>