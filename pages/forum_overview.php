<?php

require_once(__DIR__ . '/../include/Core.php');

$pageTitle = 'Forum';
include_once(__DIR__ . '/../include/Header.php');

$categories = $categories ?? [];

?>

<div class="container mt-3">
	<div class="row justify-content-center">
		<div class="col-6">
			<table class="table">
				<thead>
					<tr>
						<th scope="col" class="text-center">Category</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($categories as $category) : ?>
						<tr>
							<td class="text-center">
								<a href="/forum/view?category=<?php echo htmlspecialchars($category['name']);?>">
									<?php echo htmlspecialchars(ucwords($category['name'])); ?>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>