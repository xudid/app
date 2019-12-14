<?php

namespace App;

use App\Module\Module;
use Exception;
use InvalidArgumentException;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use Usage\TestModule;


/**
 * Class App
 * Container must define keys :app_name , "app_name"_modules
 * @package App
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class App
{
	private array $moduleClassNames = [];
	private array $modulesInstances = [];
	private array $modulesInfos = [];
	private array $errors = [];
	private ContainerInterface $container;
	private string $appName;

	/**
	 * App constructor.
	 * @param ContainerInterface $container
	 * @param array $moduleClassNames
	 */

	function __construct(ContainerInterface $container)
	{

		if ($container != null && $container instanceof ContainerInterface) {
			$this->container = $container;
			$this->appName = $this->container->get("app_name");
			$this->moduleClassNames = $this->container->get($this->appName . "_modules") ?? [];

			foreach ($this->moduleClassNames as $moduleClassName) {
				if (is_string($moduleClassName)) {

					//Loading module
					$this->loadModule($moduleClassName);
				}
			}
			return $this;
			//We don't have a ContainerInterFace  as __construct param throw an Exception
		} else {
			throw new InvalidArgumentException();
		}
	}

	private function loadModule($moduleClassName)
	{

		try {

			$moduleReflectionClass = new ReflectionClass($moduleClassName);
			$module = $moduleReflectionClass->newInstance($this->container);


			//Loading module info
			if ($module !=null && $module instanceof TestModule) {

				$this->loadModuleInfo($module);
				//Loading module dependencies

				if ($module->hasDependencies() && !($missingDependencies = $module->checkDependencies($this->modulesInstances))) {
					foreach ($missingDependencies as $missingDependency) {
						$this->loadModule($missingDependency);
					}
				}
				//Loading submodules for MetaModules
				if ($module->isMetaModule()) {
					$subModuleClassNames = $module->getSubModuleClassNames();

					foreach ($subModuleClassNames as $subModuleClassName) {

						if ($moduleClassName != $subModuleClassName) {
							$subModule = $this->loadModule($subModuleClassName);
							if ($subModule && $subModule instanceof Module) {
								$this->loadModuleInfo($subModule);
								$this->modulesInstances[$subModuleClassName] = $subModule;
							}

						} else {
							throw new Exception("Circular reference was detected in submodule referencies");
						}
					}
				}
				$this->modulesInstances[$moduleClassName] = $module;
				return $module;
			}


		} catch (ReflectionException $e) {
			$this->errors[]  = "Module $moduleClassName not Found";
		}

	}

	//todo populate the navbar in AppPage
	private function loadModuleInfo($module)
	{
		$infos = $module->getModuleInfo();
		if (!is_null($infos)) {
			$this->modulesInfos[$module->getScope()] = $infos;
			/*$this->renderer->addNavBarItem($infos->getNavBarDisplayType(),
				$infos->getPath(),
				$infos->getNavBarDisplay(),
				$infos->getAlternateDisplay(),
				$infos->getDisplaySide()
			);*/
		}
	}

	/**
	 * @return array|mixed
	 */
	public function getModuleClassNames()
	{
		return $this->moduleClassNames;
	}

	/**
	 * @return array
	 */
	public function getModulesInstances(): array
	{
		return $this->modulesInstances;
	}

	/**
	 * @return array
	 */
	public function getModulesInfos(): array
	{
		return $this->modulesInfos;
	}

	/**
	 * @return mixed|string|app_name
	 */
	public function getAppName()
	{
		return $this->appName;
	}

	/**
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}




}

