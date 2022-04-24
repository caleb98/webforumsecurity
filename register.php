<?php

require_once('include/Core.php');

require_once('include/Validation.php');
require_once('include/Database.php');
require_once('include/Security.php');

$alternateRegister = false;

// Check for a registration request via discord account
if (is_get_request() && $_GET['type'] === 'discord') {

	// Remove any remaining google registration info to prevent
	// overlap between two registration flows.
	unset($_SESSION['googleId']);

	// Create the OAuth2 provider for discord
	$provider = create_discord_provider('http://localhost/register.php?type=discord');

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

	// Flow complete, grab the access token and get user details
	else {

		$token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
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

		}	

	}

}
// Check for a registration request via google account
elseif (is_post_request() && isset($_POST['credential'])) {

	// Remove any remaining discord registration info to prevent
	// overlap between two registration flows.
	unset($_SESSION['discordId']);
	unset($_SESSION['discordToken']);
	
	// Decode the JWT
	$client = create_google_api_client();
	$payload = $client->verifyIdToken($_POST['credential']);
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
	}

}

// Check for username submission from google/discord account registration
elseif (is_post_request() && $_POST['usernameAlternate']) {

	// Set alternate registration to true in case they input something invalid
	$alternateRegister = true;

	// Get the username
	$username = $_POST['usernameAlternate'];
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

// Check for regular registration request
elseif (is_post_request()) {

	// Get registration information
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$passwordConfirm = $_POST['passwordConfirm'];

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

// We're just displaying the regular registration page here, so go ahead
// and clear any registration flow session variables to prevent overlap.
else {
	unset($_SESSION['discordId']);
	unset($_SESSION['discordToken']);
	unset($_SESSION['googleId']);
	unset($_SESSION['alternateEmail']);
}

$pageTitle = 'Register';
include_once('include/header.php');

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- Registration Form -->
			<form action="register.php" method="post">
				<h1>Register</h1>

				<!-- Regular Form Contents -->
				<?php if (!$alternateRegister): ?>
				<div class="mb-3">
					<label for="username" class="form-label">Username:</label>
					<input type="text" class="form-control" name="username" id="username" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="email" class="form-label">Email:</label>
					<input type="email" class="form-control" name="email" id="email" placeholder="name@email.com" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Password:</label>
					<input type="password" class="form-control mb-1" name="password" id="password" placeholder="password" autocomplete="off">
					<input type="password" class="form-control" name="passwordConfirm" id="password-confirm" placeholder="confirm password" autocomplete="off">
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Register</button>
					<div class="form-text">Already have an account? <a href="login.php">Login.</a></div>
				</div>
				<div class="container m-0 p-0">
					<div class="row">
						<div class="col-auto">
							<script src="https://accounts.google.com/gsi/client" async defer></script>
							<div id="g_id_onload" 
								data-client_id="149051990127-2qmd6mtg33r09c0q0989kp7dolmllfp6.apps.googleusercontent.com"
								data-login_uri="http://localhost/register.php" 
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
							<button class="btn btn-discord" type="button" onclick="window.location.replace('http://localhost/register.php?type=discord')">Register with Discord</button>
						</div>
					</div>
				</div>

				<!-- Google Registration Form Contents -->
				<?php else: ?>
				<div class=mb-3>
					<label for="username" class="form-label">Username:</label>
					<input type="text" class="form-control" name="usernameAlternate" id="username" autocomplete="off">
					<div class="form-text">Enter the username you'd like to use to complete your account creation.</div>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Register</button>
				</div>
				<?php endif; ?>
			</form>

		</div>
	</div>
</div>

<?php
include_once('include/Footer.php');
?>