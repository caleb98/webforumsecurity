<?php

require_once(__DIR__ . '/Core.php');
require_once(__DIR__ . '/Validation.php');
require_once(__DIR__ . '/Security.php');

require_once(__DIR__ . '/Resource.php');
require_once(__DIR__ . '/WebForumController.php');
require_once(__DIR__ . '/WebForumRoleProvider.php');

class RegisterController extends WebForumController {

	public function __construct() {
		parent::__construct();

		// Default mapping to show the registration page
		$this->add_resource_mapping(
			'default',
			new class extends Resource {
				public function show(string $context, array $args): void {
					include(__DIR__ . '/../pages/register.php');
				}
			}
		);

		// Alternate registration mapping to allow redirects to registration
		// if the user tries to login using a 3rd party account that has not
		// yet registered.
		$this->add_resource_mapping(
			'alternate',
			new class extends Resource {
				public function show(string $context, array $args): void {
					$alternateRegister = true;
					include(__DIR__ . '/../pages/register.php');
				}
			}
		);

		// Discord mapping to send the user to the Discord login flow
		$this->add_resource_mapping(
			'discord', 
			new class extends Resource {
				public function show(string $context, array $args): void {

					// Remove any remaining google registration info to prevent
					// overlap between two registration flows.
					unset($_SESSION['googleId']);

					// Create the OAuth2 provider for discord
					$provider = create_discord_provider('http://localhost/register/site/discord');

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
							include(__DIR__ . '/../pages/register.php');

						}	

					}

				}
			}
		);

		// Google mapping to handle google login flow
		$this->add_action_mapping(
			'google',
			new class extends Action {
				public function execute(string $context, array $args): void {

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
						include(__DIR__ . '/../pages/register.php');

					}

				}
			}
		);

		// Default login post mapping
		$this->add_action_mapping(
			'default',
			new class extends Action {
				public function execute(string $context, array $args): void {

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
						echo 'Invalid username.';
					}
					elseif (!$passwordValid) {
						echo 'Invalid password.';
					}
					elseif (!$email) {
						echo 'Invalid email.';
					}

					// Create appropriate messages for if username/email exists
					elseif (username_exists($username)) {
						echo 'Username is already in use.';
					}
					elseif (email_exists($email)) {
						echo 'Email is already in use.';
					}

					// Checks passed. We can create the user and add them.
					else {
						$passwordHash = hash_password($password);
						insert_user($username, $email, $passwordHash);

						// Login the user
						$user = get_user_by_username($username);
						login($user);
					}

				}
			}
		);

		// Alternate OAuth registration flow mapping
		$this->add_action_mapping(
			'alternate',
			new class extends Action {
				public function execute(string $context, array $args): void {

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
						echo 'Invalid username.';
					}
					elseif (!$email) {
						echo 'Invalid email.';
					}

					// Create appropriate message if the username already exists
					elseif (username_exists($username)) {
						echo 'Username is already in use.';
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

				}
			}
		);
	}

}

?>