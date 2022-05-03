<?php

require_once(__DIR__ . '/../../Core.php');
require_once(__DIR__ . '/../../Database.php');
require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayProfileFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		// Obtain the username, either from the args or from the 
		// currently logged-in user.
		if(isset($args['user'])) {
			$username = $args['user'];
		}
		elseif($userIdentifier !== -1) {
			$username = get_user_info()['username'];
		}

		// If the user is not logged in, redirect them to the
		// login page.
		else {
			header('Location: /login');
			die();
		}

		// Check user exists
		$user = get_user_by_username($username);
		if(!$user) {
			$userExists = false;
			include(__DIR__ . '/../../../pages/profile.php');
			die();
		}

		// Grab user's total posts
		$totalPosts = get_user_comment_count($user['id']);

		// Check if profile admin options should be shown
		$showAdmin = $user['id'] === $userIdentifier;

		// Show the page
		include(__DIR__ . '/../../../pages/profile.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>