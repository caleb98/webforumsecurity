<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleLoginFunction extends ControllerFunction {

	public function run(string $context, array $args): void {
		// Get the login info
		$username = $args['username'];
		$password = $args['password'];

		// Check if the user exists.
		if (username_exists($username)) {
			// Grab the user
			$userInfo = get_user_by_username($username);
			$passwordHash = $userInfo['password'];

			// Verify password
			if(password_matches($password, $passwordHash)) {
				login($userInfo);
			}
		}

		// Login was unsuccessful. Show error message.
		$loginError = 'Invalid login credentials.';
		include(__DIR__ . '/../pages/login.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>