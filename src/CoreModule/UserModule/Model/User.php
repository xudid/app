<?php

namespace App\CoreModule\UserModule\Model;


use App\CoreModule\RoleModule\Model\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * @relation::with:App\CoreModule\RoleModule\Role::type:OneToMany
 * @relation::with:App\CoreModule\RoleModule\Role::type:OneToMany
 * @description Represents base user in App
 */

/**
 * @Entity
 * @Table(name="users")
 **/
class User
{
    /**
     * [private description]
     * @var int $id
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="string") */
    private $name = "";

    /** @Column(type="string") */
    private $email = "";
    /** @Column(name="password",type="string") */
    private $password = "";

    /**
     * [private description]
     * @var App\CoreModule\RoleModule\Model\Role $role
     * @ManyToMany(targetEntity="App\CoreModule\RoleModule\Model\Role",fetch="EAGER")
     * @JoinTable(name="users_roles",
     *            joinColumns={@JoinColumn(name="users_id",referencedColumnName="id")},
     *            inverseJoinColumns={@JoinColumn(name="roles_id",referencedColumnName="id")})
     */
    private $role = null;

    public function __construct()
    {
        $this->role = new ArrayCollection();
    }

    public function setId($id)
    {
        if ($id != null) {
            $this->id = $id;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        if ($name != null) {
            $this->name = $name;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        if ($email != null) {
            $this->email = $email;
        }
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($pass)
    {
        if ($pass != null) {


            $this->password = $pass;
        }
    }

    public function initPassword($pass)
    {
        if ($pass != null) {
            $pass = password_hash($pass, PASSWORD_DEFAULT);

            $this->password = $pass;
        }
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function verifyPassword($pass)
    {
        if (password_verify($pass, $this->password)) {
            return true;
        } else {
            return false;
        }
    }

    public function setRole($role)
    {
        if ($role != null) {
            $this->role = $role;
        }
    }

    public function getRole()
    {
        return $this->role;
    }
}
