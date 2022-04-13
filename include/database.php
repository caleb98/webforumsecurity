<?php

$DATABASE_USER = getenv('WEBFORUM_USER');
$DATABASE_PASS = getenv('WEBFORUM_PASS');
$DATABASE_NAME = getenv('WEBFORUM_DBNAME');
$DATABASE_HOST = getenv('WEBFORUM_DBHOST');

/**
 * Obtains a new connection to the database.
 * Does not check that the connection was successful.
 * 
 * @return mysqli	the new db connection
 */
function get_db_connection() {
	global $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME, $DATABASE_HOST;
	return new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
}

/**
 * Inserts a new user with the given username, email, and password.
 * Input validation should be performed before this step! 
 * 
 * The password hash may be null, but should only be null in the case
 * that the user is using an alternative sign in method and is providing
 * an alternate id.
 * 
 * @param string	$username	new user's username
 * @param string	$email		new user's email
 * @param string	$password	new user's hashed password
 * @param string	$googleId	new user's googleId (if applicable)
 * 
 * @return mixed	true if insert was successful; error message otherwise.
 */
function insert_user(string $username, string $email, ?string $passwordHash, 
	string $googleId = null, string $discordId = null) : mixed {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		return 'DB connection failed: ' . $conn->connect_error;
	}

	// Insert the user
	$stmt = $conn->prepare(
		'INSERT INTO users (username, email, password, googleId, discordId) 
		VALUES (?, ?, ?, ?, ?)'
	);
	$stmt->bind_param('sssss', $username, $email, $passwordHash, $googleId, $discordId);
	if ($stmt->execute()) {
		return true;
	} else {
		return $stmt->error;
	}
}

/**
 * Adds a googleId value for a given user.
 * 
 * @param string	$username	the username of the user to add the id for
 * @param string	$googleId	the google id for the user
 * 
 * @param mixed		true if the update was successful; error message otherwise
 */
function add_user_google_id(string $username, string $googleId) : mixed {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		return 'DB connection failed: ' . $conn->connect_error;
	}

	$stmt = $conn->prepare('UPDATE users SET googleId=? WHERE username=?');
	$stmt->bind_param("ss", $googleId, $username);
	if ($stmt->execute()) {
		return true;
	} else {
		return $stmt->error;
	}
}

/**
 * Checks if a user exists for the given username.
 * 
 * @return bool		whether or not a user with the given username exists
 */
function username_exists(string $username) : bool {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE username=?');
	$stmt->bind_param("s", $username);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();

	return $result->num_rows > 0;
}

/**
 * Checks if a user exists with the given email.
 * 
 * @return bool		whether or not a user with the given email exists.
 */
function email_exists(string $email) : bool {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE email=?');
	$stmt->bind_param("s", $email);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();

	return $result->num_rows > 0;
}

/**
 * Checks if a user exists with the given googleId.
 * 
 * @return bool		whether or not a user with the given googleId exists.
 */
function google_id_exists(string $id) : bool {
		// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE googleId=?');
	$stmt->bind_param("s", $id);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();

	return $result->num_rows > 0;
}

/**
 * Checks if a user exists with the given discordId.
 * 
 * @return bool		whether or not a user with the given discordId exists.
 */
function discord_id_exists(string $id) : bool {
		// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE discordId=?');
	$stmt->bind_param("s", $id);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();

	return $result->num_rows > 0;
}

/**
 * Retrieves a user from the database using thier username.
 * 
 * @return array	array containing user information
 */
function get_user_by_username(string $username) : array {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE username=?');
	$stmt->bind_param("s", $username);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_assoc();
}

/**
 * Retrieves a user from the database using thier email.
 * 
 * @return array	array containing user information
 */
function get_user_by_email(string $email) {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE email=?');
	$stmt->bind_param("s", $email);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_assoc();
}

/**
 * Retrieves a user from the database using thier google id.
 * 
 * @return array	array containing user information
 */
function get_user_by_google_id(string $id) : ?array {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE googleId=?');
	$stmt->bind_param("s", $id);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_assoc();
}

/**
 * Retrieves a user from the database using thier discord id.
 * 
 * @return array	array containing user information
 */
function get_user_by_discord_id(string $id) : ?array {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement depending on args
	$stmt = $conn->prepare('SELECT * FROM users WHERE discordId=?');
	$stmt->bind_param('s', $id);

	// Execute and grab results
	$stmt->execute();
	$result = $stmt->get_result();
	return $result->fetch_assoc();
}

/**
 * Updates user info for the user with the given id.
 * 
 * @param int	$id			id of the user to update
 * @param array	$userData	new user data
 * 
 * @return string	empty string if successful; database error otherwise.
 */
function update_user(int $id, array $userData) : string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create update statement
	$stmt = $conn->prepare('UPDATE users SET username=?, email=?, password=?, googleId=?, discordId=? WHERE id=?');
	$stmt->bind_param(
		'sssssi',
		$userData['username'],
		$userData['email'],
		$userData['password'],
		$userData['googleId'],
		$userData['discordId'],
		$userData['id']
	);

	// Update the values
	$stmt->execute();
	return $stmt->error;
}

/**
 * Saves a discord access token for a given user, updating the information if
 * the user already has a saved discord access token.
 * 
 * @param int		$id		id of the user to save the token for
 * @param object	$token	the token to store for the user
 * 
 * @return string	empty string if successful; database error otherwise.
 */
function save_user_discord_token(int $id, object $token) : string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement
	$stmt = $conn->prepare(
		'INSERT INTO discord_tokens (userId, accessToken, refreshToken, expires) 
		VALUES (?, ?, ?, ?)
		ON DUPLICATE KEY UPDATE 
			userId=VALUES(userId), 
			accessToken=VALUES(accessToken), 
			refreshToken=VALUES(refreshToken), 
			expires=VALUES(expires)'
	);
	$stmt->bind_param('issi', $id, $token->getToken(), $token->getRefreshToken(), $token->getExpires());

	// Insert/update the values
	$stmt->execute();
	return $stmt->error;
}

/**
 * Retrieves a discord access token for a given user.
 * 
 * @param int	$id		id of the user to retrieve the access token for
 * 
 * @return object	the access token
 */
function get_user_discord_token(int $id) : object {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement
	$stmt = $conn->prepare('SELECT accessToken, refreshToken, expires FROM discord_tokens WHERE userId=?');
	$stmt->prepare('i', $id);

	// Fetch the token data
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows === 0) {
		return null;
	}
	$tokenData = $result->fetch_assoc();

	// Create the access token
	$token = new League\OAuth2\Client\Token\AccessToken([
		'access_token' => $tokenData['accessToken'],
		'refresh_token' => $tokenData['refreshToken'],
		'expires' => $tokenData['expires']
	]);
	return $token;

}

?>