<?php

/**
 * Settings used for password hashing. If new a new algorithm or 
 * set of hash options are desired, they may be updated here.
 */
$PASSWORD_HASH_SETTINGS = [
	"algorithm" => PASSWORD_BCRYPT,
	"options" => []
];

/**
 * Hashes a password using the currently active hashing algorithm.
 * 
 * @return string	the hashed password
 */
function hash_password(string $password) : string {
	global $PASSWORD_HASH_SETTINGS;

	$algo = $PASSWORD_HASH_SETTINGS["algorithm"];
	$options = $PASSWORD_HASH_SETTINGS["options"];

	return password_hash($password, $algo, $options);
}

/**
 * Checks if a given password matches the hash.
 * 
 * @return bool		true if the password matches; false otherwise
 */
function password_matches(string $password, string $hashed) : bool {
	return password_verify($password, $hashed);
}

/**
 * Checks a password hash to see if it is using the current
 * password hash settings.
 * 
 * @return bool		true if the hash is using the current hashing settings; false otherwise
 */
function password_needs_update(string $hashed) : bool {
	global $PASSWORD_HASH_SETTINGS;

	$algo = $PASSWORD_HASH_SETTINGS["algorithm"];
	$options = $PASSWORD_HASH_SETTINGS["options"];

	return password_needs_rehash($hashed, $algo, $options);
}

?>