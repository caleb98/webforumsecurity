<?php

require_once(__DIR__ . '/RoleProvider.php');
require_once(__DIR__ . '/Database.php');

class WebForumRoleProvider implements RoleProvider {

	public function get_user_roles(mixed $userIdentifier, string $context): array {
		return get_user_roles($userIdentifier, $context);
	}

	public function get_user_permissions(mixed $userIdentifier, string $context): array {
		return get_user_permissions($userIdentifier, $context);
	}

	public function add_user_role(mixed $userIdentifier, string $context, string $role): bool {
		return add_user_role($userIdentifier, $context, $role);
	}

	public function remove_user_role(mixed $userIdentifier, string $context, string $role): bool {
		return remove_user_role($userIdentifier, $context, $role);
	}

}

?>