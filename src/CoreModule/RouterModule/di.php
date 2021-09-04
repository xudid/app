<?php

use Router\Router;
use Router\RouterMiddleware;
use App\CoreModule\RouterModule\Controller\RouterController;
use App\App;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Log\LoggerInterface;
use function DI\create;
use function DI\get;

return [
	'router' => create(Router::class)
		->method('setAuthorizedMethods', ['GET', 'POST']),
	'router_middleware' => create(RouterMiddleware::class)
		->constructor(get('router')),
	RouterController::class => create(RouterController::class)
		->method('setContext', App::getInstance())
		->method('setRouter', get('router'))
		->method('setLogger', get(LoggerInterface::class))
		->method('setRequest', ServerRequest::fromGlobals())
];
