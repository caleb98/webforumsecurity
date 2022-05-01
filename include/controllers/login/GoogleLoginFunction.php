<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class GoogleLoginFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// Decode the JWT
		$client = create_google_api_client();
		$payload = $client->verifyIdToken($args['credential']);
		$googleId = $payload['sub'];
		$googleEmail = $payload['email'];

		// See if we can login directly using google id
		if (google_id_exists($googleId)) {
			$user = get_user_by_google_id($googleId);

			// Check banned
			if($user['banned']) {
				$loginError = 'This account has been banned.';
				include(__DIR__ . '/../../../pages/login.php');
				die();
			}

			login($user);
		}

		// Otherwise check if the email exists. If so, let the 
		// user know they can link their google account by logging
		// in normally first.
		elseif (email_exists($googleEmail)) {
			echo <<<'EOD'
			You have not yet activated google sign in for this account. To do so,
			log into your account normally, then go to your account settings.
			EOD;
		}

		// Otherwise, redirect to the registration page
		else {

			// Set the alternate registration flag, which changes the form
			// to only show relevant information for a new third-party-based
			// account.
			$alternateRegister = true;

			// Remove any remaining discord registration info to prevent
			// overlap between two registration flows.
			unset($_SESSION['discordId']);
			unset($_SESSION['discordToken']);

			// Store relevant user information in the session to be used
			// in the final account creation step.
			$_SESSION['googleId'] = $googleId;
			$_SESSION['alternateEmail'] = $googleEmail;

			header('Location: /register/alternate');
			die();

		}
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>