<?php 

require_once('include/Core.php');
require_once('include/Security.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="/css/style.css">
	<title>
		<?php
			echo isset($pageTitle) ? $pageTitle : 'Internet Forum 3000';
		?>
	</title>
</head>
<body>

<?php
// Check if the user is signed in and if they need to update their password
if (is_logged_in()) {
	$user = get_user_info();
	if (isset($user['password']) && password_needs_update($user['password'])) {
		echo 'password must be updated';
	}
	elseif (isset($user['password'])) {
		echo 'password is okay';
	}
	else {
		echo 'no check for password - account made via 3rd party';
	}
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<div class="container-lg">

		<a class="navbar-brand" href="/">Internet Forum 3000</a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
				<!-- Display login/register buttons if not logged in. -->
				<?php if (!is_logged_in()): ?>
				<li class="nav-item ms-auto">
					<a class="nav-link" href="/login">Login</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/register">Register</a>
				</li>

				<!-- Otherwise, show profile buttons. -->
				<?php else: ?>
				<li class="nav-item ms-auto">
					<a class="nav-link" href="/profile"><?php echo htmlspecialchars(get_user_info()['username']); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/logout.php">Logout</a>
				</li>
				<?php endif; ?>
			</ul>
		</div>

	</div>
</nav>