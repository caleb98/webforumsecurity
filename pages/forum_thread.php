<?php

require_once(__DIR__ . '/../include/Core.php');

$pageTitle = $threadName ?? 'View Thread';
include_once(__DIR__ . '/../include/Header.php');

$replies = $replies ?? [];

?>

<div class="container mt-3">
	<div class="row justify-content-center mb-3">
		<div class="col-auto">
			<h4><?php echo htmlspecialchars($threadName); ?></h4>
		</div>
	</div>
	<?php foreach($replies as $reply) : ?>
		<div class="row justify-content-center mb-3">
			<div class="col-2 text-center thread-reply-profile me-2">
				<img src="/img/profile.ico" class="rounded" style="width:100px;height:100px;">
				<p>
					<?php echo htmlspecialchars($reply['user']) ?><br>
					<?php echo htmlspecialchars($reply['date']) ?>
				</p>
			</div>

			<div class="col-8 p-3 thread-reply-text">
				<?php echo htmlspecialchars($reply['text']) ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>