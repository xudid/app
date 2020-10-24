<?php


namespace App\CoreModule\AuthorizationModule\Model;


use App\CoreModule\RoleModule\Model\Role;
use App\CoreModule\UserModule\Model\User;
use App\Module\Module;
use Entity\Model\Model;

class Autorization extends Model
{
    const ACTION = 'action';
    private Module $module;

    private Role $role;

    private array $actions;

    /**
     * @return Module
     */
    public function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @param Module $module
     * @return Autorization
     */
    public function setModule(Module $module): Autorization
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return Role
     */
    public function getUser(): Role
    {
        return $this->role;
    }

    /**
     * @param User $user
     * @return Autorization
     */
    public function setRole(Role $user): Autorization
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     * @return Autorization
     */
    public function setActions(array $actions): Autorization
    {
        $this->actions = $actions;
        return $this;
    }


}