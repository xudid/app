<?php

namespace App\CoreModule\ManagerModule\Model;

use Entity\Database\Attributes\Column;
use Entity\Database\Attributes\Table;
use Entity\Model\Model;

/**
 * Class Module
 * @package App\CoreModule\ManagerModule\Model
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/03/2021.
 */
#[Table('modules')]
class Module extends Model
{
	#[Column('string')]
	protected string $moduleClass = '';

	#[Column('string')]
	protected string $name = '';

	#[Column('string')]
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
