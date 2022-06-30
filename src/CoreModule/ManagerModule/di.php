<?php

use App\CoreModule\ManagerModule\Controller\ModuleManagerController;
use App\App;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Psr7\ServerRequest;
use function DI\create;
use function DI\get;

return [
	ModuleManagerController::class => create(ModuleManagerController::class)
		->method('setContext', App::getInstance())
		->method('setRouter', get('router'))
		->method('setLogger', get(LoggerInterface::class))
		->method('setRequest', ServerRequest::fromGlobals())
];
