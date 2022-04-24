<?php

require_once('include/Core.php');

$pageTitle = 'Register';
include_once('include/Header.php');

$alternateRegister = $alternateRegister ?? false;
$registerError = $registerError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- Registration Form -->
			<form action="<?php echo $alternateRegister ? '/register/site/alternate' : '/register' ?>" method="post">
				<h1>Register</h1>

				<!-- Show error if present -->
				<?php if($registerError) : ?>
					<div class='text-danger'>
						<?php echo $registerError; ?>
					</div>
				<?php endif; ?>

				<!-- Regular Form Contents -->
				<?php if (!$alternateRegister): ?>
				<div class="mb-3">
					<label for="username" class="form-label">Username:</label>
					<input type="text" class="form-control" name="username" id="username" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="email" class="form-label">Email:</label>
					<input type="email" class="form-control" name="email" id="email" placeholder="name@email.com" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Password:</label>
					<input type="password" class="form-control mb-1" name="password" id="password" placeholder="password" autocomplete="off">
					<input type="password" class="form-control" name="passwordConfirm" id="password-confirm" placeholder="confirm password" autocomplete="off">
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Register</button>
					<div class="form-text">Already have an account? <a href="login">Login.</a></div>
				</div>
				<div class="container m-0 p-0">
					<div class="row">
						<div class="col-auto">
							<script src="https://accounts.google.com/gsi/client" async defer></script>
							<div id="g_id_onload" 
								data-client_id="149051990127-2qmd6mtg33r09c0q0989kp7dolmllfp6.apps.googleusercontent.com"
								data-login_uri="http://localhost/register/site/google" 
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
							<button class="btn btn-discord" type="button" onclick="window.location.replace('http://localhost/register/site/discord')">Register with Discord</button>
						</div>
					</div>
				</div>

				<!-- Google Registration Form Contents -->
				<?php else: ?>
				<div class=mb-3>
					<label for="username" class="form-label">Username:</label>
					<input type="text" class="form-control" name="usernameAlternate" id="username" autocomplete="off">
					<div class="form-text">Enter the username you'd like to use to complete your account creation.</div>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Register</button>
				</div>
				<?php endif; ?>
			</form>

		</div>
	</div>
</div>

<?php
include_once('include/Footer.php');
?>