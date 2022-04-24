<?php

require_once(__DIR__ . '/Action.php');
require_once(__DIR__ . '/RoleProvider.php');
require_once(__DIR__ . '/SecurityException.php');

abstract class Controller {

	/**
	 * The role provider that allows the controller to determine what
	 * roles and permissions a user has access to.
	 */
	private RoleProvider $roleProvider;

	/**
	 * The map of action names to actions.
	 */
	private array $actionMap = [];

	/**
	 * The map of resource names to resources.
	 */
	private array $resourceMap = [];

	/**
	 * Creates a new controller that collects roles using the given provider.
	 * 
	 * @param RoleProvider	$roleProvider	the provider of roles for this controller
	 */
	public function __construct(RoleProvider $roleProvider) {
		$this->roleProvider = $roleProvider;
	}

	/**
	 * Adds a mapping of an action name to an action.
	 * 
	 * @param string	$actionName		the name of the action
	 * @param Action	$action			the action to associate with this name
	 */
	public function add_action_mapping(string $actionName, Action $action) {
		$this->actionMap[$actionName] = $action;
	}

	/**
	 * Attempts to execute a given action under a given context using a set of arguments.
	 * Throws an exception if no action with the given name exists.
	 * 
	 * @param string	$actionName		the name of the action to execute
	 * @param string	$context		the context to execute the action within
	 * @param array		$args			arguments for the action's execution
	 * 
	 * @return mixed	the return value of the action
	 */
	public function run_action(string $actionName, string $context, array $args): mixed {
		// Throw exception if no mapping exists for this action.
		if(!isset($this->actionMap[$actionName])) {
			throw new UnexpectedValueException("No mapping found for action: ${actionName}");
		}

		// Check user permissions with the given context
		if(!$this->action_runnable($actionName, $context)) {
			throw new SecurityException('User does not have permissions for this action.');
		}

		// Execute the action
		return $this->actionMap[$actionName]->execute($context, $args);
	}

	/**
	 * Adds a mapping of a resource name to an resource.
	 * 
	 * @param string	$resourceName	the name of the resource
	 * @param Resource	$resource		the resource to associate with this name
	 */
	public function add_resource_mapping(string $resourceName, Resource $resource): void {
		$this->resourceMap[$resourceName] = $resource;
	}

	/**
	 * Displays a resource to the user under the given context.
	 * 
	 * @param string	$resourceName	the anme of the resource
	 * @param string	$context		the context the resource is being accessed under
	 * @param array		$args			arguments for the display of the resource
	 */
	public function show_resource(string $resourceName, string $context, array $args): void {
		// Throw exception if no mapping exists for this resource.
		if(!isset($this->resourceMap[$resourceName])) {
			throw new UnexpectedValueException("No mapping found for resource: ${resourceName}");
		}

		// Check user permissions with the given context
		if(!$this->resource_accessible($resourceName, $context)) {
			throw new SecurityException('User does not have permissions to access this resource.');
		}

		$this->resourceMap[$resourceName]->show($context, $args);
	}

	/**
	 * Returns whether or not the given action is runnable for the current user in the given context.
	 * Will also return false if no action with the given name exists.
	 * 
	 * @param string	$actionName		the name of the action to check
	 * @param string	$context		the context to check the action with
	 * 
	 * @return bool		whether or not the given action can be run in the context by the current user
	 */
	public function action_runnable(string $actionName, string $context): bool {
		// Check that the action exists first
		if(!isset($this->actionMap[$actionName])) {
			return false;
		}

		// See if permissions are required
		$action = $this->actionMap[$actionName];
		if($action->permission === null) {
			return true;
		}

		// Collect user permissions under the context
		$permissions = $this->roleProvider->get_user_permissions(
			$this->get_user_identifier(), $context
		);

		// Check required permission in array
		return in_array($action->permission, $permissions);
	}	

	/**
	 * Returns whether or not the given action is available to the current user in the given context.
	 * Will also return false if no resource with the given name exists.
	 */
	public function resource_accessible(string $resourceName, string $context): bool {
		// Check that the resource exists first
		if(!isset($this->resourceMap[$resourceName])) {
			return false;
		}

		// See if permissions are required
		$resource = $this->resourceMap[$resourceName];
		if($resource->permission === null) {
			return true;
		}

		// Collect user permissions under the context
		$permissions = $this->roleProvider->get_user_permissions(
			$this->get_user_identifier(), $context
		);

		// Check required permission in array
		return in_array($resource->permission, $permissions);
	}

	/**
	 * Checks whether a given actions exists.
	 * 
	 * @param string	$actionName		the action to checl
	 * 
	 * @return bool		whether or not the action exists
	 */
	public function action_exists(string $actionName): bool {
		return isset($this->actionMap[$actionName]);
	}

	/**
	 * Checks whether a given resource exists.
	 * 
	 * @param string	$resourceName	the resource to check
	 * 
	 * @return bool		whether or not the resource exists
	 */
	public function resource_exists(string $resourceName): bool {
		return isset($this->resourceMap[$resourceName]);
	}

	/**
	 * Function for retrieving the user identifier for the currently logged-in user.
	 * 
	 * @return mixed	the user identifier
	 */
	abstract protected function get_user_identifier(): mixed;

}

?>