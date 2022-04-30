<?php

$pageTitle = 'New Post';
include_once(__DIR__ . '/../include/Header.php');

$category = $category ?? '';
$postCreationError = $postCreationError ?? '';

?>

<div class="container">
	<div class="row">
		<div class="col-6">

			<!-- Login Form -->
			<form action="/forum/post" method="post">
				<input type="hidden" id="category" name="category" value="<?php echo htmlspecialchars($category); ?>">

				<h1>Create Post</h1>
				<?php if($postCreationError) : ?>
					<div class='text-danger'>
						<?php echo htmlspecialchars($postCreationError); ?>
					</div>
				<?php endif; ?>
				<div class="mb-3">
					<label for="title" class="form-label">Title:</label>
					<input type="text" class="form-control" name="title" id="title" autocomplete="off">
				</div>
				<div class="mb-3">
					<label for="postText" class="form-label">Text:</label>
					<textarea class="form-control" name="postText" id="postText" autocomplete="off" rows="5"></textarea>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Create</button>
				</div>
			</form>

		</div>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php');
?>