<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DiscordRegisterFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// Remove any remaining google registration info to prevent
		// overlap between two registration flows.
		unset($_SESSION['googleId']);

		// Create the OAuth2 provider for discord
		$provider = create_discord_provider('http://localhost/register/discord');

		// If we don't have a code yet, send user to discord to authorize
		if (!isset($args['code'])) {

			// Retrieve the authorization url and redirect
			$authorizationUrl = $provider->getAuthorizationUrl(get_discord_provider_options());
			$_SESSION['oauth2state'] = $provider->getState();
			header('Location: ' . $authorizationUrl);
			die();

		}

		// If the state we got back was invalid, ignore
		elseif (empty($args['state']) || ($args['state'] !== $_SESSION['oauth2state'])) {

			unset($_SESSION['oauth2state']);
			exit('Invalid state');

		}

		// Flow complete, grab the access token and get user details
		else {

			$token = $provider->getAccessToken('authorization_code', ['code' => $args['code']]);
			$user = $provider->getResourceOwner($token);

			$discordId = $user->getId();
			$discordEmail = $user->getEmail();

			// Check if a user is already registered with this discord id
			// and log them in if so.
			if (discord_id_exists($discordId)) {
				$user = get_user_by_discord_id($discordId);

				// Check banned
				if($user['banned']) {
					$loginError = 'This account has been banned.';
					include(__DIR__ . '/../../../pages/login.php');
					die();
				}
				
				login($user);
			}

			// Check if a user already exists with the email associated with
			// the discord account that was just logged in. If so, let them 
			// know that they can connect thier discord account by logging in
			// using thier username and password first.
			elseif (email_exists($discordEmail)) {
				echo <<<'EOD'
				An account with your gmail already exists. To link your google account,
				first sign in using your username and password then go to your account
				settings.
				EOD;
			}

			// No user exists, so make the new user
			else {

				// Set the alternate registration flag, which changes the form
				// to only show relevant information for a new third-party-based
				// account.
				$alternateRegister = true;

				// Store relevant user information in the session to be used
				// in the final account creation step.
				$_SESSION['discordId'] = $discordId;
				$_SESSION['alternateEmail'] = $discordEmail;
				$_SESSION['discordToken'] = $token;
				include(__DIR__ . '/../../../pages/register.php');

			}	

		}
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>