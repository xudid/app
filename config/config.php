<?php

use App\CoreModule\AuthorizationModule\Middleware\AuthorizationMiddleware;
use App\CoreModule\ControllerDispatcher\Middleware\ControllerDispatcher;

$rootDir = dirname($_SERVER['DOCUMENT_ROOT']);
return [
	'app_name' => 'backoffice',
	'environment' => 'development',
	'root_dir' => $rootDir,
	'config_dir' => $rootDir . DIRECTORY_SEPARATOR . 'config',
	'temp_dir' => $rootDir . DIRECTORY_SEPARATOR . 'tmp',
	'cache_dir' => $rootDir . DIRECTORY_SEPARATOR . 'cache',
	// manual sort , Todo automatic sort using modules dependencies
	'core_modules' => [
		App\CoreModule\SetupModule\SetupModule::class,
		App\CoreModule\LoggingModule\LoggingModule::class,
		App\CoreModule\UserModule\UserModule::class,
		App\CoreModule\RoleModule\RoleModule::class,
		App\CoreModule\AuthModule\AuthModule::class,
		App\CoreModule\RouterModule\RouterModule::class,
		App\CoreModule\ManagerModule\ManagerModule::class,
		App\CoreModule\AuthorizationModule\AuthorizationModule::class,
	],

	'pipeline' =>[
		'router_middleware',
		AuthorizationMiddleware::class,
		ControllerDispatcher::class
	],
];
