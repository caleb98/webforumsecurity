<?php

$pageTitle = 'Create Category';
include_once(__DIR__ . '/../include/Header.php');

$changeError = $changeError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- New Category Form -->
			<form action="/profile/changename?user=<?php echo htmlspecialchars($username); ?>" method="post">
				<input type="hidden" name="user" value="<?php echo htmlspecialchars($username); ?>">
				<h1>Change Username</h1>
				<?php if($changeError) : ?>
					<div class="text-danger">
						<?php echo htmlspecialchars($changeError); ?>
					</div>
				<?php endif; ?>

				<div class="mb-3">
					<label for="newName" class="form-label">New Username:</label>
					<input type="text" class="form-control" name="newName" id="newName" autocomplete="off">
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