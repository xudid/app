<?php


namespace App\CoreModule\RoleModule\Model;


use App\CoreModule\ManagerModule\Model\Action;
use App\CoreModule\ManagerModule\Model\Module;
use App\CoreModule\RoleModule\Model\Role;
use Entity\Database\DaoInterface;

class RolesManager extends \Entity\Model\ModelManager
{
	/**
	 * StockManager constructor.
	 * @param DaoInterface $dao
	 */
	public function __construct(DaoInterface $dao)
	{
		parent::__construct($dao, Role::class);
	}

	public function addModule(Role $role, Module $module)
	{
		$request = $this->builder->insert('roles_modules')
			->columns('roles_id', 'modules_id', 'authorized')
			->values(
				[
					'roles_id' => $role->getId(),
					'modules_id' => $module->getId(),
					'authorized' => 1,
				]
			);
		return $this->builder->execute($request);
	}

	public function deleteModule($roleId, $moduleId)
	{
		$request = $this->builder->delete('roles_modules')
			->where('roles_id', '=', $roleId)
			->where('modules_id', '=', $moduleId);
		$this->builder->execute($request);
	}

	public function addAction(Role $role, Action $action)
	{
		$request = $this->builder->select()
			->from('roles_actions')
			->where('roles_id', '= ', $role->getId())
			->where('actions_id', '= ', $action->getId());
		$result = $this->builder->execute($request);
		if(!$result) {
			$request = $this->builder->insert('roles_actions')
				->columns('roles_id', 'actions_id', 'authorized')
				->values(
					[
						'roles_id' => $role->getId(),
						'actions_id' => $action->getId(),
						'authorized' => 1,
					]
				);
			return $this->builder->execute($request);
		} else {
			if(!$result['authorized']) {
				$request = $this->builder->update('roles_actions')
					->set('authorized', 1)
					->where('roles_id', '=' ,$role->getId())
					->where('actions_id', '=' ,$action->getId());
				return $this->builder->execute($request);
			}
			return false;
		}

	}

	public function getRoleAuthorizedModules(Role $role)
	{
		$this->builder->select('modules.*')
			->from('modules')
			->join('roles_modules', 'modules.id', 'modules_id')
			->where('roles_id', '=', $role->getId())
			->where('authorized', '=', 1)
			->execute();
	}
}