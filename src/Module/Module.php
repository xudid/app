<?php

namespace App\Module;

use Psr\Container\ContainerInterface;

/**
 * Class Module
 * @package App\Module
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class Module
{
	/**
	 * @var bool $isMetamodule
	 */
	protected bool $isMetamodule = false;

	/**
	 * @var ModuleInfo $moduleInfo
	 */
	protected ModuleInfo $moduleInfo;

	/**
	 * @var array $dependencies
	 */
	protected array $dependencies = [];
	/**
	 * @var ContainerInterface
	 */
	protected ContainerInterface $container;
	/**
	 * @var string $scope
	 */
	private string $scope = "scope";

	protected string $name = "";

	/**
	 * Module constructor.
	 * @param ContainerInterface $container
	 */
	function __construct(ContainerInterface $container)
	{

		if ($container != null && $container instanceof ContainerInterface) {
			$this->container = $container;
			$config = $container->get("module_".$this->name."_config");
			$this->scope = $config["scope"]??$this->name;
		}
	}

	/**
	 * @return bool
	 */
	public function isMetaModule()
	{
		return $this->isMetamodule;
	}

	/**
	 * @return string
	 */
	public function getScope()
	{
		return $this->scope;
	}

	public function getModuleInfo()
	{
		return $this->moduleInfo;
	}

	/**
	 * @param $displayType
	 * @param string $display
	 * @param string $alternateDisplay
	 * @param string $path
	 * @param string $displayside
	 */
	public function setModuleInfo(
		$displayType, string $display,
		string $alternateDisplay,
		string $path,
		string $displayside = "left")
	{
		$this->moduleInfo = new ModuleInfo(
			$displayType,
			$display,
			$alternateDisplay,
			$path
		);

		$this->moduleInfo->setDisplaySide($displayside);
	}

	/**
	 * @return bool
	 */
	public function hasDependencies(): bool
	{
		return count($this->dependencies) > 0;
	}

	/**
	 * @method heckDependencies(array $modulesInstances):bool
	 * @param array $modulesInstances
	 * @return bool|array : true if all dependencies are already loaded , an
	 * array of missing dependencies else
	 */
	public function checkDependencies(array $modulesInstances)
	{
		$dependencies = $this->getDependencies();
		$missingDependencies = [];
		foreach ($dependencies as $dependence) {
			if (!array_key_exists($dependence, $modulesInstances)) {
				$missingDependencies[] = $dependence;
			}
		}
		return empty($missingDependencies)?true:$missingDependencies;
	}

	/**
	 * @return array
	 */
	public function getDependencies(): array
	{
		return $this->dependencies;
	}
}

