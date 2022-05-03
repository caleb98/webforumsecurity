<?php

$username = $username ?? '';
$pageTitle = $username . '\'s Profile';
include_once(__DIR__ . '/../include/Header.php');

$userExists = $userExists ?? true;
$totalPosts = $totalPosts ?? 'Unknown';
$showAdmin = $showAdmin ?? false;

?>

<div class="container my-3">
	<div class="row">

		<?php if($userExists) : ?>

			<!-- Profile Image -->
			<div class="col-2 text-center thread-reply-profile me-2">
				<img src="/img/profile.ico" class="rounded" style="width:100px;height:100px;">
				<p>
					<?php echo htmlspecialchars($username); ?><br>
					<?php echo 'Total Posts: ' . htmlspecialchars($totalPosts); ?>
				</p>
			</div>

			<!-- Admin Options -->
			<div class="col-8">
				<?php if($showAdmin) : ?>
					<h3>Profile Settings</h3>
					<div class="list-group">
						<a class="list-group-item list-group-item-action" 
							href="/profile/changename?user=<?php echo htmlspecialchars($username); ?>">
							Change Username
						</a>
						<a class="list-group-item list-group-item-action" 
							href="/profile/changepass?user=<?php echo htmlspecialchars($username); ?>">
							Change Password
						</a>
						<a class="list-group-item list-group-item-action" 
							href="/profile/changemail?user=<?php echo htmlspecialchars($username); ?>">
							Change Email
						</a>
					</div>
				<?php else : ?>
					Profile descriptions not implemented.
				<?php endif; ?>
			</div>

		<?php else : ?>
			<div class="col-6">
				No user named <?php echo htmlspecialchars($username); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php')
?>