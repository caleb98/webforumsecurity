<?php

$pageTitle = 'Forum > ' . ucwords(($category ?? 'View'));
include_once(__DIR__ . '/../include/Header.php');

$threads = $threads ?? [];
$showPostButton = $showPostButton ?? false;

?>

<div class="container mt-3">
	<div class="row justify-content-center mb-3">
		<div class="col-8">
			<h3><?php echo ucwords(htmlspecialchars($category)); ?></h3>
			<table class="table table-hover">
				<thead>
					<tr>
						<th scope="col" class="text-center">Thread</th>
						<th scope="col" class="text-center">Creator</th>
						<th scope="col" class="text-center">Created</th>
						<th scope="col" class="text-center">Last Post</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($threads as $thread) : ?>
						<tr class="<?php if($thread['isLocked']) { echo 'table-warning'; }?>">
							<td>
								<a href="/forum/view?category=<?php echo htmlspecialchars($category);?>&thread=<?php echo $thread['threadId'];?>">
									<?php echo htmlspecialchars($thread['title']); ?>
								</a>
							</td>
							<td class='text-center'>
								<a href="/profile/view?user=<?php echo htmlspecialchars($thread['creator']); ?>">
									<?php echo htmlspecialchars($thread['creator']);?>
								</a>
							</td>
							<td class='text-center'><?php echo htmlspecialchars($thread['createDate']); ?></td>
							<td class='text-center'>
								<a href="/profile/view?user=<?php echo htmlspecialchars($thread['replyUser']); ?>">
									<?php echo htmlspecialchars($thread['replyUser']); ?>
								</a>
								<?php echo htmlspecialchars(' at ' . $thread['replyDate']); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php if($showPostButton) : ?>
		<div class="row justify-content-center">
			<div class="col-8">
				<a href="/forum/post?category=<?php echo htmlspecialchars($category);?>" class="btn btn-primary">New Post</a>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>