<?php

namespace App\Module;

use App\App;
use Entity\Database\DaoInterface;
use Entity\Database\DataSourceInterface;
use Entity\Migrations\PhinxAdapter;

/**
 * Class Module
 * @package App\Module
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class Module implements ModuleInterface
{
    /**
	 * @var bool $isMetamodule
	 */
	protected bool $isMetamodule = false;

	/**
	 * @var ModuleInfo $moduleInfo
	 */
	protected ?ModuleInfo $moduleInfo = null;

	/**
	 * @var array $dependencies
	 */
	protected array $dependencies = [];

	/**
	 * @var string $scope
	 */
	private string $scope = "scope";

	protected static string $name = '';
	protected static string $description = '';

    /**
     * Module constructor.
     * @param App $app
     */
	function __construct()
	{

	}

    /**
     * @return string
     */
    public function getName(): string
    {
        return static::$name;
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return static::$description;
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
	 * @method checkDependencies(array $modulesInstances):bool
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




    public function getSubModuleClassNames(): array
    {
        // TODO: Implement getSubModuleClassNames() method.
    }

    public function getMigrationsPath()
    {
        // here or in DI definition above
    }

    public static function  install(DaoInterface $dao, string $environment)
    {
        $adapter = new PhinxAdapter($dao, static::getDir(), $environment);
        $adapter->setDbName('brickdb');
        $adapter->enableOutPut();
        $adapter->run();
        //dump($adapter->getOutput());

    }



    public function update(DataSourceInterface $dataSource, string $environment)
    {
        // TODO: Implement update() method.
    }


    public function remove(DataSourceInterface $dataSource, string $environment)
    {
        // TODO: Implement remove() method.
    }
}

