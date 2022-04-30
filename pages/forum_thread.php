<?php

require_once(__DIR__ . '/../include/Core.php');

$pageTitle = $threadName ?? 'View Thread';
include_once(__DIR__ . '/../include/Header.php');

$replies = $replies ?? [];
$showReplyBox = $showReplyBox ?? false;
$replyError = $replyError ?? '';
$category = $category ?? '';
$thread = $thread ?? -1;

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

			<div class="col-8 p-3 thread-reply-box thread-reply-text"><?php echo htmlspecialchars($reply['text']) ?></div>
		</div>
	<?php endforeach; ?>
	<?php if($showReplyBox) : ?>
		<div class="row justify-content-center mb-3">
			<div class="col-2"></div>
			<div class="col-8 thread-reply-box">
				<form action="/forum/view?category=<?php echo htmlspecialchars($category); ?>&thread=<?php echo htmlspecialchars($thread); ?>" method="post">
					<div class="my-3">
						<input type="hidden" name="category" id="category" value="<?php echo htmlspecialchars($category); ?>">
						<input type="hidden" name="thread" id="thread" value="<?php echo htmlspecialchars($thread); ?>">

						<h5>Reply</h5>
						<?php if($replyError) : ?>
							<div class="text-danger">
								<?php echo htmlspecialchars($replyError); ?>
							</div>
						<?php endif; ?>
						<textarea class="form-control" name="replyText" id="replyText" autocomplete="off" rows="5"></textarea>
					</div>
					<div class="mb-3">
						<button class="btn btn-primary">Reply</button>
					</div>
				</form>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>