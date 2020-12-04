<?php

use function \DI\create;
use function \DI\get;
use Entity\Model\ManagerFactory;
use Entity\Database\DataSourceInterface;
use Entity\Database\Mysql\MysqlDataSource;

return [
	DataSourceInterface::class => create(MysqlDataSource::class)
		->constructor('mysql', get('mysql_datasource_params')),
	ManagerFactory::class => create(ManagerFactory::class)
		->constructor(get(DataSourceInterface::class)),
];
