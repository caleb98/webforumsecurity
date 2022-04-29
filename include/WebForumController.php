<?php

require_once(__DIR__ . '/Core.php');
require_once(__DIR__ . '/Controller.php');
require_once(__DIR__ . '/WebForumRoleProvider.php');

/**
 * Parent class for all web forum controllers which
 * implements the get_user_identifier function by
 * retrieving the user ID from the session.
 */
class WebForumController extends Controller {

	/**
	 * Creates a new web forum controller using the given default GET and 
	 * POST mappings.
	 * 
	 * @param ?string	$defaultGetMapping		the default GET function
	 * @param ?string	$defaultPostMapping		the default POST function
	 */
	public function __construct(?string $defaultGetMapping = null, ?string $defaultPostMapping = null) {
		parent::__construct(new WebForumRoleProvider(), $defaultGetMapping, $defaultPostMapping);
	}

	/**
	 * Retrieves the user id from the session. Returns -1
	 * if the user is not logged in.
	 */
	protected function get_user_identifier(): mixed {
		$userInfo = get_user_info();

		// If the user is not logged in, return -1
		if($userInfo === null) {
			return -1;
		}

		// Otheriwse, return the user's id
		else {
			return get_user_info()['id'];
		}
	}

}

?>