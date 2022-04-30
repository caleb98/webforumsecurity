<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class GoogleRegisterFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// Remove any remaining discord registration info to prevent
		// overlap between two registration flows.
		unset($_SESSION['discordId']);
		unset($_SESSION['discordToken']);
		
		// Decode the JWT
		$client = create_google_api_client();
		$payload = $client->verifyIdToken($args['credential']);
		$googleId = $payload['sub'];
		$googleEmail = $payload['email'];

		// Check if a user is already registered with that google id
		// and log them in if so.
		if (google_id_exists($googleId)) {
			$user = get_user_by_google_id($googleId);
			login($user);
		}

		// Check if a user already exists with the email associated with
		// the google account that was just logged in. If so, let them 
		// know that they can connect thier google account by logging in
		// using thier username and password first.
		elseif (email_exists($googleEmail)) {
			echo <<<'EOD'
			An account with your gmail already exists. To link your google account,
			first sign in using your username and password then go to your account
			settings.
			EOD;
		} 

		// No user exists, so make a new user
		else {

			// Set the alternate registration flag, which changes the form
			// to only show relevant information for a new third-party-based
			// account.
			$alternateRegister = true;

			// Store relevant user information in the session to be used
			// in the final account creation step.
			$_SESSION['googleId'] = $googleId;
			$_SESSION['alternateEmail'] = $googleEmail;
			include(__DIR__ . '/../../../pages/register.php');

		}
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>