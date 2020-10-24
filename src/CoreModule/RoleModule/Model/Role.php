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
    private string $name = '';

    /**
     * Role description
     * @var string $description
     * @Column(type="string")
     */
    private string $description = '';

    /**
     * @ManyToMany(targetEntity="App\CoreModule\ManagerModule\Model\Module")
     */
    private array $modules = [];

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


}
