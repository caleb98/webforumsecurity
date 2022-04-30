<?php

require_once(__DIR__ . '/../../Core.php');
require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../Security.php');
require_once(__DIR__ . '/../../Validation.php');

require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleRegisterFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// Get registration information
		$username = $args['username'];
		$email = $args['email'];
		$password = $args['password'];
		$passwordConfirm = $args['passwordConfirm'];

		// Check the validity of the username and the password
		$usernameValid = is_valid_username($username);
		$passwordValid = is_valid_password($password) && $password === $passwordConfirm;

		$email = filter_var($email, FILTER_VALIDATE_EMAIL);

		// Create appropriate messages for invalid inputs
		if (!$usernameValid) {
			$registerError = 'Invalid username.';
		}
		elseif (!$passwordValid) {
			$registerError = 'Invalid password.';
		}
		elseif (!$email) {
			$registerError =  'Invalid email.';
		}

		// Create appropriate messages for if username/email exists
		elseif (username_exists($username)) {
			$registerError =  'Username is already in use.';
		}
		elseif (email_exists($email)) {
			$registerError =  'Email is already in use.';
		}

		// Checks passed. We can create the user and add them.
		else {
			$passwordHash = hash_password($password);
			insert_user($username, $email, $passwordHash);

			// Login the user
			$user = get_user_by_username($username);
			login($user);
		}

		// Show the register page with any errors if registration was
		// unsuccessful.
		include(__DIR__ . '/../../../pages/register.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>