<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class HandleAlternateRegisterFunction extends ControllerFunction {

	public function run(string $context, array $args): void {
		// Set alternate registration to true in case they input something invalid
		$alternateRegister = true;

		// Get the username
		$username = $args['usernameAlternate'];
		$email = $_SESSION['alternateEmail'];

		$googleId = $_SESSION['googleId'];
		$discordId = $_SESSION['discordId'];

		// Check validity
		$usernameValid = is_valid_username($username);
		
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);

		// Create appropriate messages for invalid inputs
		if (!$usernameValid) {
			$registerError =  'Invalid username.';
		}
		elseif (!$email) {
			$registerError =  'Invalid email.';
		}

		// Create appropriate message if the username already exists
		elseif (username_exists($username)) {
			$registerError =  'Username is already in use.';
		}

		// Checks passed, so create the user and add them.
		else {
			// Create/insert the user in the DB
			insert_user($username, $email, null, $googleId, $discordId);
			$user = get_user_by_username($username);

			// Store the discord access token if present
			if(isset($_SESSION['discordToken'])) {
				$token = $_SESSION['discordToken'];
				echo save_user_discord_token($user['id'], $token);
			}

			// Unset registration-specific session variables
			unset($_SESSION['discordId']);
			unset($_SESSION['discordToken']);
			unset($_SESSION['googleId']);
			unset($_SESSION['alternateEmail']);

			// Login the user
			login($user);
		}

		// Show the register page with any errors if registration was
		// unsuccessful.
		include(__DIR__ . '/../pages/register.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>