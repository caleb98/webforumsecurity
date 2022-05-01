<?php

$pageTitle = 'Create Category';
include_once(__DIR__ . '/../include/Header.php');

$banError = $banError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- Ban User Form -->
			<form action="/admin/ban" method="post">
				<h1>Ban User</h1>
				<?php if($banError) : ?>
					<div class="text-danger">
						<?php echo htmlspecialchars($banError); ?>
					</div>
				<?php endif; ?>

				<div class="mb-3">
					<label for="username" class="form-label">Username:</label>
					<input type="text" class="form-control" name="username" id="username" autocomplete="off">
					<input type="checkbox" class="form-check-input" name="unban" value="" id="unban">
					<label for="unban" class="form-check-label">Unban this user</label>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>	

		</div>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>