<?php

use App\CoreModule\AuthorizationModule\Controller\AuthorizationController;
use App\App;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Psr7\ServerRequest;
use App\CoreModule\AuthorizationModule\Middleware\AuthorizationMiddleware;
use App\CoreModule\AuthModule\AuthController;
use function DI\create;
use function DI\get;

return [
	AuthorizationController::class => create(AuthorizationController::class)
		->method('setContext', App::getInstance())
		->method('setRouter', get('router'))
		->method('setLogger', get(LoggerInterface::class))
		->method('setRequest', ServerRequest::fromGlobals()),
	AuthorizationMiddleware::class => create(AuthorizationMiddleware::class)
		->constructor(
			get(AuthController::class),
			get(AuthorizationController::class),
			get('default_allowed_routes')
		)
];
