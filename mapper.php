<?php

require_once('include/Core.php');
require_once('include/Database.php');
require_once('include/controllers/login/LoginController.php');
require_once('include/controllers/register/RegisterController.php');
require_once('include/controllers/forum/ForumViewController.php');
require_once('include/controllers/admin/AdminController.php');
require_once('include/controllers/profile/ProfileController.php');

// Create the controllers list
$controllers = [
	'login' => new LoginController(),
	'register' => new RegisterController(),
	'forum' => new ForumViewController(),
	'admin' => new AdminController(),
	'profile' => new ProfileController(),
];

// Get controller info
$controlName = $_GET['control'];
$functionName = $_GET['function'] ?? null;

// If the user's password needs to be updated, redirect the user to the password update page
$user = get_user_info();
if ($user !== null && isset($user['password']) 
	&& password_needs_update($user['password']) 
	&& !($controlName === 'profile' && $functionName === 'changepass')) {
	header('Location: /profile/changepass?user=' . htmlspecialchars($user['username']) . '&showUpdateMessage=1');
	die();
}

// Check if an associated controller exists
if(!isset($controllers[$controlName])) {
	echo '404 not found';
	die();
}

// Get the associated controller.
$control = $controllers[$controlName];

// Get requests map to resources
if(is_get_request()) {
	unset($_GET['control']);
	unset($_GET['function']);

	try {
		// If no function name was provided, run the default one.
		// Otherwise, run the provided one.
		if($functionName === null) {
			$control->run_default_get($_GET);
		}
		else {
			$control->run_get($functionName, $_GET);
		}
	} catch(UnexpectedValueException $e) {
		echo '404 not found';
		die();
	} catch(SecurityException $e) {
		echo 'not allowed';
		die();
	}
}

// Post requests map to actions
elseif(is_post_request()) {	
	try {
		// If no function name was provided, run the default one.
		// Otherwise, run the provided one.
		if($functionName === null) {
			$control->run_default_post($_POST);
		}
		else {
			$control->run_post($functionName, $_POST);
		}
	} catch(UnexpectedValueException $e) {
		echo '404 not found';
		die();
	} catch(SecurityException $e) {
		echo 'not allowed';
		die();
	}
}

else {
	echo 'invalid request';
	die();
}

?>