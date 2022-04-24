<?php

/**
 * Represents a resource that can be obtained as the result of a http GET request.
 */
abstract class Resource {

	/**
	 * The permission required to access this resource.
	 */
	public readonly ?string $permission;

	/**
	 * Creates a new resource which requires the specified permission to access.
	 * 
	 * @param string	$permission		the permission required to access the resource
	 */
	public function __construct(string $permission = null) {
		$this->permission = $permission;
	}

	/**
	 * Displays the resource to the user.
	 */
	abstract public function show(string $context, array $args): void;

}

?>