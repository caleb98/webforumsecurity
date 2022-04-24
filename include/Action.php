<?php

/**
 * Represents an action that can be executed as the result of a http POST request.
 */
abstract class Action {

	/**
	 * The permission required to execute this action.
	 */
	public readonly ?string $permission;

	/**
	 * Creates a new action which requires the specified permission to execute.
	 * 
	 * @param string	$permission		the permission required to execute the action
	 */
	public function __construct(string $permission = null) {
		$this->permission = $permission;
	}

	/**
	 * Executes the action in the given context.
	 */
	abstract public function execute(string $context, array $args): void;

}

?>