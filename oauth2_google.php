<?php

require_once('include/Core.php');

// Create the google api client
$client = create_google_api_client();

// If there's an access code, use that to grab the token and then
// request access token.
if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	$token = $client->getAccessToken();
	//store_google_access_token($token);

	$service = new Google\Service\Oauth2($client);

	echo '<pre>';
	echo var_dump($service->userinfo->get());
	echo '</pre>';
}

// If there's an error, handle it.
elseif (isset($_GET['error'])) {
	echo 'Error: ' . $_GET['error'];
}

// Otherwise, use the client to redirect to the google consent page.
else {
	$authUrl = $client->createAuthUrl();
	header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
}

?>