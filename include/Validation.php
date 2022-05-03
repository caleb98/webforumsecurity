<?php

/**
 * Checks if the given username matches the criteria for 
 * username strings.
 * 
 * @return bool		true if the username is valid; false otherwise
 */
function is_valid_username(string $username) : bool {
	// Check general username pattern
	if(!preg_match('/^[a-z\d_]{3,16}$/i', $username)) {
		return false;
	}

	// Check no underscores adjacent
	if(str_contains($username, '__')) {
		return false;
	}

	// Checks passed, so the name is valid
	return true;
}

/**
 * Checks if the given password matches the criteria for 
 * password strings.
 * 
 * @return bool		true if the password is valid; false otherwise
 */
function is_valid_password(string $password) : bool {
	// We don't want to restrict passwords by enforcing
	// too many requirements, so only check that the
	// length is suitable.
	return strlen($password) >= 8 && strlen($password) <= 50;
}

?> 
