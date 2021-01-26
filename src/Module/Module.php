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
	protected static bool $isMetamodule = false;


	/**
	 * @var array $dependencies
	 */
	protected static array $dependencies = [];

	protected static string $name = '';
	protected static string $description = '';

	/**
	 * Module constructor.
	 */
	function __construct()
	{

	}

	/**
	 * @return string
	 */
	public static function getName(): string
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

	public static function getMigrationsDir(string $className)
	{
		return static::getDir($className) . DIRECTORY_SEPARATOR . 'migrations';
	}

	/**
	 * @return bool
	 */
	public static function isMetaModule() : bool
	{
		return self::$isMetamodule;
	}

	/**
	 * @return bool
	 */
	public static function hasDependencies(): bool
	{
		return count(self::$dependencies) > 0;
	}

	/**
	 * @method checkDependencies(array $modulesInstances):bool
	 * @param array $modulesInstances
	 * @return bool|array : true if all dependencies are already loaded , an
	 * array of missing dependencies else
	 */
	public static function checkDependencies(array $modulesInstances)
	{
		$dependencies = self::getDependencies();
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
	public static function getDependencies(): array
	{
		return self::$dependencies;
	}

	public static function exists(string $className) : bool
	{
		return class_exists($className) && is_subclass_of($className, Module::class);
	}

	public static function  install(DaoInterface $dao, string $database, string $environment)
	{
		$adapter = new PhinxAdapter($dao, static::getDir(), $environment);
		$adapter->setDbName($database);
		$adapter->enableOutPut();
		$adapter->run();
		//dump($adapter->getOutput());

	}



	public static function update(DataSourceInterface $dataSource, string $environment)
	{
		// TODO: Implement update() method.
	}


	public static function remove(DataSourceInterface $dataSource, string $environment)
	{
		// TODO: Implement remove() method.
	}
}

