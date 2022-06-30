<?php

use App\CoreModule\RoleModule\Controller\RolesController;
use App\App;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Log\LoggerInterface;
use function DI\create;
use function DI\get;

return [
	RolesController::class => create(RolesController::class)
		->method('setContext', App::getInstance())
		->method('setRouter', get('router'))
		->method('setLogger', get(LoggerInterface::class))
		->method('setRequest', ServerRequest::fromGlobals())
];
