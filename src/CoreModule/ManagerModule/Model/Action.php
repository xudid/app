<?php


namespace App\CoreModule\ManagerModule\Model;


use Entity\Model\Model;

/**
 * Class Action
 * @package App\CoreModule\ManagerModule\Model
 * @Table(name="actions")
 */
class Action extends Model
{
	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected string $name = '';
	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected string $type = '';
	/**
	 * @var string
	 * @Column(type="string")
	 */
	protected string $routeName = '';
	/**
	 * @var Module|null
	 * @OneToOne(targetEntity="App\CoreModule\ManagerModule\Model\Module")
	 */
	protected ?Module $module;

	/**
	 * Action constructor.
	 */
	public function __construct()
	{
		parent::__construct([]);
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
	 * @return Action
	 */
	public function setName(string $name): Action
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return Action
	 */
	public function setType(string $type): Action
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRouteName(): string
	{
		return $this->routeName;
	}

	/**
	 * @param string $routeName
	 * @return Action
	 */
	public function setRouteName(string $routeName): Action
	{
		$this->routeName = $routeName;
		return $this;
	}

	/**
	 * @return Module|null
	 */
	public function getModule(): ?Module
	{
		return $this->module;
	}

	/**
	 * @param Module|null $module
	 * @return Action
	 */
	public function setModule(?Module $module): Action
	{
		$this->module = $module;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}
}