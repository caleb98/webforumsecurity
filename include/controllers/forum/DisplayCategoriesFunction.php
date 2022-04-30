<?php

require_once(__DIR__ . '/../../ControllerFunction.php');

class DisplayCategoriesFunction extends ControllerFunction {

	public function run(mixed $userIdentifier, string $context, array $args): void {
		$categories = get_forum_categories();

		// Filter out non-visible categories
		$categories = array_filter($categories, function($catInfo) use ($userIdentifier) {
			// Non private stay no matter what
			if(!$catInfo['isPrivate']) {
				return true;
			}

			// Grab user permissions for this category context
			$perms = get_user_permissions($userIdentifier, 'forum.' . str_replace(' ', '_', $catInfo['name']));
			return in_array('category.view', $perms);
		});

		include(__DIR__ . '/../../../pages/forum_overview.php');
	}

	public function resolve_context(array $args): string {
		return '';
	}

}

?>