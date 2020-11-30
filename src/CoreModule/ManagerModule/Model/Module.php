<?php

namespace App\CoreModule\ManagerModule\Model;

use Entity\Model\Model;

/**
 * @Table(name="modules")
 **/
class Module extends Model
{
    /** @Column(type="string") */
    protected string $moduleClass = '';

    /** @Column(type="string") */
    protected string $name = '';

    /** @Column(type="string") */
    protected string $description = '';

    /**
     * Module constructor.
     */
    public function __construct($datas = [])
    {
        return parent::__construct($datas);
    }

    /**
     * @return string
     */
    public function getModuleClass(): string
    {
        return $this->moduleClass;
    }

    /**
     * @param string $moduleClass
     * @return Module
     */
    public function setModuleClass(string $moduleClass): Module
    {
        $this->moduleClass = $moduleClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Module
     */
    public function setName(string $name): Module
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Module
     */
    public function setDescription(string $description): Module
    {
        $this->description = $description;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }


}
