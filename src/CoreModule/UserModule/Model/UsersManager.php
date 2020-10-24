<?php


namespace App\CoreModule\UserModule\Model;


use App\CoreModule\RoleModule\Model\Role;
use Entity\Database\DaoInterface;

class UsersManager extends \Entity\Model\ModelManager
{
    /**
     * StockManager constructor.
     * @param DaoInterface $dao
     */
    public function __construct(DaoInterface $dao)
    {
        parent::__construct($dao);
    }

    public function addRole(User $user, Role $role)
    {
        $request = $this->builder->insert('users_roles')
            ->columns('users_id', 'roles_id')
            ->values(['users_id' => $user->getId(), 'roles_id' => $role->getId()]);
        return $this->builder->execute($request);
    }

    public function deleteRole($userId, $roleId)
    {
        $request = $this->builder->delete('users_roles')
            ->where('users_id', '=', $userId)
            ->where('roles_id', '=', $roleId);
        $this->builder->execute($request);
    }
}