<?php


namespace App\CoreModule\UserModule\Model;


use App\CoreModule\RoleModule\Model\Role;
use Entity\Database\DaoInterface;
use Entity\Model\ModelManager;

class UsersManager extends ModelManager
{
	/**
	 * StockManager constructor.
	 * @param DaoInterface $dao
	 */
	public function __construct(DaoInterface $dao)
	{
		parent::__construct($dao, User::class);
	}

	public function insert($user)
	{
		$id = parent::insert($user);
		foreach ($user->getRoles() as $role) {
			$this->addRole($user, $role);
		}
		return $id;
	}

	public function addRole(User $user, Role $role)
	{
		return $this->builder->insert('users_roles')
			->columns('users_id', 'roles_id')
			->values(['users_id' => $user->getId(), 'roles_id' => $role->getId()])
			->execute();
	}

	public function deleteRole($userId, $roleId)
	{
		return $this->builder->delete('users_roles')
			->where('users_id', '=', $userId)
			->where('roles_id', '=', $roleId)
			->execute();
	}
}