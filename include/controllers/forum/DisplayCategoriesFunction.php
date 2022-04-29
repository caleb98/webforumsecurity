<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayCategoriesFunction extends ControllerFunction {

	public function run(string $context, array $args): void {
		$categories = get_forum_categories();
		$userId = get_user_info()['id'] ?? -1;

		// Filter out non-visible categories
		$categories = array_filter($categories, function($catInfo) use ($userId) {
			// Non private stay no matter what
			if(!$catInfo['isPrivate']) {
				return true;
			}

			// Grab user permissions for this category context
			$perms = get_user_permissions($userId, 'forum.' . str_replace(' ', '_', $catInfo['name']));
			return in_array('category.view', $perms);
		});

		include(__DIR__ . '/../../../pages/forum_overview.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>