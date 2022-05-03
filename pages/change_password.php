<?php

$pageTitle = 'Create Category';
include_once(__DIR__ . '/../include/Header.php');

$showUpdateMessage = $showUpdateMessage ?? false;
$changeError = $changeError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- New Category Form -->
			<form action="/profile/changepass?user=<?php echo htmlspecialchars($username); ?>" method="post">
				<input type="hidden" name="user" value="<?php echo htmlspecialchars($username); ?>">
				<h1>Change Password</h1>
				<?php if($changeError) : ?>
					<div class="text-danger">
						<?php echo htmlspecialchars($changeError); ?>
					</div>
				<?php endif; ?>
				<?php if($showUpdateMessage) : ?>
					<div class="mb-3">
						We've updated our password security! Your password still uses the old security settings. Please enter your current password, then choose a new password to update to the new security settings.
					</div>
				<?php endif; ?>
				<div class="mb-3">
					<label for="oldPassword" class="form-label">Current Password:</label>
					<input type="password" class="form-control mb-1" name="oldPassword" placeholder="current password" autocomplete="off">
				</div>
				<div class="mb-3">
				<div class="mb-3">
					<label for="newPassword" class="form-label">New Password:</label>
					<input type="password" class="form-control mb-1" name="newPassword" placeholder="password" autocomplete="off">
					<input type="password" class="form-control" name="newPasswordConfirm" placeholder="confirm password" autocomplete="off">
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Change</button>
				</div>
			</form>	

		</div>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>