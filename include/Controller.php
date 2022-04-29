<?php

require_once(__DIR__ . '/RoleProvider.php');
require_once(__DIR__ . '/SecurityException.php');
require_once(__DIR__ . '/ControllerFunction.php');

abstract class Controller {

	/**
	 * The role provider that allows the controller to determine what
	 * roles and permissions a user has access to.
	 */
	private RoleProvider $roleProvider;

	/**
	 * The function map for GET requests.
	 */
	private array $getMap = [];

	/**
	 * The function map for POST requests.
	 */
	private array $postMap = [];

	/**
	 * The name of the default GET function.
	 */
	private ?string $defaultGetMapping;

	/**
	 * The name of the default POST function.
	 */
	private ?string $defaultPostMapping;

	/**
	 * Creates a new controller that collects roles using the given provider.
	 * 
	 * @param RoleProvider	$roleProvider		the provider of roles for this controller
	 * @param string		$defaultGetMapping	the name of the default GET function
	 * @param string		$defaultPostMapping	the name of the default POST function
	 */
	public function __construct(RoleProvider $roleProvider, ?string $defaultGetMapping = null, ?string $defaultPostMapping = null) {
		$this->roleProvider = $roleProvider;
		$this->defaultGetMapping = $defaultGetMapping;
		$this->defaultPostMapping = $defaultPostMapping;
	}

	/**
	 * Sets the default GET function for this controller.
	 * 
	 * @param string	$functionName	the name of the default GET function
	 */
	public function set_default_get_mapping(string $functionName): void {
		$this->defaultGetMapping = $functionName;
	}

	/**
	 * Sets the default POST function for this controller.
	 * 
	 * @param string	$functionName	the name of the default POST function
	 */
	public function set_default_post_mapping(string $functionName): void {
		$this->defaultPostMapping = $functionName;
	}

	/**
	 * Adds a get mapping for this controller.
	 * 
	 * @param string	$functionName	the name of the mapping
	 * @param Action	$function		the function to map to the name
	 */
	public function add_get_mapping(string $functionName, ControllerFunction $function): void {
		$this->getMap[$functionName] = $function;
	}

	/**
	 * Adds a mapping of a resource name to an resource.
	 * 
	 * @param string	$functionName	the name of the mapping
	 * @param Resource	$function		the function to map to the name
	 */
	public function add_post_mapping(string $functionName, ControllerFunction $function): void {
		$this->postMap[$functionName] = $function;
	}

	/**
	 * Attempts to execute a given GET request action using a set of arguments.
	 * Throws an exception if no GET mapping exists for the given name.
	 * 
	 * @param string	$functionName	the name of the function to execute
	 * @param array		$args			arguments for the function's execution
	 */
	public function run_get(string $functionName, array $args): void {
		// Throw exception if no mapping exists for this function.
		if(!isset($this->getMap[$functionName])) {
			throw new UnexpectedValueException("No GET mapping found for function: ${functionName}");
		}

		// Determine the context that the function should run under
		$function = $this->getMap[$functionName];
		$context = $function->resolve_context($args);

		// If the function has a permission it requires to run, check the permissions
		if($function->permission !== null) {
			$userId = $this->get_user_identifier();
			$perms = $this->roleProvider->get_user_permissions($userId, $context);

			// If they don't have the proper permission, deny access.
			if(!in_array($function->permission, $perms)) {
				throw new SecurityException("Access denied.");
			}
		}

		// Execute the action
		$this->getMap[$functionName]->run($context, $args);
	}

	/**
	 * Attempts to execute a given POST request action using a set of arguments.
	 * Throws an exception if no POST mapping exists for the given name.
	 * 
	 * @param string	$functionName	the name of the function to execute
	 * @param array		$args			arguments for the function's execution
	 */
	public function run_post(string $functionName, array $args): void {
		// Throw exception if no mapping exists for this resource.
		if(!isset($this->postMap[$functionName])) {
			throw new UnexpectedValueException("No POST mapping found for resource: ${functionName}");
		}

		// Determine the context that the function should run under
		$function = $this->postMap[$functionName];
		$context = $function->resolve_context($args);

		// If the function has a permission it requires to run, check the permissions
		if($function->permission !== null) {
			$userId = $this->get_user_identifier();
			$perms = $this->roleProvider->get_user_permissions($userId, $context);

			// If they don't have the proper permission, deny access.
			if(!in_array($function->permission, $perms)) {
				throw new SecurityException("Access denied.");
			}
		}

		// Run the function
		$this->postMap[$functionName]->run($context, $args);
	}

	/**
	 * Runs this controller's default GET mapping function.
	 * 
	 * @param array		$args	the arguments to pas to the default function
	 */
	public function run_default_get(array $args): void {
		$this->run_get($this->defaultGetMapping, $args);
	}

	/**
	 * Runs this controller's default POST mapping function.
	 * 
	 * @param array		$args	the arguments to pas to the default function
	 */
	public function run_default_post(array $args): void {
		$this->run_post($this->defaultPostMapping, $args);
	}

	/**
	 * Checks whether a given POST function mapping eixsts
	 * 
	 * @param string	$functionName	the name of the function to check
	 * 
	 * @return bool		whether or not the mapping exists
	 */
	public function post_mapping_exists(string $functionName): bool {
		return isset($this->postMap[$functionName]);
	}

	/**
	 * Checks whether a given GET function mapping exists.
	 * 
	 * @param string	$functionName	the name of the function to check
	 * 
	 * @return bool		whether or not the mapping exists
	 */
	public function get_mapping_exists(string $functionName): bool {
		return isset($this->getMap[$functionName]);
	}

	/**
	 * Function for retrieving the user identifier for the currently logged-in user.
	 * 
	 * @return mixed	the user identifier
	 */
	abstract protected function get_user_identifier(): mixed;

}

?>