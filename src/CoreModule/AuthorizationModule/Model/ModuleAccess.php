<?php

namespace App\CoreModule\AuthorizationModule\Controller;

use App\CoreModule\RoleModule\Model\Role;
use App\Module\Module;
use Entity\Model\Model;

class ModuleAccess extends Model
{
    private Module $module;
    private array $roles;

    /**
     * ModuleAccess constructor.
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * @return Module
     */
    public function getModule(): Module
    {
        return $this->module;
    }

    /**
     * @param Module $module
     * @return ModuleAccess
     */
    public function setModule(Module $module): ModuleAccess
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return ModuleAccess
     */
    public function setRoles(array $roles): ModuleAccess
    {
        $this->roles = $roles;
        return $this;
    }

    public function canAccess(Role $role)
    {
        if (in_array($role, $this->roles)) {
            return true;
        }
        return false;
    }
}
