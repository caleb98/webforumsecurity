<?php

$pageTitle = 'Login';
include_once(__DIR__ . '/../include/Header.php');?>

<div class="container">
	<div class="row">
		<h1>Admin Functions</h1>
		<div class="list-group">
			<a class="list-group-item list-group-item-action" href="/admin/addcategory">Create Category</a>
			<a class="list-group-item list-group-item-action" href="/admin/removecategory">Remove Category</a>
			<a class="list-group-item list-group-item-action" href="/admin/ban">Ban User</a>
		</div>
	</div>
</div>

<?php
include_once(__DIR__ . '/../include/Footer.php');
?>