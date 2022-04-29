<?php

/**
 * Represents a function that can be run by a controller.
 */
abstract class ControllerFunction {

	/**
	 * The permission required to access this function.
	 */
	public readonly ?string $permission;

	/**
	 * Creates a new function which requires the specified permission to be run.
	 * 
	 * @param string	$permission		the permission required to run the function
	 */
	public function __construct(string $permission = null) {
		$this->permission = $permission;
	}

	/**
	 * Runs the function.
	 * 
	 * @param string	$context	the context to run the function under
	 * @param array		$args		associative array of function arguments
	 */
	abstract public function run(string $context, array $args): void;

	/**
	 * Determines the context this action is being run under given
	 * the provided arguments. To run using the global context, the empty string
	 * is returned.
	 * 
	 * @param array		$args	the arguments this action will be run with
	 * 
	 * @return ?string	the context under which this function should run given the provided arguments
	 */
	abstract public function resolve_context(array $args): string;

}

?>