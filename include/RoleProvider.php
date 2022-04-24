<?php

interface RoleProvider {

	/**
	 * Returns an array of roles associated with the given user in the given context.
	 * 
	 * @param mixed		$userIdentifier		unique identifier to indicate which user to get roles for
	 * @param string	$context			the context to retrieve the user roles for
	 * 
	 * @return array	an array of strings representing the roles the user has in the given context
	 */
	public function get_user_roles(mixed $userIdentifier, string $context): array;

	/**
	 * Returns an array of permissions associated with the given user in the given context.
	 * The permissions will be the union of all permissions of the roles the user has.
	 * 
	 * @param mixed		$userIdentifier		unique identifier to indicate which user to get permissions for
	 * @param string	$context			the context to retrieve the user roles for
	 * 
	 * @return array	an array of strings representing the roles the user has in the given context
	 */
	public function get_user_permissions(mixed $userIdentifier, string $context): array;

	/** 
	 * Adds a role to a user for a given context.
	 * 
	 * @param mixed		$userIdentifier		unique identifier to indicate which user to add role for
	 * @param string	$context			the context to add the role for
	 * @param string	$role				the role to add
	 * 
	 * @return bool		whether or not the operation was successful
	 */
	public function add_user_role(mixed $userIdentifier, string $context, string $role): bool;

	/**
	 * Removes a role from a user for a given context.
	 * 
	 * @param mixed		$userIdentifier		unique identifier to indicate which user to remove role for
	 * @param string	$context			the context to remove the role from
	 * @param string	$role				the role to remove
	 * 
	 * @return bool		whether or not the operation was successful
	 */
	public function remove_user_role(mixed $userIdentifier, string $context, string $role): bool;

}

?>