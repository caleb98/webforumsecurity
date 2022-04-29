<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DiscordLoginFunction extends ControllerFunction {

	public function run(string $context, array $args): void {
		// Create the discord provider
		$provider = create_discord_provider('http://localhost/login/discord');

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

		// Discord login complete. Get the data and log in the user on the website.
		else {
			$token = $provider->getAccessToken('authorization_code', ['code' => $args['code']]);
			$discordUser = $provider->getResourceOwner($token);
			$discordId = $discordUser->getId();
			$user = get_user_by_discord_id($discordId);

			// Check user exists. If not, redirect them to the registration page
			if($user !== null) {
				login($user);
			}
			else {
				// Store relevant information in the session to be used by the
				// alternate registration flow.
				$_SESSION['discordId'] = $discordId;
				$_SESSION['alternateEmail'] = $discordUser->getEmail();
				$_SESSION['discordToken'] = $token;

				// Remove any remaining google registration info to prevent
				// overlap between two registration flows.
				unset($_SESSION['googleId']);

				// Redirect to the alternate registration page
				header('Location: /register/alternate');
				die();
			}
		}
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>