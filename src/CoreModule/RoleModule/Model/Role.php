<?php

namespace App\CoreModule\RoleModule\Model;

use Entity\Model\Model;

/**
 * @Table(name="roles")
 **/
class Role extends Model
{

    /**
     * Role name
     * @var string $name
     * @Column(type="string")
     */
    protected string $name = '';

    /**
     * Role description
     * @var string $description
     * @Column(type="string")
     */
    protected string $description = '';

    /**
     * @ManyToMany(targetEntity="App\CoreModule\ManagerModule\Model\Module")
     */
    protected array $modules = [];

    public function __construct(array $datas = [])
    {
        return parent::__construct($datas);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @param array $modules
     * @return Role
     */
    public function setModules( $modules): Role
    {
        $this->modules = $modules;
        return $this;
    }




}
