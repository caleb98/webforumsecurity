<?php

include_once(__DIR__ . '/../include/Core.php');

$pageTitle = 'Create Category';
include_once(__DIR__ . '/../include/Header.php');

$createError = $createError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- New Category Form -->
			<form action="newcategory" method="post">
				<h1>Create Category</h1>
				<?php if($createError) : ?>
					<div class="text-danger">
						<?php echo $createError; ?>
					</div>
				<?php endif; ?>

				<div class="mb-3">
					<label for="categoryName" class="form-label">Category Name:</label>
					<input type="text" class="form-control" name="categoryName" id="categoryName" autocomplete="off">
					<input type="checkbox" class="form-check-input" name="isPrivate" value="" id="isPrivate">
					<label for="isPrivate" class="form-check-label">Private Category</label>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Create</button>
				</div>
			</form>	

		</div>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>