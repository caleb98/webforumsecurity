<?php

include_once(__DIR__ . '/../include/Core.php');

$pageTitle = 'Create Category';
include_once(__DIR__ . '/../include/Header.php');

$categories = $categories ?? [];
$removeError = $removeError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- New Category Form -->
			<form action="/admin/removecategory" method="post">
				<h1>Remove Category</h1>
				<?php if($removeError) : ?>
					<div class="text-danger">
						<?php echo htmlspecialchars($removeError); ?>
					</div>
				<?php endif; ?>
				<div class="mb-3">
					<ul class="list-group">
						<?php foreach($categories as $category) :?>
							<li class="list-group-item">
								<input class="form-check-input me-1" type="checkbox" value="" name="category-<?php echo htmlspecialchars($category['id'])?>">
								<?php echo htmlspecialchars($category['name']) ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="mb-3 text-danger form-check">
					<input class="form-check-input" type="checkbox" value="" name="confirmDelete" id="confirmDelete">
					<label class="form-check-label" for="confirmDelete">
						Removing a category will permanently delete ALL threads and replies within that category! Check this box to confirm that you would like to delete the categories selected above.
					</label>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Delete</button>
				</div>
			</form>	

		</div>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>