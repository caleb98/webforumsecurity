<?php

require_once('include/core.php');

require_once('include/database.php');
require_once('include/security.php');

// Check if we received credentials from a google sign in
if (is_post_request() && isset($_POST['credential'])) {

	// Decode the JWT
	$client = create_google_api_client();
	$payload = $client->verifyIdToken($_POST['credential']);
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

}

// Check if we're using the discord login
elseif (is_get_request() && $_GET['type'] === 'discord') {

	$provider = create_discord_provider('http://localhost/login.php?type=discord');

	// If we don't have a code yet, send user to discord to authorize
	if (!isset($_GET['code'])) {

		// Retrieve the authorization url and redirect
		$authorizationUrl = $provider->getAuthorizationUrl(get_discord_provider_options());
		$_SESSION['oauth2state'] = $provider->getState();
		header('Location: ' . $authorizationUrl);
		die();

	}

	// If the state we got back was invalid, ignore
	elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
		unset($_SESSION['oauth2state']);
		exit('Invalid state');
	}

	// Discord login complete. Get the data and log in the user on the website.
	else {
		$token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
		$discordUser = $provider->getResourceOwner($token);
		$discordId = $discordUser->getId();
		$user = get_user_by_discord_id($discordId);

		login($user);
	}

}

// Normal login request
elseif (is_post_request()) {
	// Get the login info
	$username = $_POST['username'];
	$password = $_POST['password'];

	$loginError = 'Invalid login credentials.';

	// Check if the user exists.
	if (!username_exists($username)) {
		echo $loginError;
	}
	else {
		// Grab the user
		$userInfo = get_user_by_username($username);
		$passwordHash = $userInfo['password'];

		// Verify password
		if(password_matches($password, $passwordHash)) {
			login($userInfo);
		}
		else {
			echo $loginError;
		}
	}
}

$pageTitle = 'Login';
include_once('include/header.php');

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- Login Form -->
			<form action="login.php" method="post">
				<h1>Login</h1>
				<div class="mb-3">
					<label for="username" class="form-label">Username:</label>
					<input type="text" class="form-control" name="username" id="username" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Password:</label>
					<input type="password" class="form-control" name="password" id="password" autocomplete="off">
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Login</button>
					<div class="form-text">No account? <a href="login.php">Register.</a></div>
				</div>
				<div class="container m-0 p-0">
					<div class="row">
						<div class="col-auto">
							<script src="https://accounts.google.com/gsi/client" async defer></script>
							<div id="g_id_onload" 
								data-client_id="149051990127-2qmd6mtg33r09c0q0989kp7dolmllfp6.apps.googleusercontent.com"
								data-login_uri="http://localhost/login.php" 
								data-auto_prompt="false">
							</div>
							<div class="g_id_signin"
								data-type="standard"
								data-size="large"
								data-theme="outline"
								data-text="sign_in_with"
								data-shape="rectangular"
								data-logo_alignment="left">
							</div>
						</div>
						<div class="col-auto">
							<button class="btn btn-discord" type="button" onclick="window.location.replace('http://localhost/login.php?type=discord')">Login with Discord</button>
						</div>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

<?php
include_once('include/footer.php');
?>