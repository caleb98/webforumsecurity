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
function get_db_connection(): mysqli {
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
 * @return string	empty string if successful; error message otherwise.
 */
function insert_user(string $username, string $email, ?string $passwordHash, 
	string $googleId = null, string $discordId = null): string {
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
	$stmt->execute();

	$conn->close();

	if ($stmt->error) {
		return $stmt->error;
	}

	// Add the default user role to this user
	$userId = get_user_by_username($username)['id'];
	$error = add_user_role($userId, '/^$/', 'USER');

	if($error) {
		return $error;
	}

	// Add profile permissions to this user
	$error = add_user_role($userId, '/^user\.' . $username . '$/', 'ACCOUNT_ADMIN');
	return $error;
}

/**
 * Adds a googleId value for a given user.
 * 
 * @param string	$username	the username of the user to add the id for
 * @param string	$googleId	the google id for the user
 * 
 * @param mixed		true if the update was successful; error message otherwise
 */
function add_user_google_id(string $username, string $googleId): mixed {
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
function username_exists(string $username): bool {
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
function email_exists(string $email): bool {
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
function google_id_exists(string $id): bool {
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
function discord_id_exists(string $id): bool {
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
function get_user_by_username(string $username): array {
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
function get_user_by_email(string $email): object {
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
function get_user_by_google_id(string $id): ?array {
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
function get_user_by_discord_id(string $id): ?array {
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
function update_user(int $id, array $userData): string {
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
function save_user_discord_token(int $id, object $token): string {
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
	$tokenValue = $token->getToken();
	$tokenRefresh = $token->getRefreshToken();
	$tokenExpires = $token->getExpires();
	$stmt->bind_param('issi', $id, $tokenValue, $tokenRefresh, $tokenExpires);

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
function get_user_discord_token(int $id): object {
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

/**
 * Creates a new permission.
 * 
 * @param string	$permission		name of the permission to create
 * 
 * @return bool		whether or not creation of the permission was successful
 */
function create_permission(string $permission): bool {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement and insert
	$stmt = $conn->prepare('INSERT IGNORE INTO permissions (permission) VALUES (?);');
	$stmt->bind_param('s', $permission);
	$stmt->execute();

	return $stmt->error === '';
}

/**
 * Creates a new role.
 * 
 * @param string	$role	name of the role to create
 * 
 * @return bool		whether or not creation of the role was successful
 */
function create_role(string $role): bool {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement and insert
	$stmt = $conn->prepare('INSERT IGNORE INTO roles (role) VALUES (?);');
	$stmt->bind_param('s', $role);
	$stmt->execute();

	return $stmt->error === '';
}

/**
 * Adds a permission to a given role.
 * 
 * @param string	$role		the role
 * @param string	$permission	the permission to associate with the role
 * 
 * @return bool		whether or not the update was successful
 */
function add_role_permission(string $role, string $permission): bool {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement to select
	$stmt = $conn->prepare(<<<STR
		INSERT IGNORE INTO role_permissions
		SELECT roles.id, permissions.id
		FROM roles, permissions
		WHERE roles.role=? AND permissions.permission=?;
		STR);

	$stmt->bind_param('ss', $role, $permission);
	$stmt->execute();

	return $stmt->error === '';
}

/**
 * Removes a permission from a given role.
 * 
 * @param string	$role		the role
 * @param string	$permission	the permission to remove from the role
 * 
 * @return bool		whether or not the update was successful
 */
function remove_role_permission(string $role, string $permission): bool {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement to select
	$stmt = $conn->prepare(<<<STR
		DELETE FROM role_permissions
		WHERE roleId = (SELECT id FROM roles WHERE role = ?)
		AND permissionId = (SELECT id FROM permissions WHERE permission = ?);
		STR);

	$stmt->bind_param('ss', $role, $permission);
	$stmt->execute();

	return $stmt->error === '';
}

/**
 * Retrieves an array of permissions that are associated with the given role.
 */
function get_role_permissions(string $role): array {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement and execute
	$stmt = $conn->prepare(<<<STR
		SELECT role, permission FROM role_permissions
		JOIN roles ON role_permissions.roleId = roles.id
		JOIN permissions ON role_permissions.permissionId = permissions.id
		WHERE role = ?;
		STR);
	$stmt->bind_param('s', $role);
	$stmt->execute();
	$result = $stmt->get_result();

	// Run through all the rows and collect the permissions
	$permissions = [];
	while($row = $result->fetch_assoc()) {
		array_push($permissions, $row['permission']);
	}

	return $permissions;
}

/**
 * Retrieves an array of roles that a user has under the given context.
 */
function get_user_roles(int $id, string $context): array {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement and execute
	$stmt = $conn->prepare(<<<STR
		SELECT role, context FROM user_roles
		JOIN roles ON user_roles.roleId = roles.id
		WHERE userId = ?;
		STR);
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();

	// Run through all the rows and collect the roles.
	$roles = [];
	while($row = $result->fetch_assoc()) {
		// If the role is not already present and the 
		// context pattern from the db matches the current
		// operating context, add the role.
		if(!in_array($roles, $row['role']) && preg_match($row['context'], $context)) {
			$roles[] = $row['role'];
		}
	}

	return $roles;
}

/**
 * Retrieves an array of all permissions that a user has under a given context.
 */
function get_user_permissions(int $id, string $context): array {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement and execute
	$stmt = $conn->prepare(<<<STR
		SELECT DISTINCT permission, context FROM user_roles
		JOIN roles ON user_roles.roleId = roles.id
		JOIN role_permissions ON user_roles.roleId = role_permissions.roleId
		JOIN permissions ON role_permissions.permissionId = permissions.id
		WHERE userId = ?;
		STR);
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();

	// Run through all the rows and collect the permissions.
	$permissions = [];
	while($row = $result->fetch_assoc()) {
		// If the permission is not already present and the
		// context pattern from the db matches the current
		// operating context, add the permission.
		if(!in_array($row['permission'], $permissions) && preg_match($row['context'], $context)) {
			$permissions[] = $row['permission'];
		}
	}

	return $permissions;
}

/**
 * Adds a role to a given user under a given context.
 * 
 * @param int		$id			the id of the user to add the role to
 * @param string	$context	a pattern for contexts in which this role should be applied
 * @param string	$role		the role to add
 * 
 * @return string	empty string if successful; error message otherwise
 */
function add_user_role(int $id, string $context, string $role): string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create and execute statement
	$stmt = $conn->prepare(<<<STR
		INSERT IGNORE INTO user_roles
		SELECT ?, roles.id, ?
		FROM roles
		WHERE roles.role=?;
		STR);

	$stmt->bind_param('iss', $id, $context, $role);
	$stmt->execute();

	return $stmt->error;
}

/**
 * Removes a role from a given user under a given context.
 * 
 * @param int		$id			the id of the user to remove the role from
 * @param string	$context	the pattern for contexts to be removes
 * @param string	$role		the role that should be removed for the given context pattern
 * 
 * @return bool		whether or not the operation was successful
 */
function remove_user_role(int $id, string $context, string $role): bool {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement and execute
	$stmt = $conn->prepare(<<<STR
		DELETE FROM user_roles
		WHERE userId = ? 
		AND context = ? 
		AND roleId = (SELECT id FROM roles WHERE role = ?)
		STR);

	$stmt->bind_param('iss', $id, $context, $role);
	$stmt->execute();

	return $stmt->error === '';
}

/**
 * Creates a new forum category.
 * 
 * @param string	$name		the name of the new category
 * @param bool		$isPrivate	whether or not the category is private and only visible to users with proper roles
 * 
 * @return string	empty string if successful; error message otherwise
 */
function create_forum_category(string $name, bool $isPrivate = false): string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement and execute
	$stmt = $conn->prepare('INSERT IGNORE INTO categories (name, isPrivate) VALUES (?, ?);');
	$stmt->bind_param('si', $name, $isPrivate);
	$stmt->execute();

	return $stmt->error;
}

/**
 * Retrieves an array of all forum categories.
 * 
 * @return array	array containing all forum category information
 */
function get_forum_categories(): array {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement
	$result = $conn->query('SELECT * FROM categories;');
	$categories = [];

	while($category = $result->fetch_assoc()) {
		$categories[] = $category;
	}

	return $categories;
}

/**
 * 
 */
function add_forum_thread(string $category, string $postName, string $postText, int $userId): string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement to make the thread
	$stmt = $conn->prepare(<<<STR
		INSERT INTO threads (title, categoryId, creatorId)
		VALUES (
			?, 
			(SELECT id FROM categories WHERE name = ?), 
			?);
		STR
	);
	$stmt->bind_param('ssi', $postName, $category, $userId);
	$stmt->execute();

	if($stmt->error) {
		return $stmt->error;
	}

	// Add the initial post text
	$threadId = $conn->insert_id;
	$stmt = $conn->prepare(<<<STR
		INSERT INTO comments (text, threadId, commenterId)
		VALUES (?, ?, ?)
		STR
	);
	$stmt->bind_param('sii', $postText, $threadId, $userId);
	$stmt->execute();

	return $stmt->error;
}

function add_thread_comment(int $threadId, int $userId, string $text): string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create statement
	$stmt = $conn->prepare('INSERT INTO comments (text, threadId, commenterId) VALUES (?, ?, ?);');
	$stmt->bind_param('sii', $text, $threadId, $userId);
	$stmt->execute();
	return $stmt->error;	
}

/**
 * Retrieves a set of threads for the category.
 * 
 * @param string	$category		the category to retrieve threads for
 * @param int		$pageSize		number of threads to return
 * @param int		$pageNum		the page of threads to retrieve
 * 
 * @return array|string		array of forum post data if successful; error string otherwise
 */
function get_category_threads(string $category, int $pageSize, int $pageNum): array|string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Calculate offset for pagination
	$offset = $pageSize * $pageNum;

	// Create statement
	$stmt = $conn->prepare(<<<STR
		SELECT
			threads.id AS threadId,
			threads.title AS title, 
			creatorUser.username AS creator, 
			threads.date AS createDate,
			recentUser.username AS replyUser,
			MAX(comments.date) AS replyDate
		FROM threads
		JOIN users AS creatorUser ON creatorUser.id = threads.creatorId
		JOIN comments ON comments.threadId = threads.id
		JOIN users AS recentUser ON recentUser.id = comments.commenterId
		WHERE categoryId = (SELECT id FROM categories WHERE name = ?)
		GROUP BY threads.id
		ORDER BY comments.date DESC
		LIMIT ? OFFSET ?;
		STR
	);
	$stmt->bind_param('sii', $category, $pageSize, $offset);
	$stmt->execute();

	if($stmt->error) {
		return $stmt->error;
	}

	$result = $stmt->get_result();

	// Loop through results and combine
	$threads = [];
	while($row = $result->fetch_assoc()) {
		$threads[] = $row;
	}

	return $threads;
}

/**
 * Retrieves the name of a thread in a category with a given id.
 * 
 * @param string	$category	the category the thread is in
 * @param int		$threadId	the id of the thread
 * 
 * @return ?string	the name of the thread; null if no thread with this id exists in the given category
 */
function get_thread_name(string $category, int $threadId): ?string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Run statement
	$stmt = $conn->prepare(
		'SELECT title FROM threads WHERE id = ? AND categoryId = (SELECT id FROM categories WHERE name = ?)');
	$stmt->bind_param('is', $threadId, $category);
	$stmt->execute();

	// Grab resulting thread name
	$result = $stmt->get_result();
	if($result->num_rows != 1) {
		return null;
	}
	return $result->fetch_assoc()['title'];
}

function get_thread_replies(string $category, int $threadId): array|string {
	// Connect to the database
	$conn = get_db_connection();
	if ($conn->connect_error) {
		die('DB connection failed: ' . $conn->connect_error);
	}

	// Create the statement
	$stmt = $conn->prepare(<<<STR
		SELECT users.username AS user, comments.text AS text, comments.date AS date FROM threads
		JOIN categories ON categories.id = threads.categoryId
		JOIN comments ON comments.threadId = threads.id
		JOIN users ON users.id = comments.commenterId
		WHERE threads.id = ? AND categories.id = (SELECT id FROM categories WHERE name = ?)
		ORDER BY comments.date;
		STR
	);
	$stmt->bind_param('is', $threadId, $category);
	$stmt->execute();

	if($stmt->error) {
		return $stmr->error;
	}

	// Get results and add to array
	$result = $stmt->get_result();
	$replies = [];

	while($row = $result->fetch_assoc()) {
		$replies[] = $row;
	}

	return $replies;
}

?>