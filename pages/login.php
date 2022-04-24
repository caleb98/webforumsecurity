<?php

require_once('include/Core.php');

$pageTitle = 'Login';
include_once('include/Header.php');

$loginError = $loginError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- Login Form -->
			<form action="login" method="post">
				<h1>Login</h1>
				<?php if($loginError) : ?>
					<div class='text-danger'>
						<?php echo $loginError; ?>
					</div>
				<?php endif; ?>
				<div class="mb-3">
					<label for="username" class="form-label">Username:</label>
					<input type="text" class="form-control" name="username" id="username" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Password:</label>
					<input type="password" class="form-control" name="password" id="password" autocomplete="off">
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Login</button>
					<div class="form-text">No account? <a href="/register">Register.</a></div>
				</div>
				<div class="container m-0 p-0">
					<div class="row">
						<div class="col-auto">
							<script src="https://accounts.google.com/gsi/client" async defer></script>
							<div id="g_id_onload" 
								data-client_id="149051990127-2qmd6mtg33r09c0q0989kp7dolmllfp6.apps.googleusercontent.com"
								data-login_uri="http://localhost/login/site/google" 
								data-auto_prompt="false">
							</div>
							<div class="g_id_signin"
								data-type="standard"
								data-size="large"
								data-theme="outline"
								data-text="sign_in_with"
								data-shape="rectangular"
								data-logo_alignment="left">
							</div>
						</div>
						<div class="col-auto">
							<button class="btn btn-discord" type="button" onclick="window.location.replace('http://localhost/login/site/discord')">Login with Discord</button>
						</div>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

<?php
include_once('include/Footer.php');
?>