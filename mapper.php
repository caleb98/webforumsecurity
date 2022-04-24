<?php

require_once('include/Core.php');
require_once('include/LoginController.php');
require_once('include/RegisterController.php');

// Create the controllers list
$controllers = [
	'login' => new LoginController(),
	'register' => new RegisterController()
];

// Get controller info
$controlName = $_GET['control'];
$context = $_GET['context'] ?? 'site';
$name = $_GET['name'] ?? 'default';

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
	unset($_GET['context']);
	unset($_GET['name']);
	$control->show_resource($name, $context, $_GET);
}

// Post requests map to actions
elseif(is_post_request()) {
	$control->run_action($name, $context, $_POST);
}

else {
	echo 'invalid request';
	die();
}

?>