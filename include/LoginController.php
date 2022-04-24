<?php

require_once(__DIR__ . '/Core.php');
require_once(__DIR__ . '/Resource.php');
require_once(__DIR__ . '/WebForumController.php');
require_once(__DIR__ . '/WebForumRoleProvider.php');

class LoginController extends WebForumController {

	public function __construct() {
		parent::__construct();

		// Default mapping to show the login page
		$this->add_resource_mapping(
			'default',
			new class extends Resource {
				public function show(string $context, array $args): void {
					include(__DIR__ . '/../pages/login.php');
				}
			}
		);

		// Discord mapping to send the user to the Discord login flow
		$this->add_resource_mapping(
			'discord',
			new class extends Resource {
				public function show(string $context, array $args): void {

					// Create the discord provider
					$provider = create_discord_provider('http://localhost/login/site/discord');

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
							header('Location: /register/site/alternate');
							die();

						}
					}

				}
			}
		);

		// Add mapping for default login post
		$this->add_action_mapping(
			'default',
			new class extends Action {
				public function execute(string $context, array $args): void {
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
			}
		);

		// Add mapping for google login post
		$this->add_action_mapping(
			'google',
			new class extends Action {
				public function execute(string $context, array $args): void {

					// Decode the JWT
					$client = create_google_api_client();
					$payload = $client->verifyIdToken($args['credential']);
					$googleId = $payload['sub'];
					$googleEmail = $payload['email'];

					// See if we can login directly using google id
					if (google_id_exists($googleId)) {
						$user = get_user_by_google_id($googleId);
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

						header('Location: /register/site/alternate');
						die();

					}

				}
			}
		);
	}

}

?>