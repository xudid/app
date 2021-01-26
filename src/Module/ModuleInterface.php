<?php

namespace App\Module;

use Entity\Database\DaoInterface;
use Entity\Database\DataSourceInterface;

interface ModuleInterface
{
	/**
	 * @return string
	 */
	public static function getName(): string;

	/**
	 * @return string
	 */
	public static function getDescription(): string;

	/**
	 * @return bool
	 */
	public static function isMetaModule():bool;

	/**
	 * @return bool
	 */
	public static function hasDependencies(): bool;

	/**
	 * @param array $modulesInstances
	 * @return mixed
	 */
	public static function checkDependencies(array $modulesInstances);

	/**
	 * @return array
	 */
	public static function getDependencies(): array;

	/**
	 * @param string $className
	 * @return bool
	 */
	public static function exists(string $className) : bool;

	/**
	 * @param DaoInterface $dao
	 * @param string $database
	 * @param string $environment
	 * @return mixed
	 */
	public static function install(DaoInterface $dao, string $database, string $environment);

	/**
	 * @param DataSourceInterface $dataSource
	 * @param string $environment
	 * @return mixed
	 */
	public static function update(DataSourceInterface $dataSource, string $environment);

	/**
	 * @param DataSourceInterface $dataSource
	 * @param string $environment
	 * @return mixed
	 */
	public static function remove(DataSourceInterface $dataSource, string $environment);
}
