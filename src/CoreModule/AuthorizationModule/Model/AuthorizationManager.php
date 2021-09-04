<?php


namespace App\CoreModule\AuthorizationModule\Model;


use Entity\Database\DaoInterface;
use Entity\Model\ModelManager;
use App\CoreModule\RoleModule\Model\Role;
use App\CoreModule\ManagerModule\Model\Module;
use App\CoreModule\ManagerModule\Model\Action;

class AuthorizationManager extends ModelManager
{
	public function __construct(DaoInterface $dao, string $className)
	{
		parent::__construct($dao, $className);
	}

	public function getAuthorizedRolesIds(string $routeName) : array
	{
		$result = $this->builder->select('roles.id')
			->from('roles')
			->join('roles_actions', 'roles.id', 'roles_id')
			->join('actions', 'actions_id', 'actions.id')
			->where('route_name', '=', $routeName)
			->where('authorized', '=', ' 1')
			->execute();
		if ($result) {
			return $result;
		}
		return [];
	}

	public function authorizeAction(Role $role, Action $action)
	{
		return $this->builder->insert('roles_actions')
			->columns('roles_id', 'actions_id', 'authorized')
			->values(['roles_id' => $role->getId(), 'actions_id' => $action->getId(), 'authorized' => 1])
			->execute();
	}

	public function removeActionAuthorization(Role $role, Action $action)
	{
		return $this->builder->delete('roles_actions')
			->where('roles_id', '=', $role->getId())
			->where('actions_id', '=', $action->getId())
			->execute();
	}

	public function getAuthorizedModuleActions(Role $role, Module $module)
	{
		return $this->builder->select('actions_id')
			->from('roles_actions')
			->join('actions', 'actions_id', 'actions.id')
			->where('roles_id','=', $role->getId())
			->where('modules_id','=', $module->getId())
			->where('authorized', '=', true)
			->execute();
	}
}