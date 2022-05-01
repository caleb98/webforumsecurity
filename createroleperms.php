<?php

require_once('include/Core.php');
require_once('include/Database.php');

create_permission('user.change_name');
create_permission('user.change_pass');
create_permission('user.change_mail');
create_permission('user.ban');

create_permission('category.view');
create_permission('category.add');
create_permission('category.remove');
create_permission('category.modify');
create_permission('admin.view');

create_permission('thread.create');
create_permission('thread.reply');
create_permission('thread.like');
create_permission('thread.lock');
create_permission('thread.delete');

create_role('USER');
add_role_permission('USER', 'category.view');
add_role_permission('USER', 'thread.create');
add_role_permission('USER', 'thread.reply');
add_role_permission('USER', 'thread.like');

create_role('MODERATOR');
add_role_permission('MODERATOR', 'category.view');
add_role_permission('MODERATOR', 'category.modify');
add_role_permission('MODERATOR', 'thread.create');
add_role_permission('MODERATOR', 'thread.reply');
add_role_permission('MODERATOR', 'thread.like');
add_role_permission('MODERATOR', 'thread.lock');
add_role_permission('MODERATOR', 'thread.delete');

create_role('ADMIN');
add_role_permission('ADMIN', 'user.ban');
add_role_permission('ADMIN', 'category.add');
add_role_permission('ADMIN', 'category.remove');
add_role_permission('ADMIN', 'category.modify');
add_role_permission('ADMIN', 'thread.lock');
add_role_permission('ADMIN', 'thread.delete');
add_role_permission('ADMIN', 'admin.view');

create_role('ACCOUNT_ADMIN');
add_role_permission('ACCOUNT_ADMIN', 'user.change_name');
add_role_permission('ACCOUNT_ADMIN', 'user.change_pass');
add_role_permission('ACCOUNT_ADMIN', 'user.change_mail');

echo '<pre>';
echo '</pre>';

?> 
