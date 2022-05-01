<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleLoginFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
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

				// Check banned
				if($userInfo['banned']) {
					$loginError = 'This account has been banned.';
					include(__DIR__ . '/../../../pages/login.php');
					die();
				}

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