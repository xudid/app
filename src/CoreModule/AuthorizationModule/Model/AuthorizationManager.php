<?php


namespace App\CoreModule\AuthorizationModule\Model;


use Entity\Database\DaoInterface;
use Entity\Model\ModelManager;

class AuthorizationManager extends ModelManager
{
	public function __construct(DaoInterface $dao, string $className)
	{
		parent::__construct($dao, $className);
	}

	public function getAuthorizedRolesIds(string $routeName) : array
	{
		$request= $this->builder->select('roles.id')
			->from('roles')
			->join('roles_actions', 'roles.id', 'roles_id')
			->join('actions', 'actions_id', 'actions.id')
			->where('route_name', '=', $routeName)
			->where('authorized', '=', ' 1');
		$result = $this->builder->execute($request);
		if ($result) {
			return $result;
		}
		return [];
	}
}