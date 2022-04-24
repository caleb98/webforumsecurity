<?php

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

/**
 * Checks if the current request is a post request.
 * 
 * @return bool		true if request is post; false otherwise
 */
function is_post_request() {
	return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Checks if the current request is a get request.
 * 
 * @return bool		true if request is get; false otherwise
 */
function is_get_request() {
	return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Checks if a user is currently logged in.
 * 
 * @return bool		true if a user is logged in; false otherwise
 */
function is_logged_in() {
	return isset($_SESSION['user']);
}

/**
 * Logs out the current user by removing their information
 * from the session. Redirects to the given page.
 */
function logout(string $redirect = '/') : void {
	unset($_SESSION['user']);
	header('Location: /');
	die();
}

/**
 * Updates the session to be logged in using the given user info, then 
 * redirects to the given page.
 */
function login(array $user, string $redirect = '/') : void {
	$_SESSION['user'] = $user;
	header('Location: /');
	die();
}

/**
 * Gets the user info array from the session.
 * 
 * @return array	user info
 */
function get_user_info() : array {
	return $_SESSION['user'];
}


function create_google_api_client() : object {
	$client = new Google\Client();
	$client->setAuthConfig('/srv/client_secret.json');
	$client->addScope(Google\Service\Oauth2::USERINFO_EMAIL);
	$client->addScope(Google\Service\Oauth2::USERINFO_PROFILE);
	$client->addScope(Google\Service\Oauth2::OPENID);
	$client->setRedirectUri('http://localhost/oauth2_google.php');
	$client->setAccessType('offline');
	$client->setIncludeGrantedScopes(true);
	$client->setState('temp-state');

	return $client;
}

function create_discord_provider(string $redirectUri) : object {
	return new \Wohali\OAuth2\Client\Provider\Discord([
		'clientId' => getenv('DISCORD_CLIENT_ID'),
		'clientSecret' => getenv('DISCORD_CLIENT_SECRET'),
		'redirectUri' => $redirectUri
	]);
}

function get_discord_provider_options() : array {
	return [
		'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
		'scope' => ['identify', 'email']
	];
}

?>