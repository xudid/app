<?php

use App\App;
use function \DI\create;
use function \DI\get;
use Entity\Model\ManagerFactory;
use Entity\Database\DataSourceInterface;
use Entity\Database\Mysql\MysqlDataSource;

$dbConfig = require 'db.php';
$environment = App::getEnvironment();
return [
	DataSourceInterface::class => create(MysqlDataSource::class)
		->constructor('mysql', $dbConfig[$environment]),
	ManagerFactory::class => create(ManagerFactory::class)
		->constructor(get(DataSourceInterface::class)),
	Renderer::class => create(Renderer::class),
];
